jQuery(function() {
	jQuery('div#release-download-wrapper').mouseover(function() {
		jQuery(this).children('.release-download-icons').stop().animate({'opacity':'1', 'marginLeft':'10px'}, 100);
		jQuery(this).children('.release-download-icons').css('visibility', 'visible');
	}).mouseout(function() {
		jQuery(this).children('.release-download-icons').stop().animate({'opacity':'0', 'marginLeft':'5px'}, 300);
		jQuery(this).children('.release-download-icons').css('visibility', 'hidden');
	});
});