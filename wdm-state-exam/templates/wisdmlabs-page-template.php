<?php
// Template Name: WisdmLabs Page Template
?>
<html class="<?php echo ( ! Avada()->settings->get('smooth_scrolling') ) ? 'no-overflow-y' : ''; ?>" xmlns="http<?php echo (is_ssl())? 's' : ''; ?>://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <?php if (isset($_SERVER['HTTP_USER_AGENT']) && ( false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') )) : ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <?php endif; ?>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title><?php wp_title(''); ?></title>

    <?php $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad'); ?>

    <?php
    $viewport = '';
    if (Avada()->settings->get('responsive') && $isiPad && ! Avada()->settings->get('ipad_potrait')) {
        $viewport = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
    } else if (Avada()->settings->get('responsive')) {
        if (Avada()->settings->get('mobile_zoom')) {
            $viewport .= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
        } else {
            $viewport .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
        }
    }

    if ($isiPad && Avada()->settings->get('ipad_potrait')) {
        $viewport = '';
    }

    echo $viewport;
    ?>

    <?php if (Avada()->settings->get('favicon')) : ?>
        <link rel="shortcut icon" href="<?php echo Avada()->settings->get('favicon'); ?>" type="image/x-icon" />
    <?php endif; ?>

    <?php if (Avada()->settings->get('iphone_icon')) : ?>
        <!-- For iPhone -->
        <link rel="apple-touch-icon-precomposed" href="<?php echo Avada()->settings->get('iphone_icon'); ?>">
    <?php endif; ?>

    <?php if (Avada()->settings->get('iphone_icon_retina')) : ?>
        <!-- For iPhone 4 Retina display -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo Avada()->settings->get('iphone_icon_retina'); ?>">
    <?php endif; ?>

    <?php if (Avada()->settings->get('ipad_icon')) : ?>
        <!-- For iPad -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo Avada()->settings->get('ipad_icon'); ?>">
    <?php endif; ?>

    <?php if (Avada()->settings->get('ipad_icon_retina')) : ?>
        <!-- For iPad Retina display -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo Avada()->settings->get('ipad_icon_retina'); ?>">
    <?php endif; ?>

    <?php wp_head(); ?>

    <?php

    /** Include style  */

    //wp_enqueue_style('theme-css', get_stylesheet_directory_uri().'/style.css');


    $object_id = get_queried_object_id();
    $c_pageID  = Avada::c_pageID();
    ?>

    <script type="text/javascript">
        var $is_ie_10 = eval("/*@cc_on!@*/false") && document.documentMode === 10;
        if ( $is_ie_10 ) {
            document.write('<style type="text/css">.fusion-imageframe,.imageframe-align-center{font-size: 0px; line-height: normal;}.fusion-button.button-pill,.fusion-button.button-pill:hover{filter: none;}.fusion-header-shadow:after, body.side-header-left .header-shadow#side-header:before, body.side-header-right .header-shadow#side-header:before{ display: none }.search input,.searchform input {padding-left:10px;} .avada-select-parent .select-arrow,.select-arrow{height:33px;<?php if (Avada()->settings->get('form_bg_color')) :
?>background-color:<?php echo Avada()->settings->get('form_bg_color'); ?>;<?php
endif; ?>}.search input{padding-left:5px;}header .tagline{margin-top:3px;}.star-rating span:before {letter-spacing: 0;}.avada-select-parent .select-arrow,.gravity-select-parent .select-arrow,.wpcf7-select-parent .select-arrow,.select-arrow{background: #fff;}.star-rating{width: 5.2em;}.star-rating span:before {letter-spacing: 0.1em;}</style>');
        }

        var doc = document.documentElement;
        doc.setAttribute('data-useragent', navigator.userAgent);
    </script>

    <?php echo Avada()->settings->get('google_analytics'); ?>

    <?php echo Avada()->settings->get('space_head'); ?>
</head>
<?php
$wrapper_class = '';


if (is_page_template('blank.php')) {
    $wrapper_class  = 'wrapper_blank';
}

if ('modern' == Avada()->settings->get('mobile_menu_design')) {
    $mobile_logo_pos = strtolower(Avada()->settings->get('logo_alignment'));
    if ('center' == strtolower(Avada()->settings->get('logo_alignment'))) {
        $mobile_logo_pos = 'left';
    }
}

?>
<body <?php body_class(); ?> data-spy="scroll">
<div id="wrapper" class="<?php echo $wrapper_class; ?>">
<div id="main" class="clearfix">

        <div id="content" style="width:100%">
                <div class="sensei-container"><div id="sidebar">
    <?php dynamic_sidebar('User Dashboard'); ?>
    </div><div class="sensei-header"><?php
        do_action('sensei_learner_profile_info', $learner_user);
        do_action('sensei_before_user_course_content', $current_user);
        $category = $_GET['category'];
?>
<div class="dashboard my-courses"><?php
if (isset($category) && !empty($category)) {
    $cat_obj = get_term_by('slug', $category, 'question-category');
    echo $cat_obj->name;
} else {
    the_title();
}
?></div>
</div>

    <div id="content">
        <?php
    // TO SHOW THE PAGE CONTENTS
        if (is_user_logged_in()) {
            while (have_posts()) :
                the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
                    <div class="entry-content-page">
                        <?php the_content(); ?> <!-- Page Content -->
                    </div><!-- .entry-content-page -->

                <?php
            endwhile; //resetting the page loop
            wp_reset_query(); //resetting the page query
        } else {
            //wp_redirect(get_home_url());
            //exit();
            ?>
             <div class="entry-content-page">
             <p>Please login to view the page content</p> <!-- Page Content-->
                    </div>
            <?php
        }
    ?>
    </div></div>
                    </div>

            </div>  <!-- #main -->
            <?php get_footer(); ?>
             <!-- fusion-footer -->
            </div></body></html>


