<?php

/**
 * Database Migration: 003
 */
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_projects}'") == $table_projects ) {
	$wpdb->query("ALTER TABLE `{$table_projects}`
		CHANGE `genres` `genre` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		CHANGE `options` `custom` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 
	");
}

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_releases}'") == $table_releases ) {
	$wpdb->query("ALTER TABLE `{$table_releases}`
		ADD `type` INT(12) NOT NULL AFTER `revision`,
		DROP INDEX `pid`,
		ADD INDEX `project_id` (`project_id`)
	");
	
	$wpdb->query("ALTER TABLE `{$table_releases}`
		MODIFY COLUMN `download_depositfiles` VARCHAR(255) NOT NULL AFTER `title`,
		MODIFY COLUMN `download_mediafire` VARCHAR(255) NOT NULL AFTER `download_filesonic`,
		MODIFY COLUMN `download_megaupload` VARCHAR(255) NOT NULL AFTER `download_mediafire`
	");
}
 
 if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_settings}'") != $table_settings ) {
	$structure = "CREATE TABLE `{$table_settings}` (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`option` VARCHAR(255) NOT NULL,
		`value` longtext NOT NULL,
		UNIQUE KEY `id` (`id`),
		FULLTEXT KEY `option` (`option`)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 AUTO_INCREMENT=0;";
	
	$wpdb->query($structure);
}

/* EOF: includes/migration/003.php */