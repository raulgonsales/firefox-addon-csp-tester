window.localStorage.setItem('selectedSites', JSON.stringify({'sites': {}}));

jQuery( document ).ready(function( $ ) {
    $('[data-toggle="tooltip"]').tooltip({
        'delay': { show: 500, hide: 200 }
    });

    $('.test-addon-button').on('click', function () {
        let addonInfo = {
            'name': $(this).data('name'),
            'file': $(this).data('file'),
            'link': $(this).data('link'),
            'id': $(this).data('id'),
        };

        testAddonBackendCall(addonInfo);
    });

    $('.test-selected.test-selected-on-start-test').on('click', function () {
        let checkedAddons = JSON.parse(window.sessionStorage.getItem('selectedAddons')).addons;
        let testType = $(this).data('test-type');
        let domain = $(this).data('domain');

        for (let id in checkedAddons) {
            if (checkedAddons.hasOwnProperty(id)) {
                let addonInfo = checkedAddons[id];
                console.log('Addon name: ' + addonInfo.name);
                console.log(testType + ' testing started.');

                testAddonBackendCall(addonInfo, testType, domain);
            }
        }
    });

    $('.test-selected.test-selected-content-scripts-analyzing').on('click', function () {
        new Promise(function(resolve){
            $("#sitesListModal").modal('show');
            $('#sitesListModal .btn-confirm').click(function(){
                resolve();
            });
        }).then(function(){
            let checkedAddons = JSON.parse(window.sessionStorage.getItem('selectedAddons')).addons;

            for (let addonId in checkedAddons) {
                if (checkedAddons.hasOwnProperty(addonId)) {
                    let addonInfo = checkedAddons[addonId];
                    let checkedSites = JSON.parse(window.localStorage.getItem('selectedSites')).sites;

                    analyzeAddonContentScript(addonInfo, checkedSites);
                }
            }
        });
    });

    $('.check-addon').on('change', function () {
        if (window.sessionStorage.getItem('selectedAddons') === null) {
            window.sessionStorage.setItem('selectedAddons', JSON.stringify({'addons': {}}));
        }

        let selectedAddonsStorage = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
        let checkedAddon = getAddonInfoFromCheckbox(this);

        if (this.checked) {
            selectedAddonsStorage.addons[checkedAddon.id] = checkedAddon;
        } else {
            delete selectedAddonsStorage.addons[checkedAddon.id];
        }

        window.sessionStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
    });

    $('.site-checkbox').on('change', function () {
        let selectedSitesStorage = JSON.parse(window.localStorage.getItem('selectedSites'));

        if (this.checked) {
            selectedSitesStorage.sites[$(this).data('id')] = $(this).data('matching-url');
        } else {
            delete selectedSitesStorage.sites[$(this).data('id')];
        }

        window.localStorage.setItem('selectedSites', JSON.stringify(selectedSitesStorage));
    });

    $('#select_all_addons').on('click', function () {
        $('.check-addon').each(function () {
            this.checked = true;

            if (window.sessionStorage.getItem('selectedAddons') === null) {
                window.sessionStorage.setItem('selectedAddons', JSON.stringify({'addons': {}}));
            }

            let selectedAddonsStorage = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
            let checkedAddon = getAddonInfoFromCheckbox(this);
            selectedAddonsStorage.addons[checkedAddon.id] = checkedAddon;
            window.sessionStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
        });
    });

    $('.addons-deselect-button').on('click', function () {
        if ($(this).data('type') === 'current') {
            $('.check-addon').each(function () {
                this.checked = false;

                if (window.sessionStorage.getItem('selectedAddons') === null) {
                    return false;
                }

                let selectedAddonsStorage = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
                let checkedAddon = getAddonInfoFromCheckbox(this);
                if (selectedAddonsStorage.addons.hasOwnProperty(checkedAddon.id)) {
                    delete selectedAddonsStorage.addons[checkedAddon.id];
                }
                window.sessionStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
            });
        } else if ($(this).data('type') === 'all') {
            $('.check-addon').each(function () {
                this.checked = false;

                if (window.sessionStorage.getItem('selectedAddons') !== null) {
                    window.sessionStorage.removeItem('selectedAddons');
                }
            });
        }
    });
});

function getAddonInfoFromCheckbox(checkbox) {
    let addonElementWrapper = $(checkbox).parent();
    let addonTestButton = addonElementWrapper.find('.test-addon-button');

    return {
        'id': addonTestButton.data('id'),
        'name': addonTestButton.data('name'),
        'file': addonTestButton.data('file'),
        'link': addonTestButton.data('link'),
    };
}

function analyzeAddonContentScript(addonInfo, sitesMatching) {
    $.ajax({
        async: false,
        method: "POST",
        url: "http://localhost:998/api/backend-call/content-scripts-analysis",
        data: {
            addon_name: addonInfo.name,
            addon_file: addonInfo.file,
            addon_link: addonInfo.link,
            addon_id: addonInfo.id,
            sites_matching: JSON.stringify(sitesMatching)
        },
        datatype: 'application/json',
        crossDomain: true
    })
    .done(function( response ) {
        console.log(response);
    });
}

function testAddonBackendCall(addonInfo, testType, domain) {
    $.ajax({
        async: false,
        method: "POST",
        url: "http://localhost:998/api/backend-call/test/on-start-test",
        data: {
            addon_name: addonInfo.name,
            addon_file: addonInfo.file,
            addon_link: addonInfo.link,
            addon_id: addonInfo.id,
            test_type: testType,
            domain: domain
        },
        datatype: 'application/json',
        crossDomain: true
    })
    .done(function( msg ) {
        console.log(msg);
    });
}

function getReportForAll() {
    let response = null;

    $.ajax({
        async: false,
        method: "GET",
        url: "http://localhost:998/api/report-for-all",
        datatype: 'application/json',
        crossDomain: true
    })
    .done(function( msg ) {
        response = msg;
    });

    return response;
}

function renderReportForSelected(response) {
    $.ajax({
        method: "POST",
        url: "http://localhost:998/api/render-report",
        data: {
            data: response
        },
        datatype: 'application/json',
        crossDomain: true
    })
    .done(function( msg ) {
        $('body').append(msg);
        $('#reportModal').modal('toggle').on('hidden.bs.modal', function (e) {
            $(this).empty().remove();
        });
    });
}

function renderReportForAll(response) {
    MODAL = document.getElementById('reportAllModal')
        .getElementsByClassName('modal-body')
        .item(0);

    let addonsInfo = JSON.parse(response),
        addonsCount = addonsInfo['count'],
        byErrors = addonsInfo['by_errors'];

    let x = [
        "initial_error",
        "no_error"
    ];
    let y = [
        byErrors['initial_error']['count'],
        byErrors['no_error']['count']
    ];

    let data = [
        {
            histfunc: "sum",
            y: y,
            x: x,
            type: "histogram",
            name: "Addons count by error type"
        }
    ];

    Plotly.newPlot(MODAL, data);

    $('#reportAllModal .count-all').html(addonsCount);
}
