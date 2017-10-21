<?php
/**
 * Add Admin actions
 */
class WdmSenseiQuizTimerAdmin
{
    public function __construct()
    {
        add_filter('sensei_quiz_settings', array($this,'wdm_add_quiz_settings'), 10, 1);
        add_action('admin_enqueue_scripts', array($this,'wdm_enqueue_trimer_script'), 5);
        add_action('sensei_user_course_reset', array($this,'wdm_sensei_user_course_reset'), 10, 2);
        add_action('sensei_user_lesson_reset', array($this,'wdm_sensei_user_lesson_reset'), 10, 2);
        //do_action( 'sensei_user_course_reset', $user_id, $course_id );
        //do_action( 'sensei_user_lesson_reset', $user_id, $lesson_id );
    }


    /**
     * Add sensei quiz settings in lesson meta
     * @param  [type] $settings [description]
     * @return [type]           [description]
     */
    public function wdm_add_quiz_settings($settings)
    {
        $lesson_id = get_the_ID();
        $disable_timer_fields = '';
        $posts_array = array();

        $post_args = array( 'post_type'         => 'quiz',
                            'posts_per_page'        => 1,
                            'orderby'           => 'title',
                            'order'             => 'DESC',
                            'post_parent'       => $lesson_id,
                            'post_status'       => 'publish',
                            'suppress_filters'  => 0,
                            'fields'            => 'ids',
                            );
        $posts_array = get_posts($post_args);
        $quiz_id = array_shift($posts_array);
        $activate_quiz = get_post_meta($quiz_id, '_wdm_activate_quiz_timer', true);
        if (! $activate_quiz) {
            $disable_timer_fields = 'hidden';
        }
        $quiz_settings = array(
        array(
        'id'            => 'wdm_activate_quiz_timer',
        'label'         => __('Activate Quiz Timer', 'wdm-sensei-quiz-timer'),
        'description'   => __('Enables the quiz timer', 'wdm-sensei-quiz-timer'),
        'type'          => 'checkbox',
        'default'       => '',
        'checked'       => 'off',
        ),
        array(
        'id'            => 'wdm_quiz_hour',
        'label'         => __('Quiz Time', 'wdm-sensei-quiz-timer'),
        'type'          => 'number',
        'default'       => '0',
        'placeholder'   => __('Hour', 'wdm-sensei-quiz-timer'),
        'min'           => '0',
        'max'           => '24',
        'class'             =>"wdm_timer_class $disable_timer_fields"
        ),
        array(
        'id'            => 'wdm_quiz_minutes',
        'type'          => 'number',
        'default'       => '0',
        'placeholder'   => __('Minutes', 'wdm-sensei-quiz-timer'),
        'min'           => '0',
        'max'           => '60',
        'class'             =>"wdm_timer_class $disable_timer_fields"
        ),
        array(
        'id'            => 'wdm_quiz_seconds',
        'description'   => __('Applies timer to the quiz', 'wdm-sensei-quiz-timer'),
        'type'          => 'number',
        'default'       => '0',
        'placeholder'   => __('Seconds', 'wdm-sensei-quiz-timer'),
        'min'           => '0',
        'max'           => '60',
        'class'             =>"wdm_timer_class $disable_timer_fields"
        ),
        array(
        'id'            => 'wdm_accessible_after',
        'label'         => __('Accessible after', 'wdm-sensei-quiz-timer'),
        'description'   => __("days.This quiz will be accessible after N days of the registration date. Set it 0(zero) if you don't want to restrict access.", 'wdm-sensei-quiz-timer'),
        'type'          => 'number',
        'default'       => '0',
        'min'           => '0',
        'class'             =>'wdm_accessible_after'
        ));

        $settings = array_merge($settings, $quiz_settings);
        return $settings;
    }

    /**
     * enqueue script
     * @return [type] [description]
     */
    public function wdm_enqueue_trimer_script()
    {
        wp_enqueue_style('wdm_timer_css', plugins_url('assets/css/wdm-sensei-quiz-timer-admin.css', __FILE__));
        wp_enqueue_script('wdm_countdown_js', plugins_url('assets/js/wdm-sensei-quiz-timer-admin.js', __FILE__));
    }

    public function wdm_sensei_user_course_reset($user_id, $course_id)
    {
         $lessons = Sensei()->course->course_lessons($course_id);
        foreach ($lessons as $lesson) {
            $quiz_id = Sensei()->lesson->lesson_quizzes($lesson->ID);
            $user_quiz_time = get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
            $user_quiz_time[$quiz_id]= '';
            $logged_in = get_user_meta($user_id, 'wdm_user_is_logged_in', true);
            $logged_in[$quiz_id] = 'off';
            update_user_meta($user_id, 'wdm_user_quiz_time_remaining', $user_quiz_time);
            update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
        }
    }

    public function wdm_sensei_user_lesson_reset($user_id, $lesson_id)
    {
        $quiz_id = Sensei()->lesson->lesson_quizzes($lesson_id);
        $user_quiz_time = get_user_meta($user_id, 'wdm_user_quiz_time_remaining', true);
        $user_quiz_time[$quiz_id]= '';
        $logged_in = get_user_meta($user_id, 'wdm_user_is_logged_in', true);
        $logged_in[$quiz_id] = 'off';
        update_user_meta($user_id, 'wdm_user_quiz_time_remaining', $user_quiz_time);
        update_user_meta($user_id, 'wdm_user_is_logged_in', $logged_in);
    }
}
