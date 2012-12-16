<?php
/**
 * SlideshowPluginVersionConverter helps users transfer from version
 * to version without losing any data.
 *
 * @author Stefan Boonstra
 * @version 06-12-12
 */
class SlideshowPluginUpdater {

	/** Version option key */
	private static $versionKey = 'slideshow-jquery-image-gallery-plugin-version';

	/**
	 * The init function checks
	 */
	static function init(){
		if(!is_admin())
			return;

		// Transfer if no version number is set, or the current version number is greater than the on saved in the database
		$oldVersion = get_option(self::$versionKey, null);
		if($oldVersion == null || SlideshowPluginMain::$version > $oldVersion)
			self::update($oldVersion);
	}

	/**
	 * Updates user to correct version
	 *
	 * @since 2.1.20
	 * @param string $oldVersion
	 */
	private static function update($oldVersion){
		// Version numbers are registered after version 2.1.20
		if($oldVersion == null){
			self::updateV1toV2();
			self::updateV2toV2_1_20();
		}

		// This gives better performance to the update, since lower version updates can be skipped.
//		if('1.33.7' > $oldVersion || $oldVersion == null)
//			update();

		// Set new version
		update_option(self::$versionKey, SlideshowPluginMain::$version);
	}

	/**
	 * Updates v2 to the 2.1.20 settings storage system,
	 * which uses three post-meta values instead of one.
	 *
	 * @since 2.1.20
	 */
	private static function updateV2toV2_1_20(){
		// Check if this has already been done
		if(get_option('slideshow-plugin-updated-from-v2-to-v2-1-20') !== false)
			return;

		// Get slideshows
		$slideshows = get_posts(array(
			'numberposts' => -1,
			'offset' => 0,
			'post_type' => 'slideshow'
		));

		// Loop through slideshow
		if(is_array($slideshows) && count($slideshows > 0)){
			foreach($slideshows as $slideshow){
				// Get settings
				$settings = get_post_meta(
					$slideshow->ID,
					'settings',
					true
				);
				if(!is_array($settings))
					$settings = array();

				// Old prefixes
				$settingsPrefix = 'setting_';
				$stylePrefix = 'style_';
				$slidePrefix = 'slide_';

				// Meta keys
				$settingsKey = 'settings';
				$styleSettingsKey = 'styleSettings';
				$slidesKey = 'slides';

				// Extract key => value into new arrays
				$newSettings = array();
				$styleSettings = array();
				$slides = array();
				foreach($settings as $key => $value){
					if($settingsPrefix == substr($key, 0, strlen($settingsPrefix)))
						$newSettings[substr($key, strlen($settingsPrefix))] = $value;
					elseif($stylePrefix == substr($key, 0, strlen($stylePrefix)))
						$styleSettings[substr($key, strlen($stylePrefix))] = $value;
					elseif($slidePrefix == substr($key, 0, strlen($slidePrefix)))
						$slides[substr($key, strlen($slidePrefix))] = $value;
				}

				// Slides are prefixed with another prefix, their order ID. All settings of one slide should go into an
				// array referenced by their order ID. Create order lookup array below, then order slides accordingly
				$slidesOrderLookup = array();
				foreach($slides as $key => $value){
					$key = explode('_', $key);

					if($key[1] == 'order')
						$slidesOrderLookup[$value] = $key[0];
				}

				// Order slides with order lookup array
				$orderedSlides = array();
				foreach($slides as $key => $value){
					$key = explode('_', $key);

					foreach($slidesOrderLookup as $order => $id){
						if($key[0] == $id){

							// Create array if slot is empty
							if(!isset($orderedSlides[$order]) || !is_array($orderedSlides[$order]))
								$orderedSlides[$order] = array();

							// Add slide value to array
							$orderedSlides[$order][$key[1]] = $value;

							// Slide ID found and value placed in correct order slot, break to next $value
							break;
						}
					}
				}

				// Update post meta
				update_post_meta($slideshow->ID, $settingsKey, $newSettings);
				update_post_meta($slideshow->ID, $styleSettingsKey, $styleSettings);
				update_post_meta($slideshow->ID, $slidesKey, $orderedSlides);
			}
		}

		update_option('slideshow-plugin-updated-from-v2-to-v2-1-20', 'updated');
	}

