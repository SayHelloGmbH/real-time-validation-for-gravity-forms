<?php

/**
 * Core class : Contains the code for front end modules
 */
if (!class_exists('Gravity_Forms_Live_Validation')) {

    class Gravity_Forms_Live_Validation {
        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @since   1.0.0
         *
         * @var     string
         */

        const VERSION = '1.7.0';

        /*
         * Plugin slug : used plugin wide
         */

        protected $plugin_slug = 'real-time-validation';

        /**
         * Instance of this class.
         *
         * @since    1.0.0
         *
         * @var      object
         */
        protected static $instance = null;
        public $plugin_title = "Real Time Validation";
        public $plugin_main_title = "Real Time Validation for Gravity Forms";
        /**
         * Initialize the plugin by setting localization and loading public scripts
         * and styles.
         *
         * @since     1.0.0
         */

        /**
         * Supported fields that we push our support for
         * @var type
         */
        public $supported_fields = array('text', 'textarea', 'radio', 'checkbox', 'select', 'multiselect', 'number', 'name', 'date', 'phone', 'email', 'address', 'website', 'time', 'post_title', 'post_content', "post_excerpt", 'post_tags', 'post_category', 'post_custom_field', 'product', 'option', 'quantity', 'shipping');
        public $default_messages = "";
        public $has_ajax = false;
        public $is_submission = false;
        public $is_paging = false;
        public $submission = null;
        public $script;

        private function __construct() {


            // Load public-facing style sheet and JavaScript.
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));


            add_filter("gform_pre_render", array($this, "lv_apply_validations_to_form"));


            add_filter("gform_form_args", array($this, "lv_get_shortcode_attrs"));
            add_action('gform_post_submission', array($this, 'lv_after_submission_gf'), 10, 2);
            add_action("gform_post_paging", array($this, 'lv_after_paging'), 10, 2);

            add_action("lv_pre_apply_validation", array($this, 'lv_modify_validations'), 10, 2);

            add_action('init', array($this, "lv_load_dependencies"));
            add_filter('gform_logging_supported', array($this, 'set_logging_supported'));

            add_action('wp_footer', array($this, 'print_script'));
        }

        /**
         * Return the plugin slug.
         *
         * @since    1.0.0
         *
         * @return    Plugin slug variable.
         */
        public function get_plugin_slug() {
            return $this->plugin_slug;
        }

        /**
         * Return an instance of this class.
         *
         * @since     1.0.0
         *
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Register and enqueue public-facing style sheet.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {
//            wp_enqueue_style($this->plugin_slug . '-live-validation', plugins_url('assets/css/validation.css', __FILE__), array(), self::VERSION);

            wp_enqueue_style($this->plugin_slug . '-plugin-styles', plugins_url('assets/css/public.css', __FILE__), array(), self::VERSION);
        }

        /**
         * Register and enqueues public-facing JavaScript files.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {
            wp_enqueue_script($this->plugin_slug . '-live-validation', plugins_url('assets/js/livevalidation_standalone.js', __FILE__), array('jquery'), self::VERSION);
            wp_enqueue_script($this->plugin_slug . '-plugin-script', plugins_url('assets/js/public.js', __FILE__), array('jquery'), self::VERSION);

            /*             * default skin js * */
            wp_enqueue_script($this->plugin_slug . '-plugin-defaiult-skin-js', plugins_url('assets/js/default_validation.js', __FILE__), array('jquery'), self::VERSION);
        }

        public function lv_load_dependencies() {

            include_once plugin_dir_path(__FILE__) . "/includes/lv-all-ajax.php";
        }

        /**
         * Check whether validaiton is on for the form
         * @param array $form Gravit form
         * @return boolean true on success, False on failture
         */
        public function is_enable_validations($form) {

            if (rgar($form, 'gform_enable_lv') == 1) {
                return true;
            }
            return false;
        }

        /**
         * Prepere validation Javascript string , that will further be called over web page
         * @param array $validation validation array
         * @param array $field field array
         * @param int $k identifier
         * @return type nothing
         */
        public function get_validation_string($validation, $field) {


            if (!in_array($field['type'], $this->supported_fields)) {
                return;
            }


            $get_default_error = $this->get_default_error($field);
            $is_single = false;
            if (is_null($validation) || !$validation) {
                return;
            }
            $inputs = $field['inputs'];
            if ($field['type'] == "email" && is_array($inputs) && count($inputs) > 1) {
                echo ' hasSubFields.push("email"); ';
            }


            $validation = apply_filters("lv_pre_apply_validation", $validation, $field);


            foreach ($validation as $key => $config) {

                $current_key = 0;
                if (!in_array($this->getInputtype($field), array('checkbox', 'radio', "name", "date", "email", "address", "time"))) {


                    echo ' var f' . $field['id'] . ' = new LiveValidation("input_' . $field['formId'] . '_' . $field['id'] . '",jqr.extend({validMessage: " ", jqObj: jqr,fieldType:"' . $field['type'] . '" },additional_data )); ';
                    $current_key = $field['id'];
                    echo "all_validations[" . $field['formId'] . "][" . $field['id'] . "] =  f" . $field['id'] . ";";
                    $js_var = ' f' . $field['id'];
                }


                $js_var = ' f' . $field['id'];

                $incr = 0;

                foreach ($config as $eachValidation) {

                    if (!isset($eachValidation['ftype'])) {
                        $eachValidation['ftype'] = $eachValidation['fype'];
                    }

                    if ($incr == 0 && in_array($this->getInputtype($field), array('name', "date", "email", "address", "time"))):

                        echo ' var f' . $eachValidation['field_id'] . ' = new LiveValidation("input_' . $field['formId'] . '_' . $eachValidation['field_id'] . '",jqr.extend({validMessage: " ", jqObj: jqr,fieldType:"' . $field['type'] . '" },additional_data )); ';

                        echo "all_validations[" . $field['formId'] . "][\"" . $eachValidation['field_id'] . "\"] =  f" . $eachValidation['field_id'] . ";";
                        $current_key = $eachValidation['field_id'];

                    endif;
                    if (isset($eachValidation['field_id'])) {
                        $js_var = ' f' . $eachValidation['field_id'];
                    }


                    switch ($eachValidation['type']):


                        case "pattern" :


                            switch ($eachValidation['ftype']):


                                case "text":
                                    if ($eachValidation['format'] !== "")
                                        echo '' . $js_var . '.add(Validate.Format, { pattern: ' . $this->get_pattern_for_js($eachValidation['format']) . ', failureMessage: "' . $eachValidation['error'] . '" ,validMessage: " " ,jqObj: jqr } );';

                                    break;

                                case "textarea":
                                    if ($eachValidation['format'] !== "")
                                        echo '' . $js_var . '.add(Validate.Format,{ pattern: ' . $this->get_pattern_for_js($eachValidation['format']) . ',failureMessage: "' . $eachValidation['error'] . '",validMessage: " " ,jqObj: jqr });';


                                    break;

                                case "radio":
                                    break;

                                case "checkbox":
                                    break;

                                default:
                                    if ($eachValidation['format'] !== "")
                                        echo '' . $js_var . '.add(Validate.Format, { pattern: ' . $this->get_pattern_for_js($eachValidation['format']) . ', failureMessage: "' . $eachValidation['error'] . '" ,validMessage: " " ,jqObj: jqr } );';

                            endswitch;
                            break;
                        case "confirm":

                            switch ($eachValidation['ftype']):
                                case 'text':
                                     echo '' . $js_var . '.add(Validate.ConfirmEmail, { parentField: "input_' . $field['formId'] . '_' . $eachValidation['parent'] . '", failureMessage: "' . $eachValidation['error'] . '" ,validMessage: " " ,jqObj: jqr } );';

                                    break;
                                default:


                            endswitch;
                            break;
                        case "presence":

                            switch ($eachValidation['ftype']):


                                case "text":

                                    if (rgar($field, "inputMaskValue") == "" && (rgar($field, "phoneFormat") !== "standard")) {
                                        echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr });';
                                    } elseif (rgar($field, "phoneFormat") == "standard") {

                                        echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr,mask:"' . $this->get_masking_presence_validator("(999) 999-9999") . '",livevalidkey:"' . $current_key . '",form_id:"' . $field['formId'] . '" });';
                                    } else {
                                        echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr,mask:"' . $this->get_masking_presence_validator(rgar($field, "inputMaskValue")) . '",livevalidkey:"' . $current_key . '",form_id:"' . $field['formId'] . '" });';
                                    }
                                    break;

                                case "textarea":

                                    echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr });';
                                    break;

                                case "radio":

                                    $choices = $field['choices'];
                                    for ($i = 0; $i <= (count($choices) - 1); $i++) {
                                        echo ' var f' . $field['id'] . '_' . $i . ' = new LiveValidation("choice_' . $field['formId'] . '_' . $field['id'] . '_' . $i . '",jqr.extend({validMessage: " ", jqObj: jqr,fieldType:"' . $field['type'] . '" },additional_data )); ';
                                        echo "all_validations[" . $field['formId'] . "][" . $field['id'] . "] = f" . $field['id'] . "_" . $i . ";";
                                        echo 'f' . $field['id'] . '_' . $i . '.add(Validate.AcceptanceRadio,{ failureMessage: "' . $get_default_error . '" ,validMessage: " " ,jqObj: jqr,name_field:"input_' . $field['id'] . '",form_id:"' . $field['formId'] . '" });';
                                    }
                                    break;

                                case "checkbox":

                                    if (rgar($field, "displayAllCategories") == 1) {
                                        $args = array('hide_empty' => false, 'orderby' => 'name');


                                        $args = gf_apply_filters(array('gform_post_category_args', $field->id), $args, $field);
                                        $terms = get_terms('category', $args);


                                        for ($i = 1; $i <= (count($terms)); $i++) {


                                            echo ' var f' . $field['id'] . '_' . $i . ' = new LiveValidation("choice_' . $field['formId'] . '_' . $field['id'] . '_' . $i . '",jqr.extend({validMessage: " ", jqObj: jqr ,fieldType:"' . $field['type'] . '"},additional_data ));';
                                            echo "all_validations[" . $field['formId'] . "][" . $field['id'] . "] = f" . $field['id'] . "_" . $i . ";";
                                            echo 'f' . $field['id'] . '_' . $i . '.add(Validate.AcceptanceCheckbox,{ failureMessage: "' . $get_default_error . '" ,validMessage: " " ,jqObj: jqr,field_name:"input_' . $field['id'] . '",field_id:"' . $field['id'] . '",form_id:"' . $field['formId'] . '"});';
                                        }
                                    } else {


                                        echo ' var f' . $field['id'] . ' = new LiveValidation("input_' . $field['formId'] . '_' . $field['id'] . '",jqr.extend({validMessage: " ", jqObj: jqr ,fieldType:"' . $field['type'] . '"},additional_data ));';
                                        echo "all_validations[" . $field['formId'] . " ][" . $field['id'] . "] = f" . $field['id'] . ";";
                                        echo 'f' . $field['id'] . '.add(Validate.GFCheckboxes,{ failureMessage: "' . $get_default_error . '" ,validMessage: " " ,jqObj: jqr,field_name:"input_' . $field['formId'] . '_' . $field['id'] . '",field_id:"' . $field['id'] . '"});';
                                    }


                                    break;

                                case "select":
                                    echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr });';

                                    break;
                                case "number":
                                    echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr });';

                                    break;
                                case "multiselect":
                                    echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr });';

                                    break;


                                default:
                                    echo '' . $js_var . '.add(Validate.Presence,{ failureMessage: "' . $get_default_error . '",validMessage: " " ,jqObj: jqr});';

                            endswitch;
                            break;


                        default:
                    endswitch;
                    $incr++;
                }
            }
        }

        /**
         * Hooked to "gform_pre_render" in order to get form deta and print script over page
         * @param array $form Gravity form array
         * @return array  Form
         */
        public function lv_apply_validations_to_form($form) {
            if (!class_exists('GFFormDisplay')) {
                return $form;
            }

            if (isset(GFFormDisplay::$submission[$form['id']])) {
                $this->submission = GFFormDisplay::$submission[$form['id']];
            }


            /**
             * Checking suitable condition to apply validations, they can vary when we have support for more fields
             * it includes, Validation settings on/off , chekcing is ajax, checking if paging , checking if error,
             */
            if (!$this->is_enable_validations($form) || $this->is_submission || ($this->has_ajax && $this->is_paging) || ($this->has_ajax && (!is_null($this->submission) && $this->submission['is_valid'] == false)) || ( rgar($this->submission, 'saved_for_later') == true )) {
                return $form;
            }
            $max_count = max(wp_list_pluck($form['fields'], 'pageNumber'));


            /*
             * Adding necessery file for default array
             */
            include plugin_dir_path(__FILE__) . 'includes/default-validations.php';
            include plugin_dir_path(__FILE__) . 'includes/default_messages.php';

            $this->default_messages = apply_filters('lv_default_error_messages', $default_messages);
            ob_start();
            echo '<script type="text/javascript"> if(typeof window.lv_formIDs == "undefined"){ window.lv_formIDs = []; }  window.lv_formIDs.push(' . $form['id'] . ');</script>';

            $ajax = "no";
            if ($this->has_ajax):
                $ajax = "yes";
            endif;
            echo '<script type="text/javascript">var lv_gf_is_ajax = "' . ($ajax) . '";  if(typeof window.all_validations == "undefined"){ window.all_validations = {}; } </script>';
            echo ' <script type="text/javascript">window.all_validations[' . $form["id"] . '] = {};</script> ';
            Gravity_Forms_Live_Validation::log_debug("Initializing Validation for formID= " . $form['id']);

            $current_page = isset(GFFormDisplay::$submission[$form['id']]) ? GFFormDisplay::$submission[$form['id']]['page_number'] : 1;
            if ($max_count > 1 && $this->has_ajax) {


                for ($i = 1; $i <= $max_count; $i++) {

                    echo '<script type="text/javascript">
             

      
            

            jQuery(document).bind("gform_post_render", function(event,data,page){
            
           
                
             
               if(page == ' . $i . '){
                 

             try {
            
                LiveValidationForm.instances = {};
              
                var lv_gf_is_ajax = "' . ($ajax) . '";
                    var jqr = jQuery;';

                    foreach ($form['fields'] as $key => $value) {

                        $validation = $value['lv_validation'];
                        if (is_array($validation) && count($validation) > 0 && (($value->pageNumber == $i))) {


                            $validation = $this->sanitize_validations($value['lv_validation'], $default);


                            $this->get_validation_string($validation, $value);
                        }
                    }

                    echo '   } catch(err) {
               console.log(err);    
  console.error("Error Applying validations!!");
}      }}); </script>';
                }
            } else {
                echo '<script type="text/javascript">
             

      
            

            jQuery(document).bind("gform_post_render", function(event,data,page){
            
                
                  
             try {

                    var jqr = jQuery; ';

                foreach ($form['fields'] as $key => $value) {

                    $validation = $value['lv_validation'];
                    if (is_array($validation) && count($validation) > 0 && (($value->pageNumber == $current_page))) {


                        $validation = $this->sanitize_validations($value['lv_validation'], $default);


                        $this->get_validation_string($validation, $value);
                    }
                }

                echo '   } catch(err) {
               console.log(err);    
  console.error("Error Applying validations!!");
}}); </script>';
            }

            $this->script .= ob_get_clean();
            return $form;
        }

        public function sanitize_validations($validation, $default) {

            $clone_valid = false;
            if ($validation && is_array($validation) && count($validation) > 0) {

                foreach ($validation as $key => $validation) {

                    foreach ($validation as $key_second => $validation_inner) {
                        $clone_valid[$key][$key_second] = wp_parse_args($validation_inner, $default[$validation_inner['type']]);
                    }
                }
            }

            return $clone_valid;
        }

        /**
         * Helper function that converts any array to object
         * @param mixed $d array or Object
         * @return array
         */
        public function objectToArray($d) {
            if (is_object($d)) {
                // Gets the properties of the given object
                // with get_object_vars function
                $d = get_object_vars($d);
            }

            if (is_array($d)) {
                /*
                 * Return array converted to object
                 * Using __FUNCTION__ (Magic constant)
                 * for recursive call
                 */
                return array_map(array(__CLASS__, __FUNCTION__), $d);
            } else {
                // Return array
                return $d;
            }
        }

        /**
         * Get pattern using base decode
         * Validating and cleaning up the pattern to make it ready to apply over field
         * @param string $format Regex
         * @return string
         */
        public function get_pattern_for_js($format) {


            if ($format == "") {
                return "";
            }


            $format = base64_decode($format);
            if (substr($format, 0, 1) !== '/') {
                $format = "/" . $format;
            }


            $pattern = '/\/[g,m,i,x,X,s,u.U,A,J]*$/';

            //Below code will check for modifier if exists then replace it with /i
            //If doesn't exist then leaving it without modifiers
            //matching if format already has any modifiers?
            preg_match($pattern, $format, $matches);

            if ($matches && is_array($matches) && count($matches) > 0) {

                if ($matches[0] == "/") {
                    return $format;
                }
                return str_replace($matches[0], "/i", $format);
            } else {

                if (substr($format, strlen($format) - 1, 1) !== '/') {
                    return $format . "/";
                } else {
                    return $format;
                }
            }
        }

        /**
         * Helper function to debug the current data
         * @param type $thing
         * @param type $is_dump
         * @return boolean
         */
        public function _debug($thing, $is_dump = false) {

            if (WP_DEBUG == false) {
                return false;
            }
            echo "<pre>";
            (!$is_dump) ? print_r($thing) : var_dump($thing);
            echo "</pre>";
        }

        /**
         * Get error message for the field
         * It returnes from the custom message set against field OR get from default set
         * @param object $field
         * @return string Error message
         */
        public function get_default_error($field) {

            if (rgar($field, 'errorMessage') !== "") {
                return rgar($field, 'errorMessage');
            }
            return rgar($this->default_messages, rgar($field, 'type'));
        }

        /**
         * Checking if ajax is enabled for the current form
         * @param array $attrs
         * @return type
         */
        public function lv_get_shortcode_attrs($attrs) {


            if (isset($attrs['ajax']) && $attrs['ajax'] == 1) {
                $this->has_ajax = true;
            }
            return $attrs;
        }

        /**
         *
         * @param type $lead
         * @param type $form
         */
        public function lv_after_submission_gf($lead, $form) {
            $this->is_submission = true;
        }

        /**
         * Get IF request has pagination
         * @param lead $lead
         * @param array $form
         */
        public function lv_after_paging($lead, $form) {
            $this->is_paging = true;
        }

        /**
         * Prepering sub fields validations array to make it work with or logic
         * It will be treated as the normal field over main function
         * @param array $validations
         * @param arry $field
         * @return array
         */
        public function lv_modify_validations($validations, $field) {


            $apply_validation = array();
            switch (rgar($field, 'type')):

                case "name":

                    foreach (rgar($field, 'inputs') as $k => $subfield) {

                        if (isset($subfield['isHidden']) && $subfield['isHidden'] == true) {
                            continue;
                        }

                        if (!isset($validations[$k])) {
                            continue;
                        }


                        /**
                         * Handling for the optional middle name field in the name field,
                         * removing presense validation from the validations.```
                         */
                        if (2 === $k) {
                            $getallvalidations = wp_list_pluck($validations[$k], 'type');
                            $get_prresense_validation = array_search('presence', $getallvalidations);

                            if (false !== $get_prresense_validation) {
                                unset($validations[$k][$get_prresense_validation]);
                            }
                            $validations[$k] = array_values($validations[$k]);
                        }

                        $apply_validation[$k] = $validations[$k];


                        foreach ($apply_validation[$k] as &$val) {
                            $val['field_id'] = str_replace(".", "_", rgar($subfield, "id"));
                        }
                    }
                    return $apply_validation;
                    break;


                case "address":

                    foreach (rgar($field, 'inputs') as $k => $subfield) {

                        if (isset($subfield['isHidden']) && $subfield['isHidden'] == true) {
                            continue;
                        }
                        if (!isset($validations[$k])) {
                            continue;
                        }
                        $apply_validation[$k] = $validations[$k];


                        foreach ($apply_validation[$k] as &$val) {
                            $val['field_id'] = str_replace(".", "_", rgar($subfield, "id"));
                        }
                    }
                    return $apply_validation;
                    break;

                case "time":

                    foreach (rgar($field, 'inputs') as $k => $subfield) {

                        if (isset($subfield['isHidden']) && $subfield['isHidden'] == true) {
                            continue;
                        }
                        if (!isset($validations[$k])) {
                            continue;
                        }
                        $apply_validation[$k] = $validations[$k];


                        foreach ($apply_validation[$k] as &$val) {
                            $val['field_id'] = str_replace(".", "_", rgar($subfield, "id"));
                        }
                    }
                    return $apply_validation;
                    break;


                case "email":

                    if (rgar($field, 'inputs') !== "") {
                        $first_elem = null;
                        foreach (rgar($field, 'inputs') as $k => $subfield) {

                            if (isset($subfield['isHidden']) && $subfield['isHidden'] == true) {
                                continue;
                            }

                            $apply_validation[$k] = $validations[$k];
                            if ($k !== 0) {
                                array_push($apply_validation[$k], array(
                                    'type' => 'confirm',
                                    'error' => apply_filters('lv_email_confirmation_failed_message',__('Emails Do not match','real-time-validation-for-gravity-forms')),
                                    'ftype' => 'text',
                                    'parent' => str_replace(".", "_", rgar($first_elem, "id")),
                                ));
                            } else {
                                $first_elem = $subfield;
                            }


                            foreach ($apply_validation[$k] as &$val) {
                                $val['field_id'] = str_replace(".", "_", rgar($subfield, "id"));
                            }
                        }

                        return $apply_validation;
                    } else {

                        $validations[0][0]['field_id'] = rgar($field, 'id');

                        return $validations;
                    }
                    break;
                case "date":

                    if (rgar($field, 'inputs') !== "") {
                        foreach (rgar($field, 'inputs') as $k => $subfield) {

                            if (isset($subfield['isHidden']) && $subfield['isHidden'] == true) {
                                continue;
                            }
                            $apply_validation[$k] = $validations[$k];


                            foreach ($apply_validation[$k] as &$val) {
                                $val['field_id'] = str_replace(".", "_", rgar($subfield, "id"));
                            }
                        }

                        return $apply_validation;
                    } else {

                        $validations[0][0]['field_id'] = rgar($field, 'id');
                        return $validations;
                    }
                default:
                    return $validations;
            endswitch;
        }

        /**
         * Get input type for each field type
         * so for "custom field for post" can be any field under basic and advanced
         * @param array $field
         * @return string
         */
        public function getInputtype($field) {

            if (isset($field['inputType']) && $field['inputType'] !== '') {
                return $field['inputType'];
            } else {
                return $field['type'];
            }
        }

        /**
         * Handling masking field
         * it returns the corresponding expected input against given mask
         * @param string $mask
         * @return string
         */
        public function get_masking_presence_validator($mask) {

            if (!$mask) {
                return "";
            }

            $mask = str_replace("9", "_", $mask);
            $mask = str_replace("a", "_", $mask);
            $mask = str_replace("*", "_", $mask);
            $mask = str_replace("?", "", $mask);

            return $mask;
        }

        public function set_logging_supported($plugins) {
            $plugins[$this->plugin_slug] = $this->plugin_main_title;

            return $plugins;
        }

        public static function log_error($message) {
            if (class_exists('GFLogging')) {
                GFLogging::include_logger();
                GFLogging::log_message(self::$plugin_slug, $message, KLogger::ERROR);
            }
        }

        public static function log_debug($message) {
            if (class_exists('GFLogging')) {
                GFLogging::include_logger();

                GFLogging::log_message('real-time-validation', $message, KLogger::DEBUG);
            }
        }

        public function print_script() {
            echo $this->script;
        }

    }

}




