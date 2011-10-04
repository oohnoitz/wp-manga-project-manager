jQuery(document).ready(function($) {
	function release_tinymce_display(url) {
		var title = 'Insert Release';
		var url = url + '/wpmanga-editor.php?width=640';
		
		tb_show(title, url, false);
		jQuery("#TB_ajaxContent").width("auto").height("94.5%").click(function(event) {
			var $target = $(event.target);
			if($target.is("a.send_release_to_editor")){
				tinyMCE.execCommand( 'mceInsertContent', 0, "[release id=" + $target.attr("title") + "]");
				tb_remove();
			}
			return false;
		});;
		return false;
	}

	tinymce.create('tinymce.plugins.release', {
		init : function(ed, url) {
			ed.addCommand('mceRelease', function() {
				release_tinymce_display(url);
			});
			
			ed.addButton('release', {
				title: 'Insert Release',
				cmd: 'mceRelease',
				image: url + '/wpmanga-icon.png'
			});
			
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('release', n.nodeName == 'IMG');
			});
		},
		
		createControl : function(n, cm) {
			return null;
		},
		
		getInfo : function() {
			return {
				longname : 'Project Manager Plugin - Release Addon',
				author : 'prinny',
				authorurl : 'http://localhost/',
				version : "0.2"
			};
		}
	});
	
	tinymce.PluginManager.add('release', tinymce.plugins.release);
})();