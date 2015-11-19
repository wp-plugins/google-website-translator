<?php

abstract class PrisnaGWTItem {

	public $collection_item_index;
	protected $_properties;

	public function __construct($_properties=null) {

		if (is_object($_properties)) {
			$this->_properties = $_properties;
			$this->_set_properties();
		}

	}

	protected function _set_properties() {

		$this->setProperties($this->_properties);

	}

	public function getProperty($_property, $_html_entities=false) {

		return !$_html_entities ? $this->{$_property} : htmlentities($this->{$_property});

	}

	public function setProperties($_properties) {

		if (!is_null($_properties))
			foreach($_properties as $property => $value) 
				$this->setProperty($property, $value);

	}

	public function setProperty($_property, $_value) {

		return $this->{$_property} = $_value;

	}

	public function render($_options, $_html_encode=false) {

		if (array_key_exists('extra', $_options))
			if (array_key_exists('json', $_options['extra']))
				if ($_options['extra']['json'])
					$this->_json();

		if (array_key_exists('extra', $_options))
			if (array_key_exists('property', $_options['extra']))
				foreach ($_options['extra']['property'] as $property => $value)
					$this->{$property} = $value;

		$result = PrisnaGWTCommon::renderObject($this, $_options, $_html_encode);

		return $result;

	}

	protected function _json() {

		// seems like there is some kind of bug in apache, so the field names have to be grabbed like this
		$fields = array();

		foreach ($this as $property => $value)
			if (!PrisnaGWTCommon::endsWith($property, '_json'))
				$fields[] = $property;

		foreach ($fields as $value)
			if (substr($value, 0, 1) != '_')
				$this->setProperty($value . '_json', PrisnaGWTCommon::jsonCompatible($this->getProperty($value)));

	}

}

abstract class PrisnaGWTField {

	public $id;
	public $option_id;
	public $value;
	public $dependence;
	public $dependence_show_value;

	public $title_message;
	public $description_message;

	public $dependence_count;

	public $formatted_dependence;
	public $formatted_dependence_show_value;

	protected $_dependence;

	public function __construct($_properties) {

		$this->_set_properties($_properties);

	}

	protected function _set_properties($_properties) {

		foreach ($_properties as $property => $value)
			$this->{$property} = $value;

	}

	public function satisfyDependence($_fields) {

		if (PrisnaGWTValidator::isEmpty($this->dependence))
			return;

		$this->_dependence = PrisnaGWTCommon::getArrayItems($this->dependence, $_fields);
		if (is_null($this->dependence_count))
			$this->dependence_count = count($this->_dependence);

	}

	protected function _has_dependence() {

		return !is_null($this->dependence) && !PrisnaGWTValidator::isEmpty($this->dependence);

	}

	protected function _dependence_show() {

		if (!is_array($this->_dependence))
			return true;

		$result = array();

		if (is_array($this->dependence_show_value)) {
			if (count($this->dependence_show_value) == count($this->_dependence)) {
				$keys = array_keys($this->_dependence);
				for ($i=0; $i<count($keys); $i++) {
					$field = $this->_dependence[$keys[$i]];
					if ($field->value == $this->dependence_show_value[$i])
						$result[] = $field->id;
				}
				return count($result) == count($this->_dependence);
			}
		}

		foreach ($this->_dependence as $field)
			if (PrisnaGWTCommon::inArray($field->value, $this->dependence_show_value))
				$result[] = $field->id;

		return count($result) == count($this->_dependence);


	}

	protected function _get_formatted_dependence() {

		$result = array();

		if (!$this->_has_dependence())
			return '';

		foreach ($this->_dependence as $field)
			$result[] = $field->id;

		return implode(',', $result);

	}

	protected function _render($_options, $_html_encode) {
		
		$this->formatted_dependence = is_array($this->dependence) ? implode(',', $this->dependence) : $this->dependence;
		$this->formatted_dependence_show_value = is_array($this->dependence_show_value) ? implode(',', $this->dependence_show_value) : $this->dependence_show_value;

		$options = $_options;

		if (!array_key_exists('meta_tag_rules', $options))
			$options['meta_tag_rules'] = array();

		$options['meta_tag_rules'][] = array(
			'expression' => !empty($this->title_message),
			'tag' => 'title'
		);

		$options['meta_tag_rules'][] = array(
			'expression' => !empty($this->description_message),
			'tag' => 'description'
		);

		$options['meta_tag_rules'][] = array(
			'expression' => $this->_has_dependence(),
			'tag' => 'has_dependence'
		);

		$options['meta_tag_rules'][] = array(		
			'expression' => $this->_dependence_show(),
			'tag' => 'dependence.show'
		);

		$result = PrisnaGWTCommon::renderObject($this, $options, $_html_encode);

		return $result;
		
	}

