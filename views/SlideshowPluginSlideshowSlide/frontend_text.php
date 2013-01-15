<?php

$title = $description = $textColor = $color = $url = $urlTarget = '';
if(isset($properties['title']))
	$title = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']);
if(isset($properties['description']))
	$description = SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']);
if(isset($properties['textColor']))
	$textColor = htmlspecialchars($properties['textColor']);
if(isset($properties['color']))
	$color = htmlspecialchars($properties['color']);
if(isset($properties['url']))
	$url = htmlspecialchars($properties['url']);
if(isset($properties['urlTarget']))
	$urlTarget = htmlspecialchars($properties['urlTarget']);

?>

<div class="slideshow_slide slideshow_slide_text" style="background-color: #<?php echo !empty($color) ? $color : 'FFFFFF'; ?>">
	<a <?php echo !empty($url) ? 'href="' . $url . '"' : ''; ?> <?php echo !empty($urlTarget) ? 'target="' . $urlTarget . '"' : ''; ?>>

		<h2 style="color: #<?php echo !empty($textColor) ? $textColor : '000000'; ?>;">
			<?php echo $title; ?>
		</h2>

		<p style="color: #<?php echo !empty($textColor) ? $textColor : '000000'; ?>;">
			<?php echo $description; ?>
		</p>

	</a>
</div>