<?php
/**
 * @since 2.3.0
 * @author Stefan Boonstra
 */
class SlideshowPluginStyle
{
	/** @var WP_Post */
	public $post = null;

	/** @var string */
	static $postType = 'slideshow_style';

	/** @var string */
	static $duplicateAction = 'slideshow_jquery_image_gallery_duplicate_style';

	/**
	 * Constructs the style. The $post parameter can either contain a WP_Post instance or an ID to a style.
	 *
	 * Constructing a style costs no database queries if a WP_Post object is passed. When an ID is passed, a
	 * single query is run to get the WP_Post object belonging to that ID.
	 *
	 * @since 2.3.0
	 * @param WP_Post|int $post (Optional, defaults to null)
	 */
	function __construct($post = null)
	{
		if ($post instanceof WP_Post &&
			$post->post_type === self::$postType)
		{
			$this->post = $post;
		}
		else if (is_numeric($post))
		{
			$this->post = get_post($post);
		}

		// Return if the post variable was not set to an instance of WP_Post
		if (!($this->post instanceof WP_Post))
		{
			$this->post = new WP_Post(null);
		}
	}

	/**
	 * Saves the current style.
	 *
	 * If $savePost is set to true, this method will also update or insert the post in the $post variable. Whether it
	 * chooses to update or insert the post depends on whether or not the post has an ID.
	 *
	 * @since 2.3.0
	 * @param bool $savePost
	 * @return bool $success
	 */
	function save($savePost = false)
	{
		if ($savePost)
		{
			$isPostSaved = false;

			$postArray = array(
				'post_author'           => $this->post->post_author,
				'post_date'             => $this->post->post_date,
				'post_date_gmt'         => $this->post->post_date_gmt,
				'post_content'          => $this->post->post_content,
				'post_title'            => $this->post->post_title,
				'post_excerpt'          => $this->post->post_excerpt,
				'post_status'           => $this->post->post_status,
				'comment_status'        => $this->post->comment_status,
				'ping_status'           => $this->post->ping_status,
				'post_password'         => $this->post->post_password,
				'post_name'             => $this->post->post_name,
				'to_ping'               => $this->post->to_ping,
				'pinged'                => $this->post->pinged,
				'post_modified'         => $this->post->post_modified,
				'post_modified_gmt'     => $this->post->post_modified_gmt,
				'post_content_filtered' => $this->post->post_content_filtered,
				'post_parent'           => $this->post->post_parent,
				'guid'                  => $this->post->guid,
				'menu_order'            => $this->post->menu_order,
				'post_type'             => $this->post->post_type,
				'post_mime_type'        => $this->post->post_mime_type,
				'comment_count'         => $this->post->comment_count,
			);

			if (is_numeric($this->post->ID) && $this->post->ID > 0)
			{
				$postArray['ID'] = $this->post->ID;
			}

			$postID = wp_insert_post($postArray);

			if ($postID > 0)
			{
				$isPostSaved = true;

				$this->post->ID = $postID;
			}
		}
		else
		{
			$isPostSaved = true;
		}

		return $isPostSaved;
	}

	/**
	 * Returns all styles within the defined offset and limit.
	 *
	 * @since 2.3.0
	 * @param int $offset (Optional, defaults to -1)
	 * @param int $limit  (Optional, defaults to -1)
	 * @return array
	 */
	static function getAll($offset = -1, $limit = -1)
	{
		if (!is_numeric($offset) ||
			$offset < 0)
		{
			$offset = -1;
		}

		if (!is_numeric($limit) ||
			$limit < 0)
		{
			$limit = -1;
		}

		$query = new WP_Query(array(
			'post_type'      => self::$postType,
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'offset'         => $offset,
			'posts_per_page' => $limit,
		));

		$styles = array();

		foreach ($query->get_posts() as $post)
		{
			$styles[] = new SlideshowPluginStyle($post);
		};

		return $styles;
	}

