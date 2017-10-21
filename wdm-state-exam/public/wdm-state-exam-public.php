<?php
/**
 * Display Preparation Options
 */
if (!class_exists('WdmStateExamPublic')) {
    class WdmStateExamPublic
    {
        public function __construct()
        {
            add_action('wp_enqueue_scripts', array($this,'wdm_enqueue_script'));
            add_shortcode('wdm_question_categories', array($this,'wdm_prepare_for_state_exam_callback' ));
            add_shortcode('wdm_categories_quiz', array($this,'wdm_display_category_questions' ));
            add_action('sensei_complete_quiz', array($this,'wdm_save_prep_quiz_result'), 20);
            add_action('sensei_complete_quiz', array($this,'wdm_reset_prep_state_exam'), 25);
            add_action('sensei_single_quiz_questions_before', array($this,'wdm_hide_save_quiz'), 10);
            add_action('wdm_display_category_score', array($this,'display_score'), 10);
            //add_action('init', array($this,'one_time'));
           // add_action('woocommerce_order_status_completed', array($this,'wdm_add_user_to_state_exam'), 10, 1);
            add_action('template_redirect', array($this,'wdm_check_page_template'));
            add_shortcode('wdm_user_logs', array($this,'wdm_user_logs_questions' ));
            // Hook onto Sensei settings and load a new tab with settings for extension
            add_filter('sensei_settings_tabs', array( $this, 'prep_state_exam_settings_tabs' ));
            add_filter('sensei_settings_fields', array( $this, 'prep_state_exam_settings_fields' ));
            add_filter('wdm_prevent_mail', array($this,'wdm_prevent_mail_callback'), 10, 2);
        }


        public function wdm_hide_save_quiz()
        {
            $quiz_id = get_the_ID();
            $post_type = get_post_type($quiz_id);
            //check user have taken course
            if ($post_type == 'quiz') {
                $lesson_id = wp_get_post_parent_id($quiz_id);
                $term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all"));
                if (isset($term_list) && !empty($term_list)) {
                    $flag = 0;
                    foreach ($term_list as $term_single) {
                        if ($term_single->slug == 'state-exam') {
                            $slug=$term_single->slug;
                             echo '<input type="hidden" id="wdm_state_exam">';
                            break;
                        }
                    }
                }
            }
        }

        public function wdm_enqueue_script()
        {
            wp_enqueue_style('wdm-state-exam-public-css', plugins_url('css/wdm-state-exam-public.css', __FILE__), array(), '4.5.8');
            wp_enqueue_script('wdm-state-exam-public-js', plugins_url('js/wdm-state-exam-public.js', __FILE__), array( 'jquery' ), '4.5.6');

        }


        /**
         * Display List of categories
         * @param  [type] $atts [description]
         * @return [type]       [description]
         */
        public function wdm_prepare_for_state_exam_callback($atts)
        {
            if (is_user_logged_in()) {
                global $current_user;
                $args = array( 'course-category' => 'state-exam','post_type' => 'course' );
                $posts = get_posts($args);

                if (isset($posts) && !empty($posts)) {
                    $course = $posts[0];
                    if (isset($course->ID) && !empty($course->ID)) {
                        if (!WooThemes_Sensei_Utils::user_started_course($course->ID, $current_user->ID)) {
                            $wc_post_id = absint(get_post_meta($course->ID, '_course_woocommerce_product', true));
                            echo 'Please purchase the <a href='.get_permalink($wc_post_id).'> state exam course </a> before starting this Quiz.';
                            return;
                        }
                    }
                }

                $terms = get_terms('question-category');
                echo '<table class="wdm-table" cellspacing="5" cellpadding="5">
            <thead><tr><th><h3>Category Name</h3></th><th><h3>Take/Review Questions</h3></th></tr></thead><tbody>';
                foreach ($terms as $term) {
                    echo '<tr><td>'.$term->name.'</td><td><a href='.$this->wdm_get_url_by_shortcode('wdm_categories_quiz').'?category='.$term->slug.'>Click here</a></td></tr>';
                }
                echo '</tbody></table>';
            }
        }

        public function wdm_display_category_questions()
        {
            if (is_user_logged_in()) {
                global $post, $woothemes_sensei, $current_user;
                $category = $_GET['category'];
                $qargs = array(
                            'post_type'         => 'question',
                            'posts_per_page'        => -1,
                            'orderby'           => 'rand',
                            'tax_query'             => array(
                                array(
                                    'taxonomy'  => 'question-category',
                                    'field'     => 'slug',
                                    'terms'         => $category
                                )
                            ),
                            'post_status'       => 'publish',
                        );
                        $lesson_quiz_questions = get_posts($qargs);

                $args = array( 'course-category' => 'state-exam','post_type' => 'course' );
                $posts = get_posts($args);

                if (isset($posts) && !empty($posts)) {
                    $course = $posts[0];
                    if (isset($course->ID) && !empty($course->ID)) {
                        if (!WooThemes_Sensei_Utils::user_started_course($course->ID, $current_user->ID)) {
                            $wc_post_id = absint(get_post_meta($course->ID, '_course_woocommerce_product', true));
                            echo 'Please purchase the <a href='.get_permalink($wc_post_id).'> state exam course </a> before starting this Quiz.';
                            return;
                        }
                    }
                }
                $addclass="passexam";
                ?>
                <article class="quiz">
                <?php
                $html = '';
                if (isset($_GET['contact'])) {
                                $html .= $this->teacher_contact_form($post);
                } else {
                    $html .= '<span class="bp-sensei-msg-link wdm-sensei-msg-link"><a class="button send-message-button" href="'.add_query_arg($_SERVER['QUERY_STRING'], '', get_permalink($post->ID)).'&contact=quiz#private_message">Contact Lesson Tutor</a></span>';
                }

                echo $html;
                if (count($lesson_quiz_questions)) {
                    ?>
                    <span class="bp-sensei-msg-link wdm-sensei-msg-link wdm_reveal_answer"><a class="button answers-button" >Reveal Answers</a></span>
                    <?php
                }
                    ?>

        <div class="lesson-meta" id="examdetail">
        <div class="loader"></div>
            <?php
            $quiz_result=0;
            if (isset($_POST['quiz_complete']) && !empty($_POST['quiz_complete'])) {
                $cnt=0;
                foreach ($_POST['sensei_question'] as $qid => $answer) {
                    $right_ans = get_post_meta($qid, '_question_right_answer', true);
                    if ($answer == $right_ans) {
                        $cnt++;
                    }
                }
                if (isset($_POST['wdm-total-ques']) && !empty($_POST['wdm-total-ques'])) {
                    $quiz_result = intval($cnt/intval($_POST['wdm-total-ques'])*100);
                }
                 $result_text=$class='';
                if (!empty($quiz_result)) {
                    $result_text="You have completed the test. Your score is $quiz_result%. Go back to <a href=".$this->wdm_get_url_by_shortcode('wdm_question_categories').">state exam categories.</a>";
                    $class= 'info';
                } elseif ($quiz_result == 0) {
                    $result_text="You have completed the test. Your score is 0%. Go back to <a href=".$this->wdm_get_url_by_shortcode('wdm_question_categories').">state exam categories.</a>";
                    $class= 'info';
                }
            }



            echo '<div class="sensei-message '. $class.' ">' . $result_text . '</div>';

            // Lesson Quiz Meta
            if (0 < count($lesson_quiz_questions)) {
                $question_count = 1;
                ?>
                <form method="POST" action="<?php echo esc_url(get_permalink()); ?>" enctype="multipart/form-data" >

            <ol id="sensei-quiz-list">
                <?php
                    set_query_var('questions', $lesson_quiz_questions);

                    load_template(dirname(dirname(__FILE__)).'/templates/question_type-multiple-choice.php');
        ?>

            </ol>
            <input type='hidden' id="wdm-total-ques" name="wdm-total-ques" value=<?php echo count($lesson_quiz_questions);?>>
                <span><input type="submit" id="wdm-prep-state-exam" name="quiz_complete" class="quiz-submit complete" value="Complete Exam"></span>
        </form>
    <?php                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     } ?>
    </div>
    </article>
    <?php
            }
        }


        /**
         * Save preparation exam result
         * @return [type] [description]
         */
        public function wdm_save_prep_quiz_result()
        {
            if (isset($_POST['quiz_complete']) && !empty($_POST['quiz_complete'])) {
                $quiz_id = get_the_ID();
                $user_id = get_current_user_id();
                $lesson_id = wp_get_post_parent_id($quiz_id);
                $slug='';
                $term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all"));
                if (isset($term_list) && !empty($term_list)) {
                    $flag = 0;
                    foreach ($term_list as $term_single) {
                        if ($term_single->slug == 'state-exam') {
                            $slug=$term_single->slug;
                            $flag =1;
                            break;
                        }
                    }

                    if ($flag == 1) {
                        $quiz_questions=WooThemes_Sensei_Utils::sensei_get_quiz_questions($quiz_id);
                        $categorywise_grade=array();
                        $category_no_of_questions =array();
                        $category_grade = array();
                        $user_grades = WooThemes_Sensei_Utils::get_user_data('quiz_grades', $lesson_id, $user_id);
                        foreach ($quiz_questions as $quiz_question) {
                            if (!isset($user_grades[$quiz_question->ID])) {
                                $user_grades[$quiz_question->ID]=0;
                            }
                        }
                        foreach ($user_grades as $question_id => $remark) {
                            $category_detail=wp_get_object_terms(intval($question_id), 'question-category');
                            if (isset($category_detail) && !empty($category_detail)) {
                                $cat_id =$category_detail[0]->term_id;
                                $cat_grade=$categorywise_grade[$cat_id];
                                if (!isset($cat_grade) && empty($cat_grade)) {
                                    if ($remark) {
                                        $categorywise_grade[$cat_id] = 1;
                                    } else {
                                        $categorywise_grade[$cat_id] = 0;
                                    }
                                    $category_no_of_questions[$cat_id]=1;
                                } else {
                                    if ($remark) {
                                        $categorywise_grade[$cat_id] += 1;
                                    }
                                    $category_no_of_questions[$cat_id]+=1;
                                }
                            }
                        }
                        foreach ($category_no_of_questions as $cat_id => $questions) {
                            if (!isset($categorywise_grade[$cat_id]) || empty($categorywise_grade[$cat_id])) {
                                $categorywise_grade[$cat_id] =0;
                            }
                            $category_grade[$cat_id]=intval($categorywise_grade[$cat_id]/$questions*100);
                        }
                        $category_grade = maybe_serialize($category_grade);
                        $today = current_time('mysql');
                        $counts = array_count_values($user_grades);
                        $correct_ans=0;
                        if (isset($counts[1]) && !empty($counts[1])) {
                            $correct_ans=$counts[1];
                        } else {
                            $correct_ans =0;
                        }
                        global $wpdb;
                        $inserted_id = $wpdb->insert(
                            $wpdb->prefix.'pre_state_exam_score',
                            array(
                                'userid' => $user_id,
                                'lessonid' => $lesson_id ,
                                'completion_date'=>$today,
                                'score'=>$correct_ans,
                                'cat_score'=>$category_grade,
                            ),
                            array(
                                '%d',
                                '%d',
                                '%s',
                                '%s',
                                '%s'
                            )
                        );
                    }
                }
            }
        }

        /**
         * Reset quiz
         * @return [type] [description]
         */
        public function wdm_reset_prep_state_exam()
        {
            if (!isset($_POST) || empty($_POST)) {
                $quiz_id = get_the_ID();
                $user_id = get_current_user_id();
                $lesson_id = wp_get_post_parent_id($quiz_id);
                $term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all"));
                if (isset($term_list) && !empty($term_list)) {
                   // $flag = 0;
                    foreach ($term_list as $term_single) {
                        if ($term_single->slug == 'state-exam') {
                            // $flag =1;
                            $quiz_data = new WooThemes_Sensei_Quiz();
                            $deleted_data=$quiz_data->reset_user_lesson_data($lesson_id, $user_id);

                            $logged_in = get_user_meta($user_id, 'wdm_user_is_logged_in', true);
                            $logged_in[$quiz_id]='off';
                            update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
                            $user_quiz_time_test=get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
                            $user_quiz_time_test[$quiz_id] = '03:15:00';
                            update_user_meta($user_id, 'wdm_user_quiz_time_remaining', $user_quiz_time_test);
                            break;
                        }
                    }
                }
            }
        }

        /**
         * return a url which contains given shortcode
         * @param  [type] $shortcode [description]
         * @return [type]            [description]
         */
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

/**
 *  Display score after preparation exam complition
 * @return [type] [description]
 */
        public function display_score()
        {
            if (isset($_POST['quiz_complete']) && !empty($_POST['quiz_complete'])) {

                $quiz_id = get_the_ID();
                $user_id = get_current_user_id();
                $lesson_id = wp_get_post_parent_id($quiz_id);
                global $wpdb;
                $table=$wpdb->prefix.'pre_state_exam_score';
                $sql= "SELECT cat_score from $table where userid = ".$user_id." AND lessonid=".$lesson_id ." ORDER BY id DESC LIMIT 1";
                $result = $wpdb->get_results($sql);

                $category_score = unserialize($result[0]->cat_score);
                if(isset($category_score) && !empty($category_score)){
                echo'<div>';
                echo '<h3>Grade Breakdown :</h3>';
                echo '<table class=wdm-category-table>';
                $this->display_category_score($category_score);
                echo'</table>';
                echo'<span id="wdm-back-to-lesson"><a class="button wdm-back-button" href="'.$this->wdm_get_url_by_shortcode('wdm_prepare_state_exam').'">Back to preparation exam</a></span>';
                echo'</div>';
            }
            }
        }

        /**
         * Enroll All users in state exam
         */
        public function one_time()
        {
            $option = get_option('wdm_add_users1');
            if (!isset($option) || empty($option)) {
                $all_users = get_users();
                $course_id = '18809';
                //$product_ids= array ('11955','11333','11947','11963','11971','11979','11987','18506','18507','18508','18509','18510','18511','18512',' 18599');

                $product_ids= array ('13884','13883','18518','7982');

                foreach ($product_ids as $product_id) {
                    foreach ($all_users as $user) {
                        $customer_email = $user->email;
                        if (wc_customer_bought_product($customer_email, $user->ID, $product_id)) {
                            $result = WooThemes_Sensei_Utils::user_start_course($user->ID, $course_id);

                        }
                    }
                }
                update_option('wdm_add_users1', 1);
            }
        }


        /**
         * Enroll user into state exam course
         */
        public function wdm_add_user_to_state_exam($order_id)
        {
            $order = new WC_Order($order_id);
            $user_id = $order->user_id;
            $course_id = '18554';
            $result = WooThemes_Sensei_Utils::user_start_course($user_id, $course_id);
        }



        /**
         * Display Contact lesson tutor button on category quiz
         * @param  [type] $post [description]
         * @return [type]       [description]
         */
        public function teacher_contact_form($post)
        {
            if (! is_user_logged_in()) {
                return;
            }

            global $current_user;
            wp_get_current_user();

            $html = '';

            if (! isset($post->ID)) {
                return $html;
            }

        //confirm private message
            $confirmation = '';
            if (isset($_GET[ 'send' ]) && 'complete' == $_GET[ 'send' ]) {
                $confirmation_message = __('Your message has been sent.  Please allow a few moments to receive a reply. Thanks', 'woothemes-sensei');
                $confirmation .= '<script language="javascript">';
                $confirmation .= 'window.location.href = window.location.href.split("&")[0];';
                $confirmation .= 'alert("Your message has been sent.  Please allow a few moments to receive a reply. Thanks!")';
                $confirmation .= '</script>';
            }


            $qargs = array(
                            'post_type'         => 'course',
                            'posts_per_page'        => -1,
                            'tax_query'             => array(
                                array(
                                    'taxonomy'  => 'course-category',
                                    'field'     => 'slug',
                                    'terms'         => 'state-exam',
                                )
                            ),
                            'post_status'       => 'publish',
                        );
                        $state_exam_course = get_posts($qargs);
                        $author = $post->post_author;
            if (isset($state_exam_course) && !empty($state_exam_course)) {
                $author= $state_exam_course[0]->post_author;
            }

            $html .= '<h3 id="private_message">' . __('Send Private Message', 'woothemes-sensei') . '</h3>';
            $html .= '<p>';
            $html .=  $confirmation;
            $html .= '</p>';
            $html .= '<form name="contact-teacher" action="" method="post" >';
            $html .= '<p class="form-row form-row-wide">';
                $html .= '<textarea name="contact_message" class="wdm-contact-teacher" placeholder="' . __('Enter your message.', 'woothemes-sensei') . '"></textarea>';
            $html .= '</p>';
            $html .= '<p class="form-row">';
                $html .= '<input type="hidden" name="post_id" value="' . $post->ID . '" />';
                $html .= '<input type="hidden" name="sender_id" value="' . $current_user->ID . '" />';
                $html .= '<input type="hidden" name="receiver_id" value="' . $author . '" />';
                $html .= wp_nonce_field('message_teacher', 'sensei_message_teacher_nonce', true, false);
                $html .= '<input type="submit" class="send_message" value="' . __('Send Message', 'woothemes-sensei') . '" />';
            $html .= '</p>';
            $html .= '<div class="fix"></div>';
            $html .= '</form>';
            return '<div class="private-msg wdm-private-msg">'.$html.'</div>';
        }

        public function wdm_user_logs_questions($atts)
        {
            if (is_user_logged_in()) {
                $category = $_GET['quiz_id'];
                    global $wpdb;
                    $table=$wpdb->prefix.'pre_state_exam_score';
                if (isset($category) && !empty($category)) {
                    $sql= "SELECT cat_score from $table where id=".$category;
                    $results = $wpdb->get_results($sql);
                    $category_score = unserialize($results[0]->cat_score);
                     echo'<div>';
                    if (isset($category_score) && !empty($category_score)) {
                        echo '<h3>Grade Breakdown :</h3>';
                        echo '<table class=wdm-category-table>';
                        $this->display_category_score($category_score);
                        echo'</table>';
                        echo'<span id="wdm-back-to-lesson"><a class="button wdm-back-button" href="'.$this->wdm_get_url_by_shortcode('wdm_user_logs').'">Back to Logs</a></span>';
                    } else {
                        echo 'Opps! It seems you have not attempted the prep state exam.';
                    }
                    echo'</div>';
                } else {
                    global $post;
                    $lesson_id =$atts['lesson_id'];
                    $url = get_permalink($post->ID);
                     echo'<div>';
                    $user_id= get_current_user_id();
                    $sql= "SELECT * from $table where userid = ".$user_id." AND lessonid=".$lesson_id ." ORDER BY id DESC";
                    $results = $wpdb->get_results($sql);
                    if (isset($results) && !empty($results)) {
                        echo '<table class="wdm-logs-table scroll">';
                        echo '<thead><tr>
                    <th>Test Name</th>
                    <th>Date Taken</th>
                    <th>Score</th>
                    <th>Pass/Fail</th>
                    <th>Score Breakdown</th></tr></thead>';
                        foreach ($results as $result) {
                            $lessonid= $result->lessonid;
                            $quiz_id = get_post_meta($lessonid, '_lesson_quiz', true);
                            $quiz_passmark = get_post_meta($quiz_id, '_quiz_passmark', true);
                            $total_questions = intval(get_post_meta($quiz_id, '_show_questions', true));
                            $current_score = ($result->score/$total_questions )* 100;
                            echo '<tr>';
                            echo '<td>'.get_the_title($lessonid).'</td>';
                            echo '<td>'.$result->completion_date.'</td>';
                            echo '<td>'.$result->score.'/'.$total_questions.'</td>';
                            if ($current_score >= $quiz_passmark) {
                                echo '<td class="wdm_pass">Pass</td>';
                            } else {
                                echo '<td class="wdm_fail">Fail</td>';
                            }
                                echo '<td><a href="'.$url.'?quiz_id='.$result->id.'">Open Here</a></td>';
                                echo '</tr>';
                        }
                        echo '</table>';
                    } else {
                        echo 'Opps! It seems you have not attempted the prep state exam.';
                    }
                    echo'</div>';
                }
            }
        }

        /**
         * If user is not logged in redirect him to home page
        */

        public function wdm_check_page_template()
        {
            global $post;
            $post_id=$post->ID;
            if (!is_user_logged_in()) {
                $template = get_page_template_slug($post_id);//die();
                if ($template == 'wisdmlabs-page-template.php') {
                    wp_safe_redirect(get_home_url());
                    exit();
                }
            } else {
                $post_type = get_post_type($post_id);
                $post_category=array();
                if ($post_type == 'course') {
                    $terms = wp_get_post_terms($post_id, 'course-category', true);
                } else if ($post_type == 'lesson') {
                    $terms = wp_get_post_terms($post_id, 'lesson-tag', true);
                }
                if (isset($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        array_push($post_category, $term->slug);
                    }
                }
                if (in_array('state-exam', $post_category)) {
                    $settings = get_option('woothemes-sensei-settings', true);
                    if (isset($settings['prep-state-exam']) && !empty($settings['prep-state-exam'])) {
                        $url = get_permalink(intval($settings['prep-state-exam']));
                        wp_safe_redirect($url);
                        exit();
                    }
                }
            }
        }

        /**
         * Add preparation state exam tab in settings menu
         * @param  [type] $sections [description]
         * @return [type]           [description]
         */
        public function prep_state_exam_settings_tabs($sections)
        {
             $sections['prep-state-exam'] = array(
            'name'          => __('Prep State Settings', 'wdm-state-exam'),
            'description'   => __('Select preparation state exam page', 'wdm-state-exam')
             );

             return $sections;
        }

        public function prep_state_exam_settings_fields($fields)
        {

            $pages = query_posts(array(
                'post_type'  => 'page',
                'posts_per_page' => -1
            ));
            $state_exam_pages=array();
            foreach ($pages as $page) {
                $state_exam_pages[$page->ID] = $page->post_title;
            }
            wp_reset_query();

            $course_display_settings = array( 'excerpt' => __('Course Excerpt', 'woothemes-sensei'), 'full' => __('Full Course Content', 'woothemes-sensei') );
            $view_link_courses = $woothemes_sensei->settings->settings[ 'prep-state-exam' ];
            $fields['prep-state-exam'] = array(
            'name'          => __('State Exam', 'wdm-state-exam'),
            'description'   => __('Select page for display state exam.', 'wdm-state-exam'),
            'type'          => 'select',
            'section'       => 'prep-state-exam',
            'required'      => 0,
            'options'       => $state_exam_pages
            );

            return $fields;
        }

        public function wdm_prevent_mail_callback($send, $quiz_id)
        {
            $lesson_id = wp_get_post_parent_id($quiz_id);
                $term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all"));
            if (isset($term_list) && !empty($term_list)) {
                $flag = 0;
                foreach ($term_list as $term_single) {
                    if ($term_single->slug == 'state-exam') {
                        return false;
                    }
                }
            }
            return true;
        }


        public function display_category_score($category_score)
        {
            foreach ($category_score as $category_id => $score) {
                        $cat_name = get_term($category_id);
                        echo '<tr>';
                        echo '<td>'.$cat_name->name.'</td>';
                        echo '<td>'.$score.'%</td>';
                        echo '</tr>';
            }
        }
    }
}
