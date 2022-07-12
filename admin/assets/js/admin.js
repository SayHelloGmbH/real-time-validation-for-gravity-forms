
(function($) {
    "use strict";

    $(function() {
        
        
       
       
        var switcher1 = $('#gform_enable_lv').switcher();

        $('.checkbox .btnCheck').on('click', function() {
            switcher1.switcher('setValue', true);
        });

        $('.checkbox .btnUncheck').on('click', function() {
            switcher1.switcher('setValue', false);
        });

        $(".lv_validation_default_formlist_checkboxes").switcher();



        jQuery(document).on('click', '.lv_error_dismiss .notice-dismiss', function() {

            jQuery.post(ajaxurl,{'action': 'lv_dismiss_error',id: form['id']},function(){
                return true;
            });

        });

    });

}(jQuery));