	/**
	 * Updates v1 slides to the V2 slide format
	 * Slides are no longer attachments, convert attachments to post-meta.
	 *
	 * @since 2.0.1
	 */
	private static function updateV1toV2(){
		// Check if this has already been done
		if(get_option('slideshow-plugin-updated-from-v1-x-x-to-v2-0-1') !== false)
			return;

		// Get posts
		$posts = get_posts(array(
			'numberposts' => -1,
			'offset' => 0,
			'post_type' => 'slideshow'
		));

		// Loop through posts
		foreach($posts as $post){

			// Stores highest slide id.
			$highestSlideId = -1;

			// Defaults
			$defaultData = $data = array(
				'style_style' => 'light',
				'style_custom' => '',
				'setting_animation' => 'slide',
				'setting_slideSpeed' => '1',
				'setting_descriptionSpeed' => '0.4',
				'setting_intervalSpeed' => '5',
				'setting_play' => 'true',
				'setting_loop' => 'true',
				'setting_slidesPerView' => '1',
				'setting_width' => '0',
				'setting_height' => '200',
				'setting_descriptionHeight' => '50',
				'setting_stretchImages' => 'true',
				'setting_controllable' => 'true',
				'setting_controlPanel' => 'false',
				'setting_showDescription' => 'true',
				'setting_hideDescription' => 'true'
			);

			$yes = __('Yes', 'slideshow-plugin');
			$no = __('No', 'slideshow-plugin');
			$data = array( // $data : array([prefix_settingName] => array([inputType], [value], [default], [description], array([options]), array([dependsOn], [onValue]), 'group' => [groupName]))
				'style_style' => array('select', '', $defaultData['style_style'], __('The style used for this slideshow', 'slideshow-plugin'), array('light' => __('Light', 'slideshow-plugin'), 'dark' => __('Dark', 'slideshow-plugin'), 'custom' => __('Custom', 'slideshow-plugin'))),
				'style_custom' => array('textarea', '', $defaultData['style_custom'], __('Custom style editor', 'slideshow-plugin'), null, array('style_style', 'custom')),
				'setting_animation' => array('select', '', $defaultData['setting_animation'], __('Animation used for transition between slides', 'slideshow-plugin'), array('slide' => __('Slide', 'slideshow-plugin'), 'fade' => __('Fade', 'slideshow-plugin')), 'group' => __('Animation', 'slideshow-plugin')),
				'setting_slideSpeed' => array('text', '', $defaultData['setting_slideSpeed'], __('Number of seconds the slide takes to slide in', 'slideshow-plugin'), 'group' => __('Animation', 'slideshow-plugin')),
				'setting_descriptionSpeed' => array('text', '', $defaultData['setting_descriptionSpeed'], __('Number of seconds the description takes to slide in', 'slideshow-plugin'), 'group' => __('Animation', 'slideshow-plugin')),
				'setting_intervalSpeed' => array('text', '', $defaultData['setting_intervalSpeed'], __('Seconds between changing slides', 'slideshow-plugin'), 'group' => __('Animation', 'slideshow-plugin')),
				'setting_slidesPerView' => array('text', '', $defaultData['setting_slidesPerView'], __('Number of slides to fit into one slide', 'slideshow-plugin'), 'group' => __('Display', 'slideshow-plugin')),
				'setting_width' => array('text', '', $defaultData['setting_width'], __('Width of the slideshow, set to parent&#39;s width on 0', 'slideshow-plugin'), 'group' => __('Display', 'slideshow-plugin')),
				'setting_height' => array('text', '', $defaultData['setting_height'], __('Height of the slideshow', 'slideshow-plugin'), 'group' => __('Display', 'slideshow-plugin')),
				'setting_descriptionHeight' => array('text', '', $defaultData['setting_descriptionHeight'], __('Height of the description boxes', 'slideshow-plugin'), 'group' => __('Display', 'slideshow-plugin')),
				'setting_stretchImages' => array('radio', '', $defaultData['setting_stretchImages'], __('Fit image into slide (stretching it)', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), 'group' => __('Display', 'slideshow-plugin')),
				'setting_showDescription' => array('radio', '', $defaultData['setting_showDescription'], __('Show title and description', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), 'group' => __('Display', 'slideshow-plugin')),
				'setting_hideDescription' => array('radio', '', $defaultData['setting_hideDescription'], __('Hide description box, it will pop up when a mouse hovers over the slide', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), array('setting_showDescription', 'true'), 'group' => __('Display', 'slideshow-plugin')),
				'setting_play' => array('radio', '', $defaultData['setting_play'], __('Automatically slide to the next slide', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), 'group' => __('Control', 'slideshow-plugin')),
				'setting_loop' => array('radio', '', $defaultData['setting_loop'], __('Return to the beginning of the slideshow after last slide', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), 'group' => __('Control', 'slideshow-plugin')),
				'setting_controllable' => array('radio', '', $defaultData['setting_controllable'], __('Activate buttons (so the user can scroll through the slides)', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), 'group' => __('Control', 'slideshow-plugin')),
				'setting_controlPanel' => array('radio', '', $defaultData['setting_controlPanel'], __('Show control panel (play and pause button)', 'slideshow-plugin'), array('true' => $yes, 'false' => $no), 'group' => __('Control', 'slideshow-plugin')),
			);

			// Get settings
			$currentSettings = get_post_meta(
				$post->ID,
				'settings',
				true
			);

			// Fill data with settings
			foreach($data as $key => $value)
				if(isset($currentSettings[$key])){
					$data[$key][1] = $currentSettings[$key];
					unset($currentSettings[$key]);
				}

			// Load settings that are not there by default into data (slides in particular)
			foreach($currentSettings as $key => $value)
				if(!isset($data[$key]))
					$data[$key] = $value;

			// Settings
			$settings = $data;

			// Filter slides
			$prefix = 'slide_';
			foreach($settings as $key => $value)
				if($prefix != substr($key, 0, strlen($prefix)))
					unset($settings[$key]);

			// Convert slide settings to array([slide-key] => array([setting-name] => [value]));
			$slidesPreOrder = array();
			foreach($settings as $key => $value){
				$key = explode('_', $key);
				if(is_numeric($key[1]))
					$slidesPreOrder[$key[1]][$key[2]] = $value;
			}

			// Save slide keys from the $slidePreOrder array in the array itself for later use
			foreach($slidesPreOrder as $key => $value){
				// Save highest slide id
				if($key > $highestSlideId)
					$highestSlideId = $key;
			}

			// Get old data
			$oldData = get_post_meta($post->ID, 'settings', true);
			if(!is_array(($oldData)))
				$oldData = array();

			// Get attachments
			$attachments = get_posts(array(
				'numberposts' => -1,
				'offset' => 0,
				'post_type' => 'attachment',
				'post_parent' => $post->ID
			));

			// Get data from attachments
			$newData = array();
			foreach($attachments as $attachment){
				$highestSlideId++;
				$newData['slide_' . $highestSlideId . '_postId'] = $attachment->ID;
				$newData['slide_' . $highestSlideId . '_type'] = 'attachment';
			}

			// Save settings
			update_post_meta(
				$post->ID,
				'settings',
				array_merge(
					$defaultData,
					$oldData,
					$newData
				));
		}

		update_option('slideshow-plugin-updated-from-v1-x-x-to-v2-0-1', 'updated');
	}
}
