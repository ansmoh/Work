<?php
/**
 * The Template for displaying the learner profile page data.
 *
 * Override this template by copying it to yourtheme/sensei/learner-profile/learner-info.php
 *
 * @author      WooThemes
 * @package     Sensei/Templates
 * @version     1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

global $woothemes_sensei, $post, $current_user, $wp_query, $learner_user, $course, $my_courses_page, $my_courses_section,$wpdb;

// Get User Meta
get_currentuserinfo();

do_action('sensei_complete_course');
?>
        <article class="post" id="post-wrap">
        <section id="learner-info" class="learner-info entry fix">
        <div class="dashboard dashtitle" id="'<?php echo $current_user->user_login;?>'">Dashboard</div>
            <?php
    // do_action( 'sensei_frontend_messages' );

//			do_action( 'sensei_learner_profile_info', $learner_user );
            /*** WisdmLabs Changes Start ***/
            do_action('wdm_sensei_frontend_messages');
            /*** WisdmLabs Changes End ***/
            if (isset($woothemes_sensei->settings->settings[ 'learner_profile_show_courses' ]) && $woothemes_sensei->settings->settings[ 'learner_profile_show_courses' ]) {
                $manage = ( $learner_user->ID == $current_user->ID ) ? true : false;

                echo Sensei()->course->load_user_content($current_user, $manage);

                //do_action( 'sensei_before_learner_course_content', $learner_user );

                // echo Sensei()->course->load_user_courses_content( $learner_user, $manage );

                //do_action( 'sensei_after_learner_course_content', $learner_user );
            }

            ?>
        </section>
    </article>
