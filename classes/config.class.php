<?php
 
class PrisnaGWTConfig {
	
	const NAME = 'PrisnaGWT';
	const UI_NAME = 'Google Website Translator by Prisna';
	const WIDGET_NAME = 'Prisna GWT';
	const WIDGET_INTERNAL_NAME = 'prisna-google-website-translator';
	const ADMIN_SETTINGS_NAME = 'prisna-google-website-translator-settings';
	const ADMIN_SETTINGS_IMPORT_EXPORT_NAME = 'prisna-google-website-translator-plugin-import-export-settings';
	const DB_SETTINGS_NAME = 'prisna-google-website-translator-settings';
	
	protected static $_settings = null;

	public static function getName($_to_lower=false, $_ui=false) {
		
		if ($_ui)
			return $_to_lower ? strtolower(self::UI_NAME) : self::UI_NAME;
		else
			return $_to_lower ? strtolower(self::NAME) : self::NAME;
		
	}

	public static function getWidgetName($_internal=false) {
	
		return $_internal ? self::WIDGET_INTERNAL_NAME : self::WIDGET_NAME;
		
	}

	public static function getVersion() {
	
		return PRISNA_GWT__VERSION;
		
	}	

	public static function getAdminHandle() {
		
		return self::ADMIN_SETTINGS_NAME;
		
	}

	public static function getAdminImportExportHandle() {
		
		return self::ADMIN_SETTINGS_IMPORT_EXPORT_NAME;
		
	}

	public static function getDbSettingsName() {
		
		return self::DB_SETTINGS_NAME;
		
	}

	protected static function _get_settings() {
		
		$option = get_option(self::getDbSettingsName());
		return !$option ? array() : $option;
		
	}
	
	public static function getSettings($_force=false, $_direct=false) {
		
		if (is_array(self::$_settings) && $_force == false)
			return self::$_settings;
		
		$current = self::_get_settings();

		if ($_direct)
			return $current;

		$defaults = self::getDefaults();

		$result = PrisnaGWTCommon::mergeArrays($defaults, $current);

		$result = self::_adjust_languages($result, $current);
		
		return self::$_settings = $result;
		
	}

	protected static function _adjust_languages($_settings, $_current) {
		
		$result = $_settings;
		
		if (array_key_exists('languages', $_current))
			$result['languages']['value'] = $_current['languages']['value'];
		
		return $result;
		
	}
	
	public static function getSetting($_name, $_force=false) {
		
		$settings = self::getSettings($_force);
		
		return array_key_exists($_name, $settings) ? $settings[$_name] : null;
		
	}

	protected static function _compare_settings($_id, $_setting_1, $_setting_2) {
		
		if (PrisnaGWTCommon::endsWith($_id, '_template') || PrisnaGWTCommon::endsWith($_id, '_template_dd'))
			return PrisnaGWTCommon::stripBreakLinesAndTabs($_setting_1['value']) == PrisnaGWTCommon::stripBreakLinesAndTabs($_setting_2['value']);
		
		if ($_id == 'override')
			if ($_setting_1['value'] != $_setting_2['value'] && PrisnaGWTValidator::isEmpty($_setting_1['value']))
				return true;
		
		if ($_id == 'languages')
			return $_setting_1['value'] === $_setting_2['value'];
			
		return $_setting_1['value'] == $_setting_2['value'];
		
	}
	
	protected static function _get_settings_values_for_export() {
		
		$settings = self::_get_settings();
		
		return count($settings) > 0 ? base64_encode(serialize($settings)) : __('No settings to export. The current settings are the default ones.', 'prisna-gwt');
		
	}
	
	public static function getSettingsValues($_force=false, $_new=true) {
		
		$result = array();
		$settings = self::getSettings($_force);
				
		$defaults = self::getDefaults();
				
		foreach ($settings as $key => $setting) {
		
			if (!array_key_exists($key, $defaults))
				continue;
		
			if ($_new == false || !self::_compare_settings($key, $setting, $defaults[$key])) {
				$result[$key] = array(
					'value' => $setting['value'],
					'option_id' => array_key_exists('option_id', $setting) ? $setting['option_id'] : null
				);
			}
			
		}
			
		return $result;

	}
	
	public static function getSettingValue($_name, $_force=false) {
		
		$setting = self::getSetting($_name, $_force);
		
		if (is_null($setting))
			return null;
		
		$result = $setting['value'];
		
		if (PrisnaGWTValidator::isBool($result))
			$result = $result == 'true' || $result === true;
		
		return $result;
		
	}

