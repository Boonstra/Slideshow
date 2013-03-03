<?php

// TODO Initialize stylesheet class to deploy slideshow. A custom stylesheet is loaded by it's ID, prefix needs to be added.
// TODO The ID needs to be added to all .slideshow_container classes, to enable the slideshow to tell them apart.
// TODO Default styles will be loaded here as well. Not ideal, but the best way to make each stylesheet unique.



header('Content-Type: text/css; charset=UTF-8');
header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 31556926) . ' GMT');
header("Cache-Control: public, max-age=31556926");

?>

.slideshow_container { }

.slideshow_container a { text-decoration: none; }

.slideshow_container .slideshow_slide { margin-right: 2px; }
.slideshow_container .slideshow_slide_text h2 { text-align: center; font-size: 1.3em;text-shadow: black 0.1em 0.1em 0.1em; }
.slideshow_container .slideshow_slide_text p { text-align: center;text-shadow: black 0.1em 0.1em 0.1em; }
.slideshow_container .slideshow_slide_image { }
.slideshow_container .slideshow_slide_vieo { }

.slideshow_container .slideshow_description { background: #000; width: 100%; }
.slideshow_container .slideshow_description h2 a  { color: #fff; font-size: 1.3em; text-align: center; }
.slideshow_container .slideshow_description p a  { color: #fff; text-align: center; }

.slideshow_container .slideshow_transparent { filter: alpha(opacity = 50); opacity: 0.5; }
.slideshow_container .slideshow_transparent:hover { filter: alpha(opacity = 80); opacity: 0.8; }

.slideshow_container .slideshow_controlPanel {
width: 21px;
height: 21px;
margin-left: -11px;
background: #000;
border-radius: 2px;
-moz-border-radius: 10px;
}

.slideshow_container .slideshow_controlPanel ul { }

.slideshow_container .slideshow_controlPanel ul li {
margin: 3px;
width: 15px;
height: 15px;
}

.slideshow_container .slideshow_controlPanel ul li:hover { }

.slideshow_container .slideshow_play {
background: url('http://localhost/wordpress/testenvironment/wp-content/plugins/slideshow-jquery-image-gallery/images/SlideshowPlugin/light-controlpanel.png') 0 0 no-repeat;
}

.slideshow_container .slideshow_pause {
background: url('http://localhost/wordpress/testenvironment/wp-content/plugins/slideshow-jquery-image-gallery/images/SlideshowPlugin/light-controlpanel.png') -15px 0 no-repeat;
}

.slideshow_container .slideshow_button {
margin-top: -20px;
height: 40px;
width: 19px;
background: url('http://localhost/wordpress/testenvironment/wp-content/plugins/slideshow-jquery-image-gallery/images/SlideshowPlugin/light-arrows.png') no-repeat;
}

.slideshow_container .slideshow_previous { }

.slideshow_container .slideshow_next {
background-position: -19px 0;
}

.slideshow_container .slideshow_pagination { bottom: 16px; }

.slideshow_container .slideshow_pagination_center {	}

.slideshow_container .slideshow_pagination .slideshow_currentView {
filter: alpha(opacity = 80);
opacity: 0.8;
}

.slideshow_container .slideshow_pagination ul { }

.slideshow_container .slideshow_pagination ul li {
margin: 0 2px;
width: 11px;
height: 11px;
background: url('http://localhost/wordpress/testenvironment/wp-content/plugins/slideshow-jquery-image-gallery/images/SlideshowPlugin/light-bullet.png') no-repeat;
}

<?php die; ?>