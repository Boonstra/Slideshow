<p style="text-align: center;" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
	<i><?php _e('Insert', 'slideshow-plugin'); ?>:</i><br/>
	<?php echo SlideshowPluginSlideInserter::getImageSlideInsertButton(); ?>
	<?php echo SlideshowPluginSlideInserter::getTextSlideInsertButton(); ?>
	<?php echo SlideshowPluginSlideInserter::getVideoSlideInsertButton(); ?>
</p>

<?php if(count($slides) <= 0): ?>
	<p><?php _e('Add slides to this slideshow by using one of the buttons above.', 'slideshow-plugin'); ?></p>
<?php endif; ?>

<style type="text/css">
	.sortable li {
		cursor: pointer;
	}
</style>
<script type="text/javascript">
	var slideshowHighestSlideId = <?php echo (is_numeric($highestSlideId))? $highestSlideId : 0; ?>
</script>

<ul class="sortable-slides-list">
	<?php if(count($slides) > 0): ?>
	<?php foreach($slides as $key => $slide):
		// General values
		$url = $target = $order = '';
		if(isset($slide['url']))
			$url = $slide['url'];
		if(isset($slide['urlTarget']))
			$target = $slide['urlTarget'];
		if(isset($slide['order']))
			$order = $slide['order'];

		// The name is used to prefix a setting name with
		$name = SlideshowPluginSettingsHandler::$slidesKey . '[' . $key . ']';
		?>

		<li class="widefat sortable-slides-list-item">
			<?php if($slide['type'] == 'text'):

				// Type specific values
				$title = $description = $color = '';
				if(isset($slide['title']))
					$title = $slide['title'];
				if(isset($slide['description']))
					$description = $slide['description'];
				if(isset($slide['color']))
					$color = $slide['color'];
				?>

				<p style="padding: 0 15px 0 5px;">
					<input type="text" name="<?php echo $name; ?>[title]" value="<?php echo $title; ?>" /><i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
					<i><?php _e('Description', 'slideshow-plugin'); ?></i><textarea name="<?php echo $name; ?>[description]" rows="7" cols="" style="width: 100%;"><?php echo $description; ?></textarea><br />
					<input type="text" name="<?php echo $name; ?>[color]" value="<?php echo $color; ?>" class="color {required:false}" /><i><?php _e('Background color', 'slideshow-plugin'); ?></i><br />
				</p>

				<p style="float: left; padding: 0 5px;">
					<input type="text" name="<?php echo $name; ?>[url]" value="<?php echo $url; ?>" /><br />
					<select name="<?php echo $name; ?>[urlTarget]">
						<option value="_self" <?php selected('_self', $target); ?>><?php _e('Same window', 'slideshow-plugin'); ?></option>
						<option value="_blank" <?php selected('_blank', $target); ?>><?php _e('New window', 'slideshow-plugin'); ?></option>
					</select>
				</p>
				<p style="float: left; line-height: 50px;">
					<i><?php _e('URL', 'slideshow-plugin'); ?></i>
				</p>
				<p style="clear: both;"></p>

				<input type="hidden" name="<?php echo $name; ?>[type]" value="text" />
				<input type="hidden" name="<?php echo $name; ?>[order]" value="<?php echo $order; ?>" class="slide_order" />

			<?php elseif($slide['type'] == 'video'):

				// Type specific values
				$videoId = '';
				if(isset($slide['videoId']))
					$videoId = $slide['videoId'];
				?>

				<p style="padding: 0 5px;">
					<input type="text" name="<?php echo $name; ?>[videoId]" value="<?php echo $videoId; ?>" /><i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i>
				</p>

				<input type="hidden" name="<?php echo $name; ?>[type]" value="video" />
				<input type="hidden" name="<?php echo $name; ?>[order]" value="<?php echo $order; ?>" class="slide_order" />

			<?php elseif($slide['type'] == 'attachment'):

				// The attachment should always be there
				$attachment = get_post($slide['postId']);
				if(!isset($attachment))
					continue;

				$image = wp_get_attachment_image_src($attachment->ID);
				$imageSrc = '';
				if(!is_array($image) || !$image){
					if(!empty($attachment->guid))
						$imageSrc = $attachment->guid;
					else
						continue;
				}else{
					$imageSrc = $image[0];
				}
				if(!$imageSrc || empty($imageSrc)) $imageSrc = $noPreviewIcon;

				$editUrl = admin_url() . '/media.php?attachment_id=' . $attachment->ID . '&amp;action=edit'; ?>

				<p style="float: left; padding: 0 5px;">
					<a href="<?php echo $editUrl; ?>" title="<?php _e('Edit', 'slideshow-plugin'); ?> &#34;<?php echo $attachment->post_title; ?>&#34;">
						<img width="80" height="60" src="<?php echo $imageSrc; ?>" class="attachment-80x60" alt="<?php echo $attachment->post_title; ?>" title="<?php echo $attachment->post_title; ?>" />
					</a>
				</p>

				<p style="float: left; padding: 0 5px;">
					<strong>
						<a href="<?php echo $editUrl; ?>" title="<?php _e('Edit', 'slideshow-plugin'); ?> &#34;<?php echo $attachment->post_title; ?>&#34;"><?php echo $attachment->post_title; ?></a>
					</strong><br />
					<?php if(strlen($attachment->post_content) > 30) echo substr($attachment->post_content, 0, 20) . '...'; else echo $attachment->post_content; ?>
				</p>
				<p style="clear: both"></p>

				<p style="float: left; padding: 0 5px;">
					<input type="text" name="<?php echo $name; ?>[url]" value="<?php echo $url; ?>" /><br />
					<select name="<?php echo $name; ?>[urlTarget]">
						<option value="_self" <?php selected('_self', $target); ?>><?php _e('Same window', 'slideshow-plugin'); ?></option>
						<option value="_blank" <?php selected('_blank', $target); ?>><?php _e('New window', 'slideshow-plugin'); ?></option>
					</select>
				</p>
				<p style="float: left; line-height: 50px;">
					<i><?php _e('URL', 'slideshow-plugin'); ?></i>
				</p>
				<p style="clear: both;"></p>

				<input type="hidden" name="<?php echo $name; ?>[type]" value="attachment" />
				<input type="hidden" name="<?php echo $name; ?>[postId]" value="<?php echo $attachment->ID; ?>" />
				<input type="hidden" name="<?php echo $name; ?>[order]" value="<?php echo $order; ?>" class="slide_order" />

			<?php else: ?>

				<p style="padding: 0 5px;">
					<?php _e('An error occurred while loading this slide, and it will not be present in the slideshow', 'slideshow-plugin'); ?>
				</p>

			<?php endif; ?>
			<p style="padding: 0 5px; color: red; cursor: pointer;" class="slideshow-delete-slide">
				<?php _e('Delete slide', 'slideshow-plugin'); ?>
				<span style="display: none;" class="<?php echo $id; ?>"></span>
			</p>
		</li>
	<?php endforeach; ?>
	<?php endif; ?>
