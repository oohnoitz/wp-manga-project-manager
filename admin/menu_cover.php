<?php

/**
 * Display Administrative Menu for Volume Covers.
 * @return menu
 */
function wpmanga_listCovers() {
	global $wpdb;
	
	$projects = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}projects` ORDER BY `title` ASC");
	
	if ($projects) {
?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>Volume Covers <a href="?page=manga/volume" class="add-new-h2">Add a New Volume Cover</a></h2>
			
<?php
			foreach ($projects as $project) {
				$covers = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_volumes` WHERE `project_id` = '%d' ORDER BY `volume` ASC", $project->id));
				
				if ($covers) {
?>
					<br> &nbsp; <a href="admin.php?page=manga/project&action=edit&id=<?php echo $project->id; ?>" style="text-decoration: none; font-weight: bold"><?php echo $project->title; ?></a> &nbsp; <?php if ($project->title_alt) echo "&#12302;{$project->title_alt}&#12303;"; ?>
					<table class="wp-list-table widefat fixed">
						<thead>
							<th scope="col">Covers</th>
							<th scope="col" width="150px">Action</th>
						</thead>
						
						<tbody id="the-list">
							<?php $row = 1; ?>
							<?php foreach ($covers as $cover) { ?>
							<tr<?php if ($row % 2) echo ' class="alternate"'; $row++ ?>>
								<td>Volume <?php echo $cover->volume; ?></td>
								<td>
									<a href="admin.php?page=manga/volume&action=edit&id=<?php echo $cover->id; ?>" title="Edit Volume Cover Information">Edit</a> | 
									<a href="admin.php?page=manga/volume&action=delete&id=<?php echo $cover->id; ?>" title="Delete Volume Cover Information">Delete</a> | 
									<a href="<?php echo get_sPermalink($cover->project_id); ?>" title="View Project Page">View</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
<?php
				}
			}
?>
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

/* EOF: admin/menu_cover.php */