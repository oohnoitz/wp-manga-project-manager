<?php

/**
 * Sets the value for the option specified.
 * @param string $option Query settings for $option specified.
 * @param string $value Stores this value in settings.
 * @return true|false
 */
function wpmanga_set($option, $value) {
	global $wpdb;
	
	$exist = $wpdb->query($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_settings` WHERE `option` = '%s'", $option));
	
	if ($exist)
		return $wpdb->update($wpdb->prefix . 'projects_settings', array('value' => $value), array('option' => $option));
	else
		$wpdb->insert($wpdb->prefix . 'projects_settings', array('option' => $option, 'value' => $value));
		return $wpdb->insert_id;
}

/**
 * Retrieves the value for the option specified.
 * @param string $option Query settings for $option specified.
 * @param string $value Return this value if $option returns NULL.
 * @return mixed The valued stored for the option specified.
 */
function wpmanga_get($option, $value = 0) {
	global $wpdb;
	
	$data = $wpdb->get_row($wpdb->prepare("SELECT `value` FROM `{$wpdb->prefix}projects_settings` WHERE `option` = '%s'", $option));
	
	if ($data != NULL)
		return $data->value;
	else
		return $value;
}

/* EOF: includes/wp_options.php */