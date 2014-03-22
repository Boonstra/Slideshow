<?php
/**
 * @since 2.3.0
 * @author Stefan Boonstra
 */
class SlideshowPluginSettingsProfile
{
	/** @var string */
	static $postType = 'slideshow_sett_prof';

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
			__('Slideshow Settings', 'slideshow-plugin'),
			array(__CLASS__, 'settingsMetaBox'),
			self::$postType,
			'normal',
			'default'
		);

		// Add support plugin message on edit slideshow
		if (isset($_GET['action']) &&
			strtolower($_GET['action']) == strtolower('edit'))
		{
			add_action('admin_notices', array(__CLASS__,  'supportPluginMessage'));
		}
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
	 * Shows the support plugin message
	 *
	 * @since 2.3.0
	 */
	static function supportPluginMessage()
	{
		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/support-plugin.php';
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
//		global $post;
//
//		// Nonce
//		wp_nonce_field(SlideshowPluginSlideshowSettingsHandler::$nonceAction, SlideshowPluginSlideshowSettingsHandler::$nonceName);
//
//		// Get settings
//		$settings = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID, true);
//
//		// Include
//		include SlideshowPluginMain::getPluginPath() . '/views/' . __CLASS__ . '/settings.php';
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