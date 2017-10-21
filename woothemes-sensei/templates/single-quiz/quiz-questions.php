<?php
/**
 * The Template for displaying all Quiz Questions.
 *
 * Override this template by copying it to yourtheme/sensei/single-quiz/quiz-questions.php
 *
 * @author      WooThemes
 * @package     Sensei/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

global $post, $woothemes_sensei, $current_user;

// Get User Meta
get_currentuserinfo();

// Handle Quiz Completion
do_action('sensei_complete_quiz');

// Get Frontend data
$user_quiz_grade = $woothemes_sensei->quiz->data->user_quiz_grade;
$quiz_lesson = $woothemes_sensei->quiz->data->quiz_lesson;
$quiz_grade_type = $woothemes_sensei->quiz->data->quiz_grade_type;
$user_lesson_end = $woothemes_sensei->quiz->data->user_lesson_end;
$user_lesson_complete = $woothemes_sensei->quiz->data->user_lesson_complete;
$lesson_quiz_questions = $woothemes_sensei->quiz->data->lesson_quiz_questions;




// Check if the user has started the course
$lesson_course_id = absint(get_post_meta($quiz_lesson, '_lesson_course', true));
$has_user_start_the_course = WooThemes_Sensei_Utils::user_started_course($lesson_course_id, $current_user->ID);

// Get the meta info
$quiz_passmark = absint(get_post_meta($post->ID, '_quiz_passmark', true));
$quiz_passmark_float = (float) $quiz_passmark;
$term_list = wp_get_post_terms($quiz_lesson, 'lesson-tag', array("fields" => "all", "tag" => "final-exam"));
$addclass="";
if (count($term_list)>0) {
    $addclass="passexam";
}
if(apply_filters('sensei_can_user_view_lesson',$can_user_view_lesson=true, $quiz_lesson, $current_user->ID )){

    do_action('sensei_single_quiz_questions_before');
?>
<div class="lesson-meta" id="examdetail">
    <?php

    // Display user's quiz status
    $status = WooThemes_Sensei_Utils::sensei_user_quiz_status_message($quiz_lesson, $current_user->ID);

    foreach ( $term_list as $key => $term ) {
        if ( $term->slug == 'final-exam' ) {
            $course_id = intval(get_post_meta($quiz_lesson, '_lesson_course', true));
            $link = get_permalink( $course_id );

            $lesson_completed = WooThemes_Sensei_Utils::user_completed_lesson($quiz_lesson, $current_user->ID);
            $lesson_status = WooThemes_Sensei_Utils::user_lesson_status($quiz_lesson, $current_user->ID)->comment_approved;
    
            $comment_id = WooThemes_Sensei_Utils::user_lesson_status($quiz_lesson, $current_user->ID)->comment_ID;
            $percentage = get_comment_meta($comment_id, 'grade', true);

            if ( strpos( $post->post_title, 'Final Exam 2' ) !== false) {
                if ($lesson_completed){
                    $course_result_link = $woothemes_sensei->course_results->get_permalink($course_id);

                    $status['message'] = "Congratulations on passing your course exam! <b>You scored a {$percentage}%!</b> You can now access your course certificate of completion. Please click <a href={$course_result_link}>here</a> to view your course results.";
                } elseif (isset($lesson_status) && $lesson_status == 'failed') {              
                    $status['message'] = "We are sorry to inform you but you have failed your 2nd attempt at the exam. We are unable to provide the answers to course final exams. It is a requirement from the California Bureau of Real Estate to protect the integrity of the test bank. <b>Since you have failed your course exam for a second time, you must retake all quizzes and wait 18 days to retake the final exam.</b> All subsequent examinations will have a different order of questions and answers. Click <a href={$link}>here</a> to return to your course.";
                    }
            } else {
                if ($lesson_completed){
                    $course_result_link = $woothemes_sensei->course_results->get_permalink($course_id);

                    $status['message'] = "Congratulations on passing your course exam! <b>You scored a {$percentage}%!</b> You can now access your course certificate of completion. Please click <a href={$course_result_link}>here</a> to view your course results.";
                } elseif (isset($lesson_status) && $lesson_status == 'failed') {
                    $status['message'] = "We are sorry to inform you but you have failed your exam. We are unable to provide the answers to course final exams. It is a requirement from the California Bureau of Real Estate to protect the integrity of the test bank. <b>You will have an additional attempt to pass the exam. Click <a href={$link}>here</a> to attempt the 2nd exam.</b>";
                }
            }
        }
    }
    echo '<div class="sensei-message ' . $status['box_class'] . '">' . $status['message'] . '</div>';

    /** Wdm Changes start for state exam course categories */
    do_action('wdm_display_category_score');
    /** Wdm Changes End */

    // Lesson Quiz Meta
    if (0 < count($lesson_quiz_questions)) {
        $question_count = 1;
        ?>
        <form method="POST" action="<?php echo esc_url(get_permalink()); ?>" enctype="multipart/form-data" class="<?php echo $status['box_class'].' '.$addclass;?>">

            <ol id="sensei-quiz-list">
                <?php foreach ($lesson_quiz_questions as $question_item) {
                    // Setup current Frontend Question
                    $woothemes_sensei->quiz->data->question_item = $question_item;
                    $woothemes_sensei->quiz->data->question_count = $question_count;

                    // Question Type
                    $question_type = $woothemes_sensei->question->get_question_type($question_item->ID);

                    echo '<input type="hidden" name="questions_asked[]" value="' . $question_item->ID . '" />';

                    do_action('sensei_quiz_question_type', $question_type);

                    $question_count++;
} // End For Loop ?>

            </ol>
            <?php do_action('sensei_quiz_action_buttons'); ?>
        </form>
    <?php                                                                                                             } else { ?>
        <div class="sensei-message alert"><?php _e('There are no questions for this Quiz yet. Check back soon.', 'woothemes-sensei'); ?></div>
    <?php } // End If Statement ?>
</div>

<?php
}
do_action('sensei_quiz_back_link', $quiz_lesson); ?>
