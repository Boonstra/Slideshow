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
		jQuery(activeTab.attr('href').replace('#', '.')).css('display', 'none');

		// Show newly activate tab
		jQuery(thisTab.attr('href').replace('#', '.')).css('display', 'table');
	});
});