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
		$('.general-settings-capability-checkbox').change(function(event)
		{
			var $this                            = $(event.currentTarget),
				addSlideshowsCapability          = 'slideshow-jquery-image-gallery-add-slideshows',
				editSlideshowsCapability         = 'slideshow-jquery-image-gallery-edit-slideshows',
				deleteSlideshowsCapability       = 'slideshow-jquery-image-gallery-delete-slideshows',
				addSettingsProfilesCapability    = 'slideshow-jquery-image-gallery-add-settings-profiles',
				editSettingsProfilesCapability   = 'slideshow-jquery-image-gallery-edit-settings-profiles',
				deleteSettingsProfilesCapability = 'slideshow-jquery-image-gallery-delete-settings-profiles',
				idArray,
				capability,
				dependency,
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

			// If this checkbox was checked, check all boxes it depends on
			if ($this.attr('checked'))
			{
				dependency = $this.attr('data-depends-on');

				$('#' + dependency + '_' + role).attr('checked', true);
			}
			// If this checkbox was unchecked, uncheck all checkboxes that depend on it
			else
			{
				$('[data-depends-on="' + capability + '"][data-role="' + role + '"]').attr('checked', false);
			}

//			// When 'Edit slideshows' has been un-checked, set 'Add slideshows' and 'Delete slideshows' to un-checked as well
//			if (capability === editSlideshowsCapability &&
//				!$this.attr('checked'))
//			{
//				$('#' + addSlideshowsCapability    + '_' + role).attr('checked', false);
//				$('#' + deleteSlideshowsCapability + '_' + role).attr('checked', false);
//			}
//			// When 'Add slideshows' or 'Delete slideshows' is checked, 'Edit slideshows' must be checked as well
//			else if (capability === addSlideshowsCapability ||
//					 capability === deleteSlideshowsCapability)
//			{
//				$('#' + editSlideshowsCapability + '_' + role).attr('checked', true);
//			}
//
//			// When 'Edit settings profiles' has been un-checked, set 'Add settings profiles' and 'Delete settings profiles' to un-checked as well
//			if (capability === editSettingsProfilesCapability &&
//				!$this.attr('checked'))
//			{
//				$('#' + addSettingsProfilesCapability    + '_' + role).attr('checked', false);
//				$('#' + deleteSettingsProfilesCapability + '_' + role).attr('checked', false);
//			}
//			// When 'Add settings profiles' or 'Delete settings profiles' is checked, 'Edit settings profiles' must be checked as well
//			else if (capability === addSettingsProfilesCapability ||
//				capability === deleteSettingsProfilesCapability)
//			{
//				$('#' + editSettingsProfilesCapability + '_' + role).attr('checked', true);
//			}
		});
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();

// @codekit-append generalSettings.navigation.js
// @codekit-append generalSettings.customStyles.js
