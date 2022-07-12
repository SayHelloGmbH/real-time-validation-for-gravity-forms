<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-lv-dashboard
 *
 * @author amritansh
 */
class LV_dashboard {

    public $plugin = null;
    public $plugin_slug = null;

    public function __construct() {


        /**
         * Getting plugin instance
         */
        $this->plugin = Gravity_Forms_Live_Validation::get_instance();
        $this->plugin_slug = $this->plugin->get_plugin_slug();


        add_action("admin_init", array($this, "lv_modify_settings"));
    }

    public function lv_add_dashboard_menu() {


        add_submenu_page("gf_edit_forms", __("Real Time Validation", "real-time-validation-for-gravity-forms"), __("Real Time Validation", "real-time-validation-for-gravity-forms"), 'manage_options', 'lv_validation_dashboard', array($this, "lv_validation_dashboard"));
    }

    public function lv_modify_settings() {
        RGForms::add_settings_page(
                array(
                    'name' => $this->plugin_slug,
                    'tab_label' => $this->plugin->plugin_title,
                    'title' => $this->plugin->plugin_title,
                    'handler' => array($this, 'lv_validation_dashboard'),
                )
        );
    }

    public function lv_validation_dashboard() {


        ob_start();


        $selected = (isset($_GET['tab']) ? $_GET['tab'] : "pro")
        ?>


        <h3><span><i class="fa fa-cogs"></i> <?php echo $this->plugin->plugin_main_title; ?></span>
        </h3>
        <div class="wrap about-wrap">
            <h2 class="nav-tab-wrapper" id="wpseo-tabs">
                <?php foreach ($this->get_default_tabs() as $name => $tab) { ?>
                    <a class="nav-tab <?php echo $tab['class']; ?> <?php echo ($selected == $name) ? "nav-tab-active" : "" ?>" id="<?php echo $name; ?>-tab"
                       href="<?php echo $tab['url']; ?>"><?php echo $tab['label'] ?></a>

                <?php } ?>
            </h2>

            <div class="lv_dashboard_tab_content" id="<?php echo $selected; ?>">

                <?php include_once LV_ROOT . 'admin/views/lv-tabs-' . $selected . ".phtml"; ?> 
            </div>




        </div>



        <?php
        echo ob_get_clean();
    }

    public function get_default_tabs() {


        return apply_filters("lv_dashboard_tabs", array(
            'pro' => array(
                'name' => "pro",
                'label' => __("Our Plugins", "real-time-validation-for-gravity-forms"),
                'class' => '',
                'target_div' => 'lv_pro',
                'url' => admin_url('admin.php') . "?page=gf_settings&subview=" . $this->plugin_slug . "&tab=pro"
            ),
            'how_to' => array(
                'name' => "how_to",
                'label' => __("How to use", "real-time-validation-for-gravity-forms"),
                'class' => '',
                'target_div' => 'lv_how_to',
                'url' => admin_url('admin.php') . "?page=gf_settings&subview=" . $this->plugin_slug . "&tab=how_to"
            ),
        ));
    }

    private function get_active_plugins() {



        $active_plugins = get_option('active_plugins');

        /*
         * Look up the name of the plugin
         */
        $user_plugins = array();
        foreach ($active_plugins as $plugin) {
            $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $user_plugins[] = $data['Name'] . ', ' . $data['Version'] . ' - ' . $data['PluginURI'];
        }
        $plugins = implode("\n", $user_plugins);
        return $plugins;
    }

 

}

new LV_dashboard();


