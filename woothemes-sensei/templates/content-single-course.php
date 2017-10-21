<?php
/**
 * The template for displaying product content in the single-course.php template
 *
 * Override this template by copying it to yourtheme/sensei/content-single-course.php
 *
 * @author 		WooThemes
 * @package 	Sensei/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $woothemes_sensei, $post, $current_user;

// Get User Meta
get_currentuserinfo();
// Check if the user is taking the course
$is_user_taking_course = WooThemes_Sensei_Utils::user_started_course( $post->ID, $current_user->ID );

// Content Access Permissions
$access_permission = false;
if ( ( isset( $woothemes_sensei->settings->settings['access_permission'] ) && ! $woothemes_sensei->settings->settings['access_permission'] ) || sensei_all_access() ) {
	$access_permission = true;
} // End If Statement
if( !is_user_logged_in()){
	echo '<script type="text/javascript">
           window.location = "'.get_home_url().'"
      </script>';	
} 
?>
  <div class="dashboard">Course</div> 
	<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked woocommerce_show_messages - 10
	 */
	if ( WooThemes_Sensei_Utils::sensei_is_woocommerce_activated() ) {
		do_action( 'woocommerce_before_single_product' );
	} // End If Statement

		/*=============== Filter Final Exams Lessons ==================*/
		if (function_exists('filter_final_exams')){
		    $course_lessons = filter_final_exams();
		}
		/*=============== Filter Final Exams Lessons ==================*/
		
									if(WooThemes_Sensei_Utils::sensei_is_woocommerce_activated()) {
										$wc_post_id = get_post_meta( $post->ID, '_course_woocommerce_product', true );
										$product = $woothemes_sensei->sensei_get_woocommerce_product_object( $wc_post_id );
										$is_product = isset ( $product ) && is_object ( $product );
									} else {
										$is_product = true;
									}
									$orders = get_posts( array(
										'posts_per_page' => -1,
										'meta_key'    => '_customer_user',
										'meta_value'  => intval( $current_user->ID ),
										'post_type'   => 'shop_order',
										'post_status' =>  array( 'wc-pending','wc-processing', 'wc-completed' ),
									) );

									foreach ( $orders as $order_id ) {
										$order = new WC_Order($order_id->ID);
										if ( 0 < sizeof( $order->get_items() ) ) {
											foreach ($order->get_items() as $item) {
												if($product_id == $wc_post_id && ($order->post_status == 'wc-pending') || ($order->post_status == 'wc-processing')){
													echo '<div id="pendingNotification"><div class="sensei-message info pendingPayment">Please complete your pending payment before starting the course. <a href="/my-account/">Click Here</a> to pay.</div></div>';
													}
											}
										}
									}
									
									?>
			<article <?php post_class( array( 'course', 'post' ) ); ?>>				
				<section class="entry fix">
          <?php if ( ( is_user_logged_in() && $is_user_taking_course ) || ($access_permission && !$is_product) || 'excerpt' == $woothemes_sensei->settings->settings[ 'course_single_content_display' ] ) { echo '<img src="'.wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'" class="alignleft leftbox">';?>
					<div class="infobox"><h2><?php the_title()?><div class="courselink"><a href="/my-courses/" title="View All Courses">View All Courses</a></div>
					<?php do_action('sensei_lesson_tutor'); ?>
     </h2><p class="course-excerpt"><?php echo $post->post_excerpt ?></p><?php do_action('sensei_course_meter');?></div>
     <?php } else{ echo '<p class="course-excerpt">' . $post->post_excerpt . '</p>'; }
	 
	 ?>
                </section>
				
                <?php do_action( 'sensei_course_single_lessons' ); ?>

            </article><!-- .post -->

	        <?php 
					//	do_action('sensei_pagination'); ?>