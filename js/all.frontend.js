/**
 * Slideshow frontend script
 *
 * @author Stefan Boonstra
 * @version 4.1.0
 */
slideshow_jquery_image_gallery_script = function()
{
	var $    = jQuery,
		self = {};

	self.registeredSlideshows = { };
	self.youTubeAPIReady      = false;
	self.loadYouTubeAPICalled = false;
	self.stylesheetURLChecked = false;

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

			if (!(self.registeredSlideshows[ID] instanceof self.Slideshow))
			{
				self.registeredSlideshows[ID] = new self.Slideshow($slideshowElement);
			}
		});
	};

	/**
	 *
	 */
	self.loadYouTubeAPI = function()
	{
		if (self.loadYouTubeAPICalled)
		{
			return;
		}

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
	 *
	 */
	self.checkStylesheetURL = function()
	{
		if (self.stylesheetURLChecked)
		{
			return;
		}

		self.stylesheetURLChecked = true;

		var ajaxStylesheets = $('[id*="slideshow-jquery-image-gallery-ajax-stylesheet_"]');

		if (ajaxStylesheets.length <= 0)
		{
			return;
		}

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
		self.loadYouTubeAPI();
		self.checkStylesheetURL();
		self.activateSlideshows();
	});

	$(window).load(function()
	{
		self.loadYouTubeAPI();
		self.checkStylesheetURL();
		self.activateSlideshows();
	});

	return self;
}();

function onYouTubeIframeAPIReady()
{
	slideshow_jquery_image_gallery_script.youTubeAPIReady = true;
}

// @codekit-append frontend/slideshow.js

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
