<!-- #WP Manga Project Output -->

	<!-- #WP Manga Project Details Layout -->

	<script type="text/javascript">
		jQuery(window).load(function() {
			jQuery('#project-container img').css({'opacity': 0.7});
			
			jQuery('#project-container img').mouseover(function() {
				jQuery(this).parent().find('img').stop().animate({opacity:1}, 500);
			});
			
			jQuery('#project-container img').mouseout(function() {
				jQuery(this).stop().animate({opacity: 0.7}, 500);
			});
		});
	</script>
	
<?php
	$project = get_sProject($wp->query_vars["pid"]);
	
	if ($project) {
		
		if (wpmanga_get('wpmanga_page_details_header', 0))	echo "<h2>{$project->title}</h2>";
?>

		<?php if ($project->mature) : ?>
		<div id="project-warning">This project contains mature content and is rated R-18.</div>
		<?php endif; ?>

		<div id="project-container">
			<div class="item">
				<img title="<?php echo $project->title; if ($project->title_alt) echo "&nbsp; &#12302; {$project->title_alt} &#12303;"; ?>" src="<?php echo $project->image; ?>">
				<?php if ($project->mature) : ?><div class="mature">R-18</div><?php endif; ?>
			</div>
			
			<div id="project-details">
				<p>
					<span class="header">Project Status</span><br>
					<span class="description"><?php echo get_sTitleCategory($project->category); ?></span>
				</p>
				<p>
					<span class="header">Author(s) and Artist(s)</span><br>
					<span class="description"><?php if ($project->author) echo $project->author; else echo "N/A"; ?></span>
				</p>
				<p>
					<span class="header">Description</span><br>
					<span class="description"><?php if ($project->description) echo $project->description; else echo "N/A"; ?></span>
				</p>
				<p>
					<span class="header">Genre(s)</span><br>
					<span class="description"><?php if ($project->genre) echo $project->genre; else echo "N/A"; ?></span>
				</p>
				<p>
					<span class="header">Status in Country of Origin</span><br>
					<span class="description"><?php if ($project->status) echo $project->status; else echo "N/A"; ?></span>
				</p>
				<p>
					<span class="header">Links</span><br>
					<span class="description">
					<?php
						if ($project->reader || $project->url) {
							if ($project->url) echo "<a href='{$project->url}' target='_blank'>Manga Updates</a><br>";
							if ($project->reader) echo "<a href='{$project->reader}' target='_blank'>Online Reader</a>";
						} else {
							echo 'N/A';
						}
					?>
					</span>
				</p>
			</div>
		</div>
		
		<div id="release-container">
		
			<?php
				/* Generate Releases with Volume Groups */
				$volumes = get_sProjectVolumes($project->id);
				if (!isset($_volumes)) $_volumes = array();
				if ($volumes) {
					
					foreach ($volumes as $volume) {
			?>
			
						<div class="list">
							<?php
								echo "<div style='float: left; padding: 2px;'><img src='{$volume->image}' title='Volume {$volume->volume}' alt='Volume {$volume->volume}' class='volume'></div>";
								
								$releases = get_sProjectReleasesByVolume($project->id, $volume->volume);
								
								foreach ($releases as $release) {
									$_volumes[$release->id] = true;
									
									echo '<span class="title">';
										echo "<a name='release-{$release->id}'></a>" . date('Y.m.d', $release->unixtime) . ' &nbsp;&nbsp;&nbsp; ';
										echo get_sFormatRelease($project, $release, false);
										if ($release->title) echo " - <i>{$release->title}</i>";
									echo '</span>';
										
									echo '<span class="downloads">';
										$downloads = get_sReleaseDownloads($release);
										foreach ($downloads as $download => $value) {
											$download = str_replace(array('download_depositfiles', 'download_fileserve', 'download_filesonic', 'download_mediafire', 'download_megaupload', 'download_pdf', 'download_irc'), array('DF', 'FSrv', 'FSnc', 'MF', 'MU', 'PDF', 'IRC'), $download);
											
											if ($value) {
												if ($download == 'IRC')
													echo "&nbsp; <span rel='#download-overlay' title='{$value}'><a>{$download}</a></span>";
												else
													echo "&nbsp; <a href='{$value}' target='_blank'>{$download}</a>";
											}
										}
										
										if ($project->reader) echo '&nbsp; <a href="' . get_sReaderLink($project, $release) . '" target="_blank">RO</a>';
									echo '</span>';
									echo '<br>';
								}
							?>
							<div class="footer"><span>Volume <?php echo $volume->volume; ?></span></div>
						</div>
			
			<?php
					}
				}

				/* Generate Remaining Releases */
				$releases = get_sProjectReleases($project->id);
			?>
			
				<div class="list">
					<?php
						foreach ($releases as $release) {
							if (!array_key_exists($release->id, $_volumes)) {
								echo '<span class="title">';
									echo "<a name='release-{$release->id}'></a>" . date('Y.m.d', $release->unixtime) . ' &nbsp;&nbsp;&nbsp; ';
									echo get_sFormatRelease($project, $release, false);
									if ($release->title) echo " - <i>{$release->title}</i>";
								echo '</span>';
										
								echo '<span class="downloads">';
									$downloads = get_sReleaseDownloads($release);
									foreach ($downloads as $download => $value) {
										$download = str_replace(array('download_depositfiles', 'download_fileserve', 'download_filesonic', 'download_mediafire', 'download_megaupload', 'download_pdf', 'download_irc'), array('DF', 'FSrv', 'FSnc', 'MF', 'MU', 'PDF', 'IRC'), $download);
										
										if ($value) {
											if ($download == 'IRC')
												echo "&nbsp; <span rel='#download-overlay' title='{$value}'><a>{$download}</a></span>";
											else
												echo "&nbsp; <a href='{$value}' target='_blank'>{$download}</a>";
										}
									}
									
									if ($project->reader) echo '&nbsp; <a href="' . get_sReaderLink($project, $release) . '" target="_blank">RO</a>';
								echo '</span>';
								echo '<br class="clear">';
							}
						}
					?>
					<div class="footer"><span></span></div>
				</div>
				
		</div>
		
<?php
	} else {
?>

		<p><b>Error : 404 Page Not Found!</b><br><br>The page you attempted to access could not be found. It has either been moved, renamed, deleted, or never existed.</p>
		<script type="text/javascript">
			//window.location = "<?php echo get_bloginfo('siteurl') . '/projects/'; ?>";
		</script>
<?php
	}
?>

	<!-- #END WP Manga Project Details Layout -->

<!-- #END WP Manga Project Output -->