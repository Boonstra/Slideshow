<div class="text-slide-template" style="display: none;">
	<div class="widefat sortable-slides-list-item postbox">

		<div class="handlediv" title="<?php _e('Click to toggle', 'slideshow-plugin'); ?>"><br></div>

		<div class="hndle">
			<div class="slide-icon text-slide-icon"></div>
			<div class="slide-title">
				<?php _e('Text slide', 'slideshow-plugin'); ?>
			</div>
			<div style="clear: both;"></div>
		</div>

		<div class="inside">

			<p>

				<i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="title" style="width: 100%;" /><br />

				<i><?php _e('Description', 'slideshow-plugin'); ?></i><br />
				<textarea class="description" cols="" rows="7" style="width: 100%;"></textarea><br />

				<i><?php _e('Text color', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="textColor" value="000000" /><br />

				<i><?php _e('Background color', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="color" value="FFFFFF" />

			</p>

			<p>

				<i><?php _e('URL', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="url" value="" style="width: 100%;" /><br />

				<i><?php _e('Open URL in', 'slideshow-plugin'); ?></i>
				<select class="urlTarget">
					<option value="_self"><?php _e('Same window', 'slideshow-plugin'); ?></option>
					<option value="_blank"><?php _e('New window', 'slideshow-plugin'); ?></option>
				</select><br />

	            <input type="checkbox" class="noFollow" />
	            <i><?php _e('Don\'t let search engines follow link', 'slideshow-plugin'); ?></i><br />

	        </p>

			<input type="hidden" class="type" value="text" />

			<p class="slideshow-delete-slide">
				<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
			</p>

		</div>

	</div>
</div>

<div class="video-slide-template" style="display: none;">
	<div class="widefat sortable-slides-list-item postbox">

		<div class="handlediv" title="<?php _e('Click to toggle', 'slideshow-plugin'); ?>"><br></div>

		<div class="hndle">
			<div class="slide-icon video-slide-icon"></div>
			<div class="slide-title">
				<?php _e('Video slide', 'slideshow-plugin'); ?>
			</div>
			<div style="clear: both;"></div>
		</div>

		<div class="inside">

			<p>

				<i><?php _e('Youtube Video ID', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="videoId" style="width: 100%;" />

			</p>

			<p>

				<i><?php _e('Show related videos', 'slideshow-plugin'); ?></i><br />
				<label><input type="radio" class="showRelatedVideos" value="true"><?php _e('Yes', 'slideshow-plugin'); ?></label>
				<label><input type="radio" class="showRelatedVideos" value="false" checked="checked""><?php _e('No', 'slideshow-plugin'); ?></label>

			</p>

			<input type="hidden" class="type" value="video" />

			<p class="slideshow-delete-slide">
				<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
			</p>

		</div>

	</div>
</div>

<div class="image-slide-template" style="display: none;">
	<div class="widefat sortable-slides-list-item postbox">

		<div class="handlediv" title="<?php _e('Click to toggle', 'slideshow-plugin'); ?>"><br></div>

		<div class="hndle">
			<div class="slide-icon image-slide-icon"></div>
			<div class="slide-title">
				<?php _e('Image slide', 'slideshow-plugin'); ?>
			</div>
			<div style="clear: both;"></div>
		</div>

		<div class="inside">

			<p>

				<img width="80" height="60" src="" class="attachment attachment-80x60" alt="" title="" style="float: none; margin: 0; padding: 0;" />

			</p>

			<p>

				<i><?php _e('Title', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="title" style="width: 100%;" />

			</p>

			<p>

				<i><?php _e('Description', 'slideshow-plugin'); ?></i><br />
				<textarea class="description" rows="3" cols="" style="width: 100%;"></textarea><br />

			</p>

			<p>

				<i><?php _e('URL', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="url" value="" style="width: 100%;" /><br />

				<i><?php _e('Open URL in', 'slideshow-plugin'); ?></i>
				<select class="urlTarget">
					<option value="_self"><?php _e('Same window', 'slideshow-plugin'); ?></option>
					<option value="_blank"><?php _e('New window', 'slideshow-plugin'); ?></option>
				</select><br />

	            <input type="checkbox" class="noFollow" />
	            <i><?php _e('Don\'t let search engines follow link', 'slideshow-plugin'); ?></i><br />

	        </p>

			<p>

				<i><?php _e('Alternative text', 'slideshow-plugin'); ?></i><br />
				<input type="text" class="alternativeText" style="width: 100%;" />

			</p>

			<input type="hidden" class="type" value="attachment" />
			<input type="hidden" class="postId" value="" />

			<p class="slideshow-delete-slide">
				<span><?php _e('Delete slide', 'slideshow-plugin'); ?></span>
			</p>

		</div>

	</div>
</div>