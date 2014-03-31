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
	 * Instantiates Slideshow objects on all slideshow elements that have not yet been registered of having a Slideshow
	 * instance.
	 */
	self.activateSlideshows = function()
	{
		$.each(jQuery('.slideshow_container'), function(key, slideshowElement)
		{
			var $slideshowElement = $(slideshowElement),
				ID                = $slideshowElement.data('sessionId');

			if (isNaN(parseInt(ID, 10)))
			{
				ID = $slideshowElement.attr('data-session-id');
			}

			if (!(self.slideshowInstances[ID] instanceof self.Slideshow))
			{
				self.slideshowInstances[ID] = new self.Slideshow($slideshowElement);
			}
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
	 * Repairs stylesheet URLs that have been damaged during output.
	 */
	self.repairStylesheetURLs = function()
	{
		var ajaxStylesheets = $('[id*="slideshow-jquery-image-gallery-ajax-stylesheet_"]');

		// No AJAX stylesheets found. If there are slideshows on the page, there is something wrong. A slideshow always comes with an AJAX stylesheet
		if (ajaxStylesheets.length <= 0)
		{
			self.generateStylesheetURLs(false);

			return;
		}

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
	 * Generates admin AJAX stylesheet URLs for all slideshows.
	 *
	 * When forceGenerate is set to true, it will generate stylesheet URLs for slideshows that already have a matching
	 * stylesheet as well.
	 *
	 * @param forceGenerate (boolean, defaults to false)
	 */
	self.generateStylesheetURLs = function(forceGenerate)
	{
		var $slideshows = $('.slideshow_container'),
			adminURL    = window.slideshow_jquery_image_gallery_script_adminURL;

		if ($slideshows.length <= 0 ||
			typeof adminURL !== 'string' ||
			adminURL.length <= 0)
		{
			return;
		}

		$.each($slideshows, function(key, slideshow)
		{
			var $slideshow   = $(slideshow),
				styleName    = $slideshow.attr('data-style-name'),
				styleVersion = $slideshow.attr('data-style-version'),
				styleID      = 'slideshow-jquery-image-gallery-ajax-stylesheet_' + styleName + '-css',
				$originalStylesheet,
				stylesheetURL;

			if (typeof styleName === 'string' &&
				typeof styleVersion === 'string' &&
				styleName.length > 0 &&
				styleVersion.length > 0)
			{
				if (!forceGenerate &&
					typeof forceGenerate === 'boolean')
				{
					$originalStylesheet = $('#' + styleID);

					if ($originalStylesheet.length > 0)
					{
						return;
					}
				}

				stylesheetURL = adminURL + 'admin-ajax.php?action=slideshow_jquery_image_gallery_load_stylesheet&style=' + styleName + '&ver=' + styleVersion;

				$('head').append('<link rel="stylesheet" id="' + styleID + '" href="' + stylesheetURL + '" type="text/css" media="all">');
			}
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
