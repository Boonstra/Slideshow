<?php
/**
 * @since 1.0.0
 * @author: Stefan Boonstra
 */
abstract class SlideshowPluginModel
{
	/** @var string */
	protected $modelPostType;

	/** @var WP_Post */
	protected $post;

	/** @var array */
	protected $postMeta = array();

	/**
	 * Constructs the model. The $post parameter can either contain a WP_Post instance or an ID to a settings
	 * profile.
	 *
	 * Constructing a model costs no database queries if a WP_Post object is passed. When an ID is passed, a single
	 * query is run to get the WP_Post object belonging to that ID. The object's post meta variables are lazy loaded
	 * when they're requested per the $this->getPostMeta method.
	 *
	 * @since 2.3.0
	 * @param WP_Post|int $post (Optional, defaults to null)
	 */
	function __construct($post = null)
	{
		if ($post instanceof WP_Post &&
			$post->post_type === $this->modelPostType)
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
	 * Sets the post meta value of the post meta with the passed key. Returns true on success, false on failure.
	 *
	 * @since 2.3.0
	 * @param $key
	 * @param $value
	 * @return bool
	 */
	function setPostMeta($key, $value)
	{
		switch (SlideshowPluginPostType::getPostTypeInformation($this->modelPostType)['postMeta'][$key])
		{
			case 'array':

				if (!is_array($value))
				{
					return false;
				}

				$value = array_merge($this->getPostMeta($key), $value);

				break;

			case 'string':

				if (!is_string($value))
				{
					return false;
				}

				break;

			case 'int':

				if (!is_numeric($value))
				{
					return false;
				}

				break;

			default:

				return false;
		}

		$this->postMeta[$key] = $value;

		return true;
	}

	/**
	 * Get post meta by the passed key.
	 *
	 * @since 2.3.0
	 * @param string $key
	 * @return mixed
	 */
	function getPostMeta($key)
	{
		// Return cached value
		if (isset($this->postMeta[$key]))
		{
			return $this->postMeta[$key];
		}

		$postMeta = get_post_meta(
			$this->post->ID,
			$key,
			true
		);

		// Get from database has failed when an empty string is returned
		if (is_string($postMeta) && strlen($postMeta) <= 0)
		{
			$this->postMeta[$key] = $this->getPostMetaDefaults($key);

			return $this->postMeta[$key];
		}

		switch (SlideshowPluginPostType::getPostTypeInformation($this->modelPostType)['postMeta'][$key])
		{
			case 'array':

				if (is_array($postMeta))
				{
					$postMeta = $this->getPostMetaDefaults($key);
				}
				else
				{
					array_merge($this->getPostMetaDefaults($key), $postMeta);
				}

				break;

			case 'string':

				if (!is_string($postMeta))
				{
					$postMeta = $this->getPostMetaDefaults($key);
				}

				break;

			case 'int':

				if (!is_numeric($postMeta))
				{
					$postMeta = $this->getPostMetaDefaults($key);
				}

				break;
		}

		$this->postMeta[$key] = $postMeta;

		return $postMeta;
	}

	/**
	 * Get default post meta by the passed key.
	 *
	 * @since 2.3.0
	 * @param string $key
	 * @return mixed
	 */
	abstract function getPostMetaDefaults($key);

	/**
	 * Saves the current model.
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

		$postTypeInformation = SlideshowPluginPostType::getPostTypeInformation($this->modelPostType);

		$isPostMetaSaved = true;

		// Loop through registered post meta fields
		foreach ($postTypeInformation['postMeta'] as $postMetaKey => $postMetaInformation)
		{
			// Only update the post meta that is set, as it may have been changed
			if (isset($this->postMeta[$postMetaKey]))
			{
				$updatePostMetaResult = update_post_meta($this->post->ID, $postMetaKey, $this->getPostMeta($postMetaKey));

				if ($updatePostMetaResult === false)
				{
					$isPostMetaSaved = false;
				}
			}
		}

		return $isPostSaved && $isPostMetaSaved;
	}

	/**
	 * Returns all models with the passed post type and within the defined offset and limit.
	 *
	 * @since 2.3.0
	 * @param string $postType
	 * @param int    $offset   (Optional, defaults to -1)
	 * @param int    $limit    (Optional, defaults to -1)
	 * @return array
     */
	static function getAll($postType, $offset = -1, $limit = -1)
	{
		$models = array();

		$postTypeInformation = SlideshowPluginPostType::getPostTypeInformation($postType);

		if (count($postTypeInformation) <= 0)
		{
			return $models;
		}

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
			'post_type'      => $postType,
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'offset'         => $offset,
			'posts_per_page' => $limit,
		));

		$reflectionClass = new ReflectionClass($postTypeInformation['class']);

		foreach ($query->get_posts() as $post)
		{
			$models[] = $reflectionClass->newInstanceArgs($post);
		};

		return $models;
	}
}