jQuery( document ).ready(function( $ ) {
    $('[data-toggle="tooltip"]').tooltip({
        'delay': { show: 500, hide: 200 }
    });

    $('.test-addon-button').on('click', function () {
        console.log($(this).data('name'))
    })
});