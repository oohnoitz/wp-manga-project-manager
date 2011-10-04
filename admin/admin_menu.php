<?php

/**
 * Include Core WP Admin Files
 */
include('menu_project.php');
include('data_project.php');
include('menu_release.php');
include('data_release.php');
include('menu_cover.php');
include('data_cover.php');

/**
 * Generate WP Admin Menu
 * @return menu
 */
function wpmanga_adminmenu () {
	if ( current_user_can('edit_posts') || current_user_can('edit_pages') ) {
		
		// Projects
		add_menu_page('Projects', 'Projects', 'edit_posts', 'manga', 'wpmanga_listProjects');
		add_submenu_page('manga', 'Add/Edit/Delete Project', '-- Add/Edit/Delete', 'edit_posts', 'manga/project', 'wpmanga_dataProject');
		
		// Volume Covers
		add_submenu_page('manga', 'Volume Covers', 'Volume Covers', 'edit_posts', 'manga/list/volume', 'wpmanga_listCovers');
		add_submenu_page('manga', 'Add/Edit/Delete Volume Cover', '-- Add/Edit/Delete', 'edit_posts', 'manga/volume', 'wpmanga_dataCover');
		
		// Releases
		add_submenu_page('manga', 'Releases', 'Releases', 'edit_posts', 'manga/list/release', 'wpmanga_listReleases');
		add_submenu_page('manga', 'Add/Edit/Delete Release', '-- Add/Edit/Delete', 'edit_posts', 'manga/release', 'wpmanga_dataRelease');
		
		// Miscellaneous Pages
		if (is_admin())
			add_submenu_page('manga', 'WP Manga Settings', 'Settings', 'manage_options', 'manga/settings', 'wpmanga_settings');
		add_submenu_page('manga', 'About', 'About', 'edit_posts', 'manga/about', 'wpmanga_about');
		
		
		// Load Required JavaScript and StyleSheet
		if (preg_match("/(manga\/project|manga\/volume)/i", $_GET['page'])) {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('jquery');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('pimage-upload', plugin_sURL() . 'admin/assets/media-uploader.js', array('jquery', 'media-upload', 'thickbox'));
		}
		
		if (preg_match("/(manga\/release)/i", $_GET['page'])) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui', plugin_sURL() . 'admin/assets/jquery-ui.custom.js', array('jquery'));
			wp_enqueue_script('datetime', plugin_sURL() . 'admin/assets/jquery-ui.datetime.js', array('jquery'));
			wp_enqueue_style('datetime', plugin_sURL() . 'admin/assets/jquery-ui.datetime.css');
			wp_enqueue_style('jquery-ui', plugin_sURL() . 'admin/assets/jquery-ui.custom.css');
		}
	}
}

/**
 * Generate WP Admin Menu for Admin Bar
 * @return menu
 */
add_action('admin_bar_menu', 'wpmanga_adminbar', 9999);
function wpmanga_adminbar() {
	global $wp_admin_bar;
	if (!is_admin_bar_showing()) return;
	
	if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga',
			'title' => 'WPManga',
			'href' => FALSE
		));
			
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_project_list',
			'parent' => 'wpmanga',
			'title' => 'Projects',
			'href' => admin_url('admin.php?page=manga')
		));
			
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_project',
			'parent' => 'wpmanga',
			'title' => 'Add New Project',
			'href' => admin_url('admin.php?page=manga/project')
		));
			
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_release_list',
			'parent' => 'wpmanga',
			'title' => 'Releases',
			'href' => admin_url('admin.php?page=manga/list/release')
		));
			
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_release',
			'parent' => 'wpmanga',
			'title' => 'Add New Release',
			'href' => admin_url('admin.php?page=manga/release')
		));
	}
		
	if (current_user_can('level_10')) {
		$wp_admin_bar->add_menu(array(
			'id' => 'wpmanga_settings',
			'parent' => 'wpmanga',
			'title' => 'Settings',
			'href' => admin_url('admin.php?page=manga/settings')
		));
	}
}

/**
 * Generate Plugin Admin Settings Link
 * @return menu
 */
