<?php
/**
 * The template for displaying product content in the single-lessons.php template
 *
 * Override this template by copying it to yourtheme/sensei/content-single-lesson.php
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
 global $woothemes_sensei, $post, $current_user, $view_lesson, $user_taking_course;
 // Content Access Permissions
session_start();
 $access_permission = false;
 if ( ( isset( $woothemes_sensei->settings->settings['access_permission'] ) && ! $woothemes_sensei->settings->settings['access_permission'] ) || sensei_all_access() ) {
 	if(WooThemes_Sensei_Utils::sensei_is_woocommerce_activated()) {
    $course_id = get_post_meta( $post->ID, '_lesson_course', true );
    $wc_post_id = get_post_meta( $course_id, '_course_woocommerce_product', true );
    $product = $woothemes_sensei->sensei_get_woocommerce_product_object( $wc_post_id );

    $access_permission = ! ( isset ( $product ) && is_object ( $product ) );
  }
 } // End If Statement
  $user_id = $current_user->ID;
  $key = 'billing_myfield7';
  $single = true;
$user_dl = get_user_meta( $user_id, $key, $single );
$term_list = wp_get_post_terms($post->ID, 'lesson-tag', array("fields" => "all"));
foreach($term_list as $term_single) {
if($term_single->slug=='final-exam' || $term_single->slug=='final-exam-2'){
	if(($_POST['verifydl'])=='Verify'){
	$_session['DL'] = $_POST['dlid'];
	}
if(($user_dl!=$_session['DL'])){
	?>

	<div class="openpopup"><div class="popinner">
<?php if($_POST['verifydl']=='Verify'){ if(($user_dl!=$_SESSION['DL'])){echo '<span class="ErroR">Driving licence varification failed. Try again!<br />';} }?>
<form id="varificationForm" name="varification" action="<?php $_SERVER['PHP_SELF'];?>" method="post"><span style="color:#fff; float:left;"><b>Identification: </b>You must enter a current Driver's License or ID Number as a means of identification prior to accessing the final examination. By entering your Driver's License or ID Number you are affirming, under penalty of perjury, that you are the student registered for this course and that you personally will complete the final examination. Any violation of this section will result in revocation of the completion certificate for this course and will be reported to the California Bureau of Real Estate.<br /><b>In order to access the final exam, please insert your driver's license number.</b></span><br><input type="text" value="<?php echo $_session['DL'];?>" name="dlid" id="dlid"><input type="submit" value="Verify" name="verifydl" id="verifydl"><input type="button" value="Back" class="backbutton" /></form>
</div></div>
	<?php }
}
}
 ?>

     <article <?php post_class( array( 'lesson', 'post' ) ); ?>>

				<?php do_action( 'sensei_lesson_image', $post->ID ); ?>
                <?php if ( count($term_list)>0 ) { ?> <div class="dashboard">Take Course Final Exam</div><?php }else{ ?><div class="dashboard">Lesson: <?php the_title();?></div><?php }?>

                <div class="private-message"><?php  do_action('sensei_lesson_tutor'); ?></div><?php
                $view_lesson = true;

                wp_get_current_user();

                $lesson_prerequisite = absint( get_post_meta( $post->ID, '_lesson_prerequisite', true ) );

		if ( $lesson_prerequisite > 0 ) {
					// Check for prerequisite lesson completions
					$view_lesson = WooThemes_Sensei_Utils::user_completed_lesson( $lesson_prerequisite, $current_user->ID );
				}

				$lesson_course_id = get_post_meta( $post->ID, '_lesson_course', true );
				$user_taking_course = WooThemes_Sensei_Utils::user_started_course( $lesson_course_id, $current_user->ID );

				if( current_user_can( 'administrator' ) ) {
					$view_lesson = true;
					$user_taking_course = true;
				}

				$is_preview = false;
				if( WooThemes_Sensei_Utils::is_preview_lesson( $post->ID ) ) {
					$is_preview = true;
					$view_lesson = true;
				};

				 ?>
<?php if ( count($term_list)>0 ) { ?>
					<section class="entry fix single-content">

				<?php if( $view_lesson ) { if ( $is_preview && !$user_taking_course ) { ?>
						<div class="sensei-message test alert"><?php echo $woothemes_sensei->permissions_message['message']; ?></div>
					<?php } ?>
<div class="fusion-one-half fusion-layout-column fusion-spacing-yes"><div class="fusion-column-wrapper">
	<h2 class="head" style="line-height: 25px;padding-bottom: 5px!important;">
		Review Text Book
		<span class="instxt" style="color:red;font-size: 12px;display: block;line-height: 16px;">(Double click on image to open full screen)</span>
	</h2>
<?php if ( $access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) { the_content(); } else { echo $post->post_excerpt;}?></div></div><div class="fusion-one-half fusion-layout-column fusion-column-last fusion-spacing-yes">

                         <div class="fusion-column-wrapper">
                      <?php if ( $access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) {
						  echo '<h2 class="head">Final Exam Instructions</h2>
						  <ul class="exmins">
<b>Taking the Exam</b><br>
To start your exam please click "Take Exam" button below to open the exam. When the exam opens, your time begins automatically. <b>Do not click the final exam button until you are ready to begin. If at any point you need to leave the exam page and want to save your answers before submitting the exam, please click "save exam answers" at the top right of the page.</b> You will NOT be able to save the exam timer. Exam will automatically submit after time expires. <b>Exams must be completed in one session.</b> When you finish, click the SUBMIT button at the end of the exam. Please allow for a few seconds then the system will display your score. <br>
<b>Number of Questions</b><br>

The Final Exam is comprised of 100 multiple-choice questions.<br>

<b>Time Limit</b><br>

The exam has a 2 ½ hour time limit. Any questions that are not completed within the allotted time limit will automatically be submitted as incorrect.

<br><b>Results</b><br>

Immediately after you submit the exam, you will receive your results. You must receive a score of 60% or better to pass the course final exam.
In order to protect the integrity of the test bank, students will not be provided access to the correct questions and answers of the course exam.


<br><b>Retake Policy</b><br>';
if ($term_single->slug=='final-exam'){
echo 'If you should fail this course final examination, you will have 1 more attempt to retake the exam. If you fail the second attempt of the Final Exam, you must retake all quizzes and wait 18 days to take the Final Exam again.';
}
if ($term_single->slug=='final-exam-2'){
echo 'If you should fail the course final examination, you must retake all quizzes and wait 18 days to retake the Final Exam.';
}
						  echo' You will be provided with your score, but will be unable to access any questions or answers to protect the integrity of the test bank. All subsequent examinations will have a different order of questions and answers.
</ul>';
		do_action( 'sensei_lesson_single_meta' );

					} else {
						do_action( 'sensei_lesson_course_signup', $lesson_course_id );
					} ?>
</div></div>
					<?php
				}  else {
										if ( $lesson_prerequisite > 0 ) {
											echo '<div class="fusion-column-wrapper">';
						echo sprintf( __( 'You must first complete %1$s before viewing this Lesson', 'woothemes-sensei' ), '<a href="' . esc_url( get_permalink( $lesson_prerequisite ) ) . '" title="' . esc_attr(  sprintf( __( 'You must first complete: %1$s', 'woothemes-sensei' ), get_the_title( $lesson_prerequisite ) ) ) . '">' . get_the_title( $lesson_prerequisite ). '</a>' );
						echo '</div>';
					}
				} ?>


					</section>

					<?php }else{?>
                    		<section class="entry fix single-content">

				<?php if( $view_lesson ) { if ( $is_preview && !$user_taking_course ) { ?>
						<div class="sensei-message test alert"><?php echo $woothemes_sensei->permissions_message['message']; ?></div>
					<?php } ?>

<div class="fusion-one-half fusion-layout-column fusion-spacing-yes">
	<div class="fusion-column-wrapper">
		<h2 class="head" style="line-height: 25px;padding-bottom: 5px!important;">
			Online Text Book
			<span class="instxt" style="color:red;font-size: 12px;display: block;line-height: 16px;">(Double click on image to open full screen)</span>
		</h2>
		<?php
		if ( $access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) {
			the_content();
		} else { 
			echo $post->post_excerpt;}
		?>
	</div>

	<div class="fusion-column-wrapper">
  <?php
	if ( $access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) {
		echo '
			<h2 class="head">Required Lesson Quiz</h2>
		';

		do_action( 'sensei_lesson_single_meta' );
	} else {
		do_action( 'sensei_lesson_course_signup', $lesson_course_id );
	}
	?>
	</div>
</div>

<div class="fusion-one-half fusion-layout-column fusion-column-last fusion-spacing-yes"><div class="fusion-column-wrapper video-content-box"><h2 class="head">Supplemental Lesson Videos</h2>
           <div class="videos-wrap">
		    <?php
           $rows = get_field('video_embed_code');
		$total_video = count($rows);
		if ( $access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) {
		if( have_rows('video_embed_code') ){
		 		   	foreach($rows as $row)
	{

				echo '<div class="video"><div id="player"><iframe width="100%" src="https://www.youtube.com/embed/'.$row['add_video_code'].'?rel=0" frameborder="0" allowfullscreen></iframe></div><a href="https://www.youtube.com/embed/'.$row['add_video_code'].'?rel=0" id="playbtn" rel="prettyPhoto"></a></div>';
					}
						}
		 else{
			 echo '<div class="sensei-message info">No Videos!</div>';
		 }
		}
		   ?>
                      </div></div>
                      <?php /*** WisdmLabs Changes Start ***/
