<?php
/**
 * The Template for displaying the my course page data.
 *
 * Override this template by copying it to yourtheme/sensei/user/my-courses.php
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $woothemes_sensei, $post, $current_user, $wp_query, $learner_user, $course, $my_courses_page, $my_courses_section,$wpdb;

// Get User Meta
get_currentuserinfo();

// Check if the user is logged in
if ( is_user_logged_in() ) {
	// Handle completion of a course
	do_action( 'sensei_complete_course' );
	?>
   <div class="sensei-container"><div id="sidebar"> <?php dynamic_sidebar('User Dashboard'); ?></div>
	<section id="main-course" class="course-container">
    <?php 	    do_action( 'sensei_learner_profile_info', $learner_user );
		        do_action( 'sensei_before_user_course_content', $current_user );
		?>
     <div class="dashboard my-courses"><?php the_title();?></div> 
		<?php

 	//do_action( 'sensei_frontend_messages' );
			echo $woothemes_sensei->course->all_courses_content( $current_user, true );

		do_action( 'sensei_after_user_course_content', $current_user );

		?>
	</section></div>
<?php } else {
echo '<script type="text/javascript">
           window.location = "'.get_home_url().'";
      </script>';
	} // End If Statement ?>