	/**
	 * Initialize style post type.
	 *
	 * @since 2.3.0
	 */
	static function init()
	{
		add_action('init', array(__CLASS__, 'registerPostType'));

		add_action('admin_action_' . self::$duplicateAction, array(__CLASS__, 'duplicate'), 11);

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
		// TODO Use this code in the installer class to convert current custom styles to the new styles
//		var_dump(SlideshowPluginGeneralSettings::getStylesheets(true));
//
//		$customStyleValues = array();
//		$customStyleKeys   = get_option(SlideshowPluginGeneralSettings::$customStyles, array());
//
//		if (is_array($customStyleKeys))
//		{
//			foreach ($customStyleKeys as $customStyleKey => $customStyleKeyName)
//			{
//				// Get custom style value from custom style key
//				$customStyleValues[$customStyleKey] = get_option($customStyleKey);
//			}
//		}
//
//		var_dump($customStyleKeys, $customStyleValues);

		register_post_type(
			self::$postType,
			array(
				'labels'               => array(
					'name'               => __('Styles', 'slideshow-plugin'),
					'singular_name'      => __('Style', 'slideshow-plugin'),
					'menu_name'          => __('Style', 'slideshow-plugin'),
					'name_admin_bar'     => __('Style', 'slideshow-plugin'),
					'add_new'            => __('Add New', 'slideshow-plugin'),
					'add_new_item'       => __('Add New Style', 'slideshow-plugin'),
					'new_item'           => __('New Style', 'slideshow-plugin'),
					'edit_item'          => __('Edit style', 'slideshow-plugin'),
					'view_item'          => __('View style', 'slideshow-plugin'),
					'all_items'          => __('All Styles', 'slideshow-plugin'),
					'search_items'       => __('Search Styles', 'slideshow-plugin'),
					'parent_item_colon'  => __('Parent Styles:', 'slideshow-plugin'),
					'not_found'          => __('No styles found', 'slideshow-plugin'),
					'not_found_in_trash' => __('No styles found', 'slideshow-plugin')
				),
				'public'               => false,
				'publicly_queryable'   => false,
				'show_ui'              => true,
				'show_in_menu'         => 'edit.php?post_type=' . SlideshowPluginSlideshow::$postType,
				'query_var'            => true,
				'rewrite'              => true,
				'capability_type'      => 'post',
				'capabilities'         => array(
					'edit_post'              => SlideshowPluginGeneralSettings::$capabilities['editStyles'],
					'read_post'              => SlideshowPluginGeneralSettings::$capabilities['addStyles'],
					'delete_post'            => SlideshowPluginGeneralSettings::$capabilities['deleteStyles'],
					'edit_posts'             => SlideshowPluginGeneralSettings::$capabilities['editStyles'],
					'edit_others_posts'      => SlideshowPluginGeneralSettings::$capabilities['editStyles'],
					'publish_posts'          => SlideshowPluginGeneralSettings::$capabilities['addStyles'],
					'read_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editStyles'],

					'read'                   => SlideshowPluginGeneralSettings::$capabilities['addStyles'],
					'delete_posts'           => SlideshowPluginGeneralSettings::$capabilities['deleteStyles'],
					'delete_private_posts'   => SlideshowPluginGeneralSettings::$capabilities['deleteStyles'],
					'delete_published_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteStyles'],
					'delete_others_posts'    => SlideshowPluginGeneralSettings::$capabilities['deleteStyles'],
					'edit_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editStyles'],
					'edit_published_posts'   => SlideshowPluginGeneralSettings::$capabilities['editStyles'],
				),
				'has_archive'          => true,
				'hierarchical'         => false,
				'menu_position'        => null,
				'supports'             => array('title', 'editor'),
			)
		);
	}

	/**
	 * Changes the "Post published/updated" message to a "Style created/updated" message without the link to a
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

		// Return when not on a style edit page
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
				$messages[$currentScreen->base][$messageID] = __('Style created', 'slideshow-plugin');
				break;

			default:
				$messages[$currentScreen->base][$messageID] = __('Style updated', 'slideshow-plugin');
		}

		return $messages;
	}

	/**
	 * Hooked on the post_row_actions filter, add a "duplicate" action to each style on the styles overview page.
	 *
	 * @since 2.3.0
	 * @param array   $actions
	 * @param WP_Post $post
	 * @return array $actions
	 */
	static function duplicateActionLink($actions, $post)
	{
		if (current_user_can(SlideshowPluginGeneralSettings::$capabilities['addStyles']) &&
			$post->post_type === self::$postType)
		{
			$url = add_query_arg(array(
				'action' => self::$duplicateAction,
				'post'   => $post->ID,
			));

			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-style_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-plugin') . '</a>';
		}

		return $actions;
	}

	/**
	 * Checks if a "duplicate" style action was performed and whether or not the current user has the permission to
	 * perform this action at all.
	 *
	 * @since 2.3.0
	 */
	static function duplicate()
	{
		$postID           = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
		$nonce            = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
		$postType         = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
		$errorRedirectURL = remove_query_arg(array('action', 'post', 'nonce'));

		// Check if nonce is correct and user has the correct privileges
		if (!wp_verify_nonce($nonce, 'duplicate-style_' . $postID) ||
			!current_user_can(SlideshowPluginGeneralSettings::$capabilities['addStyles']) ||
			$postType !== self::$postType)
		{
			wp_redirect($errorRedirectURL);

			die();
		}

		$post = get_post($postID);

		// Check if the post was retrieved successfully
		if (!$post instanceof WP_Post ||
			$post->post_type !== self::$postType)
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
}