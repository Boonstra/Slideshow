(function()
{
	var $    = jQuery,
		self = slideshow_jquery_image_gallery_script;

	/**
	 * Activates description boxes. Only activates when showDescription is set to true.
	 */
	self.Slideshow.prototype.activateDescriptions = function()
	{
		// Only show when showDescription is true
		if (!this.settings['showDescription'])
		{
			return;
		}

		// Loop through descriptions. Show them and if they need to be hidden, hide them
		$.each(this.$slides.find('.slideshow_description'), $.proxy(function(key, description)
		{
			var $description = $(description);

			$description.show();

			if (this.settings['hideDescription'])
			{
				$description.css({
					'position': 'absolute',
					'top'     : this.$container.outerHeight(true)
				});
			}
			else
			{
				$description.css({
					'position': 'absolute',
					'bottom'  : 0
				});
			}
		}, this));

		// Return early when descriptions should not be hidden
		if (!this.settings['hideDescription'])
		{
			return;
		}

		// Adjust description's hiding position on slideshowResize event
		this.$container.bind('slideshowResize', $.proxy(function()
		{
			$.each(this.$container.find('.slideshow_description'), $.proxy(function(key, description)
			{
				$(description).css('top', this.$container.outerHeight(true));
			}, this));
		}, this));

		// Hide descriptions when the slideshow animates
		this.$container.bind('slideshowAnimationStart', $.proxy(function()
		{
			if (this.visibleViews[1] == undefined)
			{
				return;
			}

			$.each($(this.$views[this.visibleViews[1]]).find('.slideshow_description'), $.proxy(function(key, description)
			{
				$(description).css('top', this.$container.outerHeight(true));
			}, this));
		}, this));

		// Register a mouse enter event to animate showing the description boxes.
		this.$slides.mouseenter($.proxy(function(event)
		{
			var $description = $(event.currentTarget).find('.slideshow_description');

			// Use a timer, so the description doesn't pop up on fly-over
			this.descriptionTimer = setTimeout(
				$.proxy(function()
				{
					// Reset timer to original value
					this.descriptionTimer = '';

					// Animate pop up
					$description
						.stop(true, false)
						.animate({ 'top': (this.$container.outerHeight(true) - $description.outerHeight(true)) }, parseInt(this.settings['descriptionSpeed'] * 1000, 10));
				}, this),
				200
			);
		}, this));

		// Register a mouse leave event to animate hiding the description boxes.
		this.$slides.mouseleave($.proxy(function(event)
		{
			// If a description timer is still set, reset it
			if (this.descriptionTimer !== false)
			{
				clearInterval(this.descriptionTimer);

				this.descriptionTimer = false;
			}

			// Find description and stop its current animation, then start animating it out
			$(event.currentTarget)
				.find('.slideshow_description')
				.stop(true, false)
				.animate({ 'top': this.$container.outerHeight(true) }, parseInt(this.settings['descriptionSpeed'] * 1000, 10));
		}, this));
	};

	/**
	 * Activates previous and next buttons, then shows them. Only activates the buttons when controllable is true.
	 */
	self.Slideshow.prototype.activateNavigationButtons = function()
	{
		// Only show buttons if the slideshow is controllable
		if (!this.settings['controllable'])
		{
			return;
		}

		// add text for screen readers and make button keyboard focusable
		this.$nextButton
			.html('<span class="assistive-text hide-text">' + this.$nextButton.data('slideshowNextText') + '</span>')
			.attr({
				'tabindex': '0',
				'title': this.$nextButton.data('slideshowNextText')
			});

		// Register next button click event
		this.$nextButton.click($.proxy(function()
		{
			if (this.currentlyAnimating)
			{
				return;
			}

			this.pauseAllVideos();

			if (this.playState === this.PlayStates.PLAYING)
			{
				this.pause(this.PlayStates.TEMPORARILY_PAUSED);

				this.play();
			}

			this.animateTo(this.getNextViewID(), 1);
		}, this));

		// allow Enter key to trigger next button
		this.onKeyboardSubmit(this.$nextButton);

		// add text for screen readers and make button keyboard focusable
		this.$previousButton
			.html('<span class="assistive-text hide-text">' + this.$previousButton.data('slideshowPreviousText') + '</span>')
			.attr({
				'tabindex': '0',
				'title': this.$previousButton.data('slideshowPreviousText')
			});

		// Register previous button click event
		this.$previousButton.click($.proxy(function()
		{
			if (this.currentlyAnimating)
			{
				return;
			}

			this.pauseAllVideos();

			if (this.playState === this.PlayStates.PLAYING)
			{
				this.pause(this.PlayStates.TEMPORARILY_PAUSED);

				this.play();
			}

			this.animateTo(this.getPreviousViewID(), -1);
		}, this));

		// allow Enter key to trigger previous button
		this.onKeyboardSubmit(this.$previousButton);

		// If hideNavigationButtons is true, fade them in and out on mouse enter and leave. Simply show them otherwise
		if (this.settings['hideNavigationButtons'])
		{
			this.$container.mouseenter($.proxy(function(){ this.$nextButton.stop(true, true).fadeIn(100); }, this));
			this.$container.mouseleave($.proxy(function(){ this.$nextButton.stop(true, true).fadeOut(500); }, this));

			this.$container.mouseenter($.proxy(function(){ this.$previousButton.stop(true, true).fadeIn(100); }, this));
			this.$container.mouseleave($.proxy(function(){ this.$previousButton.stop(true, true).fadeOut(500); }, this));
		}
		else
		{
			this.$nextButton.show();
			this.$previousButton.show();
		}
	};

	/**
	 * Activates control panel consisting of the play and pause buttons. Only activates the control panel when control
	 * panel is set to true.
	 */
	self.Slideshow.prototype.activateControlPanel = function()
	{
		// Don't activate control panel when it's set to false
		if (!this.settings['controlPanel'])
		{
			return;
		}

		// make play button keyboard focusable
		this.$togglePlayButton.attr('tabindex', '0');

		this.$container.bind('slideshowPlayStateChange', $.proxy(function(event, playState)
		{
			if (playState === this.PlayStates.PLAYING)
			{
				this.$togglePlayButton
					.html('<span class="assistive-text hide-text">' + this.$togglePlayButton.data('slideshowPauseText')+'</span>')
					.attr({
						'class': 'slideshow_pause',
						'title': this.$togglePlayButton.data('slideshowPauseText')
					});
			}
			else if (playState === this.PlayStates.PAUSED)
			{
				this.$togglePlayButton
					.html('<span class="assistive-text hide-text">' + this.$togglePlayButton.data('slideshowPlayText') + '</span>')
					.attr({
						'class': 'slideshow_play',
						'title': this.$togglePlayButton.data('slideshowPlayText')
					});
			}
		}, this));

		// Register click event on the togglePlayButton
		this.$togglePlayButton.click($.proxy(function(event)
		{
			var $button = $(event.currentTarget);

			if ($button.hasClass('slideshow_play'))
			{
				this.play();
			}
			else
			{
				this.pause(this.PlayStates.PAUSED);
			}
		}, this));

		// Allow Enter key to trigger play/pause button
		this.onKeyboardSubmit(this.$togglePlayButton);

		// If hideControlPanel is true, fade it in and out on mouse enter and leave. Simply show it otherwise
		if (this.settings['hideControlPanel'])
		{
			this.$container.mouseenter($.proxy(function(){ this.$controlPanel.stop(true, true).fadeIn(100); }, this));
			this.$container.mouseleave($.proxy(function(){ this.$controlPanel.stop(true, true).fadeOut(500); }, this));
		}
		else
		{
			this.$controlPanel.show();
		}
	};

	/**
	 * Activates the pagination bullets on the slideshow. Only activates the pagination bullets when show pagination
	 * is set to true.
	 */
	self.Slideshow.prototype.activatePagination = function()
	{
		// Only show pagination bullets when showPagination is set to true
		if (!this.settings['showPagination'])
		{
			return;
		}

		// Find ul to add view-bullets to
		this.$pagination.find('.slideshow_pagination_center').html('<ul></ul>');

		var $ul = this.$pagination.find('ul');

		$ul.html('');

		this.$views.each($.proxy(function(viewID)
		{
			// Only add currentView class to currently active view-bullet
			var currentView = '';

			if (viewID == this.currentViewID)
			{
				currentView = 'slideshow_currentView';
			}

			var slideNum = parseInt(viewID) + 1;

			// Add list item
			$ul.append('<li class="slideshow_transparent ' + currentView + '" data-slide-position="' + viewID + '" role="button" title="Go to slide ' + slideNum + '"><span class="assistive-text hide-text">Go to slide ' + slideNum + '</span>' + '</li>');
		}, this));

		// On click of a view-bullet go to the corresponding slide
		this.$pagination.find('li')
			.attr('tabindex', '0')
			.click($.proxy(function(event)
			{
				if (this.currentlyAnimating)
				{
					return;
				}

				// Find view ID and check if it's not empty
				var viewID = $(event.currentTarget).data('slidePosition');

				if (isNaN(parseInt(viewID, 10)))
				{
					return;
				}

				this.pauseAllVideos();

				if (this.playState === this.PlayStates.PLAYING)
				{
					this.pause(this.PlayStates.TEMPORARILY_PAUSED);

					this.play();
				}

				this.animateTo(parseInt(viewID, 10), 0);
			}, this));

		this.onKeyboardSubmit(this.$pagination.find('li'));

		// Bind slideshowAnimationStart to pagination to shift currently active view-bullets
		this.$container.bind(
			'slideshowAnimationStart',
			$.proxy(function()
			{
				// Get bullets
				var $bullets = this.$pagination.find('li');

				// Remove all currentView classes from the bullets
				$bullets.each($.proxy(function(key, bullet){ $(bullet).removeClass('slideshow_currentView'); }, this));

				// Add the currentView class to the current bullet
				$($bullets[this.currentViewID]).addClass('slideshow_currentView');
			}, this)
		);

		// If hidePagination is true, fade it in and out on mouse enter and leave. Simply show it otherwise
		if (this.settings['hidePagination'])
		{
			this.$container.mouseenter($.proxy(function(){ this.$pagination.stop(true, true).fadeIn(100); }, this));
			this.$container.mouseleave($.proxy(function(){ this.$pagination.stop(true, true).fadeOut(500); }, this));
		}
		else
		{
			this.$pagination.show();
		}
	};

	/**
	 * Activate the pause on hover functionality. Pauses the slideshow on hover over the container, but only when the
	 * mouse remains on there for more than half a second so that it doesn't pause on fly-over.
	 */
	self.Slideshow.prototype.activatePauseOnHover = function()
	{
		// Exit when pauseOnHover is false
		if (!this.settings['pauseOnHover'])
		{
			return;
		}

		// Pause the slideshow when the mouse enters the slideshow container.
		this.$container.mouseenter($.proxy(function()
		{
			clearTimeout(this.pauseOnHoverTimer);

			if (this.playState === this.PlayStates.PAUSED)
			{
				return;
			}

			// Wait 500 milliseconds before pausing the slideshow. If within this time the mouse hasn't left the container, pause.
			this.pauseOnHoverTimer = setTimeout($.proxy(function(){ this.pause(this.PlayStates.TEMPORARILY_PAUSED); }, this), 500);
		}, this));

		// Continue the slideshow when the mouse leaves the slideshow container.
		this.$container.mouseleave($.proxy(function()
		{
			// This will cancel any pausing when the mouse simply flies over, instead of hovering.
			clearTimeout(this.pauseOnHoverTimer);

			if (this.playState === this.PlayStates.PAUSED)
			{
				return;
			}

			// Start slideshow, but only when the interval has been stopped
			if (this.interval === false)
			{
				this.play();
			}
		}, this));
	};
}());