(function($) {

    "use strict";



    $(function() {





        var page_column_header = '<th scope="col" id="gf_util_page" class="manage-column column-title">Real Time Validation</th>';



        var form_list_table = jQuery('#forms_form').find('table');



        form_list_table.find('thead').find('tr').find('th').filter(':last').after(page_column_header);

        form_list_table.find('tfoot').find('tr').find('th').filter(':last').after(page_column_header);



        var forms_list = form_list_table.find('tbody').find('tr');



        jQuery.each(forms_list, function() {



            var form_id = jQuery(this).find('td.column-id').html();

            var on_page = false;

            var page_links = '';

            var num_of_page_links = 0;

          

            var str = "";

            if (lv_formlist[form_id] !== "") {

                str = " checked ";

            }

            page_links = '<input class="lv_validation_default_formlist_checkboxes" onchange ="toggleRvState(' + form_id + ',this);" type="checkbox" ' + str + '/>';

            jQuery(this).find('td').filter(':last').after('<td>' + page_links + '</td>');



        });



        $(window).load(function() {

            window['is_window_loaded'] = true;

        });

    });







}(jQuery));

function toggleRvState(formID, elem)

{



    if (window['is_window_loaded'] == true) {

        window.location = lv_toggle_url + "&action=gf_lv_ajax_toggle&lvformid=" + formID + "&elemstate=" + elem.checked + "&nonce=" + lv_ajax_toggle_nonce;

    }





}

window['is_window_loaded'] = false;