<?php
/** Plugin Name:      Wdm Sensei Quiz Timer
 * Plugin URI:        https://wisdmlabs.com
 * Description:       Display Timer on sensei quiz page.
 * Version:           1.0.0
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 * Text Domain:       wdm-sensei-quiz-timer
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if (in_array('woothemes-sensei/woothemes-sensei.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    include_once plugin_dir_path(__FILE__) . 'admin/wdm-sensei-quiz-timer-admin.php';
    new WdmSenseiQuizTimerAdmin();

    include_once plugin_dir_path(__FILE__) . 'public/wdm-sensei-quiz-timer-public.php';
    new WdmSenseiQuizTimerPublic();
} else {
        add_action('admin_notices', 'base_plugin_inactive_notice');
}

if (!function_exists('base_plugin_inactive_notice')) {
    /**
     * Display base plugin activate notification
     * @return [type] [description]
     */
    function base_plugin_inactive_notice()
    {
        if (current_user_can('activate_plugins')) {
            ?>
            <div id="message" class="error">
            <p><?php _e('Wdm Sensei Quiz Timer is inactive.Install and activate Sensei for Wdm Sensei Quiz Timer to work.', 'wdm-sensei-quiz-timer');
            ?></p>
            </div>
            <?php
        }
    }
}

