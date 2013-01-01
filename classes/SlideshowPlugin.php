<?php
/**
 * Class SlideslowPlugin is called whenever a slideshow do_action tag is come across.
 * Responsible for outputting the slideshow's HTML, CSS and Javascript.
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 * @version: 06-12-12
 */
class SlideshowPlugin {

	/** int $sessionCounter */
	private static $sessionCounter = 0;

	/**
	 * Function deploy prints out the prepared html
	 *
	 * @since 1.2.0
	 * @param int $postId
	 */
	static function deploy($postId = null){
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
	static function prepare($postId = null){
		// Get post by its ID, if the ID is not a negative value
		if(is_numeric($postId) && $postId >= 0)
			$post = get_post($postId);

		// Get slideshow by slug when it's a non-empty string
		if(is_string($postId) && !is_numeric($postId) && !empty($postId)){
			$query = new WP_Query(array(
				'post_type' => SlideshowPluginPostType::$postType,
				'name' => $postId,
				'orderby' => 'post_date',
				'order' => 'DESC',
				'suppress_filters' => true
			));

			if($query->have_posts())
				$post = $query->next_post();
		}

		// When no slideshow is found, get one at random
		if(empty($post)){
			$post = get_posts(array(
				'numberposts' => 1,
				'offset' => 0,
				'orderby' => 'rand',
				'post_type' => SlideshowPluginPostType::$postType,
				'suppress_filters' => true
			));

			if(is_array($post))
				$post = $post[0];
		}

		// Exit on error
		if(empty($post))
			return '<!-- Wordpress Slideshow - No slideshows available -->';

		// Log slideshow's issues to be able to track them on the page.
		$log = array();

		// Get slides
		$slides = SlideshowPluginSlideshowSettingsHandler::getSlides($post->ID);
		if(!is_array($slides) || count($slides) <= 0)
			$log[] = 'No slides were found';

		// Get settings
		$settings = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID);
		$styleSettings = SlideshowPluginSlideshowSettingsHandler::getStyleSettings($post->ID);

		// Randomize if setting is true.
		if(isset($settings['random']) && $settings['random'] == 'true')
			shuffle($slides);

		// Enqueue functional sheet
		wp_enqueue_style(
			'slideshow_functional_style',
			SlideshowPluginMain::getPluginUrl() . '/style/' . __CLASS__ . '/functional.css',
			array(),
			SlideshowPluginMain::$version
		);

		// The slideshow's session ID, allows JavaScript and CSS to distinguish between multiple slideshows
		$sessionID = self::$sessionCounter++;

		// Get stylesheet. If the style was not found, see if a default stylesheet can be loaded
		$style = get_option($styleSettings['style'], null);
		if(!isset($style)){

			// Check if default stylesheet exists, if not get the light variant
			$filePath = SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR . $styleSettings['style'];
			if(!file_exists($filePath))
				$filePath = SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR . 'style-light.css';

			if(file_exists($filePath)){
				ob_start();
				include($filePath);
				$style = ob_get_clean();
			}
		}

		// Append the random ID to the slideshow container in the stylesheet, to identify multiple slideshows
		if(!empty($style)){

			// Replace URL tag with the site's URL
			$style = str_replace('%plugin-url%', SlideshowPluginMain::getPluginUrl(), $style);

			// Add slideshow's page ID to the CSS container class to differentiate between slideshows
			$style = str_replace('.slideshow_container', '.slideshow_container_' . $sessionID, $style);
		}

		// Include output file to store output in $output.
		$output = '';
		ob_start();
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/slideshow.php');
		$output .= ob_get_clean();

		// Enqueue slideshow script
		wp_enqueue_script(
			'slideshow-jquery-image-gallery-script',
			SlideshowPluginMain::getPluginUrl() . '/js/' . __CLASS__ . '/slideshow.js',
			array(
                'jquery',
                'swfobject'
            ),
			SlideshowPluginMain::$version
		);

		// Include slideshow settings by localizing them
		wp_localize_script(
			'slideshow-jquery-image-gallery-script',
			'SlideshowPluginSettings_' . $sessionID,
			$settings
		);

		// Return output
		return $output;
	}
}