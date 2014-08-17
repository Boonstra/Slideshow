<?php
/**
 * @since 2.3.0
 * @author Stefan Boonstra
 */
class SlideshowPluginSettingsProfile
{
	/** @var string */
	static $postType = 'slideshow_sett_prof';

	/** @var string */
	static $nonceAction = 'slideshow-jquery-image-gallery-settings-profile-nonce-action';

	/** @var string */
	static $nonceName = 'slideshow-jquery-image-gallery-settings-profile-nonce-name';

	/** @var mixed */
	public $variables = array(
		'animation'                   => 'slide',
		'slideSpeed'                  => '1',
		'descriptionSpeed'            => '0.4',
		'intervalSpeed'               => '5',
		'slidesPerView'               => '1',
		'maxWidth'                    => '0',
		'aspectRatio'                 => '3:1',
		'height'                      => '200',
		'imageBehaviour'              => 'natural',
		'showDescription'             => 'true',
		'hideDescription'             => 'true',
		'preserveSlideshowDimensions' => 'false',
		'enableResponsiveness'        => 'true',
		'play'                        => 'true',
		'loop'                        => 'true',
		'pauseOnHover'                => 'true',
		'controllable'                => 'true',
		'hideNavigationButtons'       => 'false',
		'showPagination'              => 'true',
		'hidePagination'              => 'true',
		'controlPanel'                => 'false',
		'hideControlPanel'            => 'true',
		'waitUntilLoaded'             => 'true',
		'showLoadingIcon'             => 'true',
		'random'                      => 'false',
		'avoidFilter'                 => 'true'
	);

	/**
	 * Initialize settings profile post type.
	 *
	 * @since 2.3.0
	 */
	static function init()
	{
		add_action('init', array(__CLASS__, 'registerPostType'));
//		add_action('save_post'            , array(__CLASS__, 'save')); // TODO Implement a save method

		add_filter('post_updated_messages', array(__CLASS__, 'alterCRUDMessages'));
		add_filter('post_row_actions'     , array(__CLASS__, 'duplicateActionLink'), 10, 2);
	}

	/**
	 * Registers the settings profile post type.
	 *
	 * @since 2.3.0
	 */
	static function registerPostType()
	{
		register_post_type(
			self::$postType,
			array(
				'labels'               => array(
					'name'               => __('Settings profiles', 'slideshow-plugin'),
					'singular_name'      => __('Settings profile', 'slideshow-plugin'),
					'menu_name'          => __('Settings Profile', 'slideshow-plugin'),
					'name_admin_bar'     => __('Settings Profile', 'slideshow-plugin'),
					'add_new'            => __('Add New', 'slideshow-plugin'),
					'add_new_item'       => __('Add New Settings Profile', 'slideshow-plugin'),
					'new_item'           => __('New Settings Profile', 'slideshow-plugin'),
					'edit_item'          => __('Edit Settings Profile', 'slideshow-plugin'),
					'view_item'          => __('View Settings Profile', 'slideshow-plugin'),
					'all_items'          => __('All Settings Profiles', 'slideshow-plugin'),
					'search_items'       => __('Search Settings Profiles', 'slideshow-plugin'),
					'parent_item_colon'  => __('Parent Settings Profiles:', 'slideshow-plugin'),
					'not_found'          => __('No settings profiles found', 'slideshow-plugin'),
					'not_found_in_trash' => __('No settings profiles found', 'slideshow-plugin')
				),
				'public'               => false,
				'publicly_queryable'   => false,
				'show_ui'              => true,
				'show_in_menu'         => 'edit.php?post_type=' . SlideshowPluginPostType::$postType,
				'query_var'            => true,
				'rewrite'              => true,
				'capability_type'      => 'post',
				'capabilities'         => array(
					'edit_post'              => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'read_post'              => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
					'delete_post'            => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'edit_posts'             => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'edit_others_posts'      => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'publish_posts'          => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
					'read_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],

					'read'                   => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
					'delete_posts'           => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'delete_private_posts'   => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'delete_published_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'delete_others_posts'    => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
					'edit_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
					'edit_published_posts'   => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
				),
				'has_archive'          => true,
				'hierarchical'         => false,
				'menu_position'        => null,
				'supports'             => array('title'),
				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
			)
		);
	}

