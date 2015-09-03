/**
 *  JavaScript for the admin area
 */

$ = jQuery.noConflict();

(function($) {
    var $layout = $('#default_layout');

    if($layout.val() == 'custom') {
        $('.custom-layout').show();
    }

    $layout.on('change', function() {
        var new_layout = $(this).val();

        if(new_layout == 'custom') {
            $('.custom-layout').show();
        } else {
            $('.custom-layout').hide();
        }
    });

    console.log('admin js!');
})($);