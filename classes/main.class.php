<?php

class PrisnaGWT {

	public static function initialize() {

		add_shortcode(PrisnaGWTConfig::getWidgetName(true), array('PrisnaGWT', '_create_shortcode'));
		add_action('wp_footer', array('PrisnaGWT', '_auto_initialize'));

	}

	public static function _auto_initialize() {

		if (!self::isAvailable())
			return;

		$display_mode = PrisnaGWTConfig::getSettingValue('display_mode');
		
		if ($display_mode != 'tabbed')
			return;

		echo do_shortcode('[' . PrisnaGWTConfig::getWidgetName(true) . ']');
		
	}
	
	public static function _create_shortcode() {

		if (!self::isAvailable())
			return;

		$settings = PrisnaGWTConfig::getSettingsValues();

		$translator = new PrisnaGWTOutput((object) $settings);

		return $translator->render(array(
			'type' => 'file',
			'content' => '/main.tpl'
		));
		
	}
	
	public static function isAvailable() {

		if (is_admin())
			return false;

		if (PrisnaGWTConfig::getSettingValue('test_mode') == 'true' && !current_user_can('administrator'))
			return false;

		global $post;
		
		if (!is_object($post))
			return true;
		
		$settings = PrisnaGWTConfig::getSettingsValues();
		
		if ($post->post_type == 'page' && array_key_exists('exclude_pages', $settings)) {
		
			$pages = $settings['exclude_pages']['value'];
		
			if (in_array($post->ID, $pages))
				return false;
		
		}

		if ($post->post_type == 'post' && array_key_exists('exclude_posts', $settings)) {
		
			$posts = $settings['exclude_posts']['value'];
		
			if (in_array($post->ID, $posts))
				return false;
		
		}
		
		if ($post->post_type == 'post' && array_key_exists('exclude_categories', $settings)) {
		
			$categories = $settings['exclude_categories']['value'];
		
			$post_categories = wp_get_post_categories($post->ID);

			if (PrisnaGWTCommon::inArray($categories, $post_categories))
				return false;
		
		}
		
		return true;
		
	}
	
}

class PrisnaGWTOutput extends PrisnaGWTItem {
	
	protected static $_rendered;

	public $custom_css;
	public $flags_css;
	public $flags_image_path;
	public $flags_formatted;
	public $options_formatted;
	
	protected static $_exclude_rules;
	
	public function __construct($_properties) {

		$this->_properties = $_properties;
		$this->_gen_options();
		$this->_set_properties();
		$this->_set_flags_css();
		self::_set_rendered(false);

	}
	
	public function setProperty($_property, $_value) {

		return $this->{$_property} = $_value['value'];

	}
	
	protected static function _set_rendered($_state) {
		
		if (self::_get_rendered() === true)
			return;
		
		self::$_rendered = $_state;
		
	}
	
	protected function _set_flags_css() {
		
		if ($this->_has_flags()) {
			
			$this->flags_image_path = PRISNA_GWT__IMAGES;
			
			$languages = PrisnaGWTConfig::getSettingValue('languages');
			
			if (!PrisnaGWTConfig::getSettingValue('all_languages')) {
				$available_languages = PrisnaGWTConfig::getSettingValue('available_languages');
				$languages = array_intersect($languages, $available_languages);
			}
			
			$all_languages = PrisnaGWTCommon::getLanguages(false);
			$flags_css = array();

			$ix = 0;

			$box_x = 0;
			$box_y = 0;
			$box_width = 22;
			$box_height = 16;
			
			foreach ($all_languages as $language => $name) {
				
				$ix++;

				if (in_array($language, $languages))
					$flags_css[] = '.prisna-gwt-language-' . $language . ' a { background-position: -' . $box_x . 'px -' . $box_y . 'px !important; }';
				
				if ($ix % 10 == 0) {
					$box_x = 0;
					$box_y += $box_height;
				}
				else
					$box_x += $box_width;
				
			}
			
			$this->flags_css = implode("\n", $flags_css);
			
		}

	}
	
	protected static function _get_rendered() {
		
		return self::$_rendered;
		
	}
	
	public function _prepare_option_value($_id, $_value) {
		
		$value = $_value;
				
		if (PrisnaGWTValidator::isBool($value))
			$value = $value == 'true' || $value === true;
			
		if ($_id == 'layout')
			$value = array('type' => 'literal', 'value' => $value);

		return PrisnaGWTFastJSON::encode($value);
		
	}
	
	public function render($_options, $_html_encode=false) {
		
		if (self::_get_rendered())
			return '';
		
		if (!array_key_exists('meta_tag_rules', $_options))
			$_options['meta_tag_rules'] = array();

		$_options['meta_tag_rules'][] = array(
			'expression' => !property_exists($this->_properties, 'layout') || $this->_properties->layout['option_id'] == 'layout',
			'tag' => 'has_container'
		);
		
		$_options['meta_tag_rules'][] = array(
			'expression' => $this->_has_flags(),
			'tag' => 'has_flags'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => PrisnaGWTConfig::getSettingValue('display_mode') == 'inline',
			'tag' => 'is_inline'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => PrisnaGWTConfig::getSettingValue('banner') === 'hide',
			'tag' => 'hide_banner'
		);

		self::_set_rendered(true);

		return parent::render($_options, $_html_encode);
		
	}
	
