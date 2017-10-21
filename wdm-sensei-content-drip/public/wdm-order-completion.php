<?php

if (!class_exists('WdmOrderCompletion')) {
    class WdmOrderCompletion
    {
        public function __construct()
        {
            add_action('woocommerce_order_status_completed', array($this,'wdm_woocommerce_order_status_completed'), 10, 1);
            add_action('woocommerce_order_status_processing', array($this,'wdm_woocommerce_order_status_completed'), 10, 1);
            add_filter('woocommerce_get_price', array($this,'change_video_product_price'), 10, 2);
        }
        public function wdm_woocommerce_order_status_completed($order_id)
        {
            $order = new WC_Order($order_id);
            $user_id=$order->user_id;
            foreach ($order->get_items() as $item) {
                if (isset($item['Add Course Videos']) && !empty($item['Add Course Videos'])) {
                    $purchased_videos = get_user_meta($user_id, 'wdm_user_purchased_videos', true);
                    if (!$purchased_videos) {
                        $purchased_videos=array();
                    }
                    $chained_products = get_post_meta($item['product_id'], '_chained_product_ids', true);
                    if (isset($chained_products) && !empty($chained_products[0])) {
                        $purchased_videos = array_merge($purchased_videos, $chained_products);
                    } else {
                        array_push($purchased_videos, $item['product_id']);
                    }
                    $purchased_videos=array_unique($purchased_videos);
                    update_user_meta($user_id, 'wdm_user_purchased_videos', $purchased_videos);
                }
                $course_video_qty = get_user_meta($user_id, 'wdm_course_video_qty', true);
                $product_id = $item['product_id'];
                $args = array(
                    'post_type'     =>  array('course','lesson'),
                    'meta_query'    =>  array(
                    array(
                        'key' => 'wdm_course_video_woocommerce_product',
                        'value' => strval($product_id) ,
                        )
                    )
                    );
                $courses = get_posts($args);
                if (isset($courses) && ! empty($courses)) {
                    foreach ($courses as $course) {
                        $course_id = $course->ID;
                        if ($course->post_type == 'lesson') {
                            $course_id = Sensei()->lesson->get_course_id($course_id);
                        }
                        if (isset($course_video_qty[$course_id]) && !empty($course_video_qty[$course_id])) {
                            $course_video_qty[$course_id]=$course_video_qty[$course_id]+1;
                        } else {
                            $course_video_qty[$course_id]=1;
                        }
                    }
                    update_user_meta($user_id, 'wdm_course_video_qty', $course_video_qty);
                }
            }
        }

        /**
         * Change the video product price dynamically
         * @param  [type] $price   [description]
         * @param  [type] $product [description]
         * @return [type]          [description]
         */
        public function change_video_product_price($price, $product)
        {
            $args = array(
            'post_type'     =>  'course',
            'meta_query'    =>  array(
            array(
            'key' => 'wdm_course_video_woocommerce_product',
            'value' => strval($product->id) ,
            )
            )
            );
            $courses = get_posts($args);
            if (isset($courses) && !empty($courses)) {
                $user_id=get_current_user_id();
                $course_video_qty = get_user_meta($user_id, 'wdm_course_video_qty', true);
                foreach ($courses as $course) {
                    if (isset($course_video_qty[$course->ID]) && !empty($course_video_qty[$course->ID])) {
                        $reduce_price=$price - ($course_video_qty[$course->ID]*5);
                        if ($reduce_price > 0) {
                            return $reduce_price;
                        } else {
                            return 0;
                        }
                    }
                }
            }
             return $price;
        }
    }
}
