<?php
/**
 * Learner graded quiz email
 *
 * @author WooThemes
 * @package Sensei/Templates/Emails/HTML
 * @version 1.6.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php

// Get data for email content
global $sensei_email_data;
extract( $sensei_email_data );
$term_list = wp_get_post_terms($lesson_id, 'lesson-tag', array("fields" => "all", "tag" => "final-exam"));
// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline. !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
$small = "text-align: center !important;";

$large = "text-align: center !important;font-size: 24px !important;line-height: 28px !important;";

?>

<?php do_action( 'sensei_before_email_content', $template ); ?>

<p style="<?php echo esc_attr( $small ); ?>"><?php printf( __( 'You %1$s the lesson', 'woothemes-sensei' ), $passed ); ?></p>

<h2 style="<?php echo esc_attr( $large ); ?>"><?php echo get_the_title( $lesson_id ); ?></h2>

<?php /*?><p style="<?php echo esc_attr( $small ); ?>"><?php _e( 'with a grade of', 'woothemes-sensei' ); ?></p>

<h2 style="<?php echo esc_attr( $large ); ?>"><?php echo $grade . '%'; ?></h2>

<p style="<?php echo esc_attr( $small ); ?>"><?php printf( __( 'The pass mark is %1$s', 'woothemes-sensei' ), $passmark . '%' ); ?></p><?php */?>

<p style="<?php echo esc_attr( $small ); ?>"><?php if(count($term_list)>0){ echo "We are unable to provide the answers to course final exams. It is a requirement from the California Bureau of Real Estate to protect the integrity of the test bank."; } else{ printf( __( 'You can review your grade and your answers %1$shere%2$s.', 'woothemes-sensei' ), '<a href="' . get_permalink( $quiz_id ) . '">', '</a>' ); }?></p>

<?php do_action( 'sensei_after_email_content', $template ); ?>