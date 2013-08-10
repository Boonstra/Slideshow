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

		self.activateNavigation();
	};

	/**
	 * Binds functions to fire at click events on the navigation tabs
	 */
	self.activateNavigation = function()
	{
		// On click of navigation tab, show different settings page.
		$('.nav-tab').click(function(event)
		{
			var $this      = $(event.currentTarget),
				$activeTab = $('.nav-tab-active'),
				$referrer;

			$activeTab.removeClass('nav-tab-active');
			$this.addClass('nav-tab-active');

			// Hide previously active tab's content
			$($activeTab.attr('href').replace('#', '.')).hide();

			// Show newly activated tab
			$($this.attr('href').replace('#', '.')).show();

			// Set referrer value to the current page to be able to return there after saving
			$referrer = $('input[name=_wp_http_referer]');
			$referrer.attr('value', $referrer.attr('value').split('#').shift() + $this.attr('href'));
		});

		// Navigate to correct tab by firing a click event on it. Click event needs to have already been registered on '.nav-tab'.
		$('a[href="#' + document.URL.split('#').pop() + '"]').trigger('click');
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();