<?php

$title = $description = $textColor = $color = $url = $urlTarget = $noFollow = '';

$titleElementTag = $descriptionElementTag = SlideshowPluginSlideInserter::getElementTag();

if (isset($properties['title']))
{
	$title = trim(SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']));
}

if (isset($properties['titleElementTagID']))
{
	$titleElementTag = SlideshowPluginSlideInserter::getElementTag($properties['titleElementTagID']);
}

if (isset($properties['description']))
{
	$description = trim(SlideshowPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']));
}

if (isset($properties['descriptionElementTagID']))
{
	$descriptionElementTag = SlideshowPluginSlideInserter::getElementTag($properties['descriptionElementTagID']);
}

if (isset($properties['textColor']))
{
	$textColor = $properties['textColor'];

	if (substr($textColor, 0, 1) != '#')
	{
		$textColor = '#' . $textColor;
	}

	$textColor = htmlspecialchars($textColor);
}

if (isset($properties['color']))
{
	$color = $properties['color'];

	if (substr($color, 0, 1) != '#')
	{
		$color = '#' . $color;
	}

	$color = htmlspecialchars($color);
}

if (isset($properties['url']))
{
	$url = htmlspecialchars($properties['url']);
}

if (isset($properties['urlTarget']))
{
	$urlTarget = htmlspecialchars($properties['urlTarget']);
}

if (isset($properties['noFollow']))
{
	$noFollow = ' rel="nofollow" ';
}

$anchorTagAttributes = (!empty($url) ? 'href="' . $url . '"' : '') . ' ' . (!empty($urlTarget) ? 'target="' . $urlTarget . '"' : '') . $noFollow;

?>

<div class="slideshow_slide slideshow_slide_text" style="<?php echo !empty($color) ? 'background-color: ' . $color . ';' : '' ?>">
	<?php if(!empty($title)): ?>
	<<?php echo $titleElementTag; ?>>
		<a <?php echo $anchorTagAttributes; ?> style="<?php echo !empty($textColor) ? 'color: ' . $textColor . ';' : ''; ?>">
			<?php echo $title; ?>
		</a>
	</<?php echo $titleElementTag; ?>>
	<?php endif; ?>

	<?php if(!empty($description)): ?>
	<<?php echo $descriptionElementTag; ?>>
		<a <?php echo $anchorTagAttributes; ?> style="<?php echo !empty($textColor) ? 'color: ' . $textColor . ';' : ''; ?>">
			<?php echo $description; ?>
		</a>
	</<?php echo $descriptionElementTag; ?>>
	<?php endif; ?>

	<a <?php echo $anchorTagAttributes ?> class="slideshow_background_anchor"></a>
</div>