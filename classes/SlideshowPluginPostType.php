<?php
/**
 * @since 2.3.0
 * @author: Stefan Boonstra
 */
class SlideshowPluginPostType
{
	/**
	 * This variable contains all post types and their structural information that has been registered through this
	 * class. The array is structured like the following:
	 * [ {string:$postType} => [ 'class' => {string}, 'arguments' => {array}, 'postMeta' => {array}, 'nonceName' => {string}, 'nonceAction' => {string} ], ...]
	 *
	 * @var array
	 */
	protected static $postTypes = array();

	/** @var string */
	protected static $duplicateAction = 'slideshow_jquery_image_gallery_duplicate_post';

	/**
	 * Initializes the post type class with functionality such as registering post types and handling post saving.
	 *
	 * @since 1.3.0
	 */
	static function init()
	{
		add_action('init'                                  , array(__CLASS__, 'registerPostTypes'));
		add_action('save_post'                             , array(__CLASS__, 'save'));
		add_action('admin_action_' . self::$duplicateAction, array(__CLASS__, 'duplicate'), 11);

		add_filter('post_updated_messages', array(__CLASS__, 'alterCRUDMessages'));
		add_filter('post_row_actions'     , array(__CLASS__, 'duplicateActionLink'), 10, 2);
	}

	/**
	 * Registers the passed $postType.
	 *
	 * The arguments passed in $postTypeArguments parameter must match the required arguments of the $argument parameter
	 * of the "register_post_type" function.
	 *
	 * The $postMeta array is optional, but when used must be passed like the following:
	 * [ {string:id} => [ 'dataType' => {string}, 'title' => {string} 'callback' => {callable}, 'screen' => {string}, 'context' => {string}, 'priority' => {string}, 'callback_args' => {mixed}, ], ...]
	 *
	 * @since 2.3.0
	 * @param string $class
	 * @param string $postType
	 * @param array  $postTypeArguments
	 * @param array  $postMeta          (Optional, defaults to array)
	 */
	static function registerPostType($class, $postType, $postTypeArguments, $postMeta = array())
	{
		if (!isset($postTypeArguments['register_meta_box_cb']))
		{
			$postTypeArguments['register_meta_box_cb'] = array(__CLASS__, 'registerMetaBoxes');
		}

		self::$postTypes[$postType] = array(
			'class'           => $class,
			'arguments'       => $postTypeArguments,
			'postMeta'        => $postMeta,
			'nonceName'       => 'slideshow-jquery-image-gallery-' . $postType . '-nonce-name',
			'nonceAction'     => 'slideshow-jquery-image-gallery-' . $postType . '-nonce-action',
		);
	}

	/**
	 * This function is not to be called by anything but WordPress' init action callback.
	 *
	 * Registers all post types with WordPress that have been registered with this class.
	 *
	 * @since 2.3.0
	 */
	static function registerPostTypes()
	{
		foreach (self::$postTypes as $postType => $postTypeInformation)
		{
			register_post_type($postType, $postTypeInformation['arguments']);
		}
	}

	/**
	 * This function is not the be called by anything but WordPress' register_meta_box_cb callback.
	 *
	 * Registers all registered post types' meta boxes.
	 *
	 * @since 2.3.0
	 */
	static function registerMetaBoxes()
	{
		foreach (self::$postTypes as $postTypeInformation)
		{
			if ($postTypeInformation['arguments']['register_meta_box_cb'][0] !== __CLASS__)
			{
				continue;
			}

			$postMeta = $postTypeInformation['postMeta'];

			foreach ($postMeta as $postMetaKey => $postMetaInformation)
			{
				add_meta_box(
					$postMetaKey,
					$postMetaInformation['title'],
					$postMetaInformation['callback'],
					$postMetaInformation['screen'],
					$postMetaInformation['context'],
					$postMetaInformation['priority'],
					$postMetaInformation['callback_args']
				);
			}
		}

		// Add support plugin message on edit slideshow
		if (filter_input(INPUT_GET, 'action') == strtolower('edit') &&
			SlideshowPluginMain::getCurrentPostType() == SlideshowPluginSlideshow::$postType)
		{
			add_action('admin_notices', array('SlideshowPluginSlideshow',  'supportPluginMessage'));
		}
	}

	/**
	 * Returns the information for a single post type from the self::$postTypes array.
	 *
	 * @since 2.3.0
	 * @param $postType
	 * @return array
	 */
	static function getPostTypeInformation($postType)
	{
		if (isset(self::$postTypes[$postType]))
		{
			return self::$postTypes[$postType];
		}

		return array();
	}

