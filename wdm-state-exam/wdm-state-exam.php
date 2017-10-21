<?php
/** Plugin Name:      Wdm State Exam
 * Plugin URI:        https://wisdmlabs.com
 * Description:       Give option for prepare state exams
 * Version:           1.0.0
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 * Text Domain:       wdm-state-exam
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

include_once(plugin_dir_path(__FILE__) .'public/wdm-state-exam-public.php');
new WdmStateExamPublic();

include_once(plugin_dir_path(__FILE__).'admin/wdm-state-exam-templater.php');
add_action('plugins_loaded', array( 'WdmStateExamTemplater', 'get_instance' ));

include('includes/class-state-exam-install.php');
//Install Tables associated with User Specific Pricing plugin
register_activation_hook(__FILE__, array('ClassStateExamInstall', 'create_table_for_state_exam'));
