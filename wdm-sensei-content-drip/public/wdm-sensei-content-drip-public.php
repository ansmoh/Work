<?php
/**
 * Add Admin actions
 */
if (!class_exists('WdmSenseiContentDripPublic')) {
    class WdmSenseiContentDripPublic
    {
        public function __construct()
        {
            add_action('sensei_can_user_view_lesson', array($this,'wdm_course_exam_content_drip'), 20, 3);
            add_action('sensei_complete_quiz', array($this,'wdm_content_sensei_complete_quiz'), 2);
            add_filter('gform_pre_render_8', array($this,'pre_selected_location'), 10, 1);
            add_action('wp_ajax_save_location_value', array($this,'wdm_save_location_value'));
            add_action('wp_ajax_nopriv_save_location_value', array($this,'wdm_save_location_value'));
            add_action('wp_enqueue_scripts', array($this,'video_enqueue_script'));
            add_action('sensei_lesson_video', array($this,'wdm_lesson_videos'), 5, 1);
            add_action('sensei_lesson_tutor', array($this,'display_video_button_on_course_page'), 5);
            add_filter('wdm_change_order_of_active_courses', array($this,'wdm_change_order_of_active_courses_callback'));
            add_filter('woocommerce_product_single_add_to_cart_text', array($this,'wdm_change_video_product_text' ), 99);
            add_action('woocommerce_before_single_product', array($this,'wdm_display_video_message'));
            add_action('init', array($this,'wdm_remove_quiz_link'));
        }

        public function display_sensei_lesson_image()
        {
            echo '<div class="fusion-one-half fusion-layout-column fusion-column-last fusion-spacing-yes"><div class="fusion-column-wrapper">
                      <h2 class="head" data-fontsize="24" data-lineheight="27">Lesson Video</h2><div class="video"><iframe src="https://player.vimeo.com/video/119056274?api=1&amp;player_id=player_2&amp;wmode=opaque" width="640" height="360" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" id="player_2"></iframe> <p><a href="https://vimeo.com/119056274">RCAerialCamera Sample Video</a> from <a href="https://vimeo.com/user4376311">rcaerialcamera.com</a> on <a href="https://vimeo.com">Vimeo</a>.</p></div></div>
                    </div>';
        }

        /**
         * Display video links on course and lesson page
         * @return [type] [description]
         */
        public function display_video_button_on_course_page()
        {
            global $post;
            $current_user = wp_get_current_user();
            $email = $current_user->email;
            remove_all_actions('sensei_lesson_tutor', 10);
            if ($post->post_type == 'lesson') {
                $lesson_id= $post->ID;
                $first_lesson = intval(
                    get_post_meta($lesson_id, '_lesson_prerequisite', true)
                );
                if (isset($first_lesson) && !empty($first_lesson)) {
                    $show_actions = WooThemes_Sensei_Utils::user_completed_lesson($first_lesson, $current_user->ID);
                    if (!$show_actions) {
                        return;
                    }
                }
                $course_id = Sensei()->lesson->get_course_id($lesson_id);
                $started_course = WooThemes_Sensei_Utils::user_started_course($course_id, $current_user->ID);
                if ($started_course) {
                    $lesson_product_id = get_post_meta($lesson_id, 'wdm_course_video_woocommerce_product', true);
                    if (isset($lesson_product_id) && !empty($lesson_product_id)) {
                        $course_product_id = get_post_meta($course_id, 'wdm_course_video_woocommerce_product', true);
                        if (isset($course_product_id) && !empty($course_product_id)) {
                            $user_have_purchsed_course = wc_customer_bought_product($email, $current_user->ID, $course_product_id);
                            $user_have_purchsed_lesson = wc_customer_bought_product($email, $current_user->ID, $lesson_product_id);
                            $purchased_videos = get_user_meta($current_user->ID, 'wdm_user_purchased_videos', true);
                            $product_id = get_post_meta($course_id, '_course_woocommerce_product', true);
                            $course_video_qty = get_user_meta($current_user->ID, 'wdm_course_video_qty', true);
                            $course_qty =0;
                            if (isset($course_video_qty) && !empty($course_video_qty)) {
                                if (isset($course_video_qty[$course_id]) && !empty($course_video_qty[$course_id])) {
                                    $course_qty= $course_video_qty[$course_id];
                                }
                            }

                            if ($first_lesson && !$user_have_purchsed_lesson && !$user_have_purchsed_course && !in_array($product_id, $purchased_videos) && $course_qty < 10) {
                                // if (!$user_have_purchsed_lesson && !$user_have_purchsed_course && !in_array($course_product_id, $purchased_videos)) {
                                $link = get_permalink($lesson_product_id);
                                echo '<br><div class="courselink wdm_video_link wdm_lesson"><a href="'.$link.'" title="Purchase This Lesson Video">Purchase This Lesson Video</a></div>';
                            }
                        }
                    }
                }
            } elseif ($post->post_type == 'course') {
                $course_id = $post->ID;
                $started_course = WooThemes_Sensei_Utils::user_started_course($course_id, $current_user->ID);
                if ($started_course) {
                    $course_video_qty = get_user_meta($current_user->ID, 'wdm_course_video_qty', true);
                    $course_qty =0;
                    if (isset($course_video_qty) && !empty($course_video_qty)) {
                        if (isset($course_video_qty[$course_id]) && !empty($course_video_qty[$course_id])) {
                            $course_qty= $course_video_qty[$course_id];
                        }
                    }
                    $course_product_id = get_post_meta($course_id, 'wdm_course_video_woocommerce_product', true);
                    $product_id = get_post_meta($course_id, '_course_woocommerce_product', true);
                    //var_dump($course_product_id);
                    if (isset($course_product_id) && !empty($course_product_id)) {
                        $purchased_videos = get_user_meta($current_user->ID, 'wdm_user_purchased_videos', true);
                        $user_have_purchsed_course = wc_customer_bought_product($email, $current_user->ID, $course_product_id);
                        if (!in_array($product_id, $purchased_videos) && !$user_have_purchsed_course && $course_qty < 10) {
                            $link = get_permalink($course_product_id);
                            echo '<br><div class="courselink wdm_video_link"><a href="'.$link.'" title="Purchase This Course Videos">Purchase This Course Videos</a></div>';
                        }
                    }
                }
            }
        }

        public function video_enqueue_script()
        {
            wp_enqueue_style('wdm_content_drip_public_css', plugins_url('assets/css/wdm-sensei-content-drip-public.css', __FILE__), array(), time());
        }


        /**
     * Don't allow user for final exam before the 15 days of course completion
     * @param  boolean $can_user_view_lesson [description]
     * @param  int $lesson_id            [description]
     * @param  int $user_id              [description]
     * @return boolean                     [description]
     */
        public function wdm_course_exam_content_drip($can_user_view_lesson, $lesson_id, $user_id)
        {
            $quiz_id = get_the_ID();

            $avaliability = $this->check_exam_avaliable($quiz_id, $user_id);
            if ($avaliability != 1) {
                echo $avaliability;
                return false;
            }

            return true;
        }

        /**
         * If user opens and complete more than one quiz at same time and allowd courses are over then prevent the submit of quiz.
         * @return [type] [description]
         */
        public function wdm_content_sensei_complete_quiz()
        {
            if (isset($_POST['quiz_complete'])) {
                $quiz_id = get_the_ID();
                $user_id = get_current_user_id();
                $avaliability = $this->check_exam_avaliable($quiz_id, $user_id);
                if ($avaliability != 1) {
                    unset($_POST['quiz_complete']);
                }
            }
        }

        /**
         * check the final exam is avaliable for user
         * @param  [type] $quiz_id [description]
         * @param  [type] $user_id [description]
         * @return [type]          [description]
         */
        public function check_exam_avaliable($quiz_id, $user_id)
        {
            $post_type = get_post_type($quiz_id);

            if ($post_type == 'quiz') {
                $courses_completed = 0;
                $lesson_id = wp_get_post_parent_id($quiz_id);
                $term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all"));
                $recent_course_end_date ='';
                if (isset($term_list) && !empty($term_list)) {
                    $flag = 0;
                    foreach ($term_list as $term_single) {
                        if ($term_single->slug == 'final-exam') {
                            $flag =1;
                            break;
                        }
                    }

                    if ($flag == 1) {
                        $course_id = Sensei()->lesson->get_course_id($lesson_id);

                        $current_lesson_status = WooThemes_Sensei_Utils::user_lesson_status($lesson_id, $user_id);
                        if ($current_lesson_status->comment_approved != 'passed' && $current_lesson_status->comment_approved  != 'failed') {
                            global $wpdb;
                            $sql = "SELECT comment_ID FROM ".$wpdb->prefix."comments WHERE comment_post_ID =".$course_id . " AND user_id=".$user_id;
                            $results = $wpdb->get_results($sql);
                            //get the comment meta of user for getting the start date of course
                            foreach ($results as $result) {
                                $comment_id = $result->comment_ID;
                                $start_date = get_comment_meta($comment_id, 'start', true);
                                $start_date = date_format(new DateTime($start_date), 'Y-m-d');
                                $current_date =date('Y-m-d');

                                $current_date =new DateTime($current_date);
                                $start_date=new DateTime($start_date);
                                $days = intval($start_date->diff($current_date)->format("%a"))+1;
                                if ($days >= 18) {
                                    $activities = WooThemes_Sensei_Utils::sensei_check_for_activity(array( 'user_id' => $user_id, 'status' => 'complete,in-progress'), true);
                                    if (isset($activities) && !empty($activities)) {
                                        if (!is_array($activities)) {
                                            $activities = array($activities);
                                        }
                                        $allowed_courses = intval($days / 18);
                                        $completed =  '';
                                        foreach ($activities as $activity) {
                                            if ($activity->post_type == 'course') {
                                                if ($activity->comment_approved == 'in-progress') {
                                                    $course_id = $activity->ID;
                                                    $lessons = Sensei()->course->course_lessons($course_id);
                                                    if (is_array($lessons) && !empty($lessons)) {
                                                        foreach ($lessons as $lesson) {
                                                            $term_list = wp_get_post_terms($lesson->ID, 'lesson-tag', array("fields" => "all"));

                                                            if (isset($term_list) && !empty($term_list)) {
                                                                foreach ($term_list as $term_single) {
                                                                    if ($term_single->slug == 'final-exam') {
                                                                         $user_lesson_status = WooThemes_Sensei_Utils::user_lesson_status($lesson->ID, $user_id);
                                                                        if (isset($user_lesson_status->comment_approved) && 'failed' == $user_lesson_status->comment_approved) {
                                                                            $courses_completed++;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } elseif ($activity->comment_approved == 'complete') {
                                                    $course_end_date = $activity->comment_date;
                                                    if (isset($recent_course_end_date) && !empty($recent_course_end_date)) {
                                                        if ($recent_course_end_date < $course_end_date) {
                                                            $recent_course_end_date = $course_end_date;
                                                        }
                                                    } else {
                                                        $recent_course_end_date = $course_end_date;
                                                    }

                                                    $course_end_date = date_format(new DateTime($course_end_date), 'Y-m-d');
                                                    $course_end_date=new DateTime($course_end_date);
                                                    $completed_course_days = intval($current_date->diff($course_end_date)->format("%a"))+1;
                                                    if ($completed_course_days<=$days) {
                                                        $courses_completed++;
                                                    }
                                                }
                                            }
                                        }

                                        if ($allowed_courses <= $courses_completed) {
                                            $now = strtotime(date('Y-m-d', time()). '00:00:00'); // or your date as well

                                            $recent_course_end_date = explode(' ', $recent_course_end_date);
                                            $recent_course_end_date = strtotime($recent_course_end_date[0]);
                                            $datediff = $now - $recent_course_end_date;
                                            $curr_days = floor($datediff / (60 * 60 * 24));
                                            if ($curr_days >= 18) {
                                                return 1;
                                            }
                                            return "<div class = 'sensei-message info'>You cannot attempt to take this final exam at this time. You will be able to access this exam in ".(18-($days - ($allowed_courses*18)))." days.  The state of California requires that all students wait 18 days for each course, from the date of registration, before accessing their course exam. Since you have already completed ".$allowed_courses." exams you must wait a total of ".$days." days before your next exam is available. </div>";
                                        }
                                    }
                                } else {
                                    return "<div class = 'sensei-message info'>You cannot attempt to take this final exam at this time. You will be able to access this exam in ".(18-$days)." days. The state of California requires that all students wait 18 days for each course, from the date of registration, before accessing their course exam.</div>";
                                }
                            }
                        }
                    }
                }
            }
            return 1;
        }

        /**
         * pre populate gravity form location field
         * @param  [type] $form [description]
         * @return [type]       [description]
         */
        public function pre_selected_location($form)
        {
            wp_enqueue_script('wdm_location_gravityform_js', plugins_url('assets/js/wdm-sensei-content-drip.js', __FILE__));
            wp_localize_script('wdm_location_gravityform_js', 'location_meta', array('ajax_url'=> admin_url('admin-ajax.php')));
            $user_id = get_current_user_id();
            $default_location = get_user_meta($user_id, 'wdm_location', true);
            if (!$default_location) {
                        $args = array(
                        'post_type' => 'shop_order',
                        'posts_per_page' => '1',
                        'meta_query' => array(
                        array(
                        'key' => '_customer_user',
                        'value' =>$user_id,
                        )
                        )
                        );
                        $postslist = get_posts($args);

                        foreach ($postslist as $order) {
                            $order = new WC_Order($order->ID);
                            $items = $order->get_items();
                            foreach ($items as $item) {
                                if (isset($item['Which location would you like to train?']) && !empty($item['Which location would you like to train?'])) {
                                    $default_location = $item['Which location would you like to train?'];
                                    break 2;
                                }
                            }
                        }
            }

            foreach ($form['fields'] as $field) {
                if ($field['type'] != 'select') {
                    continue;
                }
                $test_field = array();
                foreach ($field['choices'] as $location) {
                    $location['isSelected'] = 0;
                    if (isset($default_location) && $location['value'] == $default_location) {
                        $location['isSelected'] = 1;
                    }
                     array_push($test_field, $location);
                }
                $form['fields'][0]['choices'] = $test_field;
            }
            return $form;
        }


        /**
         * Save selected location
         * @return [type] [description]
         */
        public function wdm_save_location_value()
        {
            if (isset($_POST['location']) && !empty($_POST['location'])) {
                $user_id = get_current_user_id();
                update_user_meta($user_id, 'wdm_location', $_POST['location']);
            }
            die();
        }


        /**
         * display course, lessons video products if user have not purchased the videos
         * @param  [type] $post_id [description]
         * @return [type]          [description]
         */
        public function wdm_lesson_videos($post_id)
        {
            $course_id = Sensei()->lesson->get_course_id($post_id);
            $current_user = wp_get_current_user();
            $started_course = WooThemes_Sensei_Utils::user_started_course($course_id, $current_user->ID);
            if ($started_course) {
                global $woocommerce;
                $lesson_product_id = get_post_meta($post_id, 'wdm_course_video_woocommerce_product', true);
                $email = $current_user->email;
                if (isset($lesson_product_id) && !empty($lesson_product_id)) {
                    $user_have_purchsed_lesson = wc_customer_bought_product($email, $current_user->ID, $lesson_product_id);
                    $course_product_id = get_post_meta($course_id, 'wdm_course_video_woocommerce_product', true);
                    $user_have_purchsed_course = wc_customer_bought_product($email, $current_user->ID, $course_product_id);
                    $purchased_videos = get_user_meta($current_user->ID, 'wdm_user_purchased_videos', true);
                    $product_id = get_post_meta($course_id, '_course_woocommerce_product', true);
                    $first_lesson = get_post_meta($post_id, '_lesson_prerequisite', true);
                    $course_video_qty = get_user_meta($current_user->ID, 'wdm_course_video_qty', true);
                    $course_qty =0;
                    if (isset($course_video_qty) && !empty($course_video_qty)) {
                        if (isset($course_video_qty[$course_id]) && !empty($course_video_qty[$course_id])) {
                            $course_qty= $course_video_qty[$course_id];
                        }
                    }
                    //var_dump($course_product_id);
                    if ($first_lesson && !$user_have_purchsed_lesson && !$user_have_purchsed_course && !in_array($product_id, $purchased_videos) && $course_qty < 10) {
                        remove_all_actions('sensei_lesson_video');
                        $this->display_product($lesson_product_id);
                    }
                }
            } else {
                remove_all_actions('sensei_lesson_video');
            }
        }

        public function display_product($product_id)
        {
            $course_product = new WC_Product($product_id);
                ?>
                <div class='wdm_video'>
                <div class='images wdm_video_product'>
                <?php
                echo '<a href="'.$course_product->get_permalink().'">';
                echo  $course_product->get_image('shop_thumbnail');
                echo '</a>';
                ?>
                </div>
                <div id="wdm_overlay" class="overlay">
                    <div class="overlay-content">
                        <a href="<?php echo $course_product->get_permalink();?>">Purchase This Lesson Video</a>
                    </div>
                </div>
                </div>
                <?php

        }
        public function wdm_change_order_of_active_courses_callback($active_courses)
        {
            $practice_courses = $principle_courses= $finance_courses= $other_courses =array();
            foreach ($active_courses as $active_course) {
                $course_name = $active_course->post_title;
                if (strpos($course_name, 'Practice') !== false) {
                    array_push($practice_courses, $active_course);
                } else if (strpos($course_name, 'Principles') !== false) {
                    array_push($principle_courses, $active_course);
                } else if (strpos($course_name, 'Finance') !== false) {
                     array_push($finance_courses, $active_course);
                } else {
                    array_push($other_courses, $active_course);
                }
            }
            $active_courses = array_merge($practice_courses, $principle_courses, $finance_courses, $other_courses);
            return $active_courses;
        }

        /**
         * Change Add to Cart text to Purchase video for video products
         */
        public function wdm_change_video_product_text($text)
        {
            global $product;
            $product_categories= wp_get_post_terms($product->id, 'product_cat');
            if (isset($product_categories) && !empty($product_categories)) {
                foreach ($product_categories as $product_category) {
                    if ($product_category->slug == 'course-video') {
                        return 'Purchase Course Videos';
                    } else if ($product_category->slug == 'lesson-video') {
                        return 'Purchase Lesson Video';
                    }
                }
            }
            return $text;
        }

        /**
         * Display Message above the product
         * @return [type] [description]
         */
        public function wdm_display_video_message()
        {
            global $product;
            $product_categories= wp_get_post_terms($product->id, 'product_cat');
            if (isset($product_categories) && !empty($product_categories)) {
                foreach ($product_categories as $product_category) {
                    if ($product_category->slug == 'course-video') {
                        echo '<h1>Purchase Course Videos</h1><br>';
                    } else if ($product_category->slug == 'lesson-video') {
                        echo '<h1>Purchase Lesson Video</h1><br>';
                    }
                }
            }
        }

        public function wdm_remove_quiz_link()
        {
            if (!current_user_can('administrator')) {
                add_filter('show_admin_bar', '__return_false');
                //$wp_admin_bar->remove_node('edit');
            }
        }

        // public function wdm_gform_fields_add_css($classes, $field, $form)
        // {
        //     global $product;
        //     error_log('Product - '. $product);
        //     error_log('Classes - ' . print_r($classes, 1));
        //     error_log('field - ' . print_r($field, 1));
        //     error_log('form - ' . print_r($form, 1));
        // }
    }
}
