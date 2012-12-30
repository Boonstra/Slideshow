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
	 * Ajax deletes a slide from the slides list and from the database
	 */
	jQuery('.slideshow-delete-slide').click(function(){
		var confirmMessage = 'Are you sure you want to delete this slide?';
		if(typeof SlideInserterTranslations !== undefined)
			confirmMessage = SlideInserterTranslations.confirmMessage;

		var deleteSlide = confirm(confirmMessage);
		if(!deleteSlide)
			return;

		// Get postId from url
		var postId = -1;
		jQuery.each(location.search.replace('?', '').split('&'), function(key, value){
			var splitValue = value.split('=');
			if(splitValue[0] == 'post')
				postId = splitValue[1];
		});

		// Get slideId
		var slideId = jQuery(this).find('span').attr('class');

		// Exit if no slideId is found
		if(postId == -1 || slideId == 'undefined')
			return;

		// Remove slide from DOM
		jQuery(this).parent().remove();

		// Remove slide by AJAX.
		jQuery.post(
			ajaxurl,
			{
				action: 'slideshow_delete_slide',
				postId: postId,
				slideId: slideId
			}
		);
	});

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
		if(!offset){
			offset = 0;
			jQuery('#slideshow-slide-inserter-popup #results').html('');
		}

		jQuery.post(
			ajaxurl,
			{
				action: 'slideshow_slide_inserter_search_query',
				search: jQuery('#slideshow-slide-inserter-popup #search').attr('value'),
				offset: offset
			},
			function(response){
				// Fill table
				jQuery('#slideshow-slide-inserter-popup #results').append(response);

				// Apply insert to slideshow script
				jQuery('#slideshow-slide-inserter-popup #results .insert-attachment').click(function(){
					var tr = jQuery(this).closest('tr');
					slideshowSlideInserterInsertImageSlide(
						jQuery(this).attr('id'),
						jQuery(tr).find('.title').text(),
						jQuery(tr).find('.description').text(),
						jQuery(tr).find('.image img').attr('src')
					);
				});

				// Load more results on click of the 'Load more results' button
				if(jQuery('.load-more-results')){
					jQuery('.load-more-results').click(function(){
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
		imageSlide.find('.slide_order').attr('name', 'slides[0][order]');

		// Register delete link (only needs to delete from DOM)
		imageSlide.find('.slideshow-delete-new-slide').click(function(){
			var deleteSlide = confirm('Are you sure you want to delete this slide?');
			if(!deleteSlide)
				return;

			jQuery(this).closest('li').remove();
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
		textSlide.find('.color').attr('name', 'slides[0][color]');
		textSlide.find('.url').attr('name', 'slides[0][url]');
		textSlide.find('.urlTarget').attr('name', 'slides[0][urlTarget]');
		textSlide.find('.type').attr('name', 'slides[0][type]');
		textSlide.find('.slide_order').attr('name', 'slides[0][order]');

		// Register delete link (only needs to delete from DOM)
		textSlide.find('.slideshow-delete-new-slide').click(function(){
			var deleteSlide = confirm('Are you sure you want to delete this slide?');
			if(!deleteSlide)
				return;

			jQuery(this).closest('li').remove();
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
		videoSlide.find('.slide_order').attr('name', 'slides[0][order]');

		// Register delete link (only needs to delete from DOM)
		videoSlide.find('.slideshow-delete-new-slide').click(function(){
			var deleteSlide = confirm('Are you sure you want to delete this slide?');
			if(!deleteSlide)
				return;

			jQuery(this).closest('li').remove();
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