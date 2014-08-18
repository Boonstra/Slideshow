slideshow_jquery_image_gallery_backend_script.shortcode = function()
{
	var $    = jQuery,
		self = { };

	/**
	 *
	 */
	self.init = function()
	{
		self.activateShortcodeInserter();
	};

	/**
	 *
	 */
	self.activateShortcodeInserter = function()
	{
		$('.insertSlideshowShortcodeSlideshowInsertButton').click(function()
		{
			var undefinedSlideshowMessage = 'No slideshow selected.',
				shortcode                 = 'slideshow_deploy',
				slideshowID               = parseInt($('#insertSlideshowShortcodeSlideshowSelect').val(), 10),
				externalData              = window.slideshow_jquery_image_gallery_backend_script_shortcode;

			if (typeof externalData === 'object')
			{
				if (typeof externalData.data === 'object' &&
					externalData.data.shortcode !== undefined &&
					externalData.data.shortcode.length > 0)
				{
					shortcode = externalData.data.shortcode;
				}

				if (typeof externalData.localization === 'object' &&
					externalData.localization.undefinedSlideshow !== undefined &&
					externalData.localization.undefinedSlideshow.length > 0)
				{
					undefinedSlideshowMessage = externalData.localization.undefinedSlideshow;
				}
			}

			if (isNaN(slideshowID))
			{
				alert(undefinedSlideshowMessage);

				return false;
			}

			send_to_editor('[' + shortcode + ' id=\'' + slideshowID + '\']');

			tb_remove();

			return true;
		});

		$('.insertSlideshowShortcodeCancelButton').click(function()
		{
			tb_remove();

			return false;
		});
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();