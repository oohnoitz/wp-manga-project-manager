<?php

/**
 * Display Administrative Menu for Projects.
 * @return menu
 */
function wpmanga_dataProject() {
	global $wpdb;
	
	// Action Variable
	if (isset($_GET['action']))
		$action = strtolower($_GET['action']);
	else
		$action = 'add';
		
	// Sanity Check on EDIT and DEL
	if (!isset($_GET['id']) && $action != 'add')
		$action = 'add';

	if (isset($_POST['wpmanga_nonce'])) {
		if (!wp_verify_nonce( $_POST['wpmanga_nonce'], plugin_basename(plugin_sDIR() . '/wpmanga.php'))) {
			echo '<div class="error"><p>Error: Security Verification Failed.</p></div>';
		} else {
			$_POST = array_map('trim', $_POST);
			$_POST = array_map('stripslashes', $_POST);
			
			if ($_POST['title']) {
				if ($_POST['image']) {
					$thumbnail = new WP_Http;
					$thumbnail->request(plugin_sURL() . '/includes/generate_thumbnail.php?src=' . $_POST['image'] . '&w=' . wpmanga_get('wpmanga_thumbnail_list_width', 145) . '&h=' . wpmanga_get('wpmanga_thumbnail_list_height', 300));
					$thumbnail->request(plugin_sURL() . '/includes/generate_thumbnail.php?src=' . $_POST['image'] . '&w=60&h=60');
				}
				
				$custom_settings = json_encode(array('chapter' => $_POST['custom_chapter'], 'subchapter' => $_POST['custom_subchapter']));
				$data = array('category' => $_POST['category'], 'slug' => get_sSanitizedSlug($_POST['title']), 'title' => $_POST['title'], 'title_alt' => $_POST['title_alt'], 'description' => $_POST['description'], 'author' => $_POST['author'], 'genre' => $_POST['genre'], 'status' => $_POST['status'], 'image' => $_POST['image'], 'reader' => $_POST['reader'], 'url' => $_POST['url'], 'custom' => $custom_settings);
				
				switch ($action) {
					case 'edit':
						$status = $wpdb->update($wpdb->prefix . 'projects', $data, array('id' => $_GET['id']));
						
						if ($status)
							echo '<div class="updated"><p>Updated Project Information for <i>' . $_POST['title'] . '</i>.</p></div>';
						else
							echo '<div class="error"><p>Error: Failed to update information.</p></div>';
						break;
						
					case 'delete':
						$status = $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}projects` WHERE `id` = '%d'", $_GET['id']));
						$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d'", $_GET['id']));
						
						if ($status)
							echo '<div class="updated"><p>Deleted Project Information for <i>' . $_POST['title'] . '</i>.</p></div>';
						else
							echo '<div class="error"><p>Error: Failed to delete information.</p></div>';
						break;
						
					default:
						$check = $wpdb->query($wpdb->prepare("SELECT `slug` FROM `" . $wpdb->prefix . "projects` WHERE `slug` = '%s'", get_sSanitizedSlug($_POST['title'])));
						if (!$check) {
							$wpdb->insert($wpdb->prefix . 'projects', $data);
					
							if ($wpdb->insert_id)
								echo '<div class="updated"><p>Added <i>' . $_POST['title'] . '</i> to Projects. <a href="admin.php?page=manga/project&action=edit&id=' . $wpdb->insert_id . '">Edit Information</a></p></div>';
							else
								echo '<div class="error"><p>Error: Failed to add new project.</p></div>';
						} else {
							echo '<div class="error"><p>Error: Failed to add new project. (Duplicate Title)</p></div>';
						}
				}
			} else {
				echo '<div class="error"><p>Error: Please fill in the required fields.</p></div>';
			}
		}
	}
	
	if (preg_match("/(edit|delete)/i", $action))
		$project = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects` WHERE `id` = '%d' LIMIT 1", $_GET['id']));
?>
	<div class="wrap">
		<?php screen_icon('edit'); ?>
		<h2><?php echo ucfirst($action); ?> Project Information</h2>
		
		<p>Create a new project and add it to this site.</p>
<?php
		switch ($action) {
			case 'edit':
				echo '<form method="post" action="admin.php?page=manga/project&action=edit&id=' . $_GET['id'] . '">';
				break;
			
			case 'delete':
				echo '<form method="post" action="admin.php?page=manga/project&action=delete&id=' . $_GET['id'] . '">';
				break;
			
			default:
				echo '<form method="post" action="admin.php?page=manga/project">';
		}
?>
			<table class="form-table">
				<tr class="form-field">
					<th scope="row"><label for="category">Category</label></th>
					<td>
						<select name="category" id="category" style="width: 460px">
							<?php
								$categories = get_sListCategories();
								foreach ($categories as $category) {
									if (preg_match("/(edit|delete)/i", $action)) {
										if ($project->category == $category->id)
											echo "<option value='{$category->id}' selected='selected'>{$category->name}</option>";
										else
											echo "<option value='{$category->id}'>{$category->name}</option>";
									} else {
										echo "<option value='{$category->id}'>{$category->name}</option>";
									}
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="title">Title <span class="description">(required)</span></label></th>
					<td><input name="title" id="title" type="text" value="<?php if (isset($project)) echo $project->title; ?>"<?php if ($action == 'delete') echo ' readonly="readonly" '; ?>autofocus required></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="title_alt">Alternative Title</label></th>
					<td><input name="title_alt" id="title_alt" type="text" value="<?php if (isset($project)) echo $project->title_alt; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="author">Author & Artist</label></th>
					<td><input name="author" id="author" type="text" value="<?php if (isset($project)) echo $project->author; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="description">Description</label></th>
					<td><textarea name="description" id="description"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>><?php if (isset($project)) echo $project->description; ?></textarea></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="genre">Genre(s)</label></th>
					<td><textarea name="genre" id="genre"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>><?php if (isset($project)) echo $project->genre; ?></textarea></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="status">Status in Country of Origin</label></th>
					<td><textarea name="status" id="status"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>><?php if (isset($project)) echo $project->status; ?></textarea></td>
				</tr>
				
				<tr class="form">
					<th scope="row"><label for="image">Image</label></th>
					<td><input id="image" type="url" name="image" size="66" placeholder="Enter an URL or upload an image for this project." value="<?php if (isset($project)) echo $project->image; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>><input id="<?php if ($action != 'delete') echo 'upload_image_button'; ?>" type="button" value="Upload Image"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="reader">Online Reader Link</label></th>
					<td><input name="reader" id="reader" type="url" placeholder="http://reader.<?php echo $_SERVER['HTTP_HOST']; ?>/serie/..." value="<?php if (isset($project)) echo $project->reader; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="url">Reference Link</label></th>
					<td><input name="url" id="url" type="url" placeholder="http://mangaupdates.com/series.html?id=..." value="<?php if (isset($project)) echo $project->url; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="custom_chapter">Custom Chapter <span class="description">(optional)</span></label></th>
					<td><input name="custom_chapter" id="custom_chapter" type="text" placeholder="Episode %num%" value="<?php if (isset($project)) echo get_sJSON($project->custom, 'chapter'); ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
				
				<tr class="form-field">
					<th scope="row"><label for="custom_subchapter">Custom Sub-Chapter <span class="description">(optional)</span></label></th>
					<td><input name="custom_subchapter" id="custom_subchapter" type="text" placeholder="Act %num%" value="<?php if (isset($project)) echo get_sJSON($project->custom, 'subchapter'); ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" name="save" value="<?php echo ucfirst($action); ?> Project">
				<input type="hidden" name="wpmanga_nonce" value="<?php echo wp_create_nonce( plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ); ?>">
			</p>
		</form>
	</div>
<?php
}

/* EOF: admin/data_project.php */