	/**
	 * This function is not to be called by anything but WordPress' save_post action callback.
	 *
	 * @since 2.3.0
	 * @param $postID
	 * @return $postID
	 */
	static function save($postID)
	{
		$postType           = get_post_type($postID);
		$postTypeObject     = get_post_type_object($postType);
		$requiredCapability = $postTypeObject instanceof stdClass ? $postTypeObject->cap->publish_posts : null;

		// Verify nonce, check if user has sufficient rights and return on auto-save.
		if (!isset(self::$postTypes[$postType]) ||
			(!isset($_POST[self::$postTypes[$postType]['nonceName']]) || !wp_verify_nonce($_POST[self::$postTypes[$postType]['nonceName']], self::$postTypes[$postType]['nonceAction'])) ||
			!current_user_can($requiredCapability, $postID) ||
			(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE))
		{
			return $postID;
		}

		$reflectionClass = new ReflectionClass(self::$postTypes[$postType]['class']);

		$model = $reflectionClass->newInstanceArgs(array($postID));

		foreach ($_POST as $postKey => $postVariable)
		{
			if (isset(self::$postTypes[$postType]['postMeta'][$postKey]))
			{
				$model->setPostMeta($postKey, $postVariable);
			}
		}

		$model->save();

		return $postID;
	}

	/**
	 * Changes the "Post published/updated" message to a "[Post Type] created/updated" message without the link to a
	 * frontend page.
	 *
	 * @since 2.3.0
	 * @param mixed $messages
	 * @return mixed $messages
	 */
	static function alterCRUDMessages($messages)
	{
		$postTypeObject = get_post_type_object(SlideshowPluginMain::getCurrentPostType());

		if (!($postTypeObject instanceof stdClass) ||
			!isset(self::$postTypes[$postTypeObject->name]))
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
				$messages['post'][$messageID] = $postTypeObject->labels->singular_name . ' ' . __('created', 'slideshow-plugin');
				break;

			default:
				$messages['post'][$messageID] = $postTypeObject->labels->singular_name . ' ' . __('updated', 'slideshow-plugin');
		}

		return $messages;
	}

	/**
	 * Hooked on the post_row_actions filter, adds a "duplicate" action to each post on the post's overview page.
	 *
	 * @since 2.2.20
	 * @param array $actions
	 * @param WP_Post $post
	 * @return array $actions
	 */
	static function duplicateActionLink($actions, $post)
	{
		$postType           = SlideshowPluginMain::getCurrentPostType();
		$postTypeObject     = get_post_type_object(SlideshowPluginMain::getCurrentPostType());
		$requiredCapability = $postTypeObject instanceof stdClass ? $postTypeObject->cap->publish_posts : null;

		if (current_user_can($requiredCapability) &&
			isset(self::$postTypes[$postType]))
		{
			$url = add_query_arg(array(
				'action' => self::$duplicateAction,
				'post'   => $post->ID,
			));

			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-post_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-plugin') . '</a>';
		}

		return $actions;
	}

	/**
	 * Checks if a "duplicate" post action was performed and whether or not the current user has the permission to
	 * perform this action at all.
	 *
	 * @since 2.2.20
	 */
	static function duplicate()
	{
		$nonce              = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
		$postType           = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
		$postID             = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
		$errorRedirectURL   = remove_query_arg(array('action', 'post', 'nonce'));
		$postTypeObject     = get_post_type_object($postType);
		$requiredCapability = $postTypeObject instanceof stdClass ? $postTypeObject->cap->publish_posts : null;

		// Check if nonce is correct and user has the correct privileges
		if (!wp_verify_nonce($nonce, 'duplicate-post_' . $postID) ||
			!current_user_can($requiredCapability) ||
			!isset(self::$postTypes[$postType]))
		{
			wp_redirect($errorRedirectURL);

			die();
		}

		$post = get_post($postID);

		// Check if the post was retrieved successfully
		if (!$post instanceof WP_Post ||
			$post->post_type !== $postType)
		{
			wp_redirect($errorRedirectURL);

			die();
		}

		$current_user = wp_get_current_user();

		// Create post duplicate
		$newPostID = wp_insert_post(array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $current_user->ID,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . (strlen($post->post_title) > 0 ? ' - ' : '') . __('Copy', 'slideshow-plugin'),
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		));

		if ($newPostID <= 0)
		{
			wp_redirect($errorRedirectURL);

			die();
		}

		// Get all taxonomies
		$taxonomies = get_object_taxonomies($post->post_type);

		// Add taxonomies to new post
		foreach ($taxonomies as $taxonomy)
		{
			$postTerms = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'slugs'));

			wp_set_object_terms($newPostID, $postTerms, $taxonomy, false);
		}

		// Get all post meta
		$postMetaRecords = get_post_meta($post->ID);

		// Add post meta records to new post
		foreach ($postMetaRecords as $postMetaKey => $postMetaValues)
		{
			foreach ($postMetaValues as $postMetaValue)
			{
				update_post_meta($newPostID, $postMetaKey, maybe_unserialize($postMetaValue));
			}
		}

		wp_redirect(admin_url('post.php?action=edit&post=' . $newPostID));

		die();
	}

