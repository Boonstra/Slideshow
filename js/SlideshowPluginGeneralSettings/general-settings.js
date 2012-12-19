jQuery(document).ready(function(){

	/**
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
});