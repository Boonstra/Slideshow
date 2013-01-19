<?php

// Default settings
$defaultSettings = SlideshowPluginSlideshowSettingsHandler::getDefaultSettings(true);
$defaultStyleSettings = SlideshowPluginSlideshowSettingsHandler::getDefaultStyleSettings(true);

?>

<div class="default-slideshow-settings" style="display: none; float: none;">
	<p>
		<strong><?php _e('Note', 'slideshow-plugin'); ?>:</strong>
	</p>

	<p style="width: 500px;">
		<?php

		echo sprintf(__(
			'The settings set on this page apply only to newly created slideshows and therefore do not alter any existing ones. To adapt a slideshow\'s settings, %sclick here.%s', 'slideshow-plugin'),
			'<a href="' . get_admin_url(null, 'edit.php?post_type=' . SlideshowPluginPostType::$postType) . '">',
			'</a>'
		);

		?>
	</p>
</div>

<div class="default-slideshow-settings feature-filter" style="display: none;">

	<p>
		<b><?php _e('Default Slideshow Settings', 'slideshow-plugin'); ?></b>
	</p>

	<table>

		<?php foreach($defaultSettings as $defaultSettingKey => $defaultSettingValue): ?>

		<tr>
			<td>
				<?php echo $defaultSettingValue['description']; ?>
			</td>
			<td>
				<?php echo SlideshowPluginSlideshowSettingsHandler::getInputField(SlideshowPluginGeneralSettings::$defaultSettings, $defaultSettingKey, $defaultSettingValue) ?>
			</td>
		</tr>

		<?php endforeach; ?>

	</table>
</div>

<div class="default-slideshow-settings feature-filter" style="display: none;">

	<p>
		<b><?php _e('Default Slideshow Stylesheet', 'slideshow-plugin'); ?></b>
	</p>

	<table>

		<?php foreach($defaultStyleSettings as $defaultStyleSettingKey => $defaultStyleSettingValue): ?>

		<tr>
			<td>
				<?php echo $defaultStyleSettingValue['description']; ?>
			</td>
			<td>
				<?php echo SlideshowPluginSlideshowSettingsHandler::getInputField(SlideshowPluginGeneralSettings::$defaultStyleSettings, $defaultStyleSettingKey, $defaultStyleSettingValue) ?>
			</td>
		</tr>

		<?php endforeach; ?>

	</table>
</div>

<div style="clear: both;"></div>