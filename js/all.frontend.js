slideshow_jquery_image_gallery_backend_script_scriptsloadedFlag = false;

/**
 * Slideshow frontend script
 *
 * @author Stefan Boonstra
 * @version 4
 */
slideshow_jquery_image_gallery_script = function()
{
	var $    = jQuery,
		self = {};

	self.slideshowInstances = { };
	self.initialized        = false;
	self.youTubeAPIReady    = false;
	self.sessionIDCounter   = 0;

	/**
	 * Called by either $(document).ready() or $(window).load() to initialize the slideshow's script.
	 */
	self.init = function()
	{
		if (slideshow_jquery_image_gallery_backend_script_scriptsloadedFlag !== true ||
			self.initialized)
		{
			return;
		}

		self.initialized = true;

		$(document).trigger('slideshow_jquery_image_gallery_script_ready');

		//self.loadYouTubeAPI();
		self.repairStylesheetURLs();
		self.activateSlideshows();
		self.enableLazyLoading();

		$(document).trigger('slideshow_jquery_image_gallery_slideshows_ready');
	};

	/**
	 * @param searchKey (int|jQuery)
	 *
	 * @return self.Slideshow
	 */
	self.getSlideshowInstance = function(searchKey)
	{
		if (!isNaN(parseInt(searchKey, 10)))
		{
			if (self.slideshowInstances[searchKey] instanceof self.Slideshow)
			{
				return self.slideshowInstances[searchKey];
			}
		}
		else if (searchKey instanceof $ &&
				 searchKey.length > 0)
		{
			for (var ID in self.slideshowInstances)
			{
				if (!self.slideshowInstances.hasOwnProperty(ID))
				{
					continue;
				}

				var slideshowInstance = self.slideshowInstances[ID];

				if (slideshowInstance instanceof self.Slideshow &&
					slideshowInstance.$container.get(0) === searchKey.get(0))
				{
					return slideshowInstance;
				}
			}
		}

		return new self.Slideshow();
	};

	/**
	 * Calls the activateSlideshow method on all slideshows on the page.
	 */
	self.activateSlideshows = function()
	{
		$.each($('.slideshow_container'), function(key, slideshowElement)
		{
			self.activateSlideshow($(slideshowElement));
		});
	};

	/**
	 * Activate a slideshow instance on the passed slideshow container element, provided it has not yet been activated.
	 *
	 * @param $slideshowElement (jQuery)
	 */
	self.activateSlideshow = function($slideshowElement)
	{
		if ($slideshowElement.hasClass('slideshow_container') &&
			$slideshowElement.attr('data-slideshow-active') != '1')
		{
			$slideshowElement.attr('data-slideshow-active', '1');

			self.slideshowInstances[self.sessionIDCounter] = new self.Slideshow($slideshowElement);

			self.sessionIDCounter++;
		}
	};

	/**
	 * This method starts a MutationObserver to watch for any lazy loaded slideshows. However, MutationObserver is not
	 * available on older browsers. Therefore a fallback to polling is required on these unsupported browsers.
	 */
	self.enableLazyLoading = function()
	{
		var observer;

		// Test if there's browser support for finding DOM mutations (most lightweight)
		if (typeof(MutationObserver) == 'function')
		{
			observer = new MutationObserver(function(mutations)
			{
				mutations.forEach(function(mutation)
				{
					var i;

					if (!mutation.addedNodes)
					{
						return;
					}

					for (i = 0; i < mutation.addedNodes.length; i++)
					{
						// Try to find and activate all slideshows in the current node, including the node itself
						$.each($(mutation.addedNodes[i]).find('.slideshow_container').addBack('.slideshow_container'), function(key, slideshowElement)
						{
							self.activateSlideshow($(slideshowElement));
						});
					}
				});
			});

			observer.observe(document.body, {
				childList    : true,
				subtree      : true,
				attributes   : false,
				characterData: false
			});
		}
		// Fallback on polling, more resource intensive
		else
		{
			setInterval(function()
			{
				$.each($('.slideshow_container:not([data-slideshow-active])'), function(key, slideshowElement)
				{
					self.activateSlideshow($(slideshowElement));
				});
			}, 2000);
		}
	};

	/**
	 * Loads the YouTube API.
	 */
	self.loadYouTubeAPI = function()
	{
		if (self.loadYouTubeAPICalled)
		{
			return;
		}

		self.loadYouTubeAPICalled = true;

		var tag            = document.createElement('script'),
			firstScriptTag = document.getElementsByTagName('script')[0];

		tag.src = "//www.youtube.com/iframe_api";

		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	};

	/**
	 * Repairs stylesheet URLs that have been damaged during output.
	 */
	self.repairStylesheetURLs = function()
	{
		var ajaxStylesheets = $('[id*="slideshow-jquery-image-gallery-ajax-stylesheet_"]');

		// Some website disable URL variables, impairing the AJAX loaded stylesheets. Check and fix all slideshow stylesheet related URLs
		$.each(ajaxStylesheets, function(ajaxStylesheetKey, ajaxStylesheet)
		{
			var $ajaxStylesheet = $(ajaxStylesheet),
				URL             = $(ajaxStylesheet).attr('href'),
				styleNameParts,
				styleName,
				URLData;

			if (URL === undefined ||
				URL === '')
			{
				return;
			}

			// Get the style name from the element's ID. Splits at the first underscore and removes WordPress' stylesheet suffix: '-css'
			styleNameParts = $ajaxStylesheet.attr('id').split('_');
			styleName      = styleNameParts.splice(1, styleNameParts.length - 1).join('_').slice(0, -4);

			URLData = URL.split('?');

			if (URLData[1] === undefined ||
				URLData[1] === '' ||
				URLData[1].toLowerCase().indexOf('style=') < 0)
			{
				URLData[1] =
					'action=slideshow_jquery_image_gallery_load_stylesheet' +
						'&style=' + styleName +
						'&ver=' + Math.round((new Date().getTime() / 1000));
			}
			else
			{
				return;
			}

			URL = URLData.join('?');

			$ajaxStylesheet.attr('href', URL);
		});
	};

	/**
	 * Namespaced log method that is browser safe.
	 *
	 * @param message (String)
	 */
	self.log = function(message)
	{
		if (typeof console == 'object')
		{
			console.log('slideshow-jquery-image-gallery', message);
		}
	};

	$(document).ready(function()
	{
		self.init();
	});

	$(window).load(function()
	{
		self.init();
	});

	$.fn.getSlideshowInstance = function()
	{
		return self.getSlideshowInstance(this);
	};

	return self;
}();

/**
 * This function must be named "onYouTubeIframeAPIReady", as it is needed to check whether or not the YouTube IFrame API
 * has loaded.
 */
function onYouTubeIframeAPIReady()
{
	slideshow_jquery_image_gallery_script.youTubeAPIReady = true;
}

// @codekit-append frontend/slideshow.js
// @codekit-append frontend/scriptsLoadedFlag.js

///**
//* Simple logging function for Internet Explorer
//*
//* @param message
//*/
//function log(message)
//{
//	var $ = jQuery;
//
//	$('body').prepend('<p style="color: red;">' + message +  '</p>');
//}
