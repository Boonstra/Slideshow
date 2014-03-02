(function()
{
	var $    = jQuery,
		self = slideshow_jquery_image_gallery_script;

	/**
	 * Initial start method
	 */
	self.Slideshow.prototype.start = function()
	{
		// Activate modules
		this.activateDescriptions();
		this.activateControlPanel();
		this.activateNavigationButtons();
		this.activatePagination();
		this.activatePauseOnHover();

		if (this.$loadingIcon.length > 0)
		{
			this.$loadingIcon.remove();
		}

		this.$content.show();

		this.recalculateViews();

		// Register recalculation on window resize
		if (this.settings['enableResponsiveness'])
		{
			$(window).resize($.proxy(function()
			{
				this.recalculate(true);
			}, this));
		}

		this.playState = this.PlayStates.PAUSED;

		this.$container.trigger('slideshowPlayStateChange', [ this.playState ]);

		if (this.settings['play'])
		{
			this.play();
		}
	};

	/**
	 * Sets the slideshow's animation interval when play is true.
	 */
	self.Slideshow.prototype.play = function()
	{
		if (this.interval)
		{
			return;
		}

		this.playState = this.PlayStates.PLAYING;

		this.$container.trigger('slideshowPlayStateChange', [ this.playState ]);

		// Set interval to intervalSpeed
		this.interval = setInterval(
			$.proxy(function retrieveViewAndAnimateToView(viewID, slideshowInstance)
			{
				// The slideshowInstance variable is necessary here, as 'this' will sometimes refer to 'window'
				if (slideshowInstance === undefined)
				{
					slideshowInstance = this;
				}

				if (viewID === undefined)
				{
					viewID = slideshowInstance.getNextViewID();
				}

				if (slideshowInstance.isViewLoaded(viewID))
				{
					slideshowInstance.animateTo(viewID, 1);

					slideshowInstance.play();
				}
				else
				{
					slideshowInstance.pause(this.PlayStates.TEMPORARILY_PAUSED);

					setTimeout($.proxy(function(){ retrieveViewAndAnimateToView(viewID, slideshowInstance); }, slideshowInstance), 100);
				}
			}, this),
			this.settings['intervalSpeed'] * 1000
		);
	};

	/**
	 * Stops the slideshow's animation interval. $interval is set to false.
	 *
	 * @param playState (PlayState) Optional, defaults to this.PlayState.PAUSED
	 */
	self.Slideshow.prototype.pause = function(playState)
	{
		clearInterval(this.interval);

		this.interval = false;

		if (playState !== this.PlayStates.PAUSED &&
			playState !== this.PlayStates.TEMPORARILY_PAUSED)
		{
			playState = this.PlayStates.PAUSED;
		}

		this.playState = playState;

		this.$container.trigger('slideshowPlayStateChange', [ this.playState ]);
	};

	/**
	 * Animates slideshow to next view
	 */
	self.Slideshow.prototype.next = function()
	{
		if (this.playState === this.PlayStates.PLAYING)
		{
			this.pause(this.PlayStates.TEMPORARILY_PAUSED);

			this.play();
		}

		this.animateTo(this.getNextViewID(), 1);
	};

	/**
	 * Animates slideshow to previous view
	 */
	self.Slideshow.prototype.previous = function()
	{
		if (this.playState === this.PlayStates.PLAYING)
		{
			this.pause(this.PlayStates.TEMPORARILY_PAUSED);

			this.play();
		}

		this.animateTo(this.getPreviousViewID(), -1);
	};

	/**
	 * Returns true if a video is playing, returns false otherwise.
	 *
	 * @return boolean isVideoPlaying
	 */
	self.Slideshow.prototype.isVideoPlaying = function()
	{
		// Loop through video players to check if any is running
		for (var playerID in this.videoPlayers)
		{
			if (!this.videoPlayers.hasOwnProperty(playerID))
			{
				continue;
			}

			// State can be one of the following: Unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5)
			var state = this.videoPlayers[playerID].state;

			if (state == 1 ||
				state == 3)
			{
				return true;
			}
		}

		return false;
	};

	/**
	 * Pauses all videos
	 */
	self.Slideshow.prototype.pauseAllVideos = function()
	{
		for (var playerID in this.videoPlayers)
		{
			if (!this.videoPlayers.hasOwnProperty(playerID))
			{
				continue;
			}

			var player = this.videoPlayers[playerID].player;

			if (player != null &&
				typeof player.pauseVideo === 'function')
			{
				this.videoPlayers[playerID].state = 2;

				player.pauseVideo();
			}
		}
	};

	/**
	 * Returns whether or not all slides in the passed view have been loaded.
	 *
	 * @param viewID (int)
	 * @returns bool isViewLoaded
	 */
	self.Slideshow.prototype.isViewLoaded = function(viewID)
	{
		var isViewLoaded = true;

		// Check viewID
		if (isNaN(parseInt(viewID, 10)))
		{
			return false;
		}

		$.each(this.viewData[viewID], $.proxy(function(key, slideData)
		{
			if (slideData.loaded == 0)
			{
				isViewLoaded = false;
			}
		}, this));

		return isViewLoaded;
	};

	/**
	 * Gets the natural size of the passed image jQuery object. The natural size is the original size, as it hasn't been
	 * adapted by any kind of styles on the page.
	 *
	 * This function returns no data. It calls a callback function, as the image may not be fully loaded at the time
	 * this function is called. The callback function will receive the following parameters:
	 *
	 * callback(long naturalWidth, long naturalHeight, mixed data)
	 *
	 * @param $image   (jQuery)
	 * @param callback (function)
	 * @param data     (mixed)
	 */
	self.Slideshow.prototype.getNaturalImageSize = function($image, callback, data)
	{
		if ($image.length <= 0 ||
			!($image instanceof $) ||
			typeof $image.attr('src') !== 'string')
		{
			callback(-1, -1, data);

			return;
		}

		this.onImageLoad($image, $.proxy(function(success, image)
		{
			callback(image.width, image.height, data);
		}, this));
	};

	/**
	 * An IE-safe way to wait for an image to load. The passed callback function will be called once the image has
	 * fully loaded.
	 *
	 * The callback function will be called with the following parameters:
	 *
	 * callback(bool success, HTMLImageElement image, mixed data)
	 *
	 * @param $image   (jQuery)
	 * @param callback (function)
	 * @param data     (mixed)
	 */
	self.Slideshow.prototype.onImageLoad = function($image, callback, data)
	{
		var image = new Image();

		if ($image.length <= 0 ||
			!($image instanceof $) ||
			typeof $image.attr('src') !== 'string')
		{
			callback(false, image, data);

			return;
		}

		image.onload = $.proxy(function()
		{
			callback(true, image, data);
		}, this);

		image.src = $image.attr('src');
	};

	/**
	 * Returns the next view ID, is a (semi) random number when random is true.
	 *
	 * @return int viewID
	 */
	self.Slideshow.prototype.getNextViewID = function()
	{
		var viewID = this.currentViewID;

		// Return a random ID when random is true
		if (this.settings['random'])
		{
			var oldViewID = viewID;

			viewID = this.getNextRandomViewID();

			// Only return when it's not the same ID as before
			if (viewID != oldViewID)
			{
				return viewID;
			}
		}

		// If viewID is not a number, return 0
		if (isNaN(parseInt(viewID, 10)))
		{
			return 0;
		}

		// When the end of the views array is reached, return to the first view
		if (viewID >= this.$views.length - 1)
		{
			// When animation should loop, start over, otherwise stay on same view
			if (this.settings['loop'])
			{
				return 0;
			}
			else
			{
				return this.currentViewID;
			}
		}

		// Increment
		return viewID + 1;
	};

	/**
	 * Returns the previous view ID, is a (semi) random number when random is true.
	 *
	 * @return int viewID
	 */
	self.Slideshow.prototype.getPreviousViewID = function()
	{
		// Get current view ID
		var viewID = this.currentViewID;

		// If viewID is not a number, set it to 0
		if (isNaN(parseInt(viewID, 10)))
		{
			viewID = 0;
		}

		// Return a random ID when random is true
		if (this.settings['random'])
		{
			var oldViewID = viewID;

			viewID = this.getPreviousRandomViewID();

			// Only return when it's not the same ID as before
			if (viewID != oldViewID)
			{
				return viewID;
			}
		}

		// When the start of the views array is reached, go to the last view
		if (viewID <= 0)
		{
			// When animation should loop, go to the last view, otherwise stay on same view
			if (this.settings['loop'])
			{
				return viewID = this.$views.length - 1;
			}
			else
			{
				return this.currentViewID;
			}
		}

		// Increment
		return viewID -= 1;
	};

	/**
	 * Returns the next random view ID
	 *
	 * @return int nextRandomViewID
	 */
	self.Slideshow.prototype.getNextRandomViewID = function()
	{
		// Push current view ID to previous slide history
		if (!isNaN(parseInt(this.currentViewID, 10)))
		{
			this.randomPreviousHistoryViewIDs.push(this.currentViewID);
		}

		// The history should only be as long as twice the slideshows length in views
		if (this.randomPreviousHistoryViewIDs.length > this.viewIDs.length * 2)
		{
			this.randomPreviousHistoryViewIDs.shift();
		}

		// When we're in history, use the next view ID in history
		if (this.randomNextHistoryViewIDs.length > 0)
		{
			return this.randomNextHistoryViewIDs.pop();
		}

		// Fill available view IDs array when empty
		if (this.randomAvailableViewIDs === undefined ||
			this.randomAvailableViewIDs.length <= 0)
		{
			this.randomAvailableViewIDs = $.extend(true, [], this.viewIDs);

			var randomAvailableViewIDsCurrentViewIDPosition = $.inArray(this.currentViewID, this.randomAvailableViewIDs);

			// Remove current view ID from random available view IDs
			if (randomAvailableViewIDsCurrentViewIDPosition >= 0)
			{
				this.randomAvailableViewIDs.splice(randomAvailableViewIDsCurrentViewIDPosition, 1);
			}
		}

		return this.randomAvailableViewIDs.splice(Math.floor(Math.random() * this.randomAvailableViewIDs.length), 1).pop();
	};

	/**
	 * Returns the previous random view ID
	 *
	 * @return int previousRandomViewID
	 */
	self.Slideshow.prototype.getPreviousRandomViewID = function(){

		if (!isNaN(parseInt(this.currentViewID, 10)))
		{
			this.randomNextHistoryViewIDs.push(this.currentViewID);
		}

		if (this.randomNextHistoryViewIDs.length > this.viewIDs.length * 2)
		{
			this.randomNextHistoryViewIDs.shift();
		}

		if (this.randomPreviousHistoryViewIDs.length > 0)
		{
			return this.randomPreviousHistoryViewIDs.pop();
		}

		return this.viewIDs[Math.floor(Math.random() * this.viewIDs.length)];
	};

	/**
	 * Retrieves session ID from the slideshow container
	 *
	 * @return int ID
	 */
	self.Slideshow.prototype.getID = function()
	{
		var ID = this.$container.data('sessionId');

		if (isNaN(parseInt(ID, 10)))
		{
			ID = this.$container.attr('data-session-id');
		}

		return ID;
	};

	/**
	 * Triggers click on an element if the Enter key is pressed while element is focused
	 *
	 * @param $obj (jQuery)
	 */
	self.Slideshow.prototype.onKeyboardSubmit = function($obj)
	{
		$obj.keypress(function(e) {
			var code = e.keyCode || e.which;
			if (code === 13) {
				e.preventDefault();
				$(this).click()
			}
		});
	}
}());