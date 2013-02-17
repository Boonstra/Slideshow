<?php

$videoId = '';
if(isset($properties['videoId']))
	$videoId = $properties['videoId'];

?>

<li class="widefat sortable-slides-list-item">

	<h3 class="hndle">
		<span>
			<?php _e('Video slide', 'slideshow-plugin'); ?>
		</span>
	</h3>

	<p>

		<i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i><br />
		<input type="text" name="<?php echo $name; ?>[videoId]" value="<?php echo $videoId; ?>" style="width: 100%;" />

	</p>

	<input type="hidden" name="<?php echo $name; ?>[type]" value="video" />

	<p class="slideshow-delete-slide">
		<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
	</p>

</li>