<?php if ($data instanceof stdClass) : ?>

	<p style="text-align: center; font-style: italic"><?php _e('Insert', 'slideshow-jquery-image-gallery'); ?>:</p>
	<p style="text-align: center;">
		<input type="button" class="button slideshow-insert-image-slide" value="<?php _e('Image slide', 'slideshow-jquery-image-gallery'); ?>" />
		<input type="button" class="button slideshow-insert-text-slide" value="<?php _e('Text slide', 'slideshow-jquery-image-gallery'); ?>" />
		<input type="button" class="button slideshow-insert-video-slide" value="<?php _e('Video slide', 'slideshow-jquery-image-gallery'); ?>" />
	</p>

	<p style="text-align: center;">
		<a href="#" class="open-slides-button"><?php _e('Open all', 'slideshow-jquery-image-gallery'); ?></a> |
		<a href="#" class="close-slides-button"><?php _e('Close all', 'slideshow-jquery-image-gallery'); ?></a>
	</p>

	<?php if (count($data->slides) <= 0) : ?>
		<p><?php _e('Add slides to this slideshow by using one of the buttons above.', 'slideshow-jquery-image-gallery'); ?></p>
	<?php endif; ?>

	<div class="sortable-slides-list">

		<?php

		if (is_array($data->slides))
		{
			$i = 0;

			foreach ($data->slides as $slide)
			{
				$data             = new stdClass();
				$data->name       = SlideshowPluginSlideshowSettingsHandler::$slidesKey . '[' . $i . ']';
				$data->properties = $slide;

				SlideshowPluginMain::outputView('SlideshowPluginSlideshowSlide' . DIRECTORY_SEPARATOR . 'backend_' . $slide['type'] . '.php', $data);

				$i++;
			}
		}

		?>

	</div>

	<?php SlideshowPluginMain::outputView('SlideshowPluginSlideshowSlide' . DIRECTORY_SEPARATOR . 'backend_templates.php'); ?>

<?php endif; ?>