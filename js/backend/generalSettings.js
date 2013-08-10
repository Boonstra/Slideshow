slideshow_jquery_image_gallery_backend_script.generalSettings = function()
{
	var $    = jQuery,
		self = { };

	self.isCurrentPage = false;

	/**
	 *
	 */
	self.init = function()
	{
		if (window.pagenow === 'slideshow_page_general_settings')
		{
			self.isCurrentPage = true;

			self.activateUserCapabilities();
		}
	};

	/**
	 * When either the 'Add slideshows' capability or the 'Delete slideshow' capability is changed, the 'Edit slideshows'
	 * checkbox should also be checked. Un-checking the 'Edit slideshows' checkbox needs to do the opposite.
	 */
	self.activateUserCapabilities = function()
	{
		$('input').change(function(event)
		{
			var $this                      = $(event.currentTarget),
				addSlideshowsCapability    = 'slideshow-jquery-image-gallery-add-slideshows',
				editSlideshowsCapability   = 'slideshow-jquery-image-gallery-edit-slideshows',
				deleteSlideshowsCapability = 'slideshow-jquery-image-gallery-delete-slideshows',
				idArray,
				capability,
				role;

			// Check if the type was a checkbox
			if ($this.attr('type').toLowerCase() != 'checkbox')
			{
				return;
			}

			// Get capability and role
			idArray    = $this.attr('id').split('_');
			capability = idArray.shift();
			role       = idArray.join('_');

			// When 'Edit slideshows' has been un-checked, set 'Add slideshows' and 'Delete slideshows' to un-checked as well
			if (capability === editSlideshowsCapability &&
				!$this.attr('checked'))
			{
				$('#' + addSlideshowsCapability    + '_' + role).attr('checked', false);
				$('#' + deleteSlideshowsCapability + '_' + role).attr('checked', false);
			}
			// When 'Add slideshows' or 'Delete slideshows' is checked, 'Edit slideshows' must be checked as well
			else if (capability === addSlideshowsCapability ||
					 capability === deleteSlideshowsCapability)
			{
				$('#' + editSlideshowsCapability + '_' + role).attr('checked', true);
			}
		});
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();

// @codekit-append generalSettings.navigation.js
// @codekit-append generalSettings.customStyles.js
