<?php
/**
 * Add Admin actions
 */
class WdmSenseiQuizTimerPublic
{
    public function __construct()
    {
        add_action('sensei_single_quiz_questions_before', array($this,'wdm_display_quiz_time'), 10);
        add_action('sensei_complete_quiz', array($this,'wdm_sensei_complete_quiz'), 15);
        add_action('wp_ajax_save_time_remaining', array($this,'wdm_save_quiz_time_remaining'));
        add_action('wp_ajax_nopriv_save_time_remaining', array($this,'wdm_save_quiz_time_remaining'));
        add_filter('sensei_can_user_view_lesson', array($this,'wdm_restrict_user_view_quiz'), 10, 3);
        add_action('sensei_quiz_single_title', array($this,'display_restrict_message'), 30);
        add_action('sensei_user_quiz_submitted', array($this,'wdm_sensei_user_lesson_end'), 10, 5);
        remove_all_actions('sensei_reset_lesson_button');
        //add_action('sensei_single_quiz_questions_before', array($this,'wdm_course_exam_content_drip'), 20);
    }


/**
     * Display quiz timer on view quiz page
     * @param  [type] $quiz_id [description]
     * @return [type]          [description]
     */
    public function wdm_display_quiz_time()
    {

        $quiz_id = get_the_ID();
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $post_type = get_post_type($quiz_id);
            //check user have taken course
            if ($post_type == 'quiz') {
                $course_id = Sensei()->lesson->get_course_id(wp_get_post_parent_id($quiz_id));
                if (! WooThemes_Sensei_Utils::user_started_course($course_id, $user_id)) {
                          return;
                }

                $lesson_id = wp_get_post_parent_id($quiz_id);
                $lesson_complete = WooThemes_Sensei_Utils::user_completed_lesson($user_lesson_status);
                $user_lesson_status = WooThemes_Sensei_Utils::user_lesson_status($lesson_id, $user_id);
                $valid_status=array('completed','failed','passed','graded');
                if (!isset($user_lesson_status->comment_approved) || !in_array($user_lesson_status->comment_approved, $valid_status)) {
                    $check_quiz_timer=get_post_meta($quiz_id, '_wdm_activate_quiz_timer', true);
                    if (isset($check_quiz_timer) && $check_quiz_timer == 'on') {
                        $user_quiz_time=get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
                        if (isset($user_quiz_time[$quiz_id]) && !empty($user_quiz_time[$quiz_id])) {
                            $formatted_date = $user_quiz_time[$quiz_id];
                            $remaining_time = explode(':', $formatted_date);
                            $hour = $remaining_time[0];
                            $minutes = $remaining_time[1];
                            $seconds = $remaining_time[2];
                        } else {
                            $hour = get_post_meta($quiz_id, '_wdm_quiz_hour', true);
                            $minutes = get_post_meta($quiz_id, '_wdm_quiz_minutes', true);
                            $seconds = get_post_meta($quiz_id, '_wdm_quiz_seconds', true);
                            $user_quiz_time [$quiz_id] = $hour .':'.$minutes.':'.$seconds;
                            update_user_meta($user_id, 'wdm_user_quiz_time_remaining', $user_quiz_time);
                        }

                        $time_stamp = get_user_meta($user_id, 'wdm_user_quiz_time_stamp', true);

                        if (!isset($time_stamp[$quiz_id]) || empty($time_stamp[$quiz_id]))
                            $time_stamp[$quiz_id] = time();

                        $period_leave_test = time() - $time_stamp[$quiz_id];
                        $remaining_time = strtotime("$hour:$minutes:$seconds") - $period_leave_test;

                        $formatted_date = date("H:i:s", $remaining_time);
                        $remaining_time = explode(':', $formatted_date);

                        $hour = $remaining_time[0];
                        $minutes = $remaining_time[1];
                        $seconds = $remaining_time[2];

                        if ($hour >= 2){
                            $term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all"));
                            foreach ($term_list as $term_single) {
                                if ($term_single->slug == 'state-exam') {
                                    $hour = 3;
                                    if ($minutes >= 15){
                                      $minutes = 15;
                                      $seconds = 0;
                                    }
                                    break;
                                }else {
                                    if ($minutes >= 30){
                                        $hour = 2;
                                        $minutes = 30;
                                        $seconds = 0;
                                    }
                                }
                            }
                        }

                        $logged_in = get_user_meta($user_id, 'wdm_user_is_logged_in', true);
                        if (empty($logged_in) || $logged_in[$quiz_id] =='off') {
                            $logged_in[$quiz_id] = 'on';
                            update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
                            $logged_in[$quiz_id] = 'off';
                        }
                        // $curr_time = current_time('mysql');
                        // var_dump($curr_time);
                        // $time =new DateTime($curr_time);
                        // var_dump($time);
                        // $time->add(new DateInterval('PT' . $hour . 'H'));
                        // $time->add(new DateInterval('PT' . $minutes . 'M'));
                        // $time->add(new DateInterval('PT' . $seconds . 'S'));
                        // $formatted_date = $time->format('Y/m/d H:i:s');

                        echo '
                            <div style="float:right;">';
                        if ( get_the_ID() != 18813 ) {
                            echo '
                                <a class="above save-answers" href="javascript:void(0)">Save Exam Answers</a>
                            ';
                        }
                        echo '  Time Remaining - <span id="timer"></span>
                                <input type="hidden" id="wdm_auto_complete_quiz" value="0">
                            </div>';

                        //$timezone = get_option('gmt_offset');
                        wp_enqueue_script('wdm_jquery_min_js', 'https://code.jquery.com/jquery-2.2.3.min.js');
                        wp_enqueue_script('jquery_min_js', plugins_url('assets/js/jquery.min.js', __FILE__));
                        wp_enqueue_script('wmd_plugin_min_js', plugins_url('assets/js/jquery.plugin.js', __FILE__));
                        wp_enqueue_script('wmd_countdown_js', plugins_url('assets/js/jquery.countdownTimer.js', __FILE__));
                        wp_enqueue_script('wmd_quiz_timer_js', plugins_url('assets/js/wdm-sensei-quiz-timer-public.js?ver=4.5.7', __FILE__));
                        wp_enqueue_style('wdm_quiz_timer_public_css', plugins_url('assets/css/wdm-sensei-quiz-timer-public.css', __FILE__));
                        wp_localize_script('wmd_quiz_timer_js', 'quiz_meta', array( 'quiz_time' => $formatted_date , 'ajax_url'=> admin_url('admin-ajax.php'),'quiz_id'=>$quiz_id,'user_id'=>$user_id,'logged_in'=>$logged_in[$quiz_id], 'hour'=>$hour, 'minutes'=>$minutes, 'seconds'=>$seconds));
                    }
                }
            }
        }
    }

    /**
     * Save remaining quiz time after specified time interval.
     * @return [type] [description]
     */
    public function wdm_save_quiz_time_remaining()
    {
        $user_id= $_POST['user_id'];
        $quiz_id = $_POST['quiz_id'];
        $logged_in = get_user_meta($user_id, 'wdm_user_is_logged_in', true);
        echo 'hello - ';
        if ($_POST['timer_status'] == 'active') {
            if (isset($_POST['reload']) && $_POST['reload'] == 'true') {
                if ($logged_in[$quiz_id] =='on') {
                    $logged_in[$quiz_id]='off';
                    update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
                }
            }
            $user_quiz_time_test=get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
            $user_quiz_time_test[$quiz_id] = $_POST['time'];

            $user_quiz_time_stamp = get_user_meta($user_id, 'wdm_user_quiz_time_stamp', true);
            $user_quiz_time_stamp[$quiz_id] = time();
            update_user_meta($user_id, 'wdm_user_quiz_time_stamp', $user_quiz_time_stamp);

            update_user_meta($user_id, 'wdm_user_quiz_time_remaining', $user_quiz_time_test);
            echo($user_quiz_time_test[$quiz_id]);
        } else {
            if ($logged_in[$quiz_id] =='off') {
                $user_quiz_time=get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
                if (isset($user_quiz_time[$quiz_id]) && !empty($user_quiz_time[$quiz_id])) {
                    $formatted_date = $user_quiz_time[$quiz_id];
                    $remaining_time = explode(':', $formatted_date);
                    $hour = $remaining_time[0];
                    $minutes = $remaining_time[1];
                    $seconds = $remaining_time[2];
                }
                  //$curr_time = current_time('mysql');
                  //var_dump($curr_time);
                      // $time =new DateTime($curr_time);
                      // $time->add(new DateInterval('PT' . $hour . 'H'));
                      // $time->add(new DateInterval('PT' . $minutes . 'M'));
                      // $time->add(new DateInterval('PT' . $seconds . 'S'));
                  $formatted_date = $hour .':'.$minutes.':'.$seconds;
                  $logged_in[$quiz_id] = 'on';
                  update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
                  echo($formatted_date);
            }
        }
        die();
    }

    /**
 * check number of days are greater than
 * @param  [type] $can_user_view_lesson [description]
 * @param  [type] $lesson_id            [description]
 * @param  [type] $user_id              [description]
 * @return [type]                       [description]
 */
    public function wdm_restrict_user_view_quiz($can_user_view_lesson, $lesson_id, $user_id)
    {
        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);
        if ($post_type == 'quiz') {
            $quiz_id = Sensei()->lesson->lesson_quizzes($lesson_id);
            $accessible_after = get_post_meta($quiz_id, '_wdm_accessible_after', true);
            if ($accessible_after > 0) {
                $udata = get_userdata($user_id);
                $registered = $udata->user_registered;
                $date = strtotime($registered);
                $date = strtotime("+".$accessible_after." day", $date);
                $current_date = intval(current_time(timestamp));
                if ($current_date <= $date) {
                    return false;
                }
            }
        }
        return true;
    }

