<?php
	$downloads = get_sReleaseDownloads($release);
	if (get_object_vars($downloads) || $project->reader) {
	
		switch (wpmanga_get('wpmanga_releasebar_style', 1)) {
			case 2:
				$output .= '<div id="release-download-wrapper-style2">';
				$output .= '<span class="release-download-style2-title">';
				
				$output .= "{$project->title} - " . get_sFormatRelease($project, $release);
				if ($release->title != NULL) $output .= "<br>{$release->title}";
				
				$output .= '</span>';
				$output .= '<span class="release-download-style2-icons">';
					foreach ($downloads as $download => $value) {
						$title = str_replace(array('download_depositfiles', 'download_fileserve', 'download_filesonic', 'download_mediafire', 'download_megaupload', 'download_pdf', 'download_irc'), array('Deposit Files', 'FileServe', 'FileSonic', 'MediaFire', 'MEGAUPLOAD', 'PDF', 'IRC'), $download);
						$download = str_replace('download_', 'download-icon-', $download);
						
						if ($value) {
							if ($download == 'download-icon-irc')
								$output .= "&nbsp; <span rel='#download-overlay' title='{$value}'><a><img src='" . plugin_sURL() . "images/{$download}-32.png' title='{$title}'></a></span>";
							else
								$output .= "&nbsp; <a href='{$value}' target='_blank'><img src='" . plugin_sURL() . "images/{$download}-32.png' title='{$title}'></a>";
						}
					}
				
				$output .= '</span>';
				$output .= '</div>';
				$output .= '<br/><br class="wpmanga-clear"/>';
				break;
				
			default:
				$output .= "{$project->title} - " . get_sFormatRelease($project, $release);
				if ($release->title != NULL) $output .= ": {$release->title}";
				
				$output .= '<br>';
				
				$output .= '<div id="release-download-wrapper">';
				
				$output .= "<span><a class='release-download-button' href='#release-{$release->id}' title='{$project->title} - " . get_sFormatRelease($project, $release) . "'> Download</a></span>";
				
				$output .= '<ul class="release-download-icons">';
				
					foreach ($downloads as $download => $value) {
						$title = str_replace(array('download_depositfiles', 'download_fileserve', 'download_filesonic', 'download_mediafire', 'download_megaupload', 'download_pdf', 'download_irc'), array('Deposit Files', 'FileServe', 'FileSonic', 'MediaFire', 'MEGAUPLOAD', 'PDF', 'IRC'), $download);
						$download = str_replace('download_', 'download-icon-', $download);
						
						if ($value) {
							if ($download == 'download-icon-irc')
								$output .= "<li><span rel='#download-overlay' title='{$value}'><a><img src='" . plugin_sURL() . "images/{$download}-32.png' title='{$title}'></a></span></li>";
							else
								$output .= "<li><a href='{$value}' target='_blank'><img src='" . plugin_sURL() . "images/{$download}-32.png' title='{$title}'></a></li>";
						}
					}
				
				if ($project->reader) $output .= '<li><a href="' . get_sReaderLink($project, $release) . '" target="_blank"><img src="' . plugin_sURL() . 'images/download-icon-onlinereader-32.png" title="Read ' . $project->name . ' Online"></a></li>';
				
				$output .= '</ul>';
				
				$output .= '</div>';
				
				$output .= '<br class="wpmanga-clear"/>';
		}

	} else {
		// Display Error!
		$output .= "<del>{$project->title} - " . get_sFormatRelease($project, $release);
		if ($release->title != NULL) $output .= ": {$release->title}";
		$output .= '</del> (Please wait, the downloads for this release have not been added yet.)';
		$output .= '<br><br class="wpmanga-clear">';
	}
?>