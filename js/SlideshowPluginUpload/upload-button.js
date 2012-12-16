jQuery(document).ready(function(){
	jQuery('#upload_image_button').click(function() {
		formfield = jQuery('#upload_image').attr('name');
		post_id = jQuery('#post_ID').val();
		tb_show('', 'media-upload.php?post_id='+post_id+'&amp;type=image&amp;TB_iframe=true');
		return false;
	});
});