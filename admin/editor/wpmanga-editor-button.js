jQuery(document).ready(function(c) {
	el = c("#ed_toolbar");
	if (el) {
		var button = document.createElement("input");
		button.type = "button";
		button.value = "Release";
		button.className = "ed_button";
		button.title = "Insert Release";
		button.id = "ed_button_release";
		el.append(button);
		c("#ed_button_release").click(doButton);
	}
	
	function doButton() {
		var title = "Insert Release";
		var url = sPROJECTS_Button.str_EditorURL + 'admin/editor/wpmanga-editor.php?width=640';

		tb_show(title, url, false);
		jQuery("#TB_ajaxContent").width("auto").height("94.5%").click(function(h) {
			var g=c(h.target);
			if(g.is("a.send_release_to_editor")){
				edInsertContent(edCanvas, "[release id="+g.attr("title")+"]");
				tb_remove();
			}
		return false;
		});
		return false;
	}
});