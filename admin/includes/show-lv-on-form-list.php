<?php

/**
 *
 */
class lv_formlist_option {

    public function run() {

        add_action('admin_init', array($this, 'admin_init'),300);
         
    }

    public function admin_init() {

        if (( 'gf_edit_forms' == RGForms::get('page'))) {

            $this->show_page_on_form_list();

       
        }
    }

  

    private function show_page_on_form_list() {


        $sort_column = empty($_GET['sort']) ? 'title' : $_GET['sort'];
        $db_columns = GFFormsModel::get_form_db_columns();

        if (!in_array(strtolower($sort_column), $db_columns)) {
            $sort_column = 'title';
        }

        $sort_direction = empty($_GET['dir']) ? 'ASC' : $_GET['dir'];
        $active = RGForms::get('active') == '' ? null : (bool) RGForms::get('active');
        $trash = RGForms::get('trash') == '' ? false : (bool) RGForms::get('trash');
        $forms = RGFormsModel::get_forms($active, $sort_column, $sort_direction, $trash);

        $array = array();
        foreach ($forms as $form) {
            $array[$form->id] = rgar(GFFormsModel::get_form_meta($form->id), "gform_enable_lv");
        }

        wp_localize_script("jquery", 'lv_formlist', $array);
        wp_localize_script("jquery", 'lv_ajax_toggle_nonce', wp_create_nonce('gf_lv_ajax_toggle'));
        wp_localize_script("jquery", 'lv_toggle_url', admin_url('admin.php?page=gf_edit_forms'));
    }
    
   

}

$gfpgfu_show_page = new lv_formlist_option();
$gfpgfu_show_page->run();

