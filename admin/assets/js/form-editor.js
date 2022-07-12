
/**
 * Initilization
 * @param {type} elem
 * @returns 
 */


var fields_inherit = new Array('post_tags', 'post_category', 'post_custom_field', 'product', 'quantity', 'option', 'shipping');
var supported_fields_pattern = new Array("text", 'textarea', 'number', 'name', 'date', 'phone', 'email', 'address', 'website', 'time', 'post_title', "post_content", 'post_excerpt', 'price');
var supported_fields = new Array("text", 'textarea', 'number', 'checkbox', 'radio', 'multiselect', 'number', 'select', 'name', 'date', 'phone', 'email', 'address', 'website', 'time', 'post_title', "post_content", 'post_excerpt', 'post_tags', 'post_category', 'post_custom_field', 'product', 'quantity', 'option', 'shipping', 'price');

for (key in supported_fields_pattern) {
    if (is_RealTimeValidOn) {
        fieldSettings[supported_fields_pattern[key]] += ", .gf_live_validation_type_settings_level_1";
    }

}



var lv_default_error_messages = JSON.parse(lv_default_error_messages);


function createbaseHash(elem) {



    try {
        var testRegEx = new RegExp(elem.value);

        document.getElementById("gf_live_validation_format_based").value = btoa(elem.value);
        SetFieldProperty('gf_live_validation_format_based', document.getElementById("gf_live_validation_format_based").value)


        var clone = field['lv_validation'] || new Array();
        var All_fields = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + " .gf_lv_is_pattern_field");
        var extra_hidden_data = jQuery("#gf_live_validation_type_settings_is_" + getDefaultInputField(field) + "_hidden_data");

        var incr_init = 0;
        if (extra_hidden_data.length > 0 && field['isRequired'] == true) {
            var elem_config = new Array();
            elem_config.push({type: "presence", "ftype": "select"});
            clone[incr_init] = elem_config;
            incr_init++;
        }

        var count_int = 0;
        for (var incr = incr_init; incr < (All_fields.length + incr_init); incr++) {





            if (jQuery(All_fields[count_int]).attr("id") == elem.id) {

                var elem_config = new Array();
                if (field['isRequired'] == true) {
                    elem_config.push({type: "presence", "ftype": "text"});
                }
                elem_config.push({type: "pattern", "format": document.getElementById("gf_live_validation_format_based").value, "error": document.getElementById("gf_live_validation_hidden_error").value, 'ftype': jQuery(All_fields[count_int]).attr("data-ftype")});
                clone[incr] = elem_config;
                count_int++;
            }
        }


        field['lv_validation'] = clone;

        jQuery(elem).siblings(".lv_admin_input_error").eq(0).hide();
        jQuery("body").trigger("lv_admin_apply_validations", document.getElementById("gf_live_validation_value").checked);








    }
    catch (err) {
        jQuery(elem).siblings(".lv_admin_input_error").eq(0).show();
        // jQuery(".lv_admin_input_error").show();
    }


    return elem.value;
}



function moveErrorTohidden(elem) {
    document.getElementById("gf_live_validation_hidden_error").value = elem.value;
    SetFieldProperty('gf_live_validation_hidden_error', document.getElementById("gf_live_validation_hidden_error").value);
    var clone = field['lv_validation'] || new Array();
    var All_fields = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + "_error input[type='text']");
    //var all_main_field = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + " .gf_lv_is_pattern_field")
    var extra_hidden_data = jQuery("#gf_live_validation_type_settings_is_" + getDefaultInputField(field) + "_hidden_data");
    var incr_init = 0;
    if (extra_hidden_data.length > 0 && field['isRequired'] == true) {
        var elem_config = new Array();
        elem_config.push({type: "presence", "ftype": "select"});
        clone[incr_init] = elem_config;
        incr_init++;
    }

    var count_opr = 0;
    for (var incr = incr_init; incr < (All_fields.length + incr_init); incr++) {


        if (jQuery(All_fields[count_opr]).attr("id") == elem.id) {

            var elem_config = new Array();
            if (field['isRequired'] == true) {
                elem_config.push({type: "presence", "ftype": "text"});
            }
            elem_config.push({type: "pattern", "format": document.getElementById("gf_live_validation_format_based").value, "error": document.getElementById("gf_live_validation_hidden_error").value, 'ftype': "text"});
            clone[incr] = elem_config;

        }
        count_opr++;
    }


    field['lv_validation'] = clone;
    jQuery("body").trigger("lv_admin_apply_validations", document.getElementById("gf_live_validation_value").checked);
    return elem.value;
}



