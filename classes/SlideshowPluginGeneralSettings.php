<?php
/**
 * SlideshowPluginGeneralSettings provides a sub menu page for the slideshow post type. The general settings page is
 * the page that holds most of the slideshow's overall settings, such as user privileges and slideshow defaults.
 *
 * @since 2.1.22
 * @author Stefan Boonstra
 * @version 17-12-12
 */
class SlideshowPluginGeneralSettings {

	/** settingsGroup */
	static $settingsGroup = 'slideshow-jquery-image-gallery-general-settings';

	/** User privilege settings */
	static $addSlideshows = 'slideshow-jquery-image-gallery-add-slideshows';
	static $editOwnSlideshows = 'slideshow-jquery-image-gallery-edit-own-slideshows';
	static $editSlideshows = 'slideshow-jquery-image-gallery-edit-slideshows';
	static $deleteSlideshow = 'slideshow-jquery-image-gallery-delete-slideshows';

	/**
	 * Initializes the slideshow post type's general settings.
	 *
	 * @since 2.1.22
	 */
	static function init(){

		// Only initialize in admin
		if(!is_admin())
			return;

		// Register settings
		add_action('admin_init', array(__CLASS__, 'registerSettings'));

		// Add sub menu
		add_action('admin_menu', array(__CLASS__, 'addSubMenuPage'));
	}

	/**
	 * Adds a sub menu page to the slideshow post type menu.
	 *
	 * @since 2.1.22
	 */
	static function addSubMenuPage(){

		// Return if the slideshow post type does not exist
		if(!post_type_exists(SlideshowPluginPostType::$postType))
			return;

		// Add sub menu
		add_submenu_page(
			'edit.php?post_type=' . SlideshowPluginPostType::$postType,
			__('General Settings', 'slideshow-plugin'),
			__('General Settings', 'slideshow-plugin'),
			'manage_options',
			'general_settings',
			array(__CLASS__, 'generalSettings')
		);
	}

	/**
	 * Registers required settings into the WordPress settings API
	 *
	 * @since 2.1.22
	 */
	static function registerSettings(){

		// Register settings TODO This is probably going to need the callback to handle adding user rights via the WordPress user system. (Easier queryable)
		register_setting(self::$settingsGroup, self::$addSlideshows);
		register_setting(self::$settingsGroup, self::$editOwnSlideshows);
		register_setting(self::$settingsGroup, self::$editSlideshows);
		register_setting(self::$settingsGroup, self::$deleteSlideshow);
	}

	/**
	 * Shows the general settings page.
	 *
	 * @since 2.1.22
	 */
	static function generalSettings(){
		include SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . __CLASS__ . DIRECTORY_SEPARATOR . 'general-settings.php';
	}
}