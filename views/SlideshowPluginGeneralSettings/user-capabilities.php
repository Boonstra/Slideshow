<?php

// Roles
global $wp_roles;

// Capabilities
$capabilities = array(
	SlideshowPluginGeneralSettings::$capabilities['addSlideshows'] => __('Add slideshows', 'slideshow-plugin'),
	SlideshowPluginGeneralSettings::$capabilities['editSlideshows'] => __('Edit slideshows', 'slideshow-plugin'),
	SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'] => __('Delete slideshows', 'slideshow-plugin')
);

?>

<div class="user-capabilities feature-filter">

	<p>
		<b><?php _e('Select the user roles that will able to perform certain actions.', 'slideshow-plugin');  ?></b>
	</p>

	<table>

		<?php foreach($capabilities as $capability => $capabilityName): ?>

		<tr valign="top">
			<th><?php echo $capabilityName; ?></th>
			<td>
				<?php

				if(isset($wp_roles->roles) && is_array($wp_roles->roles)):
					foreach($wp_roles->roles as $roleSlug => $values):

						$disabled = ($roleSlug == 'administrator') ? 'disabled="disabled"' : '';
						$checked = ((isset($values['capabilities']) && array_key_exists($capability, $values['capabilities']) && $values['capabilities'][$capability] == true) || $roleSlug == 'administrator') ? 'checked="checked"' : '';
						$name = (isset($values['name'])) ? htmlspecialchars($values['name']) : __('Untitled role', 'slideshow-plugin');

						?>

						<input
							type="checkbox"
							name="<?php echo htmlspecialchars($capability); ?>[<?php echo htmlspecialchars($roleSlug); ?>]"
							id="<?php echo htmlspecialchars($capability . '_' . $roleSlug); ?>"
							<?php echo $disabled; ?>
							<?php echo $checked; ?>
							/>
						<label for="<?php echo htmlspecialchars($capability . '_' . $roleSlug); ?>"><?php echo $name; ?></label>
						<br />

						<?php endforeach; ?>
					<?php endif; ?>

			</td>
		</tr>

		<?php endforeach; ?>

	</table>
</div>