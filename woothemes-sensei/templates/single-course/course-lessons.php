<?php
/**
 * The Template for displaying all single course meta information.
 *
 * Override this template by copying it to yourtheme/sensei/single-course/course-lessons.php
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $post, $woothemes_sensei, $current_user , $wpdb, $course, $active_courses,$progress_percentage;
$html = '';

// Get Course Lessons
$course_lessons = Sensei()->course->course_lessons( $post->ID );
$total_lessons = count( $course_lessons );

/*================ Hide Final Exam 2 ==============*/
if (function_exists('hide_final_exam_2')){
    $course_lessons = hide_final_exam_2($course_lessons);
}
/*================ Hide Final Exam 2 ==============*/

// Check if the user is taking the course
$is_user_taking_course = WooThemes_Sensei_Utils::user_started_course( $post->ID, $current_user->ID );

// Get User Meta
get_currentuserinfo();

// exit if no lessons exist
if (  ! ( $total_lessons  > 0 ) ) {
    return;
}
												
$html .= '<section class="completed course-lessons">';
$html .= '<header>';
$html .= '<h2 class="head">Completed Lessons</h2>';
$html .= '</header>';
$html1 .= '<section class="upcoming course-lessons">';
$html1 .= '<header>';
$html1 .= '<h2 class="head">Upcoming Lessons</h2>';
$html1 .= '</header>';
$html2 .= '<section class="current course-lessons">';
$html2 .= '<header>';
$html2 .= '<h2 class="head">Current Lesson</h2>';
$html2 .= '</header>';
$lesson_count = 1;

$lessons_completed = count( Sensei()->course->get_completed_lesson_ids( $post->ID, $current_user->ID ));
$show_lesson_numbers = false;
$i = $j =0;

