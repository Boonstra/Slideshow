<div class="wrap">

	<div class="icon32" style="background: url('<?php echo SlideshowPluginMain::getPluginUrl() . '/images/SlideshowPluginPostType/adminIcon32.png'; ?>');"></div>
	<h2 class="nav-tab-wrapper">
		<a href="#user-privileges" class="nav-tab nav-tab-active"><?php _e('User Privileges', 'slideshow-plugin'); ?></a>
		<!--<a href="#default-slideshow-settings" class="nav-tab"><?php _e('Default Slideshow Values', 'slideshow-plugin'); ?></a>-->
		<!--<a href="#custom-styles" class="nav-tab"><?php _e('Custom Styles', 'slideshow-plugin'); ?></a>-->
	</h2>

	<form action="options.php">
		<?php settings_fields(SlideshowPluginGeneralSettings::$settingsGroup); ?>

		<table class="form-table user-privileges">
			<tr valign="top">
				<th><label for="blogname">Site Title</label></th>
				<td><input name="blogname" type="text" id="blogname" value="Website" class="regular-text"></td>
			</tr>
		</table>

        <table class="form-table user-privileges" style="display: none;">

        </table>

		<?php submit_button(); ?>
	</form>
</div>