<?php if ($data instanceof stdClass) : ?>

	<p><?php _e('To use this slideshow in your website either add this piece of shortcode to your posts or pages', 'slideshow-jquery-image-gallery'); ?>:</p>
	<p style="font-style: italic;"><?php echo $data->shortCode; ?></p>

	<?php if(current_user_can('edit_themes')): ?>
	<p><?php _e('Or add this piece of code to where ever in your website you want to place the slideshow', 'slideshow-jquery-image-gallery'); ?>:</p>
	<p style="font-style: italic;"><?php echo $data->snippet; ?></p>
	<?php endif; ?>

	<p><?php echo sprintf(__('Or go to the %swidgets page%s and show the slideshow as a widget.', 'slideshow-jquery-image-gallery'), '<a href="' . get_admin_url(null, 'widgets.php') . '" target="_blank">', '</a>'); ?></p>

<?php endif; ?>