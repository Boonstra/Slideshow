jQuery(document).bind('slideshowBackendReady', function()
{
	var $    = jQuery,
		self = slideshow_jquery_image_gallery_backend_script;

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
});