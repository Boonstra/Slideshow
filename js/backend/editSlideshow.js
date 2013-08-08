slideshow_jquery_image_gallery_backend_script.editSlideshow = function()
{
	var $    = jQuery,
		self = { };

	self.isCurrentPage = false;

	/**
	 *
	 */
	self.init = function()
	{
		if (window.pagenow === 'slideshow')
		{
			self.isCurrentPage = true;

			self.activateSettingsVisibilityDependency();
		}
	};

	/**
	 * Set fields, that depend on another field being a certain value, to show when that certain field becomes a certain
	 * value and to hide when that certain fields loses that certain value.
	 */
	self.activateSettingsVisibilityDependency = function()
	{
//		jQuery('.depends-on-field-value').each(function(key, value){
//			var attributes = jQuery(this).attr('class').split(' ');
//
//			// Check if field should be shown
//			var element = jQuery(this).closest('tr');
//			if(jQuery('input[name="' + attributes[1] + '"]:checked').val() == attributes[2])
//				jQuery(element).show();
//			else
//				jQuery(element).hide();
//
//			// On change, set field's visibility
//			jQuery('input[name="' + attributes[1] + '"]').change(attributes, function(){
//				var element = jQuery('.' + attributes[3]).closest('tr');
//
//				if(jQuery(this).val() == attributes[2])
//					self.animateElementVisibility(element, true);
//				else
//					self.animateElementVisibility(element, false);
//			});
//		});
//
//		return;

		$('.depends-on-field-value').each(function(undefined, field)
		{
			var $field     = $(field),
				attributes = $field.attr('class').split(' '),
				$tr        = $field.closest('tr');

			// Check whether or not field should be shown
			if ($('input[name="' + attributes[1] + '"]:checked').val() == attributes[2])
			{
				console.log('show');
				$tr.show();
			}
			else
			{
				console.log('no show');
				$tr.hide();
			}

			// On change of the field that the current field depends on, set field's visibility
			$('input[name="' + attributes[1] + '"]').change(attributes, function(event)
			{
				var $tr = $('.' + attributes[3]).closest('tr');

				if ($(event.currentTarget).val() == attributes[2])
				{
					self.animateElementVisibility($tr, true);
				}
				else
				{
					self.animateElementVisibility($tr, false);
				}
			});
		});
	};

	/**
	 * Animate an element's visibility
	 *
	 * @param element
	 * @param setVisible Optional, defaults to the current opposite when left empty.
	 */
	self.animateElementVisibility = function(element, setVisible)
	{
		var $element = $(element);

		if (setVisible === undefined)
		{
			// Finish animation before checking visibility
			$element.stop(true, true);

			setVisible = !$element.is(':visible');
		}

		if (setVisible)
		{
			$element.show().css('background-color', '#c0dd52')

			setTimeout(
				function()
				{
					$element.stop(true, true).animate({ 'background-color': 'transparent' }, 1500);
				},
				500
			);
		}
		else
		{
			$element.stop(true, true).hide();
		}
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();