	protected function _has_flags() {

		$display_mode = PrisnaGWTConfig::getSettingValue('display_mode');
		
		if ($display_mode != 'inline')
			return false;

		$style_inline = PrisnaGWTConfig::getSettingValue('style_inline');

		if ($style_inline == 'dropdown')
			return false;

		$show_flags = PrisnaGWTConfig::getSettingValue('show_flags');
		
		if (!$show_flags)
			return false;
	
		$languages = PrisnaGWTConfig::getSettingValue('languages');
		
		if (empty($languages))
			return false;
			
		return true;
		
	}
	
	protected function _gen_flags() {

		if (!$this->_has_flags())
			return;
			
		$flags_container_template = PrisnaGWTConfig::getSettingValue('flags_container_template');
		$flag_template = PrisnaGWTConfig::getSettingValue('flag_template');

		$languages = PrisnaGWTConfig::getSettingValue('languages');
		
		if (!PrisnaGWTConfig::getSettingValue('all_languages')) {
			$available_languages = PrisnaGWTConfig::getSettingValue('available_languages');
			$languages = array_intersect($languages, $available_languages);
		}

		$flags_items = array();
		
		foreach ($languages as $language)
			$flags_items[] = array(
				'language_code' => $language,
				'language_name' => PrisnaGWTCommon::getLanguage($language),
				'language_name_no_space' => PrisnaGWTCommon::getLanguage($language, '_'),
				'flags_path' => PRISNA_GWT__IMAGES . '/'
			);
		
		$flags = PrisnaGWTCommon::renderObject($flags_items, array(
			'type' => 'html',
			'content' => $flag_template
		));

		$result = array(
			'content' => $flags,
			'align_mode' => $this->align_mode
		);
		
		$this->flags_formatted = PrisnaGWTCommon::renderObject((object) $result, array(
			'type' => 'html',
			'content' => $flags_container_template
		));
		
	}
	
	protected function _gen_banner() {
	
		$banner = PrisnaGWTConfig::getSettingValue('banner');
		
		if ($banner !== false)
			return;
			
		$this->_properties->banner = array(
			'option_id' => 'autoDisplay',
			'value' => false
		);
		
	}
	
	protected function _gen_google_analytics() {
	
		$google_analytics = PrisnaGWTConfig::getSettingValue('google_analytics');
		
		if (!$google_analytics)
			return;
			
		$google_analytics_code = PrisnaGWTConfig::getSettingValue('google_analytics_code');
		
		if (empty($google_analytics_code))
			return;
			
		$this->_properties->google_analytics = array(
			'option_id' => 'gaTrack',
			'value' => true
		);

		$this->_properties->google_analytics_code = array(
			'option_id' => 'gaId',
			'value' => $google_analytics_code
		);
		
	}
	
	protected function _gen_languages() {
		
		$all_languages = PrisnaGWTConfig::getSettingValue('all_languages');
		
		if ($all_languages)
			return;
			
		$available_languages = PrisnaGWTConfig::getSettingValue('available_languages');
		
		if (empty($available_languages))
			return;
			
		$this->_properties->selected_languages = array(
			'option_id' => 'includedLanguages',
			'value' => join(',', $available_languages)
		);
		
	}
	
	protected function _gen_layout() {
		
		$settings = array(
			'inline_vertical' => '',
			'inline_horizontal' => 'InlineLayout.HORIZONTAL',
			'inline_dropdown' => 'InlineLayout.SIMPLE',
			'tabbed_upper_left' => 'FloatPosition.TOP_LEFT',
			'tabbed_upper_right' => 'FloatPosition.TOP_RIGHT',
			'tabbed_lower_left' => 'FloatPosition.BOTTOM_LEFT',
			'tabbed_lower_right' => 'FloatPosition.BOTTOM_RIGHT',
			'automatic' => 'FloatPosition.TOP_LEFT'
		);
		
		$display_mode = PrisnaGWTConfig::getSettingValue('display_mode');
		$option_id = $display_mode == 'inline' ? 'layout' : 'floatPosition';
		
		if ($display_mode != 'automatic')
			$display_mode .= '_' . PrisnaGWTConfig::getSettingValue('style_' . $display_mode);

		$result = $settings[$display_mode];

		if (empty($result))
			return;

		$this->_properties->layout = array(
			'option_id' => $option_id,
			'value' => 'google.translate.TranslateElement.' . $result
		);

	}
	
	protected function _gen_options() {

		$this->align_mode = PrisnaGWTConfig::getSettingValue('align_mode');

		$this->_gen_layout();
		$this->_gen_languages();
		$this->_gen_google_analytics();
		$this->_gen_banner();
		$this->_gen_flags();

		$result = array();

		foreach ($this->_properties as $key => $property)
			if (array_key_exists('option_id', $property) && !PrisnaGWTValidator::isEmpty($property['option_id']))
				$result[$key] = array(
					'option_id' => $property['option_id'],
					'value' => $this->_prepare_option_value($key, $property['value'])
				);
		
		$this->options_formatted = PrisnaGWTCommon::renderObject($result, array(
			'type' => 'html',
			'content' => "\t\t{{ option_id }}: {{ value }},\n"
		));

		$this->options_formatted = preg_replace('/,\n$/', "\n", $this->options_formatted);

	}
	
}

PrisnaGWT::initialize();

?>
