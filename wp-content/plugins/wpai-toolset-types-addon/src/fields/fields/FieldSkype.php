<?php

namespace wpai_toolset_types_add_on\fields\fields;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\ToolsetService;

/**
 *
 * Multiple Lines Field
 *
 * Class FieldTextarea
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldSkype extends Field {

    /**
     *  Field type key
     */
    public $type = 'skype';

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

        $values = $xpath;
        $values['skypename'] = $this->getByXPath($values['skypename']);

        $values = base64_encode(serialize($values));

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
            // Import multiple-instances of this field.
            $skypenames = explode($this->getDelimiter(), $parsedValue['skypename']);
            if (!empty($skypenames)) {
                $skypenames = array_map('trim', $skypenames);
                foreach ($skypenames as $skypename) {
                    $parsedValue['skypename'] = $skypename;
                    ToolsetService::add_post_meta($this, $this->getPostID(), $this->getFieldName(), $parsedValue);
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

    /**
     * @return mixed
     */
    public function getFieldValue() {
        $parsedValue = unserialize(base64_decode($this->getOption('values')));
        $parsedValue['skypename'] = $parsedValue['skypename'][$this->getPostIndex()];
        $parents = $this->getParents();
        if (!empty($parents)){
            $skypename = '';
            foreach ($parents as $key => $parent) {
                $skypename = explode($parent['delimiter'], $parsedValue['skypename']);
                $skypename = $skypename[$parent['index']];
            }
            $parsedValue['skypename'] = $skypename;
        }
        return $parsedValue;
    }
}