<?php if ($data instanceof stdClass) : ?>

	<p>
		<label for="<?php echo $data->widget->get_field_id('title'); ?>"><?php _e('Title', 'slideshow-jquery-image-gallery'); ?></label>
		<input class="widefat" id="<?php echo $data->widget->get_field_id('title'); ?>" name="<?php echo $data->widget->get_field_name('title'); ?>" value="<?php echo htmlspecialchars($data->instance['title']); ?>" style="width:100%" />
	</p>

	<p>
		<label for="<?php echo $data->widget->get_field_id('slideshowId'); ?>"><?php _e('Slideshow', 'slideshow-jquery-image-gallery'); ?></label>
		<select class="widefat" id="<?php echo $data->widget->get_field_id('slideshowId'); ?>" name="<?php echo $data->widget->get_field_name('slideshowId'); ?>" value="<?php echo (is_numeric($data->instance['slideshowId']))? $data->instance['slideshowId'] : ''; ?>" style="width:100%">
			<option value="-1" <?php selected($data->instance['slideshowId'], -1); ?>><?php _e('Random Slideshow', 'slideshow-jquery-image-gallery'); ?></option>
			<?php if(count($data->slideshows) > 0): ?>
			<?php foreach($data->slideshows as $slideshow): ?>
				<option value="<?php echo $slideshow->ID ?>" <?php selected($data->instance['slideshowId'], $slideshow->ID); ?>><?php echo !empty($slideshow->post_title) ? $slideshow->post_title : __('Untitled slideshow', 'slideshow-jquery-image-gallery'); ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</p>

<?php endif; ?>