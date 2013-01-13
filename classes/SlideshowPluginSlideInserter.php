<?php
/**
 * Class SlideshowPluginSlideInserter
 *
 * TODO This class will probably need to be renamed to SlideshowPluginSlideHandler to explain more functionality
 * TODO than just inserting slides. (Show and delete functionality should be applied here as well)
 * @since 2.0.0
 * @author Stefan Boonstra
 * @version 03-10-2012
 */
class SlideshowPluginSlideInserter {

	/** Flag to see if enqueue function has been called */
	private static $enqueuedFiles;

	/**
	 * Returns the html for showing the image insert button.
	 * Enqueues scripts unless $enqueueFiles is set to false.
	 *
	 * @since 2.0.0
	 * @param boolean $enqueueFiles
	 * @return String $button
	 */
	static function getImageSlideInsertButton($enqueueFiles = true){
		if($enqueueFiles)
			self::enqueueFiles();

		// Put popup html in footer
		add_action('admin_footer', array(__CLASS__, 'includePopup'));

		// Return button html
		ob_start();
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/insert-image-button.php');
		return ob_get_clean();
	}

	/**
	 * Returns the html for showing the text insert button.
	 * Enqueues scripts unless $enqueueFiles is set to false.
	 *
	 * @since 2.0.0
	 * @param boolean $enqueueFiles
	 * @return String $button
	 */
	static function getTextSlideInsertButton($enqueueFiles = true){
		if($enqueueFiles)
			self::enqueueFiles();

		// Return button html
		ob_start();
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/insert-text-button.php');
		return ob_get_clean();
	}

	/**
	 * Returns the html for showing the video insert button.
	 * Enqueues scripts unless $enqueueFiles is set to false.
	 *
	 * @since 2.1.0
	 * @param boolean $enqueueFiles
	 * @return String $button
	 */
	static function getVideoSlideInsertButton($enqueueFiles = true){
		if($enqueueFiles)
			self::enqueueFiles();

		// Return button html
		ob_start();
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/insert-video-button.php');
		return ob_get_clean();
	}

	/**
	 * This function is registered in the SlideshowPluginAjax class
	 * and deletes slides with a particular $_POST['slideId'].
	 *
	 * TODO: This function has become obsolete and slides are deleted by simply unsetting them and saving the slideshow.
	 *
	 * @since 2.0.0
	 */
	static function deleteSlide(){
		if((!isset($_POST['slideId']) || !is_numeric($_POST['slideId'])) ||
			(!isset($_POST['postId']) || !is_numeric($_POST['postId'])))
			die;

		$search = 'slide_' . $_POST['slideId'] . '_';
		$settings = get_post_meta($_POST['postId'], 'settings', true);
		if(is_array($settings) && count($settings) > 0){
			foreach($settings as $key => $setting)
				if(strtolower(substr($key, 0, strlen($search))) == strtolower($search))
					unset($settings[$key]);
		}
		update_post_meta($_POST['postId'], 'settings', $settings);

		die;
	}

	/**
	 * This function is registered in the SlideshowPluginAjax class
	 * and prints the results from the search query.
	 *
	 * @since 2.0.0
	 */
	static function printSearchResults(){
		// Numberposts and offset
		$numberPosts = 10;
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = $_POST['offset'];

		// Get attachments with a title alike the search string, needs to be filtered
		add_filter('posts_where', array(__CLASS__, 'printSearchResultsWhereFilter'));
		$attachments = get_posts(array(
			'numberposts' => $numberPosts + 1,
			'offset' => $offset,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'attachment',
			'suppress_filters' => false
		));
		remove_filter('posts_where', array(__CLASS__, 'printSearchResultsWhereFilter'));

		// Check if there are enough attachments to print a 'Load more images' button
		$loadMoreResults = false;
		if(count($attachments) > $numberPosts){
			array_pop($attachments);
			$loadMoreResults = true;
		}

		// Print results to the screen
		if(count($attachments) > 0){
			foreach($attachments as $attachment){
				$image = wp_get_attachment_image_src($attachment->ID);
				if(!is_array($image) || !$image){
					if(!empty($attachment->guid))
						$imageSrc = $attachment->guid;
					else
						continue;
				}else{
					$imageSrc = $image[0];
				}
				if(!$imageSrc || empty($imageSrc)) $imageSrc = SlideshowPluginMain::getPluginUrl() . '/images/SlideshowPluginPostType/no-img.png';
				echo '<tr valign="top">
					<td class="image">
						<img width="60" height="60" src="' . $imageSrc . '" class="attachment" alt="' . $attachment->post_title . '" title="' . $attachment->post_title . '">
					</td>
					<td class="column-title">
						<strong class="title">' . $attachment->post_title . '</strong>
						<p class="description">' . $attachment->post_content . '</p>
					</td>
					<td class="insert-button">
						<input
							type="button"
							id="' . $attachment->ID . '"
							class="insert-attachment button-secondary"
							value="' . __('Insert', 'slideshow-plugin') . '"
						/>
					</td>
				</tr>';
			}
			if($loadMoreResults){
				echo '<tr>
					<td colspan="3" style="text-align: center;">
						<button class="button-secondary load-more-results ' . ($offset + $numberPosts) . '" >
							' . __('Load more results', 'slideshow-plugin') . '
						</button>
					</td>
				</tr>';
			}
		} else {
			echo '<tr>
				<td colspan="3" style="text-align: center;">
					<a href="' . admin_url() . 'media-new.php" target="_blank">
						' . __('No images were found, click here to upload some.', 'slideshow-plugin') . '
					</a>
				</td>
			</tr>';
		}

		die;
	}

	/**
	 * Applies a where clause on the get_posts call from self::printSearchResults()
	 *
	 * @since 2.0.0
	 * @param string $where
	 * @return string $where
	 */
	static function printSearchResultsWhereFilter($where){
		global $wpdb;

		if(isset($_POST['search']))
			$where .= $wpdb->prepare(
				" AND (post_title LIKE '%%%s%%' OR ID LIKE '%%%s%%') ",
				$_POST['search'],
				$_POST['search']
			);

		return $where;
	}

	/**
	 * Include popup, needs to be called in the footer
	 *
	 * @since 2.0.0
	 */
	static function includePopup(){
		include(SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/search-popup.php');
	}

	/**
	 * Enqueues styles and scripts necessary for the media upload button.
	 *
	 * @since 2.0.0
	 */
	static function enqueueFiles(){
        // Return when not on a slideshow edit page, or files have already been included.
        $currentScreen = get_current_screen();
        if($currentScreen->post_type != SlideshowPluginPostType::$postType || self::$enqueuedFiles)
            return;

		// Enqueue style
		wp_enqueue_style(
			'slideshow-slide-inserter',
			SlideshowPluginMain::getPluginUrl() . '/style/' . __CLASS__ . '/slide-inserter.css',
			null,
			SlideshowPluginMain::$version
		);

		// Enqueue insert button script
		wp_enqueue_script(
			'slideshow-slide-inserter',
			SlideshowPluginMain::getPluginUrl() . '/js/' . __CLASS__ . '/slide-inserter.js',
			array('jquery'),
			SlideshowPluginMain::$version
		);

		wp_localize_script(
			'slideshow-slide-inserter',
			'SlideInserterTranslations',
			array(
				'confirmMessage' => __('Are you sure you want to delete this slide?', 'slideshow-plugin')
			)
		);

		// Set enqueued to true
		self::$enqueuedFiles = true;
	}
}