function hide_it_completely() {


    jQuery(".gf_live_validation_type_settings_level_1").hide();
}

function show_checkbox_for_toggle() {

    if (is_RealTimeValidOn) {
        jQuery(".gf_live_validation_type_settings_level_1").show();
    }

}


jQuery("#field_address_type").on('change', function() {
    //  field['lv_validation'] = new Array();
    jQuery("#gf_live_validation_value").trigger("change");
});

jQuery("#field_input_mask").on('change', function() {



    if (field['type'] !== "text") {
        return true;
    }


    field['lv_validation'] = new Array();
    jQuery("#gf_live_validation_value").trigger("change");



    if (!is_RealTimeValidOn) {
        return true;
    }

    if (this.checked == true) {
        jQuery(".gf_live_validation_type_settings_valid").slideUp();
        hide_it_completely();
    } else {
        if (field['gf_live_validation_settings'] == true) {
            jQuery(".gf_live_validation_type_settings_valid").slideDown();
        }

        show_checkbox_for_toggle();
    }


});


jQuery("#field_phone_format").on('change', function() {


    if (field['type'] !== "phone") {
        return true;
    }
    field['lv_validation'] = new Array();

    jQuery("#gf_live_validation_value").trigger("change");

    if (!is_RealTimeValidOn) {
        return true;
    }

    if (jQuery(this).val() == "standard") {
        jQuery(".gf_live_validation_type_settings_valid").slideUp();
        hide_it_completely();
    } else {
        if (field['gf_live_validation_settings'] == true) {
            jQuery(".gf_live_validation_type_settings_valid").slideDown();
        }
        show_checkbox_for_toggle();
    }

});


jQuery("#field_time_format").on('change', function() {
    //  field['lv_validation'] = new Array();
    jQuery("#gf_live_validation_value").trigger("change");
});

jQuery("#field_date_input_type").on('click', function() {
    field['lv_validation'] = new Array();
    jQuery("#gf_live_validation_value").trigger("change");
});


jQuery(".lv_open_support_box").on('click', function() {

    tb_show("Popular RegEx Patterns", "#TB_inline?height=700&amp;width=500&amp;inlineId=my-help-div-" + getDefaultInputField(field));

});

jQuery("#field_required").on('change', function(event) {


    if (this.checked == true && jQuery("#field_error_message").val() == "") {

        jQuery("#field_error_message").val(lv_default_error_messages[field['type']]);
    }


    jQuery("#gf_live_validation_value").trigger("change");
});

jQuery("#gfield_email_confirm_enabled").on('change', function(event) {

    jQuery("#gf_live_validation_value").trigger("change");
});


