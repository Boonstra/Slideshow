slideshow_jquery_image_gallery_backend_script.slideshow.slideManager = function()
{
	var $    = jQuery,
		self = { };

	self.uploader = null;

	/**
	 *
	 */
	self.init = function()
	{
		if (!slideshow_jquery_image_gallery_backend_script.slideshow.isCurrentPage)
		{
			return;
		}

		self.$slidesMetaBox      = $('#_slideshow_jquery_image_gallery_slides');
		self.$slidesGrid         = self.$slidesMetaBox.find('.slideshow-slides-grid');
		self.$slideshowTemplates = self.$slidesMetaBox.find('.slideshow-templates');

		// Make slides in grid sortable
		self.$slidesGrid.sortable({
			revert: true,
			placeholder: 'slideshow-slide-placeholder',
			stop: function()
			{
//				self.indexImageOrder(); // TODO Re-implement and re-enable
			},
			cancel: 'input, select, textarea, span, .slideshow-slide-editor', // Prevents elements being draggable
			helper: 'clone' // Prevents click event after dragging
		});

		self.$slidesGrid.find('.slideshow-slide').on('click', function(event)
		{
			var $slide              = $(event.currentTarget),
				slideOffsetTop      = $slide.offset().top,
				$slideEditor        = self.$slideshowTemplates.find('.slideshow-slide-editor').clone(true, true),
				isSlideEditorPlaced = false;

			

			// Try to place the slide editor a row below the clicked slide
			self.$slidesGrid.find('.slideshow-slide').each(function()
			{
				var $currentSlide = $(this);

				if ($currentSlide.offset().top > slideOffsetTop && isSlideEditorPlaced === false)
				{
					$currentSlide.before($slideEditor);

					isSlideEditorPlaced = true;
				}
			});

			// If the slide editor hasn't yet been placed, append it to the grid
			if (!isSlideEditorPlaced)
			{
				self.$slidesGrid.append($slideEditor);
			}
		});
	};

	/**
	 * 2.3.0 method
	 */
	self.closeSlideEditors = function()
	{
		self.$slidesGrid.find('.slideshow-slide-editor').each(function()
		{
			$(this).animate({height: '0px'}, function()
			{
				$(this).remove();
			});
		});
	};

	$(document).bind('slideshowBackendReady', self.init);

	return self;
}();