<div class="slideshow_container slideshow_container_<?php echo htmlspecialchars($styleName); ?>" style="<?php echo (isset($settings['preserveSlideshowDimensions']) && $settings['preserveSlideshowDimensions'] == 'false' && isset($settings['height']) && $settings['height'] > 0) ? 'height: ' . $settings['height'] . 'px;' : ''; ?> <?php echo (isset($settings['maxWidth']) && $settings['maxWidth'] > 0) ? 'max-width: ' . $settings['maxWidth'] . 'px;' : ''; ?>" data-session-id="<?php echo htmlspecialchars($sessionID); ?>" data-style-name="<?php echo htmlspecialchars($styleName); ?>" data-style-version="<?php echo htmlspecialchars($styleVersion); ?>">

	<?php if(isset($settings['showLoadingIcon']) && $settings['showLoadingIcon'] === 'true'): ?>
		<div class="slideshow_loading_icon"></div>
	<?php endif; ?>

	<div class="slideshow_content" style="display: none;">

		<?php

		if(is_array($views) && count($views) > 0)
		{
			foreach($views as $view)
			{
				echo $view->toFrontEndHTML();
			}
		}

		?>

	</div>

	<div class="slideshow_controlPanel slideshow_transparent" style="display: none;"><ul><li role="button" class="slideshow_togglePlay" data-slideshow-play-text="<?php _e('Play the slideshow', 'slideshow-plugin'); ?>" data-slideshow-pause-text="<?php _e('Pause the slideshow', 'slideshow-plugin'); ?>"></li></ul></div>

	<div class="slideshow_button slideshow_previous slideshow_transparent" role="button" data-slideshow-previous-text="<?php _e('Previous slide', 'slideshow-plugin'); ?>" style="display: none;"></div>
	<div class="slideshow_button slideshow_next slideshow_transparent" role="button" data-slideshow-next-text="<?php _e('Next slide', 'slideshow-plugin'); ?>" style="display: none;"></div>

	<div class="slideshow_pagination" style="display: none;"><div class="slideshow_pagination_center"></div></div>

	<!-- WordPress Slideshow Version <?php echo SlideshowPluginMain::$version; ?> -->

	<?php if(is_array($log) && count($log) > 0): ?>
	<!-- Error log
	<?php foreach($log as $logMessage): ?>
		- <?php echo htmlspecialchars($logMessage); ?>
	<?php endforeach; ?>
	-->
	<?php endif; ?>
</div>