foreach ( $course_lessons as $lesson_item ){
    $i++;
   // if there are 15 $item[number] in this foreach, I want get the value : 15
  if( false != Sensei()->modules->get_lesson_module( $lesson_item->ID ) ){
        continue;
    }

    $single_lesson_complete = false;
    $post_classes = array( 'course', 'post' );
    $user_lesson_status = false;
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
$title = get_the_title();
$last_seen = date("Y-F-d h:i:sa");
$table_name = $wpdb->prefix . "custom_table";
$rows = get_field('video_embed_code');
 $total_video = count($rows);
 $rows = get_field('powerpoint_presentation_slide');
		  $total_ppt = count($rows);		
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name where user_id=".$current_user->ID." && post_id=".$post->ID."");
if($user_count>0){
$wpdb->update( $table_name,array( 'user_id' => $current_user->ID, 'post_id' => $post->ID , 'post_title' => $title,'complete_lesson' =>$j,'total_lesson' =>$i, 'course_percentage' =>$progress_percentage, 'last_seen' => $last_seen), array('post_id' => $post->ID,'user_id' => $current_user->ID)); 
	}
else
{
$wpdb->insert( $table_name, array( 'user_id' => $current_user->ID, 'post_id' => $post->ID , 'post_title' => $title,'complete_lesson' =>$j,'total_lesson' =>$i, 'course_percentage' =>$progress_percentage, 'last_seen' => $last_seen));
	}
$count_lesson = 1;

foreach ( $course_lessons as $lesson_item ){
    //skip lesson that are already in the modules
    if( false != Sensei()->modules->get_lesson_module( $lesson_item->ID ) ){
        continue;
    }

    $single_lesson_complete = false;
    $post_classes = array( 'course', 'post' );
    $user_lesson_status = false;
    if ( is_user_logged_in() ) {
        // Check if Lesson is complete
        $single_lesson_complete = WooThemes_Sensei_Utils::user_completed_lesson( $lesson_item->ID, $current_user->ID );
        if ( $single_lesson_complete ) {
            $post_classes[] = 'lesson-completed';
		        } // End If Statement
    } // End If Statement
    // Get Lesson data
  $complexity_array = $woothemes_sensei->post_types->lesson->lesson_complexities();
 $lesson_length = get_post_meta( $lesson_item->ID, '_lesson_length', true );
   $lesson_complexity = get_post_meta( $lesson_item->ID, '_lesson_complexity', true );
    if ( '' != $lesson_complexity ) { $lesson_complexity = $complexity_array[$lesson_complexity]; }
    $user_info = get_userdata( absint( $lesson_item->post_author ) );
    $is_preview = WooThemes_Sensei_Utils::is_preview_lesson( $lesson_item->ID );
    $preview_label = '';
    if ( $is_preview && !$is_user_taking_course ) {
        $preview_label = $woothemes_sensei->frontend->sensei_lesson_preview_title_text( $post->ID );
        $preview_label = '<span class="preview-heading">' . $preview_label . '</span>';
        $post_classes[] = 'lesson-preview';
    }
 if ( $single_lesson_complete ) {
    $html .= '<article class="' . esc_attr( join( ' ', get_post_class( $post_classes, $lesson_item->ID ) ) ) . '">';

        $html .= '<header class="comp">';

            $html .= '<h2><a href="' . esc_url( get_permalink( $lesson_item->ID ) ) . '" title="' . esc_attr( sprintf( __( 'Start %s', 'woothemes-sensei' ), $lesson_item->post_title ) ) . '">';

            if( apply_filters( 'sensei_show_lesson_numbers', $show_lesson_numbers ) ) {
                $html .= '<span class="lesson-number">' . $lesson_count . '. </span>';
            }

            $html .= esc_html( sprintf( __( '%s', 'woothemes-sensei' ), $lesson_item->post_title ) ) . $preview_label . '</a></h2>';

            $html .= '<p class="lesson-meta">';

                if ( '' != $lesson_length ) { $html .= '<span class="lesson-length">' . apply_filters( 'sensei_length_text', __( 'Length: ', 'woothemes-sensei' ) ) . $lesson_length . __( ' minutes', 'woothemes-sensei' ) . '</span>'; }
              /*  if ( isset( $woothemes_sensei->settings->settings[ 'lesson_author' ] ) && ( $woothemes_sensei->settings->settings[ 'lesson_author' ] ) ) {
                    $html .= '<span class="lesson-author">' . apply_filters( 'sensei_author_text', __( 'Author: ', 'woothemes-sensei' ) ) . '<a href="' . get_author_posts_url( absint( $lesson_item->post_author ) ) . '" title="' . esc_attr( $user_info->display_name ) . '">' . esc_html( $user_info->display_name ) . '</a></span>';
                } // End If Statement
				
				*/
                if ( '' != $lesson_complexity ) { $html .= '<span class="lesson-complexity">' . apply_filters( 'sensei_complexity_text', __( 'Complexity: ', 'woothemes-sensei' ) ) . '<a>'.$lesson_complexity .'</a></span>'; }

                if ( $single_lesson_complete ) {
                    $html .= '<span class="lesson-status complete">' . apply_filters( 'sensei_complete_text', __( 'Complete', 'woothemes-sensei' ) ) .'</span>';
                }
                elseif ( $user_lesson_status ) {
                    $html .= '<span class="lesson-status in-progress">' . apply_filters( 'sensei_in_progress_text', __( 'In Progress', 'woothemes-sensei' ) ) .'</span>';
                } // End If Statement

            $html .= '</p>';

        $html .= '</header>';

        // Image
        $html .=  $woothemes_sensei->post_types->lesson->lesson_image( $lesson_item->ID );

        $html .= '<section class="entry innersec">';

            $html .= WooThemes_Sensei_Lesson::lesson_excerpt( $lesson_item );

        $html .= '</section><span class="bdr"><img src="/wp-content/uploads/2016/01/bdr.jpg"></span>';

    $html .= '</article>';
 }else{
	 if($count_lesson == 1){
		  $html2 .= '<article class="' . esc_attr( join( ' ', get_post_class( $post_classes, $lesson_item->ID ) ) ) . '">';

        $html2 .= '<header class="notcomp">';

            $html2 .= '<h2><a href="' . esc_url( get_permalink( $lesson_item->ID ) ) . '" title="' . esc_attr( sprintf( __( 'Start %s', 'woothemes-sensei' ), $lesson_item->post_title ) ) . '">';

            if( apply_filters( 'sensei_show_lesson_numbers', $show_lesson_numbers ) ) {
                $html2 .= '<span class="lesson-number">' . $lesson_count . '. </span>';
            }

            $html2 .= esc_html( sprintf( __( '%s', 'woothemes-sensei' ), $lesson_item->post_title ) ) . $preview_label . '</a></h2>';

            $html2 .= '<p class="lesson-meta">';

                if ( '' != $lesson_length ) { $html2 .= '<span class="lesson-length">' . apply_filters( 'sensei_length_text', __( 'Length: ', 'woothemes-sensei' ) ) . $lesson_length . __( ' minutes', 'woothemes-sensei' ) . '</span>'; }
             
                if ( '' != $lesson_complexity ) { $html2 .= '<span class="lesson-complexity">' . apply_filters( 'sensei_complexity_text', __( 'Complexity: ', 'woothemes-sensei' ) ) .'<a>'. $lesson_complexity .'</a></span>'; }

                if ( $single_lesson_complete ) {
                    $html2 .= '<span class="lesson-status complete">' . apply_filters( 'sensei_complete_text', __( 'Complete', 'woothemes-sensei' ) ) .'</span>';
                }
                elseif ( $user_lesson_status ) {
                    $html2 .= '<span class="lesson-status in-progress">' . apply_filters( 'sensei_in_progress_text', __( 'In Progress', 'woothemes-sensei' ) ) .'</span>';
                } // End If Statement

            $html2 .= '</p>';

        $html2 .= '</header>';

        // Image
        $html2 .=  $woothemes_sensei->post_types->lesson->lesson_image( $lesson_item->ID );

        $html2 .= '<section class="entry innersec">';

            $html2 .= WooThemes_Sensei_Lesson::lesson_excerpt( $lesson_item );

        $html2 .= '</section><span class="bdr"></span>';

    $html2 .= '</article>';
		 }else{
	  $html1 .= '<article class="' . esc_attr( join( ' ', get_post_class( $post_classes, $lesson_item->ID ) ) ) . '">';

        $html1 .= '<header class="notcomp">';

            $html1 .= '<h2><a href="' . esc_url( get_permalink( $lesson_item->ID ) ) . '" title="' . esc_attr( sprintf( __( 'Start %s', 'woothemes-sensei' ), $lesson_item->post_title ) ) . '">';

            if( apply_filters( 'sensei_show_lesson_numbers', $show_lesson_numbers ) ) {
                $html1 .= '<span class="lesson-number">' . $lesson_count . '. </span>';
            }

            $html1 .= esc_html( sprintf( __( '%s', 'woothemes-sensei' ), $lesson_item->post_title ) ) . $preview_label . '</a></h2>';

            $html1 .= '<p class="lesson-meta">';

                if ( '' != $lesson_length ) { $html1 .= '<span class="lesson-length">' . apply_filters( 'sensei_length_text', __( 'Length: ', 'woothemes-sensei' ) ) . $lesson_length . __( ' minutes', 'woothemes-sensei' ) . '</span>'; }
              /*  if ( isset( $woothemes_sensei->settings->settings[ 'lesson_author' ] ) && ( $woothemes_sensei->settings->settings[ 'lesson_author' ] ) ) {
                    $html1 .= '<span class="lesson-author">' . apply_filters( 'sensei_author_text', __( 'Author: ', 'woothemes-sensei' ) ) . '<a href="' . get_author_posts_url( absint( $lesson_item->post_author ) ) . '" title="' . esc_attr( $user_info->display_name ) . '">' . esc_html( $user_info->display_name ) . '</a></span>';
                } // End If Statement
				
				*/
                if ( '' != $lesson_complexity ) { $html1 .= '<span class="lesson-complexity">' . apply_filters( 'sensei_complexity_text', __( 'Complexity: ', 'woothemes-sensei' ) ) .'<a>'. $lesson_complexity .'</a></span>'; }

                if ( $single_lesson_complete ) {
                    $html1 .= '<span class="lesson-status complete">' . apply_filters( 'sensei_complete_text', __( 'Complete', 'woothemes-sensei' ) ) .'</span>';
                }
                elseif ( $user_lesson_status ) {
                    $html1 .= '<span class="lesson-status in-progress">' . apply_filters( 'sensei_in_progress_text', __( 'In Progress', 'woothemes-sensei' ) ) .'</span>';
                } // End If Statement

            $html1 .= '</p>';

        $html1 .= '</header>';

        // Image
        $html1 .=  $woothemes_sensei->post_types->lesson->lesson_image( $lesson_item->ID );

        $html1 .= '<section class="entry innersec">';

            $html1 .= WooThemes_Sensei_Lesson::lesson_excerpt( $lesson_item );

        $html1 .= '</section><span class="bdr"><img src="/wp-content/uploads/2016/01/bdr.jpg"></span>';

    $html1 .= '</article>';
	 }
	 $count_lesson++;
 }
    $lesson_count++;

} // End For Loop

$html2 .= '</section>';
$html .= '</section>';
$html1 .= '</section>';


// Output the HTML
echo $html2.$html.$html1;
