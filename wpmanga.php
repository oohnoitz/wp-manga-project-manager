<?php

/**
 * @package WP Manga Project Manager
 * @version 0.2.87
 */
/*
	Plugin Name: WP Manga Project Manager
	Plugin URI: http://dev.xengi.org/blog/
	Description: WP Manga Project Manager allows administrators and editors to manage information regarding project and release information. This plugin allows the changes to be made throughout the database and avoids displaying inaccurate information to the users.
	Author: TEAM SEPTiCORE
	Version: 0.2.87
	Author URI: http://dev.xengi.org/blog/
*/

/**
 * Include Core WP Functions
 */
include('admin/admin_menu.php');
include('includes/database_migration.php');
include('includes/wp_dashboard.php');
include('includes/wp_functions.php');
include('includes/wp_options.php');
include('includes/wp_shortcode.php');
include('includes/wp_widget.php');

$wpmanga_plugin = "0.2.87";

/**
 * Activates the Manga Project Manager Plugin.
 * @return true
 */
register_activation_hook(__FILE__, 'wpmanga_activate');
add_action('admin_menu', 'wpmanga_adminmenu');
function wpmanga_activate() {
	modify_sDatabase();
	return true;
}

/**
 * Returns the projects that belonged to the specified category.
 * @param integer|NULL $id ID of the category.
 * @return object
 */