	public function output($_html_encode=false) {
		
	}

	public function render($_options, $_html_encode=false) {

		return $this->_render($_options, $_html_encode);

	}

}

class PrisnaGWTColorField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/color.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTTextField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/text.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTPremiumField extends PrisnaGWTField {

	public $images_path;

	public function output($_html_encode=false) {

		$this->images_path = PRISNA_GWT__IMAGES;

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/premium.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => time() < strtotime('2015-12-02 00:00:00'),
					'tag' => 'black_friday'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTUsageField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/usage.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTHeadingField extends PrisnaGWTField {

	public $group;

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/heading.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->value == "true",
					'tag' => 'value'
				),
				array(
					'expression' => !PrisnaGWTValidator::isEmpty($this->description_message),
					'tag' => 'description'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTHeading2Field extends PrisnaGWTField {

	public $group;

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/heading_2.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->value == "true",
					'tag' => 'value'
				),
				array(
					'expression' => !PrisnaGWTValidator::isEmpty($this->description_message),
					'tag' => 'description'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTSitemapviewField extends PrisnaGWTField {

	public $xml_url;
	public $csv_url;
	
	public $permalink_structure_empty_message;

	public function __construct($_properties) {

		$this->xml_url = PrisnaGWTCommon::getHomeUrl('/' . PrisnaGWTConfig::getSitemapFilename() . '.xml');
		$this->csv_url = PrisnaGWTCommon::getHomeUrl('/' . PrisnaGWTConfig::getSitemapFilename() . '.csv');

		parent::__construct($_properties);
			
	}

	public function output($_html_encode=false) {

		$permalink_structure = get_option('permalink_structure');

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/sitemap_view.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => !PrisnaGWTValidator::isEmpty($this->description_message),
					'tag' => 'description'
				),
				array(
					'expression' => empty($permalink_structure),
					'tag' => 'permalink_structure.empty'
				)
			)
		), $_html_encode);

		return $result;

	}	
	
}

class PrisnaGWTRuleField extends PrisnaGWTField {

	public $priority;
	public $changefreq;
	public $lastmod;
	public $rule;

	public $priority_formatted;
	public $changefreq_formatted;
	public $lastmod_formatted;
	public $rule_formatted;
	
	public $changefreq_message;
	public $priority_message;

	public function __construct($_properties) {

		parent::__construct($_properties);

		$this->priority = new PrisnaGWTSelectField(array(
			'title_message' => '',
			'description_message' => '',
			'id' => $this->id . '_priority',
			'type' => 'select',
			'values' => array(
				'0.1' => '0.1',
				'0.2' => '0.2',
				'0.3' => '0.3',
				'0.4' => '0.4',
				'0.5' => '0.5',
				'0.6' => '0.6',
				'0.7' => '0.7',
				'0.8' => '0.8',
				'0.9' => '0.9',
				'1' => '1'
			),
			'value' => $this->value['priority']
		));

		$this->changefreq = new PrisnaGWTSelectField(array(
			'title_message' => '',
			'description_message' => '',
			'id' => $this->id . '_changefreq',
			'type' => 'select',
			'values' => array(
				'always' => PrisnaGWTMessage::get('changefreq_always'),
				'hourly' => PrisnaGWTMessage::get('changefreq_hourly'),
				'daily' => PrisnaGWTMessage::get('changefreq_daily'),
				'weekly' => PrisnaGWTMessage::get('changefreq_weekly'),
				'monthly' => PrisnaGWTMessage::get('changefreq_monthly'),
				'yearly' => PrisnaGWTMessage::get('changefreq_yearly'),
				'never' => PrisnaGWTMessage::get('changefreq_never')
			),
			'value' => $this->value['changefreq']
		));

	}

