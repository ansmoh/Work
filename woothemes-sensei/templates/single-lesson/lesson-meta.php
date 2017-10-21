<?php
/**
 * The Template for displaying all single lesson meta data.
 *
 * Override this template by copying it to yourtheme/sensei/single-lesson/lesson-meta.php
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $post, $woothemes_sensei, $current_user , $wpdb, $course, $active_courses,$progress_percentage;
// Get the meta info
$rows = get_field('video_embed_code');
$total_video = count($rows);
$rows = get_field('powerpoint_presentation_slide');
$total_ppt = count($rows);
$lesson_course_id = absint( get_post_meta( $post->ID, '_lesson_course', true ) );
$course_title = get_the_title($lesson_course_id);
$lesson_title = get_the_title();
$last_seen = date("Y-F-d h:i:sa");
$table_name = $wpdb->prefix . "bp_current_lesson";
$table_quiz = $wpdb->prefix . "postmeta";
$sql_quiz = $wpdb->get_results("SELECT meta_value FROM $table_quiz WHERE post_id=".$post->ID." && meta_key='_lesson_quiz'");
$quiz_id = null;
 foreach ($sql_quiz as $row) {
$quiz_id = $row->meta_value;
 }
$count_quiz = $wpdb->get_results("SELECT DISTINCT(post_id) FROM $table_quiz WHERE `meta_key`='_quiz_id' AND `meta_value`=".$quiz_id."");
$total_quiz = count($count_quiz);
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name where user_id=".$current_user->ID." && lesson_id=".$post->ID."");
if($user_count>0){
$wpdb->update( $table_name,array( 'user_id' => $current_user->ID, 'course_id' => $lesson_course_id, 'course_title' => $course_title ,'lesson_id' => $post->ID, 'lesson_title' => $lesson_title, 'total_video' => $total_video, 'total_ppt' => $total_ppt,'total_quiz_qus' => $total_quiz, 'last_seen' => $last_seen), array('lesson_id' => $post->ID, 'user_id' => $current_user->ID)); 
	}
else
{
$wpdb->insert( $table_name, array( 'user_id' => $current_user->ID, 'course_id' => $lesson_course_id, 'course_title' => $course_title ,'lesson_id' => $post->ID, 'lesson_title' => $lesson_title,'total_video' => $total_video, 'total_ppt' => $total_ppt,'total_quiz_qus' => $total_quiz, 'last_seen' => $last_seen));
	}

$is_preview = WooThemes_Sensei_Utils::is_preview_lesson( $post->ID );
// Get User Meta
get_currentuserinfo();
// Complete Lesson Logic
do_action( 'sensei_complete_lesson' );
// Check that the course has been started
if ( $woothemes_sensei->access_settings() || WooThemes_Sensei_Utils::user_started_course( $lesson_course_id, $current_user->ID ) || $is_preview ) { ?>
	<section class="lesson-meta" id="lesson_complete">
		        <?php do_action( 'sensei_frontend_messages' ); ?>
        <?php if ( ! $is_preview || WooThemes_Sensei_Utils::user_started_course( $lesson_course_id, $current_user->ID ) ) {
        	do_action( 'sensei_lesson_quiz_meta', $post->ID, $current_user->ID  );
    	} ?>
    </section>
    <?php // do_action( 'sensei_lesson_back_link', $lesson_course_id ); ?>
<?php } else {
	 do_action( 'sensei_lesson_course_signup', $lesson_course_id );
} // End If Statement
//do_action( 'sensei_lesson_meta_extra', $post->ID );
// Get Course Lessons
$course_lessons = Sensei()->course->course_lessons( $lesson_course_id );
$total_lessons = count( $course_lessons );
$i = $j =0;
foreach ( $course_lessons as $lesson_item ) {
    $i++;
    if ( is_user_logged_in() ) {
        // Check if Lesson is complete
        $single_lesson_complete = WooThemes_Sensei_Utils::user_completed_lesson( $lesson_item->ID, $current_user->ID );
        if ( $single_lesson_complete ) {
            $post_classes[] = 'lesson-completed';
            $j++;
        } // End If Statement
    } // End If Statement
}
$progress_percentage = abs( round( ( doubleval( $j ) * 100 ) / ( $i ), 0 ) );
$table = $wpdb->prefix . "custom_table";
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table where user_id=".$current_user->ID." && post_id=".$lesson_course_id."");
if($user_count>0){
    $wpdb->update( $table,array( 'user_id' => $current_user->ID, 'post_id' => $lesson_course_id , 'post_title' => $course_title,'complete_lesson' =>$j,'total_lesson' =>$i, 'course_percentage' =>$progress_percentage, 'last_seen' => $last_seen), array('post_id' => $lesson_course_id,'user_id' => $current_user->ID));
}
else
{
    $wpdb->insert( $table, array( 'user_id' => $current_user->ID, 'post_id' => $lesson_course_id , 'post_title' => $course_title,'complete_lesson' =>$j,'total_lesson' =>$i, 'course_percentage' =>$progress_percentage, 'last_seen' => $last_seen));
}
?>