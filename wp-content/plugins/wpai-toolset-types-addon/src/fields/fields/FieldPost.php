<?php

namespace wpai_toolset_types_add_on\fields\fields;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\relationships\Relationship;

/**
 * Class FieldPost
 *
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldPost extends Field {

    /**
     *  Field type key
     */
    public $type = 'post';

    /**
     * @var Relationship
     */
    public $relationship;

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

        $field_name = str_replace("wpcf-", "", $this->getFieldName());
        $field = wpcf_fields_get_field_by_slug($field_name);
        if (!empty($field['data']['relationship_slug'])) {
            global $wpdb;
            $relationship = $wpdb->get_row($wpdb->prepare("SELECT * from {$wpdb->prefix}toolset_relationships WHERE slug = %s;", $field['data']['relationship_slug']), ARRAY_A);
            if (!empty($relationship['id'])) {
                $this->relationship = new Relationship(['id' => $relationship['id']], $parsingData['import']->options);
                $this->relationship->setImportType(Relationship::IMPORTING_CHILD);
                if ($this->isRepeatable()) {
                    // Import multiple-instances of this field.
                    $this->relationship->setDelim($this->getDelimiter());
                }
                $this->relationship->parse($xpath, $parsingData);
            }
        }
    }

    /**
     * @param $importData
     * @param array $args
     * @return mixed
     */
    public function import($importData, $args = []) {
        $isUpdated = parent::import($importData, $args);
        if (!$isUpdated) {
            return FALSE;
        }
        if ($this->relationship) {
            $this->relationship->saveRelationship($importData);
        }
    }
}