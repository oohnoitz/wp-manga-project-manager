<?php

/**
 * Modify the wp_title() function to display correct title.
 * @return string Title of the Project
 */
add_filter('wp_title', 'rewrite_sWPTitle');
function rewrite_sWPTitle($title) {
	global $wp;
	
	if ($wp->query_vars['pid']) {
		$ntitle = get_sTitleProject(get_sProjectId($wp->query_vars['pid']));
		if ($ntitle)
			return str_replace('Projects', $ntitle, $title);
		else
			return str_replace('Projects', 'Oops!', $title);
	} else {
		return $title;
	}
}

/**
 * Flush Custom Rewrite Rules in WordPress.
 * @return array List of Rewrite Rules parsed by WordPress.
 */
add_action('wp_loaded', 'flush_sProjects');
function flush_sProjects() {
	$rewrite = get_option('rewrite_rules');
	if (!isset($rewrite['projects/([^/]+)/?$'])) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}

/**
 * Add Custom Rewrite Rule to WordPress.
 * @return array List of Rewrite Rules parsed by WordPress.
 */
add_action('init', 'rewrite_sProjects');
function rewrite_sProjects() {
	add_rewrite_rule('projects/([^/]+)/?$', 'index.php?pagename=projects&pid=$matches[1]', 'top');
}

/**
 * Add Custom Query Variables to WordPress.
 * @return array List of Query Variables parsed by WordPress.
 */
add_filter('query_vars', 'query_sProjects');
function query_sProjects($query_vars) {
	$query_vars[] = 'pid';
	return $query_vars;
}

 /**
 * Add Custom Template Redirection to WordPress.
 * @return template The correct template file for the page queried.
 */
add_action("template_redirect", 'redirect_sProjects');
function redirect_sProjects() {
	if (file_exists(plugin_sDIR() . 'templates/style.alt.css'))
		wp_enqueue_style('wpmanga_style', plugin_sURL() . 'templates/style.alt.css');
	else
		wp_enqueue_style('wpmanga_style', plugin_sURL() . 'templates/style.css');
	
	wp_enqueue_script('jquery-tools', plugin_sURL() . 'assets/jquery.tools.min.js', array('jquery'));
	wp_enqueue_script('jquery-overlay', plugin_sURL() . 'assets/jquery.overlay.js', array('jquery'));
	wp_enqueue_script('jquery-icons', plugin_sURL() . 'assets/jquery.icons.js', array('jquery'));
}

 /**
 * Display the correct title for pages.
 * @return string Returns the title of the page.
 */
add_filter('the_title', 'title_sProjects');
function title_sProjects($title) {
	global $id, $wp;
	
	if (wpmanga_get('wpmanga_page_details_title', 0)) return $title;
	if ($wp->query_vars["pagename"] == 'projects' && $wp->query_vars["pid"] && $id && $title == 'Projects') {
		$title = get_sTitleProject(get_sProjectId($wp->query_vars['pid']));
		if ($title)
			return $title;
		else
			return "Oops!";
	} elseif ($wp->query_vars["pagename"] == 'projects' && $id && $title == 'Projects') {
		return 'Projects';
	} else {
		return $title;
	}
}

 /**
 * Displays the Project Pages and Details in the_content.
 * @return string Replaces the_content with provided template.
 */
add_filter('the_content', 'template_sProjects');
function template_sProjects($content) {
	global $wp, $wp_query, $post;
	
	if ($wp->query_vars["pagename"] == 'projects') {
		if ($wp->query_vars["pid"])
			$template = 'page-projects-details.php';
		else
			$template = 'page-projects.php';
		
		if (file_exists(TEMPLATEPATH . '/' . $template))
			$templatePath = TEMPLATEPATH . '/' . $template;
		else
			$templatePath = get_sTemplate($template);
		
		include($templatePath);
	}
	else {
		return $content;
	}
}

 /**
 * Returns the correct 'edit' link on pages.
 * @return string
 */
add_filter('edit_post_link', 'editlink_sProjects');
function editlink_sProjects($link) {
	global $id, $wp;
	
	if ($wp->query_vars["pagename"] == 'projects' && $wp->query_vars["pid"] && $id) {
		if (get_sProjectId($wp->query_vars["pid"]))
			return preg_replace('/<(.*?)href="(.*?)"(.*?)>/i', '<\1href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=manga/project&action=edit&id=' . get_sProjectId($wp->query_vars["pid"]) . '"\3>', $link);
		else
			return preg_replace('/<(.*?)href="(.*?)"(.*?)>/i', '<\1href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=manga"\3>', $link);
	} elseif ($wp->query_vars["pagename"] == 'projects' && $id) {
		return preg_replace('/<(.*?)href="(.*?)"(.*?)>/i', '<\1href="' . get_bloginfo('siteurl') . '/wp-admin/admin.php?page=manga"\3>', $link);
	} else {
		return $link;
	}
}

