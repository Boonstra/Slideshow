<?php

/* ==== ==== ==== Capabilities ==== ==== ==== */

// Roles
global $wp_roles;

// Capabilities
$capabilities = array(
	SlideshowPluginGeneralSettings::$capabilities['addSlideshows'] => __('Add slideshows', 'slideshow-plugin'),
	SlideshowPluginGeneralSettings::$capabilities['editSlideshows'] => __('Edit slideshows', 'slideshow-plugin'),
	SlideshowPluginGeneralSettings::$capabilities['deleteSlideshows'] => __('Delete slideshows', 'slideshow-plugin')
);

/* ==== ==== ==== Default settings ==== ==== ==== */

// Default settings
$defaultSettings = SlideshowPluginSlideshowSettingsHandler::getDefaultSettings(true);
$defaultStyleSettings = SlideshowPluginSlideshowSettingsHandler::getDefaultStyleSettings(true);

/* ==== ==== ==== Custom styles ==== ==== ==== */

// Get default stylesheets
$defaultStyles = array();
$defaultStylesheets = array(
	'style-light.css' => __('Light', 'slideshow-plugin'),
	'style-dark.css' => __('Dark', 'slideshow-plugin')
);
$stylesheetsFilePath = SlideshowPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'SlideshowPlugin';
foreach($defaultStylesheets as $fileName => $name){
	if(file_exists($stylesheetsFilePath . DIRECTORY_SEPARATOR . $fileName)){
		ob_start();
		include $stylesheetsFilePath . DIRECTORY_SEPARATOR . $fileName;
		$defaultStyles[$fileName] = array(
			'name' => $name,
			'style' => ob_get_clean()
		);
	}
}

// Custom styles
$customStyleKeys = get_option(SlideshowPluginGeneralSettings::$customStyles, array());
$customStyleKeys = array(
	'slideshow-jquery-image-gallery-custom-style_0' => 'Name',
	'slideshow-jquery-image-gallery-custom-style_1' => 'Other name'
);

// Get custom styles
$customStyleValues = array();
if(is_array($customStyleKeys)){
	foreach($customStyleKeys as $customStyleKey => $customStyleKeyName){

		// Get custom style value from custom style key
		$customStyleValues[$customStyleKey] = get_option($customStyleKey);
	}
}
$customStyleValues = array(
	'slideshow-jquery-image-gallery-custom-style_0' => 'Style 1',
	'slideshow-jquery-image-gallery-custom-style_1' => 'Style 2'
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

					<?php foreach($defaultStyles as $defaultStyleKey => $defaultStyleValues): ?>

					<?php if(!isset($defaultStyleValues['style']) || empty($defaultStyleValues['style'])) continue; // Continue if style is not set or empty ?>

					<li>
						<span class="style-title"><?php echo (isset($defaultStyleValues['name'])) ? htmlspecialchars($defaultStyleValues['name']) : __('Untitled'); ?></span>
						<span
							class="style-action style-default <?php htmlspecialchars($defaultStyleKey); ?>"
							title="<?php _e('Create a new custom style from this style', 'slideshow-plugin'); ?>"
						>
							<?php _e('Customize', 'slideshow-plugin'); ?> &raquo;
						</span>

						<p style="clear: both;"></p>

						<span class="style-content" style="display: none;"><?php echo htmlspecialchars($defaultStyleValues['style']); ?></span>
					</li>

					<?php endforeach; ?>

				</ul>

				<b>Custom styles</b>

				<ul style="">

					<?php foreach($customStyleKeys as $customStyleKey => $customStyleKeyName): ?>

					<li>
						<span class="style-title"><?php echo htmlspecialchars($customStyleKeyName); ?></span>

						<span
							class="style-action <?php echo htmlspecialchars($customStyleKey); ?>"
							title="<?php _e('Edit this style', 'slideshow-plugin'); ?>"
						>
							<?php _e('Edit', 'slideshow-plugin'); ?> &raquo;
						</span>

						<span style="float: right;">&#124;</span>

						<span
							class="style-delete <?php echo htmlspecialchars($customStyleKey); ?>"
							title="<?php _e('Delete this style', 'slideshow-plugin'); ?>"
						>
							<?php _e('Delete', 'slideshow-plugin'); ?>
						</span>

						<p style="clear: both;"></p>
					</li>

					<?php endforeach; ?>

				</ul>

			</div>

			<div class="style-editors">

				<b><?php _e('Custom style editor', 'slideshow-plugin'); ?></b>

				<p class="style-editor">
					<?php _e('Select a style from the left to start customizing.', 'slideshow-plugin'); ?>
				</p>

				<?php foreach($customStyleValues as $customStyleKey => $customStyleValue): ?>

				<div class="style-editor <?php echo htmlspecialchars($customStyleKey); ?>" style="display: none;">

					<p>
						<i><?php _e('Name', 'slideshow-plugin'); ?></i><br />
						<input
							type="text"
							name="<?php echo SlideshowPluginGeneralSettings::$customStyles; ?>[<?php echo htmlspecialchars($customStyleKey); ?>][title]"
						    value="<?php echo (isset($customStyleKeys[$customStyleKey]) && !empty($customStyleKeys[$customStyleKey])) ? $customStyleKeys[$customStyleKey] : __('Untitled', 'slideshow-plugin'); ?>"
						/>
					</p>

					<p>
						<i><?php _e('Editor', 'slideshow-plugin'); ?></i><br />
						<textarea
							name="<?php echo SlideshowPluginGeneralSettings::$customStyles; ?>[<?php echo htmlspecialchars($customStyleKey); ?>][style]"
							rows="20"
							cols=""
						><?php echo htmlspecialchars($customStyleValue); ?></textarea>
					</p>

				</div>

				<?php endforeach; ?>

			</div>

			<div style="clear: both;"></div>

			<div class="style-editor-template" style="display: none;">
				<div class="style-editor">

					<p>
						<i><?php _e('Name', 'slideshow-plugin'); ?></i><br />
						<input
							type="text"
							class="title"
						/>
					</p>

					<p>
						<i><?php _e('Editor', 'slideshow-plugin'); ?></i><br />
						<textarea
							class="style"
							rows="20"
							cols=""
						></textarea>
					</p>

				</div>
			</div>

		</div>

		<?php submit_button(); ?>
	</form>
</div>