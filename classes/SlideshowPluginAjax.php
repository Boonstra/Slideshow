<?php
/**
 * Class SlideshowAjax is used to register ajax functions
 * as soon as possible, so they leave a light footprint.
 *
 * @since 2.0.0
 * @author: Stefan Boonstra
 * @version: 03-03-13
 */
class SlideshowPluginAjax {

	/**
	 * Called as early as possible to be able to have as light as possible AJAX requests. Hooks can be added here as to
	 * have early execution.
	 *
	 * @since 2.0.0
	 */
	static function init() {
		add_action('wp_ajax_slideshow_slide_inserter_search_query', array('SlideshowPluginSlideInserter', 'printSearchResults'));

		add_action('wp_ajax_slideshow_jquery_image_gallery_load_stylesheet', array('SlideshowPluginSlideshowStylesheet', 'loadStylesheetByAjax'));
		add_action('wp_ajax_nopriv_slideshow_jquery_image_gallery_load_stylesheet', array('SlideshowPluginSlideshowStylesheet', 'loadStylesheetByAjax'));
	}
}