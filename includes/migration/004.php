<?php

/**
 * Database Migration: 004
 * Added by Busaway
 *
 */

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_releases}'") == $table_releases ) {
	$wpdb->query("ALTER TABLE `{$table_releases}`
		ADD `chapter_link` VARCHAR(255) NOT NULL AFTER `download_irc`,
		ADD `chapter_lang` VARCHAR(10) NOT NULL AFTER `chapter_link`
	");
}

/* EOF: includes/migration/004.php */