	/**
	 * Add custom meta boxes.
	 *
	 * @since 2.3.0
	 */
	static function registerMetaBoxes()
	{
		add_meta_box(
			'settings',
			__('Settings', 'slideshow-plugin'),
			array(__CLASS__, 'settingsMetaBox'),
			self::$postType,
			'normal',
			'default'
		);
	}

	/**
	 * Changes the "Post published/updated" message to a "Settings profile created/updated" message without the link to a
	 * frontend page.
	 *
	 * @since 2.3.0
	 * @param mixed $messages
	 * @return mixed $messages
	 */
	static function alterCRUDMessages($messages)
	{
		if (!function_exists('get_current_screen'))
		{
			return $messages;
		}

		$currentScreen = get_current_screen();

		// Return when not on a slideshow edit page
		if ($currentScreen->post_type != self::$postType)
		{
			return $messages;
		}

		$messageID = filter_input(INPUT_GET, 'message', FILTER_VALIDATE_INT);

		if (!$messageID)
		{
			return $messages;
		}

		switch ($messageID)
		{
			case 6:
				$messages[$currentScreen->base][$messageID] = __('Settings profile created', 'slideshow-plugin');
				break;

			default:
				$messages[$currentScreen->base][$messageID] = __('Settings profile updated', 'slideshow-plugin');
		}

		return $messages;
	}

	/**
	 * Shows the settings for the current settings profile.
	 *
	 * TODO Implement
	 *
	 * @since 2.3.0
	 */
	static function settingsMetaBox()
	{
		global $post;

		var_dump($post);

		// Nonce
		wp_nonce_field(self::$nonceAction, self::$nonceName);

		// Get settings
		$settings = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID, true);

