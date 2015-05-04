<?php if ($data instanceof stdClass): ?>
<table>
	<select name="<?php echo htmlspecialchars(SlideshowPluginSlideshow::SETTINGS_PROFILE_POST_META_KEY); ?>">
		<option value="-1">lol</option>
		<?php foreach ($data->settingsProfiles as $settingsProfile): ?>
		<option value="<?php echo htmlspecialchars($settingsProfile->post->id); ?>">
			<?php echo htmlspecialchars($settingsProfile->post->post_title); ?>
		</option>
		<?php endforeach; ?>
	</select>
</table>

<p>
	<?php
	echo sprintf(__(
			'Settings profiles can be created and customized %shere%s.',
			'slideshow-plugin'
		),
		'<a href="' . admin_url() . 'edit.php?post_type=' . SlideshowPluginSettingsProfile::$postType . '" target="_blank">',
		'</a>'
	);
	?>
</p>
<?php endif; ?>