	public function output($_html_encode=false) {

		$this->priority_formatted = $this->priority->output('select_raw.tpl');
		$this->changefreq_formatted = $this->changefreq->output('select_raw.tpl');

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/rule.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => !PrisnaGWTValidator::isEmpty($this->description_message),
					'tag' => 'description'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTToggleField extends PrisnaGWTField {

	public $name;
	public $value_true;
	public $option_true;
	public $value_false;
	public $option_false;

	protected function _set_properties($_properties) {

		foreach ($_properties as $property => $value)
			$this->{$property} = $value;

		$this->name = $this->id;
		$keys = array_keys($_properties['values']);
		$this->value_true = $keys[0];
		$this->option_true = $_properties['values'][$keys[0]];
		$this->value_false = $keys[1];
		$this->option_false = $_properties['values'][$keys[1]];

	}

	public function output($_html_encode=false) {

		if (!in_array($this->value, array($this->value_true, $this->value_false)))
			$this->value = $this->value_true;

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/toggle.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->value == $this->value_true,
					'tag' => 'value_true.checked'
				),
				array(
					'expression' => $this->value == $this->value_false,
					'tag' => 'value_false.checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTRangeField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/range.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTTextareaField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/textarea.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTExportField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/export.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTLanguageOptionField extends PrisnaGWTField {

	public $base_url;
	public $value_formatted;

	public function __construct($_properties) {

		parent::__construct($_properties);
		
		$this->base_url = PRISNA_GWT__IMAGES . '/';
		$this->value_formatted = preg_replace('/[^a-zA-Z0-9]+|\s+/', '_', $this->value);

	}

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/language_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTLanguageOrderOptionField extends PrisnaGWTField {

	public $base_url;
	public $value_formatted;

	public function __construct($_properties) {

		parent::__construct($_properties);
		
		$this->base_url = PRISNA_GWT__IMAGES . '/';
		$this->value_formatted = preg_replace('/[^a-zA-Z0-9]+|\s+/', '_', $this->value);

	}

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/language_order_option.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTLanguageField extends PrisnaGWTField {

	public $title_order_message;
	public $description_order_message;
	
	public $collection_formatted;
	public $collection_order_formatted;
	
	public $value_order;
	public $enable_order;
	
	public $columns;
	protected $_collections;
	protected $_collection_order;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();
		$this->_set_order_options();

	}

	protected function _set_options() {

		$this->_collections = array();
		for ($i=0; $i<$this->columns; $i++)
			$this->_collections[$i] = new PrisnaGWTItemCollection();

		$top = ceil(count($this->values) / $this->columns);
		$count = 0;
		foreach ($this->values as $key => $value) {

			$group = intval($count / $top);

			$this->_collections[$group]->add(new PrisnaGWTLanguageOptionField((object) array(
				'id' => PrisnaGWTCommon::cleanId($this->id . '_' . $key, '_'),
				'name' => $this->id,
				'checked' => is_array($this->value) ? in_array((string) $key, $this->value, true) : false,
				'option' => $key,
				'value' => $value
			)), $key);

			$count++;

		}

	}

	protected function _set_order_options() {
		
		if (!$this->enable_order)
			return;
		
		$this->value_order = is_array($this->value) ? implode(',', $this->value) : $this->value;
		
		$this->_collection_order = new PrisnaGWTItemCollection();
		
		$values = is_array($this->value) ? $this->value : array($this->value);

		$items = array();
		
		foreach ($this->values as $key => $value) {
			
			if (!in_array((string) $key, $values, true))
				continue;
			
			$items[array_search($key, $values)] = new PrisnaGWTLanguageOrderOptionField((object) array(
				'id' => PrisnaGWTCommon::cleanId($this->id . '_order_' . $key, '_'),
				'option' => $key,
				'value' => $value
			));
			
		}
		
		ksort($items);
		
		foreach ($items as $key => $item)
			$this->_collection_order->add($item, $key);
		
	}

	public function output($_html_encode=false) {

		$this->collection_formatted = '';
		
		foreach ($this->_collections as $collection)
			$this->collection_formatted .= $collection->render(array(
				'type' => 'file',
				'content' => '/admin/language_option_group.tpl'
			), $_html_encode);

		if ($this->enable_order)
			$this->collection_order_formatted = $this->_collection_order->render(array(
				'type' => 'file',
				'content' => '/admin/language_order_option_group.tpl'
			), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/language.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->enable_order,
					'tag' => 'order.show'
				)
			)
		), $_html_encode);

		return $result;

	}	
	
}

class PrisnaGWTCheckboxOptionField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/Checkbox_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTCheckboxField extends PrisnaGWTField {

	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaGWTItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaGWTCheckboxOptionField((object) array(
				'id' => PrisnaGWTCommon::cleanId($this->id . '_' . $key, '_'),
				'name' => $this->id,
				'checked' => is_array($this->value) ? in_array((string) $key, $this->value, true) : false,
				'option' => $key,
				'value' => $value
			)), $key);

		}

	}

	public function output($_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/checkbox.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTExcategoryField extends PrisnaGWTExclitemField {

	public function __construct($_properties) {
		
		parent::__construct($_properties, 'category');
		
	}
	
}

class PrisnaGWTExpageField extends PrisnaGWTExclitemField {

	public function __construct($_properties) {
		
		parent::__construct($_properties, 'page');
		
	}
	
}

class PrisnaGWTExpostField extends PrisnaGWTExclitemField {
	
	public function __construct($_properties) {
		
		parent::__construct($_properties, 'post');
	
	}
	
}

class PrisnaGWTExclitemOptionField extends PrisnaGWTField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/exclude_item_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				),
				array(
					'expression' => $this->indent != 0,
					'tag' => 'indent'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTExclitemField extends PrisnaGWTCheckboxField {
	
	public $values;
	protected $_items;
	protected $_kind;
	
	public function __construct($_properties, $_kind) {

		$this->_kind = $_kind;

		$this->_set_properties($_properties);
		$this->_gen_values();
		$this->_set_options();

	}
	
	protected function _gen_items() {
		
		$this->_items = array();
		
		switch ($this->_kind) {
			case 'page': {
				$temp = get_pages();
				break;
			}
			case 'post': {
				$temp = get_posts();
				break;
			}
			case 'category': {
				$temp = get_categories(array(
					'hide_empty' => 0
				));
				break;
			}
			default: {
				return;
				break;
			}
		}			

		switch ($this->_kind) {
			case 'post':
			case 'page': {
				foreach ($temp as $item)
					$this->_items[$item->ID] = array(
						'title' => $item->post_title,
						'parent' => $item->post_parent
					);
				break;
			}
			case 'category': {
				foreach ($temp as $item)
					$this->_items[$item->cat_ID] = array(
						'title' => $item->cat_name,
						'parent' => $item->category_parent
					);
				break;
			}
		}
		
	}
	
	protected function _gen_values() {
	
		$this->_gen_items();
		$this->values = array();
		
		foreach ($this->_items as $id => $item)
			$this->values[$id] = array(
				'value' => $item['title'],
				'id' => $id,
				'parent' => $item['parent'],
			);

		$this->_sort();

	}
	
	protected function _sort() {
	
		if (count($this->values) < 1)
			return;
	
		$result = array();
		$temp = PrisnaGWTCommon::chain('id', 'parent', 'value', $this->values);
		
		if (is_array($temp))
			foreach ($temp as $item)
				$result[$item['id']] = $item;
		
		$this->values = $result;
		
	}
	
	protected function _set_options() {

		$this->collection = new PrisnaGWTItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaGWTExclitemOptionField((object) array(
				'id' => PrisnaGWTCommon::cleanId($this->id . '_' . $key, '_'),
				'name' => $this->id,
				'checked' => is_array($this->value) ? in_array((string) $key, $this->value, true) : false,
				'option' => $key,
				'indent' => $value['indent'] * 20,
				'value' => $value['value']
			)), $key);

		}

	}
	
	public function output($_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = $this->_render(array(
			'type' => 'file',
			'content' => '/admin/exclude_item.tpl'
		), $_html_encode);

		return $result;

	}	
	
}