//	/** @var WP_Post */
//	public $post = null;
//
//	/** @var string $postType */
//	static $postType = null;
//
//	/** @var bool */
//	static $isDuplicatable = true;
//
//	// TODO Move to subclass
//	/** @var string */
//	static $duplicateAction = null;
//
//	// TODO Move these keys to the settings profile sub class
//	/** @var string */
//	static $settingsProfilePostMetaKey = '_slideshow_jquery_image_gallery_slideshow_settings_profile';
//	/** @var string @var string */
//	static $stylePostMetaKey = '_slideshow_jquery_image_gallery_slideshow_settings_profile';
//
//	/** @var array */
//	public $postMeta = array();
//
//	/**
//	 * Initialize Slideshow post type.
//	 * Called on load of plugin
//	 *
//	 * @since 1.3.0
//	 */
//	static function init($postType, $isDuplicatable)
//	{
//		self::$duplicateAction = 'slideshow_jquery_image_gallery_duplicate_' . self::$postType;
//
//		add_action('init'                , array(__CLASS__, 'registerSlideshowPostType'));
////		add_action('save_post'           , array('SlideshowPluginSlideshowSettingsHandler', 'save')); TODO Implement save method
//		add_action(self::$duplicateAction, array(__CLASS__, 'duplicate'), 11);
//
//		add_filter('post_updated_messages', array(__CLASS__, 'alterCRUDMessages'));
//
//		if (self::$isDuplicatable)
//		{
//			add_filter('post_row_actions', array(__CLASS__, 'duplicateActionLink'), 10, 2);
//		}
//	}
//
//	/**
//	 * @since 2.3.0
//	 */
//	static function registerPostType()
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
//
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
//
//	/**
//	 * Changes the "Post published/updated" message to a "[Post Type] created/updated" message without the link to a
//	 * frontend page.
//	 *
//	 * @since 2.3.0
//	 * @param mixed $messages
//	 * @return mixed $messages
//	 */
//	static function alterCRUDMessages($messages)
//	{
//		if (!function_exists('get_current_screen'))
//		{
//			return $messages;
//		}
//
//		$currentScreen = get_current_screen();
//
//		// Return when not on the right edit page
//		if ($currentScreen->post_type != self::$postType)
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
//		$postTypeObject = get_post_type_object(self::$postType);
//
//		if (!($postTypeObject instanceof stdClass))
//		{
//			return $messages;
//		}
//
//		switch ($messageID)
//		{
//			case 6:
//				$messages[$currentScreen->base][$messageID] = $postTypeObject->labels->name . __('created', 'slideshow-plugin');
//				break;
//
//			default:
//				$messages[$currentScreen->base][$messageID] = $postTypeObject->labels->name . __('updated', 'slideshow-plugin');
//		}
//
//		return $messages;
//	}
//
//	/**
//	 * Hooked on the post_row_actions filter, adds a "duplicate" action to each post on the post's overview page.
//	 *
//	 * @since 2.2.20
//	 * @param array $actions
//	 * @param WP_Post $post
//	 * @return array $actions
//	 */
//	static function duplicateActionLink($actions, $post)
//	{
//		$postTypeObject     = get_post_type_object(SlideshowPluginMain::getCurrentPostType());
//		$requiredCapability = $postTypeObject instanceof stdClass ? $postTypeObject->cap->publish_posts : null;
//
//		if (current_user_can($requiredCapability) &&
//			$post->post_type === self::$postType)
//		{
//			$url = add_query_arg(array(
//				'action' => self::$duplicateAction,
//				'post'   => $post->ID,
//			));
//
//			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-post_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-plugin') . '</a>';
//		}
//
//		return $actions;
//	}
//
//	/**
//	 * Checks if a "duplicate" post action was performed and whether or not the current user has the permission to
//	 * perform this action at all.
//	 *
//	 * @since 2.2.20
//	 */
//	static function duplicate()
//	{
//		$nonce              = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
//		$postType           = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
//		$postID             = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
//		$errorRedirectURL   = remove_query_arg(array('action', 'post', 'nonce'));
//		$postTypeObject     = get_post_type_object($postType);
//		$requiredCapability = $postTypeObject instanceof stdClass ? $postTypeObject->cap->publish_posts : null;
//
//		// Check if nonce is correct and user has the correct privileges
//		if (!wp_verify_nonce($nonce, 'duplicate-post_' . $postID) ||
//			!current_user_can($requiredCapability) ||
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