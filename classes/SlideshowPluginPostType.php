<?php
/**
 * SlideshowPluginPostType creates a post type specifically designed for
 * slideshows and their individual settings
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 * @version: 18-12-12
 */
class SlideshowPluginPostType {

	/** Variables */
	static $postType = 'slideshow';

	/**
	 * Initialize Slideshow post type.
	 * Called on load of plugin
	 *
	 * @since 1.3.0
	 */
	static function init(){
		add_action('init', array(__CLASS__, 'registerSlideshowPostType'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue'));
		add_action('save_post', array('SlideshowPluginSlideshowSettingsHandler', 'save'));
	}

	/**
	 * Registers new posttype slideshow
	 *
	 * @since 1.0.0
	 */
	static function registerSlideshowPostType(){
		register_post_type(
			self::$postType,
			array(
				'labels' => array(
					'name' => __('Slideshows', 'slideshow-plugin'),
					'singular_name' => __('Slideshow', 'slideshow-plugin'),
					'add_new_item' => __('Add New Slideshow', 'slideshow-plugin'),
					'edit_item' => __('Edit slideshow', 'slideshow-plugin'),
					'new_item' => __('New slideshow', 'slideshow-plugin'),
					'view_item' => __('View slideshow', 'slideshow-plugin'),
					'search_items' => __('Search slideshows', 'slideshow-plugin'),
					'not_found' => __('No slideshows found', 'slideshow-plugin'),
					'not_found_in_trash' => __('No slideshows found', 'slideshow-plugin')
				),
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => true,
				'capability_type' => 'post',
				'capabilities' => array(
					'edit_post' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'read_post' => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
					'delete_post' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'edit_posts' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'edit_others_posts' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'publish_posts' => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
					'read_private_posts' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],

					'read' => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
					'delete_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'delete_private_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'delete_published_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'delete_others_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'edit_private_posts' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'edit_published_posts' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
				),
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'menu_icon' => SlideshowPluginMain::getPluginUrl() . '/images/' . __CLASS__ . '/adminIcon.png',
				'supports' => array('title'),
				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
			)
		);
	}

	/**
	 * Enqueues scripts and stylesheets for when the admin page
	 * is a slideshow edit page.
	 *
	 * @since 2.1.11
	 */
	static function enqueue(){
        // Return when not on a slideshow edit page.
		$currentScreen = get_current_screen();
		if($currentScreen->post_type != self::$postType)
			return;

		// Enqueue associating script
		wp_enqueue_script(
			'post-type-handler',
			SlideshowPluginMain::getPluginUrl() . '/js/' . __CLASS__ . '/post-type-handler.js',
			array('jquery'),
			SlideshowPluginMain::$version
		);

		// TODO: These scripts have been moved here from the footer. They need to be always printed in the header
		// TODO: a solution for this needs to be found.
		// Enqueue scripts required for sorting the slides list
		wp_enqueue_script('jquery-ui-sortable');

		// Enqueue JSColor
		wp_enqueue_script(
			'jscolor-colorpicker',
			SlideshowPluginMain::getPluginUrl() . '/js/SlideshowPluginPostType/jscolor/jscolor.js',
			null,
			SlideshowPluginMain::$version
		);

		// Enqueue slide insert script and style
		SlideshowPluginSlideInserter::enqueueFiles();
	}

	/**
	 * Adds custom meta boxes to slideshow post type.
	 *
	 * @since 1.0.0
	 */
	static function registerMetaBoxes(){
		add_meta_box(
			'information',
			__('Information', 'slideshow-plugin'),
			array(__CLASS__, 'informationMetaBox'),
			self::$postType,
			'normal',
			'high'
		);

		add_meta_box(
			'slides-list',
			__('Slides List', 'slideshow-plugin'),
			array(__CLASS__, 'slidesMetaBox'),
			self::$postType,
			'side',
			'default'
		);

		add_meta_box(
			'style',
			__('Slideshow Style', 'slideshow-plugin'),
			array(__CLASS__, 'styleMetaBox'),
			self::$postType,
			'normal',
			'low'
		);

		add_meta_box(
			'settings',
			__('Slideshow Settings', 'slideshow-plugin'),
			array(__CLASS__, 'settingsMetaBox'),
			self::$postType,
			'normal',
			'low'
		);

		// Add support plugin message on edit slideshow
		if(isset($_GET['action']) && strtolower($_GET['action']) == strtolower('edit'))
			add_action('admin_notices', array(__CLASS__,  'supportPluginMessage'));
	}

	/**
	 * Shows the support plugin message
	 *
	 * @since 2.0.0
	 */
	static function supportPluginMessage(){
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/support-plugin.php');
	}

	/**
	 * Shows some information about this slideshow
	 *
	 * @since 1.0.0
	 */
	static function informationMetaBox(){
		global $post;

		$snippet = htmlentities(sprintf('<?php do_action(\'slideshow_deploy\', \'%s\'); ?>', $post->ID));
		$shortCode = htmlentities(sprintf('[' . SlideshowPluginShortcode::$shortCode . ' id=\'%s\']', $post->ID));

		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/information.php');
	}

	/**
	 * Shows slides currently in slideshow
	 *
	 * @since 1.0.0
	 */
	static function slidesMetaBox(){
		global $post;

		// Get slides
		$slides = SlideshowPluginSlideshowSettingsHandler::getSlides($post->ID);

		// Stores highest slide id.
		$highestSlideId = count($slides) - 1;

		// Set url from which a substitute icon can be fetched
		$noPreviewIcon = SlideshowPluginMain::getPluginUrl() . '/images/' . __CLASS__ . '/no-img.png';

		// Include slides preview file
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/slides.php');
	}

	/**
	 * Shows style used for slideshow
	 *
	 * @since 1.3.0
	 */
	static function styleMetaBox(){
		global $post;

		// Get settings
		$settings = SlideshowPluginSlideshowSettingsHandler::getStyleSettings($post->ID, true);

		// Fill custom style with default css if empty
		if(isset($settings['custom']) && isset($settings['custom']['value']) && empty($settings['custom']['value'])){
			ob_start();
			include(SlideshowPluginMain::getPluginPath() . '/style/SlideshowPlugin/style-custom.css');
			$settings['custom']['value'] = ob_get_clean();
		}

		// Include style settings file
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/style-settings.php');
	}

	/**
	 * Shows settings for particular slideshow
	 *
	 * @since 1.0.0
	 */
	static function settingsMetaBox(){
		global $post;

		// Get settings
		$settings = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID, true);

		// Include
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/settings.php');
	}
}