		// Include
		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/settings.php';
	}

	/**
	 * Returns an array of setting defaults.
	 *
	 * For a full description of the parameters, see getAllDefaults().
	 *
	 * @since 2.1.20
	 * @param boolean $fullDefinition (optional, defaults to false)
	 * @param boolean $fromDatabase (optional, defaults to true)
	 * @return mixed $data
	 */
	static function getDefaultSettings($fullDefinition = false, $fromDatabase = true)
	{
		// Much used data for translation
		$yes = __('Yes', 'slideshow-plugin');
		$no  = __('No', 'slideshow-plugin');

		// Default values
		$data = array(
			'animation' => 'slide',
			'slideSpeed' => '1',
			'descriptionSpeed' => '0.4',
			'intervalSpeed' => '5',
			'slidesPerView' => '1',
			'maxWidth' => '0',
			'aspectRatio' => '3:1',
			'height' => '200',
			'imageBehaviour' => 'natural',
			'showDescription' => 'true',
			'hideDescription' => 'true',
			'preserveSlideshowDimensions' => 'false',
			'enableResponsiveness' => 'true',
			'play' => 'true',
			'loop' => 'true',
			'pauseOnHover' => 'true',
			'controllable' => 'true',
			'hideNavigationButtons' => 'false',
			'showPagination' => 'true',
			'hidePagination' => 'true',
			'controlPanel' => 'false',
			'hideControlPanel' => 'true',
			'waitUntilLoaded' => 'true',
			'showLoadingIcon' => 'true',
			'random' => 'false',
			'avoidFilter' => 'true'
		);

		// Read defaults from database and merge with $data, when $fromDatabase is set to true
		if ($fromDatabase)
		{
			$data = array_merge(
				$data,
				$customData = get_option(SlideshowPluginGeneralSettings::$defaultSettings, array())
			);
		}

		// Full definition
		if ($fullDefinition)
		{
			$descriptions = array(
				'animation'                   => __('Animation used for transition between slides', 'slideshow-plugin'),
				'slideSpeed'                  => __('Number of seconds the slide takes to slide in', 'slideshow-plugin'),
				'descriptionSpeed'            => __('Number of seconds the description takes to slide in', 'slideshow-plugin'),
				'intervalSpeed'               => __('Seconds between changing slides', 'slideshow-plugin'),
				'slidesPerView'               => __('Number of slides to fit into one slide', 'slideshow-plugin'),
				'maxWidth'                    => __('Maximum width. When maximum width is 0, maximum width is ignored', 'slideshow-plugin'),
				'aspectRatio'                 => sprintf('<a href="' . str_replace('%', '%%', __('http://en.wikipedia.org/wiki/Aspect_ratio_(image)', 'slideshow-plugin')) . '" title="' . __('More info', 'slideshow-plugin') . '" target="_blank">' . __('Proportional relationship%s between slideshow\'s width and height (width:height)', 'slideshow-plugin'), '</a>'),
				'height'                      => __('Slideshow\'s height', 'slideshow-plugin'),
				'imageBehaviour'              => __('Image behaviour', 'slideshow-plugin'),
				'preserveSlideshowDimensions' => __('Shrink slideshow\'s height when width shrinks', 'slideshow-plugin'),
				'enableResponsiveness'        => __('Enable responsiveness (Shrink slideshow\'s width when page\'s width shrinks)', 'slideshow-plugin'),
				'showDescription'             => __('Show title and description', 'slideshow-plugin'),
				'hideDescription'             => __('Hide description box, pop up when mouse hovers over', 'slideshow-plugin'),
				'play'                        => __('Automatically slide to the next slide', 'slideshow-plugin'),
				'loop'                        => __('Return to the beginning of the slideshow after last slide', 'slideshow-plugin'),
				'pauseOnHover'                => __('Pause slideshow when mouse hovers over', 'slideshow-plugin'),
				'controllable'                => __('Activate navigation buttons', 'slideshow-plugin'),
				'hideNavigationButtons'       => __('Hide navigation buttons, show when mouse hovers over', 'slideshow-plugin'),
				'showPagination'              => __('Activate pagination', 'slideshow-plugin'),
				'hidePagination'              => __('Hide pagination, show when mouse hovers over', 'slideshow-plugin'),
				'controlPanel'                => __('Activate control panel (play and pause button)', 'slideshow-plugin'),
				'hideControlPanel'            => __('Hide control panel, show when mouse hovers over', 'slideshow-plugin'),
				'waitUntilLoaded'             => __('Wait until the next slide has loaded before showing it', 'slideshow-plugin'),
				'showLoadingIcon'             => __('Show a loading icon until the first slide appears', 'slideshow-plugin'),
				'random'                      => __('Randomize slides', 'slideshow-plugin'),
				'avoidFilter'                 => sprintf(__('Avoid content filter (disable if \'%s\' is shown)', 'slideshow-plugin'), SlideshowPluginShortcode::$bookmark)
			);

			$data = array(
				'animation'                   => array('type' => 'select', 'default' => $data['animation']                  , 'description' => $descriptions['animation']                  , 'group' => __('Animation', 'slideshow-plugin')    , 'options' => array('slide' => __('Slide Left', 'slideshow-plugin'), 'slideRight' => __('Slide Right', 'slideshow-plugin'), 'slideUp' => __('Slide Up', 'slideshow-plugin'), 'slideDown' => __('Slide Down', 'slideshow-plugin'), 'crossFade' => __('Cross Fade', 'slideshow-plugin'), 'directFade' => __('Direct Fade', 'slideshow-plugin'), 'fade' => __('Fade', 'slideshow-plugin'), 'random' => __('Random Animation', 'slideshow-plugin'))),
				'slideSpeed'                  => array('type' => 'text'  , 'default' => $data['slideSpeed']                 , 'description' => $descriptions['slideSpeed']                 , 'group' => __('Animation', 'slideshow-plugin')),
				'descriptionSpeed'            => array('type' => 'text'  , 'default' => $data['descriptionSpeed']           , 'description' => $descriptions['descriptionSpeed']           , 'group' => __('Animation', 'slideshow-plugin')),
				'intervalSpeed'               => array('type' => 'text'  , 'default' => $data['intervalSpeed']              , 'description' => $descriptions['intervalSpeed']              , 'group' => __('Animation', 'slideshow-plugin')),
				'slidesPerView'               => array('type' => 'text'  , 'default' => $data['slidesPerView']              , 'description' => $descriptions['slidesPerView']              , 'group' => __('Display', 'slideshow-plugin')),
				'maxWidth'                    => array('type' => 'text'  , 'default' => $data['maxWidth']                   , 'description' => $descriptions['maxWidth']                   , 'group' => __('Display', 'slideshow-plugin')),
				'aspectRatio'                 => array('type' => 'text'  , 'default' => $data['aspectRatio']                , 'description' => $descriptions['aspectRatio']                , 'group' => __('Display', 'slideshow-plugin')                                                           , 'dependsOn' => array('settings[preserveSlideshowDimensions]', 'true')),
				'height'                      => array('type' => 'text'  , 'default' => $data['height']                     , 'description' => $descriptions['height']                     , 'group' => __('Display', 'slideshow-plugin')                                                           , 'dependsOn' => array('settings[preserveSlideshowDimensions]', 'false')),
				'imageBehaviour'              => array('type' => 'select', 'default' => $data['imageBehaviour']             , 'description' => $descriptions['imageBehaviour']             , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('natural' => __('Natural and centered', 'slideshow-plugin'), 'crop' => __('Crop to fit', 'slideshow-plugin'), 'stretch' => __('Stretch to fit', 'slideshow-plugin'))),
				'preserveSlideshowDimensions' => array('type' => 'radio' , 'default' => $data['preserveSlideshowDimensions'], 'description' => $descriptions['preserveSlideshowDimensions'], 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[enableResponsiveness]', 'true')),
				'enableResponsiveness'        => array('type' => 'radio' , 'default' => $data['enableResponsiveness']       , 'description' => $descriptions['enableResponsiveness']       , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'showDescription'             => array('type' => 'radio' , 'default' => $data['showDescription']            , 'description' => $descriptions['showDescription']            , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'hideDescription'             => array('type' => 'radio' , 'default' => $data['hideDescription']            , 'description' => $descriptions['hideDescription']            , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[showDescription]', 'true')),
				'play'                        => array('type' => 'radio' , 'default' => $data['play']                       , 'description' => $descriptions['play']                       , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'loop'                        => array('type' => 'radio' , 'default' => $data['loop']                       , 'description' => $descriptions['loop']                       , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'pauseOnHover'                => array('type' => 'radio' , 'default' => $data['loop']                       , 'description' => $descriptions['pauseOnHover']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'controllable'                => array('type' => 'radio' , 'default' => $data['controllable']               , 'description' => $descriptions['controllable']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'hideNavigationButtons'       => array('type' => 'radio' , 'default' => $data['hideNavigationButtons']      , 'description' => $descriptions['hideNavigationButtons']      , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[controllable]', 'true')),
				'showPagination'              => array('type' => 'radio' , 'default' => $data['showPagination']             , 'description' => $descriptions['showPagination']             , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'hidePagination'              => array('type' => 'radio' , 'default' => $data['hidePagination']             , 'description' => $descriptions['hidePagination']             , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[showPagination]', 'true')),
				'controlPanel'                => array('type' => 'radio' , 'default' => $data['controlPanel']               , 'description' => $descriptions['controlPanel']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
				'hideControlPanel'            => array('type' => 'radio' , 'default' => $data['hideControlPanel']           , 'description' => $descriptions['hideControlPanel']           , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[controlPanel]', 'true')),
				'waitUntilLoaded'             => array('type' => 'radio' , 'default' => $data['waitUntilLoaded']            , 'description' => $descriptions['waitUntilLoaded']            , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no)),
				'showLoadingIcon'             => array('type' => 'radio' , 'default' => $data['showLoadingIcon']            , 'description' => $descriptions['showLoadingIcon']            , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[waitUntilLoaded]', 'true')),
				'random'                      => array('type' => 'radio' , 'default' => $data['random']                     , 'description' => $descriptions['random']                     , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no)),
				'avoidFilter'                 => array('type' => 'radio' , 'default' => $data['avoidFilter']                , 'description' => $descriptions['avoidFilter']                , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no))
			);
		}

		// Return
		return $data;
	}

	/**
	 * Hooked on the post_row_actions filter, add a "duplicate" action to each settings profile on the settings profiles
	 * overview page.
	 *
	 * TODO Implement
	 *
	 * @since 2.3.0
	 * @param array $actions
	 * @param WP_Post $post
	 * @return array $actions
	 */
	static function duplicateActionLink($actions, $post)
	{
//		if (current_user_can('slideshow-jquery-image-gallery-add-slideshows') &&
//			$post->post_type === self::$postType)
//		{
//			$url = add_query_arg(array(
//				'action' => 'slideshow_jquery_image_gallery_duplicate_slideshow',
//				'post'   => $post->ID,
//			));
//
//			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-slideshow_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-plugin') . '</a>';
//		}

		return $actions;
	}

	/**
	 * Checks if a "duplicate" settings profile action was performed and whether or not the current user has the
	 * permission to perform this action at all.
	 *
	 * TODO Implement
	 *
	 * @since 2.3.0
	 */
	static function duplicate()
	{
//		$postID           = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
//		$nonce            = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
//		$postType         = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
//		$errorRedirectURL = remove_query_arg(array('action', 'post', 'nonce'));
//
//		// Check if nonce is correct and user has the correct privileges
//		if (!wp_verify_nonce($nonce, 'duplicate-slideshow_' . $postID) ||
//			!current_user_can('slideshow-jquery-image-gallery-add-slideshows') ||
//			$postType !== self::$postType)
//		{
//			wp_redirect($errorRedirectURL);
//
//			die();
//		}
//
//		$post = get_post($postID);
//
//		// Check if the post was retrieved successfully
//		if (!$post instanceof WP_Post ||
//			$post->post_type !== self::$postType)
//		{
//			wp_redirect($errorRedirectURL);
//
//			die();
//		}
//
//		$current_user = wp_get_current_user();
//
//		// Create post duplicate
//		$newPostID = wp_insert_post(array(
//			'comment_status' => $post->comment_status,
//			'ping_status'    => $post->ping_status,
//			'post_author'    => $current_user->ID,
//			'post_content'   => $post->post_content,
//			'post_excerpt'   => $post->post_excerpt,
//			'post_name'      => $post->post_name,
//			'post_parent'    => $post->post_parent,
//			'post_password'  => $post->post_password,
//			'post_status'    => 'draft',
//			'post_title'     => $post->post_title . (strlen($post->post_title) > 0 ? ' - ' : '') . __('Copy', 'slideshow-plugin'),
//			'post_type'      => $post->post_type,
//			'to_ping'        => $post->to_ping,
//			'menu_order'     => $post->menu_order,
//		));
//
//		if (is_wp_error($newPostID))
//		{
//			wp_redirect($errorRedirectURL);
//
//			die();
//		}
//
//		// Get all taxonomies
//		$taxonomies = get_object_taxonomies($post->post_type);
//
//		// Add taxonomies to new post
//		foreach ($taxonomies as $taxonomy)
//		{
//			$postTerms = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'slugs'));
//
//			wp_set_object_terms($newPostID, $postTerms, $taxonomy, false);
//		}
//
//		// Get all post meta
//		$postMetaRecords = get_post_meta($post->ID);
//
//		// Add post meta records to new post
//		foreach ($postMetaRecords as $postMetaKey => $postMetaValues)
//		{
//			foreach ($postMetaValues as $postMetaValue)
//			{
//				update_post_meta($newPostID, $postMetaKey, maybe_unserialize($postMetaValue));
//			}
//		}
//
//		wp_redirect(admin_url('post.php?action=edit&post=' . $newPostID));
//
//		die();
	}
}