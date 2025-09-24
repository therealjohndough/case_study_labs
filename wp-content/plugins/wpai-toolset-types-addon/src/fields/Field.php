<?php

namespace wpai_toolset_types_add_on\fields;

use PMTI_Plugin;

/**
 * Class Field.
 *
 * @package wpai_toolset_types_add_on\fields
 */
abstract class Field implements FieldInterface {

    /**
     * Field type.
     */
    public $type;

    /**
     * @var array
     */
    public $data;

    /**
     * @var bool
     */
    public $supportedVersion = false;

    /**
     * @var array
     */
    public $parsingData;

    /**
     * @var array
     */
    public $importData;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var Field
     */
    public $parent;

    /**
     * @var array
     */
    public $subFields = [];

    /**
     * @var \WP_Post
     */
    public $repeatableGroupRow;

    /**
     * Field constructor.
     *
     * @param $field
     * @param $post
     * @param $field_name
     * @param $parent_field
     */
    public function __construct($field, $post, $field_name = "", $parent_field = false) {
        $this->data = [
            'field' => $field,
            'post' => $post,
            'field_name' => $field_name
        ];
        $this->setParent($parent_field);
        $this->data = array_merge($this->data, $this->getFieldData());
    }

    /**
     *
     * Data from this method will be available in field view template
     *
     * @return array
     */
    private function getFieldData() {
        $data = [];
        $field = $this->getData('field');
        $post  = $this->getData('post');
        $data['current_field'] = false;

        if (!empty($field['meta_key']) && !empty($post['wpcs_fields'][$field['meta_key']])) {
            $data['current_field'] = $post['wpcs_fields'][$field['meta_key']];
        }

        // If parent field exists, parse field name.
        if ( "" != $this->getData('field_name') ) {
            $field_keys = str_replace(['[',']'], [''], str_replace('][', ':', $this->getData('field_name')));
            $data['current_field'] = false;
            foreach (explode(":", $field_keys) as $n => $key) {
                if (isset($post['wpcs_fields'][$key])) {
                    $data['current_field'] = $post['wpcs_fields'][$key];
                } else {
                    if (isset($data['current_field'][$key])) {
                        $data['current_field'] = $data['current_field'][$key];
                    }
                }
            }
            $data['current_field'] = empty($data['current_field'][$field['meta_key']]) ? false : $data['current_field'][$field['meta_key']];
        }

        return $data;
    }

    /**
     * @param $xpath
     * @param $parsingData
     * @param array $args
     * @return void
     */
    public function parse($xpath, $parsingData, $args = []) {

        $this->parsingData = $parsingData;

        $defaults = [
            'field_path' => '',
            'xpath_suffix' => '',
            'repeater_count_rows' => 0,
            'inside_repeater' => false
        ];

        $args = array_merge($defaults, $args);

        $field = $this->getData('field');

        $isMultipleField = FALSE;
        $multipleValue   = FALSE;

        if (!empty($field['meta_key'])) {
            if (isset($parsingData['import']->options['is_multiple_field_value'][$field['meta_key']])) {
                $isMultipleField = $parsingData['import']->options['is_multiple_field_value'][$field['meta_key']];
            }
	        if (isset($parsingData['import']->options['wpcs_fields'][$field['meta_key']]['is_multiple_value'])) {
		        $isMultipleField = $parsingData['import']->options['wpcs_fields'][$field['meta_key']]['is_multiple_value'];
	        }
            if (isset($parsingData['import']->options['multiple_value'][$field['meta_key']])) {
                $multipleValue = $parsingData['import']->options['multiple_value'][$field['meta_key']];
            }
	        if (isset($parsingData['import']->options['wpcs_fields'][$field['meta_key']]['multiple_value'])) {
		        $multipleValue = $parsingData['import']->options['wpcs_fields'][$field['meta_key']]['multiple_value'];
	        }
        }

        if ("" != $args['field_path']) {

            $fieldKeys = preg_replace('%[\[\]]%', '', str_replace('][', ':', $args['field_path']));

            foreach (explode(":", $fieldKeys) as $n => $key) {
                $xpath = (!$n) ? $parsingData['import']->options['wpcs_fields'][$key] : $xpath[$key];
            }
            $xpath = empty($xpath[$field['meta_key']]) ? false : $xpath[$field['meta_key']];
            $isMultipleField = !empty($xpath['is_multiple_value']);
            $multipleValue = $xpath['multiple_value'] ?? false;
        }

        $this->setOption('base_xpath', $parsingData['xpath_prefix'] . $parsingData['import']->xpath . $args['xpath_suffix']);
        $this->setOption('xpath', $xpath);
        $this->setOption('is_multiple_field', $isMultipleField);
        $this->setOption('multiple_value', $multipleValue);
        $this->setOption('count', ($args['repeater_count_rows']) ? $args['repeater_count_rows'] : $parsingData['count']);
        $this->setOption('values', array_fill(0, $this->getOption('count'), ""));
    }

