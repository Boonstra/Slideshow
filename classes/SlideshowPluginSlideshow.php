<?php
/**
 * SlideshowPluginSlideshow creates a post type specifically designed for
 * slideshows and their individual settings
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 */
class SlideshowPluginSlideshow extends SlideshowPluginModel
{
	/** @var string */
	static $postType = 'slideshow';

	/** @var array */
	static $postMetaDefaults = array(
		'_slideshow_jquery_image_gallery_slides'           => array(),
		'_slideshow_jquery_image_gallery_style'            => -1,
		'_slideshow_jquery_image_gallery_settings_profile' => -1,
	);

	/**
	 * Registers class with the slideshow's post type class.
	 */
	static function init()
	{
		global $wp_version;

		add_action('admin_menu'           , array(__CLASS__, 'modifyAdminMenu'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'localizeScript'), 11);

		SlideshowPluginPostType::registerPostType(
			__CLASS__,
			self::$postType,
			array(
				'labels'               => array(
					'name'               => __('Slideshows', 'slideshow-plugin'),
					'singular_name'      => __('Slideshow', 'slideshow-plugin'),
					'menu_name'          => __('Slideshows', 'slideshow-plugin'),
					'name_admin_bar'     => __('Slideshows', 'slideshow-plugin'),
					'add_new'            => __('Add New', 'slideshow-plugin'),
					'add_new_item'       => __('Add New Slideshow', 'slideshow-plugin'),
					'new_item'           => __('New Slideshow', 'slideshow-plugin'),
					'edit_item'          => __('Edit slideshow', 'slideshow-plugin'),
					'view_item'          => __('View slideshow', 'slideshow-plugin'),
					'all_items'          => __('All Slideshows', 'slideshow-plugin'),
					'search_items'       => __('Search Slideshows', 'slideshow-plugin'),
					'parent_item_colon'  => __('Parent Slideshows:', 'slideshow-plugin'),
					'not_found'          => __('No slideshows found', 'slideshow-plugin'),
					'not_found_in_trash' => __('No slideshows found', 'slideshow-plugin')
				),
				'public'               => false,
				'publicly_queryable'   => false,
				'show_ui'              => true,
				'show_in_menu'         => true,
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
				'menu_icon'            => version_compare($wp_version, '3.8', '<') ? SlideshowPluginMain::getPluginUrl() . '/images/' . __CLASS__ . '/adminIcon.png' : 'dashicons-format-gallery',
				'supports'             => array('title'),
			),
			array(
				'_slideshow_jquery_image_gallery_information'      => array(
					'dataType'      => null,
					'title'         => __('Information', 'slideshow-plugin'),
					'callback'      => array(__CLASS__, 'informationMetaBox'),
					'screen'        => self::$postType,
					'context'       => 'normal',
					'priority'      => 'high',
					'callback_args' => null,
				),
				'_slideshow_jquery_image_gallery_slides'           => array(
					'dataType'      => 'array',
					'title'         => __('Slides', 'slideshow-plugin'),
					'callback'      => array(__CLASS__, 'slidesMetaBox'),
					'screen'        => self::$postType,
					'context'       => 'advanced',
					'priority'      => 'default',
					'callback_args' => null,
				),
				'_slideshow_jquery_image_gallery_style'            => array(
					'dataType'      => 'int',
					'title'         => __('Style', 'slideshow-plugin'),
					'callback'      => array(__CLASS__, 'styleMetaBox'),
					'screen'        => self::$postType,
					'context'       => 'side',
					'priority'      => 'default',
					'callback_args' => null,
				),
				'_slideshow_jquery_image_gallery_settings_profile' => array(
					'dataType'      => 'int',
					'title'         => __('Settings Profile', 'slideshow-plugin'),
					'callback'      => array(__CLASS__, 'settingsMetaBox'),
					'screen'        => self::$postType,
					'context'       => 'side',
					'priority'      => 'default',
					'callback_args' => null,
				),
			)
		);
	}

	/**
	 * @see SlideshowPluginModel::__construct
	 * @param int|null|WP_Post $post
	 */
	function __construct($post)
	{
		$this->modelPostType = self::$postType;

		parent::__construct($post);
	}

