<?php
/**
 * Class SlideshowPlugin is called whenever a slideshow do_action tag is come across.
 * Responsible for outputting the slideshow's HTML, CSS and Javascript.
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 */
class SlideshowPlugin
{
	/**
	 * Function deploy prints out the prepared html
	 *
	 * @since 1.2.0
	 * @param int $postId
	 */
	static function deploy($postId = null)
	{
		echo self::prepare($postId);
	}

	/**
	 * Function prepare returns the required html and enqueues
	 * the scripts and stylesheets necessary for displaying the slideshow
	 *
	 * Passing this function no parameter or passing it a negative one will
	 * result in a random pick of slideshow
	 *
	 * @since 2.1.0
	 * @param int $postId
	 * @return String $output
	 */
	static function prepare($postId = null)
	{
		$post = null;

		// Get post by its ID, if the ID is not a negative value
		if (is_numeric($postId) &&
			$postId >= 0)
		{
			$post = get_post($postId);
		}

		// Get slideshow by slug when it's a non-empty string
		if ($post === null &&
			is_string($postId) &&
			!is_numeric($postId) &&
			!empty($postId))
		{
			$query = new WP_Query(array(
				'post_type'        => SlideshowPluginPostType::$postType,
				'name'             => $postId,
				'orderby'          => 'post_date',
				'order'            => 'DESC',
				'suppress_filters' => true
			));

			if($query->have_posts())
			{
				$post = $query->next_post();
			}
		}

		// When no slideshow is found, get one at random
		if ($post === null)
		{
			$post = get_posts(array(
				'numberposts'      => 1,
				'offset'           => 0,
				'orderby'          => 'rand',
				'post_type'        => SlideshowPluginPostType::$postType,
				'suppress_filters' => true
			));

			if(is_array($post))
			{
				$post = $post[0];
			}
		}

		// Exit on error
		if($post === null)
		{
			return '<!-- WordPress Slideshow - No slideshows available -->';
		}

		// Log slideshow's problems to be able to track them on the page.
		$log = array();

		// Get slides
		$slides = SlideshowPluginSlideshowSettingsHandler::getSlides($post->ID);

		if (!is_array($slides) ||
			count($slides) <= 0)
		{
			$log[] = 'No slides were found';
		}

		// Get settings
		$settings      = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID);
		$styleSettings = SlideshowPluginSlideshowSettingsHandler::getStyleSettings($post->ID);

		// Only enqueue the functional stylesheet when the 'allStylesheetsRegistered' flag is false
		if (!SlideshowPluginSlideshowStylesheet::$allStylesheetsRegistered)
		{
			wp_enqueue_style(
				'slideshow-jquery-image-gallery-stylesheet_functional',
				SlideshowPluginMain::getPluginUrl() . '/style/' . __CLASS__ . '/functional.css',
				array(),
				SlideshowPluginMain::$version
			);
		}

		// Check if requested style is available. If not, use the default
		list($styleName, $styleVersion) = SlideshowPluginSlideshowStylesheet::enqueueStylesheet($styleSettings['style']);

		$data               = new stdClass();
		$data->log          = $log;
		$data->post         = $post;
		$data->slides       = $slides;
		$data->settings     = $settings;
		$data->styleName    = $styleName;
		$data->styleVersion = $styleVersion;

		// Include output file to store output in $output.
		$output = SlideshowPluginMain::getView(__CLASS__ . DIRECTORY_SEPARATOR . 'slideshow.php', $data);

		// Enqueue slideshow script
		wp_enqueue_script(
			'slideshow-jquery-image-gallery-script',
			SlideshowPluginMain::getPluginUrl() . '/js/min/all.frontend.min.js',
			array('jquery'),
			SlideshowPluginMain::$version
		);

		// Set dimensionWidth and dimensionHeight if dimensions should be preserved
		if (isset($settings['preserveSlideshowDimensions']) &&
			$settings['preserveSlideshowDimensions'] == 'true')
		{
			$aspectRatio = explode(':', $settings['aspectRatio']);

			// Width
			if (isset($aspectRatio[0]) &&
				is_numeric($aspectRatio[0]))
			{
				$settings['dimensionWidth'] = $aspectRatio[0];
			}
			else
			{
				$settings['dimensionWidth'] = 1;
			}

			// Height
			if (isset($aspectRatio[1]) &&
				is_numeric($aspectRatio[1]))
			{
				$settings['dimensionHeight'] = $aspectRatio[1];
			}
			else
			{
				$settings['dimensionHeight'] = 1;
			}
		}

		if (!SlideshowPluginGeneralSettings::getEnableLazyLoading())
		{
			// Include slideshow settings by localizing them
			wp_localize_script(
				'slideshow-jquery-image-gallery-script',
				'SlideshowPluginSettings_' . $post->ID,
				$settings
			);

			// Include the location of the admin-ajax.php file
			wp_localize_script(
				'slideshow-jquery-image-gallery-script',
				'slideshow_jquery_image_gallery_script_adminURL',
				admin_url()
			);
		}

		// Return output
		return $output;
	}
}