</ul>

<div class="text-slide-template" style="display: none;">
	<li class="widefat sortable-slides-list-item">
		<p style="padding: 0 15px 0 5px;">
			<input type="text" class="title" /><i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
			<i><?php _e('Description', 'slideshow-plugin'); ?></i><textarea class="description" cols="" rows="7" style="width: 100%;"></textarea><br />
			<input type="text" class="color {required:false}" /><i><?php _e('Background color', 'slideshow-plugin'); ?></i><br />
		</p>

		<p style="float: left; padding: 0 5px;">
			<input type="text" class="url" value="" /><br />
			<select class="urlTarget">
				<option value="_self"><?php _e('Same window', 'slideshow-plugin'); ?></option>
				<option value="_blank"><?php _e('New window', 'slideshow-plugin'); ?></option>
			</select>
		</p>
		<p style="float: left; line-height: 50px;">
			<i><?php _e('URL', 'slideshow-plugin'); ?></i>
		</p>
		<p style="clear: both"></p>

		<input type="hidden" class="type" value="text" />
		<input type="hidden" class="slide_order" />

		<p style="padding: 0 5px; color: red; cursor: pointer;" class="slideshow-delete-new-slide">
			<?php _e('Delete slide', 'slideshow-plugin'); ?>
		</p>
	</li>
</div>

<div class="video-slide-template" style="display: none;">
	<li class="widefat sortable-slides-list-item">
		<p style="padding: 0 5px;">
			<input type="text" class="videoId" /><i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i>
		</p>

		<input type="hidden" class="type" value="video" />
		<input type="hidden" class="slide_order" />

		<p style="padding: 0 5px; color: red; cursor: pointer;" class="slideshow-delete-new-slide">
			<?php _e('Delete slide', 'slideshow-plugin'); ?>
		</p>
	</li>
</div>

<div class="image-slide-template" style="display: none;">
	<li class="widefat sortable-slides-list-item">
		<p style="float: left; padding: 0 5px;">
			<img width="80" height="60" src="" class="attachment attachment-80x60" alt="" title="" />
		</p>

		<p style="float: left; padding: 0 5px;">
			<strong class="title"></strong><br />
			<span class="description"></span>
		</p>
		<p style="clear: both"></p>

		<p style="float: left; padding: 0 5px;">
			<input type="text" class="url" value="" /><br />
			<select class="urlTarget">
				<option value="_self"><?php _e('Same window', 'slideshow-plugin'); ?></option>
				<option value="_blank"><?php _e('New window', 'slideshow-plugin'); ?></option>
			</select>
		</p>
		<p style="float: left; line-height: 50px;"
			<i><?php _e('URL', 'slideshow-plugin'); ?></i>
		</p>
		<p style="clear: both"></p>

		<input type="hidden" class="type" value="attachment" />
		<input type="hidden" class="postId" value="" />
		<input type="hidden" value="" class="slide_order" />

		<p style="padding: 0 5px; color: red; cursor: pointer;" class="slideshow-delete-new-slide">
			<?php _e('Delete slide', 'slideshow-plugin'); ?>
		</p>
	</li>
</div>