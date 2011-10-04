<?php

/**
 * Adds a widget to display the latest releases made in the dashboard.
 * @return widget
 */
add_action('wp_dashboard_setup', 'add_sDashboard');
function add_sDashboard() {
	wp_add_dashboard_widget('Latest_Release_WidgetD', 'Latest Releases', 'dashboard_sLatestWidgetD');
}

/**
 * Generate the latest widget for the dashboard.
 * @return widget
 */
function dashboard_sLatestWidgetD() {
?>
<div id="the-comment-list" class="list:comment">
	<?php $releases = get_sListLatest(5); ?>
	<?php if ( $releases != NULL ) { ?>
		<?php foreach ( $releases as $release ) { ?>
		<?php $project = get_sProject($release->project_id, false); ?>
	<div id="comment-87" class="comment byuser comment-author-zyki odd alt thread-odd thread-alt depth-1 comment-item approved">
		<img src="<?php echo get_sThumbnail('60x60', $project->image); ?>" class='avatar avatar-50 photo' />
		<div class="dashboard-comment-wrap">
			<h4 class="comment-meta">
				<?php echo $project->title; ?> - <i><?php echo get_sFormatRelease($project, $release); ?></i>
			</h4>
			<blockquote><p>Released: <?php if ($release->revision > 1) echo get_sDuration($release->unixtime_mod); else echo get_sDuration($release->unixtime); ?></p></blockquote>
			<p class="row-actions">
				<a href="admin.php?page=manga/release&action=edit&id=<?php echo $release->id; ?>" title="Edit this Release">Edit</a> | <a href="<?php echo get_sPermalink($project->id); ?>#release-<?php echo $release->id; ?>" title="<?php echo $release->title; ?>">View</a>
			</p>

		</div>
	</div>
		<?php } ?>
	<?php }	else { ?>
		<li>
			<img src="<?php bloginfo('template_directory'); ?>/images/no-image-60px.png" /><?php echo $project->name; ?></a>
			<p>
				<i>No Releases.</i>
				<span class="popular-post-date">Pending...</span>
			</p>
		</li>
	<?php } ?>
	<?php wp_reset_query(); ?>
</div>
<?php
}
 
/* EOF: includes/wp_dashboard.php */