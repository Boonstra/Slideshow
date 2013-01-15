<?php

$videoId = '';
if(isset($properties['videoId']))
	$videoId = htmlspecialchars($properties['videoId']);

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

?>

<div class="slideshow_slide slideshow_slide_video">
	<div style="display: none;"><?php echo $videoId; ?></div>
</div>