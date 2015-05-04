<?php
/**
 * @since 2.3.0
 * @author Stefan Boonstra
 */
class SlideshowPluginSettingsProfile extends SlideshowPluginModel
{
	/** @var string */
	static $postType = 'slideshow_sett_prof';

	/** @var string */
	static $settingsPostMetaKey = '_slideshow_jquery_image_gallery_settings';

	/** @var array */
	static $postMetaDefaults = array(
		'_slideshow_jquery_image_gallery_settings' => array(
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
			'avoidFilter'                 => 'true',
		)
	);

	/** @var array */
	protected static $settingsDefinitions = array();

	/**
	 * Registers class with the slideshow's post type class.
	 */
	static function init()
	{
		SlideshowPluginPostType::registerPostType(
			__CLASS__,
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
					'edit_item'          => __('Edit settings profile', 'slideshow-plugin'),
					'view_item'          => __('View settings profile', 'slideshow-plugin'),
					'all_items'          => __('All Settings Profiles', 'slideshow-plugin'),
					'search_items'       => __('Search Settings Profiles', 'slideshow-plugin'),
					'parent_item_colon'  => __('Parent Settings Profiles:', 'slideshow-plugin'),
					'not_found'          => __('No settings profiles found', 'slideshow-plugin'),
					'not_found_in_trash' => __('No settings profiles found', 'slideshow-plugin')
				),
				'public'               => false,
				'publicly_queryable'   => false,
				'show_ui'              => true,
				'show_in_menu'         => 'edit.php?post_type=' . SlideshowPluginSlideshow::$postType,
				'query_var'            => true,
				'rewrite'              => true,
				'capability_type'      => 'post',
				'capabilities'         => array(
					'edit_post'              => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'],
					'read_post'              => SlideshowPluginGeneralSettings::$capabilities['addSettingsProfiles'],
					'delete_post'            => SlideshowPluginGeneralSettings::$capabilities['deleteSettingsProfiles'],
					'edit_posts'             => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'],
					'edit_others_posts'      => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'],
					'publish_posts'          => SlideshowPluginGeneralSettings::$capabilities['addSettingsProfiles'],
					'read_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'],

					'read'                   => SlideshowPluginGeneralSettings::$capabilities['addSettingsProfiles'],
					'delete_posts'           => SlideshowPluginGeneralSettings::$capabilities['deleteSettingsProfiles'],
					'delete_private_posts'   => SlideshowPluginGeneralSettings::$capabilities['deleteSettingsProfiles'],
					'delete_published_posts' => SlideshowPluginGeneralSettings::$capabilities['deleteSettingsProfiles'],
					'delete_others_posts'    => SlideshowPluginGeneralSettings::$capabilities['deleteSettingsProfiles'],
					'edit_private_posts'     => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'],
					'edit_published_posts'   => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'],
				),
				'has_archive'          => true,
				'hierarchical'         => false,
				'menu_position'        => null,
				'supports'             => array('title'),
			),
			array(
				'_slideshow_jquery_image_gallery_settings' => array(
					'dataType'      => 'array',
					'title'         => __('Settings', 'slideshow-plugin'),
					'callback'      => array(__CLASS__, 'settingsMetaBox'),
					'screen'        => self::$postType,
					'context'       => 'normal',
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
		if (isset(self::$postMetaDefaults[$key]))
		{
			return self::$postMetaDefaults[$key];
		}

		return null;
	}

	/**
	 * @since 2.3.0
	 * @return mixed
	 */
	function getSettings()
	{
		return $this->getPostMeta('_slideshow_jquery_image_gallery_settings');
	}

	/**
	 * Returns an array of definitions of the settings array.
	 *
	 * The definition array is built up as follows:
	 * array([settingsKey] => array([settingName] => array('type' => [inputType], 'value' => [value], 'default' => [default], 'description' => [description], 'options' => array([options]), 'dependsOn' => array([dependsOn], [onValue]), 'group' => [groupName])))
	 *
	 * @since 2.3.0
	 * @return array $variableDefinitions
	 */
	static function getSettingsDefinitions()
	{
		if (count(self::$settingsDefinitions) > 0)
		{
			return self::$settingsDefinitions;
		}

		// Much used data for translation
		$yes = __('Yes', 'slideshow-plugin');
		$no  = __('No', 'slideshow-plugin');

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

		$settingsDefaults = self::$postMetaDefaults['_slideshow_jquery_image_gallery_settings'];

		$settingsDefinitions = array(
			'animation'                   => array('type' => 'select', 'default' => $settingsDefaults['animation']                  , 'description' => $descriptions['animation']                  , 'group' => __('Animation', 'slideshow-plugin')    , 'options' => array('slide' => __('Slide Left', 'slideshow-plugin'), 'slideRight' => __('Slide Right', 'slideshow-plugin'), 'slideUp' => __('Slide Up', 'slideshow-plugin'), 'slideDown' => __('Slide Down', 'slideshow-plugin'), 'crossFade' => __('Cross Fade', 'slideshow-plugin'), 'directFade' => __('Direct Fade', 'slideshow-plugin'), 'fade' => __('Fade', 'slideshow-plugin'), 'random' => __('Random Animation', 'slideshow-plugin'))),
			'slideSpeed'                  => array('type' => 'text'  , 'default' => $settingsDefaults['slideSpeed']                 , 'description' => $descriptions['slideSpeed']                 , 'group' => __('Animation', 'slideshow-plugin')),
			'descriptionSpeed'            => array('type' => 'text'  , 'default' => $settingsDefaults['descriptionSpeed']           , 'description' => $descriptions['descriptionSpeed']           , 'group' => __('Animation', 'slideshow-plugin')),
			'intervalSpeed'               => array('type' => 'text'  , 'default' => $settingsDefaults['intervalSpeed']              , 'description' => $descriptions['intervalSpeed']              , 'group' => __('Animation', 'slideshow-plugin')),
			'slidesPerView'               => array('type' => 'text'  , 'default' => $settingsDefaults['slidesPerView']              , 'description' => $descriptions['slidesPerView']              , 'group' => __('Display', 'slideshow-plugin')),
			'maxWidth'                    => array('type' => 'text'  , 'default' => $settingsDefaults['maxWidth']                   , 'description' => $descriptions['maxWidth']                   , 'group' => __('Display', 'slideshow-plugin')),
			'aspectRatio'                 => array('type' => 'text'  , 'default' => $settingsDefaults['aspectRatio']                , 'description' => $descriptions['aspectRatio']                , 'group' => __('Display', 'slideshow-plugin')                                                           , 'dependsOn' => array('settings[preserveSlideshowDimensions]', 'true')),
			'height'                      => array('type' => 'text'  , 'default' => $settingsDefaults['height']                     , 'description' => $descriptions['height']                     , 'group' => __('Display', 'slideshow-plugin')                                                           , 'dependsOn' => array('settings[preserveSlideshowDimensions]', 'false')),
			'imageBehaviour'              => array('type' => 'select', 'default' => $settingsDefaults['imageBehaviour']             , 'description' => $descriptions['imageBehaviour']             , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('natural' => __('Natural and centered', 'slideshow-plugin'), 'crop' => __('Crop to fit', 'slideshow-plugin'), 'stretch' => __('Stretch to fit', 'slideshow-plugin'))),
			'preserveSlideshowDimensions' => array('type' => 'radio' , 'default' => $settingsDefaults['preserveSlideshowDimensions'], 'description' => $descriptions['preserveSlideshowDimensions'], 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[enableResponsiveness]', 'true')),
			'enableResponsiveness'        => array('type' => 'radio' , 'default' => $settingsDefaults['enableResponsiveness']       , 'description' => $descriptions['enableResponsiveness']       , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'showDescription'             => array('type' => 'radio' , 'default' => $settingsDefaults['showDescription']            , 'description' => $descriptions['showDescription']            , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'hideDescription'             => array('type' => 'radio' , 'default' => $settingsDefaults['hideDescription']            , 'description' => $descriptions['hideDescription']            , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[showDescription]', 'true')),
			'play'                        => array('type' => 'radio' , 'default' => $settingsDefaults['play']                       , 'description' => $descriptions['play']                       , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'loop'                        => array('type' => 'radio' , 'default' => $settingsDefaults['loop']                       , 'description' => $descriptions['loop']                       , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'pauseOnHover'                => array('type' => 'radio' , 'default' => $settingsDefaults['loop']                       , 'description' => $descriptions['pauseOnHover']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'controllable'                => array('type' => 'radio' , 'default' => $settingsDefaults['controllable']               , 'description' => $descriptions['controllable']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'hideNavigationButtons'       => array('type' => 'radio' , 'default' => $settingsDefaults['hideNavigationButtons']      , 'description' => $descriptions['hideNavigationButtons']      , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[controllable]', 'true')),
			'showPagination'              => array('type' => 'radio' , 'default' => $settingsDefaults['showPagination']             , 'description' => $descriptions['showPagination']             , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'hidePagination'              => array('type' => 'radio' , 'default' => $settingsDefaults['hidePagination']             , 'description' => $descriptions['hidePagination']             , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[showPagination]', 'true')),
			'controlPanel'                => array('type' => 'radio' , 'default' => $settingsDefaults['controlPanel']               , 'description' => $descriptions['controlPanel']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
			'hideControlPanel'            => array('type' => 'radio' , 'default' => $settingsDefaults['hideControlPanel']           , 'description' => $descriptions['hideControlPanel']           , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[controlPanel]', 'true')),
			'waitUntilLoaded'             => array('type' => 'radio' , 'default' => $settingsDefaults['waitUntilLoaded']            , 'description' => $descriptions['waitUntilLoaded']            , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no)),
			'showLoadingIcon'             => array('type' => 'radio' , 'default' => $settingsDefaults['showLoadingIcon']            , 'description' => $descriptions['showLoadingIcon']            , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[waitUntilLoaded]', 'true')),
			'random'                      => array('type' => 'radio' , 'default' => $settingsDefaults['random']                     , 'description' => $descriptions['random']                     , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no)),
			'avoidFilter'                 => array('type' => 'radio' , 'default' => $settingsDefaults['avoidFilter']                , 'description' => $descriptions['avoidFilter']                , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no))
		);

		self::$settingsDefinitions = $settingsDefinitions;

		return $settingsDefinitions;
	}

	/**
	 * Shows the settings for the current settings profile.
	 *
	 * @since 2.3.0
	 */
	static function settingsMetaBox()
	{
		global $post;

		$postTypeInformation = SlideshowPluginPostType::getPostTypeInformation(self::$postType);

		wp_nonce_field($postTypeInformation['nonceAction'], $postTypeInformation['nonceName']);

		$data                  = new stdClass();
		$data->settingsProfile = new SlideshowPluginSettingsProfile($post);

		SlideshowPluginMain::outputView(__CLASS__ . '/settings.php', $data);
	}
}