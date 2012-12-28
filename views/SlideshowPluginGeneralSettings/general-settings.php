<?php

// Roles
global $wp_roles;

// Capabilities
$capabilities = array(
	SlideshowPluginGeneralSettings::$capabilities['addSlideshows'] => __('Add slideshows', 'slideshow-plugin'),
	SlideshowPluginGeneralSettings::$capabilities['editSlideshows'] => __('Edit slideshows', 'slideshow-plugin'),
	SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'] => __('Delete slideshows', 'slideshow-plugin')
);

// Default settings
$defaultSettings = SlideshowPluginSlideshowSettingsHandler::getDefaultSettings(true);
$defaultStyleSettings = SlideshowPluginSlideshowSettingsHandler::getDefaultStyleSettings(true);

// Custom styles
$customStyles = get_option(SlideshowPluginGeneralSettings::$customStyles, array());
$customStyles = array(
	'slideshow-jquery-image-gallery-custom-style_0' => 'Name',
	'slideshow-jquery-image-gallery-custom-style_1' => 'Other name'
);
?>

<div class="wrap">

	<form method="post" action="options.php">
		<?php settings_fields(SlideshowPluginGeneralSettings::$settingsGroup); ?>

		<div class="icon32" style="background: url('<?php echo SlideshowPluginMain::getPluginUrl() . '/images/SlideshowPluginPostType/adminIcon32.png'; ?>');"></div>
		<h2 class="nav-tab-wrapper">
			<a href="#user-capabilities" class="nav-tab nav-tab-active"><?php _e('User Capabilities', 'slideshow-plugin'); ?></a>
			<a href="#default-slideshow-settings" class="nav-tab"><?php _e('Default Slideshow Settings', 'slideshow-plugin'); ?></a>
			<a href="#custom-styles" class="nav-tab"><?php _e('Custom Styles', 'slideshow-plugin'); ?></a>

			<?php submit_button(null, 'primary', null, false, 'style="float: right;"'); ?>
		</h2>

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
		<table class="feature-filter default-slideshow-settings" style="display: none;">

			<tr>
				<td colspan="2">
					<h3><?php _e('Default Slideshow Settings', 'slideshow-plugin'); ?></h3>
				</td>
			</tr>

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

		<table class="feature-filter default-slideshow-settings" style="display: none;">

			<tr>
				<td colspan="2">
					<h3><?php _e('Default Slideshow Style', 'slideshow-plugin'); ?></h3>
				</td>
			</tr>

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

		<!-- ==== ==== Custom styles ==== ==== -->
		<div class="custom-styles feature-filter" style="display: none;">
			<div class="styles-list">

				<b>Default styles</b>

				<ul>

					<li>
						<p class="style-title">Light</p>
						<p class="style-action">Customize &raquo;</p>

						<p style="clear: both;"></p>
					</li>

					<li>
						<p class="style-title">Dark</p>
						<p class="style-action">Customize &raquo;</p>

						<p style="clear: both;"></p>
					</li>

				</ul>

				<b>Custom styles</b>

				<ul style="">

					<?php foreach($customStyles as $customStyleSlug => $customStyleName): ?>

					<li>
						<p class="style-title"><?php echo htmlspecialchars($customStyleName); ?></p>
						<p class="style-action">Edit &raquo;</p>

						<p style="clear: both;"></p>
					</li>

					<?php endforeach; ?>

				</ul>

			</div>

			<div class="styles-editor">

				<b>Other stuff, editor and crap.</b>

				<?php // TODO: Place all custom styles here as hidden text area's. ?>

			</div>

			<div style="clear: both;"></div>
		</div>

		<div style="clear: both;"></div>

		<?php submit_button(); ?>
	</form>
</div>