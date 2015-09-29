<?php
/**
 * SlideshowPluginPostType creates a post type specifically designed for
 * slideshows and their individual settings
 *
 * @since 1.0.0
 * @author: Stefan Boonstra
 */
class SlideshowPluginPostType
{
	/** @var string $postType */
	static $postType = 'slideshow';

	/**
	 * Initialize Slideshow post type.
	 * Called on load of plugin
	 *
	 * @since 1.3.0
	 */
	static function init()
	{
		add_action('init'                 , array(__CLASS__, 'registerSlideshowPostType'));
		add_action('save_post'            , array('SlideshowPluginSlideshowSettingsHandler', 'save'));
		add_action('admin_enqueue_scripts', array('SlideshowPluginSlideInserter', 'localizeScript'), 11);

		add_action('admin_action_slideshow_jquery_image_gallery_duplicate_slideshow', array(__CLASS__, 'duplicateSlideshow'), 11);

		add_filter('post_updated_messages', array(__CLASS__, 'alterSlideshowMessages'));
		add_filter('post_row_actions'     , array(__CLASS__, 'duplicateSlideshowActionLink'), 10, 2);
	}

	/**
	 * Registers new post type slideshow
	 *
	 * @since 1.0.0
	 */
	static function registerSlideshowPostType()
	{
		global $wp_version;

		register_post_type(
			self::$postType,
			array(
				'labels'               => array(
					'name'               => __('Slideshows', 'slideshow-jquery-image-gallery'),
					'singular_name'      => __('Slideshow', 'slideshow-jquery-image-gallery'),
					'add_new_item'       => __('Add New Slideshow', 'slideshow-jquery-image-gallery'),
					'edit_item'          => __('Edit slideshow', 'slideshow-jquery-image-gallery'),
					'new_item'           => __('New slideshow', 'slideshow-jquery-image-gallery'),
					'view_item'          => __('View slideshow', 'slideshow-jquery-image-gallery'),
					'search_items'       => __('Search slideshows', 'slideshow-jquery-image-gallery'),
					'not_found'          => __('No slideshows found', 'slideshow-jquery-image-gallery'),
					'not_found_in_trash' => __('No slideshows found', 'slideshow-jquery-image-gallery')
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
				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
			)
		);
	}

	/**
	 * Adds custom meta boxes to slideshow post type.
	 *
	 * @since 1.0.0
	 */
	static function registerMetaBoxes()
	{
		add_meta_box(
			'information',
			__('Information', 'slideshow-jquery-image-gallery'),
			array(__CLASS__, 'informationMetaBox'),
			self::$postType,
			'normal',
			'high'
		);

		add_meta_box(
			'slides-list',
			__('Slides List', 'slideshow-jquery-image-gallery'),
			array(__CLASS__, 'slidesMetaBox'),
			self::$postType,
			'side',
			'default'
		);

		add_meta_box(
			'style',
			__('Slideshow Style', 'slideshow-jquery-image-gallery'),
			array(__CLASS__, 'styleMetaBox'),
			self::$postType,
			'normal',
			'low'
		);

		add_meta_box(
			'settings',
			__('Slideshow Settings', 'slideshow-jquery-image-gallery'),
			array(__CLASS__, 'settingsMetaBox'),
			self::$postType,
			'normal',
			'low'
		);

		// Add support plugin message on edit slideshow
		if (isset($_GET['action']) &&
			strtolower($_GET['action']) == strtolower('edit'))
		{
			add_action('admin_notices', array(__CLASS__,  'supportPluginMessage'));
		}
	}

	/**
	 * Changes the "Post published/updated" message to a "Slideshow created/updated" message without the link to a
	 * frontend page.
	 *
	 * @since 2.2.20
	 * @param mixed $messages
	 * @return mixed $messages
	 */
	static function alterSlideshowMessages($messages)
	{
		if (!function_exists('get_current_screen'))
		{
			return $messages;
		}

		$currentScreen = get_current_screen();

		// Return when not on a slideshow edit page
		if ($currentScreen->post_type != SlideshowPluginPostType::$postType)
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
				$messages[$currentScreen->base][$messageID] = __('Slideshow created', 'slideshow-jquery-image-gallery');
				break;

			default:
				$messages[$currentScreen->base][$messageID] = __('Slideshow updated', 'slideshow-jquery-image-gallery');
		}

		return $messages;
	}

