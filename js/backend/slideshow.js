slideshow_jquery_image_gallery_backend_script.slideshow = function()
{
	var $    = jQuery,
		self = { };

	self.isCurrentPage = false;

	/**
	 *
	 */
	self.init = function()
	{
		if (window.pagenow === 'slideshow')
		{
			self.isCurrentPage = true;
		}
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();

// @codekit-append slideshow.slideManager.js
