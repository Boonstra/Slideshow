(function()
{
	var $    = jQuery,
		self = slideshow_jquery_image_gallery_script;

	/**
	 * Animate to a certain viewID that's not the same as the current view ID and is within the range of the $views
	 * array.
	 *
	 * The direction of animation can be set by setting the direction to either -1 or 1, depending on whether you'd like
	 * to animate left or right respectively. Direction can be set to 0 or left empty to calculate animation direction
	 * by using the current view ID.
	 *
	 * @param viewID    (int)
	 * @param direction (int)
	 */
	self.Slideshow.prototype.animateTo = function(viewID, direction)
	{
		// Don't animate if a video is playing, or viewID is out of range, or viewID is equal to the current view ID
		if (this.isVideoPlaying() ||
			viewID < 0 ||
			viewID >= this.$views.length ||
			viewID == this.currentViewID)
		{
			return;
		}

		// Queue up animateTo requests while an animation is still running
		if (this.currentlyAnimating === true)
		{
			this.$container.one('slideshowAnimationEnd', $.proxy(function()
			{
				if (this.playState === this.PlayStates.PLAYING)
				{
					this.pause(this.PlayStates.TEMPORARILY_PAUSED);

					this.play();
				}

				this.animateTo(viewID, direction);
			}, this));

			return;
		}

		this.currentlyAnimating = true;

		// When direction is 0 or undefined, calculate direction
		if (isNaN(parseInt(direction, 10)) ||
			direction == 0)
		{
			// If viewID is smaller than the current view ID, set direction to -1, otherwise set direction to 1
			if (viewID < this.currentViewID)
			{
				direction = -1;
			}
			else
			{
				direction = 1;
			}
		}

		// Put viewID in viewsInAnimation array so it's registered for recalculation
		this.visibleViews = [this.currentViewID, viewID];

		// Get animation, randomize animation if it's set to random
		var animation  = this.settings['animation'];
		var animations = ['slide', 'slideRight', 'slideUp', 'slideDown', 'fade', 'directFade'];

		if (animation == 'random')
		{
			animation = animations[Math.floor(Math.random() * animations.length)];
		}

		// When going back in slides, slide with the opposite animation
		var animationOpposites = {
			'slide'     : 'slideRight',
			'slideRight': 'slide',
			'slideUp'   : 'slideDown',
			'slideDown' : 'slideUp',
			'fade'      : 'fade',
			'directFade': 'directFade'
		};

		if (direction < 0)
		{
			animation = animationOpposites[animation];
		}

		// Get current and next view
		var $currentView = $(this.$views[this.currentViewID]);
		var $nextView    = $(this.$views[viewID]);

		// Stop any currently running animations
		$currentView.stop(true, true);
		$nextView.stop(true, true);

		// Add current view identifier to next slide
		$nextView.addClass('slideshow_nextView');

		this.recalculateVisibleViews();

		// Set new current view ID
		this.currentViewID = viewID;

		// Fire the slideshowAnimationStart event
		this.$container.trigger('slideshowAnimationStart', [ viewID, animation ]);

		// Animate
		switch(animation)
		{
			case 'slide':

				// Prepare next view
				$nextView.css({
					top : 0,
					left: this.$content.width()
				});

				// Animate
				$currentView.animate({ left: -$currentView.outerWidth(true) }, this.settings['slideSpeed'] * 1000);
				$nextView.animate   ({ left: 0                              }, this.settings['slideSpeed'] * 1000);

				// Hide current view out of sight
				setTimeout(
					$.proxy(function()
					{
						$currentView.stop(true, true).css('top', this.$container.outerHeight(true));
					}, this),
					this.settings['slideSpeed'] * 1000
				);

				break;
			case 'slideRight':

				// Prepare next view
				$nextView.css({
					top : 0,
					left: -this.$content.width()
				});

				// Animate
				$currentView.animate({ left: $currentView.outerWidth(true) }, this.settings['slideSpeed'] * 1000);
				$nextView.animate   ({ left: 0                             }, this.settings['slideSpeed'] * 1000);

				// Hide current view
				setTimeout(
					$.proxy(function()
					{
						$currentView.stop(true, true).css('top', this.$container.outerHeight(true));
					}, this),
					this.settings['slideSpeed'] * 1000
				);

				break;
			case 'slideUp':

				// Prepare next view
				$nextView.css({
					top : this.$content.height(),
					left: 0
				});

				// Animate
				$currentView.animate({ top: -$currentView.outerHeight(true) }, this.settings['slideSpeed'] * 1000);
				$nextView.animate   ({ top: 0                               }, this.settings['slideSpeed'] * 1000);

				// Hide current view
				setTimeout(
					$.proxy(function()
					{
						$currentView.stop(true, true).css('top', this.$container.outerHeight(true));
					}, this),
					this.settings['slideSpeed'] * 1000
				);

				break;
			case 'slideDown':

				// Prepare next view
				$nextView.css({
					top : -this.$content.height(),
					left: 0
				});

				// Animate
				$currentView.animate({ top: $currentView.outerHeight(true) }, this.settings['slideSpeed'] * 1000);
				$nextView.animate   ({ top: 0                              }, this.settings['slideSpeed'] * 1000);

				// Hide current view
				setTimeout(
					$.proxy(function()
					{
						$currentView.stop(true, true).css('top', this.$container.outerHeight(true));
					}, this),
					this.settings['slideSpeed'] * 1000
				);

				break;
			case 'fade':

				// Prepare next view
				$nextView.css({
					top    : 0,
					left   : 0,
					display: 'none'
				});

				// Animate
				$currentView.fadeOut((this.settings['slideSpeed'] * 1000) / 2);

				setTimeout(
					$.proxy(function()
					{
						$nextView.fadeIn((this.settings['slideSpeed'] * 1000) / 2);

						$currentView.stop(true, true).css({
							top    : this.$container.outerHeight(true),
							display: 'block'
						});
					}, this),
					(this.settings['slideSpeed'] * 1000) / 2
				);

				break;
			case 'directFade':

				// Prepare next view
				$nextView.css({
					top      : 0,
					left     : 0,
					'z-index': 0,
					display  : 'none'
				});
				$currentView.css({ 'z-index': 1 });

				// Animate
				$nextView.stop(true, true).fadeIn(this.settings['slideSpeed'] * 1000);
				$currentView.stop(true, true).fadeOut(this.settings['slideSpeed'] * 1000);

				setTimeout(
					$.proxy(function()
					{
						$nextView.stop(true, true).css({ 'z-index': 0 });
						$currentView.stop(true, true).css({
							top      : this.$container.outerHeight(true),
							display  : 'block',
							'z-index': 0
						});
					}, this),
					this.settings['slideSpeed'] * 1000
				);

				break;
		}

		// After animation
		setTimeout(
			$.proxy(function()
			{
				// Remove current view identifier class from the previous view
				$currentView.removeClass('slideshow_currentView').find('a').attr('tabindex', '-1');
				$nextView.removeClass('slideshow_nextView');
				$nextView.addClass('slideshow_currentView').find('a').attr('tabindex', '0');

				// Update visible views array after animating
				this.visibleViews = [ viewID ];

				this.currentlyAnimating = false;

				this.$container.trigger('slideshowAnimationEnd');

			}, this),
			this.settings['slideSpeed'] * 1000
		);
	};
}());