	public static function getDefaults($_force=false) {
		
		$settings = self::_get_settings();
		$display_mode = array_key_exists('display_mode', $settings) ? $settings['display_mode']['value'] : 'inline';
		
		$result = array(

			'usage' => array(
				'title_message' => __('Usage', 'prisna-gwt'),
				'description_message' => '',
				'id' => 'prisna_usage',
				'type' => 'usage',
				'value' => $display_mode == 'inline' ? sprintf(__('
				
				- Go to the <em>Appereance &gt; Widgets</em> panel, search for the following widget<br /><br />
				
				<code>%s</code><br /><br />
				
				- Or copy and paste the following code into pages, posts, etc...<br /><br />
				
				<code>[prisna-google-website-translator]</code><br /><br />
				
				- Or copy and paste the following code into any page, post or front end PHP file<br /><br />
				
				<code>&lt;?php echo do_shortcode(\'[prisna-google-website-translator]\'); ?&gt;</code><br />
				
				', 'prisna-gwt'), self::getWidgetName()) : __('
				
				The selected <em>Display mode</em> doesn\'t require any further action.<br />
				The plugin is already active in your website. 
				
				', 'prisna-gwt'),
				'group' => 1
			),
			
			'premium' => array(
				'title_message' => '',
				'description_message' => '',
				'id' => 'prisna_usage',
				'type' => 'premium',
				'value' => '',
				'group' => 4
			),

			'from' => array(
				'title_message' => __('Website\'s language', 'prisna-gwt'),
				'description_message' => __('Sets the website\'s source language.', 'prisna-gwt'),
				'id' => 'prisna_from',
				'option_id' => 'pageLanguage',
				'type' => 'select',
				'values' => PrisnaGWTCommon::getLanguages(),
				'value' => 'en',
				'group' => 1
			),
			
			'all_languages' => array(
				'title_message' => __('Translation languages', 'prisna-gwt'),
				'description_message' => __('Sets the available languages.', 'prisna-gwt'),
				'id' => 'prisna_all_languages',
				'type' => 'toggle',
				'value' => 'true',
				'values' => array(
					'true' => __('Yes, use all languages', 'prisna-gwt'),
					'false' => __('No, choose languages', 'prisna-gwt')
				),
				'group' => 1
			),
			
			'available_languages' => array(
				'title_message' => __('Specific languages', 'prisna-gwt'),
				'description_message' => __('Specifically sets the available languages.', 'prisna-gwt'),
				'id' => 'prisna_available_languages',
				'values' => PrisnaGWTCommon::getLanguages(),
				'value' => array(),
				'type' => 'language',
				'enable_order' => false,
				'columns' => 4,
				'dependence' => 'all_languages',
				'dependence_show_value' => 'false',
				'group' => 1
			),
			
			'display_mode' => array(
				'title_message' => __('Display mode', 'prisna-gwt'),
				'description_message' => __('Sets the display mode. When <code>Automatic</code> is selected, the translation banner will automatically be displayed when the default browser language of the visitor is different from the language of your page. No dropdown will be displayed.', 'prisna-gwt'),
				'id' => 'prisna_display_mode',
				'type' => 'select',
				'values' => array(
					'inline' => __('Inline', 'prisna-gwt'),
					'tabbed' => __('Tabbed', 'prisna-gwt'),
					'automatic' => __('Automatic', 'prisna-gwt')
				),
				'value' => 'inline',
				'group' => 1
			),
			
			'style_inline' => array(
				'title_message' => __('Style mode', 'prisna-gwt'),
				'id' => 'prisna_style_inline',
				'values' => array(
					'vertical' => PRISNA_GWT__IMAGES . '/style_vertical.png',
					'horizontal' => PRISNA_GWT__IMAGES . '/style_horizontal.png',
					'dropdown' => PRISNA_GWT__IMAGES . '/style_dropdown.png'
				),
				'value' => 'vertical',
				'type' => 'visual',
				'col_count' => 3,
				'dependence' => 'display_mode',
				'dependence_show_value' => 'inline',
				'group' => 1
			),
			
			'style_tabbed' => array(
				'title_message' => __('Style mode', 'prisna-gwt'),
				'id' => 'prisna_style_tabbed',
				'values' => array(
					'upper_left' => PRISNA_GWT__IMAGES . '/upper_left.png',
					'upper_right' => PRISNA_GWT__IMAGES . '/upper_right.png',
					'lower_left' => PRISNA_GWT__IMAGES . '/lower_left.png',
					'lower_right' => PRISNA_GWT__IMAGES . '/lower_right.png'
				),
				'value' => 'lower_right',
				'type' => 'visual',
				'col_count' => 2,
				'dependence' => 'display_mode',
				'dependence_show_value' => 'tabbed',
				'group' => 1
			),

			'align_mode' => array(
				'title_message' => __('Align mode', 'prisna-gwt'),
				'description_message' => __('Sets the alignment mode of the translator within its container.', 'prisna-gwt'),
				'id' => 'prisna_align_mode',
				'type' => 'radio',
				'value' => 'left',
				'values' => array(
					'left' => __('Left', 'prisna-gwt'),
					'right' => __('Right', 'prisna-gwt')
				),
				'dependence' => 'display_mode',
				'dependence_show_value' => 'inline',
				'group' => 1
			),
			
			'show_flags' => array(
				'title_message' => __('Show flags over translator', 'prisna-gwt'),
				'description_message' => __('Sets whether to display a few flags over the translator, or not.', 'prisna-gwt'),
				'id' => 'prisna_show_flags',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, show flags', 'prisna-gwt'),
					'false' => __('No, don\'t show flags', 'prisna-gwt')
				),
				'dependence' => 'display_mode',
				'dependence_show_value' => 'inline',
				'group' => 1
			),
			
			'languages' => array(
				'title_message' => __('Select languages', 'prisna-gwt'),
				'description_message' => __('Sets the available languages to display over the translator.', 'prisna-gwt'),
				'title_order_message' => __('Languages order', 'prisna-gwt'),
				'description_order_message' => __('Defines the order to display the languages.', 'prisna-gwt'),
				'id' => 'prisna_languages',
				'values' => PrisnaGWTCommon::getLanguages(),
				'value' => array('en', 'es', 'de', 'fr', 'pt', 'da'),
				'type' => 'language',
				'enable_order' => true,
				'columns' => 4,
				'dependence' => array('show_flags', 'display_mode'),
				'dependence_show_value' => array('true', 'inline'),
				'group' => 1
			),
			
			'test_mode' => array(
				'title_message' => __('Test mode', 'prisna-gwt'),
				'description_message' => __('Sets whether the translator is in test mode or not. In "test mode", the translator will be displayed only if the current logged in user has admin privileges.<br />Is useful for setting up the translator without letting visitors to see the changes while the plugin is being implemented.', 'prisna-gwt'),
				'id' => 'prisna_test_mode',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, enable test mode', 'prisna-gwt'),
					'false' => __('No, disable test mode', 'prisna-gwt')
				),
				'group' => 2
			),

			'custom_css' => array(
				'title_message' => __('Custom CSS', 'prisna-gwt'),
				'description_message' => __('Defines custom CSS rules.', 'prisna-gwt'),
				'id' => 'prisna_custom_css',
				'type' => 'textarea',
				'value' => '',
				'group' => 2
			),

			'display_heading' => array(
				'title_message' => __('Hide on pages, posts and categories', 'prisna-gwt'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_display_heading',
				'type' => 'heading',
				'group' => 2
			),
			
			'exclude_pages' => array(
				'title_message' => __('Pages', 'prisna-gwt'),
				'description_message' => __('Selects the pages where the translator should not be displayed.', 'prisna-gwt'),
				'id' => 'prisna_exclude_pages',
				'value' => array(''),
				'type' => 'expage',
				'dependence' => 'display_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),

			'exclude_posts' => array(
				'title_message' => __('Posts', 'prisna-gwt'),
				'description_message' => __('Selects the posts where the translator should not be displayed.', 'prisna-gwt'),
				'id' => 'prisna_exclude_posts',
				'value' => array(''),
				'type' => 'expost',
				'dependence' => 'display_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'exclude_categories' => array(
				'title_message' => __('Categories', 'prisna-gwt'),
				'description_message' => __('Selects the categories where the translator should not be displayed.', 'prisna-gwt'),
				'id' => 'prisna_exclude_categories',
				'value' => array(''),
				'type' => 'excategory',
				'dependence' => 'display_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'templates_heading' => array(
				'title_message' => __('Templates', 'prisna-gwt'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_templates_heading',
				'type' => 'heading',
				'group' => 2
			),
			
			'flags_container_template' => array(
				'title_message' => __('Flags container template', 'prisna-gwt'),
				'description_message' => __('Sets the flags\' container template. New templates can be created if the provided one doesn\'t fit the web page requirements.', 'prisna-gwt'),
				'id' => 'prisna_flags_container_template',
				'type' => 'textarea',
				'value' => '<ul class="prisna-gwt-flags-container prisna-gwt-align-{{ align_mode }} notranslate">
	{{ content }}
</ul>',
				'dependence' => 'templates_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'flag_template' => array(
				'title_message' => __('Flag template', 'prisna-gwt'),
				'description_message' => __('Sets the flag\'s template. New templates can be created if the provided one doesn\'t fit the web page requirements.', 'prisna-gwt'),
				'id' => 'prisna_flag_template',
				'type' => 'textarea',
				'value' => '<li class="prisna-gwt-flag-container prisna-gwt-language-{{ language_code }}">
	<a href="javascript:;" onclick="PrisnaGWT.translate(\'{{ language_code }}\'); return false;" title="{{ language_name }}"></a>
</li>',
				'dependence' => 'templates_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'other_customizations' => array(
				'title_message' => __('Other customizations', 'prisna-gwt'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_other_customizations_heading',
				'type' => 'heading',
				'group' => 2
			),

			'banner' => array(
				'title_message' => __('Translation banner', 'prisna-gwt'),
				'description_message' => __('Sets whether automatically display translation banner to users speaking languages other than the language of your page, or not. If the <code>Display mode</code> is set to <code>Tabbed</code>, then the <code>Completely hide the translation banner</code> option won\'t be selectable.', 'prisna-gwt'),
				'id' => 'prisna_banner',
				'type' => 'radio',
				'value' => 'hide',
				'values' => array(
					'hide' => __('Completely hide the translation banner', 'prisna-gwt'),
					/*'true' => __('Automatically display translation banner', 'prisna-gwt'), Disabled feature until Google fixes it */
					'false' => __('Don\'t display translation banner automatically', 'prisna-gwt')
				),
				'dependence' => 'other_customizations',
				'dependence_show_value' => 'true',
				'group' => 2
			),

			'multiple_languages' => array(
				'title_message' => __('Multiple languages pages', 'prisna-gwt'),
				'description_message' => __('Sets whether the pages in this site contain content in multiple languages.', 'prisna-gwt'),
				'id' => 'prisna_multiple_languages',
				'option_id' => 'multilanguagePage',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, pages on this site contain multiple languages', 'prisna-gwt'),
					'false' => __('No, pages on this site don\'t contain multiple languages', 'prisna-gwt')
				),
				'dependence' => 'other_customizations',
				'dependence_show_value' => 'true',
				'group' => 2
			),

			'google_analytics' => array(
				'title_message' => __('Google Analytics', 'prisna-gwt'),
				'description_message' => __('Sets whether to track translation traffic using Google Analytics, or not.', 'prisna-gwt'),
				'id' => 'prisna_google_analytics',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, track traffic using Google Analytics', 'prisna-gwt'),
					'false' => __('No, don\'t track traffic using Google Analytics', 'prisna-gwt')
				),
				'dependence' => 'other_customizations',
				'dependence_show_value' => 'true',
				'group' => 2
			),
			
			'google_analytics_code' => array(
				'title_message' => __('Google Analytics code', 'prisna-gwt'),
				'description_message' => __('Sets the  Analytics Web Property ID. For instance: UA-12345-12', 'prisna-gwt'),
				'id' => 'prisna_google_analytics_code',
				'type' => 'text',
				'value' => '',
				'dependence' => array('other_customizations', 'google_analytics'),
				'dependence_show_value' => array('true', 'true'),
				'group' => 2
			),
			
			'import' => array(
				'title_message' => __('Import settings', 'prisna-gwt'),
				'description_message' => __('Imports previously exported settings. Paste the previously exported settings in the field. If the data\'s structure is correct, it will overwrite the current settings.', 'prisna-gwt'),
				'id' => 'prisna_import',
				'value' => '',
				'type' => 'textarea',
				'group' => 3
			),

			'export' => array(
				'title_message' => __('Export settings', 'prisna-gwt'),
				'description_message' => __('Exports the current settings to make a backup or to transfer the settings from the development server to the production server. Triple click on the field to select all the content.', 'prisna-gwt'),
				'id' => 'prisna_export',
				'value' => self::_get_settings_values_for_export(),
				'type' => 'export',
				'group' => 3
			)
			
		);
			
		
		return $result;
		
	}

}

?>