class PrisnaGWTRadioOptionField extends PrisnaGWTField {

	public $indent;

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/radio_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTRadioField extends PrisnaGWTField {

	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaGWTItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaGWTRadioOptionField((object) array(
				'id' => $this->id . '_' . $key,
				'name' => $this->id,
				'checked' => $this->value == $key,
				'option' => $key,
				'value' => $value
			)), $key);

		}

	}

	public function output($_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/radio.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTVisualOptionField extends PrisnaGWTField {

	protected $_parent;

	public function __construct($_properties, $_parent) {

		$this->_set_properties($_properties);
		$this->_parent = $_parent;

	}

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/visual_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				),
				array(
					'expression' => $this->collection_item_index % $this->_parent->col_count == 0,
					'tag' => 'new_row'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTVisualField extends PrisnaGWTField {

	public $col_count;
	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaGWTItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaGWTVisualOptionField((object) array(
				'id' => $this->id . '_' . $key,
				'name' => $this->id,
				'checked' => $this->value == $key,
				'option' => $key,
				'value' => $value
			), $this), $key);

		}

	}

	public function output($_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/visual.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTSelectOptionField extends PrisnaGWTField {

	protected $_parent;

	public function __construct($_properties, $_parent) {

		$this->_set_properties($_properties);
		$this->_parent = $_parent;

	}

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/select_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->selected,
					'tag' => 'selected'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTSelectField extends PrisnaGWTField {

	public $force_selected;
	
	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaGWTItemCollection();
		$selected_flag = false;

		foreach ($this->values as $key => $value) {

			if ($this->value == $key)
				$selected_flag = true;

			$this->collection->add(new PrisnaGWTSelectOptionField((object) array(
				'selected' => $this->value == $key,
				'option' => $key,
				'value' => $value
			), $this), $key);

		}

		if ($this->force_selected === true && $selected_flag !== true)
			$this->collection->add(new PrisnaGWTSelectOptionField((object) array(
				'selected' => true,
				'option' => $this->value,
				'value' => $this->value
			), $this), $this->value);

	}

	public function output($_template='select.tpl', $_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template,
			'meta_tag_rules' => array(
				array(
					'expression' => property_exists($this, 'post_id') && !PrisnaGWTValidator::isEmpty($this->post_id),
					'tag' => 'has_post_id'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaGWTTranslationsField extends PrisnaGWTField {
	
	public $resource_formatted;
	
	protected $_resource_field;
	
	protected $folder;
	
	public function __construct($_properties) {
		
		parent::__construct($_properties);
		$this->_initialize_resource_field($_properties);
		
	}
	
	protected function _initialize_resource_field($_properties) {

		$_resource_field = $_properties;
		$_resource_field['id'] = $_resource_field['id'] . '_resource';
		$_resource_field['values'] = $this->_get_resource_values();
		
		$this->_resource_field = new PrisnaGWTSelectField($_resource_field);
		
	}
	
	public static function getFilesPaths($_folder, $_globals=false) {
		
		$contents = @scandir($_folder);
		$result = array();

		if (empty($contents))
			return $result;
		
		foreach ($contents as $name) {
			$file = $_folder . '/' . $name;
			if (PrisnaGWTCommon::endsWith($name, '.xml') && !PrisnaGWTCommon::startsWith($name, 'permalinks') && !PrisnaGWTCommon::startsWith($name, 'log_') && @is_file($file) && ($_globals || !PrisnaGWTCommon::endsWith($name, 'global.xml')))
				$result[] = $file;
		}
		
		return $result;
		
	}
	
	protected function _get_files_scopes() {

		$files = self::getFilesPaths($this->folder);
		$result = array();
		
		foreach ($files as $file) {
			
			$contents = PrisnaGWTFileHandler::read($file);
			
			if (!$contents)
				continue;

			$xml = new DOMDocument();
			$xml->preserveWhiteSpace = false;
			
			if (@!$xml->loadXML($contents))
				continue;
			
			$xpath = new DOMXPath($xml); 
			
			$translation = $xpath->query("/translations")->item(0);
			
			if (!is_object($translation))
				continue;
			
			$scope = $translation->getAttribute('scope');
			$result[base64_encode($scope)] = $scope;

		}

		asort($result);

		return $result;
		
	}
	
	protected function _get_resource_values() {
		
		$result = array_merge(array('' => ''), $this->_get_files_scopes());
		return $result;
		
	}
	
	public function output($_template='translations.tpl', $_html_encode=false) {

		$this->resource_formatted = $this->_resource_field->output('translations_select.tpl', $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template
		), $_html_encode);

		return $result;

	}
	
}

class PrisnaGWTSeologField extends PrisnaGWTField {
	
	public $logs_formatted;
	public $empty_message;
	protected $_file;
	protected $_entries_quantity;
	
	public function __construct($_properties) {
		
		parent::__construct($_properties);
		$this->_file = PrisnaGWTCommon::getLogFile(PrisnaGWTCommon::getTestApiKey());
		$this->empty_message = PrisnaGWTMessage::get('seo_log_empty_message');
		
	}
	
	protected function _render_log($_template='log.tpl', $_html_encode=false) {
		
		$result = '';
		$xml = $this->_get_log();

		if (!$xml)
			return $result;

		$xpath = new DOMXPath($xml);
				
		$entries = $xpath->query("//entry");
		
		$count = (int) $entries->length;
		
		if ($count == 0)
			return $result;
		
		for ($i=$count-1; $i>=0; $i--) {
		
			$entry = array(
				'date' => $entries->item($i)->getAttribute('date'),
				'kind' => $entries->item($i)->getAttribute('kind'),
				'content' => $entries->item($i)->nodeValue
			);
		
			$result .= PrisnaGWTCommon::renderObject((object) $entry, array(
				'type' => 'file',
				'content' => '/admin/' . $_template,
				'meta_tag_rules' => array(
					array(
						'expression' => !($i % 2),
						'tag' => 'even'
					)
				)
			), $_html_encode);
		
		}
		
		return $result;
		
	}
	
	protected function _count_entries($_xml=null) {
		
		if (!is_null($this->_entries_quantity))
			return $this->_entries_quantity;
		
		$xml = is_null($_xml) ? $this->_get_log() : $_xml;
		
		$xpath = new DOMXPath($xml);
				
		$entries = $xpath->query("//entry");
		return $this->_entries_quantity = (int) $entries->length;
		
	}
	
	protected function _get_log() {
		
		if (!is_file($this->_file))
			return false;
		
		$contents = PrisnaGWTFileHandler::read($this->_file);
		
		if (!$contents)
			return false;
		
		$result = new DOMDocument();

		if (@!$result->loadXML($contents))
			return false;
		
		return $result;
		
	}
	
	public function output($_template='seo_log.tpl', $_html_encode=false) {

		$this->logs_formatted = $this->_render_log('log.tpl', $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template,
			'meta_tag_rules' => array(
				array(
					'expression' => !is_file($this->_file) || $this->_count_entries() == 0,
					'tag' => 'empty'
				)
			)
		), $_html_encode);

		return $result;

	}
	
}

class PrisnaGWTItemCollection {

    protected $_position = 0;

    public $collection;

    public function __construct() {

		$this->_position = 0;

	}

    public function add($_object, $_index=null) {

		if (is_null($_index))
			$this->collection[] = $_object;
		else
			$this->collection[$_index] = $_object;

	}

    public function rewind() {

        $this->_position = 0;

    }

    public function current() {

		$keys = array_keys($this->collection);
        return $this->collection[$keys[$this->_position]];

	}

    public function getFirst() {

		$keys = array_keys($this->collection);
        return $this->collection[$keys[0]];

	}

    public function getLast() {

		$keys = array_keys($this->collection);
        return $this->collection[$keys[count($keys)-1]];

	}

    public function key() {

		return $this->_position;

    }

    public function next() {

        ++$this->_position;

	}

    public function count() {

		return count($this->collection);

	}

    public function valid() {

		$keys = array_keys($this->collection);

		if (!isset($keys[$this->_position]))
			return false;

		return isset($this->collection[$keys[$this->_position]]);

	}

	protected function _add_count_for_render() {

		if (count($this->collection) > 0) {
			$i = 0;
			foreach ($this->collection as $item) {
				$item->collection_item_index = $i;
				$i++;
			}
		}

	}

	public function render($_options, $_html_encode=false) {

		$result = '';
		$partial = array();

		$this->_add_count_for_render();

		if (count($this->collection) > 0) 
			foreach ($this->collection as $item)
				$partial[] = $item->output($_html_encode);

		$object = (object) array(
			'collection' => join("\n", $partial),
			'collection_count' => count($partial)
		);

		foreach ($this as $property => $value)
			if (!is_array($value))
				$object->{$property} = $value;				

		if (!array_key_exists('meta_tag_rules', $_options))
			$_options['meta_tag_rules'] = array();

		$_options['meta_tag_rules'][] = array(
			'expression' => count($partial) == 0,
			'tag' => 'collection.is_empty'
		);

		$result = PrisnaGWTCommon::renderObject($object, $_options, $_html_encode);

		return $result;

	}

}

?>
