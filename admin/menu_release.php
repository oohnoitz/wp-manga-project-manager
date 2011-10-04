<?php

/**
 * Display Administrative Menu for Releases.
 * @return menu
 */
function wpmanga_listReleases() {
	global $wpdb;
	
	$projects = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}projects` ORDER BY `title` ASC");
	
	if ($projects) {
?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>Releases <a href="?page=manga/release" class="add-new-h2">Add a New Release</a></h2>
			
<?php
			foreach ($projects as $project) {
				$releases = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}projects_releases` WHERE `project_id` = '%d' ORDER BY `volume` ASC, `chapter` ASC, `subchapter` ASC, `type` ASC", $project->id));
				
				if ($releases) {
?>
					<br> &nbsp; <a href="admin.php?page=manga/project&action=edit&id=<?php echo $project->id; ?>" style="text-decoration: none; font-weight: bold"><?php echo $project->title; ?></a> &nbsp; <?php if ($project->title_alt) echo "&#12302;{$project->title_alt}&#12303;"; ?>
					<table class="wp-list-table widefat fixed">
						<thead>
							<th scope="col" width="100px">Date</th>
							<th scope="col">Release</th>
							<th scope="col" width="150px">Action</th>
						</thead>
						
						<tbody id="the-list">
							<?php $row = 1; ?>
							<?php foreach ($releases as $release) { ?>
							<tr<?php if ($row % 2) echo ' class="alternate"'; $row++ ?>>
								<td><?php echo date('Y.m.d', $release->unixtime); ?></td>
								<td><?php echo get_sFormatRelease($project, $release); if ($release->title) echo ' - <i>' . $release->title . '</i>'; ?></td>
								<td>
									<a href="admin.php?page=manga/release&action=edit&id=<?php echo $release->id; ?>" title="Edit Release Information">Edit</a> | 
									<a href="admin.php?page=manga/release&action=delete&id=<?php echo $release->id; ?>" title="Delete Release Information">Delete</a> | 
									<a href="<?php echo get_sPermalink($release->project_id); ?>#release-<?php echo $release->id; ?>" title="View Release Information">View</a>
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

/* EOF: admin/menu_release.php */