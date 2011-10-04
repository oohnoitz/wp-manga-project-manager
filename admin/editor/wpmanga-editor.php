<?php
	// Load WP Environment
	$wp_environment = '../wp-blog-header.php'; $depth = 0;
	while (!file_exists($wp_environment) && $depth++ <= 15) { $wp_environment = '../' . $wp_environment; }
	require($wp_environment);
	
	// Send Header Response
	header("HTTP/1.1 200 OK");
	header("Status: 200 OK");
?>
<script type="text/javascript" src="<?php echo plugin_sURL() . 'admin/assets/jquery-tablesorter.js'; ?>"></script>
<script>
	jQuery(document).ready(function() {
		jQuery("#releases_editor").tablesorter({
			headers: {
				2: {sorter: false}
			}
		});
	});
</script>
<div class="wrap">
	<h2>List of Releases</h2>
	<div id="poststuff">
		<div style="clear: both;"><p>
			This is a list of all releases available in our database. You may insert a release into a post or page here. Click the "Insert" link after the desired release and the corresponding shortcode will be inserted into the editor (<strong>[release id=&lt;ID&gt;]</strong>).
		</p></div>
		<div style="clear: both;">
		<table id="releases_editor" class="widefat tablesorter">
			<thead>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Release</th>
					<th scope="col">Action</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Release</th>
					<th scope="col">Action</th>
				</tr>
			</tfoot>
			<tbody>
				<?php
					$releases = get_sListLatest(0);
					foreach ($releases as $release) {
						$project = get_sProject($release->project_id, false);
				?>
				<tr>
					<th scope="row"><?php echo $release->id; ?></th>
					<td style="vertical-align: inherit;"><?php echo $project->title . ' - ' . get_sFormatRelease($project, $release); ?></td>
					<td style="vertical-align: inherit;"><a class="send_release_to_editor" title="<?php echo $release->id; ?>" href="#" style="color: rgb(33, 117, 155);">Insert</a></td>
				</tr>
				<?php
					}
				?>
			</tbody>
		</table>
		</div>
	</div>
</div>