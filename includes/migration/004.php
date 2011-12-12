<?php

/**
 * Database Migration: 004
 */

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_projects}'") == $table_projects ) {
	$wpdb->query("ALTER TABLE `{$table_projects}`
		ADD `mature` INT(12) NOT NULL AFTER `url`
	");
}

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_releases}'") == $table_releases ) {
	$wpdb->query("ALTER TABLE `{$table_releases}`
		ADD `link_reader` VARCHAR(255) NOT NULL AFTER `download_irc`,
		ADD `language` VARCHAR(10) NOT NULL AFTER `title`
	");
}

/* EOF: includes/migration/004.php */