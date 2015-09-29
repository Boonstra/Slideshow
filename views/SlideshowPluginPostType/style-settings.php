<?php if ($data instanceof stdClass) : ?>
	<table>
		<?php if(count($data->settings) > 0): $i = 0; ?>

		<?php foreach($data->settings as $key => $value): ?>

		<?php if( !isset($value, $value['type'], $value['default'], $value['description']) || !is_array($value)) continue; ?>

		<tr <?php if(isset($value['dependsOn'])) echo 'style="display:none;"'; ?>>
			<td><?php echo $value['description']; ?></td>
			<td><?php echo SlideshowPluginSlideshowSettingsHandler::getInputField(htmlspecialchars(SlideshowPluginSlideshowSettingsHandler::$styleSettingsKey), $key, $value); ?></td>
			<td><?php _e('Default', 'slideshow-jquery-image-gallery'); ?>: &#39;<?php echo (isset($value['options']))? $value['options'][$value['default']]: $value['default']; ?>&#39;</td>
		</tr>

		<?php endforeach; ?>

		<?php endif; ?>
	</table>

	<p>
		<?php
			echo sprintf(__(
					'Custom styles can be created and customized %shere%s.',
					'slideshow-jquery-image-gallery'
				),
				'<a href="' . admin_url() . '/edit.php?post_type=slideshow&page=general_settings#custom-styles" target="_blank">',
				'</a>'
			);
		?>
	</p>
<?php endif; ?>