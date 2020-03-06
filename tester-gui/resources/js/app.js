window.localStorage.setItem('selectedAddons', JSON.stringify({'addons': {}}));

jQuery( document ).ready(function( $ ) {
    $('[data-toggle="tooltip"]').tooltip({
        'delay': { show: 500, hide: 200 }
    });

    $('.test-addon-button').on('click', function () {
        let addonInfo = {
            'name': $(this).data('name'),
            'file': $(this).data('file'),
            'link': $(this).data('link'),
        };

        let response = testAddonBackendCall(addonInfo, 'initial-error');

        if (response === 'true') {
            updateCspErrorStatus($(this).data('id'), 'initial-error');
        }
    });

    $('.test-selected').on('click', function () {
        let errorType = $(this).data('error-type');
        let checkedAddons = JSON.parse(window.localStorage.getItem('selectedAddons')).addons;

        for (let id in checkedAddons) {
            if (checkedAddons.hasOwnProperty(id)) {
                let addonInfo = checkedAddons[id];

                console.log('Addon name: ' + addonInfo.name);
                console.log(errorType + ' testing started.');

                let response = testAddonBackendCall(addonInfo, errorType);

                if (response === 'true') {
                    console.log('Detecting CSP reports started.');
                    updateCspErrorStatus(addonInfo.id, errorType);
                }

                console.log('------------------------------------------')
            }
        }
    });

    $('.report-all').on('click', function () {
        renderReportForAll(getReportForAll());
        $('#show_all_report').removeClass('hidden').on('click', function () {
            $('#reportAllModal').modal('toggle');
        });
        $('#reportAllModal').modal('toggle');
    });

    $('.check-addon').on('change', function () {
        let selectedAddonsStorage = JSON.parse(window.localStorage.getItem('selectedAddons'));
        let checkedAddon = getAddonInfoFromCheckbox(this);

        if (this.checked) {
            selectedAddonsStorage.addons[checkedAddon.id] = checkedAddon;
        } else {
            delete selectedAddonsStorage.addons[checkedAddon.id];
        }

        window.localStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
    });

    $('#select_all_addons').on('click', function () {
        $('.check-addon').each(function () {
            this.checked = true;

            let selectedAddonsStorage = JSON.parse(window.localStorage.getItem('selectedAddons'));
            let checkedAddon = getAddonInfoFromCheckbox(this);
            selectedAddonsStorage.addons[checkedAddon.id] = checkedAddon;
            window.localStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
        });
    });

    $('#deselect_all_addons').on('click', function () {
        $('.check-addon').each(function () {
            this.checked = false;

            let selectedAddonsStorage = JSON.parse(window.localStorage.getItem('selectedAddons'));
            selectedAddonsStorage.addons = {};
            window.localStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
        });
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

/**
 * Updates csp error status for addon
 * @param addon_id
 * @param cspErrorType
 */
function updateCspErrorStatus(addon_id, cspErrorType) {
    $.ajax({
        async: false,
        method: "POST",
        url: "http://localhost:998/api/update-addon-csp-status",
        data: {
            addon_id: addon_id,
            csp_error_type: cspErrorType
        },
        datatype: 'application/json',
        crossDomain: true
    })
    .done(function( msg ) {
        console.log(msg);
    });
}

function testAddonBackendCall(addonInfo, cspErrorType) {
    let response = '';

    $.ajax({
        async: false,
        method: "POST",
        url: "http://localhost:996/test/" + cspErrorType,
        data: {
            name: addonInfo.name,
            file: addonInfo.file,
            link: addonInfo.link
        },
        datatype: 'application/json',
        crossDomain: true
    })
    .done(function( msg ) {
        response = msg;
    });

    return response;
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
