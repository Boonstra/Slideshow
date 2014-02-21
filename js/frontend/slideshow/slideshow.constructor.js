(function()
{
	var $    = jQuery,
		self = slideshow_jquery_image_gallery_script;

	/**
	 * Slideshow constructor
	 *
	 * @param $slideshow (jQuery)
	 */
	self.Slideshow = function($slideshow)
	{
		if (!($slideshow instanceof $))
		{
			return;
		}

		// Element variables
		this.$container        = $slideshow;
		this.$content          = this.$container.find('.slideshow_content');
		this.$views            = this.$container.find('.slideshow_view');
		this.$slides           = this.$container.find('.slideshow_slide');
		this.$controlPanel     = this.$container.find('.slideshow_controlPanel');
		this.$togglePlayButton = this.$controlPanel.find('.slideshow_togglePlay');
		this.$nextButton       = this.$container.find('.slideshow_next');
		this.$previousButton   = this.$container.find('.slideshow_previous');
		this.$pagination       = this.$container.find('.slideshow_pagination');
		this.$loadingIcon      = this.$container.find('.slideshow_loading_icon');

		// Settings
		this.ID = this.getID();

		if (isNaN(parseInt(this.ID, 10)))
		{
			return;
		}

		this.settings = window['SlideshowPluginSettings_' + this.ID];

		// Convert 'true' and 'false' to boolean values.
		$.each(this.settings, $.proxy(function(setting, value)
		{
			if (value == 'true')
			{
				this.settings[setting] = true;
			}
			else if (value == 'false')
			{
				this.settings[setting] = false;
			}
		}, this));

		// Interchanging variables
		this.$parentElement     = this.$container.parent();
		this.viewData           = [];
		this.viewIDs            = [];
		this.currentlyAnimating = false;
		this.currentViewID      = undefined;
		this.currentWidth       = 0;
		this.visibleViews       = [];
		this.videoPlayers       = [];
		this.PlayStates         = { UNSTARTED: -2, PAUSED: -1, TEMPORARILY_PAUSED: 0, PLAYING: 1 };
		this.playState          = this.PlayStates.UNSTARTED;

		// Timers
		this.interval          = false;
		this.pauseOnHoverTimer = false;
		this.descriptionTimer  = false;

		// Randomization
		this.randomNextHistoryViewIDs     = [];
		this.randomPreviousHistoryViewIDs = [];
		this.randomAvailableViewIDs       = [];

		$.each(this.$views, $.proxy(function(viewID){ this.viewIDs.push(viewID); }, this));

		this.currentViewID = this.getNextViewID();

		this.visibleViews = [this.currentViewID];

		// Initial size calculation of slideshow, doesn't recalculate views
		this.recalculate(false);

		// Hide views that should not be currently shown out of sight
		// TODO As a slideshow (in very few cases) may not have received a height after calculation, this wrapper function
		// TODO is needed to wait for the slideshow to receive a height value. It would probably be better to listen
		// TODO to an event fired by the recalculate() function in order to hide the views.
		var hideViews = $.proxy(function(hideViewsFunction)
		{
			if (this.$container.width() <= 0 ||
				this.$container.height() <= 0)
			{
				setTimeout($.proxy(function(){ hideViewsFunction(); }, this), 500)
			}

			$.each(this.$views, $.proxy(function(viewID, view)
			{
				var $view = $(view);

				// Hide views, except for the one that's currently showing.
				if (viewID != this.visibleViews[0])
				{
					$view.css('top', this.$container.outerHeight(true)).find('a').attr('tabindex', '-1');
				}
				else
				{
					$view.addClass('slideshow_currentView');
				}
			}, this));
		}, this);
		hideViews(hideViews);

		// Initialize $viewData array as $viewData[ view[ slide{ 'loaded': 0 } ] ]
		// Add slideshow_currentView identifier class to the visible views
		// Recalculate views
		var hasFirstSlideLoaded = true;
		$.each(this.$views, $.proxy(function(viewID, view)
		{
			var $view = $(view);

			this.viewData[viewID] = [];

			$.each($view.find('.slideshow_slide'), $.proxy(function(slideID, slide)
			{
				var $slide = $(slide);

				//this.viewData[viewID][slideID] = { 'imageDimension': '' };
				this.viewData[viewID][slideID] = { };

				// Check if the image in this slide is loaded. The loaded value van have the following values:
				// -1: Slide is no image slide, 0: Not yet loaded, 1: Successfully loaded, 2: Unsuccessfully loaded
				if ($slide.hasClass('slideshow_slide_image'))
				{
					var $image = $slide.find('img');

					if ($image.length > 0)
					{
						if ($image.get(0).complete)
						{
							this.viewData[viewID][slideID].loaded = 1;
						}
						else
						{
							if (viewID === this.currentViewID)
							{
								hasFirstSlideLoaded = false;
							}

							this.viewData[viewID][slideID].loaded = 0;

							this.onImageLoad($image, $.proxy(function(success)
							{
								if (success)
								{
									this.viewData[viewID][slideID].loaded = 1;
								}
								else
								{
									this.viewData[viewID][slideID].loaded = 2;
								}

								if (this.settings['waitUntilLoaded'] &&
									viewID === this.currentViewID &&
									this.isViewLoaded((viewID)))
								{
									this.start();
								}
							}, this));
						}
					}
					else
					{
						this.viewData[viewID][slideID].loaded = -1;
					}
				}
				else
				{
					this.viewData[viewID][slideID].loaded = -1;
				}
			}, this));
		}, this));

		// Recalculate visible views when window is loaded
		$(window).load($.proxy(function()
		{
			this.recalculateVisibleViews();
		}, this));

		// Check if intervalSpeed is greater than slideSpeed
		if (parseFloat(this.settings['intervalSpeed']) < parseFloat(this.settings['slideSpeed']) + 0.1)
		{
			this.settings['intervalSpeed'] = parseFloat(this.settings['slideSpeed']) + 0.1;
		}

		// Start slideshow
		if (!this.settings['waitUntilLoaded'] ||
			(this.settings['waitUntilLoaded'] && hasFirstSlideLoaded))
		{
			this.start();
		}
	};
}());