jQuery("#gf_live_validation_value").on('change', function(event) {

    if (supported_fields.indexOf(getDefaultInputField(field)) == -1) {
        return true;
    }

    if (is_RealTimeValidOn) {
        switch (getDefaultInputField(field)) {
            case "date":

                if (field.dateType !== "datefield") {

                    if (this.checked == true) {
                        jQuery(".gf_live_validation_type_settings_valid").slideDown();
                        jQuery(".gf_live_validation_type_settings_is_date").slideUp();
                        jQuery(".gf_live_validation_type_settings_is_date_error").slideUp();
                    }
                    else {
                        jQuery(".gf_live_validation_type_settings_valid").slideUp();
                        jQuery(".gf_live_validation_type_settings_is_date").slideUp();
                        jQuery(".gf_live_validation_type_settings_is_date_error").slideUp();
                    }

                } else {

                    if (this.checked == true) {
                        jQuery(".gf_live_validation_type_settings_valid").slideUp();
                        jQuery(".gf_live_validation_type_settings_is_date").slideDown();
                        jQuery(".gf_live_validation_type_settings_is_date_error").slideDown();
                    } else {
                        jQuery(".gf_live_validation_type_settings_valid").slideUp();
                        jQuery(".gf_live_validation_type_settings_is_date").slideUp();
                        jQuery(".gf_live_validation_type_settings_is_date_error").slideUp();
                    }

                }
                break;
            case "name":



                if (this.checked == true) {
                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_name").slideDown();
                    jQuery(".gf_live_validation_type_settings_is_name_error").slideDown();
                } else {
                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_name_error").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_name").slideUp();
                }
                break;



            case "address":

                if (this.checked == true) {


                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_address").slideDown();
                    jQuery(".gf_live_validation_type_settings_is_address_error").slideDown();
                } else {

                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_address_error").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_address").slideUp();
                }
                break;

            case "time":

                if (this.checked == true) {


                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_time").slideDown();
                    jQuery(".gf_live_validation_type_settings_is_time_error").slideDown();
                } else {
                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_time_error").slideUp();
                    jQuery(".gf_live_validation_type_settings_is_time").slideUp();
                }
                break;
            default:
                if (this.checked == true) {
                    jQuery(".gf_live_validation_type_settings_valid").slideDown();
                } else {
                    jQuery(".gf_live_validation_type_settings_valid").slideUp();
                }



        }
    }

    if (this.checked == true) {
        var clone = field['lv_validation'] || new Array();
        var All_fields = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + " .gf_lv_is_pattern_field");
        var All_fields_error = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + "_error input[type='text']");
        var extra_hidden_data = jQuery("#gf_live_validation_type_settings_is_" + getDefaultInputField(field) + "_hidden_data");
        
        var incr_init = 0;
        if (extra_hidden_data.length > 0 && field['isRequired'] == true) {
            var elem_config = new Array();
            elem_config.push({type: "presence", "ftype": "select"});
            clone[incr_init] = elem_config;
            incr_init++;
        }

        var count_opr = 0;
        for (var incr = incr_init; incr < (All_fields.length + incr_init); incr++) {




            var elem_config = new Array();
            if (field['isRequired'] == true) {
                elem_config.push({type: "presence", 'ftype': jQuery(All_fields[count_opr]).attr("data-ftype")});
            }
            elem_config.push({type: "pattern", "format": btoa(jQuery(All_fields[count_opr]).val()), "error": jQuery(All_fields_error[count_opr]).val(), 'ftype': jQuery(All_fields[count_opr]).attr("data-ftype")});
            clone[incr] = elem_config;
            count_opr++;
        }



        field['lv_validation'] = clone;
    } else {

        if (field['isRequired'] == true) {

            var clone_valid = new Array();
            if (typeof field['lv_validation'] !== "undefined" && (field['lv_validation'].length > 0)) {


                jQuery(field['lv_validation']).each(function(k, v) {

                    clone_valid[k] = new Array();
                    jQuery(v).each(function(k1, v1) {

                        if (v1.type !== "pattern") {

                            clone_valid[k][k1] = v1;
                        }
                    });
                });
            }
            else {

                var All_fields = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + " .gf_lv_is_pattern_field");
                var extra_hidden_data = jQuery("#gf_live_validation_type_settings_is_" + getDefaultInputField(field) + "_hidden_data");

                var incr_init = 0;
                if (extra_hidden_data.length > 0 && field['isRequired'] == true) {
                    var elem_config = new Array();
                    elem_config.push({type: "presence", "ftype": "select"});
                    clone_valid[incr_init] = elem_config;
                    incr_init++;
                }
                var count_opr = 0;
                for (var incr = incr_init; incr < (All_fields.length + incr_init); incr++) {
                    clone_valid[incr] = [{'type': "presence", 'ftype': jQuery(All_fields[count_opr]).attr("data-ftype")}];
                    count_opr++;
                }




            }


            field['lv_validation'] = clone_valid;
        } else {

            field['lv_validation'] = new Array();
        }





    }

    jQuery("body").trigger("lv_admin_apply_validations", this.checked);

});

 jQuery(document).on('input propertychange', '#confirm_field_error_message', function() {
           
            SetFieldProperty(this.name, this.value);
        });


