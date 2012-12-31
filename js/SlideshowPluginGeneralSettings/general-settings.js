jQuery(document).ready(function(){

	/**
	 * ==== Navigation ====
	 *
	 * On click of navigation tab, show different settings page.
	 */
	jQuery('.nav-tab').click(function(){

		// Tab references
		var activeTab = jQuery('.nav-tab-active');
		var thisTab = jQuery(this);

		// Set active navigation tab
		activeTab.removeClass('nav-tab-active');
		thisTab.addClass('nav-tab-active');

		// Hide previously active tab's content
		jQuery(activeTab.attr('href').replace('#', '.')).hide();

		// Show newly activate tab
		jQuery(thisTab.attr('href').replace('#', '.')).show();

		// Set referrer value to the current page to be able to return there after saving
		var referrer = jQuery('input[name=_wp_http_referer]');
		referrer.attr('value', referrer.attr('value').split('#').shift() + thisTab.attr('href'));
	});

	// Navigate to correct tab by firing a click event on it. Click event needs to have already been registered on '.nav-tab'.
	jQuery('a[href="#' + document.URL.split('#').pop() + '"]').trigger('click');

	/**
	 * ==== User Capabilities ====
	 *
	 * On checking either the 'Add slideshows' capability or the 'Delete slideshow' capability, the 'Edit slideshows'
	 * checkbox should also be checked. Un-checking the 'Edit slideshows' checkbox needs to do the opposite.
	 */
	jQuery('input').change(function(){

		// Check if the type was a checkbox
		if(jQuery(this).attr('type').toLowerCase() != 'checkbox')
			return;

		// Capabilities
		var addSlideshows = 'slideshow-jquery-image-gallery-add-slideshows';
		var editSlideshows = 'slideshow-jquery-image-gallery-edit-slideshows';
		var deleteSlideshows = 'slideshow-jquery-image-gallery-delete-slideshows';

		// Get capability and role
		var idArray = jQuery(this).attr('id').split('_');
		var capability = idArray.shift();
		var role = idArray.join('_');

		// When 'Edit slideshows' has been un-checked, set 'Add slideshows' and 'Delete slideshows' to un-checked as well
		if(capability == editSlideshows && !jQuery(this).attr('checked')){

			// Un-check 'Delete slideshows' and 'Add slideshows'
			jQuery('#' + addSlideshows + '_' + role).attr('checked', false);
			jQuery('#' + deleteSlideshows + '_' + role).attr('checked', false);
		}
		// When 'Add slideshows' or 'Delete slideshows' has been checked, 'Edit slideshows' must be checked as well
		else if(capability == addSlideshows || capability == deleteSlideshows){

			jQuery('#' + editSlideshows + '_' + role).attr('checked', true);
		}
	});

	/**
	 * ==== Custom Styles ====
	 *
	 * Show chosen style editor and hide all others when clicked on style-action. Delete chosen style when clicked on
	 * style-delete
	 */
	jQuery(
		'.custom-styles .styles-list .style-action,' +
		'.custom-styles .styles-list .style-delete,' +
		'.custom-styles .custom-style-templates .custom-styles-list-item .style-action,' +
		'.custom-styles .custom-style-templates .custom-styles-list-item .style-delete'
	).click(function(){

		// Get custom style key
		var customStyleKey = jQuery(this).attr('class').split(' ')[1];

		// Return if no style key was found
		if(customStyleKey == undefined)
			return;

		// Show
		if(jQuery(this).hasClass('style-action')){

			// Fade editors out
			jQuery('.custom-styles .style-editors .style-editor').each(function(){
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
			jQuery('.custom-styles .style-editors .style-editor.' + customStyleKey).remove();

			// Delete item from list
			jQuery(this).closest('li').remove();
		}
	});

	/**
	 * ==== Custom Styles ====
	 *
	 * Create new editor from editor template when a default style needs to be customized.
	 */
	jQuery('.custom-styles .styles-list .style-action.style-default').click(function(){

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
		var $editor = jQuery('.custom-styles .custom-style-templates .style-editor').clone();

		// Add class to editor
		$editor.addClass(customStyleID);

		// Add value attributes
		$editor.find('.new-custom-style-title').attr('value', title);
		$editor.find('.new-custom-style-content').html(content);

		// Add name attributes
		$editor.find('.new-custom-style-title').attr('name', customStylesKey + '[' + customStyleID + '][title]');
		$editor.find('.new-custom-style-content').attr('name', customStylesKey + '[' + customStyleID + '][style]');

		// Add editor to DOM
		jQuery('.custom-styles .style-editors').append($editor);

		// Fade editor in
		setTimeout(
			function(){
				$editor.fadeIn(200);
			},
			200
		);

		// Clone custom styles list item
		var $li = jQuery('.custom-styles .custom-style-templates .custom-styles-list-item').clone(true);

		// Prepare
		$li.removeClass('custom-styles-list-item')
		$li.find('.style-title').html(title);
		$li.find('.style-action').addClass(customStyleID);
		$li.find('.style-delete').addClass(customStyleID);

		// Remove 'No custom stylesheets found message'
		jQuery('.custom-styles .styles-list .custom-styles-list .no-custom-styles-found').remove();

		// Add custom styles list item to DOM
		jQuery('.custom-styles .styles-list .custom-styles-list').append($li);
	});

	/**
	 * ==== Custom Styles ====
	 *
	 * Returns highest custom style id in existence
	 *
	 * @return highestCustomStyleID
	 */
	function getHighestCustomStyleID(){

		var highestCustomStyleID = 0;

		// Loop through style editors
		jQuery('.custom-styles .style-editors .style-editor').each(function(){

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