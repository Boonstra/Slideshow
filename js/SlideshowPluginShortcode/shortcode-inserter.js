jQuery(document).ready(function(){

	jQuery('.insertSlideshowShortcodeSlideshowInsertButton').click(function(){
		var undefinedSlideshowMessage = SlideshowShortcodeInserter.undefinedSlideshowMessage;
		if(undefinedSlideshowMessage == undefined)
			undefinedSlideshowMessage = 'No slideshow selected.';

		var shortcode = SlideshowShortcodeInserter.shortcode;
		if(shortcode == undefined)
			shortcode = 'slideshow_deploy';

		var slideshowId = jQuery('#insertSlideshowShortcodeSlideshowSelect').val();

		if(slideshowId == undefined){
			alert(undefinedSlideshowMessage);
			return;
		}

		send_to_editor('[' + shortcode + ' id=\'' + slideshowId + '\']');
		tb_remove();
		return true;
	});

	jQuery('.insertSlideshowShortcodeCancelButton').click(function(){
		tb_remove();
		return false;
	});
});