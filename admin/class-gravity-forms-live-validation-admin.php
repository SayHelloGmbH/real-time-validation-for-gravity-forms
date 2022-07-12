<?php
/**
 * Class handles logic for admin panel activites
 */
if (!class_exists('Gravity_Forms_Live_Validation_Admin')) {

    class Gravity_Forms_Live_Validation_Admin {

        /**
         * Instance of this class.
         *
         * @since    1.0.0
         *
         * @var      object
         */
        protected static $instance = null;

        /**
         * Slug of the plugin screen.
         *
         * @since    1.0.0
         *
         * @var      string
         */
        protected $plugin_screen_hook_suffix = null;
        public $plugin = null;
        public $lv_formID = 0;
        public $lv_form_meta = null;
        public $lv_is_enabled = false;
        public $lv_supported_fields_for_help = array('text', 'textarea', 'number', 'name', 'date', 'phone', 'email', 'address', 'website', 'time');
        public $lv_supported_fields_for_presence = array('text', 'textarea', 'radio', 'checkbox', 'select', 'multiselect', 'number', 'name', 'date', 'phone', 'email', 'address', 'website', 'time', 'post_title', 'post_content', "post_excerpt", 'post_tags', 'post_category', 'post_custom_field', 'product', 'quantity', 'option', 'shipping');
        public $lv_has_sub_fields = array('name', 'date', 'email', 'address');
        public $lv_has_multiple_fields = array('post_tags', 'post_category', 'post_custom_field', 'product', 'quantity', 'option', 'shipping');

        function __construct() {



            /**
             * Getting plugin instance
             */
            $this->plugin = Gravity_Forms_Live_Validation::get_instance();
            $this->plugin_slug = $this->plugin->get_plugin_slug();

            /**
             * WP hooks
             */
            add_action('admin_enqueue_scripts', array($this, 'lv_admin_scripts'));
            add_action('admin_head', array($this, "lv_install_help"));
            add_action('admin_init', array($this, "lv_maybe_toggle_setting_from_list"), 100);
            add_action('admin_footer', array($this, "lv_script_back"));
            add_action('init', array($this, "lv_load_dependencies"));
            add_filter('plugin_action_links_' . plugin_basename(LV_ROOT . 'real-time-validation-for-gravity-form.php'), array($this, 'lv_admin_plugin_settings_link'));


            /**
             * Gf hooks 
             */
            add_action("gform_field_advanced_settings", array($this, "enable_live_valiadtion_advanced_settings"), 10, 2);
            add_action("gform_editor_js", array($this, "gf_live_valiadtion_editor_script"));

            add_filter('gform_form_settings_menu', array($this, 'lv_maybe_save_settings_tabs'), 10, 2);
            add_action('gform_form_settings_page_lv_form_setting', array($this, 'lv_form_setting_page'));
            add_filter("gform_form_actions", array($this, "lv_modify_toolbar"), 10, 2);
            add_filter('gform_tooltips', array($this, 'lv_tooltips_gform'));
            add_filter("gform_admin_pre_render", array($this, "lv_messaging"), 10);






            /**
             * Plugin Hooks
             */
            add_filter("lv_auto_populate_validation_date", array($this, "lv_date_field_auto_apply"), 10, 2);
            add_filter("lv_auto_populate_validation_email", array($this, "lv_email_field_auto_apply"), 10, 2);

            add_filter("lv_auto_populate_validation_address", array($this, "lv_address_field_auto_apply"), 10, 2);

            add_filter("lv_auto_populate_validation_time", array($this, "lv_time_field_auto_apply"), 10, 2);
        }

        /**
         * Return an instance of this class.
         *
         * @since     1.0.0
         *
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

            if (!is_super_admin()) {
                return;
            }


            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Loading plugin classes
         */
        public function lv_load_dependencies() {

            require_once plugin_dir_path(__FILE__) . '/includes/show-lv-on-form-list.php';
            require_once plugin_dir_path(__FILE__) . '/includes/class-lv-dashboard.php';
            require_once plugin_dir_path(__FILE__) . '/includes/class-lv-pointers.php';
        }

        /**
         * Place settings tab
         * @param array $tabs existing tabs
         * @param type $id
         * @return string
         */
        public function lv_maybe_save_settings_tabs($tabs, $id) {

            $tabs['40'] = array(
                'name' => 'lv_form_setting',
                'label' => $this->plugin->plugin_title,
            );
            return $tabs;
        }

        /**
         * Handles rendering and saving for the settings page
         */
        public function lv_form_setting_page() {


            $this->lv_maybe_save_settings();

            GFFormSettings::page_header();


            if (is_null($this->lv_form_meta)) {
                $this->lv_form_meta = GFFormsModel::get_form_meta(rgget('id'));
            }
            ?> <form method="POST"> <?php wp_nonce_field('gforms_update_settings', 'gforms_update_settings') ?>
                <h3><span><i class="fa fa-cogs"></i> <?php printf(esc_html__('%s Settings ', 'real-time-validation-for-gravity-forms'),$this->plugin->plugin_title); ?></span></h3>
                <table class="form-table">

                    <tr valign="top">
                        <th scope="row">
                            <label for=""><?php printf(esc_html__('Enable %s', 'real-time-validation-for-gravity-forms'),$this->plugin->plugin_title); ?></label> 
                        </th>
                        <td>
                            <input type="checkbox" name="gform_enable_lv" value="1" <?php echo (isset($this->lv_form_meta['gform_enable_lv']) && $this->lv_form_meta['gform_enable_lv'] == 1) ? "checked='checked'" : '' ?> id="gform_enable_lv" /> &nbsp;&nbsp;
                            <br />
                            <span class="gf_settings_description"><?php esc_html_e('Enable or Disable real time validation on this form.', 'real-time-validation-for-gravity-forms'); ?></span>

                        </td>
                    </tr>






                </table>





                <?php if (GFCommon::current_user_can_any('gravityforms_edit_settings')) { ?>
                    <p class="submit" style="text-align: left;">
                        <?php
                        $save_button = '<input type="submit" name="submit" value="' . esc_html__('Save Settings', 'real-time-validation-for-gravity-forms') . '" class="button-primary gfbutton"/>';

                        /**
                         * Filters through and allows modification of the Settings save button HTML in a Form
                         *
                         * @param string $save_button
                         */
                        echo apply_filters('gform_settings_save_button', $save_button);
                        ?>
                    </p>
                <?php }
                ?> </form><?php
            GFFormSettings::page_footer();
        }

        /**
         * Save gf settigs
         */
        public function lv_maybe_save_settings() {

            if (GFSettings::get_subview() == "lv_form_setting" && rgpost('submit') !== "") {

                $get_prev_data = GFFormsModel::get_form_meta(rgget('id'));

                if (!isset($get_prev_data['gform_enable_lv'])) {
                    Gravity_Forms_Live_Validation::log_debug("Initializing fields validation for the first time for form= " . rgget('id'));

                    $get_prev_data['fields'] = $this->initialte_populate_field($get_prev_data['fields']);
                }
                $get_prev_data['gform_enable_lv'] = rgpost('gform_enable_lv');

                GFFormsModel::update_form_meta(rgget('id'), $get_prev_data);
                Gravity_Forms_Live_Validation::log_debug("Toggle RV validation state to " . rgpost('gform_enable_lv'));

                $this->lv_form_meta = $get_prev_data;
            }
        }

        /**
         * function to toggle state when changing from form list
         */
        public function lv_maybe_toggle_setting_from_list() {


            if (isset($_GET['lvformid']) && rgget('page') == "gf_edit_forms") {

                wp_verify_nonce(rgget('nonce'), rgget('action'));


                $get_prev_data = GFFormsModel::get_form_meta(rgget('lvformid'));

                if (!isset($get_prev_data['gform_enable_lv'])) {
                    Gravity_Forms_Live_Validation::log_debug("Initializing fields validation for the first time for form= " . rgget('lvformid'));

                    $get_prev_data['fields'] = $this->initialte_populate_field($get_prev_data['fields']);
                }
                $get_prev_data['gform_enable_lv'] = (rgget('elemstate') == "true" ? true : false);
                Gravity_Forms_Live_Validation::log_debug("Toggle RV validation state to " . rgget('elemstate'));
                GFFormsModel::update_form_meta(rgget('lvformid'), $get_prev_data);

                //redirect after toggle so that it wont get appended to the url.
                wp_safe_redirect(admin_url('admin.php?page=gf_edit_forms'));
            }
        }

        /**
         * Auto apply validations to req fields
         * @param type $fields
         * @return type
         */
        public function initialte_populate_field($fields) {
            $field_clone = array();
            if (is_array($fields) && count($fields) > 0) {

                include_once plugin_dir_path(__FILE__) . 'includes/admin-validations-auto.php';
                foreach ($fields as $field) {
                    $f = $field;
                    if (in_array(rgar($field, 'type'), $this->lv_supported_fields_for_presence) && rgar($field, 'isRequired')) {


                        if (in_array(rgar($field, 'type'), $this->lv_has_sub_fields)) {


                            $f['lv_validation'] = apply_filters("lv_auto_populate_validation_" . rgar($field, 'type'), rgar($default_admin_vaidations_auto, rgar($field, 'type')), $field);
                        } elseif (in_array(rgar($field, 'type'), $this->lv_has_multiple_fields)) {



                            $f['lv_validation'] = apply_filters("lv_auto_populate_validation_" . rgar($field, 'inputType'), rgar($default_admin_vaidations_auto, rgar($field, 'inputType')), $field);
                        } else {
                            $f['lv_validation'] = array(
                                array(array('type' => 'presence', 'ftype' => $this->get_presence_ftype(rgar($field, 'type')))
                            ));
                        }
                    }
                    $field_clone[] = $f;
                }
            }
            return $field_clone;
        }

        /**
         * Render the settings page for this plugin.
         *
         * @since    1.0.0
         */
        public function lv_admin_scripts() {
            wp_enqueue_script($this->plugin_slug . '-admin-switcher', plugins_url('assets/js/switcher.js', __FILE__), array('jquery'), Gravity_Forms_Live_Validation::VERSION);
            wp_enqueue_style($this->plugin_slug . '-admin-styles-switcher', plugins_url('assets/css/switcher.css', __FILE__), array(), Gravity_Forms_Live_Validation::VERSION);


            wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), Gravity_Forms_Live_Validation::VERSION);
            wp_enqueue_script($this->plugin_slug . '-admin-script-formlist', plugins_url('assets/js/form-list.js', __FILE__), array('jquery'), Gravity_Forms_Live_Validation::VERSION);

            wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), Gravity_Forms_Live_Validation::VERSION);
        }

        /**
         * Get all the validation type available for validations 
         * @return string
         */
        public function get_validation_type() {
            $validations = array(
                'Format' => 'Format',
            );
            return $validations;
        }

        /**
         * Form editor HTML
         * @param type $position position where field settings will remain
         * @param type $form_id
         */
        public function enable_live_valiadtion_advanced_settings($position, $form_id) {

            if (!$this->lv_formID) {
                $this->lv_formID = $form_id;
            }
            $this->lv_is_enabled = false;

            $get_prev_data = GFFormsModel::get_form_meta(rgget('id'));

            if (isset($get_prev_data['gform_enable_lv']) && $get_prev_data['gform_enable_lv'] == 1)
            $this->lv_is_enabled = true;

            if ($position == 200) {


                add_thickbox();

                /**
                 * Loading help <div> for each pages
                 */
                foreach ($this->lv_supported_fields_for_help as $field) {

                    if (is_file(plugin_dir_path(__FILE__) . 'views/help-pages/help-' . $field . '.phtml')) {
                        echo '<div id="my-help-div-' . $field . '" style="display:none;">';
                        include plugin_dir_path(__FILE__) . 'views/help-pages/header.phtml';
                        include_once plugin_dir_path(__FILE__) . 'views/help-pages/help-' . $field . '.phtml';
                        include plugin_dir_path(__FILE__) . 'views/help-pages/footer.phtml';
                    }
                }


                $get_allvalidationtype = self::get_validation_type();
                ?>
                <li class="gf_live_validation_type_settings field_setting gf_live_validation_type_settings_level_1">
                    <input type="checkbox" id="gf_live_validation_value" onclick="SetFieldProperty('gf_live_validation_settings', this.checked);" /> 
                    <label for="gf_live_validation_value" class="inline">
                        <?php _e("Validate Real Time Input", "real-time-validation-for-gravity-forms"); ?>
                        <?php gform_tooltip("form_gf_live_validation_value") ?>
                    </label>
                </li>


                <li class="gf_live_validation_type_settings field_setting">
                    <select name="gf_live_validation_type" id="gf_live_validation_type" onchange="SetFieldProperty('gf_live_validation_type', this.value);">
                        <?php
                        foreach ($get_allvalidationtype as $key => $value) {
                            echo '<option value="' . $key . '">' . $value . '</option>';
                        }
                        ?>
                    </select>
                    <label for="gf_live_validation_type" class="inline">
                        <?php _e("Validation Type", "real-time-validation-for-gravity-forms"); ?>
                        <?php gform_tooltip("form_gf_live_validation_type") ?>
                    </label>
                </li>







                <input type="hidden" name="gf_live_validation_format_based" id="gf_live_validation_format_based"/>
                <input type="hidden" name="gf_live_validation_hidden_error" id="gf_live_validation_hidden_error"/>

                <li class="gf_live_validation_type_settings field_setting gf_live_validation_type_settings_valid gf_validation_default_pattern gf_lv_is_pattern_field" style="display: none;">

                    <label for="gf_live_validation_format" class="" >
                        <?php _e("Enter RegEx pattern", "real-time-validation-for-gravity-forms"); ?>
                        <?php gform_tooltip("gf_live_validation_format"); ?>
                    </label>

                    <textarea  data-ftype="text"  placeholder="Your Regex Here..." id="gf_live_validation_format" rows="5" cols="40" onkeyup="SetFieldProperty('gf_live_validation_format', createbaseHash(this));" onfocusout="SetFieldProperty('gf_live_validation_format', createbaseHash(this))" ></textarea>
                    <span class="lv_open_support_box_outer"> <b> <?php _e('Need Help with RegEx Patterns?','real-time-validation-for-gravity-forms' ); ?></b><a href="javascript:void(0);" class="lv_open_support_box"> Click Here</a></span> 
                    <span class="lv-toggle-off lv_admin_input_error"> <?php _e('OOPS! It doesn\'t seems like a valid RegEx pattern.','real-time-validation-for-gravity-forms' ); ?>  </span>


                </li>



                <!-- START DATE PICKER PATTERN FIELDS -->

                <?php include_once plugin_dir_path(__FILE__) . '/views/gravity-form-date-fields.php' ?>

                <!-- END DATE PICKER PATTERN FIELDS -->




                <!-- START NAME PATTERN FIELDS -->

                <?php include_once plugin_dir_path(__FILE__) . '/views/gravity-form-name-fields.php' ?>


                <!-- END NAME PATTERN FIELDS -->



                <!-- START ADDRESS PATTERN FIELDS -->

                <?php include_once plugin_dir_path(__FILE__) . '/views/gravity-form-address-fields.php' ?>


                <!-- END ADDRESS PATTERN FIELDS -->  


                <!-- START TIME PATTERN FIELDS -->

                <?php include_once plugin_dir_path(__FILE__) . '/views/gravity-form-time-fields.php' ?>


                <!-- END TIME PATTERN FIELDS -->  




                <li class="gf_live_validation_type_settings field_setting gf_live_validation_type_settings_valid" style="display: none;">

                    <label for="gf_live_validation_error_msg" class="" >
                        <?php _e("Error message", "real-time-validation-for-gravity-forms"); ?>
                        <?php gform_tooltip("gf_live_validation_error_msg"); ?>
                    </label>

                    <input type="text" id="gf_live_validation_error_msg" onkeyup="SetFieldProperty('gf_live_validation_error_msg', moveErrorTohidden(this));" />


                </li>

                <?php
            }
            // }
        }

        /**
         * Javascript that needs to be putted over the form editor of gravity form
         */
        public function gf_live_valiadtion_editor_script() {






            include_once LV_ROOT . 'public/includes/default_messages.php';
            ?>

            <script type="text/javascript"> var is_RealTimeValidOn = <?php echo ($this->lv_is_enabled) ? "true" : "false"; ?>;</script>


            <script type="text/javascript"> var lv_default_error_messages = '<?php echo json_encode(apply_filters('lv_default_error_messages', $default_messages)); ?>';</script>

            <script type='text/javascript' src="<?php echo plugin_dir_url(__FILE__) . 'assets/js/form-editor.js' ?>"></script>

            <?php
        }

        /**
         * Get ftype param for each actual field type
         * so that we can apply validations to the front more easily withour being worried about the field we are handling
         * @param type $type
         * @return string
         */
        public function get_presence_ftype($type) {
            switch ($type) {
                case "date":
                    return "text";
                    break;
                case "phone":
                    return "text";
                case "number":
                    return "text";
                case "website":
                    return "text";
                case "email":
                    return "text";
                case "post_title":
                    return "text";
                case "post_content":
                    return "textarea";
                case "post_excerpt":
                    return "textarea";
                default:
                    return $type;
            }
        }

        /**
         * Set of default array for date field
         * @param type $validation_setup
         * @param type $field_obj
         * @return type
         */
        public function lv_date_field_auto_apply($validation_setup, $field_obj) {
            switch ($field_obj['dateType']) {
                case "datefield":

                    return array(
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'text'
                            )
                        ),
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'text'
                            )
                        ),
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'text'
                            )
                        ),
                    );



                    break;
                case "datepicker":
                    return array(
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'text'
                            )
                        )
                    );

                    break;
                case "datedropdown":
                    return array(
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'select'
                            )
                        ),
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'select'
                            )
                        ),
                        array(
                            array(
                                'type' => 'presence',
                                'ftype' => 'select'
                            )
                        ),
                    );

                    break;
                default:
            }
        }

        /**
         * Set of default array 
         * @param type $validation_setup
         * @param type $field_obj
         * @return type
         */
        public function lv_email_field_auto_apply($validation_setup, $field_obj) {


            if (isset($field_obj['emailConfirmEnabled']) && $field_obj["emailConfirmEnabled"] == true) {



                return array(
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                );
            } else {
                return array(
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                );
            }
        }

        public function lv_address_field_auto_apply($validation_setup, $field_obj) {

            if (isset($field_obj['addressType']) && $field_obj["addressType"] == "international") {
                return array(
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'select'
                        )
                    )
                );
            } else {
                return array(
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'select'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                );
            }
        }

        public function lv_time_field_auto_apply($validation_setup, $field_obj) {


            if (isset($field_obj['field_time_format']) && $field_obj["field_time_format"] == "12") {
                return array(
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'select'
                        )
                    ),
                );
            } else {
                return array(
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                    array(
                        array(
                            'type' => 'presence',
                            'ftype' => 'text'
                        )
                    ),
                );
            }
        }

        public function lv_modify_toolbar($menu, $id) {
            $menu['lv_gf'] = array(
                'label' => __("Real Time validation","real-time-validation-for-gravity-forms"),
                'icon' => '<i class="fa fa-pencil-square-o fa-lg"></i>',
                'title' => __('Real Time validation', 'real-time-validation-for-gravity-forms'),
                'url' => admin_url("admin.php?page=gf_edit_forms&view=settings&subview=lv_form_setting&id={$id}"),
                'link_class' => 'lv_addon_help',
                'priority' => 1,
            );

            return $menu;
        }

        public function lv_output_script() {
            ?>

            <style>
                #forms_form .row-actions {
                    left:0;
                }

                .lv_addon_help {
                    -webkit-transition: all 1s linear;
                    -moz-transition: all 1s linear;
                    transition: all 1s linear;
                }

                .wp-list-table a.lv_addon_help {
                    -webkit-transition: all 1s linear;
                    -moz-transition: all 1s linear;
                    transition: all 1s linear;
                }
            </style>

            <script>

                jQuery(window).load(function() {

                    jQuery(".row-actions").css({"left": "0px"});
                    jQuery(".lv_addon_help").css({"background-color": "#0e3f7a", "color": "white"});

                    setTimeout(function() {

                        jQuery(".lv_addon_help").css({"background": "none", "color": "#0073aa"});
                        jQuery(".row-actions").css({"left": "-9999px"});
                    }, 3000);


                });
            </script>
            <div class="notice notice-success">
                <p><?php printf(__("Plugin <b> %s </b> activated.", 'real-time-validation-for-gravity-forms'),$this->plugin->plugin_main_title); ?></p>
            </div>

            <?php
            echo ob_get_clean();
        }

        public function lv_install_help() {

            if (isset($_GET['lv_install_complete'])) {

                add_action('admin_print_footer_scripts', array($this, 'lv_output_script'), 20);
            }
        }

        public function lv_messaging($form) {

           
            if (  !GFCommon::is_form_editor() || get_option("lv_error_dismiss_" . $form['id']) == "yes") {
                return $form;
            }
            if (!isset($form['gform_enable_lv']) || !$form['gform_enable_lv']) {
                $enablelink = add_query_arg(array('page' => 'gf_edit_forms', 'view' => 'settings', 'subview' => 'lv_form_setting', 'id' => rgar($form, 'id')), admin_url('admin.php'));
                ?>
                <div class="notice notice-warning is-dismissible lv_error_dismiss" data-id="<?php echo $form['id']; ?>">
                    <p><?php printf(__('It seems like <b>Real time validation </b>is turned off. &nbsp; <a href="%s"><button id="button_link_lv_on" class="button button-primary">Turn On</button></a>', 'real-time-validation-for-gravity-forms'),$enablelink); ?></p>
                </div>
                <?php
            }
            return $form;
        }

        public function lv_tooltips_gform($tooltips) {
            $tooltips['form_gf_live_validation_value'] = __("<h6>Validate Real Time Input</h6> You can use RegEx to validate user input. An error message will be thrown to user in real-time, if the RegEx pattern is violated.",'real-time-validation-for-gravity-forms');
            $tooltips['form_gf_lv_confirmation_email'] = __("<h6>Validation Message For Confirm Email</h6> You can add custom error message that will get triggered while confirm email field value is not same as email.",'real-time-validation-for-gravity-forms');
            return $tooltips;
        }

        public function get_setting_page_url($tab) {

            return admin_url('admin.php') . "?page=gf_settings&subview=" . $this->plugin_slug . "&tab=" . $tab;
        }

        public function lv_admin_plugin_settings_link($links) {
            $link_1 = '<a href="' . esc_url($this->get_setting_page_url('support')) . '">' . __('Support', 'real-time-validation-for-gravity-forms') . '</a>';
            $link_2 = '<a href="' . esc_url($this->get_setting_page_url('how_to')) . '">' . __('How To Use', 'real-time-validation-for-gravity-forms') . '</a>';
            $link_3 = '<a href="' . esc_url($this->get_setting_page_url('pro')) . '">' . __('Premium Version', 'real-time-validation-for-gravity-forms') . '</a>';

            array_unshift($links, $link_1, $link_2, $link_3);
            return $links;
        }

        public function lv_script_back() {

            if (!wp_style_is($this->plugin_slug . '-admin-styles-switcher', 'done')) {
                echo "<link href='" . plugins_url('assets/css/switcher.css', __FILE__) . "' rel='stylesheet' type='text/css'/>";
            }

            if (!wp_style_is($this->plugin_slug . '-admin-styles', "done")) {
                echo "<link href='" . plugins_url('assets/css/admin.css', __FILE__) . "' rel='stylesheet' type='text/css'/>";
            }




            if (!wp_script_is($this->plugin_slug . '-admin-switcher', "done")) {
                echo "<script src='" . plugins_url('assets/js/switcher.js', __FILE__) . "' type='text/javascript' ></script>";
            }

            if (!wp_script_is($this->plugin_slug . '-admin-script', "done")) {
                echo "<script src='" . plugins_url('assets/js/admin.js', __FILE__) . "'  type='text/javascript'/> </script>";
            }
        }

    }

}
