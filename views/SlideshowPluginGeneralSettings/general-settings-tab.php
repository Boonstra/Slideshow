<?php

if ($data instanceof stdClass) :

	// General settings
	$stylesheetLocation = SlideshowPluginGeneralSettings::getStylesheetLocation();
	$enableLazyLoading  = SlideshowPluginGeneralSettings::getEnableLazyLoading();

	// Roles
	global $wp_roles;

	// Capabilities
	$capabilities = array(
		SlideshowPluginGeneralSettings::$capabilities['addSlideshows']    => __('Add slideshows', 'slideshow-jquery-image-gallery'),
		SlideshowPluginGeneralSettings::$capabilities['editSlideshows']   => __('Edit slideshows', 'slideshow-jquery-image-gallery'),
		SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'] => __('Delete slideshows', 'slideshow-jquery-image-gallery')
	);

	?>

	<div class="general-settings-tab feature-filter">

		<h4><?php _e('User Capabilities', 'slideshow-jquery-image-gallery'); ?></h4>

		<p><?php _e('Select the user roles that will able to perform certain actions.', 'slideshow-jquery-image-gallery');  ?></p>

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
							$name = (isset($values['name'])) ? htmlspecialchars($values['name']) : __('Untitled role', 'slideshow-jquery-image-gallery');

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

	<div class="general-settings-tab feature-filter">

		<h4><?php _e('Settings', 'slideshow-jquery-image-gallery'); ?></h4>

		<table>
			<tr>
				<td><?php _e('Stylesheet location', 'slideshow-jquery-image-gallery'); ?></td>
				<td>
					<select name="<?php echo SlideshowPluginGeneralSettings::$stylesheetLocation; ?>">
						<option value="head" <?php selected('head', $stylesheetLocation); ?>>Head (<?php _e('top', 'slideshow-jquery-image-gallery'); ?>)</option>
						<option value="footer" <?php selected('footer', $stylesheetLocation); ?>>Footer (<?php _e('bottom', 'slideshow-jquery-image-gallery'); ?>)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php _e('Enable lazy loading', 'slideshow-jquery-image-gallery'); ?></td>
				<td>
					<input type="radio" name="<?php echo SlideshowPluginGeneralSettings::$enableLazyLoading; ?>" <?php checked(true, $enableLazyLoading); ?> value="true" /> <?php _e('Yes', 'slideshow-jquery-image-gallery'); ?>
					<input type="radio" name="<?php echo SlideshowPluginGeneralSettings::$enableLazyLoading; ?>" <?php checked(false, $enableLazyLoading); ?> value="false" /> <?php _e('No', 'slideshow-jquery-image-gallery'); ?>
				</td>
			</tr>
		</table>

	</div>
<?php endif; ?>