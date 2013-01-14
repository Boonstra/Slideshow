<?php

$videoId = '';
if(isset($properties['videoId']))
	$videoId = $properties['videoId'];

?>

<li class="widefat sortable-slides-list-item" style="margin: 10px 0; width: auto; background-color: #fafafa;">

	<h3 class="hndle">
		<span style="font-size: 0.8em;">
			<?php _e('Video slide', 'slideshow-plugin'); ?>
		</span>
	</h3>

	<p style="margin: 5px 15px 5px 5px;">

		<i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i><br />
		<input type="text" name="<?php echo $name; ?>[videoId]" value="<?php echo $videoId; ?>" />

	</p>

	<input type="hidden" name="<?php echo $name; ?>[type]" value="video" />

	<p style="margin: 5px 15px 5px 5px; color: red; cursor: pointer;" class="slideshow-delete-slide">
		<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
		<span style="display: none;" class="<?php echo $id; ?>"></span>
	</p>

</li>