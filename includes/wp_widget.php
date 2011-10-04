<?php

/**
 * Adds a widget to display the latest releases.
 * @return widget
 */
add_action('widgets_init', 'load_sLatestWidget');
function load_sLatestWidget() {
	unregister_widget('Latest_Releases_Widget');
	register_widget('Latest_Releases_Widget');
}

/**
 * Extends the widget class to include settings for latest releases.
 * @return widget
 */
class Latest_Releases_Widget extends WP_Widget {
	function Latest_Releases_Widget() {
		$widget_ops = array('classname' => 'latest-releases', 'description' => 'Display Latest Releases (by date).');
		$control_ops = array('width' => 220, 'height' => 350, 'id_base' => 'latest-releases');
		$this->WP_Widget('latest-releases', 'Latest Releases', $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title'] );
		$numofposts = $instance['numofposts'];
		if ($numofposts == "") $numofposts = 3;

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		} else {
			echo $before_title . __('Latest Releases') . $after_title;
		}
		if (file_exists(get_sTemplate('widget-latest-releases.php'))) {
			include(get_sTemplate('widget-latest-releases.php'));
		}
		echo $after_widget;
	}

	function form($instance) {
		$numofposts = $instance['numofposts'];
		if ($numofposts == "") $numofposts = 3;
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('numofposts'); ?>">Number of Releases to show:</label>
			<input type="text" size="3" id="<?php echo $this->get_field_id('numofposts'); ?>" name="<?php echo $this->get_field_name('numofposts'); ?>" value="<?php echo $numofposts; ?>" />
		</p>
	<?php
	}
}

/* EOF: includes/wp_widget.php */