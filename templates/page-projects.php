<!-- #WP Manga Project Output -->

<script type="text/javascript">
	jQuery(window).load(function() {
		jQuery('.item img').mouseover(function() {
			jQuery(this).parent().find('img').stop().animate({opacity:1}, 500);
			project_id = jQuery(this).parent().find('img').attr('id');
			project_information(true, project_id);
		});
		
		jQuery('.item img').mouseout(function() {
			jQuery(this).stop().animate({opacity: 0.7}, 500);
			project_information(false, 0);
		});
	});
	
	function project_information(status, id) {
		if (status) {
			var project = new Array();
			<?php
				$projects = get_sListProject();
				foreach ($projects as $project) {
					$lastRelease = get_sLastRelease($project->id);
					if ($project->image) $project->image = get_sThumbnail('60x60', $project->image);
					unset($project->custom);
					unset($project->hit);
					
					if ($lastRelease) {
						if ($lastRelease->revision > 1)
							$project->last_release_time = get_sDuration($lastRelease->unixtime_mod);
						else
							$project->last_release_time = get_sDuration($lastRelease->unixtime);
						$project->last_release = get_sFormatRelease($project, $lastRelease);
						$project->last_release_title = $lastRelease->title;
					} else {
						$project->last_release = '';
						$project->last_release_title = '';
						$project->last_release_time = 'No Releases Yet!';
					}
					
					echo "project[{$project->id}] = [" . json_encode($project) . "];";
				}
			?>
			
			var output = 'Project Details';
			if (project[id][0].title != "") {	output = '<b>' + project[id][0].title + '</b>'; }
			if (project[id][0].title_alt != "") {	output = output + '<br />&#12302; ' + project[id][0].title_alt + ' &#12303;<br />'; }
			if (project[id][0].last_release != "") { output = output + '<br />Latest Release: ' + project[id][0].last_release; }
			if (project[id][0].last_release_title != "") { output = output + '<br /><i>' + project[id][0].last_release_title + '</i>'; }
			if (project[id][0].last_release_time != "") { output = output + '<br /><font size="1">(' + project[id][0].last_release_time + ')</font>'; }
			
			jQuery("#projects-tooltip").html(output).show();
		} else {
			jQuery("#projects-tooltip").html('').hide();
		}
	}
</script>

<?php
	$categories = get_sListCategories();
	$thumbnail_width = wpmanga_get('wpmanga_thumbnail_list_width', 145);
	$thumbnail_height = wpmanga_get('wpmanga_thumbnail_list_height', 300);
	foreach ($categories as $category) {
		$projects = get_sListCategory($category->id);
		if ($projects) {
			echo "<h2>{$category->description}</h2>";
			echo '<div id="projects-wrapper">';
			
			foreach ($projects as $project) {
				echo "<div class='item'><a href='" . get_sPermalink($project) . "'><img src='" . get_sThumbnail($thumbnail_width . "x" . $thumbnail_height, $project->image) . "' width='{$thumbnail_width}' height='{$thumbnail_height}' id='{$project->id}' title='{$project->name}' alt='{$project->name}'></a></div>";
			}
			
			echo '</div><br class="wpmanga-clear"><br>';
		}
	}
?>

<div id="projects-tooltip"></div>

<!-- #END WP Manga Project Output -->