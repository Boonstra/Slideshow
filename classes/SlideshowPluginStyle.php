<?php
/**
 * @since 2.3.0
 * @author Stefan Boonstra
 */
class SlideshowPluginStyle extends SlideshowPluginModel
{
	/** @var string */
	static $postType = 'slideshow_style';

	/**
	 * Registers class with the slideshow's post type class.
	 */
	static function init()
	{
		add_action('wp_trash_post'     , array(__CLASS__, 'beforeDeletePost'), 1);
		add_action('before_delete_post', array(__CLASS__, 'beforeDeletePost'), 1);

		SlideshowPluginPostType::registerPostType(
			__CLASS__,
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
			),
			array(
				'_slideshow_jquery_image_gallery_parent_slideshows' => array(
					'dataType'      => null,
					'title'         => __('Slideshows', 'slideshow-plugin'),
					'callback'      => array(__CLASS__, 'parentSlideshowsMetaBox'),
					'screen'        => self::$postType,
					'context'       => 'side',
					'priority'      => 'default',
					'callback_args' => null,
				),
			)
		);
	}

	/**
	 * @since 2.3.0
	 * @see SlideshowPluginModel::__construct
	 * @param int|null|WP_Post $post
	 */
	function __construct($post)
	{
		$this->modelPostType = self::$postType;

		parent::__construct($post);
	}

	/**
	 * @since 2.3.0
	 * @see SlideshowPluginModel::getPostMetaDefaults
	 */
	function getPostMetaDefaults($key)
	{
		return null;
	}

	/**
	 * Shows the parent slideshows of this settings profile.
	 *
	 * @since 2.3.0
	 */
	static function parentSlideshowsMetaBox()
	{
		global $post;

		$data = new stdClass();

		// Get parent slideshows
		$data->slideshows = SlideshowPluginModel::getAll(SlideshowPluginSlideshow::$postType, array(
			'meta_key'   => SlideshowPluginSlideshow::STYLE_POST_META_KEY,
			'meta_value' => $post->ID,
		));

		// Localizations
		$data->localizations = array(
			'parent-slideshows'    => __('This style is used by the following slideshows', 'slideshow-plugin') . ':',
			'no-parent-slideshows' => __('This style is not used by any slideshows.', 'slideshow-plugin'),
		);

		SlideshowPluginMain::outputView('parent-slideshows.php', $data);
	}

	/**
	 * Called upon the WordPress 'before_delete_post' hook. Checks if the passed settings profile can be deleted without
	 * any slideshows being affected.
	 *
	 * @param int $postID
	 */
	static function beforeDeletePost($postID)
	{
		$postType = SlideshowPluginMain::getCurrentPostType();

		if ($postType != self::$postType)
		{
			return;
		}

		// Get all slideshows that use the settings profile with the passed $postID
		$slideshows = SlideshowPluginModel::getAll(SlideshowPluginSlideshow::$postType, array(
			'meta_key'   => SlideshowPluginSlideshow::SETTINGS_PROFILE_POST_META_KEY,
			'meta_value' => $postID,
		));

		if (count($slideshows) > 0)
		{
			wp_redirect(add_query_arg(
				array(
					'slideshow-jquery-image-gallery-message'      =>
						urlencode(__("This style cannot be deleted, as it's used by one or more slideshows", 'slideshow-plugin')),
					'slideshow-jquery-image-gallery-message-type' =>
						urlencode('error')
				),
				admin_url() . 'edit.php?post_type=' . self::$postType
			));

			die();
		}
	}
}