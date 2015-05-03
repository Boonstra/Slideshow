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

		self.loadYouTubeAPI();
		self.repairStylesheetURLs();
		self.activateSlideshows();

		$(document).trigger('slideshow_jquery_image_gallery_slideshows_ready');
	};

	/**
	 * @param searchKey (int|jQuery)
	 *
	 * @return null|self.Slideshow|[self.Slideshow, ...]
	 */
	self.getSlideshowInstance = function(searchKey)
	{
		var slideshowInstanceIndex,
			id;

		// Get by ID. May return an array of slideshows with the same ID if multiple matches are found
		if (!isNaN(parseInt(searchKey, 10)))
		{
			if (self.slideshowInstances[searchKey] instanceof self.Slideshow ||
				$.isArray(self.slideshowInstances[searchKey]))
			{
				return self.slideshowInstances[searchKey];
			}
		}
		// Get by element. Returns a single slideshow if a match is found
		else if (searchKey instanceof $ &&
				 searchKey.length > 0)
		{
			for (id in self.slideshowInstances)
			{
				if (!self.slideshowInstances.hasOwnProperty(id))
				{
					continue;
				}

				if ($.isArray(self.slideshowInstances[id]))
				{
					for (slideshowInstanceIndex = 0; slideshowInstanceIndex < self.slideshowInstances[id].length; slideshowInstanceIndex++)
					{
						if (self.slideshowInstances[id][slideshowInstanceIndex] instanceof self.Slideshow &&
							self.slideshowInstances[id][slideshowInstanceIndex].$container.get(0) === searchKey.get(0))
						{
							return self.slideshowInstances[id][slideshowInstanceIndex];
						}
					}
				}
				else
				{
					if (self.slideshowInstances[id] instanceof self.Slideshow &&
						self.slideshowInstances[id].$container.get(0) === searchKey.get(0))
					{
						return self.slideshowInstances[id];
					}
				}
			}
		}

		return null;
	};

	/**
	 * Instantiates Slideshow objects on all slideshow elements that have not yet been registered of having a Slideshow
	 * instance.
	 */
	self.activateSlideshows = function()
	{
		$.each(jQuery('.slideshow_container'), function(key, slideshowElement)
		{
			var $slideshowElement = $(slideshowElement),
				isActivated       = $slideshowElement.attr('data-slideshow-activated'),
				id                = $slideshowElement.data('slideshowId'),
				newSlideshow;

			if (isActivated)
			{
				return;
			}

			if (isNaN(parseInt(id, 10)))
			{
				id = $slideshowElement.attr('data-slideshow-id');
			}

			newSlideshow = new self.Slideshow($slideshowElement);

			if ($.isArray(self.slideshowInstances[id]))
			{
				self.slideshowInstances[id].push(newSlideshow);
			}
			else
			{
				if (self.slideshowInstances[id] instanceof self.Slideshow)
				{
					self.slideshowInstances[id] = [self.slideshowInstances[id], newSlideshow];
				}
				else
				{
					self.slideshowInstances[id] = newSlideshow;
				}
			}

			$slideshowElement.attr('data-slideshow-activated', true);
		});
	};

	/**
	 * Loads the YouTube API.
	 */
	self.loadYouTubeAPI = function()
	{
		self.loadYouTubeAPICalled = true;

		if ($('.slideshow_slide_video').length <= 0)
		{
			return;
		}

		var tag            = document.createElement('script'),
			firstScriptTag = document.getElementsByTagName('script')[0];

		tag.src = "//www.youtube.com/iframe_api";

		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	};

	/**
	 * Some WordPress websites don't allow for stylesheets to have URL parameters and remove them before they are output
	 * to the website. When this is the case, AJAX loaded stylesheets can't be loaded and custom styles fail to work.
	 *
	 * This method repairs stylesheet URLs that have been broken by the website.
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
