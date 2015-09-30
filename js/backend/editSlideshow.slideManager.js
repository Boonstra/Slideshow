slideshow_jquery_image_gallery_backend_script.editSlideshow.slideManager = function()
{
	var $    = jQuery,
		self = { };

	self.uploader = null;

	/**
	 *
	 */
	self.init = function()
	{
		if (slideshow_jquery_image_gallery_backend_script.editSlideshow.isCurrentPage)
		{
			self.activate();
		}
	};

	/**
	 * Activate edit slideshow functionality.
	 */
	self.activate = function()
	{
		// Index first
		self.indexSlidesOrder();

		// Make list items in the sortables list sortable, exclude elements by using the cancel option
		$('.sortable-slides-list').sortable({
			revert: true,
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			stop: function()
			{
				self.indexSlidesOrder();
			},
			cancel: 'input, select, textarea'
		});

		// Add the wp-color-picker plugin to the color fields
		$('.wp-color-picker-field').wpColorPicker({ width: 234 });

		// Open all slides on click
		$('.open-slides-button').on('click', function(event)
		{
			event.preventDefault();

			$('.sortable-slides-list .sortable-slides-list-item').each(function(listItemIndex, listItem)
			{
				var $listItem = $(listItem);

				if (!$listItem.find('.inside').is(':visible'))
				{
					$listItem.find('.handlediv').trigger('click');
				}
			});
		});

		// Close all slides on click
		$('.close-slides-button').on('click', function(event)
		{
			event.preventDefault();

			$('.sortable-slides-list .sortable-slides-list-item').each(function(listItemIndex, listItem)
			{
				var $listItem = $(listItem);

				if ($listItem.find('.inside').is(':visible'))
				{
					$listItem.find('.handlediv').trigger('click');
				}
			});
		});

		// Bind insert buttons
		$('.slideshow-insert-text-slide').on('click' , self.insertTextSlide);
		$('.slideshow-insert-video-slide').on('click', self.insertVideoSlide);
		$('.slideshow-insert-image-slide').on('click', self.mediaUploader);

		// Call self.deleteSlide on click
		$('.slideshow-delete-slide').on('click', function(event)
		{
			self.deleteSlide($(event.currentTarget).closest('.sortable-slides-list-item'));
		});
	};

	/**
	 * Deletes slide from DOM
	 *
	 * @param $slide
	 */
	self.deleteSlide = function($slide)
	{
		var confirmMessage = 'Are you sure you want to delete this slide?',
			extraData      = window.slideshow_jquery_image_gallery_backend_script_editSlideshow;

		if (typeof extraData === 'object' &&
			typeof extraData.localization === 'object' &&
			extraData.localization.confirm !== undefined &&
			extraData.localization.confirm.length > 0)
		{
			confirmMessage = extraData.localization.confirm;
		}

		if(!confirm(confirmMessage))
		{
			return;
		}

		// Remove slide from DOM
		$slide.remove();
	};

	/**
	 * Loop through sortable slides list items, setting slide orders
	 */
	self.indexSlidesOrder = function()
	{
		// Loop through sortables
		$('.sortable-slides-list .sortable-slides-list-item').each(function(slideID, slide)
		{
			// Loop through all fields to set their name attributes with the new index
			$.each($(slide).find('input, select, textarea'), function(key, input)
			{
				var $input = $(input),
					name   = $input.attr('name');

				if (name === undefined ||
					name.length <= 0)
				{
					return;
				}

				name = name.replace(/[\[\]']+/g, ' ').split(' ');

				// Put name with new order ID back on the page
				$input.attr('name', name[0] + '[' + (slideID + 1) + '][' + name[2] + ']');
			});
		});
	};

	/**
	 * Opens the WordPress 3.5 media uploader.
	 */
	self.mediaUploader = function(event)
	{
		event.preventDefault();

		var uploaderTitle,
			externalData;

		// Reopen file frame if it has already been created
		if (self.uploader)
		{
			self.uploader.open();

			return;
		}

		externalData = window.slideshow_jquery_image_gallery_backend_script_editSlideshow;

		uploaderTitle = '';

		if (typeof externalData === 'object' &&
			typeof externalData.localization === 'object' &&
			externalData.localization.uploaderTitle !== undefined &&
			externalData.localization.uploaderTitle.length > 0)
		{
			uploaderTitle = externalData.localization.uploaderTitle;
		}

		// Create the uploader
		self.uploader = wp.media.frames.slideshow_jquery_image_galler_uploader = wp.media({
			frame   : 'select',
			title   : uploaderTitle,
			multiple: true,
			library :
			{
				type: 'image'
			}
		});

		// Create image slide on select
		self.uploader.on('select', function()
		{
			var attachments = self.uploader.state().get('selection').toJSON(),
				attachment,
				attachmentID;

			for (attachmentID in attachments)
			{
				if (!attachments.hasOwnProperty(attachmentID))
				{
					continue;
				}

				attachment = attachments[attachmentID];

				self.insertImageSlide(attachment.id, attachment.title, attachment.description, attachment.url, attachment.alt);
			}
		});

		self.uploader.open();
	};

	/**
	 * Inserts image slide into the slides list
	 *
	 * @param id
	 * @param title
	 * @param description
	 * @param src
	 * @param alternativeText
	 */
	self.insertImageSlide = function(id, title, description, src, alternativeText)
	{
		// Find and clone the image slide template
		var $imageSlide = $('.image-slide-template').find('.sortable-slides-list-item').clone(true, true);

		// Fill slide with data
		$imageSlide.find('.attachment').attr('src', src);
		$imageSlide.find('.attachment').attr('title', title);
		$imageSlide.find('.attachment').attr('alt', alternativeText);
		$imageSlide.find('.title').attr('value', title);
		$imageSlide.find('.description').html(description);
		$imageSlide.find('.alternativeText').attr('value', alternativeText);
		$imageSlide.find('.postId').attr('value', id);

		// Set names to be saved to the database
		$imageSlide.find('.title').attr('name', 'slides[0][title]');
		$imageSlide.find('.titleElementTagID').attr('name', 'slides[0][titleElementTagID]');
		$imageSlide.find('.description').attr('name', 'slides[0][description]');
		$imageSlide.find('.descriptionElementTagID').attr('name', 'slides[0][descriptionElementTagID]');
		$imageSlide.find('.url').attr('name', 'slides[0][url]');
		$imageSlide.find('.urlTarget').attr('name', 'slides[0][urlTarget]');
		$imageSlide.find('.alternativeText').attr('name', 'slides[0][alternativeText]');
        $imageSlide.find('.noFollow').attr('name', 'slides[0][noFollow]');
		$imageSlide.find('.type').attr('name', 'slides[0][type]');
		$imageSlide.find('.postId').attr('name', 'slides[0][postId]');

		// Put slide in the sortables list.
		$('.sortable-slides-list').prepend($imageSlide);

		// Reindex
		self.indexSlidesOrder();
	};

	/**
	 * Inserts text slide into the slides list
	 */
	self.insertTextSlide = function()
	{
		// Find and clone the text slide template
		var $textSlide = $('.text-slide-template').find('.sortable-slides-list-item').clone(true, true);

		// Set names to be saved to the database
		$textSlide.find('.title').attr('name', 'slides[0][title]');
		$textSlide.find('.titleElementTagID').attr('name', 'slides[0][titleElementTagID]');
		$textSlide.find('.description').attr('name', 'slides[0][description]');
		$textSlide.find('.descriptionElementTagID').attr('name', 'slides[0][descriptionElementTagID]');
		$textSlide.find('.textColor').attr('name', 'slides[0][textColor]');
		$textSlide.find('.color').attr('name', 'slides[0][color]');
		$textSlide.find('.url').attr('name', 'slides[0][url]');
		$textSlide.find('.urlTarget').attr('name', 'slides[0][urlTarget]');
        $textSlide.find('.noFollow').attr('name', 'slides[0][noFollow]');
		$textSlide.find('.type').attr('name', 'slides[0][type]');

		// Add color picker
		$textSlide.find('.color, .textColor').wpColorPicker();

		// Put slide in the sortables list.
		$('.sortable-slides-list').prepend($textSlide);

		// Reindex slide orders
		self.indexSlidesOrder();
	};

	/**
	 * Inserts video slide into the slides list
	 */
	self.insertVideoSlide = function()
	{
		// Find and clone the video slide template
		var $videoSlide = $('.video-slide-template').find('.sortable-slides-list-item').clone(true, true);

		// Set names to be saved to the database
		$videoSlide.find('.videoId').attr('name', 'slides[0][videoId]');
		$videoSlide.find('.showRelatedVideos').attr('name', 'slides[0][showRelatedVideos]');
		$videoSlide.find('.type').attr('name', 'slides[0][type]');

		// Put slide in the sortables list.
		$('.sortable-slides-list').prepend($videoSlide);

		// Reindex slide orders
		self.indexSlidesOrder();
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();