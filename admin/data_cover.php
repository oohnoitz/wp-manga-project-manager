<?php

/**
 * Display Administrative Menu for Covers.
 * @return menu
 */
function wpmanga_dataCover() {
	global $wpdb;
	
	// Action Variable
	if (isset($_GET['action']))
		$action = strtolower($_GET['action']);
	else
		$action = 'add';
		
	// Sanity Check on EDIT and DEL
	if (!isset($_GET['id']) && $action != 'add')
		$action = 'add';
	
	if ( isset( $_POST['wpmanga_nonce'] ) ) {
		if ( !wp_verify_nonce( $_POST['wpmanga_nonce'], plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ) ) {
			echo '<div class="error"><p>Error: Security Verification Failed.</p></div>';
		} else {
			$_POST = array_map('trim', $_POST);
			$_POST = array_map('stripslashes', $_POST);
			
			if ( $_POST['project_id'] ) {
				$data = array('project_id' => $_POST['project_id'], 'volume' => $_POST['volume'], 'image' => $_POST['image']);
				
				switch ($action) {
					case 'edit':
						$status = $wpdb->update($wpdb->prefix . 'projects_volumes', $data, array('id' => $_GET['id']));
						
						if ($status)
							echo '<div class="updated"><p>Updated Volume Cover Information.</p></div>';
						else
							echo '<div class="error"><p>Error: Failed to update information.</p></div>';
						break;
						
					case 'delete':
						$status = $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}projects_volumes` WHERE `id` = '%d'", $_GET['id']));
						
						if ($status)
							echo '<div class="updated"><p>Deleted Volume Cover Information.</p></div>';
						else
							echo '<div class="error"><p>Error: Failed to delete information.</p></div>';
						break;
				
					default:
						$wpdb->insert($wpdb->prefix . 'projects_volumes', $data);
						
						if ( $wpdb->insert_id )
							echo '<div class="updated"><p>Added Volume Cover for Releases. <a href="admin.php?page=manga/volume&action=edit&id=' . $wpdb->insert_id . '">Edit Cover Information</a></p></div>';
						else
							echo '<div class="error"><p>Error: Failed to add new cover.</p></div>';
				}
			} else {
				echo '<div class="error"><p>Error: Please fill in the required fields.</p></div>';
			}
		}
	}
	
	$projects = $wpdb->get_results("SELECT `id`, `title` FROM `" . $wpdb->prefix . "projects` ORDER BY `title` ASC");
	
	if ( $projects ) {
		if (preg_match("/(edit|delete)/i", $action))
			$cover = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_volumes` WHERE `id` = '%d' LIMIT 1", $_GET['id']));
?>
		<div class="wrap">
			<?php screen_icon('edit'); ?>
			<h2><?php echo ucfirst($action); ?> Volume Cover</h2>
			
			<p>Add a new cover for a volume.</p>
<?php
			switch ($action) {
				case 'edit':
					echo '<form method="post" action="admin.php?page=manga/volume&action=edit&id=' . $_GET['id'] . '">';
					break;
				
				case 'delete':
					echo '<form method="post" action="admin.php?page=manga/volume&action=delete&id=' . $_GET['id'] . '">';
					break;
				
				default:
					echo '<form method="post" action="admin.php?page=manga/volume">';
			}
?>
				<table class="form-table">
					<tr class="form-field">
						<th scope="row"><label for="project_id">Project</label></th>
						<td>
							<select name="project_id" id="project_id" style="width: 460px">
								<?php
									foreach ($projects as $project) {
										if (preg_match("/(edit|delete)/i", $action)) {
											if ($project->id == $cover->project_id)
												echo "<option value='{$project->id}' selected='selected'>{$project->title}</option>";
											else
												echo "<option value='{$project->id}'>{$project->title}</option>";
										} else {
											echo "<option value='{$project->id}'>{$project->title}</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					
					<tr class="form-field">
						<th scope="row"><label for="volume">Volume</label></th>
						<td><input name="volume" id="volume" type="number" value="<?php if (isset($cover)) echo $cover->volume; else echo '0'; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?> autofocus></td>
					</tr>
					
					<tr class="form">
						<th scope="row"><label for="image">Image <span class="description">(required)</span></label></th>
						<td><input id="image" type="url" name="image" size="66" placeholder="Enter an URL or upload an image cover for this volume." value="<?php if (isset($cover)) echo $cover->image; ?>"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?> required><input id="<?php if ($action != 'delete') echo 'upload_image_button'; ?>" type="button" value="Upload Image"<?php if ($action == 'delete') echo ' readonly="readonly"'; ?>></td>
					</tr>
				</table>
				
				<p class="submit">
					<input type="submit" class="button-primary" name="save" value="<?php echo ucfirst($action); ?> Volume Cover" />
					<input type="hidden" name="wpmanga_nonce" value="<?php echo wp_create_nonce( plugin_basename( plugin_sDIR() . '/wpmanga.php' ) ); ?>" />
				</p>
			</form>
		</div>
<?php
	} else {
?>
		<script type="text/javascript">
			location.replace("admin.php?page=manga/project")
		</script>
<?php
	}
}

/* EOF: admin/data_cover.php */