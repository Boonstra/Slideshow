<div id="slideshow-slide-inserter-popup-background"></div>
<div id="slideshow-slide-inserter-popup">
	<div id="close"></div>
	<div>
		<input type="text" id="search" />
		<?php submit_button(__('Search', 'slideshow-jquery-image-gallery'), 'primary', 'search-submit', false); ?>
		<i><?php _e('Search images by title or ID', 'slideshow-jquery-image-gallery'); ?></i>
	</div>
	<div style="clear: both;"></div>

	<div id="search-results">
		<table id="results" class="widefat"></table>
	</div>
</div>