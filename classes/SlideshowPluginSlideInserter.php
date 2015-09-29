<?php
/**
 * Class SlideshowPluginSlideInserter
 *
 * @since 2.0.0
 * @author Stefan Boonstra
 */
class SlideshowPluginSlideInserter
{
	/**
	 * Returns a list of element tags, without special characters.
	 *
	 * @since 2.2.20
	 * @return array $elementTags
	 */
	static function getElementTags()
	{
		return array(
			0 => 'div',
			1 => 'p',
			2 => 'h1',
			3 => 'h2',
			4 => 'h3',
			5 => 'h4',
			6 => 'h5',
			7 => 'h6',
		);
	}

	/**
	 * Get a specific element tag by its ID. If no ID is passed, the first value in the element tags array will be
	 * returned.
	 *
	 * @since 2.2.20
	 * @param int $id
	 * @return array $elementTags
	 */
	static function getElementTag($id = null)
	{
		$elementTags = self::getElementTags();

		if (isset($elementTags[$id]))
		{
			return $elementTags[$id];
		}

		return reset($elementTags);
	}

	/**
	 * Enqueues styles and scripts necessary for the media upload button.
	 *
	 * @since 2.2.12
	 */
	static function localizeScript()
	{
		// Return if function doesn't exist
		if (!function_exists('get_current_screen'))
		{
			return;
		}

        // Return when not on a slideshow edit page
        $currentScreen = get_current_screen();

        if ($currentScreen->post_type != SlideshowPluginPostType::$postType)
        {
            return;
        }

		wp_localize_script(
			'slideshow-jquery-image-gallery-backend-script',
			'slideshow_jquery_image_gallery_backend_script_editSlideshow',
			array(
				'data' => array(),
				'localization' => array(
					'confirm'       => __('Are you sure you want to delete this slide?', 'slideshow-jquery-image-gallery'),
					'uploaderTitle' => __('Insert image slide', 'slideshow-jquery-image-gallery')
				)
			)
		);
	}
}