	/**
	 * Get default post meta by the passed key.
	 *
	 * @since 2.3.0
	 * @see SlideshowPluginModel::getPostMetaDefaults
	 */
	function getPostMetaDefaults($key)
	{
		if (isset(self::$postMetaDefaults[$key]))
		{
			return self::$postMetaDefaults[$key];
		}

		return null;
	}

//	/** @var string $postType */
//	static $postType = 'slideshow';
//
//	/**
//	 * Initialize Slideshow post type.
//	 * Called on load of plugin
//	 *
//	 * @since 1.3.0
//	 */
//	static function init()
//	{
//		add_action('init'                 , array(__CLASS__, 'registerSlideshowPostType'));
//		add_action('save_post'            , array('SlideshowPluginSlideshowSettingsHandler', 'save'));
//		add_action('admin_menu'           , array(__CLASS__, 'modifyAdminMenu'));
//		add_action('admin_enqueue_scripts', array('SlideshowPluginSlideInserter', 'localizeScript'), 11);
//
//		add_action('admin_action_slideshow_jquery_image_gallery_duplicate_slideshow', array(__CLASS__, 'duplicate'), 11);
//
//		add_filter('post_updated_messages', array(__CLASS__, 'alterSlideshowMessages'));
//		add_filter('post_row_actions'     , array(__CLASS__, 'duplicateActionLink'), 10, 2);
//	}
//
//	/**
//	 * Registers new post type slideshow
//	 *
//	 * @since 1.0.0
//	 */
//	static function registerSlideshowPostType()
//	{
//		global $wp_version;
//
//		register_post_type(
//			self::$postType,
//			array(
//				'labels'               => array(
//					'name'               => __('Slideshows', 'slideshow-plugin'),
//					'singular_name'      => __('Slideshow', 'slideshow-plugin'),
//					'menu_name'          => __('Slideshows', 'slideshow-plugin'),
//					'name_admin_bar'     => __('Slideshows', 'slideshow-plugin'),
//					'add_new'            => __('Add New', 'slideshow-plugin'),
//					'add_new_item'       => __('Add New Slideshow', 'slideshow-plugin'),
//					'new_item'           => __('New Slideshow', 'slideshow-plugin'),
//					'edit_item'          => __('Edit slideshow', 'slideshow-plugin'),
//					'view_item'          => __('View slideshow', 'slideshow-plugin'),
//					'all_items'          => __('All Slideshows', 'slideshow-plugin'),
//					'search_items'       => __('Search Slideshows', 'slideshow-plugin'),
//					'parent_item_colon'  => __('Parent Slideshows:', 'slideshow-plugin'),
//					'not_found'          => __('No slideshows found', 'slideshow-plugin'),
//					'not_found_in_trash' => __('No slideshows found', 'slideshow-plugin')
//				),
//				'public'               => false,
//				'publicly_queryable'   => false,
//				'show_ui'              => true,
//				'show_in_menu'         => true,
//				'query_var'            => true,
//				'rewrite'              => true,
//				'capability_type'      => 'post',
//				'capabilities'         => array(
//					'edit_post'              => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
//					'read_post'              => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
//					'delete_post'            => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
//					'edit_posts'             => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
//					'edit_others_posts'      => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
//					'publish_posts'          => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
//					'read_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
//
//					'read'                   => SlideshowPluginGeneralSettings::$capabilities['addSlideshows'],
//					'delete_posts'           => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
//					'delete_private_posts'   => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
//					'delete_published_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
//					'delete_others_posts'    => SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'],
//					'edit_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
//					'edit_published_posts'   => SlideshowPluginGeneralSettings::$capabilities['editSlideshows'],
//				),
//				'has_archive'          => true,
//				'hierarchical'         => false,
//				'menu_position'        => null,
//				'menu_icon'            => version_compare($wp_version, '3.8', '<') ? SlideshowPluginMain::getPluginUrl() . '/images/' . __CLASS__ . '/adminIcon.png' : 'dashicons-format-gallery',
//				'supports'             => array('title'),
//				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
//			)
//		);
//	}

//	/**
//	 * Adds custom meta boxes to slideshow post type.
//	 *
//	 * @since 1.0.0
//	 */
//	static function registerMetaBoxes()
//	{
//		add_meta_box(
//			'information',
//			__('Information', 'slideshow-plugin'),
//			array(__CLASS__, 'informationMetaBox'),
//			self::$postType,
//			'normal',
//			'high'
//		);
//
//		add_meta_box(
//			'slides-list',
//			__('Slides', 'slideshow-plugin'),
//			array(__CLASS__, 'slidesMetaBox'),
//			self::$postType,
//			'advanced',
//			'default'
//		);
//
//		add_meta_box(
//			'style',
//			__('Style', 'slideshow-plugin'),
//			array(__CLASS__, 'styleMetaBox'),
//			self::$postType,
//			'side',
//			'default'
//		);
//
//		add_meta_box(
//			'settings',
//			__('Settings Profile', 'slideshow-plugin'),
//			array(__CLASS__, 'settingsMetaBox'),
//			self::$postType,
//			'side',
//			'default'
//		);
//
//		// Add support plugin message on edit slideshow
//		if (isset($_GET['action']) &&
//			strtolower($_GET['action']) == strtolower('edit'))
//		{
//			add_action('admin_notices', array(__CLASS__,  'supportPluginMessage'));
//		}
//	}

//	/**
//	 * Changes the "Post published/updated" message to a "Slideshow created/updated" message without the link to a
//	 * frontend page.
//	 *
//	 * @since 2.2.20
//	 * @param mixed $messages
//	 * @return mixed $messages
//	 */
//	static function alterSlideshowMessages($messages)
//	{
//		if (!function_exists('get_current_screen'))
//		{
//			return $messages;
//		}
//
//		$currentScreen = get_current_screen();
//
//		// Return when not on a slideshow edit page
//		if ($currentScreen->post_type != SlideshowPluginSlideshow::$postType)
//		{
//			return $messages;
//		}
//
//		$messageID = filter_input(INPUT_GET, 'message', FILTER_VALIDATE_INT);
//
//		if (!$messageID)
//		{
//			return $messages;
//		}
//
//		switch ($messageID)
//		{
//			case 6:
//				$messages[$currentScreen->base][$messageID] = __('Slideshow created', 'slideshow-plugin');
//				break;
//
//			default:
//				$messages[$currentScreen->base][$messageID] = __('Slideshow updated', 'slideshow-plugin');
//		}
//
//		return $messages;
//	}