function get_sListCategory($int = NULL, $list = TRUE) {
	global $wpdb;
	
	if ($list) {
		return $wpdb->get_results( $wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects` WHERE `category` = '%d' ORDER BY `title` ASC", $int) );
	} else {
		$result = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(*) as `total` FROM `{$wpdb->prefix}projects` WHERE `category` = '%d' ORDER BY `title` ASC", $int) );
		return $result->total;
	}
}

/**
 * Returns the list of categories stored in the database.
 * @return object
 */
function get_sListCategories() {
	global $wpdb;
	
	return $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}projects_category` ORDER BY `index` ASC");
}

/**
 * Returns the list of projects stored in the database.
 * @return object
 */
function get_sListProject() {
	global $wpdb;
	
	$projects = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}projects` ORDER BY `title` ASC");
	return $projects;
}

/**
 * Returns the List of Latest Releases.
 * @param integer $limit Limit the number of results returned.
 * @return object
 */
function get_sListLatest($limit = 5, $all = FALSE) {
	global $wpdb;
	
	if ($all)
		$display = "WHERE `unixtime_mod` <= '" . time() . "'";
	else
		$display = '';
	
	if ($limit)
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` {$display} ORDER BY `unixtime_mod` DESC LIMIT 0, %d", $limit));
	else
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` {$display} ORDER BY `unixtime_mod` DESC", $limit));
}

/**
 * Returns the title or name of the category specified.
 * @param integer|NULL $id ID of the category.
 * @return string
 */
function get_sTitleCategory($id = NULL) {
	global $wpdb;
	
	$category = $wpdb->get_row($wpdb->prepare("SELECT `name` FROM `{$wpdb->prefix}projects_category` WHERE `id` = '%d'", $id));
	return (string) $category->name;
}

/**
 * Returns the title or name of the project specified.
 * @param integer|NULL $id ID of the project.
 * @return string
 */
function get_sTitleProject($id = NULL) {
	global $wp, $wpdb;
	
	if (!is_int($id))
		$pid = get_sProjectId($wp->query_vars["pid"]);
	else
		$pid = (int) $id;
	
	$project = $wpdb->get_row($wpdb->prepare("SELECT `title` FROM `{$wpdb->prefix}projects` WHERE `id` = '%d' ORDER BY `title` ASC", $pid));

	return (string) $project->title; 
}

/**
 * Returns the Project ID of the project specified.
 * @param integer|string $query Query the integer/string to obtain ID.
 * @return integer
 */
function get_sProjectId($query) {
	global $wpdb;
	
	if (isset($query->project_id)) return (int) $query->project_id;
	if (isset($query->id)) return (int) $query->id;
	if (is_int($query)) return (int) $query;
	
	$pid = $wpdb->get_row($wpdb->prepare("SELECT `id` FROM `{$wpdb->prefix}projects` WHERE `slug` = '%s'", $query));
	
	if ($pid)
		return (int) $pid->id;
	else
		return (int) $query;
}

/**
 * Returns the Project Details of the project specified.
 * @param integer|string $project Query the integer/string to obtain ID.
 * @param boolean $count Count hits to the project or not.
 * @return object
 */
function get_sProject($project, $count = true) {
	global $wpdb;
	
	$id = get_sProjectId($project);
	if ($count && !is_user_logged_in() ) { $wpdb->query($wpdb->prepare("UPDATE `{$wpdb->prefix}projects` SET `hit` = hit + 1 WHERE `id` = '%d'", $id)); }
	
	$project = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects` WHERE `id` = '%d'", $id));
	if ($project->description != NULL) $project->description = nl2br($project->description);
	if ($project->information != NULL) $project->information = nl2br($project->information);
	
	return $project;
}

/**
 * Returns the Project Volumes.
 * @param integer|string $project Query the integer/string to obtain ID.
 * @return object
 */
function get_sProjectVolumes($project) {
	global $wp, $wpdb;
	
	$id = get_sProjectId($project);
	return $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_volumes` WHERE `project_id` = '%d' ORDER BY `volume` ASC", $id));
}

/**
 * Returns the Project Releases by Volume.
 * @param integer|string $project Query the integer/string to obtain ID.
 * @return object
 */
function get_sProjectReleasesByVolume($project, $volume, $all = FALSE) {
	global $wp, $wpdb;
	
	if ($all)
		$display = "AND `unixtime_mod` <= '" . time() . "'";
	else
		$display = '';
	
	$id = get_sProjectId($project);
	return $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d' AND `volume` = '%d' {$display} ORDER BY `volume` ASC, `chapter` ASC, `subchapter` ASC, `type` ASC", $id, $volume));
}

/**
 * Returns the Project Releases.
 * @param integer|string $project Query the integer/string to obtain ID.
 * @return object
 */
function get_sProjectReleases($project, $all = FALSE) {
	global $wp, $wpdb;
	
	if ($all)
		$display = "AND `unixtime_mod` <= '" . time() . "'";
	else
		$display = '';
	
	$id = get_sProjectId($project);
	return $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d' {$display} ORDER BY `volume` ASC, `chapter` ASC, `subchapter` ASC, `type` ASC", $id));
}

/**
 * Returns the Release Downloads Modified.
 * @param object $release Modifies the release data passed.
 * @return object
 */
function get_sReleaseDownloads($release) {
	$downloads = new stdClass();

	foreach ($release as $download => $link) {
		if (preg_match("/download_(.*?)/i", $download) && $link) {
			if (wpmanga_get('wpmanga_disable_' . str_replace('download_', '', $download), 0))
				unset($release->{$download});
			elseif (wpmanga_get('wpmanga_delay_' . str_replace('download_', '', $download), 0) && (($release->unixtime + wpmanga_get('wpmanga_delay', 0)*60*60) >= time()))
				unset($release->{$download});
			else
				$downloads->{$download} = $release->{$download};
		}
	}
	
	return $downloads;
}

/**
 * Returns the Release Information for specified ID.
 * @param integer $project Query the integer to obtain ID.
 * @return object
 */
function get_sRelease($id) {
	global $wpdb;
	
	$release = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `id` = '%d'", $id));
	return $release;
}

/**
 * Returns the Release Information for LAST release.
 * @param integer $project Query the integer to obtain ID.
 * @return object
 */
function get_sLastRelease($id) {
	global $wpdb;
	
	#$release = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d' ORDER BY `volume` DESC, `chapter` DESC, `subchapter` DESC LIMIT 1", $id));
	$release = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d' ORDER BY `unixtime_mod` DESC, `unixtime` DESC LIMIT 1", $id));
	return $release;
}

/**
 * Returns the Release Information for specified ID.
 * @param integer $project Query the integer to obtain ID.
 * @return object
 */
function get_sFormatRelease($project, $release, $v = true, $c = true, $r = true) {
	$custom_chp = get_sJSON($project->custom, 'chapter');
	$custom_sub = get_sJSON($project->custom, 'subchapter');
	
	$output = '';
	
	switch ($release->type) {
		case 5:
			$output .= 'Volume ' . str_pad($release->volume, 2, '0', STR_PAD_LEFT);
			break;
			
		case 10:
			if ($v && $release->volume) $output .= 'Volume ' . str_pad($release->volume, 2, '0', STR_PAD_LEFT) . ' ';
			$output .= 'Special';
			if ($release->subchapter > 0) $output .= ' ' . $release->subchapter;
			break;
		
		case 20:
			if ($v && $release->volume) $output .= 'Volume ' . str_pad($release->volume, 2, '0', STR_PAD_LEFT) . ' ';
			$output .= 'Oneshot';
			if ($release->subchapter > 0) $output .= ' ' . $release->subchapter;
			break;
		
		default:
			if ($v && $release->volume) $output .= 'Volume ' . str_pad($release->volume, 2, '0', STR_PAD_LEFT) . ' ';
			if ($c) {
				if ($custom_chp)
					$output .= str_replace('%num%', str_pad($release->chapter, 2, '0', STR_PAD_LEFT), $custom_chp);
				else
					$output .= 'Chapter ' . str_pad($release->chapter, 2, '0', STR_PAD_LEFT);
				
				if ($release->subchapter > 0) {
					if ($custom_sub)
						$output .= ' ' . str_replace('%num%', str_pad($release->subchapter, 2, '0', STR_PAD_LEFT), $custom_sub);
					else
						$output .= '.' . $release->subchapter;
				}
			}
	}
	
	if ($r && $release->revision > 1) $output .= ' v' . $release->revision;

	return $output;
}

/**
 * Returns the Thumbnail Generated for the source and dimensions specified.
 * @param string $dimensions The dimensions of the thumbnail generated by WIDTHxHEIGHT.
 * @param string $limit The original source of the thumbnail.
 * @return string
 */
function get_sThumbnail($dimensions, $source = NULL) {
	if ($source != NULL && file_exists(plugin_sDIR() . 'cache/' . $dimensions . '__' . basename($source)))
		return plugin_sURL() . 'cache/' . $dimensions . '__' . basename($source);
	else
		return plugin_sURL() . 'images/__blank.png';
}

/**
 * Generates a link to the corresponding chapter in FoOlSlide. This is still under some test and has not been tested under certain circumstances.
 * @param object $project Process the project array provided.
 * @param object $release Process the release array provided.
 * @return string
 */
function get_sReaderLink($project, $release) {
	switch (wpmanga_get('wpmanga_reader')) {
		 // FoOlSlide
		case 1:
			$url = str_replace('/reader/comic/', '/reader/read/', $project->reader) . 'en'. '/' . $release->volume . '/' . $release->chapter . '/';
			$url = str_replace('/reader/serie/', '/reader/read/', $url);
			if ($release->subchapter) $url .= $release->subchapter . '/';
			break;
		
		 // None: Return $reader
		default:
			$url = $project->reader;
			break;
	}
	
	return $url;
}

/**
 * Returns the Permalink for the project specified.
 * @param integer $id Query the ID to obtain the permalink.
 * @return string
 */
function get_sPermalink($project = NULL) {
	global $wp, $wpdb;
	
	if (!$project) {
		$id = get_sProjectId($wp->query_vars["pid"]);
		$project = $wpdb->get_row($wpdb->prepare("SELECT `id`, `slug` FROM `{$wpdb->prefix}projects` WHERE `id` = '%d' ORDER BY `title` ASC", $id));
	}
	elseif (is_int($project)) {
		$id = (int) $project;
		$project = $wpdb->get_row($wpdb->prepare("SELECT `id`, `slug` FROM `{$wpdb->prefix}projects` WHERE `id` = '%d' ORDER BY `title` ASC", $id));
	}
	
	if ($project->slug != NULL)
		return (string) get_bloginfo('siteurl') . '/projects/' . $project->slug . '/';
	else
		return (string) get_bloginfo('siteurl') . '/projects/' . $project->id . '/';
}

/**
 * Returns the sanitized format of the title specified.
 * @param string $title Title of the project to be sanitized.
 * @return string
 */
function get_sSanitizedSlug($title) {
	return sanitize_title_with_dashes(remove_accents($title));
}

/* EOF: wpmanga.php */