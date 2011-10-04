jQuery(document).ready(function() {
	jQuery("span[rel]").overlay({
		mask: { color: '#000', loadSpeed: 200, opacity: 0.60 },
		onLoad: function() {
			var command = this.getTrigger().attr('title');
			jQuery('input.xdcc').val(command).focus().select();
		}
	});
});