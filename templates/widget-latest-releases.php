<ul class="latest-release-list">
	<?php
		$releases = get_sListLatest($numofposts);
		
		if ($releases) {
			$icons = wpmanga_get('wpmanga_widget_icons', 0);
		
			foreach ($releases as $release) {
				$project = get_sProject($release, false);
				
				echo '<li>';
					if ($icons) echo "<a href='" . get_sPermalink($project) . "' title='{$project->title}'><img src='" . get_sThumbnail('60x60', $project->image) . "' width='60' height='60' style='float: left; margin-right: 5px;' class='project-icon-thumbnail'></a>";
					
					echo "<p><a href='" . get_sPermalink($project) . "#release-{$release->id}' title='{$release->title}'>{$project->title} - " . get_sFormatRelease($project, $release, false) . "</a><br><span class='latest-release-date'>";
					if ($release->revision > 1)
						echo get_sDuration($release->unixtime_mod);
					else
						echo get_sDuration($release->unixtime);
				if ($icons)
					echo '</span></p><span class="wpmanga-clear"></span></li>';
				else
					echo '</span></p></li>';
			}
		} else {
			echo '<li>No Releases</li>';
		}
	?>
</ul>