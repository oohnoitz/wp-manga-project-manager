<?php

/**
 * Database Migration: 002
 */
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_projects}'") == $table_projects ) {
	$wpdb->query("ALTER TABLE `{$table_projects}`
		CHANGE `name` `title` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		CHANGE `name_alt` `title_alt` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
	");
}
 
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_releases}'") == $table_releases ) {
	$wpdb->query("ALTER TABLE `{$table_releases}`
		CHANGE `pid` `project_id` MEDIUMINT(9) NOT NULL DEFAULT '1',
		CHANGE `link1` `download_megaupload` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		CHANGE `link2` `download_mediafire` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		CHANGE `link3` `download_depositfiles` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		CHANGE `link4` `download_irc` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
	");

	$wpdb->query("ALTER TABLE `{$table_releases}`
		ADD `download_fileserve` VARCHAR(255) NOT NULL AFTER `download_depositfiles`,
		ADD `download_filesonic` VARCHAR(255) NOT NULL AFTER `download_fileserve`,
		ADD `download_pdf` VARCHAR(255) NOT NULL AFTER `download_filesonic`
	");
}

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_volumes}'") == $table_volumes ) {
	$wpdb->QUERY("ALTER TABLE `{$table_volumes}`
		CHANGE `pid` `project_id` MEDIUMINT(9) NOT NULL DEFAULT '1',
		CHANGE `cover` `image` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
	");
}

/* EOF: includes/migration/002.php */