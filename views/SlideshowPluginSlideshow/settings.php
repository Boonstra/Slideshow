<?php if ($data instanceof stdClass && count($data->settingsProfiles) > 0): ?>
<table>
	<select name="<?php echo htmlspecialchars(SlideshowPluginSlideshow::SETTINGS_PROFILE_POST_META_KEY); ?>">
		<?php foreach ($data->settingsProfiles as $settingsProfile): ?>
		<option value="<?php echo htmlspecialchars($settingsProfile->post->ID); ?>" <?php selected($data->currentSettingsProfileID, $settingsProfile->post->ID); ?>>
			<?php echo htmlspecialchars($settingsProfile->post->post_title); ?>
		</option>
		<?php endforeach; ?>
	</select>
</table>
<?php endif; ?>

<p>
	<?php
	echo sprintf(__(
			'Settings profiles can be created and edited %shere%s.',
			'slideshow-plugin'
		),
		'<a href="' . admin_url() . 'edit.php?post_type=' . SlideshowPluginSettingsProfile::$postType . '" target="_blank">',
		'</a>'
	);
	?>
</p>