	/**
	 * Shows the support plugin message
	 *
	 * @since 2.0.0
	 */
	static function supportPluginMessage()
	{
		// TODO Show support message on edit slideshow
//		// Add support plugin message on edit slideshow
//		if (isset($_GET['action']) &&
//			strtolower($_GET['action']) == strtolower('edit'))
//		{
//			add_action('admin_notices', array(__CLASS__,  'supportPluginMessage'));
//		}

		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/support-plugin.php';
	}

	/**
	 * Shows some information about this slideshow
	 *
	 * @since 1.0.0
	 */
	static function informationMetaBox()
	{
		global $post;

		$snippet   = htmlentities(sprintf('<?php do_action(\'slideshow_deploy\', \'%s\'); ?>', $post->ID));
		$shortCode = htmlentities(sprintf('[' . SlideshowPluginShortcode::$shortCode . ' id=\'%s\']', $post->ID));

		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/information.php';
	}

	/**
	 * Shows slides currently in slideshow
	 *
	 * TODO Tidy up, it's probably best to move all to 'slides.php'
	 *
	 * @since 1.0.0
	 */
	static function slidesMetaBox()
	{
		global $post;

		// Get views
		$views = SlideshowPluginSlideshowSettingsHandler::getViews($post->ID);

		// Insert slide buttons
		echo '<p style="text-align: center;">
			<i>' . __('Insert', 'slideshow-plugin') . ':</i><br/>' .
			SlideshowPluginSlideInserter::getImageSlideInsertButton() .
			SlideshowPluginSlideInserter::getTextSlideInsertButton() .
			SlideshowPluginSlideInserter::getVideoSlideInsertButton() .
			'</p>';

		// Toggle slides open/closed
		echo '<p style="text-align: center;">
			<a href="#" class="open-slides-button">' . __( 'Open all', 'slideshow-plugin' ) . '</a>
			|
			<a href="#" class="close-slides-button">' . __( 'Close all', 'slideshow-plugin' ) . '</a>
		</p>';

		// No views/slides message
		if (count($views) <= 0)
		{
			echo '<p>' . __('Add slides to this slideshow by using one of the buttons above.', 'slideshow-plugin') . '</p>';
		}

		// Start list
		echo '<div class="sortable-slides-list">';

		// Print views
		if (is_array($views))
		{
			foreach($views as $view)
			{
				if (!($view instanceof SlideshowPluginSlideshowView))
				{
					continue;
				}

				echo $view->toBackEndHTML();
			}
		}

		// End list
		echo '</div>';

		// Templates
		SlideshowPluginSlideshowSlide::getBackEndTemplates(false);
	}

