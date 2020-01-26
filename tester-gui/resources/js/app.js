jQuery( document ).ready(function( $ ) {
    $('[data-toggle="tooltip"]').tooltip({
        'delay': { show: 500, hide: 200 }
    });

    $('.test-addon-button').on('click', function () {
        let clickedAddonButton = $(this);

        $.ajax({
            method: "POST",
            url: "http://localhost:996/test/initial-error",
            data: {
                name: clickedAddonButton.data('name'),
                file: clickedAddonButton.data('file'),
                link: clickedAddonButton.data('link')
            },
            datatype: 'application/json',
            crossDomain: true
        })
        .done(function( msg ) {
            if (msg === 'true') {
                updateCspErrorStatus(clickedAddonButton.data('id'), 'initial-error');
            }
        });
    })
});

/**
 * Updates csp error status for addon
 * @param addon_id
 * @param cspErrorType
 */
function updateCspErrorStatus(addon_id, cspErrorType) {
    $.ajax({
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