    /**
     * @param $importData
     * @param array $args
     * @return bool
     */
    public function import($importData, $args = []) {
        $defaults = [
            'parent_repeater' => ''
        ];
        $field = $this->getData('field');
        $args = array_merge($defaults, $args);
        $this->importData = array_merge($importData, $args);
        if (isset($field['name'])) {
	        $this->parsingData['logger'] and call_user_func($this->parsingData['logger'], sprintf(__('- Importing field `%s`', PMTI_Plugin::TEXT_DOMAIN), $field['name']));
        }
        $parsedData = $this->getParsedData();
        // If update is not allowed.
        if (!empty($this->importData['articleData']['ID']) && ! \pmti_is_wpcs_update_allowed($field['meta_key'], $this->parsingData['import']->options) && empty($parsedData['xpath']['only_append_new'])) {
            $this->parsingData['logger'] && call_user_func($this->parsingData['logger'], sprintf(__('- Field `%s` is skipped attempted to import options', PMTI_Plugin::TEXT_DOMAIN), $this->getFieldName()));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $importData
     * @return void
     */
    public function saved_post($importData){}

    /**
     *  Render field
     */
    public function view() {
        $this->renderHeader();
        extract($this->data);
        $fields = $this->getSubFields();
        $filePath = __DIR__ . '/views/'. $this->type .'.php';
        if (is_file($filePath)) {
            include $filePath;
        }
        $this->renderFooter();
    }

        /**
         *  Render field header
         */
        protected function renderHeader() {
            $filePath = __DIR__ . '/templates/header.php';
            if (is_file($filePath)) {
                extract($this->data);
                include $filePath;
            }
        }

        /**
         *  Render field footer
         */
        protected function renderFooter() {
            $filePath = __DIR__ . '/templates/footer.php';
            if (is_file($filePath)) {
                include $filePath;
            }
        }

    /**
     * @return bool
     */
    public function isRepetitive(){
	    $is_repetitive = (bool) !empty($this->data['field']['data']['repetitive']);
		if ($is_repetitive && !empty($this->importData['articleData']['ID'])) {
			$field = $this->getData('field');
			delete_post_meta($this->importData['articleData']['ID'], $field['meta_key']);
		}
        return $is_repetitive;
    }

    /**
     * @return \WP_Post
     */
    public function getRepeatableGroupRow() {
        return $this->repeatableGroupRow;
    }

    /**
     * @param \WP_Post $repeatableGroupRow
     */
    public function setRepeatableGroupRow($repeatableGroupRow) {
        $this->repeatableGroupRow = $repeatableGroupRow;
    }

    /**
     * @return bool
     */
    public function isRepeatable() {
        return $this->repeatableGroupRow ? TRUE : FALSE;
    }

    /**
     * @return string
     */
    public function getDelimiter() {
        return empty($this->getData('current_field')['delimiter']) ? ',' : esc_attr($this->getData('current_field')['delimiter']);
    }

    /**
     * @return bool
     */
    public function isMultipleValue() {
        return !empty($this->getOption('is_multiple_field'));
    }

    /**
     * @return array|string
     */
    public function getPostMultipleValue() {
        $value = empty($this->getOption('multiple_value')) ? '' : $this->getOption('multiple_value');
        return is_array($value) ? array_map('esc_attr', $value) : esc_attr($value);
    }

    /**
     * @return string
     */
    public function getPostValue() {
        return empty($this->getData('current_field')['value']) ? '' : esc_attr($this->getData('current_field')['value']);
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return Field|bool
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param Field|bool $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * @param $option
     * @return bool|mixed
     */
    public function getData($option) {
        return $this->data[ $option ] ?? false;
    }

    /**
     * @param $option
     * @param $value
     */
    public function setData($option, $value) {
        $this->data[$option] = $value;
    }

    /**
     * @param $option
     * @return bool|mixed
     */
    public function getOption($option) {
        return $this->options[ $option ] ?? false;
    }

    /**
     * @param $option
     * @param $value
     */
    public function setOption($option, $value) {
        $this->options[$option] = $value;
    }

    /**
     * @param $xpath
     * @return array
     */
    public function getByXPath($xpath) {
        $values = array_fill(0, $this->getOption('count'), "");
        if ($xpath != "") {
            $file = false;
            $values = \XmlImportParser::factory($this->parsingData['xml'], $this->getOption('base_xpath'), $xpath, $file)->parse();
            @unlink($file);
        }
        return $values;
    }

    /**
     * @return mixed
     */
    public function getParsingData() {
        return $this->parsingData;
    }

    /**
     * @return mixed
     */
    public function getImportData() {
        return $this->importData;
    }

    /**
     * @return mixed
     */
    public function getPostIndex() {
        return $this->importData['i'];
    }

    /**
     * @return mixed
     */
    public function getPostID() {
        return $this->importData['pid'];
    }

    /**
     * @return string
     */
    public function getFieldName() {
        return $this->getFieldKey();
    }

    /**
     * @param $fieldName
     */
    public function setFieldInputName($fieldName) {
        $this->data['field_name'] = $fieldName;
        $this->data = array_merge($this->data, $this->getFieldData());
    }

    /**
     * @return string
     */
    public function getFieldKey() {
        return $this->data['field']['meta_key'];
    }

    /**
     * @return string
     */
    public function getFieldLabel() {
        return $this->data['field']['name'];
    }

    /**
     * @return mixed
     */
    public function getFieldValue() {
        $index = $this->isMultipleValue() ? 0 : $this->getPostIndex();
        $value = $this->options['values'][$index];
        if (!$this->isMultipleValue()) {
            $parents = $this->getParents();
            if (!empty($parents)){
                foreach ($parents as $key => $parent) {
                    $value = explode($parent['delimiter'], $value);
                    $value = $value[$parent['index']];
                }
            }
        }
        return $value;
    }

    /**
     * @param $option
     * @return null|mixed
     */
    public function getFieldOption($option) {
        return $this->data['field'][ $option ] ?? null;
    }

    /**
     * @param $option
     * @return null|mixed
     */
    public function getImportOption($option) {
        $importData = $this->getImportData();
        return $importData['import']->options[ $option ] ?? null;
    }

    /**
     * @return mixed
     */
    public function getImportType() {
        $importData = $this->getImportData();
        return $importData['import']->options['custom_type'];
    }

    /**
     * @return mixed
     */
    public function getTaxonomyType() {
        $importData = $this->getImportData();
        return $importData['import']->options['taxonomy_type'];
    }

    /**
     * @return mixed
     */
    public function getLogger() {
        return $this->parsingData['logger'];
    }

    /**
     * @return array
     */
    public function getSubFields() {
        return $this->subFields;
    }

    /**
     * @return bool
     */
    public function isNotEmpty() {
        return (bool) $this->getCountValues();
    }

    /**
     * @return int
     */
    public function getCountValues() {
        $parents = $this->getParents();
        $value = $this->getOriginalFieldValueAsString();
        if (!empty($parents) && !is_array($value)){
            $parentIndex = false;
            foreach ($parents as $key => $parent) {
                if ($parentIndex !== false){
                    $value = $value[$parentIndex];
                }
                $value = explode($parent['delimiter'], $value);
                $parentIndex = $parent['index'];
            }
        }
        return is_array($value) ? count($value) : ( ! is_null($value) && $value !== false && $value !== "");
    }

    /**
     * @return mixed
     */
    public function getOriginalFieldValueAsString() {
        return $this->options['values'][$this->getPostIndex()];
    }

    /**
     * @return array
     */
    protected function getParents() {
        $field = $this;
        $parents = [];
        do {
            $parent = $field->getParent();
            if ($parent){
                switch ($parent->type){
                    case 'repeatable_group':
                        if ($parent->getMode() == 'csv' && $parent->getDelimiter()){
                            $parents[] = [
                                'delimiter' => $parent->getDelimiter(),
                                'index'     => $parent->getRowIndex()
                            ];
                        }
                        break;
                    default:
                        break;
                }
                $field = $parent;
            }
        }
        while($parent);

        return array_reverse($parents);
    }

    /**
     * @return array
     */
    public function getParsedData() {
        $field = $this->getOption('field');
        return [
            'type' => $field['type'] ?? false,
            'post_type' => $field['post_type'] ?? false,
            'name' => $field['name'] ?? false,
            'multiple' => $field['multiple'] ?? false,
            'values' => $this->getOption('values'),
            'is_multiple' => $this->getOption('is_multiple'),
            'is_variable' => $this->getOption('is_variable'),
            'is_ignore_empties' => $this->getOption('is_ignore_empties'),
            'xpath' => $this->getOption('xpath'),
            'id' => !empty($field['ID']) ? $field['ID'] : ( $field['id'] ?? false )
        ];
    }
}