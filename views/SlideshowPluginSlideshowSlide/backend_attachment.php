<?php

if ($data instanceof stdClass) :

	$properties = $data->properties;

	// The attachment should always be there
	$attachment = get_post($properties['postId']);

	if (isset($attachment)):

		$name = htmlspecialchars($data->name);

		$title = $titleElementTagID = $description = $descriptionElementTagID = $url = $target = $alternativeText = '';

	    $noFollow = false;

	    if (isset($properties['title']))
		{
			$title = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']);
		}

		if (isset($properties['titleElementTagID']))
		{
			$titleElementTagID = $properties['titleElementTagID'];
		}

		if (isset($properties['description']))
		{
			$description = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']);
		}

		if (isset($properties['descriptionElementTagID']))
		{
			$descriptionElementTagID = $properties['descriptionElementTagID'];
		}

		if (isset($properties['url']))
		{
			$url = $properties['url'];
		}

		if (isset($properties['urlTarget']))
		{
			$target = $properties['urlTarget'];
		}

	    if (isset($properties['noFollow']))
	    {
	        $noFollow = true;
	    }

		if (isset($properties['alternativeText']))
		{
			$alternativeText = htmlspecialchars($properties['alternativeText']);
		}
		else
		{
			$alternativeText = $title;
		}

		// Prepare image
		$image        = wp_get_attachment_image_src($attachment->ID);
		$imageSrc     = '';
		$displaySlide = true;

		if (!is_array($image) ||
			!$image)
		{
			if (!empty($attachment->guid))
			{
				$imageSrc = $attachment->guid;
			}
			else
			{
				$displaySlide = false;
			}
		}
		else
		{
			$imageSrc = $image[0];
		}

		if (!$imageSrc ||
			empty($imageSrc))
		{
			$imageSrc = SlideshowPluginMain::getPluginUrl() . '/images/' . __CLASS__ . '/no-img.png';
		}

		$editUrl = admin_url() . '/media.php?attachment_id=' . $attachment->ID . '&amp;action=edit';

		if ($displaySlide): ?>

			<div id="" class="widefat sortable-slides-list-item postbox">

				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>

				<div class="hndle">
					<div class="slide-icon image-slide-icon"></div>
					<div class="slide-title">
						<?php if (strlen($title) > 0) : ?>

							<?php echo $title; ?>

						<?php else : ?>

							<?php _e('Image slide', 'slideshow-jquery-image-gallery'); ?>

						<?php endif; ?>
					</div>
					<div class="clear"></div>
				</div>

				<div class="inside">

					<div class="slideshow-group">

						<a href="<?php echo $editUrl; ?>" title="<?php _e('Edit', 'slideshow-jquery-image-gallery'); ?> &#34;<?php echo $attachment->post_title; ?>&#34;">
							<img width="80" height="60" src="<?php echo $imageSrc; ?>" class="attachment-80x60" alt="<?php echo $attachment->post_title; ?>" title="<?php echo $attachment->post_title; ?>" />
						</a>

					</div>

					<div class="slideshow-group">

						<div class="slideshow-left slideshow-label"><?php _e('Title', 'slideshow-jquery-image-gallery'); ?></div>
						<div class="slideshow-right">
							<select name="<?php echo $name; ?>[titleElementTagID]">
								<?php foreach (SlideshowPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
									<option value="<?php echo $elementTagID; ?>" <?php selected($titleElementTagID, $elementTagID); ?>><?php echo $elementTag; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="clear"></div>
						<input type="text" name="<?php echo $name; ?>[title]" value="<?php echo $title; ?>" style="width: 100%;" />

					</div>

					<div class="slideshow-group">

						<div class="slideshow-left slideshow-label"><?php _e('Description', 'slideshow-jquery-image-gallery'); ?></div>
						<div class="slideshow-right">
							<select name="<?php echo $name; ?>[descriptionElementTagID]">
								<?php foreach (SlideshowPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
									<option value="<?php echo $elementTagID; ?>" <?php selected($descriptionElementTagID, $elementTagID); ?>><?php echo $elementTag; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div clear="clear"></div>
						<textarea name="<?php echo $name; ?>[description]" rows="3" cols="" style="width: 100%;"><?php echo $description; ?></textarea>

					</div>

					<div class="slideshow-group">

						<div class="slideshow-label"><?php _e('URL', 'slideshow-jquery-image-gallery'); ?></div>
						<input type="text" name="<?php echo $name; ?>[url]" value="<?php echo $url; ?>" style="width: 100%;" />

						<div class="slideshow-label slideshow-left"><?php _e('Open URL in', 'slideshow-jquery-image-gallery'); ?></div>
						<select name="<?php echo $name; ?>[urlTarget]" class="slideshow-right">
							<option value="_self" <?php selected('_self', $target); ?>><?php _e('Same window', 'slideshow-jquery-image-gallery'); ?></option>
							<option value="_blank" <?php selected('_blank', $target); ?>><?php _e('New window', 'slideshow-jquery-image-gallery'); ?></option>
						</select>
						<div class="clear"></div>

						<div class="slideshow-label slideshow-left"><?php _e('Don\'t let search engines follow link', 'slideshow-jquery-image-gallery'); ?></div>
		                <input type="checkbox" name="<?php echo $name; ?>[noFollow]" value="" <?php checked($noFollow); ?> class="slideshow-right" />
						<div class="clear"></div>

		            </div>

					<div class="slideshow-group">

						<div class="slideshow-label"><?php _e('Alternative text', 'slideshow-jquery-image-gallery'); ?></div>
						<input type="text" name="<?php echo $name; ?>[alternativeText]" value="<?php echo $alternativeText; ?>" style="width: 100%;" />

					</div>

					<div class="slideshow-group slideshow-delete-slide">
						<span><?php _e('Delete slide', 'slideshow-jquery-image-gallery'); ?></span>
					</div>

					<input type="hidden" name="<?php echo $name; ?>[type]" value="attachment" />
					<input type="hidden" name="<?php echo $name; ?>[postId]" value="<?php echo $attachment->ID; ?>" />

				</div>

			</div>

		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
