<?php
/**
 * Add Admin actions
 */
if (!class_exists('WdmSenseiContentDripAdmin')) {
    class WdmSenseiContentDripAdmin
    {
        public function __construct()
        {
            add_action('add_meta_boxes', array($this, 'wdm_add_video_settings'), 20, 2);
            add_action('save_post', array( $this, 'wdm_save_video_meta' ));
            //add_action('sensei_complete_lesson', array($this,'wdm_sensei_complete_lesson'));
        }

        public function wdm_add_video_settings()
        {
             add_meta_box('wdm-course-video-product', __('Associate Video Product', 'wdm-sensei-content-drip'), array( $this, 'wdm_course_video_product_callback' ), 'course', 'side');
             add_meta_box('wdm-lesson-video-product', __('Associate Video Product', 'wdm-sensei-content-drip'), array( $this, 'wdm_course_video_product_callback' ), 'lesson', 'side');
        }

        public function wdm_course_video_product_callback()
        {
            global $post;

            $select_course_woocommerce_product = get_post_meta($post->ID, 'wdm_course_video_woocommerce_product', true);

            $post_args = array( 'post_type'         => array( 'product'/*, 'product_variation' */),
                            'posts_per_page'        => -1,
                            'orderby'           => 'title',
                            'order'             => 'DESC',
                            'exclude'           => $post->ID,
                            'post_status'       => array( 'publish', 'private', 'draft' ),
                            'tax_query'         => array(
                                array(
                                    'taxonomy'  => 'product_type',
                                    'field'     => 'slug',
                                    'terms'     => array( 'variable', 'grouped' ),
                                    'operator'  => 'NOT IN'
                                )
                            ),
                            'suppress_filters'  => 0
                            );
            $posts_array = get_posts($post_args);

            $html = '';

            if (count($posts_array) > 0) {
                $html .= '<select id="course-woocommerce-product-options" name="wdm_course_video_woocommerce_product" class="chosen_select widefat">' . "\n";
                $html .= '<option value="-">' . __('None', 'woothemes-sensei') . '</option>';
                $prev_parent_id = 0;
                foreach ($posts_array as $post_item) {
                    /*if ('product_variation' == $post_item->post_type) {
                        $product_object = get_product($post_item->ID);
                        $parent_id = wp_get_post_parent_id($post_item->ID);
                        if (sensei_check_woocommerce_version('2.1')) {
                            $formatted_variation = wc_get_formatted_variation($product_object->variation_data, true);
                        } else {
                            $formatted_variation = woocommerce_get_formatted_variation($product_object->variation_data, true);
                        }
                        $product_name = ucwords($formatted_variation);
                    } else {*/
                        $parent_id = false;
                        $prev_parent_id = 0;
                        $product_name = $post_item->post_title;
                   //}

                    // Show variations in groups
                    if ($parent_id && $parent_id != $prev_parent_id) {
                        if (0 != $prev_parent_id) {
                            $html .= '</optgroup>';
                        }
                        $html .= '<optgroup label="' . get_the_title($parent_id) . '">';
                        $prev_parent_id = $parent_id;
                    } elseif (! $parent_id && 0 == $prev_parent_id) {
                        $html .= '</optgroup>';
                    }

                    $html .= '<option value="' . esc_attr(absint($post_item->ID)) . '"' . selected($post_item->ID, $select_course_woocommerce_product, false) . '>' . esc_html($product_name) . '</option>' . "\n";
                } // End For Loop
                $html .= '</select>' . "\n";
                if (current_user_can('publish_product')) {
                    $html .= '<p>' . "\n";
                    $html .= '<a href="' . admin_url('post-new.php?post_type=product') . '" title="' . esc_attr(__('Add a Product', 'woothemes-sensei')) . '">' . __('Add a Product', 'woothemes-sensei') . '</a>' . "\n";
                    $html .= '</p>'."\n";
                } // End If Statement
            } else {
                if (current_user_can('publish_product')) {
                    $html .= '<p>' . "\n";
                    $html .= esc_html(__('No products exist yet.', 'woothemes-sensei')) . '&nbsp;<a href="' . admin_url('post-new.php?post_type=product') . '" title="' . esc_attr(__('Add a Product', 'woothemes-sensei')) . '">' . __('Please add some first', 'woothemes-sensei') . '</a>' . "\n";
                    $html .= '</p>'."\n";
                } else {
                    $html .= '<p>' . "\n";
                    $html .= esc_html(__('No products exist yet.', 'woothemes-sensei')) . "\n";
                    $html .= '</p>'."\n";
                } // End If Statement
            } // End If Statement

            echo $html;

        }
        public function wdm_save_video_meta($post_id)
        {
            if (isset($_POST['wdm_course_video_woocommerce_product']) && !empty($_POST['wdm_course_video_woocommerce_product']) && $_POST['wdm_course_video_woocommerce_product'] !='-') {
                update_post_meta($post_id, 'wdm_course_video_woocommerce_product', $_POST['wdm_course_video_woocommerce_product']);
            }

        }
    }
}
