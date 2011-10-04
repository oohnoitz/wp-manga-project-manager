<?php

/**
 * Database Migration: 001
 */
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_projects}'") != $table_projects ) {
	$structure = "CREATE TABLE `{$table_projects}` (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`category` int(1) NOT NULL DEFAULT '1',
		`slug` tinytext NOT NULL,
		`name` tinytext NOT NULL,
		`name_alt` tinytext NOT NULL,
		`description` text NOT NULL,
		`author` tinytext NOT NULL,
		`genres` tinytext NOT NULL,
		`status` tinytext NOT NULL,
		`image` tinytext NOT NULL,
		`reader` VARCHAR(255) NOT NULL,
		`url` VARCHAR(255) NOT NULL,
		`options` VARCHAR(255) NOT NULL,
		`hit` mediumint(9) NOT NULL DEFAULT '0',
		UNIQUE KEY `id` (`id`),
		KEY `category` (`category`),
		FULLTEXT KEY `slug` (`slug`)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 AUTO_INCREMENT=0;";
	
	$wpdb->query($structure);
}

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_releases}'") != $table_releases ) {
	$structure = "CREATE TABLE " . $table_releases . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`pid` mediumint(9) NOT NULL DEFAULT '1',
		`unixtime` int(12) NOT NULL,
		`unixtime_mod` int(12) NOT NULL,
		`volume` int(12) NOT NULL DEFAULT '0',
		`chapter` int(12) NOT NULL DEFAULT '0',
		`subchapter` int(12) NOT NULL DEFAULT '0',
		`revision` int(12) NOT NULL DEFAULT '0',
		`title` tinytext NOT NULL,
		`link1` VARCHAR(255) NOT NULL,
		`link2` VARCHAR(255) NOT NULL,
		`link3` VARCHAR(255) NOT NULL,
		`link4` VARCHAR(255) NOT NULL,
		UNIQUE KEY `id` (`id`),
		KEY `pid` (`pid`)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 AUTO_INCREMENT=0;";
	
	$wpdb->query($structure);
}

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_category}'") != $table_category ) {
	$structure = "CREATE TABLE " . $table_category . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` tinytext NOT NULL,
		`description` text NOT NULL,
		`index` mediumint(9) NOT NULL,
		UNIQUE KEY `id` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 AUTO_INCREMENT=0;";
	
	$wpdb->query($structure);
	
	// Default Categories
	$wpdb->insert($table_category,	array('name' => 'Ongoing', 'description' => 'Ongoing Project(s)', 'index' => 1) );
	$wpdb->insert($table_category,	array('name' => 'Completed', 'description' => 'Completed Project(s)', 'index' => 2) );
	$wpdb->insert($table_category,	array('name' => 'Future Project', 'description' => 'Future Project(s)', 'index' => 3) );
	$wpdb->insert($table_category,	array('name' => 'On Hiatus', 'description' => 'Stalled Project(s)', 'index' => 4) );
	$wpdb->insert($table_category,	array('name' => 'Dropped', 'description' => 'Dropped Project(s)', 'index' => 5) );
}

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_volumes}'") != $table_volumes ) {
	$structure = "CREATE TABLE " . $table_volumes . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`pid` int(12) NOT NULL DEFAULT '1',
		`volume` int(12) NOT NULL DEFAULT '0',
		`cover` tinytext NOT NULL,
		UNIQUE KEY `id` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=UTF8 AUTO_INCREMENT=0;";
	
	$wpdb->query($structure);
}

/* EOF: includes/migration/001.php */