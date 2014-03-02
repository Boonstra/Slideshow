<?php

$videoId           = '';
$showRelatedVideos = 'false';

if (isset($properties['videoId']))
{
	$videoId = $properties['videoId'];
}

if (isset($properties['showRelatedVideos']) &&
	$properties['showRelatedVideos'] === 'true')
{
	$showRelatedVideos = 'true';
}

?>

<div class="widefat sortable-slides-list-item postbox">

	<div class="handlediv" title="<?php _e('Click to toggle', 'slideshow-plugin'); ?>"><br></div>

	<div class="hndle">
		<div class="slide-icon video-slide-icon"></div>
		<div class="slide-title">
			<?php _e('Video slide', 'slideshow-plugin'); ?>
		</div>
		<div style="clear: both;"></div>
	</div>

	<div class="inside">

		<p>

			<i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i><br />
			<input type="text" name="<?php echo $name; ?>[videoId]" value="<?php echo $videoId; ?>" style="width: 100%;" />

		</p>

		<p>

			<i><?php _e('Show related videos', 'slideshow-plugin'); ?></i><br />
			<label><input type="radio" name="<?php echo $name; ?>[showRelatedVideos]" value="true" <?php checked('true', $showRelatedVideos); ?>><?php _e('Yes', 'slideshow-plugin'); ?></label>
			<label><input type="radio" name="<?php echo $name; ?>[showRelatedVideos]" value="false" <?php checked('false', $showRelatedVideos); ?>><?php _e('No', 'slideshow-plugin'); ?></label>

		</p>

		<input type="hidden" name="<?php echo $name; ?>[type]" value="video" />

		<p class="slideshow-delete-slide">
			<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
		</p>

	</div>

</div>