/**
 * Disables the comments section by default for projects.
 * @return boolean
 */
add_filter('comments_open', 'comments_sProjects');
function comments_sProjects() {
	global $id, $wp;
	
	if ($wp->query_vars["pagename"] == 'projects' && $id)
		return false;
	else
		return true;
}

/**
 * Modifies Footer to add overlay code.
 * @return string
 */
add_filter('get_footer', 'footer_sProjects');
function footer_sProjects() {
	$channel = wpmanga_get('wpmanga_channel', '#');
	$content = '<div class="download-overlay" id="download-overlay">';
	$content .= "TO DOWNLOAD, COPY AND PASTE THE FOLLOWING LINE INTO YOUR <a href='{$channel}' title='{$channel}'>IRC CLIENT</a>:<br><br><input class='xdcc' type='text' size='48'><br><br>(CLICK ANYWHERE OUTSIDE THIS BOX TO CLOSE THE OVERLAY)";
	$content .= '</div>';
	echo $content;
}

/**
 * Obtain the path to the template specified.
 * @param string $template Template filename
 * @return string Path to the template
 */
function get_sTemplate($template) {
	if (file_exists(plugin_sDIR() . 'templates/' . strtolower(basename(TEMPLATEPATH)) . '/' . $template))
		$templatePath = plugin_sDIR() . 'templates/' . strtolower(basename(TEMPLATEPATH)) . '/' . $template;
	elseif (file_exists(plugin_sDIR() . 'templates/' . $template))
		$templatePath = plugin_sDIR() . 'templates/' . $template;
	else
		$templatePath = '';
	
	return (string) $templatePath;
}

/**
 * Returns the Plugin URL for this Plugin.
 * @return string
 */
function plugin_sURL() {
	return WP_PLUGIN_URL . '/' . str_replace('includes/' . basename(__FILE__), "", plugin_basename(__FILE__));
}

/**
 * Returns the Plugin DIR for this Plugin.
 * @return string
 */
function plugin_sDIR() {
	return WP_PLUGIN_DIR . '/' . str_replace('includes/' . basename(__FILE__), "", plugin_basename(__FILE__));
}

/**
 * Returns the JSON values for options.
 * @param object $data Process the JSON provided.
 * @param string $field Obtain the field specified.
 * @return string
 */
function get_sJSON($data, $field) {
	$json = json_decode($data);
	
	return $json->{$field};
}

/*
 * Truncates the string passed.
 * @param string $string
 * @param integer $limit
 * @return string Returns the string truncated.
 */
function get_sTruncate($string, $limit) {
	if (strlen($string) <= $limit) return $string;
	
	if (false !== ($trunc = strpos($string, '.', $limit))) {
		if ($trunc < strlen($string) - 1) $string = substr($string, 0, $trunc) . '...';
	} else {
		$string = substr($string, 0, $limit) . '...';
	}
	return $string;
}

/**
 * Returns the elapsed time passed.
 * @param integer $unixtime The unixtime used to calculate the elapsed time displayed.
 * @return string
 */
function get_sDuration($unixtime) {
	$unixtime = time() - $unixtime;
	$w = 0; $d = 0; $h = 0; $m = 0; $s = 0; $output = "";
	if ($unixtime < 60) return "Just Recently...";
	while ($unixtime >= 604800) { $w++; $unixtime = $unixtime - 604800; }
	while ($unixtime >= 86400) { $d++; $unixtime = $unixtime - 86400; }
	while ($unixtime >= 3600) { $h++; $unixtime = $unixtime - 3600; }
	while ($unixtime >= 60) { $m++; $unixtime = $unixtime - 60; }
	while ($unixtime >= 0) { $s++; $unixtime = $unixtime - 1; }
	if ($w == 1) { $output .= "1 week "; } else if ($w > 1) { $output .= "{$w} weeks "; }
	if ($d == 1) { $output .= "1 day "; } else if ($d > 1) { $output .= "{$d} days "; }
	if ($h == 1) { $output .= "1 hour ";	} else if ($h > 1) { $output .= "{$h} hours "; }
	if ($w == 0) {
		if ($m == 1) { $output .= "1 minute "; } else if ($m > 1) { $output .= "{$m} minutes "; }
	}
	$output .= "ago";
	return $output;
}

/**
 * Returns the Version of the Project Plugin.
 * @param string Obtain the version of the Plugin/Database
 * @return string Version
 */
function get_sVersion($type = 'plugin') {
	global $wpmanga_plugin;
	
	switch ($type) {
		case 'db':
			return get_option('wpmanga_db');
			break;
		default:
			return $wpmanga_plugin;
	}
}

/* EOF: includes/wp_functions.php */