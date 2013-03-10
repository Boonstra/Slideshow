<?php

$title = $description = $textColor = $color = $url = $urlTarget = '';
if(isset($properties['title']))
	$title = trim(SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']));
if(isset($properties['description']))
	$description = trim(SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']));
if(isset($properties['textColor']))
	$textColor = htmlspecialchars($properties['textColor']);
if(isset($properties['color']))
	$color = htmlspecialchars($properties['color']);
if(isset($properties['url']))
	$url = htmlspecialchars($properties['url']);
if(isset($properties['urlTarget']))
	$urlTarget = htmlspecialchars($properties['urlTarget']);

$anchorTagAttributes = (!empty($url) ? 'href="' . $url . '"' : '') . ' ' . (!empty($urlTarget) ? 'target="' . $urlTarget . '"' : '');

?>

<div class="slideshow_slide slideshow_slide_text" style="<?php echo !empty($color) ? 'background-color: #' . $color . ';' : '' ?>">
	<?php if(!empty($title)): ?>
	<h2>
		<a <?php echo $anchorTagAttributes; ?> style="<?php echo !empty($textColor) ? 'color: #' . $textColor . ';' : ''; ?>">
			<?php echo $title; ?>
		</a>
	</h2>
	<?php endif; ?>

	<?php if(!empty($description)): ?>
	<p>
		<a <?php echo $anchorTagAttributes; ?> style="<?php echo !empty($textColor) ? 'color: #' . $textColor . ';' : ''; ?>">
			<?php echo $description; ?>
		</a>
	</p>
	<?php endif; ?>

	<a <?php echo $anchorTagAttributes ?> class="slideshow_background_anchor"></a>
</div>