<div class="slideshow_container slideshow_container_<?php echo (is_numeric($sessionID)) ? $sessionID : 0; ?>" style="width: <?php echo (is_numeric($settings['width']))? $settings['width'] : 0; ?>px; height: <?php echo (is_numeric($settings['height']))? $settings['height'] : 0; ?>px;">
	<div class="slideshow_overflow" style="width: <?php echo (is_numeric($settings['width']))? $settings['width'] : 0; ?>px; height: <?php echo (is_numeric($settings['height']))? $settings['height'] : 0; ?>px;">
		<div class="slideshow">
			<?php if(count($slides) > 0): ?>
				<?php $i = 0; ?>
				<?php foreach($slides as $slide): ?>

					<?php
					$title = $description = $url = $target = '';
					if(isset($slide['title']))
						$title = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($slide['title']);
					if(isset($slide['description']))
						$description = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($slide['description']);
					if(isset($slide['url']))
						$url = htmlspecialchars($slide['url']);
					if(isset($slide['urlTarget']))
						$target = htmlspecialchars($slide['urlTarget']);
					?>

					<?php if($slide['type'] == 'text'): ?>

						<?php
							$color = '';
							if(isset($slide['color']))
								$color = htmlspecialchars($slide['color']);
						?>

						<div class="slide slide_<?php echo $i; ?>" <?php if(!empty($color)) echo 'style="background: #' . $color . ';"'; ?> style="height: <?php echo (is_numeric($settings['height']))? $settings['height'] : 0; ?>px;">
							<a <?php if(!empty($url)) echo 'href="' . $url . '"';?> <?php if(!empty($target)) echo 'target="' . $target . '"'; ?>>
                                <h2><?php echo $title; ?></h2>
                                <p><?php echo $description; ?></p>
							</a>
						</div>

					<?php elseif($slide['type'] == 'video'): ?>

						<?php
							$videoId = '';
							if(isset($slide['videoId']))
								$videoId = htmlspecialchars($slide['videoId']);

							// If the video ID contains 'v=', it means a URL has been passed. Retrieve the video ID.
							$idPosition = null;
							if(($idPosition = stripos($videoId, 'v=')) !== false){
								// The video ID, which perhaps still has some arguments behind it.
								$videoId = substr($videoId, $idPosition + 2);

								// Explode on extra arguments (&).
								$videoId = explode('&', $videoId);

								// The first element is the video ID
								if(is_array($videoId) && isset($videoId[0]))
									$videoId = $videoId[0];
							}

							$elementVideoId = 'youtube-player-' . rand() . '-' . $videoId;
						?>

						<div class="slide slide_<?php echo $i; ?> slide_video" style="height: <?php echo (is_numeric($settings['height']))? $settings['height'] : 0; ?>px;">
							<div class="videoId" style="display: none;"><?php echo $videoId; ?> <?php echo $elementVideoId; ?></div>
							<div id="<?php echo $elementVideoId; ?>"></div>
						</div>

					<?php elseif($slide['type'] == 'attachment'): ?>

						<?php
						// Get post id, continue if no post id was found
						$postId = '';
						if(isset($slide['postId']) && is_numeric($slide['postId']))
							$postId = $slide['postId'];
						else
							continue;

						// Get post from post id. Continue if post can't be loaded
						$attachment = get_post($postId);
						if(empty($attachment))
							continue;

						// Get post tile and description when empty
						if(empty($title))
							$title = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($attachment->post_title);
						if(empty($description))
							$description = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($attachment->post_content);

						// Prepare image
						$image = wp_get_attachment_image_src($attachment->ID, 'full');
						$imageSrc = '';
						if(!is_array($image) || !$image){
							if(!empty($attachment->guid))
								$imageSrc = $attachment->guid;
							else
								continue;
						}else{
							$imageSrc = $image[0];
						}
						?>

						<div class="slide slide_<?php echo $i; ?>" style="height: <?php echo (is_numeric($settings['height']))? $settings['height'] : 0; ?>px;">
							<div class="description transparent">
								<a <?php if(!empty($url)) echo 'href="' . $url . '"'; ?> <?php if(!empty($target)) echo 'target="' . $target . '"'; ?>>
									<h2><?php echo $title ?></h2>
									<p><?php echo $description; ?></p>
								</a>
							</div>
							<a <?php if(!empty($url)) echo 'href="' . $url . '"'; ?> <?php if(!empty($target)) echo 'target="' . $target . '"'; ?>>
								<img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo $title; ?>" />
							</a>
						</div>

					<?php endif; ?>
					<?php $i++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="controllers">
		<div class="controlPanel transparent"><ul><li class="togglePlay play"></li></ul></div>

		<div class="button previous transparent"></div>
		<div class="button next transparent"></div>
	</div>

	<div class="slideshow_plugin_manufacturer">
		<a href="http://www.stefanboonstra.com/slideshow/">Wordpress Slideshow</a>
	</div>

	<!-- WordPress Slideshow Version <?php echo SlideshowPluginMain::$version; ?> -->

	<?php if(is_array($log) && count($log) > 0): ?>
	<!-- Error log
	<?php foreach($log as $logMessage): ?>
		- <?php echo htmlspecialchars($logMessage); ?>
	<?php endforeach; ?>
	-->
	<?php endif; ?>

	<?php if(!empty($style)): ?>
	<style type="text/css">
			<?php echo htmlspecialchars($style); ?>
	</style>
	<?php endif; ?>
</div>