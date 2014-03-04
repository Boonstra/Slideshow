slideshow_jquery_image_gallery_backend_script.generalSettings.customStyles = function()
{
	var $    = jQuery,
		self = { };

	/**
	 *
	 */
	self.init = function()
	{
		if (!slideshow_jquery_image_gallery_backend_script.generalSettings.isCurrentPage)
		{
			return;
		}

		self.activateActionButtons();
		self.activateDeleteButtons();
	};

	/**
	 * Activate action buttons.
	 */
	self.activateActionButtons = function()
	{
		// On click of the customize default style button
		$('.custom-styles-tab .styles-list .style-action.style-default').click(function(event)
		{
			var $this                 = $(event.currentTarget),
				title                 = $this.closest('li').find('.style-title').html(),
				content               = $this.closest('li').find('.style-content').html(),
				externalData          = window.slideshow_jquery_image_gallery_backend_script_generalSettings,
				customStylesKey       = 'slideshow-jquery-image-gallery-custom-styles',
				customStyleID,
				$editor,
				$li,
				$customStyleTemplates,
				$customStylesList;

			if (typeof content !== 'string' ||
				content.length <= 0)
			{
				return;
			}

			if (typeof externalData === 'object')
			{
				// Prefix title with 'New'
				if (typeof externalData.localization === 'object' &&
					externalData.localization.newCustomizationPrefix !== undefined &&
					externalData.localization.newCustomizationPrefix.length > 0)
				{
					title = externalData.localization.newCustomizationPrefix + ' - ' + title;
				}

				// Get custom styles key
				if (typeof externalData.data === 'object' &&
					externalData.data.customStylesKey !== undefined &&
					externalData.data.customStylesKey.length > 0)
				{
					customStylesKey = externalData.data.customStylesKey;
				}
			}

			customStyleID = customStylesKey + '_' + (self.getHighestCustomStyleID() + 1);

			$customStyleTemplates = $('.custom-styles-tab .custom-style-templates');

			// Clone editor template
			$editor =  $customStyleTemplates.find('.style-editor').clone();

			// Add class to editor
			$editor.addClass(customStyleID);

			// Add value attributes
			$editor.find('.new-custom-style-title').attr('value', title);
			$editor.find('.new-custom-style-content').html(content);

			// Add name attributes
			$editor.find('.new-custom-style-title').attr('name', customStylesKey + '[' + customStyleID + '][title]');
			$editor.find('.new-custom-style-content').attr('name', customStylesKey + '[' + customStyleID + '][style]');

			// Add editor to DOM
			$('.custom-styles-tab .style-editors').append($editor);

			// Fade editor in
			setTimeout(
				function()
				{
					$editor.fadeIn(200);
				},
				200
			);

			// Clone custom styles list item (with events)
			$li = $customStyleTemplates.find('.custom-styles-list-item').clone(true);

			// Prepare
			$li.removeClass('custom-styles-list-item');
			$li.find('.style-title').html(title);
			$li.find('.style-action').addClass(customStyleID);
			$li.find('.style-delete').addClass(customStyleID);

			$customStylesList = $('.custom-styles-tab .styles-list .custom-styles-list');

			// Remove 'No custom stylesheets found message'
			$customStylesList.find('.no-custom-styles-found').remove();

			// Add custom styles list item to DOM
			$customStylesList.append($li);
		});

		// On click of the edit custom style button
		$('.custom-styles-tab .styles-list .style-action, .custom-styles-tab .custom-style-templates .custom-styles-list-item .style-action').click(function(event)
		{
			// Get custom style key
			var customStyleKey = $(event.currentTarget).attr('class').split(' ')[1];

			// Return if no style key was found
			if (customStyleKey === undefined)
			{
				return;
			}

			// Fade editors out
			$('.custom-styles-tab .style-editors .style-editor').each(function(key, editor)
			{
				$(editor).fadeOut(200);
			});

			// Fade active editor in
			setTimeout(
				function()
				{
					$('.style-editor.' + customStyleKey).fadeIn(200);
				},
				200
			);
		});
	};

	/**
	 * Activate delete buttons.
	 */
	self.activateDeleteButtons = function()
	{
		$('.custom-styles-tab .styles-list .style-delete, .custom-styles-tab .custom-style-templates .custom-styles-list-item .style-delete').click(function(event)
		{
			// Get custom style key
			var $this                = $(event.currentTarget),
				customStyleKey       = $this.attr('class').split(' ')[1],
				externalData         = window.slideshow_jquery_image_gallery_backend_script_generalSettings,
				confirmDeleteMessage = 'Are you sure you want to delete this custom style?';

			// Return if no style key was found
			if(customStyleKey === undefined)
			{
				return;
			}

			if (typeof externalData === 'object' &&
				typeof externalData.localization === 'object' &&
				externalData.localization.confirmDeleteMessage !== undefined &&
				externalData.localization.confirmDeleteMessage.length > 0)
			{
				confirmDeleteMessage = externalData.localization.confirmDeleteMessage;
			}

			// Show confirm deletion message
			if (!confirm(confirmDeleteMessage))
			{
				return;
			}

			// Delete custom style
			$('.custom-styles-tab .style-editors .style-editor.' + customStyleKey).remove();

			// Delete item from list
			$this.closest('li').remove();
		});
	};

	/**
	 * Returns highest custom style id in existence
	 *
	 * @return int highestCustomStyleID
	 */
	self.getHighestCustomStyleID = function()
	{
		var highestCustomStyleID = 0;

		// Loop through style editors
		$('.custom-styles-tab .style-editors .style-editor').each(function(key, editor)
		{
			var customStyleID = parseInt($(editor).attr('class').split('_').pop(), 10);

			// Check if the ID is higher than any previously checked
			if (customStyleID > highestCustomStyleID)
			{
				highestCustomStyleID = customStyleID;
			}
		});

		// Return
		return parseInt(highestCustomStyleID, 10);
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();