<?php

$title = $description = $url = $urlTarget = $postId = '';
if(isset($properties['title']))
	$title = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']);
if(isset($properties['description']))
	$description = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']);
if(isset($properties['url']))
	$url = htmlspecialchars($properties['url']);
if(isset($properties['urlTarget']))
	$urlTarget = htmlspecialchars($properties['urlTarget']);
if(isset($properties['postId']))
	$postId = $properties['postId'];

// Post ID should always be numeric
if(is_numeric($postId)):

	// Anchor tag is used twice
	$anchorTagAttributes = (!empty($url) ? ' href="' . $url . '" ' : '') . (!empty($urlTarget) ? ' target="' . $urlTarget . '" ' : '');

	// Get post from post id. Post should be able to load
	$attachment = get_post($postId);
	if(!empty($attachment)):

		// If no title is set, get the alt from the original image
		$alt = $title;
		if(empty($alt))
			$alt = htmlspecialchars($attachment->post_title);
		if(empty($alt))
			$alt = htmlspecialchars($attachment->post_content);

		// Prepare image
		$image = wp_get_attachment_image_src($attachment->ID, 'full');
		$imageSrc = '';
		$imageWidth = 0;
		$imageHeight = 0;
		$imageAvailable = true;
		if(!is_array($image) || !$image || !isset($image[0])){
			if(!empty($attachment->guid))
				$imageSrc = $attachment->guid;
			else
				$imageAvailable = false;
		}else{
			$imageSrc = $image[0];

			if(isset($image[1], $image[2])){
				$imageWidth = $image[1];
				$imageHeight = $image[2];
			}
		}

		// If image is available
		if($imageAvailable): ?>

			<div class="slideshow_slide slideshow_slide_image">
				<a <?php echo $anchorTagAttributes; ?>>
					<img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo $alt; ?>" width="<?php echo $imageWidth ?>" height="<?php echo $imageHeight; ?>">
				</a>
				<div class="slideshow_description slideshow_transparent">
					<a <?php echo $anchorTagAttributes; ?>>
						<?php echo !empty($title) ? '<h2>' . $title . '</h2>' : ''; ?>
						<?php echo !empty($description) ? '<p>' . $description . '</p>' : ''; ?>
					</a>
				</div>
			</div>

		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>