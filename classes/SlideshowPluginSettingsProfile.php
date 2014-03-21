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
//				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
			)
		);
	}
}