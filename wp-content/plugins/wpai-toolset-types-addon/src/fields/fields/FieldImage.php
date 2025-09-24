<?php

namespace wpai_toolset_types_add_on\fields\fields;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\ToolsetService;

/**
 *
 * Image Field
 *
 * Class FieldImage
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldImage extends Field {

    /**
     *  Field type key
     */
    public $type = 'image';

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
        if ($this->isAppendMedia()) {
            add_filter('pmxi_custom_field_to_delete', [$this, 'is_custom_field_to_delete'], 99, 5);
        }
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

        if ( $this->isRepetitive() ) {
            $current_values = ToolsetService::get_post_meta($this, $this->getPostID(), $this->getFieldName(), false);
            $values = explode($this->getDelimiter(), $parsedValue);
            if (!empty($values)) {
                $values = array_map('trim', $values);
                foreach ($values as $value) {
                    $formatted_value = $this->getFormattedValue($value);
                    // Do not add duplicate images to the gallery.
                    if ($this->isAppendMedia() && in_array($formatted_value, $current_values)) {
                        continue;
                    }
                    if (!empty($formatted_value)) {
                        ToolsetService::add_post_meta($this, $this->getPostID(), $this->getFieldName(), $formatted_value);
                    }
                }
            }
        } else {
            $value = $this->getFormattedValue($parsedValue);
            if (!empty($value)) {
                if ($this->isRepeatable()) {
                    ToolsetService::update_post_meta($this, $this->getRepeatableGroupRow()->ID, $this->getFieldName(), $this->getFormattedValue($parsedValue));
                } else {
                    ToolsetService::update_post_meta($this, $this->getPostID(), $this->getFieldName(), $this->getFormattedValue($parsedValue));
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isSearchInMedia(){
        return !empty($this->getData('current_field')['search_in_media']);
    }

    /**
     * @return bool
     */
    public function isAppendMedia(){
        return !empty($this->getData('current_field')['only_append_new']);
    }

    /**
     * @param $image
     * @return false|int|mixed|string
     */
    public function getFormattedValue($image) {
        $parsingData = $this->getParsingData();
        $attachmentID = ToolsetService::import_image($image, $this->getPostID(), $parsingData['logger'], $this->isSearchInMedia());
        return $attachmentID ? wp_get_attachment_url($attachmentID) : '';
    }

    /**
     * @param $field_to_delete
     * @param $pid
     * @param $post_type
     * @param $options
     * @param $cur_meta_key
     * @return false
     */
    public function is_custom_field_to_delete($field_to_delete, $pid, $post_type, $options, $cur_meta_key) {
        if ($cur_meta_key == $this->getFieldName()) {
            $field_to_delete = FALSE;
        }
        return $field_to_delete;
    }
}