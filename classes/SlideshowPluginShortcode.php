<?php
/**
 * Class SlideshowPluginShortcode provides the shortcode function, which is called
 * on use of shortcode anywhere in the posts and pages. Also provides the shortcode
 * inserter, so that it's made easier for non-programmers to insert the shortcode
 * into a post or page.
 *
 * @since 1.2.0
 * @author: Stefan Boonstra
 * @version: 06-12-12
 */
class SlideshowPluginShortcode {

	/** Variables */
	public static $shortCode = 'slideshow_deploy';
	public static $bookmark = '!slideshow_deploy!';
	private static $postIds = array();

	/**
	 * Initializes the shortcode, registering it and hooking the shortcode
	 * inserter media buttons.
	 *
	 * @since 2.1.16
	 */
	static function init(){
		// Register shortcode
		add_shortcode(self::$shortCode, array(__CLASS__, 'slideshowDeploy'));

		// Admin
		if(is_admin()){
			// Add shortcode inserter HTML
			add_action('media_buttons',  array(__CLASS__, 'shortcodeInserter'), 11);

			// Enqueue shortcode inserter script
			add_action('admin_enqueue_scripts', array(__CLASS__, 'shortcodeInserterScript'));
		}
	}

	/**
	 * Function slideshowDeploy adds a bookmark to where ever a shortcode
	 * is found and adds the postId to an array, it then is loaded after
	 * Wordpress has done its HTML checks.
	 *
	 * @since 1.2.0
	 * @param mixed $atts
	 * @return String $output
	 */
	static function slideshowDeploy($atts){
		$postId = '';
		if(isset($atts['id']))
			$postId = $atts['id'];

		$output = '';
		$settings = SlideshowPluginSlideshowSettingsHandler::getSettings($postId);
		if($settings['avoidFilter'] == 'true'){
			// Filter content after all Wordpress HTML parsers are done, then replace bookmarks with raw HTML
			add_filter('the_content', array(__CLASS__, 'insertSlideshow'), 999);
			add_filter('the_excerpt', array(__CLASS__, 'insertSlideshow'), 999);

			// Save post id
			self::$postIds[] = $postId;

			// Set output
			$output = self::$bookmark;
		}else{
			// Just output the slideshow, without filtering
			$output = SlideshowPlugin::prepare($postId);
		}

		// Return output
		return $output;
	}

	/**
	 * Function insertSlideshow uses the prepare method of class SlideshowPlugin
	 * to insert the code for the slideshow on the location a bookmark was found.
	 *
	 * @since 2.1.8
	 * @param String $content
	 * @return String $content
	 */
	static function insertSlideshow($content){
		// Loop through post ids
		if(is_array(self::$postIds) && count(self::$postIds) > 0)
			foreach(self::$postIds as $postId){
				$updatedContent = preg_replace("/" . self::$bookmark . "/", SlideshowPlugin::prepare($postId), $content, 1);

				if(is_string($updatedContent))
					$content = $updatedContent;
			}

		// Reset postIds, so a shortcode in a next post can be used
		self::$postIds = array();

		return $content;
	}

	/**
	 * Hooked on the admin's 'media_buttons' hook, outputs the shortcode inserter media button
	 *
	 * @since 2.1.16
	 */
	static function shortcodeInserter(){
		// Get slideshows
		$slideshows = new WP_Query(array(
			'post_type' => SlideshowPluginPostType::$postType,
			'orderby' => 'post_date',
			'order' => 'DESC'
		));

		include(SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR . 'shortcode-inserter.php');
	}

	/**
	 * Enqueues the shortcode inserter script
	 *
	 * @since 2.1.16
	 */
	static function shortcodeInserterScript(){
		wp_enqueue_script(
			'slideshow-shortcode-inserter',
			SlideshowPluginMain::getPluginUrl() . '/js/' . __CLASS__ . '/shortcode-inserter.js',
			array('jquery'),
			SlideshowPluginMain::$version
		);

		wp_localize_script(
			'slideshow-shortcode-inserter',
			'SlideshowShortcodeInserter',
			array(
				'undefinedSlideshowMessage' => __('No slideshow selected.', 'slideshow-plugin'),
				'shortcode' => SlideshowPluginShortcode::$shortCode
			)
		);
	}
}