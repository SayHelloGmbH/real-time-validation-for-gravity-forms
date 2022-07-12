<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-lv-pointers
 *
 * @author amritansh
 */
class LV_pointers {

//put your code here


    public function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'custom_admin_pointers_header'));
    }

    public function custom_admin_pointers_header() {
        if ($this->custom_admin_pointers_check()) {
            add_action('admin_print_footer_scripts', array($this, 'custom_admin_pointers_footer'));

            wp_enqueue_script('wp-pointer');
            wp_enqueue_style('wp-pointer');
        }
    }

    public function custom_admin_pointers_check() {
        $admin_pointers = $this->custom_admin_pointers();
        foreach ($admin_pointers as $pointer => $array) {
            if ($array['active'])
                return true;
        }
    }

    public function custom_admin_pointers_footer() {

        if (isset($_GET['page']) && $_GET['page'] == "gf_edit_forms" && !isset($_GET['view'])) {





            $admin_pointers = $this->custom_admin_pointers();
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function($) {

                    var myPointers = new Array();
                    if ($(".gforms_edit_form h1").length > 0) {
                        $(".gforms_edit_form h1").append('<button class="button" id="lv_pointer_target" >Real Time Validation help!</button>');
                    }



            <?php
            foreach ($admin_pointers as $pointer => $array) {
                if ($array['active']) {
                    ?>
                            myPointers.push($('<?php echo $array['anchor_id']; ?>').pointer({
                                content: '<?php echo $array['content']; ?>',
                                position: {
                                    edge: '<?php echo $array['edge']; ?>',
                                    align: '<?php echo $array['align']; ?>'
                                },
                            }));
                    <?php
                }
            }
            ?>

                    $(document).on("click", "#lv_pointer_target", function() {
                        myPointers[0].pointer('open');
                        return false;
                    });
                })(jQuery);
                /* ]]> */
            </script>
            <?php
        }
    }

    public function custom_admin_pointers() {
        $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        $version = '1_2'; // replace all periods in 1.0 with an underscore
        $prefix = 'custom_admin_pointers' . $version . '_';

        $new_pointer_content32 = '<h3>' . __('Real Time Validation for Gravity Forms','real-time-validation-for-gravity-forms') . '</h3>';
        $new_pointer_content32 .= "<p><iframe width=\"285\" frameborder=\"0\" height=\"160\" allowfullscreen src=\"https://www.youtube.com/embed/m_bv3eCmvgs\"></iframe></p>";
        return array(
            $prefix . 'new_items' => array(
                'content' => $new_pointer_content32,
                'anchor_id' => '#add_standard_fields',
                'edge' => 'right',
                'align' => 'left',
                'active' => (!in_array($prefix . 'new_items', $dismissed) )
            ),
        );
    }

}

new LV_pointers();
