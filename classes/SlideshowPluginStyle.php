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
	static $nonceAction = 'slideshow-jquery-image-gallery-style-nonce-action';

	/** @var string */
	static $nonceName = 'slideshow-jquery-image-gallery-style-nonce-name';

	/** @var string */
	static $variablesPostMetaKey = '_slideshow_jquery_image_gallery_style_variables';

//	/** @var array */
//	protected static $variableDefaults = array(
//		'animation'                   => 'slide',
//		'slideSpeed'                  => '1',
//		'descriptionSpeed'            => '0.4',
//		'intervalSpeed'               => '5',
//		'slidesPerView'               => '1',
//		'maxWidth'                    => '0',
//		'aspectRatio'                 => '3:1',
//		'height'                      => '200',
//		'imageBehaviour'              => 'natural',
//		'showDescription'             => 'true',
//		'hideDescription'             => 'true',
//		'preserveSlideshowDimensions' => 'false',
//		'enableResponsiveness'        => 'true',
//		'play'                        => 'true',
//		'loop'                        => 'true',
//		'pauseOnHover'                => 'true',
//		'controllable'                => 'true',
//		'hideNavigationButtons'       => 'false',
//		'showPagination'              => 'true',
//		'hidePagination'              => 'true',
//		'controlPanel'                => 'false',
//		'hideControlPanel'            => 'true',
//		'waitUntilLoaded'             => 'true',
//		'showLoadingIcon'             => 'true',
//		'random'                      => 'false',
//		'avoidFilter'                 => 'true'
//	);
//
//	/** @var array Allows variable definitions to be cached */
//	protected static $variableDefinitions = array();
//
//	/** @var array */
//	public $variables = array();
//
//	/**
//	 * Constructs the settings profile. The $post parameter can either contain a WP_Post instance or an ID to a settings
//	 * profile.
//	 *
//	 * Constructing a settings profile costs no database queries if a WP_Post object is passed. When an ID is passed, a
//	 * single query is run to get the WP_Post object belonging to that ID. The object's variables are lazy loaded when
//	 * they're requested per the $this->getVariables method.
//	 *
//	 * @since 2.3.0
//	 * @param WP_Post|int $post (Optional, defaults to null)
//	 */
//	function __construct($post = null)
//	{
//		if ($post instanceof WP_Post &&
//			$post->post_type === self::$postType)
//		{
//			$this->post = $post;
//		}
//		else if (is_numeric($post))
//		{
//			$this->post = get_post($post);
//		}
//
//		// Return if the post variable was not set to an instance of WP_Post
//		if (!($this->post instanceof WP_Post))
//		{
//			$this->post = new WP_Post(null);
//		}
//	}
//
//	/**
//	 * Set a single variable with the passed variable by the passed key.
//	 *
//	 * @since 2.3.0
//	 * @param string $key
//	 * @param mixed  $value
//	 */
//	function setVariable($key, $value)
//	{
//		if (count($this->variables) <= 0)
//		{
//			$this->getVariables();
//		}
//
//		$this->variables[$key] = $value;
//	}
//
//	/**
//	 * Expects an array of variables to be passed. The associative variables array is merged with the passed array,
//	 * which will overwrite any existing keys.
//	 *
//	 * @since 2.3.0
//	 * @param array $newVariables
//	 */
//	function setVariables($newVariables)
//	{
//		if (!is_array($newVariables))
//		{
//			return;
//		}
//
//		if (count($this->variables) <= 0)
//		{
//			$this->getVariables();
//		}
//
//		$this->variables = array_merge($this->variables, $newVariables);
//	}
//
//	/**
//	 * Gets the object's variables either from the database or the object itself if the variables have already been set.
//	 *
//	 * @since 2.3.0
//	 * @return array
//	 */
//	function getVariables()
//	{
//		if (count($this->variables) > 0)
//		{
//			return $this->variables;
//		}
//
//		$variables = get_post_meta(
//			$this->post->ID,
//			self::$variablesPostMetaKey,
//			true
//		);
//
//		// The variables variable should always be an array
//		if (!$variables ||
//			!is_array($variables))
//		{
//			$variables = array();
//		}
//
//		$variables = array_merge(self::$variableDefaults, $variables);
//
//		$this->variables = $variables;
//
//		return $variables;
//	}
//
//	/**
//	 * Saves the current settings profile.
//	 *
//	 * If $savePost is set to true, this method will also update or insert the post in the $post variable. Whether it
//	 * chooses to update or insert the post depends on whether or not the post has an ID.
//	 *
//	 * @since 2.3.0
//	 * @param bool $savePost
//	 * @return bool $success
//	 */
//	function save($savePost = false)
//	{
//		if ($savePost)
//		{
//			$isPostSaved = false;
//
//			$postArray = array(
//				'post_author'           => $this->post->post_author,
//				'post_date'             => $this->post->post_date,
//				'post_date_gmt'         => $this->post->post_date_gmt,
//				'post_content'          => $this->post->post_content,
//				'post_title'            => $this->post->post_title,
//				'post_excerpt'          => $this->post->post_excerpt,
//				'post_status'           => $this->post->post_status,
//				'comment_status'        => $this->post->comment_status,
//				'ping_status'           => $this->post->ping_status,
//				'post_password'         => $this->post->post_password,
//				'post_name'             => $this->post->post_name,
//				'to_ping'               => $this->post->to_ping,
//				'pinged'                => $this->post->pinged,
//				'post_modified'         => $this->post->post_modified,
//				'post_modified_gmt'     => $this->post->post_modified_gmt,
//				'post_content_filtered' => $this->post->post_content_filtered,
//				'post_parent'           => $this->post->post_parent,
//				'guid'                  => $this->post->guid,
//				'menu_order'            => $this->post->menu_order,
//				'post_type'             => $this->post->post_type,
//				'post_mime_type'        => $this->post->post_mime_type,
//				'comment_count'         => $this->post->comment_count,
//			);
//
//			if (is_numeric($this->post->ID) && $this->post->ID > 0)
//			{
//				$postArray['ID'] = $this->post->ID;
//			}
//
//			$postID = wp_insert_post($postArray);
//
//			if ($postID > 0)
//			{
//				$isPostSaved = true;
//
//				$this->post->ID = $postID;
//			}
//		}
//		else
//		{
//			$isPostSaved = true;
//		}
//
//		$updatePostMetaResult = update_post_meta($this->post->ID, self::$variablesPostMetaKey, $this->getVariables());
//
//		// $updatePostMetaResult will be absolutely false on failure
//		$isPostMetaSaved = $updatePostMetaResult !== false;
//
//		return $isPostSaved && $isPostMetaSaved;
//	}
//
//	/**
//	 * Called whenever a settings profile is saved through the admin user interface.
//	 *
//	 * @since 2.3.0
//	 * @param int $postID
//	 * @return int $postID
//	 */
//	static function saveThroughEditor($postID)
//	{
//		// Verify nonce, check if user has sufficient rights and return on auto-save.
//		if (get_post_type($postID) != self::$postType ||
//			(!isset($_POST[self::$nonceName]) || !wp_verify_nonce($_POST[self::$nonceName], self::$nonceAction)) ||
//			!current_user_can(SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles'], $postID) ||
//			(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE))
//		{
//			return $postID;
//		}
//
//		$newVariables = array();
//
//		if (isset($_POST[self::$variablesPostMetaKey]) &&
//			is_array($_POST[self::$variablesPostMetaKey]))
//		{
//			$newVariables = $_POST[self::$variablesPostMetaKey];
//		}
//
//		$settingsProfile = new SlideshowPluginSettingsProfile($postID);
//		$settingsProfile->setVariables($newVariables);
//
//		$settingsProfile->save();
//
//		return $postID;
//	}
//
//	/**
//	 * Returns an array of definitions of the variables in the variables array.
//	 *
//	 * The definition array is built up as follows:
//	 * array([settingsKey] => array([settingName] => array('type' => [inputType], 'value' => [value], 'default' => [default], 'description' => [description], 'options' => array([options]), 'dependsOn' => array([dependsOn], [onValue]), 'group' => [groupName])))
//	 *
//	 * @since 2.3.0
//	 * @return array $variableDefinitions
//	 */
//	static function getVariableDefinitions()
//	{
//		if (count(self::$variableDefinitions) > 0)
//		{
//			return self::$variableDefinitions;
//		}
//
//		// Much used data for translation
//		$yes = __('Yes', 'slideshow-plugin');
//		$no  = __('No', 'slideshow-plugin');
//
//		$descriptions = array(
//			'animation'                   => __('Animation used for transition between slides', 'slideshow-plugin'),
//			'slideSpeed'                  => __('Number of seconds the slide takes to slide in', 'slideshow-plugin'),
//			'descriptionSpeed'            => __('Number of seconds the description takes to slide in', 'slideshow-plugin'),
//			'intervalSpeed'               => __('Seconds between changing slides', 'slideshow-plugin'),
//			'slidesPerView'               => __('Number of slides to fit into one slide', 'slideshow-plugin'),
//			'maxWidth'                    => __('Maximum width. When maximum width is 0, maximum width is ignored', 'slideshow-plugin'),
//			'aspectRatio'                 => sprintf('<a href="' . str_replace('%', '%%', __('http://en.wikipedia.org/wiki/Aspect_ratio_(image)', 'slideshow-plugin')) . '" title="' . __('More info', 'slideshow-plugin') . '" target="_blank">' . __('Proportional relationship%s between slideshow\'s width and height (width:height)', 'slideshow-plugin'), '</a>'),
//			'height'                      => __('Slideshow\'s height', 'slideshow-plugin'),
//			'imageBehaviour'              => __('Image behaviour', 'slideshow-plugin'),
//			'preserveSlideshowDimensions' => __('Shrink slideshow\'s height when width shrinks', 'slideshow-plugin'),
//			'enableResponsiveness'        => __('Enable responsiveness (Shrink slideshow\'s width when page\'s width shrinks)', 'slideshow-plugin'),
//			'showDescription'             => __('Show title and description', 'slideshow-plugin'),
//			'hideDescription'             => __('Hide description box, pop up when mouse hovers over', 'slideshow-plugin'),
//			'play'                        => __('Automatically slide to the next slide', 'slideshow-plugin'),
//			'loop'                        => __('Return to the beginning of the slideshow after last slide', 'slideshow-plugin'),
//			'pauseOnHover'                => __('Pause slideshow when mouse hovers over', 'slideshow-plugin'),
//			'controllable'                => __('Activate navigation buttons', 'slideshow-plugin'),
//			'hideNavigationButtons'       => __('Hide navigation buttons, show when mouse hovers over', 'slideshow-plugin'),
//			'showPagination'              => __('Activate pagination', 'slideshow-plugin'),
//			'hidePagination'              => __('Hide pagination, show when mouse hovers over', 'slideshow-plugin'),
//			'controlPanel'                => __('Activate control panel (play and pause button)', 'slideshow-plugin'),
//			'hideControlPanel'            => __('Hide control panel, show when mouse hovers over', 'slideshow-plugin'),
//			'waitUntilLoaded'             => __('Wait until the next slide has loaded before showing it', 'slideshow-plugin'),
//			'showLoadingIcon'             => __('Show a loading icon until the first slide appears', 'slideshow-plugin'),
//			'random'                      => __('Randomize slides', 'slideshow-plugin'),
//			'avoidFilter'                 => sprintf(__('Avoid content filter (disable if \'%s\' is shown)', 'slideshow-plugin'), SlideshowPluginShortcode::$bookmark)
//		);
//
//		$variableDefinitions = array(
//			'animation'                   => array('type' => 'select', 'default' => self::$variableDefaults['animation']                  , 'description' => $descriptions['animation']                  , 'group' => __('Animation', 'slideshow-plugin')    , 'options' => array('slide' => __('Slide Left', 'slideshow-plugin'), 'slideRight' => __('Slide Right', 'slideshow-plugin'), 'slideUp' => __('Slide Up', 'slideshow-plugin'), 'slideDown' => __('Slide Down', 'slideshow-plugin'), 'crossFade' => __('Cross Fade', 'slideshow-plugin'), 'directFade' => __('Direct Fade', 'slideshow-plugin'), 'fade' => __('Fade', 'slideshow-plugin'), 'random' => __('Random Animation', 'slideshow-plugin'))),
//			'slideSpeed'                  => array('type' => 'text'  , 'default' => self::$variableDefaults['slideSpeed']                 , 'description' => $descriptions['slideSpeed']                 , 'group' => __('Animation', 'slideshow-plugin')),
//			'descriptionSpeed'            => array('type' => 'text'  , 'default' => self::$variableDefaults['descriptionSpeed']           , 'description' => $descriptions['descriptionSpeed']           , 'group' => __('Animation', 'slideshow-plugin')),
//			'intervalSpeed'               => array('type' => 'text'  , 'default' => self::$variableDefaults['intervalSpeed']              , 'description' => $descriptions['intervalSpeed']              , 'group' => __('Animation', 'slideshow-plugin')),
//			'slidesPerView'               => array('type' => 'text'  , 'default' => self::$variableDefaults['slidesPerView']              , 'description' => $descriptions['slidesPerView']              , 'group' => __('Display', 'slideshow-plugin')),
//			'maxWidth'                    => array('type' => 'text'  , 'default' => self::$variableDefaults['maxWidth']                   , 'description' => $descriptions['maxWidth']                   , 'group' => __('Display', 'slideshow-plugin')),
//			'aspectRatio'                 => array('type' => 'text'  , 'default' => self::$variableDefaults['aspectRatio']                , 'description' => $descriptions['aspectRatio']                , 'group' => __('Display', 'slideshow-plugin')                                                           , 'dependsOn' => array('settings[preserveSlideshowDimensions]', 'true')),
//			'height'                      => array('type' => 'text'  , 'default' => self::$variableDefaults['height']                     , 'description' => $descriptions['height']                     , 'group' => __('Display', 'slideshow-plugin')                                                           , 'dependsOn' => array('settings[preserveSlideshowDimensions]', 'false')),
//			'imageBehaviour'              => array('type' => 'select', 'default' => self::$variableDefaults['imageBehaviour']             , 'description' => $descriptions['imageBehaviour']             , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('natural' => __('Natural and centered', 'slideshow-plugin'), 'crop' => __('Crop to fit', 'slideshow-plugin'), 'stretch' => __('Stretch to fit', 'slideshow-plugin'))),
//			'preserveSlideshowDimensions' => array('type' => 'radio' , 'default' => self::$variableDefaults['preserveSlideshowDimensions'], 'description' => $descriptions['preserveSlideshowDimensions'], 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[enableResponsiveness]', 'true')),
//			'enableResponsiveness'        => array('type' => 'radio' , 'default' => self::$variableDefaults['enableResponsiveness']       , 'description' => $descriptions['enableResponsiveness']       , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'showDescription'             => array('type' => 'radio' , 'default' => self::$variableDefaults['showDescription']            , 'description' => $descriptions['showDescription']            , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'hideDescription'             => array('type' => 'radio' , 'default' => self::$variableDefaults['hideDescription']            , 'description' => $descriptions['hideDescription']            , 'group' => __('Display', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[showDescription]', 'true')),
//			'play'                        => array('type' => 'radio' , 'default' => self::$variableDefaults['play']                       , 'description' => $descriptions['play']                       , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'loop'                        => array('type' => 'radio' , 'default' => self::$variableDefaults['loop']                       , 'description' => $descriptions['loop']                       , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'pauseOnHover'                => array('type' => 'radio' , 'default' => self::$variableDefaults['loop']                       , 'description' => $descriptions['pauseOnHover']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'controllable'                => array('type' => 'radio' , 'default' => self::$variableDefaults['controllable']               , 'description' => $descriptions['controllable']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'hideNavigationButtons'       => array('type' => 'radio' , 'default' => self::$variableDefaults['hideNavigationButtons']      , 'description' => $descriptions['hideNavigationButtons']      , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[controllable]', 'true')),
//			'showPagination'              => array('type' => 'radio' , 'default' => self::$variableDefaults['showPagination']             , 'description' => $descriptions['showPagination']             , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'hidePagination'              => array('type' => 'radio' , 'default' => self::$variableDefaults['hidePagination']             , 'description' => $descriptions['hidePagination']             , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[showPagination]', 'true')),
//			'controlPanel'                => array('type' => 'radio' , 'default' => self::$variableDefaults['controlPanel']               , 'description' => $descriptions['controlPanel']               , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no)),
//			'hideControlPanel'            => array('type' => 'radio' , 'default' => self::$variableDefaults['hideControlPanel']           , 'description' => $descriptions['hideControlPanel']           , 'group' => __('Control', 'slideshow-plugin')      , 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[controlPanel]', 'true')),
//			'waitUntilLoaded'             => array('type' => 'radio' , 'default' => self::$variableDefaults['waitUntilLoaded']            , 'description' => $descriptions['waitUntilLoaded']            , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no)),
//			'showLoadingIcon'             => array('type' => 'radio' , 'default' => self::$variableDefaults['showLoadingIcon']            , 'description' => $descriptions['showLoadingIcon']            , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no) , 'dependsOn' => array('settings[waitUntilLoaded]', 'true')),
//			'random'                      => array('type' => 'radio' , 'default' => self::$variableDefaults['random']                     , 'description' => $descriptions['random']                     , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no)),
//			'avoidFilter'                 => array('type' => 'radio' , 'default' => self::$variableDefaults['avoidFilter']                , 'description' => $descriptions['avoidFilter']                , 'group' => __('Miscellaneous', 'slideshow-plugin'), 'options' => array('true' => $yes, 'false' => $no))
//		);
//
//		self::$variableDefinitions = $variableDefinitions;
//
//		return $variableDefinitions;
//	}
//
//	/**
//	 * Returns all settings profiles within the defined offset and limit.
//	 *
//	 * @since 2.3.0
//	 * @param int $offset (Optional, defaults to -1)
//	 * @param int $limit  (Optional, defaults to -1)
//	 * @return array
//	 */
//	static function getAll($offset = -1, $limit = -1)
//	{
//		if (!is_numeric($offset) ||
//			$offset < 0)
//		{
//			$offset = -1;
//		}
//
//		if (!is_numeric($limit) ||
//			$limit < 0)
//		{
//			$limit = -1;
//		}
//
//		$query = new WP_Query(array(
//			'post_type'      => self::$postType,
//			'orderby'        => 'post_date',
//			'order'          => 'DESC',
//			'offset'         => $offset,
//			'posts_per_page' => $limit,
//		));
//
//		$settingsProfiles = array();
//
//		foreach ($query->get_posts() as $post)
//		{
//			$settingsProfiles[] = new SlideshowPluginSettingsProfile($post);
//		};
//	}

	/**
	 * Initialize style post type.
	 *
	 * @since 2.3.0
	 */
	static function init()
	{
		add_action('init', array(__CLASS__, 'registerPostType'));
//		add_action('save_post'            , array(__CLASS__, 'saveThroughEditor'));

//		add_filter('post_updated_messages', array(__CLASS__, 'alterCRUDMessages'));
//		add_filter('post_row_actions'     , array(__CLASS__, 'duplicateActionLink'), 10, 2);
	}

	/**
	 * Registers the settings profile post type.
	 *
	 * @since 2.3.0
	 */
	static function registerPostType()
	{
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
					'edit_item'          => __('Edit Style', 'slideshow-plugin'),
					'view_item'          => __('View Style', 'slideshow-plugin'),
					'all_items'          => __('All Styles', 'slideshow-plugin'),
					'search_items'       => __('Search Styles', 'slideshow-plugin'),
					'parent_item_colon'  => __('Parent Styles:', 'slideshow-plugin'),
					'not_found'          => __('No styles found', 'slideshow-plugin'),
					'not_found_in_trash' => __('No styles found', 'slideshow-plugin')
				),
				'public'               => false,
				'publicly_queryable'   => false,
				'show_ui'              => true,
				'show_in_menu'         => 'edit.php?post_type=' . SlideshowPluginPostType::$postType,
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
				'supports'             => array('title'),