	/**
	 * Shows the support plugin message
	 *
	 * @since 2.0.0
	 */
	static function supportPluginMessage()
	{
		SlideshowPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'support-plugin.php');
	}

	/**
	 * Shows some information about this slideshow
	 *
	 * @since 1.0.0
	 */
	static function informationMetaBox()
	{
		global $post;

		$data            = new stdClass();
		$data->snippet   = htmlentities(sprintf('<?php do_action(\'slideshow_deploy\', \'%s\'); ?>', $post->ID));
		$data->shortCode = htmlentities(sprintf('[' . SlideshowPluginShortcode::$shortCode . ' id=\'%s\']', $post->ID));

		SlideshowPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'information.php', $data);
	}

	/**
	 * Shows slides currently in slideshow
	 *
	 * @since 1.0.0
	 */
	static function slidesMetaBox()
	{
		global $post;

		$data         = new stdClass();
		$data->slides = SlideshowPluginSlideshowSettingsHandler::getSlides($post->ID);

		SlideshowPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'slides.php', $data);
	}

	/**
	 * Shows style used for slideshow
	 *
	 * @since 1.3.0
	 */
	static function styleMetaBox()
	{
		global $post;

		$data           = new stdClass();
		$data->settings = SlideshowPluginSlideshowSettingsHandler::getStyleSettings($post->ID, true);

		SlideshowPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'style-settings.php', $data);
	}

	/**
	 * Shows settings for particular slideshow
	 *
	 * @since 1.0.0
	 */
	static function settingsMetaBox()
	{
		global $post;

		// Nonce
		wp_nonce_field(SlideshowPluginSlideshowSettingsHandler::$nonceAction, SlideshowPluginSlideshowSettingsHandler::$nonceName);

		$data           = new stdClass();
		$data->settings = SlideshowPluginSlideshowSettingsHandler::getSettings($post->ID, true);

		SlideshowPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'settings.php', $data);
	}

	/**
	 * Hooked on the post_row_actions filter, adds a "duplicate" action to each slideshow on the slideshow's overview
	 * page.
	 *
	 * @param array $actions
	 * @param WP_Post $post
	 * @return array $actions
	 */
	static function duplicateSlideshowActionLink($actions, $post)
	{
		if (current_user_can('slideshow-jquery-image-gallery-add-slideshows') &&
			$post->post_type === self::$postType)
		{
			$url = add_query_arg(array(
				'action' => 'slideshow_jquery_image_gallery_duplicate_slideshow',
				'post'   => $post->ID,
			));

			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-slideshow_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-jquery-image-gallery') . '</a>';
		}

		return $actions;
	}

	/**
	 * Checks if a "duplicate" slideshow action was performed and whether or not the current user has the permission to
	 * perform this action at all.
	 */
	static function duplicateSlideshow()
	{
		$postID           = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
		$nonce            = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
		$postType         = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
		$errorRedirectURL = remove_query_arg(array('action', 'post', 'nonce'));

		// Check if nonce is correct and user has the correct privileges
		if (!wp_verify_nonce($nonce, 'duplicate-slideshow_' . $postID) ||
			!current_user_can('slideshow-jquery-image-gallery-add-slideshows') ||
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
			'post_title'     => $post->post_title . (strlen($post->post_title) > 0 ? ' - ' : '') . __('Copy', 'slideshow-jquery-image-gallery'),
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		));

		if (is_wp_error($newPostID))
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