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

		// Prepare image
		$image = wp_get_attachment_image_src($attachment->ID, 'full');
		$imageSrc = '';
		$imageAvailable = true;
		if(!is_array($image) || !$image){
			if(!empty($attachment->guid))
				$imageSrc = $attachment->guid;
			else
				$imageAvailable = false;
		}else{
			$imageSrc = $image[0];
		}

		// If image is available
		if($imageAvailable): ?>

			<div class="slideshow_slide slideshow_slide_image">
				<a <?php echo $anchorTagAttributes; ?>>
					<img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo $title; ?>">
				</a>
				<div class="slideshow_description slideshow_transparent">
					<a <?php echo $anchorTagAttributes; ?>>
						<h2><?php echo $title; ?></h2>
						<p>
							<?php echo $description; ?>
						</p>
					</a>
				</div>
			</div>

		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>