/**
 * display restrict access message if user tries to access the page before specified number of days.
 * @return String  restriction message.
 */
    public function display_restrict_message()
    {
        $post_id = get_the_ID();
        $accessible_after = get_post_meta($post_id, '_wdm_accessible_after', true);
        if ($accessible_after > 0) {
            $udata = get_userdata(get_current_user_id());
            $registered = $udata->user_registered;
            $registered = get_date_from_gmt($registered);
            $date = strtotime($registered);
            $date = strtotime("+".$accessible_after." day", $date);
            $current_date = intval(current_time(timestamp));
            if ($current_date <= $date) {
                remove_action('sensei_single_quiz_content_inside_before', array( 'Sensei_Quiz', 'the_user_status_message' ), 40);
                echo "<div class = 'sensei-message info'>You can not attempt this quiz now. You will have access to this quiz from ".date('jS F Y', $date)."</div>";
            }
        }
    }

    public function wdm_sensei_user_lesson_end($user_id, $quiz_id, $grade, $quiz_pass_percentage, $quiz_grade_type)
    {
        $user_quiz_time = get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
        $user_quiz_time[$quiz_id]= '';

        $user_quiz_time_stamp = get_user_meta($user_id, 'wdm_user_quiz_time_stamp', true);
        if (!is_array($user_quiz_time_stamp))
            $user_quiz_time_stamp = array();
        $user_quiz_time_stamp[$quiz_id]= '';

        $logged_in = get_user_meta($user_id, 'wdm_user_is_logged_in', true);
        $logged_in[$quiz_id] = 'off';
        update_user_meta($user_id, 'wdm_user_quiz_time_remaining', $user_quiz_time);
        update_user_meta($user_id, 'wdm_user_quiz_time_stamp', $user_quiz_time_stamp);
        update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
    }


    public function wdm_sensei_complete_quiz()
    {
        if (isset($_POST['quiz_complete']) && !empty($_POST['quiz_complete'])) {
            $quiz_id = get_the_ID();
            $user_id = get_current_user_id();
            $lesson_id = wp_get_post_parent_id($quiz_id);
            if (empty($_POST[ 'sensei_question' ])) {
                $status = 'failed';
                $metadata =array('grade'=>0);
                WooThemes_Sensei_Utils::update_lesson_status($user_id, $lesson_id, $status, $metadata);
            }
        }
    }
}
