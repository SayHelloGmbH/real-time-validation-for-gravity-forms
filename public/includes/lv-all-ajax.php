<?php

/**
 * This class holds the logic for all admin-ajax requests
 *
 * @author amritansh
 */
class lv_all_ajax {

    public function __construct() {


        add_action('wp_ajax_lv_dismiss_error', array($this, "lv_dismiss_error"));
    
    }

    /**
     * Error dismiss handling for each form
     */
    public function lv_dismiss_error() {

        extract($_POST);

        if ($id) {
            update_option("lv_error_dismiss_" . $id, "yes");
            echo json_encode(array('result' => 'success'));
            exit;
        }
        echo json_encode(array('result' => 'error'));

        exit;
    }

  

}

new lv_all_ajax();
