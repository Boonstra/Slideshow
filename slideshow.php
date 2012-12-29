<?php
/*
 Plugin Name: Slideshow
 Plugin URI: http://wordpress.org/extend/plugins/slideshow-jquery-image-gallery/
 Description: The slideshow plugin is easily deployable on your website. Add any image that has already been uploaded to add to your slideshow, add text slides, or even add a video. Options and styles are customizable for every single slideshow on your website.
 Version: 2.1.23
 Requires at least: 3.3
 Author: StefanBoonstra
 Author URI: http://stefanboonstra.com/
 License: GPLv2
*/

/**
 * Class SlideshowPluginMain fires up the application on plugin load and provides some
 * methods for the other classes to use like the auto-includer and the
 * base path/url returning method.
 *
 * @since 1.0.0
 * @author Stefan Boonstra
 * @version 18-12-12
 */
class SlideshowPluginMain {

	/** Variables */
	static $version = '2.1.23';

	/**
	 * Bootstraps the application by assigning the right functions to
	 * the right action hooks.
	 */
	static function bootStrap(){
		self::autoInclude();

		// Initialize localization on init
		add_action('init', array(__CLASS__, 'localize'));

		// For ajax requests
		SlideshowPluginAjax::init();

		// Register slideshow post type
		SlideshowPluginPostType::init();

		// Add general settings page
		SlideshowPluginGeneralSettings::init();

		// Deploy slideshow on do_action('slideshow_deploy'); hook.
		add_action('slideshow_deploy', array('SlideshowPlugin', 'deploy'));

		// Initialize shortcode
		SlideshowPluginShortcode::init();

		// Register widget
		add_action('widgets_init', array('SlideshowPluginWidget', 'registerWidget'));

		// Initialize plugin updater
		SlideshowPluginInstaller::init();
	}

	/**
	 * Translates the plugin
	 */
	static function localize(){
		load_plugin_textdomain(
			'slideshow-plugin',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}

	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @return string pluginUrl
	 */
	static function getPluginUrl(){
		return plugins_url('', __FILE__);
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @return string pluginPath
	 */
	static function getPluginPath(){
		return dirname(__FILE__);
	}

	/**
	 * This function will load classes automatically on-call.
	 */
	function autoInclude(){
		if(!function_exists('spl_autoload_register'))
			return;

		function slideshowPluginAutoLoader($name) {
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';

			if(is_file($file))
				require_once $file;
		}

		spl_autoload_register('slideshowPluginAutoLoader');
	}
}

/**
 * Activate plugin
 */
SlideShowPluginMain::bootStrap();