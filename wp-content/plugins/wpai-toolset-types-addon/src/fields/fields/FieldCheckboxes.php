<?php

namespace wpai_toolset_types_add_on\fields\fields;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\ToolsetService;

/**
 *
 * Checkboxes Field
 *
 * Class FieldCheckboxes
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldCheckboxes extends Field {

    /**
     *  Field type key
     */
    public $type = 'checkboxes';

    /**
     * Parse field data
     *
     * @param $xpath
     * @param $parsingData
     * @param array $args
     */
    public function parse($xpath, $parsingData, $args = []) {
        parent::parse($xpath, $parsingData, $args);
        if ($this->isMultipleValue()){
            $values = array_fill(0, 1, $this->getPostMultipleValue());
            if (!empty($values)) {
                foreach ($values as $key => $value) {
                    $v = array_values($value);
                    $values[$key] = implode($this->getDelimiter(), array_filter($v));
                }
            }
        } else {
            $values = $this->getByXPath($xpath);
        }
        $this->setOption('values', $values);
    }

    /**
     * @param $importData
     * @param array $args
     * @return mixed
     */
    public function import($importData, $args = []) {
        $isUpdated = parent::import($importData, $args);
        if (!$isUpdated){
            return FALSE;
        }
        $values = [];
        $options = [];
        // Get parsed value.
        $parsedValue = $this->getFieldValue();
        $field = $this->getData('field');
        $field_options = $field['data']['options'];

        $exploded = explode($this->getDelimiter(), $parsedValue);
        foreach ($exploded as $i => $v) {
            foreach ($field_options as $key => $option) {
                if (strtolower($option['set_value']) === strtolower($v) || strtolower($option['title']) === strtolower($v)) {
                    $options[$key] = $option['set_value'];
                }
            }
        }

        foreach ($options as $key => $value){
            if (empty($value)){
                if ($this->isSaveEmpty()){
                    $values[$key] = 0;
                }
            } else {
                $values[$key] = [$value];
            }
        }
        if ($this->isRepeatable()) {
            ToolsetService::update_post_meta($this, $this->getRepeatableGroupRow()->ID, $this->getFieldName(), $values);
        } else {
            ToolsetService::update_post_meta($this, $this->getPostID(), $this->getFieldName(), $values);
        }
    }

    /**
     * @return bool
     */
    protected function isSaveEmpty(){
        $field = $this->getData('field');
        return $field['data']['save_empty'] == 'yes';
    }
}