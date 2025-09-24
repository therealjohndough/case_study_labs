<?php

namespace wpai_toolset_types_add_on\fields\fields;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\ToolsetService;

/**
 *
 * Checkbox Field
 *
 * Class FieldCheckbox
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldCheckbox extends Field {

    /**
     *  Field type key
     */
    public $type = 'checkbox';

    /**
     *
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
        // Get parsed value.
        $parsedValue = $this->getFieldValue();
        $parentEntityID = $this->isRepeatable() ? $this->getRepeatableGroupRow()->ID : $this->getPostID();
        if (empty($parsedValue)) {
            if ($this->isSaveEmpty()) {
                // Save 0 to the database.
                ToolsetService::update_post_meta($this, $parentEntityID, $this->getFieldName(), 0);
            } else {
                // Don't save anything to the database.
                ToolsetService::delete_post_meta($this, $parentEntityID, $this->getFieldName());
            }
        } else {
            ToolsetService::update_post_meta($this, $parentEntityID, $this->getFieldName(), $parsedValue);
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