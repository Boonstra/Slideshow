/**
 * Slideshow backend script
 *
 * @author Stefan Boonstra
 */
slideshow_jquery_image_gallery_backend_script = function()
{
	var $    = jQuery,
		self = {};

	self.isBackendActivated = false;

	/**
	 * Called by either jQuery's document ready event or JavaScript's window load event in case document ready fails to
	 * fire.
	 *
	 * Triggers the slideshowBackendReady on the document to inform all backend scripts they can start.
	 */
	self.activateBackend = function()
	{
		if (self.isBackendActivated)
		{
			return;
		}

		self.isBackendActivated = true;

		$(document).trigger('slideshowBackendReady');
	};

	$(document).ready(function()
	{
		self.activateBackend();
	});

	$(window).load(function()
	{
		self.activateBackend();
	});

	return self;
}();

// @codekit-append generalSettings.js
