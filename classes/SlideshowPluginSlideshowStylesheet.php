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
	 * Loads the requested stylesheet, outputs it to the page and returns it as a text/css content type.
	 *
	 * @since 2.2.8
	 */
	public static function loadStylesheet(){

		$styleName = filter_input(INPUT_GET, 'style', FILTER_SANITIZE_SPECIAL_CHARS);

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

		// Set header to CSS. Cache for a year (as WordPress does)
		header('Content-Type: text/css; charset=UTF-8');
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 31556926) . ' GMT');
		header('Pragma: cache');
		header("Cache-Control: public, max-age=31556926");

		echo $stylesheet;

		die;
	}
}