add_filter('plugin_action_links', 'wpmanga_settings_link', 10, 2);
function wpmanga_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(plugin_sDIR() . '/wpmanga.php');
	
	if (is_admin() && $file == $this_plugin) {
		$settings_link = '<a href="admin.php?page=manga/settings">Settings</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}


function wpmanga_settings() {
	global $wpdb;
	
	if (isset($_POST['settings_nonce'])) {
		if ( !wp_verify_nonce( $_POST['settings_nonce'], plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ) ) {
			echo '<div class="error"><p>Error: Security Verification Failed.</p></div>';
		} else {
			$_POST = array_map('trim', $_POST);
			
			// Check Boxes Suck (Search for Alternative Method)
			if (!$_POST['wpmanga_page_details_title']) $_POST['wpmanga_page_details_title'] = 0;
			if (!$_POST['wpmanga_page_details_header']) $_POST['wpmanga_page_details_header'] = 0;
			if (!$_POST['wpmanga_foolreader']) $_POST['wpmanga_page_details_header'] = 0;
			if (!$_POST['wpmanga_widget_icons']) $_POST['wpmanga_widget_icons'] = 0;
			if (!$_POST['wpmanga_delay_megaupload']) $_POST['wpmanga_delay_megaupload'] = 0;
			if (!$_POST['wpmanga_delay_mediafire']) $_POST['wpmanga_delay_mediafire'] = 0;
			if (!$_POST['wpmanga_delay_depositfiles']) $_POST['wpmanga_delay_depositfiles'] = 0;
			if (!$_POST['wpmanga_delay_fileserve']) $_POST['wpmanga_delay_fileserve'] = 0;
			if (!$_POST['wpmanga_delay_filesonic']) $_POST['wpmanga_delay_filesonic'] = 0;
			if (!$_POST['wpmanga_delay_pdf']) $_POST['wpmanga_delay_pdf'] = 0;
			if (!$_POST['wpmanga_disable_megaupload']) $_POST['wpmanga_disable_megaupload'] = 0;
			if (!$_POST['wpmanga_disable_mediafire']) $_POST['wpmanga_disable_mediafire'] = 0;
			if (!$_POST['wpmanga_disable_depositfiles']) $_POST['wpmanga_disable_depositfiles'] = 0;
			if (!$_POST['wpmanga_disable_fileserve']) $_POST['wpmanga_disable_fileserve'] = 0;
			if (!$_POST['wpmanga_disable_filesonic']) $_POST['wpmanga_disable_filesonic'] = 0;
			if (!$_POST['wpmanga_disable_pdf']) $_POST['wpmanga_disable_pdf'] = 0;
	
			// Filter $_POST and Update Setting
			$_DATA = array();
			foreach ($_POST as $key => $value) {
				if (preg_match("/wpmanga_(.*?)/i", $key)) {
					$status = wpmanga_set($key, $value); $_DATA[$key] = $status;
				}
			}
			
			// Update Thumbnails
			if ($_DATA['wpmanga_thumbnail_list_width'] || $_DATA['wpmanga_thumbnail_list_height']) {
				set_time_limit(0);
				$thumbnail = new WP_Http;
				foreach (get_sListProject() as $project) {
					$thumbnail->request(plugin_sURL() . 'includes/generate_thumbnail.php?src=' . $project->image . '&w=' . wpmanga_get('wpmanga_thumbnail_list_width', 145) . '&h=' . wpmanga_get('wpmanga_thumbnail_list_height', 300));
				}
			}
			
			echo '<div class="updated"><p>Updated Settings.</p></div>';
		}
	}
	
	if (isset($_GET['generate'])) {
		if ($_GET['generate'] == 'thumbnails') {
			set_time_limit(0);
			$thumbnail = new WP_Http;
			foreach (get_sListProject() as $project) {
				$thumbnail->request(plugin_sURL() . 'includes/generate_thumbnail.php?src=' . $project->image . '&w=' . wpmanga_get('wpmanga_thumbnail_list_width', 145) . '&h=' . wpmanga_get('wpmanga_thumbnail_list_height', 300));
			}
			
			echo '<div class="updated"><p>Finished Generating Thumbnails.</p></div>';
		}
	}
?>
	<div class="wrap">
		<?php screen_icon('options-general'); ?>
		<h2>WP Manga Settings</h2>

		<p>WP Manga Project Manager has several options which affect the plugin behavior in different areas. The Frontend Options influence the output and features available in the pages, posts, or text-widgets. The Backend Options control the plugin's administrative area.</p>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<form method="post" action="admin.php?page=manga/settings">
					<div class="postbox">
						<h3 class='hndle'><span>Frontend Options</span></h3>
						<div class="inside">
							<table class="form-table fixed">
								<tr class="form-field">
									<td width="250px"><label for="wpmanga_thumbnail_list_width">Thumbnail Dimensions</label></td>
									<td>
										Width <input name="wpmanga_thumbnail_list_width" id="wpmanga_thumbnail_list_width" type="number" value="<?php echo wpmanga_get('wpmanga_thumbnail_list_width', 145); ?>" style="width: 10%;"> &nbsp; 
										Height <input name="wpmanga_thumbnail_list_height" id="wpmanga_thumbnail_list_height" type="number" value="<?php echo wpmanga_get('wpmanga_thumbnail_list_height', 300); ?>" style="width: 10%;"> &nbsp; <a class="button-secondary" href="admin.php?page=manga/settings&generate=thumbnails">Force Generate Thumbnails</a>
									</td>
								</tr>
								
								<tr class="form">
									<td valign="top" width="250px"><label>Individual Project Page</label></td>
									<td>
										<input type="checkbox" name="wpmanga_page_details_title" id="wpmanga_page_details_title" value="1" <?php if (wpmanga_get('wpmanga_page_details_title', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_page_details_title">Disable Title Filter <span class="description">(For Specific Themes)</span></label> <br>
										<input type="checkbox" name="wpmanga_page_details_header" id="wpmanga_page_details_header" value="1" <?php if (wpmanga_get('wpmanga_page_details_header', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_page_details_header">Display Header <span class="description">(For Specific Themes)</span></label>
									</td>
								</tr>
								
								<tr class="form">
									<td valign="top" width="250px"><label>Online Reader Link Generator</label></td>
									<td>
										<input name="wpmanga_reader" id="reader_foolreader" type="radio" value="1"<?php if (wpmanga_get('wpmanga_reader', 1) == 1) echo ' checked="checked"'; ?>> <label for="reader_foolreader">FoOlSlide</label> &nbsp; 
										<input name="wpmanga_reader" id="reader_none" type="radio" value="0"<?php if (wpmanga_get('wpmanga_reader', 1) == 0) echo ' checked="checked"'; ?>> <label for="reader_none">None</label>
									</td>
								</tr>
								
								<tr class="form">
									<td valign="top" width="250px"><label for="wpmanga_releasebar_style">Release Bar Display Style</label></td>
									<td>
										<select name="wpmanga_releasebar_style" id="wpmanga_releasebar_style" style="width: 100%">
											<option value="1"<?php if (wpmanga_get('wpmanga_releasebar_style', 1) == 1); echo ' selected="selected"'; ?>>Default Release Bar</option>
											<option value="2"<?php if (wpmanga_get('wpmanga_releasebar_style', 1) == 2); echo ' selected="selected"'; ?>>Plain Release Bar</option>
										</select>
									</td>
								</tr>
								
								<tr class="form">
									<td width="250px"><label for="wpmanga_widget_icons">Latest Widget List</label></td>
									<td>
										<input type="checkbox" name="wpmanga_widget_icons" id="wpmanga_widget_icons" value="1" <?php if (wpmanga_get('wpmanga_widget_icons', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_widget_icons">Show Release Icons</label>
									</td>
								</tr>
								
								<tr class="form">
									<td width="250px"><label for="wpmanga_channel">IRC Channel</label></td>
									<td>
										<input name="wpmanga_channel" id="wpmanga_channel" type="text" placeholder="irc://irc.irchighway.net/beta" value="<?php echo wpmanga_get('wpmanga_channel', ''); ?>" style="width: 100%;">
									</td>
								</tr>
								
								<tr class="form-field">
									<td valign="top" style="padding-top: 10px;" width="250px"><label for="wpmanga_delay">Delay Download Link</label></td>
									<td>
										<input name="wpmanga_delay" id="wpmanga_delay" type="number" value="<?php echo wpmanga_get('wpmanga_delay', 0); ?>" style="width: 10%;"> Hours <br>
										<input type="checkbox" name="wpmanga_delay_depositfiles" id="wpmanga_delay_depositfiles" value="1" <?php if (wpmanga_get('wpmanga_delay_depositfiles', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_depositfiles">Deposit Files</label> <br>
										<input type="checkbox" name="wpmanga_delay_fileserve" id="wpmanga_delay_fileserve" value="1" <?php if (wpmanga_get('wpmanga_delay_fileserve', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_fileserve">FileServe</label> <br>
										<input type="checkbox" name="wpmanga_delay_filesonic" id="wpmanga_delay_filesonic" value="1" <?php if (wpmanga_get('wpmanga_delay_filesonic', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_filesonic">FileSonic</label> <br>
										<input type="checkbox" name="wpmanga_delay_mediafire" id="wpmanga_delay_mediafire" value="1" <?php if (wpmanga_get('wpmanga_delay_mediafire', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_mediafire">MediaFire</label> <br>
										<input type="checkbox" name="wpmanga_delay_megaupload" id="wpmanga_delay_megaupload" value="1" <?php if (wpmanga_get('wpmanga_delay_megaupload', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_megaupload">MEGAUPLOAD</label> <br>
										<input type="checkbox" name="wpmanga_delay_pdf" id="wpmanga_delay_pdf" value="1" <?php if (wpmanga_get('wpmanga_delay_pdf', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_delay_pdf">PDF</label>
									</td>
								</tr>
							</table>
								
							&nbsp; <input type="submit" class="button-primary" name="save" value="Save Settings"><br><br>
							<input type="hidden" name="settings_nonce" value="<?php echo wp_create_nonce( plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ); ?>">
						</div>
					</div>
					
					<div class="postbox" >
						<h3 class='hndle'><span>Backend Options</span></h3>
						<div class="inside">
							<table class="form-table fixed">
								<tr class="form-field">
									<td valign="top" style="padding-top: 10px;" width="250px"><label>Disable Download Links</label></td>
									<td>
										<input type="checkbox" name="wpmanga_disable_depositfiles" id="wpmanga_disable_depositfiles" value="1" <?php if (wpmanga_get('wpmanga_disable_depositfiles', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_depositfiles">Deposit Files</label> <br>
										<input type="checkbox" name="wpmanga_disable_fileserve" id="wpmanga_disable_fileserve" value="1" <?php if (wpmanga_get('wpmanga_disable_fileserve', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_fileserve">FileServe</label> <br>
										<input type="checkbox" name="wpmanga_disable_filesonic" id="wpmanga_disable_filesonic" value="1" <?php if (wpmanga_get('wpmanga_disable_filesonic', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_filesonic">FileSonic</label> <br>
										<input type="checkbox" name="wpmanga_disable_mediafire" id="wpmanga_disable_mediafire" value="1" <?php if (wpmanga_get('wpmanga_disable_mediafire', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_mediafire">MediaFire</label> <br>
										<input type="checkbox" name="wpmanga_disable_megaupload" id="wpmanga_disable_megaupload" value="1" <?php if (wpmanga_get('wpmanga_disable_megaupload', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_megaupload">MEGAUPLOAD</label> <br>
										<input type="checkbox" name="wpmanga_disable_pdf" id="wpmanga_disable_pdf" value="1" <?php if (wpmanga_get('wpmanga_disable_pdf', 0)) echo 'checked="checked"' ?> style="width: 20px;"> <label for="wpmanga_disable_pdf">PDF</label>
									</td>
								</tr>
							</table>
								
							&nbsp; <input type="submit" class="button-primary" name="save" value="Save Settings"><br><br>
							<input type="hidden" name="settings_nonce" value="<?php echo wp_create_nonce( plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}


function wpmanga_about() {
	global $wpdb;
?>
	<div class="wrap">
		<?php screen_icon('users'); ?>
		<h2><?php echo esc_html( 'About' ); ?></h2>

		<br />
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div class="postbox" >
					<h3 class='hndle'><span>What is the purpose of this plugin?</span></h3>
					<div class="inside">
						<p>WP Manga Project Manager allows you to manage your projects and releases to ensure that all information and links are accurate and working throughout the entire WordPress. This will enable your users to avoid any confusion and conflicting information by delivering all the information from one single database.</p>
					</div>
				</div>
				
				<div class="postbox" >
					<h3 class='hndle'><span>Usage</span></h3>
					<div class="inside">
						<p>We are currently drafting a documentation regarding how to use this plugin. It was meant to be used by scanlation groups to keep information about their releases updated.</p>
					</div>
				</div>
				
				<div class="postbox" >
					<h3 class='hndle'><span>Help and Support</span></h3>
					<div class="inside">
						<p>Support is provided through IRC. Please contact me (prinny) on the IRCHighway network in #beta.</p>
					</div>
				</div>
				
				<div class="postbox" >
					<h3 class='hndle'><span>Author and License</span></h3>
					<div class="inside">
						<p>This plugin was written by prinny. It is licensed as Free Software under GPL v2.<br />
						If you like this plugin, please send a donation. This will allow me to further develop the plugin and to provide countless hours of support in the future. Any amount is appreciated!</p>
					</div>
				</div>
				
				<div class="postbox" >
					<h3 class='hndle'><span>Credits and Thanks</span></h3>
					<div class="inside">
						<p>
							Many thanks for those groups and users that help with the testing of this plugin and providing suggestions to improve it as well.<br /><br />
							Scanlation Groups:<br />
							- Sense Scans<br />
							- Kirei Cake<br />
							- Extras<br />
							- FTH Scans
							<br /><br />
							Members/Users:<br />
							- busaway<br />
							- Empathy<br />
							- Lollipop<br />
							- Zyki
						</p>
					</div>
				</div>
				<?php if (current_user_can('manage_options')) { ?>
				<div class="postbox" >
					<h3 class='hndle'><span>Debug and Version Information</span></h3>
					<div class="inside">
						<p>The following will provide you with information regarding software versions. <b>This information must be provided in bug reports.</b><br /><br />
						- Manga Projects (Plugin): <?php echo get_sVersion('plugin'); ?><br />
						- Manga Projects (Database): <?php echo get_sVersion('db'); ?><br />
						- WordPress: <?php echo get_bloginfo('version'); ?><br />
						- PHP: <?php echo phpversion(); ?><br />
						- MySQL Server: <?php echo mysql_get_server_info(); ?>
						</p>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php
}

/* EOF: admin/admin_menu.php */