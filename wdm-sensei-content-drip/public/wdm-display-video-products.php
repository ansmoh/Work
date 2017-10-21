<?php
if (!class_exists('WdmDisplayVideoProducts')) {
    class WdmDisplayVideoProducts
    {
        public function __construct()
        {
            add_shortcode('display_video_products', array($this,'wdm_display_video_products'));
            add_action('wdm_sensei_add_more_options', array($this,'wdm_add_purchase_more_videos_link'));
            add_action('wdm_sensei_frontend_messages', array($this,'wdm_dashboard_purchase_more_videos'));
        }

        public function wdm_dashboard_purchase_more_videos()
        {
            $purchasable_video_course_id = $this->check_videos_avalability();
            if (isset($purchasable_video_course_id) && !empty($purchasable_video_course_id)) {
                echo '<div class="wdm_dashboard_videos"><a class="courselink" href="'.$this->wdm_get_url_by_shortcode('display_video_products').'"><strong>Purchase More Videos</strong></a></div>';
            }
        }

        public function wdm_add_purchase_more_videos_link()
        {

            $purchasable_video_course_id = $this->check_videos_avalability();
            if (isset($purchasable_video_course_id) && !empty($purchasable_video_course_id)) {
                echo '<a class="wdm_purchase_video" href="'.$this->wdm_get_url_by_shortcode('display_video_products').'"><strong>Purchase More Videos</strong></a>';
            }

        }


        public function wdm_display_video_products()
        {
            $current_user = wp_get_current_user();
            $course_statuses = WooThemes_Sensei_Utils::sensei_check_for_activity(array( 'user_id' => $current_user->ID, 'type' => 'sensei_course_status' ), true);
            $active_ids = array();

            if (!is_array($course_statuses)) {
                $temp =$course_statuses;
                $course_statuses=array();
                array_push($course_statuses, $temp);
            }
            foreach ($course_statuses as $course_status) {
                if (! WooThemes_Sensei_Utils::user_completed_course($course_status, $current_user->ID)) {
                    array_push($active_ids, $course_status->comment_post_ID);
                    //$active_ids[] = $course_status->comment_post_ID;
                }
            }
            $purchasable_video_course_id =array();
            $purchased_videos = get_user_meta($current_user->ID, 'wdm_user_purchased_videos', true);
            foreach ($active_ids as $course_id) {
                $course_product_id = get_post_meta($course_id, 'wdm_course_video_woocommerce_product', true);
                $course_video_qty = get_user_meta($current_user->ID, 'wdm_course_video_qty', true);
                $course_qty =0;
                if (isset($course_video_qty) && !empty($course_video_qty)) {
                    if (isset($course_video_qty[$course_id]) && !empty($course_video_qty[$course_id])) {
                        $course_qty= $course_video_qty[$course_id];
                    }
                }


                $course_prod_id= get_post_meta($course_id, '_course_woocommerce_product', true);

                if (isset($course_product_id)&& !empty($course_product_id)) {
                    if (!wc_customer_bought_product($current_user->user_email, $current_user->ID, $course_product_id) && $course_qty < 10 && !in_array($course_prod_id, $purchased_videos)) {
                        array_push($purchasable_video_course_id, intval($course_product_id));
                    }
                }
            }


            if (isset($purchasable_video_course_id) && !empty($purchasable_video_course_id)) {
                $purchasable_video_course_id = array_unique($purchasable_video_course_id);

                 global $wpdb;
                $group_product_array=array();
                foreach ($purchasable_video_course_id as $product_id) {
                    $sql = "select post_id from " . $wpdb->prefix . "postmeta where
        meta_key = '_chained_product_ids' &&
        meta_value like '%%%s%%'";
                    $product_id = 's:' . strlen($product_id) . ':"' . (int) $product_id . '";';
                    $sql = $wpdb->prepare($sql, $product_id);
                    $res = $wpdb->get_results($sql);

                    if (isset($res) && !empty($res)) {
                        foreach ($res as $post) {
                            $group_product_id = intval($post->post_id);
                            if (isset($group_product_id) && !empty($group_product_id)) {
                                if (!WooThemes_Sensei_Utils::sensei_customer_bought_product($current_user->user_email, $current_user->ID, $group_product_id)) {
                                    array_push($group_product_array, $group_product_id);
                                }
                            }
                        }
                    }
                }
                $group_product_array=array_unique($group_product_array);
                foreach ($group_product_array as $group_product) {
                    $video_products = get_post_meta($group_product, '_chained_product_ids', true);
                    $video_products = array_unique($video_products);
                    $result = array_intersect($video_products, $purchasable_video_course_id);
                    $display_group_product = array_diff($video_products, $result);
                    if (empty($display_group_product)) {
                        array_unshift($purchasable_video_course_id, $group_product);
                    }
                }
                /**** Changes End ****/

                echo '<ul class="products wdm_video_products">';
                foreach ($purchasable_video_course_id as $product_id) {
                    echo '<li class="product">';
                    $product_id = intval($product_id);
                    $_product = wc_get_product($product_id);
                    ?>
                    <a class="product-images">
                    <?php

                    if (!empty(get_the_post_thumbnail($product_id))) {
                        echo '<span>'.get_the_post_thumbnail($product_id).'</span>';
                    } else {
                        echo wc_placeholder_img();
                    }
                    ?>
                    </a>
                    <div class="product-details">
                    <div class="product-details-container packages">
                    <div class="p-info"><span class="p-title"><a><?php echo get_the_title($product_id); ?></a></span><div class="short-desc"><?php echo $_product->post->post_excerpt;?></div></div>
                    <div class="price-box">     <?php
                        $currency_symbol=get_woocommerce_currency_symbol();
                    ?>
                    <span class="price"><span class="amount"><?php echo $currency_symbol.$_product->get_price();?></span></span>

                    <a class="enroll-btn" href="<?php echo get_post_permalink($product_id); ?>">Purchase Course Videos</a></div>
                    </div>

                    </div>

                    </li>

                    <?php
                }
                    echo '</ul>';
            } else {
                echo '<div><p>No video products are available for Purchase.</p></div>';
            }
        }
        public function wdm_get_url_by_shortcode($shortcode)
        {
            global $wpdb;

            $url = '';

            $sql = 'SELECT ID
        FROM ' . $wpdb->posts . '
        WHERE
            post_type = "page"
            AND post_status="publish"
            AND post_content LIKE "%' . $shortcode . '%"';

            if ($id = $wpdb->get_var($sql)) {
                $url = get_permalink($id);
            }

            return $url;
        }

        public function check_videos_avalability()
        {
            $current_user = wp_get_current_user();
            $course_statuses = WooThemes_Sensei_Utils::sensei_check_for_activity(array( 'user_id' => $current_user->ID, 'type' => 'sensei_course_status' ), true);
            $active_ids = array();

            if (!is_array($course_statuses)) {
                $temp =$course_statuses;
                $course_statuses=array();
                array_push($course_statuses, $temp);
            }
            foreach ($course_statuses as $course_status) {
                if (! WooThemes_Sensei_Utils::user_completed_course($course_status, $current_user->ID)) {
                    array_push($active_ids, $course_status->comment_post_ID);
                }
            }
            $purchasable_video_course_id =array();
            $purchased_videos = get_user_meta($current_user->ID, 'wdm_user_purchased_videos', true);
            foreach ($active_ids as $course_id) {
                $course_product_id = get_post_meta($course_id, 'wdm_course_video_woocommerce_product', true);
                $course_video_qty = get_user_meta($current_user->ID, 'wdm_course_video_qty', true);
                $course_qty =0;
                if (isset($course_video_qty) && !empty($course_video_qty)) {
                    if (isset($course_video_qty[$course_id]) && !empty($course_video_qty[$course_id])) {
                        $course_qty= $course_video_qty[$course_id];
                    }
                }


                $course_prod_id= get_post_meta($course_id, '_course_woocommerce_product', true);

                if (isset($course_product_id)&& !empty($course_product_id)) {
                    if (!wc_customer_bought_product($current_user->user_email, $current_user->ID, $course_product_id) && $course_qty < 10 && !in_array($course_prod_id, $purchased_videos)) {
                        array_push($purchasable_video_course_id, intval($course_product_id));
                    }
                }
            }
            return $purchasable_video_course_id;
        }
    }
}
