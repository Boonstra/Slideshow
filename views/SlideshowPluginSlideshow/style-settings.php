<?php if ($data instanceof stdClass && count($data->styles) > 0): ?>
<table>
	<select name="<?php echo htmlspecialchars(SlideshowPluginSlideshow::STYLE_POST_META_KEY); ?>">
		<?php foreach ($data->styles as $style): ?>
		<option value="<?php echo htmlspecialchars($style->post->ID); ?>" <?php selected($data->currentStyleID, $style->post->ID); ?>>
			<?php echo htmlspecialchars($style->post->post_title); ?>
		</option>
		<?php endforeach; ?>
	</select>
</table>
<?php endif; ?>

<p>
	<?php
	echo sprintf(__(
			'Custom styles can be created and edited %shere%s.',
			'slideshow-plugin'
		),
		'<a href="' . admin_url() . '/edit.php?post_type=edit.php?post_type=' . SlideshowPluginStyle::$postType . '" target="_blank">',
		'</a>'
	);
	?>
</p>