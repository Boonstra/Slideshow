<?php
/**
 * SlideshowPluginSlideshowStylesheet loads the requested stylesheet into the page and returns it as CSS.
 *
 * @since 2.2.8
 * @author Stefan Boonstra
 * @version 03-03-2013
 */
class SlideshowPluginSlideshowStylesheet {

	/**
	 * Called through WordPress' admin-ajax.php script, registered in the SlideshowPluginAjax class. This function
	 * must not be called on itself.
	 *
	 * Uses the loadStylesheet function to load the stylesheet passed in the URL data. If no stylesheet name is set, all
	 * stylesheets will be loaded.
	 *
	 * Headers are set to allow file caching.
	 *
	 * @since 2.2.11
	 */
	public static function loadStylesheetByAjax(){

		$styleName = filter_input(INPUT_GET, 'style', FILTER_SANITIZE_SPECIAL_CHARS);

		// If no style name is set, all stylesheets will be loaded.
		if(isset($styleName) && !empty($styleName) && strlen($styleName) > 0)
			$stylesheet = self::loadStylesheet($styleName);
		else
			return;

		// Set header to CSS. Cache for a year (as WordPress does)
		header('Content-Type: text/css; charset=UTF-8');
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 31556926) . ' GMT');
		header('Pragma: cache');
		header("Cache-Control: public, max-age=31556926");

		echo $stylesheet;

		die;
	}

	/**
	 * Loads the stylesheet with the parsed style name, then returns it.
	 *
	 * @since 2.2.8
	 * @param string $styleName
	 * @return string $stylesheet
	 */
	public static function loadStylesheet($styleName){

		// Get custom stylesheet, of the default stylesheet if the custom stylesheet does not exist
		$stylesheet = get_option($styleName, '');
		if(strlen($stylesheet) <= 0){

			$stylesheetFile = SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'SlideshowPlugin' . DIRECTORY_SEPARATOR . $styleName . '.css';
			if(!file_exists($stylesheetFile)){
				$stylesheetFile = SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'SlideshowPlugin' . DIRECTORY_SEPARATOR . 'style-light.css';
			}

			// Get contents of stylesheet
			ob_start();
			include($stylesheetFile);
			$stylesheet .= ob_get_clean();
		}

		// Replace the '%plugin-url%' tag with the actual URL and add a unique identifier to separate stylesheets
		$stylesheet = str_replace('%plugin-url%', SlideshowPluginMain::getPluginUrl(), $stylesheet);
		$stylesheet = str_replace('.slideshow_container', '.slideshow_container_' . $styleName, $stylesheet);

		return $stylesheet;
	}
}