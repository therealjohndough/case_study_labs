<?php

namespace wpai_toolset_types_add_on\fields\fields;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\ToolsetService;

/**
 *
 * Single Line Field
 *
 * Class FieldTextfield
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldTextfield extends Field {

    /**
     *  Field type key
     */
    public $type = 'textfield';

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
        $values = $this->getByXPath($xpath);
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
        if ($this->isRepetitive()) {
            $values = explode($this->getDelimiter(), $parsedValue);
            if (!empty($values)) {
                $values = array_map('trim', $values);
                foreach ($values as $value) {
                    ToolsetService::add_post_meta($this, $this->getPostID(), $this->getFieldName(), $value);
                }
            }
        } else {
            if ($this->isRepeatable()) {
                ToolsetService::update_post_meta($this, $this->getRepeatableGroupRow()->ID, $this->getFieldName(), $parsedValue);
            } else {
                ToolsetService::update_post_meta($this, $this->getPostID(), $this->getFieldName(), $parsedValue);
            }
        }
    }
}