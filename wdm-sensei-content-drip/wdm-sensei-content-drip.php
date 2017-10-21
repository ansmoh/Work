<?php
/** Plugin Name:      Wdm Sensei Content Drip
 * Plugin URI:        https://wisdmlabs.com
 * Description:       Add restriction for access final exam
 * Version:           1.0.0
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 * Text Domain:       wdm-sensei-content-drip
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if (in_array('woothemes-sensei/woothemes-sensei.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    include_once plugin_dir_path(__FILE__) . 'public/wdm-sensei-content-drip-public.php';
    new WdmSenseiContentDripPublic();
    include_once plugin_dir_path(__FILE__) . 'admin/wdm-sensei-content-drip-admin.php';
    new WdmSenseiContentDripAdmin();
    include_once plugin_dir_path(__FILE__).'public/wdm-order-completion.php';
    new WdmOrderCompletion();
    include_once plugin_dir_path(__FILE__).'public/wdm-display-video-products.php';
    new WdmDisplayVideoProducts();
    add_action('init', 'wdm_associate_video');
} else {
        add_action('admin_notices', 'sensei_plugin_inactive_notice');
}

if (!function_exists('sensei_plugin_inactive_notice')) {
    /**
     * Display base plugin activate notification
     * @return [type] [description]
     */
    function sensei_plugin_inactive_notice()
    {
        if (current_user_can('activate_plugins')) {
            ?>
            <div id="message" class="error">
            <p><?php _e('Wdm Sensei Content Drip is inactive.Install and activate Sensei for Wdm Sensei Content Drip to work.', 'wdm-sensei-content-drip');
            ?></p>
            </div>
            <?php
        }
    }
}

function wdm_create_products()
{
    //$options = get_option('create_product');
    if (get_option('create_product')) {
        return;
    }
    $courses = array('17275');
    foreach ($courses as $course_id) {
        $lessons = array();
        $args = array(
            'post_type' => 'lesson',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_lesson_course',
                    'value' => intval($course_id),
                    'compare' => '='
                )
            ),
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'suppress_filters' => 0
        );
         $lessons = new WP_Query($args);
        foreach ($lessons->posts as $lesson) {
            echo $lesson->post_title;
            $post = array(
            'post_author' => 1,
            'post_content' => '',
            'post_status' => "draft",
            'post_title' => 'Video for ' . $lesson->post_title,
            'post_parent' => '',
            'post_type' => "product",
            );

//Create post
            $post_id = wp_insert_post($post, $wp_error);
            // if ($post_id) {
            //     $attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
            //     add_post_meta($post_id, '_thumbnail_id', $attach_id);
            // }

            wp_set_object_terms($post_id, 'Lesson Video', 'product_cat');
            wp_set_object_terms($post_id, 'simple', 'product_type');
            update_post_meta($post_id, '_visibility', 'visible');
            update_post_meta($post_id, '_stock_status', 'instock');
            update_post_meta($post_id, 'total_sales', '0');
            update_post_meta($post_id, '_regular_price', "5");
            update_post_meta($post_id, '_price', "5");
        }
        update_option('create_product', '1');
         // echo '<pre>';
         // print_r($lessons);
         // echo '</pre>';
    }
}




function wdm_associate_video()
{
    if (get_option('wdm_update_product_video1')) {
        return;
    }
    $courses = array('13991','11772','14026','13310','17275','8287');
    foreach ($courses as $course_id) {
        $lessons = array();
        $args = array(
        'post_type' => 'lesson',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
        array(
            'key' => '_lesson_course',
            'value' => intval($course_id),
            'compare' => '='
        )
        ),
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'suppress_filters' => 0
        );
        $lessons = new WP_Query($args);
        $lesson_video_array=array(
        'California Real Estate Finance, Chapter 1' => array('<iframe src="https://player.vimeo.com/video/179069198?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18236'),
        'California Real Estate Finance, Chapter 2' => array('<iframe src="https://player.vimeo.com/video/179010859?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18237'),
        'California Real Estate Finance, Chapter 3' => array('<iframe src="https://player.vimeo.com/video/179010854?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18238'),
        'California Real Estate Finance, Chapter 4' => array('<iframe src="https://player.vimeo.com/video/179010857?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18239'),
        'California Real Estate Finance, Chapter 5' => array('<iframe src="https://player.vimeo.com/video/179010858?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18240'),
        'California Real Estate Finance, Chapter 6' => array('<iframe src="https://player.vimeo.com/video/179010853?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18241'),
        'California Real Estate Finance, Chapter 7' => array('<iframe src="https://player.vimeo.com/video/179010865?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18242'),
        'California Real Estate Finance, Chapter 8'=> array('<iframe src="https://player.vimeo.com/video/179010862?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18243'),
        'California Real Estate Finance, Chapter 9' => array('<iframe src="https://player.vimeo.com/video/179010856?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18244'),
        'California Real Estate Finance, Chapter 10' => array('<iframe src="https://player.vimeo.com/video/179010861?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18245'),
        'California Real Estate Finance, Chapter 11' => array('<iframe src="https://player.vimeo.com/video/179010852?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18246'),
        'California Real Estate Finance, Chapter 12' => array('<iframe src="https://player.vimeo.com/video/179010860?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18247'),
        'California Real Estate Finance, Chapter 13' => array('<iframe src="https://player.vimeo.com/video/179069360?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18248'),
        'California Real Estate Finance, Chapter 14' => array('<iframe src="https://player.vimeo.com/video/179010851?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18249'),
        'California Real Estate Finance, Chapter 15' => array('<iframe src="https://player.vimeo.com/video/179010863?color=e8ae26&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>','18250'),
        );


        foreach ($lessons->posts as $lesson) {
            //echo $lesson->post_title;
            //echo ($lesson->ID);
            foreach ($lesson_video_array as $key => $value) {
                if ($key == $lesson->post_title) {
                    $lesson_video = htmlentities($value[0]);
                    update_post_meta($lesson->ID, '_lesson_video_embed', $lesson_video);
                    update_post_meta($lesson->ID, 'wdm_course_video_woocommerce_product', $value[1]);
                }
            }
        }

        update_option('wdm_update_product_video1', '1');
        // array(

        // 'Real Estate Principles, Chapter 1' => array('<url>',iD);

        // )
    }
}