	/**
	 * Shows style used for slideshow
	 *
	 * TODO Improve styling for usage in sidebar.
	 *
	 * @since 1.3.0
	 */
	static function styleMetaBox()
	{
		echo 'Placeholder for styles dropdown';

		echo '<br /><br />Add "edit style" link';

//		global $post;
//
//		// Get settings
//		$settings = SlideshowPluginSlideshowSettingsHandler::getStyleSettings($post->ID, true);
//
//		// Include style settings file
//		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/style-settings.php';
	}

	/**
	 * Shows settings for particular slideshow
	 *
	 * TODO Implement.
	 *
	 * @since 1.0.0
	 */
	static function settingsMetaBox()
	{
//		global $post;

		$postTypeInformation = SlideshowPluginPostType::getPostTypeInformation(self::$postType);

		wp_nonce_field($postTypeInformation['nonceAction'], $postTypeInformation['nonceName']);

		echo 'Placeholder for settings profiles dropdown';

		echo '<br /><br />Add "edit settings profile" link';

//		// Get settings
//		$settings = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID, true);
//
//		// Include
//		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/settings.php';
	}

	/**
	 * Modifies the admin menu, removing the "Add New" link from the slideshow menu.
	 *
	 * @since 2.3.0
	 */
	static function modifyAdminMenu()
	{
		global $submenu;

		unset($submenu['edit.php?post_type=' . self::$postType][10]);
	}

	/**
	 * Enqueues styles and scripts.
	 *
	 * @since 2.3.0
	 */
	static function localizeScript()
	{
		if (SlideshowPluginMain::getCurrentPostType() != self::$postType)
		{
			return;
		}

		wp_localize_script(
			'slideshow-jquery-image-gallery-backend-script',
			'slideshow_jquery_image_gallery_backend_script_slideshow',
			array(
				'data' => array(),
				'localization' => array(
					'confirm'       => __('Are you sure you want to delete this slide?', 'slideshow-plugin'),
					'uploaderTitle' => __('Insert image slide', 'slideshow-plugin')
				)
			)
		);
	}

//	/**
//	 * Hooked on the post_row_actions filter, adds a "duplicate" action to each slideshow on the slideshow's overview
//	 * page.
//	 *
//	 * @since 2.2.20
//	 * @param array $actions
//	 * @param WP_Post $post
//	 * @return array $actions
//	 */
//	static function duplicateActionLink($actions, $post)
//	{
//		if (current_user_can(SlideshowPluginGeneralSettings::$capabilities['addSlideshows']) &&
//			$post->post_type === self::$postType)
//		{
//			$url = add_query_arg(array(
//				'action' => 'slideshow_jquery_image_gallery_duplicate_slideshow',
//				'post'   => $post->ID,
//			));
//
//			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-slideshow_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-plugin') . '</a>';
//		}
//
//		return $actions;
//	}
//
//	/**
//	 * Checks if a "duplicate" slideshow action was performed and whether or not the current user has the permission to
//	 * perform this action at all.
//	 *
//	 * @since 2.2.20
//	 */
//	static function duplicate()
//	{
//		$postID           = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
//		$nonce            = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
//		$postType         = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
//		$errorRedirectURL = remove_query_arg(array('action', 'post', 'nonce'));
//
//		// Check if nonce is correct and user has the correct privileges
//		if (!wp_verify_nonce($nonce, 'duplicate-slideshow_' . $postID) ||
//			!current_user_can(SlideshowPluginGeneralSettings::$capabilities['addSlideshows']) ||
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
//		if ($newPostID <= 0)
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
//	}
}