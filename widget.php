<?php
//create widget kiyoh_review
class kiyoh_review extends WP_Widget {

	function __construct() {
		parent::__construct('kiyoh_review','Kiyoh review', array( 'description' => 'show Kiyoh review'));
	}
	public function widget($args, $instance) {
		extract($args);
		$link_rate 	= $instance['link_rate'];
		$width 		= $instance['width'];
		$height 	= $instance['height'];
		$ssl 		= $instance['ssl'];
		$border 	= $instance['border'];
		$language 	= $instance['language'];

		
		if ($language == "English") {
			$language = ($language == "English") ? 'com' : 'nl';
		}
		$ssl = ($ssl == 'On') ? '' : '&usessl=0';
		$border = ($border == 'On') ? '' : '&border=0';
		echo '<iframe scrolling="no" src="' . $link_rate . $border . $ssl . '" width="' . $width . '" height="' . $height . '" border="0" frameborder="0"></iframe>';
	}
	public function form( $instance ) {
		$link_rate 	= (isset( $instance['link_rate'] )) ? $instance['link_rate'] : '';
		$width 		= (isset( $instance['width'] )) ? $instance['width'] : 210;
		$height 	= (isset( $instance['height'] )) ? $instance['height'] : 217;
		$ssl 		= (isset( $instance['ssl'] )) ? $instance['ssl'] : "On";
		$border 	= (isset( $instance['border'] )) ? $instance['border'] : "On";
		$language 	= (isset( $instance['language'] )) ? $instance['language'] : 'English';

	?>
	<p style="padding: 0 0 10px;">
		<label for="<?php echo $this->get_field_id( 'link_rate' ); ?>">Link rate</label>
		<input id="<?php echo $this->get_field_id( 'link_rate'); ?>" name="<?php echo $this->get_field_name( 'link_rate' ); ?>" value="<?php echo esc_attr($link_rate); ?>" type="text" style="width:100%;" /><br>
	</p>
	<p style="padding: 0 0 10px;">
		<label for="<?php echo $this->get_field_id( 'width' ); ?>">Width(px)</label>
		<input id="<?php echo $this->get_field_id( 'width'); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo esc_attr($width); ?>" type="text" style="width:100%;" /><br>
	</p>
	<p style="padding: 0 0 10px;">
		<label for="<?php echo $this->get_field_id( 'height' ); ?>">Height(px)</label>
		<input id="<?php echo $this->get_field_id( 'height'); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr($height); ?>" type="text" style="width:100%;" /><br>
	</p>
	
	<p style="padding: 0 0 10px;">
		<label for="<?php echo $this->get_field_id( 'ssl' ); ?>">SSL</label>
		<select id="<?php echo $this->get_field_id("ssl"); ?>" name="<?php echo $this->get_field_name("ssl"); ?>">
			<option value="On"<?php selected( $instance["ssl"], "On" ); ?>>On</option>
			<option value="Off"<?php selected( $instance["ssl"], "Off" ); ?>>Off</option>
		  </select>
	</p>
	<p style="padding: 0 0 10px;">
		<label for="<?php echo $this->get_field_id( 'border' ); ?>">Border</label>
		<select id="<?php echo $this->get_field_id("border"); ?>" name="<?php echo $this->get_field_name("border"); ?>">
			<option value="On"<?php selected( $instance["border"], "On" ); ?>>On</option>
			<option value="Off"<?php selected( $instance["border"], "Off" ); ?>>Off</option>
		  </select>
	</p>
	<p style="padding: 0 0 10px;">
		<label for="<?php echo $this->get_field_id( 'language' ); ?>">Language</label>
		<select id="<?php echo $this->get_field_id("language"); ?>" name="<?php echo $this->get_field_name("language"); ?>">
			<option value="English"<?php selected( $instance["language"], "English" ); ?>>English</option>
			<option value="Dutch"<?php selected( $instance["language"], "Dutch" ); ?>>Dutch</option>
		  </select>
	</p>
	<?php }
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['link_rate']	= strip_tags($new_instance['link_rate']);
		$instance['width']		= strip_tags($new_instance['width']);
		$instance['height'] 	= strip_tags($new_instance['height']);
		$instance['ssl']		= strip_tags($new_instance['ssl']);
		$instance['border']   	= strip_tags($new_instance['border']);
		$instance['language'] 	= strip_tags($new_instance['language']);
		return $instance;
	}
}
?>