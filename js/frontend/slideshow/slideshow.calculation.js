(function()
{
	var $         = jQuery,
		self      = slideshow_jquery_image_gallery_script;

	/**
	 * Set container width to parent width, set overflow width to container width
	 * Only calculates the width when no default width was set.
	 *
	 * If recalculateVisibleViews is set to false, visible views won't be recalculated. If left empty or true, they will.
	 *
	 * @param recalculateViews (boolean, defaults to true)
	 */
	self.Slideshow.prototype.recalculate = function(recalculateViews)
	{
		// Check for slideshow's visibility. If it's invisible, start polling for visibility, then return
		if (!this.$container.is(':visible'))
		{
			// Poll as long as the slideshow is invisible. When visible recalculate and cancel polling
			setTimeout(
				$.proxy(function()
				{
					this.recalculate(recalculateViews);
				}, this),
				500
			);

			return;
		}

		// Walk up the DOM looking for elements with a width the slideshow can use to adjust to
		var $parentElement = this.$parentElement;

		for (var i = 0; $parentElement.width() <= 0; i++)
		{
			$parentElement = $parentElement.parent();

			if (i > 50)
			{
				break;
			}
		}

		// Exit when the slideshow's parent element hasn't changed in width
		if (this.currentWidth == $parentElement.width())
		{
			return;
		}

		// Set current width variable to parent element width to be able to check if any change in width has occurred
		this.currentWidth = $parentElement.width();

		// Calculate slideshow's maximum width (don't include container's margin, as it could be set to 'auto')
		var width = $parentElement.width() - (this.$container.outerWidth() - this.$container.width());

		if (parseInt(this.settings['maxWidth'], 10) > 0 && parseInt(this.settings['maxWidth'], 10) < width)
		{
			width = parseInt(this.settings['maxWidth'], 10);
		}

		// Set width
		this.$container.css('width', Math.round(width));
		this.$content.css('width', Math.round(width) - (this.$content.outerWidth(true) - this.$content.width()));

		// Calculate and set the heights
		if (this.settings['preserveSlideshowDimensions'])
		{
			var height = (width * this.settings['dimensionHeight']) / this.settings['dimensionWidth'];

			this.$container.css('height', Math.round(height));
			this.$content.css('height', Math.round(height) - (this.$content.outerHeight(true) - this.$content.height()));
		}
		else
		{
			this.$container.css('height', Math.round(this.settings['height']));
			this.$content.css('height', Math.round(this.settings['height']));
		}

		// Recalculate hiding position of hidden views
		this.$views.each($.proxy(function(viewID, view)
		{
			if ($.inArray(viewID, this.visibleViews) < 0)
			{
				$(view).css('top', this.$container.outerHeight(true));
			}
		}, this));

		// Fire slideshowResize event
		this.$container.trigger('slideshowResize');

		// Recalculate all views in animation
		if (recalculateViews ||
			typeof recalculateViews !== 'boolean')
		{
			this.recalculateVisibleViews();
		}
	};

	/**
	 * Recalculates all views. Uses recalculateView() to recalculate every separate view.
	 */
	self.Slideshow.prototype.recalculateViews = function()
	{
		$.each(this.$views, $.proxy(function(viewID)
		{
			this.recalculateView(viewID, false);
		}, this));
	};

	/**
	 * Recalculates all slides that are currently defined as being in state of animation. Uses recalculateView() to
	 * recalculate every separate view.
	 */
	self.Slideshow.prototype.recalculateVisibleViews = function()
	{
		// Loop through viewsInAnimation array
		$.each(this.visibleViews, $.proxy(function(key, viewID)
		{
			this.recalculateView(viewID, false);
		}, this));
	};

	/**
	 * Calculates all slides' heights and widths in the passed view, keeping their border widths in mind.
	 *
	 * When forceCalculation is set to true, the view with the passed viewID will be recalculated even though the width
	 * of the view may not have changed.
	 *
	 * TODO Implement separate slide widths. This can be done by making use of the $viewData array using a 'width'
	 * TODO variable.
	 *
	 * @param viewID           (int)
	 * @param forceCalculation (bool) Optional, defaults to false.
	 */
	self.Slideshow.prototype.recalculateView = function(viewID, forceCalculation)
	{
		// Don't calculate the view when the $content element isn't at a displayable size
		if (this.$content.width() <= 0 ||
			this.$content.height() <= 0)
		{
			setTimeout(
				$.proxy(function()
				{
					this.recalculateView(viewID, forceCalculation);
				}, this),
				500
			);

			return;
		}

		// Create jQuery object from view
		var $view = $(this.$views[viewID]);

		// Return when the slideshow's width or height haven't changed
		if ((typeof forceCalculation !== 'boolean' || !forceCalculation) &&
			this.$content.width() == $view.outerWidth(true))
		{
			return;
		}

		// Find slides in $view
		var $slides = $view.find('.slideshow_slide');

		if ($slides.length <= 0)
		{
			return;
		}

		var viewWidth  = this.$content.width() - ($view.outerWidth(true) - $view.width());
		var viewHeight = this.$content.height() - ($view.outerHeight(true) - $view.height());

		var slideWidth  = Math.floor(viewWidth / $slides.length);
		var slideHeight = viewHeight;
		var spareWidth  = viewWidth % $slides.length;
		var totalWidth  = 0;

		// Cut off left and right margin of outer slides
		$($slides[0]).css('margin-left', 0);
		$($slides[$slides.length - 1]).css('margin-right', 0);

		$.each($slides, $.proxy(function(slideID, slide)
		{
			// Instantiate slide as jQuery object
			var $slide = $(slide);

			// Calculate slide dimensions
			var outerWidth  = $slide.outerWidth(true) - $slide.width();
			var outerHeight = $slide.outerHeight(true) - $slide.height();

			// Add spare width pixels to the last slide
			if (slideID == ($slides.length - 1))
			{
				$slide.width((slideWidth - outerWidth) + spareWidth);
			}
			else
			{
				$slide.width(slideWidth - outerWidth);
			}

			$slide.height(slideHeight - outerHeight);

			// Each slide type has type specific features
			if ($slide.hasClass('slideshow_slide_text'))
			{
				var $anchor = $slide.find('.slideshow_background_anchor');

				if ($anchor.length <= 0)
				{
					return;
				}

				// Calculate image width and height
				var anchorWidth  = $slide.width() - ($anchor.outerWidth(true) - $anchor.width());
				var anchorHeight = $slide.height() - ($anchor.outerHeight(true) - $anchor.height());

				// Set $anchor width and height
				$anchor.css({
					'width' : anchorWidth,
					'height': anchorHeight
				});
			}
			else if ($slide.hasClass('slideshow_slide_image'))
			{
				var $image = $slide.find('img');

				if ($image.length <= 0)
				{
					return;
				}

				var imageOuterWidth  = $image.outerWidth();
				var imageOuterHeight = $image.outerHeight();

				var imageAbsoluteOuterWidth,
					imageAbsoluteOuterHeight;

				if (!isNaN(parseInt(imageOuterWidth, 10)))
				{
					imageAbsoluteOuterWidth = imageOuterWidth - $image.width();
				}
				else
				{
					imageAbsoluteOuterWidth = 0;
				}

				if (!isNaN(parseInt(imageOuterHeight, 10)))
				{
					imageAbsoluteOuterHeight = imageOuterHeight - $image.height();
				}
				else
				{
					imageAbsoluteOuterHeight = 0;
				}

				// Calculate image width and height
				var maxImageWidth  = $slide.width() - imageAbsoluteOuterWidth;
				var maxImageHeight = $slide.height() - imageAbsoluteOuterHeight;

				if (this.settings['imageBehaviour'] === 'stretch')
				{
					$image.css({
						width : maxImageWidth,
						height: maxImageHeight
					});

					$image.attr({
						width : maxImageWidth,
						height: maxImageHeight
					});
				}
				else // No stretching
				{
					this.getNaturalImageSize($image, $.proxy(function(naturalWidth, naturalHeight)
					{
						var slideDimension,
							imageDimension;

						if (naturalWidth <= 0 ||
							naturalHeight <= 0)
						{
							setTimeout(
								$.proxy(function()
								{
									this.recalculateView(viewID, true);
								}, this),
								500
							);

							return;
						}

						slideDimension = $slide.width() / $slide.height();
						imageDimension = (naturalWidth + imageAbsoluteOuterWidth) / (naturalHeight + imageAbsoluteOuterHeight);

						if (imageDimension >= slideDimension) // Image dimension is wider than or equal to the dimension of the slide
						{
							if (this.settings['imageBehaviour'] === 'natural')
							{
								// Remove auto centering
								$image.css({
									'margin': '0px',
									'width' : maxImageWidth,
									'height': Math.round(maxImageWidth / imageDimension)
								});

								// Set width to slide's width, keep height in same dimension
								$image.attr({
									width : maxImageWidth,
									height: Math.round(maxImageWidth / imageDimension)
								});
							}
							else if (this.settings['imageBehaviour'] === 'crop')
							{
								$image.css({
									'margin-top' : '0px',
									'margin-left': -Math.floor(((maxImageHeight * imageDimension) - maxImageWidth) / 2),
									'height'     : maxImageHeight,
									'width'      : Math.floor(maxImageHeight * imageDimension)
								});

								$image.attr({
									width : Math.floor(maxImageHeight * imageDimension),
									height: maxImageHeight
								});
							}
						}
						else // Image has a slimmer dimension than the slide
						{
							if (this.settings['imageBehaviour'] === 'natural')
							{
								// Center image
								$image.css({
									'margin-left' : 'auto',
									'margin-right': 'auto',
									'display'     : 'block',
									'width'       : Math.round(maxImageHeight * imageDimension),
									'height'      : maxImageHeight
								});

								// Set height to slide's height, keep width in same dimension
								$image.attr({
									width : Math.round(maxImageHeight * imageDimension),
									height: maxImageHeight
								});
							}
							else if (this.settings['imageBehaviour'] === 'crop')
							{
								$image.css({
									'margin-top' : -Math.floor(((maxImageWidth / imageDimension) - maxImageHeight) / 2),
									'margin-left': '0px',
									'width'      : maxImageWidth,
									'height'     : Math.floor(maxImageWidth / imageDimension)
								});

								$image.attr({
									'width'      : maxImageWidth,
									'height'     : Math.floor(maxImageWidth / imageDimension)
								});
							}
						}
					},this));
				}
			}
			else if ($slide.hasClass('slideshow_slide_video'))
			{
				var $videoElement = $slide.find('iframe');

				// If the player already exists, adjust its size. Otherwise, create a new player
				if ($videoElement.length > 0)
				{
					$videoElement.attr({
						width : $slide.width(),
						height: $slide.height()
					});
				}
				else
				{
					var youTubePlayerReadyTimer = setInterval(
						$.proxy(function()
						{
							if (!self.youTubeAPIReady)
							{
								return;
							}

							clearInterval(youTubePlayerReadyTimer);

							// Find element and create a unique element ID for it
							var $element = $slide.find('.slideshow_slide_video_id');

							$element.attr('id', 'slideshow_slide_video_' + Math.floor(Math.random() * 1000000) + '_' + $element.text());

							var showRelatedVideos = $element.attr('data-show-related-videos');

							var player = new YT.Player(
								$element.attr('id'),
								{
									width     : $slide.width(),
									height    : $slide.height(),
									videoId   : $element.text(),
									playerVars:
									{
										wmode: 'opaque',
										rel  : showRelatedVideos
									},
									events    :
									{
										'onReady'      : function(){ },
										'onStateChange': $.proxy(function(event)
										{
											this.videoPlayers[$element.attr('id')].state = event.data;
										}, this)
									}
								}
							);

							var $playerElement = $('#' + $element.attr('id'));

							$playerElement.show();
							$playerElement.attr('src', $playerElement.attr('src') + '&wmode=opaque');

							// Save player element and state referenced by its element ID, to determine whether or not its playing
							this.videoPlayers[$element.attr('id')] = {'player': player, 'state': -1 };
						}, this),
						500
					);
				}
			}

			// Add up total width
			totalWidth += $slide.outerWidth(true);
		}, this));

		$view.css({
			'width' : viewWidth,
			'height': viewHeight
		});
	};
}());