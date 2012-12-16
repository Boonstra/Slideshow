<?php
/**
 * Class SlideshowPluginUpload provides the code for an upload button that can be used
 * anywhere on a website.
 *
 * @author: Stefan Boonstra
 * @version: 15-09-12
 */
class SlideshowPluginUpload {

	/**
	 * Returns the html for showing the upload button.
	 * Enqueues scripts unless $enqueueFiles is set to false.
	 *
	 * @param boolean $enqueueFiles
	 * @return String $button
	 */
	static function getUploadButton($enqueueFiles = true){
		if($enqueueFiles)
			self::enqueueFiles();

		// Return button html
		ob_start();
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/upload-button.php');
		return ob_get_clean();
	}

	/**
	 * Enqueues styles and scripts necessary for the media upload button.
	 */
	static function enqueueFiles(){
		// Enqueue styles
		wp_enqueue_style('thickbox');

		// Enqueue Wordpress scripts
		wp_enqueue_script('media-upload', false, array(), false, true);
		wp_enqueue_script('thickbox', false, array(), false, true);

		// Enqueue slideshow upload button script
		wp_enqueue_script(
			'slideshow-upload-button',
			SlideshowPluginMain::getPluginUrl() . '/js/' . __CLASS__ . '/upload-button.js',
			array(
				'jquery',
				'media-upload',
				'thickbox'),
			false,
			true
		);
	}
}