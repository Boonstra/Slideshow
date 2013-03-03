jQuery(document).ready(function(){

	// Index first
	slideshowSlideInserterIndexSlidesOrder();

	// Make list items in the sortables list sortable, exclude elements with cancel option.
	jQuery('.sortable-slides-list').sortable({
		revert: true,
		placeholder: 'sortable-placeholder',
		forcePlaceholderSize: true,
		stop: function(event, ui){
			slideshowSlideInserterIndexSlidesOrder();
		},
		cancel: 'input, select, p'
	});

	// Make the black background stretch all the way down the document
	jQuery('#slideshow-slide-inserter-popup-background').height(jQuery(document).outerHeight(true));

	// Center the popup in the window
	jQuery('#slideshow-slide-inserter-popup').css({
		'top': parseInt((jQuery(window).height() / 2) - (jQuery('#slideshow-slide-inserter-popup').outerHeight(true) / 2), 10),
		'left': parseInt((jQuery(window).width() / 2) - (jQuery('#slideshow-slide-inserter-popup').outerWidth(true) / 2), 10)
	});

	// Focus on search bar
	jQuery('#slideshow-slide-inserter-popup #search').focus();

	// Preload attachments
	slideshowSlideInserterGetSearchResults();

	/**
	 * Close popup when clicked on cross
	 */
	jQuery('#slideshow-slide-inserter-popup #close').click(function(){
		slideshowSlideInserterClosePopup();
	});

	/**
	 * Close popup when clicked on background
	 */
	jQuery('#slideshow-slide-inserter-popup-background').click(function(){
		slideshowSlideInserterClosePopup();
	});

	/**
	 * Send ajax request on click of the search button
	 */
	jQuery('#slideshow-slide-inserter-popup #search-submit').click(function(){
		slideshowSlideInserterGetSearchResults();
	});

	/**
	 * Make the 'enter' key do the same as the search button
	 */
	jQuery('#slideshow-slide-inserter-popup #search').keypress(function(event){
		if(event.which == 13){
			event.preventDefault();
			slideshowSlideInserterGetSearchResults();
		}
	});

	/**
	 * Open popup by click on button
	 */
	jQuery('#slideshow-insert-image-slide').click(function(){
		jQuery('#slideshow-slide-inserter-popup, #slideshow-slide-inserter-popup-background').css({ display: 'block' });
	});

	/**
	 * Insert text slide into the sortable list when the Insert Text Slide button is clicked
	 */
	jQuery('#slideshow-insert-text-slide').click(function(){
		slideshowSlideInserterInsertTextSlide();
	});

	/**
	 * Insert video slide into the sortable list when the Insert Video Slide button is clicked
	 */
	jQuery('#slideshow-insert-video-slide').click(function(){
		slideshowSlideInserterInsertVideoSlide();
	});

	/**
	 * Call slideshowDeleteSlide on click
	 */
	jQuery('.slideshow-delete-slide').click(function(){
		slideshowDeleteSlide(jQuery(this).closest('li'));
	});

	/**
	 * Deletes slide from DOM
	 *
	 * @param slide
	 */
	function slideshowDeleteSlide(slide){

		// Deletion message
		var confirmMessage = 'Are you sure you want to delete this slide?';
		if(typeof SlideInserterTranslations !== undefined)
			confirmMessage = SlideInserterTranslations.confirmMessage;

		// Confirm deletion
		var deleteSlide = confirm(confirmMessage);
		if(!deleteSlide)
			return;

		// Remove slide from DOM
		slide.remove();
	}

	/**
	 * Loop through list items, setting slide orders
	 */
	function slideshowSlideInserterIndexSlidesOrder(){
		// Loop through sortables
		jQuery.each(jQuery('.sortable-slides-list').find('li'), function(key, value){

			// Loop through all input, select and text area boxes
			jQuery.each(jQuery(this).find('input, select, textarea'), function(key2, input){

				// Remove brackets
				var name = jQuery(input).attr('name');

				// No name found, skip
				if(name == undefined)
					return;

				// Divide name parts
				name = name.replace(/[\[\]']+/g, ' ').split(' ');

				// Put name with new order ID back on the page
				jQuery(input).attr('name', name[0] + '[' + (key + 1) + '][' + name[2] + ']');
			});
		});
	}

	/**
	 * Sends an ajax post request with the search query and print
	 * retrieved html to the results table.
	 *
	 * If offset is set, append data to data that is already there
	 *
	 * @param offset (optional, defaults to 0)
	 */
	function slideshowSlideInserterGetSearchResults(offset){
		var popup = jQuery('#slideshow-slide-inserter-popup');
		var resultsTable = popup.find('#results');

		if(!offset){
			offset = 0;
			resultsTable.html('');
		}

		var attachmentIDs = [];
		jQuery.each(resultsTable.find('.result-table-row'), function(key, tr){
			attachmentIDs.push(jQuery(tr).data('attachmentId'));
		});

		jQuery.post(
			ajaxurl,
			{
				action: 'slideshow_slide_inserter_search_query',
				search: popup.find('#search').attr('value'),
				offset: offset,
				attachmentIDs: attachmentIDs
			},
			function(response){
				// Fill table
				resultsTable.append(response);

				// Apply insert to slideshow script
				resultsTable.find('.insert-attachment').unbind('click').click(function(){
					var tr = jQuery(this).closest('tr');
					slideshowSlideInserterInsertImageSlide(
						jQuery(tr).data('attachmentId'),
						jQuery(tr).find('.title').text(),
						jQuery(tr).find('.description').text(),
						jQuery(tr).find('.image img').attr('src')
					);
				});

				// Load more results on click of the 'Load more results' button
				var loadMoreResultsButton = jQuery('.load-more-results');
				if(loadMoreResultsButton){
					loadMoreResultsButton.click(function(){
						// Get offset
						var previousOffset = jQuery(this).attr('class').split(' ')[2];

						// Load ajax results
						slideshowSlideInserterGetSearchResults(previousOffset);

						// Remove button row
						jQuery(this).closest('tr').remove();
					});
				}
			}
		);
	}

	/**
	 * Inserts image slide into the slides list
	 *
	 * @param id
	 * @param title
	 * @param description
	 * @param src
	 */
	function slideshowSlideInserterInsertImageSlide(id, title, description, src){

		// Find and clone the image slide template
		var imageSlide = jQuery('.image-slide-template').find('li').clone();

		// Fill slide with data
		imageSlide.find('.attachment').attr('src', src);
		imageSlide.find('.attachment').attr('title', title);
		imageSlide.find('.attachment').attr('alt', title);
		imageSlide.find('.title').attr('value', title);
		imageSlide.find('.description').html(description);
		imageSlide.find('.postId').attr('value', id);

		// Set names to be saved to the database
		imageSlide.find('.title').attr('name', 'slides[0][title]');
		imageSlide.find('.description').attr('name', 'slides[0][description]');
		imageSlide.find('.url').attr('name', 'slides[0][url]');
		imageSlide.find('.urlTarget').attr('name', 'slides[0][urlTarget]');
		imageSlide.find('.type').attr('name', 'slides[0][type]');
		imageSlide.find('.postId').attr('name', 'slides[0][postId]');

		// Register delete link
		imageSlide.find('.slideshow-delete-slide').click(function(){
			slideshowDeleteSlide(jQuery(this).closest('li'));
		});

		// Put slide in the sortables list.
		jQuery('.sortable-slides-list').prepend(imageSlide);

		// Reindex
		slideshowSlideInserterIndexSlidesOrder();
	}

	/**
	 * Inserts text slide into the slides list
	 */
	function slideshowSlideInserterInsertTextSlide(){

		// Find and clone the text slide template
		var textSlide = jQuery('.text-slide-template').find('li').clone();

		// Set names to be saved to the database
		textSlide.find('.title').attr('name', 'slides[0][title]');
		textSlide.find('.description').attr('name', 'slides[0][description]');
		textSlide.find('.textColor').attr('name', 'slides[0][textColor]');
		textSlide.find('.color').attr('name', 'slides[0][color]');
		textSlide.find('.url').attr('name', 'slides[0][url]');
		textSlide.find('.urlTarget').attr('name', 'slides[0][urlTarget]');
		textSlide.find('.type').attr('name', 'slides[0][type]');

		// Register delete link
		textSlide.find('.slideshow-delete-slide').click(function(){
			slideshowDeleteSlide(jQuery(this).closest('li'));
		});

		// Put slide in the sortables list.
		jQuery('.sortable-slides-list').prepend(textSlide);

		// Reindex slide orders
		slideshowSlideInserterIndexSlidesOrder();
	}

	/**
	 * Inserts video slide into the slides list
	 */
	function slideshowSlideInserterInsertVideoSlide(){

		// Find and clone the video slide template
		var videoSlide = jQuery('.video-slide-template').find('li').clone();

		// Set names to be saved to the database
		videoSlide.find('.videoId').attr('name', 'slides[0][videoId]');
		videoSlide.find('.type').attr('name', 'slides[0][type]');

		// Register delete link
		videoSlide.find('.slideshow-delete-slide').click(function(){
			slideshowDeleteSlide(jQuery(this).closest('li'));
		});

		// Put slide in the sortables list.
		jQuery('.sortable-slides-list').prepend(videoSlide);

		// Reindex slide orders
		slideshowSlideInserterIndexSlidesOrder();
	}

	/**
	 * Closes popup
	 */
	function slideshowSlideInserterClosePopup(){
		jQuery('#slideshow-slide-inserter-popup, #slideshow-slide-inserter-popup-background').css({ display: 'none' });
	}
});