$lesson_video_product = get_post_meta($post->ID,'_lesson_video_embed',true);
			if(isset($lesson_video_product ) && !empty($lesson_video_product )){
 ?>
                      <div class="fusion-column-wrapper">
<?php if ($access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) {
    echo '<h2 class="head">Supplemental Lesson Video</h2>';
    do_action('sensei_lesson_video', $post->ID);
}
echo '</div>';
}
/*** WisdmLabs Changes End ***/
?>


                <?php $rows = get_field('powerpoint_presentation_slide');
		  $total_ppt = count($rows);
		  if($total_ppt<=1){
          echo '<div class="fusion-column-wrapper"><h2 class="head">Supplemental Lesson Presentation</h2>';
		  }else{
			  echo '<div class="fusion-column-wrapper"><h2 class="head">Supplemental Lesson Presentations</h2>';
			  }
		  if ( $access_permission || ( is_user_logged_in() && $user_taking_course ) || $is_preview ) {
		  if( have_rows('powerpoint_presentation_slide') ){
		   	foreach($rows as $row)
	{
						echo '<div class="pp-wrap"><div class="ppt-width"><a href="'.$row['add_slide_url'].'" rel="prettyPhoto"></a><iframe src="'.$row['add_slide_url'].'" width="100%"></iframe></div><div class="pp-title"><a href="'.$row['add_slide_url'].'" rel="prettyPhoto">'.$row['presentation_heading'].'</a><div class="popout"><a href="'.$row['add_slide_url'].'" rel="prettyPhoto">View</a></div></div></div>';
	}
	  }
	else{
		echo '<div class="sensei-message info">No Presentations!</div>';
		}
		  }
	?>
	</div>
					<?php
				}  else {
										if ( $lesson_prerequisite > 0 ) {
											echo '<div class="fusion-column-wrapper">';
						echo sprintf( __( 'You must first complete %1$s before viewing this Lesson', 'woothemes-sensei' ), '<a href="' . esc_url( get_permalink( $lesson_prerequisite ) ) . '" title="' . esc_attr(  sprintf( __( 'You must first complete: %1$s', 'woothemes-sensei' ), get_the_title( $lesson_prerequisite ) ) ) . '">' . get_the_title( $lesson_prerequisite ). '</a>' );
						echo '</div>';
					}
				} ?>


					</section>
                    <?php }?>
            </article><!-- .post -->

            <?php

			 do_action('sensei_pagination'); ?>
