<?php

namespace wpai_toolset_types_add_on\fields;

use wpai_toolset_types_add_on\fields\fields\FieldEmpty;
use wpai_toolset_types_add_on\fields\fields\FieldNotSupported;
use wpai_toolset_types_add_on\fields\fields\FieldRepeatableGroup;

/**
 * Class FieldFactory
 * @package wpai_toolset_types_add_on\fields
 */
final class FieldFactory {

    /**
     *
     * An array of fields which are doesn't have any functionality
     *
     * @var array
     */
    public static $hiddenFields = ['accordion', 'tab', 'message'];

    /**
     * @param $fieldData
     * @param $post
     * @param $fieldName
     * @param $fieldParent
     * @return bool|\wpai_toolset_types_add_on\fields\fields\FieldEmpty
     */
    public static function create($fieldData, $post, $fieldName = "", $fieldParent = false) {
        $field = FALSE;
        if (is_array($fieldData)) {
	        $class = '\\wpai_toolset_types_add_on\\fields\\fields\\Field' . str_replace(" ", "", ucwords(str_replace("_", " ", $fieldData['type'])));
            if (in_array($fieldData['type'], self::$hiddenFields)) {
                $field = new FieldEmpty($fieldData, $post, $fieldName);
            } elseif (class_exists($class)) {
                $field = new $class($fieldData, $post, $fieldName, $fieldParent);
            }
        } else {
            if (strpos($fieldData, 'repeatable_group') !== FALSE) {
                $field = new FieldRepeatableGroup($fieldData, $post, $fieldName, $fieldParent);
            }
        }

        if (empty($field)){
            $field = new FieldNotSupported($fieldData, $post);
        }
        return $field;
    }
}