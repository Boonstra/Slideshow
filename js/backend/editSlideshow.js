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
		$('.depends-on-field-value').each(function(key, field)
		{
			var $field     = $(field),
				attributes = $field.attr('class').split(' '),
				$tr        = $field.closest('tr');

			// Check whether or not field should be shown
			if ($('input[name="' + attributes[1] + '"]:checked').val() == attributes[2])
			{
				$tr.show();
			}
			else
			{
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
			$element.stop(true, true).show().css('background-color', '#c0dd52');

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
			$element.stop(true, true).css('background-color', '#d44f6e');

			setTimeout(
				function()
				{
					$element.stop(true, true).hide(1500, function()
					{
						$element.css('background-color', 'transparent');
					});
				},
				500
			);
		}
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();

// @codekit-append editSlideshow.slideManager.js
