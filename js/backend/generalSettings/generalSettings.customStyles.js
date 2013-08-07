jQuery(document).bind('slideshowBackendReady', function()
{
	var $    = jQuery,
		self = slideshow_jquery_image_gallery_backend_script;

	/**
	 * Show chosen style editor and hide all others when clicked on style-action. Delete chosen style when clicked on
	 * style-delete
	 */
	jQuery(
		'.custom-styles-tab .styles-list .style-action,' +
			'.custom-styles-tab .styles-list .style-delete,' +
			'.custom-styles-tab .custom-style-templates .custom-styles-list-item .style-action,' +
			'.custom-styles-tab .custom-style-templates .custom-styles-list-item .style-delete'
	).click(function(){

			// Get custom style key
			var customStyleKey = jQuery(this).attr('class').split(' ')[1];

			// Return if no style key was found
			if(customStyleKey == undefined)
				return;

			// Show
			if(jQuery(this).hasClass('style-action')){

				// Fade editors out
				jQuery('.custom-styles-tab .style-editors .style-editor').each(function(){
					jQuery(this).fadeOut(200);
				});

				// Fade active editor in
				setTimeout(
					function(){
						jQuery('.style-editor.' + customStyleKey).fadeIn(200);
					},
					200
				);
			}

			// Delete
			else if(jQuery(this).hasClass('style-delete')){

				// Exit when the general settings variables is not present
				var confirmDeleteMessage = 'Are you sure you want to delete this custom style?';
				if( typeof GeneralSettingsVariables != 'undefined' &&
					GeneralSettingsVariables.confirmDeleteMessage != undefined &&
					GeneralSettingsVariables.confirmDeleteMessage != '')
					confirmDeleteMessage = GeneralSettingsVariables.confirmDeleteMessage;

				// Show confirm deletion message
				if(!confirm(confirmDeleteMessage))
					return;

				// Delete custom style
				jQuery('.custom-styles-tab .style-editors .style-editor.' + customStyleKey).remove();

				// Delete item from list
				jQuery(this).closest('li').remove();
			}
		});

	/**
	 * Create new editor from editor template when a default style needs to be customized.
	 */
	jQuery('.custom-styles-tab .styles-list .style-action.style-default').click(function(){

		// Get the default stylesheet title and content
		var title = jQuery(this).closest('li').find('.style-title').html();
		var content = jQuery(this).closest('li').find('.style-content').html();

		// Prefix title with new, or its translation
		if( typeof GeneralSettingsVariables != 'undefined' &&
			GeneralSettingsVariables.newCustomizationPrefix != undefined &&
			GeneralSettingsVariables.newCustomizationPrefix != '')
			title = GeneralSettingsVariables.newCustomizationPrefix + ' - ' + title;

		// Exit when content is empty
		if(content == '' || content == undefined)
			return;

		// Exit when the general settings variables is not present
		var customStylesKey = 'slideshow-jquery-image-gallery-custom-styles';
		if( typeof GeneralSettingsVariables != 'undefined' &&
			GeneralSettingsVariables.customStylesKey != undefined &&
			GeneralSettingsVariables.customStylesKey != '')
			customStylesKey = GeneralSettingsVariables.customStylesKey;

		// Highest custom style ID
		var highestCustomStyleID = getHighestCustomStyleID();

		// Custom style ID
		var customStyleID = customStylesKey + '_' + (highestCustomStyleID + 1);

		// Clone editor template
		var $editor = jQuery('.custom-styles-tab .custom-style-templates .style-editor').clone();

		// Add class to editor
		$editor.addClass(customStyleID);

		// Add value attributes
		$editor.find('.new-custom-style-title').attr('value', title);
		$editor.find('.new-custom-style-content').html(content);

		// Add name attributes
		$editor.find('.new-custom-style-title').attr('name', customStylesKey + '[' + customStyleID + '][title]');
		$editor.find('.new-custom-style-content').attr('name', customStylesKey + '[' + customStyleID + '][style]');

		// Add editor to DOM
		jQuery('.custom-styles-tab .style-editors').append($editor);

		// Fade editor in
		setTimeout(
			function(){
				$editor.fadeIn(200);
			},
			200
		);

		// Clone custom styles list item
		var $li = jQuery('.custom-styles-tab .custom-style-templates .custom-styles-list-item').clone(true);

		// Prepare
		$li.removeClass('custom-styles-list-item')
		$li.find('.style-title').html(title);
		$li.find('.style-action').addClass(customStyleID);
		$li.find('.style-delete').addClass(customStyleID);

		// Remove 'No custom stylesheets found message'
		jQuery('.custom-styles-tab .styles-list .custom-styles-list .no-custom-styles-found').remove();

		// Add custom styles list item to DOM
		jQuery('.custom-styles-tab .styles-list .custom-styles-list').append($li);
	});

	/**
	 * Returns highest custom style id in existence
	 *
	 * @return highestCustomStyleID
	 */
	function getHighestCustomStyleID(){

		var highestCustomStyleID = 0;

		// Loop through style editors
		jQuery('.custom-styles-tab .style-editors .style-editor').each(function(){

			// Get custom style ID
			var customStyleID = parseInt(jQuery(this).attr('class').split('_').pop());

			// Check if the ID is higher than any previously checked
			if(customStyleID > highestCustomStyleID)
				highestCustomStyleID = customStyleID;
		});

		// Return
		return parseInt(highestCustomStyleID);
	}
});