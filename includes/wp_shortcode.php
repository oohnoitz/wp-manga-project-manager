<?php

/**
 * Adds a support for shortcode buttons in the editors.
 * @return widget
 */
add_action('init', 'init_sShortCode');
function init_sShortCode() {
	if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
	
	add_thickbox();
	wp_enqueue_style('projects-editor-tablesorter-css', plugin_sURL() . 'admin/assets/jquery-tablesorter.css');
	wp_enqueue_script('projects-editor-button-js', plugin_sURL() . 'admin/editor/wpmanga-editor-button.js', array('jquery', 'thickbox'));
	wp_localize_script('projects-editor-button-js', 'sPROJECTS_Button', array('str_EditorURL' => plugin_sURL(), 'l10n_print_after' => 'try{convertEntities(sPROJECTS_Button);}catch(e){};'));
	
	if (get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'add_sShortCode');
		add_filter('mce_buttons', 'register_sShortCode');
	}
}

/**
 * Add a shortcode button for the visual editor.
 * @return array
 */
function add_sShortCode($plugins) {
	$plugins['release'] = plugin_sURL() . 'admin/editor/wpmanga-tinymce-editor-button.js';
	return $plugins;
}

/**
 * Add a shortcode button for the html editor.
 * @return array
 */
function register_sShortCode($buttons) {
	array_push($buttons, '|', 'release');
	return $buttons;
}

/**
 * Adds the shortcode to display the release information.
 * @return string
 */
add_shortcode('release', 'shortcode_sRelease') ;
function shortcode_sRelease($arr) {
	if ($arr['id'] == "") { return 'Missing Release ID.'; }
	
	global $wpdb; $wpdb->flush();
	$release = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `id` = '%d'", $arr['id']));
			
	return get_sReleaseBar($release);
}

/**
 * Returns the information to display release information in place of the shortcode.
 * @return string
 */
function get_sReleaseBar($release) {
	global $wpdb;
	$project = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects` WHERE `id` = '%d'", $release->project_id));
	
	$output = '';
	if ($release) {
		if (file_exists(get_sTemplate('shortcode-download-release.php')))
			include(get_sTemplate('shortcode-download-release.php'));
	}
	return $output;
}
 
/* EOF: includes/wp_shortcode.php */