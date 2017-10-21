<?php
/**
 * Create custom table on plugin installation for saving the preparation exam result
 */
if (!class_exists('ClassStateExamInstall')) {
    class ClassStateExamInstall
    {
        public static function create_table_for_state_exam()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'pre_state_exam_score';

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        userid bigint(20) NOT NULL,
        lessonid bigint(20) NOT NULL,
        completion_date varchar(50) NOT NULL,
        score varchar(50) NOT NULL,
        cat_score longtext NOT NULL,
        PRIMARY KEY  (id)
	) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}
