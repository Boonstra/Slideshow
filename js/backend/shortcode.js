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
				extraData                 = window.slideshow_jquery_image_gallery_backend_script_shortcode;

			if (typeof extraData === 'object')
			{
				if (typeof extraData.data === 'object' &&
					extraData.data.shortcode !== undefined &&
					extraData.data.shortcode.length > 0)
				{
					shortcode = extraData.data.shortcode;
				}

				if (typeof extraData.localization === 'object' &&
					extraData.localization.undefinedSlideshow !== undefined &&
					extraData.localization.undefinedSlideshow.length > 0)
				{
					undefinedSlideshowMessage = extraData.localization.undefinedSlideshow;
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