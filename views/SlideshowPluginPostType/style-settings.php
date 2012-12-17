<table>
	<?php if(count($settings) > 0): $i = 0; ?>

	<?php foreach($settings as $key => $value): ?>

	<?php if(empty($value) || !is_array($value)) continue; ?>
<?php //var_dump($value); ?>
	<tr <?php if(isset($value['dependsOn'])) echo 'style="display:none;"'; ?>>
		<td><?php echo $value['description']; ?></td>
		<td><?php echo SlideshowPluginSlideshowSettingsHandler::getInputField(htmlspecialchars(SlideshowPluginSlideshowSettingsHandler::$styleSettingsKey), $key, $value); ?></td>
		<td><?php _e('Default', 'slideshow-plugin'); ?>: &#39;<?php echo $value['default']; ?>&#39;</td>
	</tr>

	<?php endforeach; ?>

	<?php endif; ?>
</table>