jQuery(document).bind("gform_load_field_settings", function(event, field, form) {

   
    jQuery("#gf_live_validation_format").attr("data-ftype", getDefaultPattern(getDefaultInputField(field)));



    if ((supported_fields_pattern.indexOf(getDefaultInputField(field)) == -1 && fields_inherit.indexOf(field.type) == -1)) {
        return true;
    }


    if (supported_fields.indexOf(getDefaultInputField(field)) == -1) {
        field['lv_validation'] = new Array();
        return true;
    }



    for (var key in field) {
        if (field.hasOwnProperty(key)) {
            var res = key.match(/gf_live_validation_/g);
            if (res === null) {
                continue;
            }
            jQuery("#" + key).val(field[key]);
        }
    }

    jQuery("#gf_live_validation_format").val(field["gf_live_validation_format"]);
    jQuery("#gf_live_validation_format").trigger("keyup");
    jQuery("#gf_live_validation_format_based").val(field["gf_live_validation_format_based"]);
    jQuery("#gf_live_validation_error_msg").val(field["gf_live_validation_error_msg"]);

    jQuery("#gf_live_validation_value").attr("checked", field["gf_live_validation_settings"] == true);
    var setting_elem = document.getElementById("gf_live_validation_value");

    if (setting_elem) {
        jQuery(setting_elem).trigger("change");
    }





    jQuery("#field_input_mask").trigger("change");
    jQuery("#field_phone_format").trigger("change");

//    jQuery("body").trigger("lv_admin_apply_validations", document.getElementById("gf_live_validation_value").checked);



});
jQuery("body").on("lv_admin_apply_validations", function(event, var1) {


    if (supported_fields.indexOf(field['type']) == -1) {
        return true;
    }

    switch (getDefaultInputField(field)) {

        case "date":

            if (field.dateType == "datepicker") {
                field['lv_validation'] = new Array();
                var field_Arr = new Array();
                if (field['isRequired'] == true) {
                    field_Arr.push({"type": "presence", "ftype": "text"});
                }

                if (var1 == true) {
                    field_Arr.push({"type": "pattern", "format": document.getElementById("gf_live_validation_format_based").value, "error": document.getElementById("gf_live_validation_error_msg").value, "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
                }
                field['lv_validation'][0] = field_Arr;
                show_checkbox_for_toggle();
            }

            if (field.dateType == "datedropdown") {


                var All_fields = jQuery(".gf_live_validation_type_settings_is_" + getDefaultInputField(field) + " .gf_lv_is_pattern_field");

                field['lv_validation'] = new Array();
                if (All_fields.length > 0) {

                    for (var incr_2 = 0; incr_2 < All_fields.length; incr_2++) {



                        var field_Arr = new Array();
                        if (field['isRequired'] == true) {
                            field_Arr.push({"type": "presence", "ftype": "select"});
                        }


                        field['lv_validation'].push(field_Arr);
                    }




                }


                hide_it_completely();
                jQuery(".gf_live_validation_type_settings_valid").slideUp();
            }

            if (field.dateType == "datefield") {
                show_checkbox_for_toggle();
            }

            break;
        case "name":
            break;



        case "phone":

            if (field.phoneFormat == "standard") {


                field['lv_validation'] = new Array();
                var field_Arr = new Array();
                if (field['isRequired'] == true) {
                    field_Arr.push({"type": "presence", "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
                }


                field['lv_validation'][0] = field_Arr;
            } else {
                field['lv_validation'] = new Array();
                var field_Arr = new Array();
                if (field['isRequired'] == true) {
                    field_Arr.push({"type": "presence", "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
                }

                if (var1 == true) {
                    field_Arr.push({"type": "pattern", "format": document.getElementById("gf_live_validation_format_based").value, "error": document.getElementById("gf_live_validation_error_msg").value, "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
                }
                field['lv_validation'][0] = field_Arr;
            }
            break;

        case "email":

            field['lv_validation'] = new Array();
            var field_Arr = new Array();
            if (field['isRequired'] == true) {
                field_Arr.push({"type": "presence", "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
            }

            if (var1 == true) {
                field_Arr.push({"type": "pattern", "format": document.getElementById("gf_live_validation_format_based").value, "error": document.getElementById("gf_live_validation_error_msg").value, "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
            }
            field['lv_validation'][0] = field_Arr;
            if (document.getElementById("gfield_email_confirm_enabled").checked == true) {
                var currLength = field['lv_validation'].length;
              
                field["lv_validation"][currLength] = field['lv_validation'][0];
           
                
                
            }
            else {
                if (field['lv_validation'].length == 2) {

                    field['lv_validation'].splice(0, 1);
                }

            }





            break;

        case "address":



            if (field.addressType !== "international") {



                if (field['isRequired'] == true) {
                    field['lv_validation'][3] = new Array({"type": "presence", "ftype": "select"});

                    if (typeof field['lv_validation'][5] !== "undefined") {
                        field['lv_validation'].splice(5, 1);
                    }
                }
                jQuery(".gf_live_validation_address_toggle").slideUp();
            }
            else {

                if (field['isRequired'] == true) {
                    if (typeof field['lv_validation'][5] == "undefined") {
                        field['lv_validation'].push(new Array({"type": "presence", "ftype": "select"}));
                    }

                }


                //  jQuery(".gf_live_validation_address_toggle").slideDown();
            }
            
            if(field['lv_validation'].length > 0){
                
                for(var key in field['lv_validation'][1]){
                   if(field['lv_validation'][1][key]['type'] == "presence"){
                        field['lv_validation'][1].splice(key, 1);
                   }
                }
            }
//            




            break;

        case "time":


            if (field['timeFormat'] !== "12") {



                if (field['isRequired'] == true) {


                    if (typeof field['lv_validation'][2] !== "undefined") {
                        field['lv_validation'].splice(2, 1);
                    }
                }

            }
            else {

                if (field['isRequired'] == true) {
                    if (typeof field['lv_validation'][2] == "undefined") {
                        field['lv_validation'].push(new Array({"type": "presence", "ftype": "select"}));
                    }
                }
                else {
                    if (typeof field['lv_validation'][2] !== "undefined") {
                        field['lv_validation'].splice(2, 1);
                    }
                }


            }
            break;

        default:

            field['lv_validation'] = new Array();
            var field_Arr = new Array();
            if (field['isRequired'] == true) {
                field_Arr.push({"type": "presence", "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
            }

            if (var1 == true && (field['inputMask'] == false || typeof field['inputMask'] == "undefined")) {
                field_Arr.push({"type": "pattern", "format": document.getElementById("gf_live_validation_format_based").value, "error": document.getElementById("gf_live_validation_error_msg").value, "ftype": jQuery("#gf_live_validation_format").attr("data-ftype")});
            }
            field['lv_validation'][0] = field_Arr;




    }





});
function getDefaultPattern(fieldtype) {

    switch (fieldtype) {
        case "date":
            return "text";
            break;
        case "phone":
            return "text";
        case "number":
            return "text";
        case "email":
            return "text";
        case "website":
            return "text";
        case "post_title":
            return "text";
        case "post_content":
            return "text";
        case "post_excerpt":
            return "text";

        default:
            return fieldtype;
    }




}


function getDefaultInputField(field_custom) {

    if (typeof field_custom['inputType'] !== "undefined" && field_custom['inputType'] !== "") {
        return field_custom['inputType'];
    } else {
        return field_custom['type'];
    }
}