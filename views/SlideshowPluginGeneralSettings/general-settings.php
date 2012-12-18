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

<div class="wrap">

	<div class="icon32" style="background: url('<?php echo SlideshowPluginMain::getPluginUrl() . '/images/SlideshowPluginPostType/adminIcon32.png'; ?>');"></div>
	<h2 class="nav-tab-wrapper">
		<a href="#user-capabilities" class="nav-tab nav-tab-active"><?php _e('User Capabilities', 'slideshow-plugin'); ?></a>
<!--		<a href="#default-slideshow-settings" class="nav-tab">--><?php //_e('Default Slideshow Values', 'slideshow-plugin'); ?><!--</a>-->
<!--		<a href="#custom-styles" class="nav-tab">--><?php //_e('Custom Styles', 'slideshow-plugin'); ?><!--</a>-->
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields(SlideshowPluginGeneralSettings::$settingsGroup); ?>

		<!-- ==== ==== User capabilities ==== ==== -->
		<table class="form-table user-capabilities">

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

		<!-- ==== ==== Defaults slideshow settings ==== ==== -->
        <table class="form-table default-slideshow-settings" style="display: none;">

        </table>

		<!-- ==== ==== Custom styles ==== ==== -->
		<table class="form-table custom-styles" style="display: none;">

		</table>

		<?php submit_button(); ?>
	</form>
</div>