//				'register_meta_box_cb' => array(__CLASS__, 'registerMetaBoxes')
			)
		);
	}

//	/**
//	 * Add custom meta boxes.
//	 *
//	 * @since 2.3.0
//	 */
//	static function registerMetaBoxes()
//	{
//		add_meta_box(
//			'settings',
//			__('Settings', 'slideshow-plugin'),
//			array(__CLASS__, 'settingsMetaBox'),
//			self::$postType,
//			'normal',
//			'default'
//		);
//	}
//
//	/**
//	 * Shows the settings for the current settings profile.
//	 *
//	 * @since 2.3.0
//	 */
//	static function settingsMetaBox()
//	{
//		global $post;
//
//		wp_nonce_field(self::$nonceAction, self::$nonceName);
//
//		$data                  = new stdClass();
//		$data->settingsProfile = new SlideshowPluginSettingsProfile($post);
//
//		SlideshowPluginMain::outputView(__CLASS__ . '/settings.php', $data);
//	}
//
//	/**
//	 * Changes the "Post published/updated" message to a "Settings profile created/updated" message without the link to a
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
//		// Return when not on a settings profile edit page
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
//		switch ($messageID)
//		{
//			case 6:
//				$messages[$currentScreen->base][$messageID] = __('Settings profile created', 'slideshow-plugin');
//				break;
//
//			default:
//				$messages[$currentScreen->base][$messageID] = __('Settings profile updated', 'slideshow-plugin');
//		}
//
//		return $messages;
//	}
//
//	/**
//	 * Hooked on the post_row_actions filter, add a "duplicate" action to each settings profile on the settings profiles
//	 * overview page.
//	 *
//	 * @since 2.3.0
//	 * @param array $actions
//	 * @param WP_Post $post
//	 * @return array $actions
//	 */
//	static function duplicateActionLink($actions, $post)
//	{
//		if (current_user_can(SlideshowPluginGeneralSettings::$capabilities['addSettingsProfiles']) &&
//			$post->post_type === self::$postType)
//		{
//			$url = add_query_arg(array(
//				'action' => 'slideshow_jquery_image_gallery_duplicate_settings_profile',
//				'post'   => $post->ID,
//			));
//
//			$actions['duplicate'] = '<a href="' . wp_nonce_url($url, 'duplicate-settings-profile_' . $post->ID, 'nonce') . '">' . __('Duplicate', 'slideshow-plugin') . '</a>';
//		}
//
//		return $actions;
//	}
//
//	/**
//	 * Checks if a "duplicate" settings profile action was performed and whether or not the current user has the
//	 * permission to perform this action at all.
//	 *
//	 * @since 2.3.0
//	 */
//	static function duplicate()
//	{
//		$postID           = filter_input(INPUT_GET, 'post'     , FILTER_VALIDATE_INT);
//		$nonce            = filter_input(INPUT_GET, 'nonce'    , FILTER_SANITIZE_STRING);
//		$postType         = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
//		$errorRedirectURL = remove_query_arg(array('action', 'post', 'nonce'));
//
//		// Check if nonce is correct and user has the correct privileges
//		if (!wp_verify_nonce($nonce, 'duplicate-settings-profile_' . $postID) ||
//			!current_user_can(SlideshowPluginGeneralSettings::$capabilities['addSettingsProfiles']) ||
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
//	}
}