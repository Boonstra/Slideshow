jQuery.fn.slideshow_jquery_image_gallery_script = function(){

	/** Using $, instead of jQuery */
	var $ = jQuery;

	/** Element variables */
	var $container = $(this),
		$content = $container.find('.slideshow_content'),
		$views = $container.find('.slideshow_view'),
		$slides = $container.find('.slideshow_slide'),
		$controlPanel = $container.find('.slideshow_controlPanel'),
		$togglePlayButton = $controlPanel.find('.slideshow_togglePlay'),
		$nextButton = $container.find('.slideshow_next'),
		$previousButton = $container.find('.slideshow_previous'),
		$pagination = $container.find('.slideshow_pagination');

	/** Settings */
	var $ID = getID();
	var $settings = window['SlideshowPluginSettings_' + $ID];
	$.each($settings, function(setting, value){ // Convert 'true' and 'false' to boolean values.
		if(value == 'true')
			$settings[setting] = true;
		else if(value == 'false')
			$settings[setting] = false;
	});

	/** Interchanging variables */
	var	$parentElement = $container.parent(),
		$viewData = [],
		$navigationActive = true,
		$currentViewId = getNextViewId(),
		$currentWidth = 0,
		$visibleViews = [$currentViewId],
		$videoPlayers = [],

	/** Timers */
		$interval = '',
		$mouseEnterTimer = '',
		$invisibilityTimer = '';

	init();
	/**
	 * Initialize the slideshow
	 */
	function init(){

		// Initial size calculation of slideshow, doesn't recalculate views
		recalculate(false);

		// Initialize $viewData array as $viewData[ view[ slide{ 'imageDimension': '' } ] ]
		// Recalculate views
		// Hide views out of the content area
		$.each($views, function(viewId, view){

			recalculateView(view);

			// Hide views, except for the one that's currently showing.
			if(viewId != $visibleViews[0])
				$(view).css('top', $container.outerHeight(true));

			$viewData[viewId] = [];

			$.each($(view).find('.slideshow_slide'), function(slideId, slide){
				$viewData[viewId][slideId] = {
					'imageDimension': ''
				}
			});
		});

		// Show content
		$content.show();

		// Recalculate visible views when window is loaded
		$(window).load(function(){
			recalculateVisibleViews();
		});

		// Register recalculation on window resize
		if($settings['enableResponsiveness']){
			$(window).resize(function(){
				recalculate();
			});
		}

		// Check if intervalSpeed is greater than slideSpeed
		if(parseFloat($settings['intervalSpeed']) < parseFloat($settings['slideSpeed']) + 0.1)
			$settings['intervalSpeed'] = parseFloat($settings['slideSpeed']) + 0.1;

		// Activate modules
		activateDescriptions();
		activateControlPanel();
		activateNavigationButtons();
		activatePagination();
		activatePauseOnHover();

		// Start slideshow
		start();
	}

	/**
	 * Sets the slideshow's animation interval when play is true.
	 */
	function start(){

		// Only start when play is true
		if(!$settings['play'])
			return;

		// Set interval to intervalSpeed
		$interval = setInterval(
			function(){
				animateTo(getNextViewId(), 1);
			},
			$settings['intervalSpeed'] * 1000
		);
	}

	/**
	 * Stops the slideshow's animation interval. $interval is set to false.
	 */
	function stop(){
		clearInterval($interval);
		$interval = false;
	}

	/**
	 * Animate to a certain viewId that's not the same as the current view ID and is within the range of the $views
	 * array.
	 *
	 * The direction of animation can be set by setting the direction to either -1 or 1, depending on whether you'd like
	 * to animate left or right respectively. Direction can be set to 0 or left empty to calculate animation direction
	 * by using the current view ID.
	 *
	 * @param viewId
	 * @param direction
	 */
	function animateTo(viewId, direction){

		// Don't animate if a video is playing, or viewId is out of range, or viewId is equal to the current view ID
		if( videoIsPlaying() ||
			viewId < 0 || viewId >= $views.length || viewId == $currentViewId)
			return;

		// Disable navigation to prevent user input when animating
		$navigationActive = false;

		// When direction is 0 or undefined, calculate direction
		if(direction == 0 || direction == undefined){

			// If viewId is smaller than the current view ID, set direction to -1, otherwise set direction to 1
			if(viewId < $currentViewId)
				direction = -1;
			else
				direction = 1;
		}

		// Put viewId in viewsInAnimation array so it's registered for recalculation
		$visibleViews = [$currentViewId, viewId];

		// Get animation, randomize animation if it's set to random
		var animation = $settings['animation'];
		var animations = ['slide', 'slideRight', 'slideUp', 'slideDown', 'fade', 'directFade'];
		if(animation == 'random')
			animation = animations[Math.floor(Math.random() * animations.length)];

		// When going back in slides, slide with the opposite animation
		var animationOpposites = {
			'slide': 'slideRight',
			'slideRight': 'slide',

			'slideUp': 'slideDown',
			'slideDown': 'slideUp',

			'fade': 'fade',

			'directFade': 'directFade'
		};
		if(direction < 0)
			animation = animationOpposites[animation];

		// Get current and next view
		var currentView = $($views[$currentViewId]);
		var nextView = $($views[viewId]);

		// Stop any currently running animations
		currentView.stop(true, true);
		nextView.stop(true, true);

		recalculateVisibleViews();

		// Set new current view ID
		$currentViewId = viewId;

		// Trigger functions hooked to the onSlideshowAnimate hook
		$container.trigger('onSlideshowAnimate');

		// Animate
		switch(animation){
			case 'slide':

				recalculateVisibleViews();

				// Prepare next view
				nextView.css({
					top: 0,
					left: $content.width()
				});

				// Animate
				currentView.animate({ left: -currentView.outerWidth(true) }, $settings['slideSpeed'] * 1000);
				nextView.animate({ left: 0 }, $settings['slideSpeed'] * 1000);

				// Hide current view out of sight
				setTimeout(function(){ currentView.stop(true, true).css('top', $container.outerHeight(true)); }, $settings['slideSpeed'] * 1000);

				break;
			case 'slideRight':

				// Prepare next view
				nextView.css({
					top: 0,
					left: -$content.width()
				});

				// Animate
				currentView.animate({ left: currentView.outerWidth(true) }, $settings['slideSpeed'] * 1000);
				nextView.animate({ left: 0 }, $settings['slideSpeed'] * 1000);

				// Hide current view
				setTimeout(function(){ currentView.stop(true, true).css('top', $container.outerHeight(true)); }, $settings['slideSpeed'] * 1000);

				break;
			case 'slideUp':

				// Prepare next view
				nextView.css({
					top: $content.height(),
					left: 0
				});

				// Animate
				currentView.animate({ top: -currentView.outerHeight(true) }, $settings['slideSpeed'] * 1000);
				nextView.animate({ top: 0 }, $settings['slideSpeed'] * 1000);

				// Hide current view
				setTimeout(function(){ currentView.stop(true, true).css('top', $container.outerHeight(true)); }, $settings['slideSpeed'] * 1000);

				break;
			case 'slideDown':

				// Prepare next view
				nextView.css({
					top: -$content.height(),
					left: 0
				});

				// Animate
				currentView.animate({ top: currentView.outerHeight(true) }, $settings['slideSpeed'] * 1000);
				nextView.animate({ top: 0 }, $settings['slideSpeed'] * 1000);

				// Hide current view
				setTimeout(function(){ currentView.stop(true, true).css('top', $container.outerHeight(true)); }, $settings['slideSpeed'] * 1000);

				break;
			case 'fade':

				// Prepare next view
				nextView.css({
					top: 0,
					left: 0,
					display: 'none'
				});

				// Animate
				currentView.fadeOut(($settings['slideSpeed'] * 1000) / 2);
				setTimeout(
					function(){
						nextView.fadeIn(($settings['slideSpeed'] * 1000) / 2);

						currentView.stop(true, true).css({
							top: $container.outerHeight(true),
							display: 'block'
						});
					},
					($settings['slideSpeed'] * 1000) / 2
				);

				break;
			case 'directFade':

				// Prepare next view
				nextView.css({
					top: 0,
					left: 0,
					'z-index': 0,
					display: 'none'
				});
				currentView.css({ 'z-index': 1 });

				// Animate
				nextView.fadeIn($settings['slideSpeed'] * 1000);
				currentView.fadeOut($settings['slideSpeed'] * 1000);
				setTimeout(
					function(){

						nextView.stop(true, true).css({ 'z-index': 'auto' });
						currentView.stop(true, true).css({
							top: $container.outerHeight(true),
							display: 'block',
							'z-index': 'auto'
						});
					},
					$settings['slideSpeed'] * 1000
				);

				break;
		}

		// Update visible views array after animating
		setTimeout(function(){ $visibleViews = [ viewId ]; }, $settings['slideSpeed'] * 1000);

		// Re-enable navigation
		setTimeout(function(){ $navigationActive = true; }, $settings['slideSpeed'] * 1000);
	}

	/**
	 * Set container width to parent width, set overflow width to container width
	 * Only calculates the width when no default width was set.
	 *
	 * If recalculateVisibleViews is set to false, visible views won't be recalculated. If left empty or true, they will.
	 *
	 * @param recalculateViews (optional, defaults to true)
	 */
	function recalculate(recalculateViews){

		// Check for slideshow's visibility. If it's invisible, start polling for visibility, then return
		if(!$container.is(':visible')){

			// Poll as long as the slideshow is invisible. When visible recalculate and cancel polling
			$invisibilityTimer = setInterval(function(){
				if($container.is(':visible')){
					recalculate();
					clearInterval($invisibilityTimer);
					$invisibilityTimer = '';
				}
			}, 500);

			return;
		}

		// Walk up the DOM looking for elements with a width the slideshow can use to adjust to
		var parentElement = $parentElement;
		for(var i = 0; parentElement.width() <= 0; i++){
			parentElement = parentElement.parent();

			if(i > 50)
				break;
		}

		// Exit when the slideshow's parent element hasn't changed in width
		if($currentWidth == parentElement.width())
			return;

		// Set current width variable to parent element width to be able to check if any change in width has occurred
		$currentWidth = parentElement.width();

		// Calculate slideshow's maximum width
		var width = parentElement.width() - ($container.outerWidth(true) - $container.width());
		if($settings['maxWidth'] > 0 && $settings['maxWidth'] < width)
			width = $settings['maxWidth'];

		// Set width
		$container.css('width', Math.floor(width));
		$content.css('width', Math.floor(width) - ($content.outerWidth(true) - $content.width()));

		// Calculate and set the heights
		if($settings['preserveSlideshowDimensions']){
			var height = (width * $settings['dimensionHeight']) / $settings['dimensionWidth'];
			$container.css('height', Math.floor(height));
			$content.css('height', Math.floor(height) - ($content.outerHeight(true) - $content.height()));
		}else{
			$container.css('height', Math.floor($settings['height']));
			$content.css('height', Math.floor($settings['height']));
		}

		// Recalculate hiding position of hidden views
		$views.each(function(viewId, view){

			if($.inArray(viewId, $visibleViews) < 0)
				$(view).css('top', $container.outerHeight(true));
		});

		// Recalculate all views in animation
		if(recalculateViews || recalculateViews == undefined){
			recalculateVisibleViews();}
	}

	/**
	 * Recalculates all slides that are currently defined as being in state of animation. Uses recalculateView() to
	 * recalculate every separate view.
	 */
	function recalculateVisibleViews(){

		// Loop through viewsInAnimation array
		$.each($visibleViews, function(key, viewId){
			recalculateView(viewId);
		});
	}

	/**
	 * Calculates all slides' heights and widths in the passed view, keeping their border widths in mind.
	 *
	 * TODO Implement separate slide widths. This can be done by making use of the $viewData array using a 'width'
	 * TODO variable.
	 *
	 * @param viewId
	 */
	function recalculateView(viewId){

		// Create jQuery object from view
		view = $($views[viewId]);

		// Return when the slideshow's width hasn't changed
		if($content.width() == view.outerWidth(true))
			return;

		// Find slides in view
		var slides = view.find('.slideshow_slide');
		if(slides.length <= 0)
			return;

		var viewWidth = $content.width() - (view.outerWidth(true) - view.width());
		var viewHeight = $content.height() - (view.outerHeight(true) - view.height());

		var slideWidth = Math.floor(viewWidth / slides.length);
		var slideHeight = viewHeight;
		var spareWidth = viewWidth % slides.length;
		var totalWidth = 0;

		// Cut off left and right margin of slides
		$(slides[0]).css('margin-left', 0);
		$(slides[slides.length - 1]).css('margin-right', 0);

		$.each(slides, function(slideId, slide){

			// Instantiate slide as jQuery object
			slide = $(slide);

			// Calculate slide dimensions
			var outerWidth = slide.outerWidth(true) - slide.width();
			var outerHeight = slide.outerHeight(true) - slide.height();

			// Add spare width pixels to the last slide
			if(slideId == (slides.length - 1))
				slide.width((slideWidth - outerWidth) + spareWidth);
			else
				slide.width(slideWidth - outerWidth);
			slide.height(slideHeight - outerHeight);

			// Each slide type has type specific features
			if(slide.hasClass('slideshow_slide_text')){

				var anchor = slide.find('a');
				if(anchor.length <= 0)
					return;

				// Calculate image width and height
				var anchorWidth = slide.width() - (anchor.outerWidth(true) - anchor.width());
				var anchorHeight = slide.height() - (anchor.outerHeight(true) - anchor.height());

				// Set anchor width and height
				anchor.css({
					'width': anchorWidth,
					'height': anchorHeight
				});

			}else if(slide.hasClass('slideshow_slide_image')){

				var image = slide.find('img');
				if(image.length <= 0)
					return;

				// Calculate image width and height
				var maxImageWidth = slide.width() - (image.outerWidth(true) - image.width());
				var maxImageHeight = slide.height() - (image.outerHeight(true) - image.height());

				// If stretch images is true, stretch to the slide's sizes.
				if($settings['stretchImages']){

					image.css({
						width: maxImageWidth,
						height: maxImageHeight
					});

				}else if(image.width() > 0 && image.height() > 0){ // If stretch images is false, keep image dimensions

					// Falls off (worse than too small):

					var imageDimension = $viewData[viewId][slideId]['imageDimension'];
					if(imageDimension == '')
						imageDimension = $viewData[viewId][slideId]['imageDimension'] = image.outerWidth(true) / image.outerHeight(true);
					var slideDimension = slide.width() / slide.height();

					if(imageDimension > slideDimension){ // Image has a wider dimension than the slide

						// Remove auto centering
						image.css({
							'margin': '0px'
						});

						// Set width to slide's width, keep height in same dimension
						image.css({
							width: maxImageWidth,
							height: Math.floor(maxImageWidth / imageDimension)
						});

					}else if(imageDimension < slideDimension){ // Image has a slimmer dimension than the slide

						// Set height to slide's height, keep width in same dimension, center image
						image.css({
							width: Math.floor(maxImageHeight * imageDimension),
							height: maxImageHeight,
							'margin': 'auto',
							'margin-right': 'auto',
							'display': 'block'
						});
					}
				}

			}else if(slide.hasClass('slideshow_slide_video')){

				var videoElement = slide.find('iframe');
				console.log(videoElement.attr('id'));

				// If the player already exists, adjust its size
				if(videoElement.length > 0){

					videoElement.attr({
						width: slide.width(),
						height: slide.height()
					});

				}else{ // Create a new player

					// Find element and create a unique element ID for it
					var element = slide.find('div');
					element.attr('id', 'slideshow_slide_video_' + Math.floor(Math.random() * 1000000) + '_' + element.text());

//					var tag = document.createElement('script');
//
//					// iFrame API
//					element.src = "//www.youtube.com/iframe_api";
//					var firstScriptTag = document.getElementsByTagName('script')[0];
//					firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

					// Player initialization
					//function onYouTubeIframeAPIReady() {console.log('ready');
					var player = new YT.Player(
						element.attr('id'),
						{
							width: $(slide).width(),
							height: $(slide).height(),
							videoId: element.text(),
							events: {
								'onReady': function(){ $('#' + element.attr('id')).show(); console.log('ready'); },
								'onStateChange': function(x){ console.log('change state'); console.log(x); }
							}
						}
					);
					//}

					// Create player
//					swfobject.embedSWF(
//						'http://www.youtube.com/v/' + element.text() + '?version=3&enablejsapi=1&playerapiid=' + element.attr('id'),
//						element.attr('id'),
//						jQuery(slide).width(),
//						jQuery(slide).height(),
//						'9',
//						null,
//						null,
//						{
//							allowScriptAccess: 'always',
//							allowFullScreen: true,
//							wmode: 'opaque'
//						},
//						{ id: element.attr('id') }
//					);

					// Register player ID to be able to read wether or not it's playing
					//$youtubePlayerIds.push(element.attr('id'));
					//$videoPlayers.push(player);
					$videoPlayers[element.attr('id')] = { 'player': player, 'state': -1 };
				}
			}

			// Add up total width
			totalWidth += slide.outerWidth(true);
		});

		view.css({
			'width': viewWidth,
			'height': viewHeight
		});
	}

	/**
	 * Returns true if a video is playing, returns false otherwise.
	 *
	 * @return boolean videoIsPlaying
	 */
	function videoIsPlaying(){

		var videoIsPlaying = false;

		// Loop through players
		$.each($videoPlayers, function(key, videoPlayer){
//			var state = -1;
//			var player = document.getElementById(playerId);
			player = videoPlayer.player;

			// Check if retrieved player is an instance of the YouTube API. If so, check its state.
			if(player != null && typeof player.getPlayerState === 'function')
				state = player.getPlayerState();

			// State can be one of the following: Unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5)
			if(state == 1 || state == 3)
				videoIsPlaying = true;
		});

		return videoIsPlaying;
	}

	/**
	 * Pauses all videos
	 */
	function pauseAllVideos(){

		$.each($videoPlayers, function(key, player){

			player.pauseVideo();
		});
	}

	/**
	 * Activates description boxes. Only activates when showDescription is set to true.
	 */
	function activateDescriptions(){

		// Only show when showDescription is true
		if(!$settings['showDescription'])
			return;

		// Loop through descriptions. Show them and if they need to be hidden, hide them
		$.each($slides.find('.slideshow_description'), function(key, description){

			$(description).show();

			if($settings['hideDescription'])
				$(description).css({
					'position': 'absolute',
					'bottom': -$(description).outerHeight(true)
				});
		});

		// Return early when descriptions should not be hidden
		if(!$settings['hideDescription'])
			return;

		// Hide descriptions
		$container.bind('onSlideshowAnimate', function(){

			if($visibleViews[1] == undefined)
				return;

			$.each($($views[$visibleViews[1]]).find('.slideshow_description'), function(key, description){
				$(description).css('bottom', -$(description).outerHeight(true));
			});
		});

		// Register a mouse enter event to animate showing the description boxes.
		$slides.mouseenter(function(){

			// Find description and stop its current animation, then start animating it in
			$(this)
				.find('.slideshow_description')
				.stop(true, true)
				.animate({ 'bottom': 0 }, parseInt($settings['descriptionSpeed'] * 1000));
		});

		// Register a mouse leave event to animate hiding the description boxes.
		$slides.mouseleave(function(){

			// Find description and stop its current animation, then start animating it out
			$(this)
				.find('.slideshow_description')
				.stop(true, true)
				.animate({ 'bottom': -$(this).outerHeight(true) }, parseInt($settings['descriptionSpeed'] * 1000));
		});
	}

	/**
	 * Activates previous and next buttons, then shows them. Only activates the buttons when controllable is true.
	 */
	function activateNavigationButtons(){

		// Only show buttons if the slideshow is controllable
		if(!$settings['controllable'])
			return;

		// Register next button click event
		$nextButton.click(function(){

			if(!$navigationActive)
				return;

			pauseAllVideos();

			stop();
			animateTo(getNextViewId(), 1);
			start();
		});

		// Register previous button click event
		$previousButton.click(function(){

			if(!$navigationActive)
				return;

			pauseAllVideos();

			stop();
			animateTo(getPreviousViewId(), -1);
			start();
		});

		// If hideNavigationButtons is true, fade them in and out on mouse enter and leave. Simply show them otherwise
		if($settings['hideNavigationButtons']){
			$container.mouseenter(function(){ $nextButton.stop(true, true).fadeIn(100); });
			$container.mouseleave(function(){ $nextButton.stop(true, true).fadeOut(500); });

			$container.mouseenter(function(){ $previousButton.stop(true, true).fadeIn(100); });
			$container.mouseleave(function(){ $previousButton.stop(true, true).fadeOut(500); });
		}else{
			$nextButton.show();
			$previousButton.show();
		}
	}

	/**
	 * Activates control panel consisting of the play and pause buttons. Only activates the control panel when control
	 * panel is set to true.
	 */
	function activateControlPanel(){

		// Don't activate control panel when it's set to false
		if(!$settings['controlPanel'])
			return;

		// Set button to pause when slideshow is initially running, otherwise set button to play
		if($settings['play'])
			$togglePlayButton.attr('class', 'slideshow_pause');
		else
			$togglePlayButton.attr('class', 'slideshow_play');

		// Register click event on the togglePlayButton
		$togglePlayButton.click(function(){
			if($settings['play']){
				$settings['play'] = false;
				$(this).attr('class', 'slideshow_play');
				stop();
			}
			else{
				$settings['play'] = true;
				$(this).attr('class', 'slideshow_pause');
				start();
			}
		});

		// If hideControlPanel is true, fade it in and out on mouse enter and leave. Simply show it otherwise
		if($settings['hideControlPanel']){
			$container.mouseenter(function(){ $controlPanel.stop(true, true).fadeIn(100); });
			$container.mouseleave(function(){ $controlPanel.stop(true, true).fadeOut(500); });
		}else{
			$controlPanel.show();
		}
	}

	/**
	 * Activates the pagination bullets on the slideshow. Only activates the pagination bullets when show pagination
	 * is set to true.
	 */
	function activatePagination(){

		// Only show pagination bullets when showPagination is set to true
		if(!$settings['showPagination'])
			return;

		// Find ul to add view-bullets to
		var ul = $pagination.find('ul');
		ul.html('');
		$views.each(function(key, view){

			// Only add currentView class to currently active view-bullet
			var currentView = '';
			if(key == $currentViewId)
				currentView = 'slideshow_currentView';

			// Add list item
			ul.append(
				'<li class="slideshow_transparent ' + currentView + '">' +
					'<span style="display: none;">' +
						key +
					'</span>' +
				'</li>'
			);
		});

		// On click of a view-bullet go to the corresponding slide
		$pagination.find('li').click(function(){

			if(!$navigationActive)
				return;

			// Find view ID and check if it's not empty
			var viewId = $(this).find('span').text();
			if(viewId == '' || viewId == undefined)
				return;

			pauseAllVideos();

			// Animate to view ID
			stop();
			animateTo(parseInt(viewId), 0);
			start();
		});

		// Bind onSlideshowAnimate to pagination to shift currently active view-bullets
		$container.bind(
			'onSlideshowAnimate',
			function(){

				// Get bullets
				var bullets = $pagination.find('li');

				// Remove all currentView classes from the bullets and add the currentView class to the current bullet
				bullets.each(function(key, bullet){ $(bullet).removeClass('slideshow_currentView'); });
				$(bullets[$currentViewId]).addClass('slideshow_currentView');
			}
		);

		// If hidePagination is true, fade it in and out on mouse enter and leave. Simply show it otherwise
		if($settings['hidePagination']){
			$container.mouseenter(function(){ $pagination.stop(true, true).fadeIn(100); });
			$container.mouseleave(function(){ $pagination.stop(true, true).fadeOut(500); });
		}else{
			$pagination.show();
		}
	}

	/**
	 * Activate the pause on hover functionality. Pauses the slideshow on hover over the container, but only when the
	 * mouse remains on there for more than half a second so that it doesn't pause on fly-over.
	 */
	function activatePauseOnHover(){

		// Exit when pauseOnHover is false
		if(!$settings['pauseOnHover'])
			return;

		// Pause the slideshow when the mouse enters the slideshow container.
		$container.mouseenter(function(){

			// Wait 500 milliseconds before pausing the slideshow. If within this time the mouse hasn't left the container, pause.
			clearTimeout($mouseEnterTimer);
			$mouseEnterTimer = setTimeout(function(){ stop(); }, 500);
		});

		// Continue the slideshow when the mouse leaves the slideshow container.
		$container.mouseleave(function(){

			// This will cancel any pausing when the mouse simply flies over, instead of hovering.
			clearTimeout($mouseEnterTimer);

			// Start slideshow, but only when the interval has been stopped
			if($interval === false)
				start();
		});
	}

	/**
	 * Returns the next view ID, is a random number when random is true.
	 *
	 * @return int viewId
	 */
	function getNextViewId(){

		// Return a random ID when random is true
		if($settings['random']){
			var oldViewId = viewId;
			viewId = Math.floor(Math.random() * $views.length);

			// Only return when it's not the same ID as before
			if(viewId != oldViewId)
				return viewId;
		}

		// Get current view ID. If it's undefined, set it to 0
		var viewId = $currentViewId;
		if(viewId == undefined)
			return 0;

		// When the end of the views array is reached, return to the first view
		if(viewId >= $views.length - 1){

			// When animation should loop, start over, otherwise stay on same view
			if($settings['loop'])
				return viewId = 0;
			else
				return $currentViewId;
		}

		// Increment
		return viewId += 1;
	}

	/**
	 * Returns the previous view ID, is a random number when random is true.
	 *
	 * @return int viewId
	 */
	function getPreviousViewId(){

		// Get current view ID. If it's undefined, set it to 0
		var viewId = $currentViewId;
		if(viewId == undefined)
			viewId = 0;

		// Return a random ID when random is true
		if($settings['random']){
			var oldViewId = viewId;
			viewId = Math.floor(Math.random() * $views.length);

			// Only return when it's not the same ID as before
			if(viewId != oldViewId)
				return viewId;
		}

		// When the start of the views array is reached, go to the last view
		if(viewId <= 0){

			// When animation should loop, go to the last view, otherwise stay on same view
			if($settings['loop'])
				return viewId = $views.length - 1;
			else
				return $currentViewId;
		}

		// Increment
		return viewId -= 1;
	}

	/**
	 * Returns the unique ID of this slideshow
	 *
	 * @return int $ID
	 */
	function getID(){
		// Get container classes
		var splitClasses = $container.attr('class').split('_');

		// Get last of classes
		return splitClasses[splitClasses.length - 1];
	}
};



jQuery(document).ready(function(){
	jQuery.each(jQuery('.slideshow_container'), function(key, slideshow){
		jQuery(slideshow).slideshow_jquery_image_gallery_script();
	});
});