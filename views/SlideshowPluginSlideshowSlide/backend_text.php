<?php

$title = $description = $textColor = $color = $url = $target = '';
if(isset($properties['title']))
	$title = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']);
if(isset($properties['description']))
	$description = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']);
if(isset($properties['textColor']))
	$textColor = $properties['textColor'];
if(isset($properties['color']))
	$color = $properties['color'];
if(isset($properties['url']))
	$url = $properties['url'];
if(isset($properties['urlTarget']))
	$target = $properties['urlTarget'];

?>

<li class="widefat sortable-slides-list-item">

	<h3 class="hndle">
		<span>
			<?php _e('Text slide', 'slideshow-plugin'); ?>
		</span>
	</h3>

	<p>

		<i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
		<input type="text" name="<?php echo $name; ?>[title]" value="<?php echo $title; ?>" style="width: 100%;" /><br />

		<i><?php _e('Description', 'slideshow-plugin'); ?></i><br />
		<textarea name="<?php echo $name; ?>[description]" rows="7" cols="" style="width: 100%;"><?php echo $description; ?></textarea><br />

		<i><?php _e('Text color', 'slideshow-plugin'); ?></i><br />
		<input type="text" name="<?php echo $name; ?>[textColor]" value="<?php echo !empty($textColor) ? $textColor : '000000'; ?>" class="color {required:false}" /><br />

		<i><?php _e('Background color', 'slideshow-plugin'); ?></i><br />
		<input type="text" name="<?php echo $name; ?>[color]" value="<?php echo $color; ?>" class="color {required:false}" />

	</p>

	<p>

		<i><?php _e('URL', 'slideshow-plugin'); ?></i><br />
		<input type="text" name="<?php echo $name; ?>[url]" value="<?php echo $url; ?>" style="width: 100%;" /><br />

		<i><?php _e('Open URL in', 'slideshow-plugin'); ?></i>
		<select name="<?php echo $name; ?>[urlTarget]">
			<option value="_self" <?php selected('_self', $target); ?>><?php _e('Same window', 'slideshow-plugin'); ?></option>
			<option value="_blank" <?php selected('_blank', $target); ?>><?php _e('New window', 'slideshow-plugin'); ?></option>
		</select>

	</p>

	<input type="hidden" name="<?php echo $name; ?>[type]" value="text" />

	<p class="slideshow-delete-slide">
		<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
	</p>

</li>