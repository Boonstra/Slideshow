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
	 * Show chosen style editor and hide all others.
	 */
	jQuery('.custom-styles .styles-list .style-action').click(function(){

		// Get custom style key
		var customStyleKey = jQuery(this).attr('class').split(' ')[1];

		// Return if no style key was found
		if(customStyleKey == undefined)
			return;

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
		)
	});

	/**
	 * ==== Custom Styles ====
	 *
	 * Create new editor from editor template when a default style needs to be customized.
	 */
	jQuery('.custom-styles .styles-list .style-action.style-default').click(function(){

		// Get the default stylesheet content
		var content = jQuery(this).closest('li').find('.style-content').html();

		// Exit when content is empty
		if(content == '' || content == undefined)
			return;

		// Clone editor template
		var $editor = jQuery('.custom-styles .style-editor-template .style-editor').clone();

		// 

		//console.log($editor);
	});
});