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
			var $this = $(event.currentTarget),
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
		});
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();

// @codekit-append generalSettings.navigation.js
// @codekit-append generalSettings.customStyles.js
