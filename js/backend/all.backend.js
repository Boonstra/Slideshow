/**
 * Slideshow backend script
 *
 * @author Stefan Boonstra
 */
slideshow_jquery_image_gallery_backend_script = function()
{
	var $    = jQuery,
		self = {};

	self.isBackendInitialized = false;

	/**
	 * Called by either jQuery's document ready event or JavaScript's window load event in case document ready fails to
	 * fire.
	 *
	 * Triggers the slideshowBackendReady on the document to inform all backend scripts they can start.
	 */
	self.init = function()
	{
		if (self.isBackendInitialized)
		{
			return;
		}

		self.isBackendInitialized = true;

		$(document).trigger('slideshowBackendReady');
	};

	$(document).ready(self.init);

	$(window).load(self.init);

	return self;
}();

// @codekit-append generalSettings.js
// @codekit-append editSlideshow.js
// @codekit-append shortcode.js
