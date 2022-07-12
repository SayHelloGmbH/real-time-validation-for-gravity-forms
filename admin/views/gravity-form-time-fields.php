
<li class="gf_live_validation_type_settings_is_time field_setting" style="display: none;">

    <label for="gf_live_validation_format_time_0" class="" >
        <?php _e("Enter pattern for Hours", "real-time-validation-for-gravity-forms"); ?>
        <?php gform_tooltip("gf_live_validation_format_time_0") ?>
    </label>

    <textarea class="gf_lv_is_pattern_field" data-ftype="text" placeholder="Your Regex Here..." id="gf_live_validation_format_time_0" rows="5" cols="40" onkeyup="SetFieldProperty('gf_live_validation_format_time_0', createbaseHash(this));" ></textarea>
    <span class="lv_open_support_box_outer"> <b>Need Help with RegEx Patterns? </b><a href="javascript:void(0);" class="lv_open_support_box"> Click Here</a></span> 
                        
    <span class="lv-toggle-off lv_admin_input_error">OOPS! It doesn't seems like a valid RegEx pattern. </span>


</li>


<li class="gf_live_validation_type_settings_is_time_error field_setting " style="display: none;">

    <label for="gf_live_validation_error_msg_time_0" class="" >
        <?php _e("Error message", "real-time-validation-for-gravity-forms"); ?>
        <?php gform_tooltip("gf_live_validation_error_msg_time_0"); ?>
    </label>

    <input type="text" class="gf_live_validation_error_msg_fields" id="gf_live_validation_error_msg_time_0" onkeyup="SetFieldProperty('gf_live_validation_error_msg_time_0', moveErrorTohidden(this));" />


</li>




<li class="gf_live_validation_type_settings_is_time field_setting" style="display: none;">

    <label for="gf_live_validation_format_time_1" class="" >
        <?php _e("Enter pattern for Minutes", "real-time-validation-for-gravity-forms"); ?>
        <?php gform_tooltip("gf_live_validation_format_time_1") ?>
    </label>

    <textarea class="gf_lv_is_pattern_field" data-ftype="text"  placeholder="Your Regex Here..." id="gf_live_validation_format_time_1" rows="5" cols="40" onkeyup="SetFieldProperty('gf_live_validation_format_time_1', createbaseHash(this));" ></textarea>
    <span class="lv_open_support_box_outer"> <b>Need Help with RegEx Patterns? </b><a href="javascript:void(0);" class="lv_open_support_box"> Click Here</a></span> 
                        
    <span class="lv-toggle-off lv_admin_input_error">OOPS! It doesn't seems like a valid RegEx pattern. </span>


</li>   

<li class="gf_live_validation_type_settings_is_time_error field_setting " style="display: none;">

    <label for="gf_live_validation_error_msg_time_1" class="" >
        <?php _e("Error message", "real-time-validation-for-gravity-forms"); ?>
        <?php gform_tooltip("gf_live_validation_error_msg_time_1"); ?>
    </label>

    <input type="text" class="gf_live_validation_error_msg_fields" id="gf_live_validation_error_msg_time_1" onkeyup="SetFieldProperty('gf_live_validation_error_msg_time_1', moveErrorTohidden(this));" />


</li>




