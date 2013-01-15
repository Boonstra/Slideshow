<div class="text-slide-template" style="display: none;">
	<li class="widefat sortable-slides-list-item" style="margin: 10px 0; width: auto; background-color: #fafafa;">

		<h3 class="hndle">
			<span style="font-size: 0.8em;">
				<?php _e('Text slide', 'slideshow-plugin'); ?>
			</span>
		</h3>

		<p style="margin: 5px 15px 5px 5px;">

			<i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="title" /><br />

			<i><?php _e('Description', 'slideshow-plugin'); ?></i><br />
			<textarea class="description" cols="" rows="7" style="width: 100%;"></textarea><br />

			<i><?php _e('Text color', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="textColor {required:false}" value="000000" /><br />

			<i><?php _e('Background color', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="color {required:false}" value="FFFFFF" />

		</p>

		<p style="margin: 5px 15px 5px 5px;">
			<i><?php _e('URL', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="url" value="" /><br />
			<i><?php _e('Open URL in', 'slideshow-plugin'); ?></i>
			<select class="urlTarget">
				<option value="_self"><?php _e('Same window', 'slideshow-plugin'); ?></option>
				<option value="_blank"><?php _e('New window', 'slideshow-plugin'); ?></option>
			</select>
		</p>

		<input type="hidden" class="type" value="text" />
		<input type="hidden" class="slide_order" />

		<p style="margin: 5px 15px 5px 5px; color: red; cursor: pointer;" class="slideshow-delete-new-slide">
			<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
			<span style="display: none;" class="<?php echo $id; ?>"></span>
		</p>

	</li>
</div>

<div class="video-slide-template" style="display: none;">
	<li class="widefat sortable-slides-list-item" style="margin: 10px 0; width: auto; background-color: #fafafa;">

		<h3 class="hndle">
			<span style="font-size: 0.8em;">
				<?php _e('Video slide', 'slideshow-plugin'); ?>
			</span>
		</h3>

		<p style="margin: 5px 15px 5px 5px;">
			<i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="videoId" />
		</p>

		<input type="hidden" class="type" value="video" />
		<input type="hidden" class="slide_order" />

		<p style="margin: 5px 15px 5px 5px; color: red; cursor: pointer;" class="slideshow-delete-new-slide">
			<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
			<span style="display: none;" class="<?php echo $id; ?>"></span>
		</p>

	</li>
</div>

<div class="image-slide-template" style="display: none;">
	<li class="widefat sortable-slides-list-item" style="margin: 10px 0; width: auto; background-color: #fafafa;">

		<h3 class="hndle">
			<span style="font-size: 0.8em;">
				<?php _e('Image slide', 'slideshow-plugin'); ?>
			</span>
		</h3>

		<p style="float: left; margin: 5px;">
			<img width="80" height="60" src="" class="attachment attachment-80x60" alt="" title="" />
		</p>

		<p style="float: left; margin: 5px 15px 5px 5px;">
			<i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="title" />
		</p>
		<p style="clear: both"></p>

		<p style="margin: 5px 15px 5px 5px;">
			<i><?php _e('Description', 'slideshow-plugin'); ?></i><br />
			<textarea class="description" rows="3" cols="" style="width: 100%;"></textarea><br />
		</p>

		<p style="margin: 5px 15px 5px 5px;">
			<i><?php _e('URL', 'slideshow-plugin'); ?></i><br />
			<input type="text" class="url" value="" /><br />
			<i><?php _e('Open URL in', 'slideshow-plugin'); ?></i>
			<select class="urlTarget">
				<option value="_self"><?php _e('Same window', 'slideshow-plugin'); ?></option>
				<option value="_blank"><?php _e('New window', 'slideshow-plugin'); ?></option>
			</select>
		</p>

		<input type="hidden" class="type" value="attachment" />
		<input type="hidden" class="postId" value="" />
		<input type="hidden" value="" class="slide_order" />

		<p style="margin: 5px 15px 5px 5px; color: red; cursor: pointer;" class="slideshow-delete-new-slide">
			<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
			<span style="display: none;" class="<?php echo $id; ?>"></span>
		</p>

	</li>
</div>