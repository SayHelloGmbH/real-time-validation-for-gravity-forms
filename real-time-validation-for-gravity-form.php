<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Real Time Validation for Gravity Forms
 * Plugin URI:        https://wisetr.com
 * Description:       This plugin ensures that users gets the feedback on each field as he types even before form is submitted. Specific validation messages shown to user help him quickly rectify the mistakes.
 * Version:           1.7.0
 * Author:            Wisetr
 * Author URI:        https://wisetr.com
 * Text Domain: real-time-validation-for-gravity-forms
   Domain Path: /languages
 *
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */


define('LV_ROOT', plugin_dir_path(__FILE__));
define('LV_ROOT_URL', plugin_dir_url(__FILE__));


if(class_exists('GFForms')){

    require_once(plugin_dir_path(__FILE__) . 'public/class-gravity-forms-live-validation.php');

    add_action('init', 'lv_init_domain');

    add_action('plugins_loaded', array('Gravity_Forms_Live_Validation', 'get_instance'));


    add_action('activated_plugin', 'lv_validation_install');


    /* ----------------------------------------------------------------------------*
     * Dashboard and Administrative Functionality
     * ---------------------------------------------------------------------------- */


    if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {

        require_once(plugin_dir_path(__FILE__) . 'admin/class-gravity-forms-live-validation-admin.php');
        add_action('plugins_loaded', array('Gravity_Forms_Live_Validation_Admin', 'get_instance'));
    }
}

function lv_init_domain()
{
    load_plugin_textdomain('real-time-validation-for-gravity-forms', FALSE, plugin_basename(dirname(__FILE__)) . '/languages');

}

function lv_validation_install($plugin)
{

    if ($plugin == plugin_basename(__FILE__)) {
        wp_redirect(add_query_arg(array('page' => 'gf_edit_forms', 'lv_install_complete' => 'y'), admin_url('admin.php')));
        exit;
    }
}
