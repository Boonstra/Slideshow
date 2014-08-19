<?php

// General settings
$stylesheetLocation = SlideshowPluginGeneralSettings::getStylesheetLocation();

// Roles
global $wp_roles;

// Capabilities
$capabilities = array(
	SlideshowPluginGeneralSettings::$capabilities['addSlideshows']          => array('name' => __('Add slideshows', 'slideshow-plugin')          , 'dependsOn' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows']),
	SlideshowPluginGeneralSettings::$capabilities['editSlideshows']         => array('name' => __('Edit slideshows', 'slideshow-plugin')         , 'dependsOn' => ''),
	SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows']       => array('name' => __('Delete slideshows', 'slideshow-plugin')       , 'dependsOn' => SlideshowPluginGeneralSettings::$capabilities['editSlideshows']),
	SlideshowPluginGeneralSettings::$capabilities['addSettingsProfiles']    => array('name' => __('Add settings profiles', 'slideshow-plugin')   , 'dependsOn' => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles']),
	SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles']   => array('name' => __('Edit settings profiles', 'slideshow-plugin')  , 'dependsOn' => ''),
	SlideshowPluginGeneralSettings::$capabilities['deleteSettingsProfiles'] => array('name' => __('Delete settings profiles', 'slideshow-plugin'), 'dependsOn' => SlideshowPluginGeneralSettings::$capabilities['editSettingsProfiles']),
	SlideshowPluginGeneralSettings::$capabilities['addStyles']              => array('name' => __('Add styles', 'slideshow-plugin')              , 'dependsOn' => SlideshowPluginGeneralSettings::$capabilities['editStyles']),
	SlideshowPluginGeneralSettings::$capabilities['editStyles']             => array('name' => __('Edit styles', 'slideshow-plugin')             , 'dependsOn' => ''),
	SlideshowPluginGeneralSettings::$capabilities['deleteStyles']           => array('name' => __('Delete styles', 'slideshow-plugin')           , 'dependsOn' => SlideshowPluginGeneralSettings::$capabilities['editStyles']),
);

?>

<div class="general-settings-tab feature-filter">

	<h4><?php _e('User Capabilities', 'slideshow-plugin'); ?></h4>

	<p><?php _e('Select the user roles that will able to perform certain actions.', 'slideshow-plugin');  ?></p>

	<table>

		<?php foreach($capabilities as $capability => $capabilityValues): ?>

		<tr valign="top">
			<th><?php echo $capabilityValues['name']; ?></th>
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
							class="general-settings-capability-checkbox"
							data-role="<?php echo htmlspecialchars($roleSlug); ?>"
							data-depends-on="<?php echo htmlspecialchars($capabilityValues['dependsOn']); ?>"
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

	<h4><?php _e('Settings', 'slideshow-plugin'); ?></h4>

	<table>
		<tr>
			<td><?php _e('Stylesheet location', 'slideshow-plugin'); ?></td>
			<td>
				<select name="<?php echo SlideshowPluginGeneralSettings::$stylesheetLocation; ?>">
					<option value="head" <?php selected('head', $stylesheetLocation); ?>>Head (<?php _e('top', 'slideshow-plugin'); ?>)</option>
					<option value="footer" <?php selected('footer', $stylesheetLocation); ?>>Footer (<?php _e('bottom', 'slideshow-plugin'); ?>)</option>
				</select>
			</td>
		</tr>
	</table>

</div>