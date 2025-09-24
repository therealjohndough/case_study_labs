<?php

namespace wpai_toolset_types_add_on\fields\fields;

use SitePress;
use Toolset_Association_Factory;
use Toolset_Post_Type_Repository;
use Toolset_Relationship_Definition;
use Toolset_Relationship_Definition_Translator;
use Toolset_Relationship_Service;
use Types_Field_Group_Repeatable;
use Types_Field_Group_Repeatable_Service;
use WP_Post;
use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\fields\FieldFactory;
use wpai_toolset_types_add_on\relationships\ToolsetLegacy;

/**
 * Class fieldRepeatableGroup
 *
 * @package wpai_toolset_types_add_on\fields\fields
 */
class FieldRepeatableGroup extends Field {

    /**
     *  Field type key
     */
    public $type = 'repeatable_group';

    /**
     * @var array
     */
    public $fieldsData = [];

    /**
     * @var \WP_Post
     */
    public $groupPost;

    /**
     * @var Types_Field_Group_Repeatable
     */
    public $repeatableGroup;

    /**
     * @var Types_Field_Group_Repeatable_Service
     */
    public $repeatableService;

    /**
     * @var Toolset_Association_Factory
     */
    public $associationFactory;

    /**
     * @var Toolset_Relationship_Definition
     */
    public $relationship;

    /**
     * @var string
     */
    public $mode = 'csv';

    /**
     * @var string
     */
    public $delimiter = ',';

    /**
     * @var bool
     */
    public $ignoreEmpties = false;

    /**
     * @var int
     */
    public $rowIndex = 0;

    /**
     *
     * fieldRepeatableGroup constructor.
     *
     * @param $field
     * @param $post
     * @param string $field_name
     * @param bool $parent_field
     */
    public function __construct($field, $post, $field_name = "", $parent_field = false) {
        $id = preg_replace("/[^0-9]/", "", $field );
        $this->groupPost = get_post($id);

        if (!empty($this->groupPost)) {
            $field = [
                'ID' => $this->groupPost->ID,
                'id' => $this->groupPost->ID,
                'label' => $this->groupPost->post_title,
                'key' => $this->groupPost->post_name,
                'meta_key' => $this->groupPost->post_name,
            ];

            $this->repeatableService = new Types_Field_Group_Repeatable_Service();
            $this->repeatableGroup = new Types_Field_Group_Repeatable($this->groupPost);

            $relationshipData = false;
            $relationships = \PMTI_Plugin::getInstance()->get_database_layer_factory()->relationship_database_operations()->load_all_relationships();
            if (!empty($relationships)) {
                foreach ($relationships as $relationship) {
                    if ($relationship->slug === $this->groupPost->post_name) {
                        $relationshipData = $relationship;
                        break;
                    }
                }
            }

            $definition_translator = new Toolset_Relationship_Definition_Translator();
            $this->relationship = $definition_translator->from_database_row($relationshipData);
            $this->associationFactory = new Toolset_Association_Factory();

            parent::__construct($field, $post, $field_name, $parent_field);

            $this->initSubFields();
        }
    }

    /**
     * @inheritdoc
     */
    public function parse($xpath, $parsingData, $args = []) {
        parent::parse($xpath, $parsingData, $args);
        $xpath = $this->getOption('xpath');
        if (empty($xpath)) {
            $xpath = $this->getData('current_field');
        }

        // Remove repeater row template.
        if (isset($xpath['rows']['ROWNUMBER'])) {
            unset($xpath['rows']['ROWNUMBER']);
        }
        if (!empty($xpath['rows'])) {
            $values = [];
            $this->setIgnoreEmpties($xpath['is_ignore_empties']);
            switch ($xpath['is_variable']) {
                case 'yes':
                    $rowFields = array_shift($xpath['rows']);
                    $this->setMode('xml');
                    for ($k = 0; $k < $this->getOption('count'); $k++) {
                        $repeater_xpath = '[' . ($k + 1) . ']/' . ltrim(trim($xpath['foreach'], '{}!'), '/');
                        $file = false;
                        $repeaterRows = \XmlImportParser::factory($this->parsingData['xml'], $this->getOption('base_xpath') . $repeater_xpath, "{.}", $file)->parse();
                        @unlink($file);

                        $xpath_suffix = '';
                        if ((!isset($rowFields[$this->getFieldKey()])
                                || (is_array($rowFields[$this->getFieldKey()]) || strpos($rowFields[$this->getFieldKey()], "!") !== 0))
                                    && strpos($xpath['foreach'], "!") !== 0
                        ) {
                            $xpath_suffix = $this->getOption('base_xpath') . $repeater_xpath;
                            $xpath_suffix = str_replace($parsingData['xpath_prefix'] . $parsingData['import']->xpath, '', $xpath_suffix);
                        }

                        $rowData = [];
                        /** @var Field $subField */
                        foreach ($this->getSubFields() as $subField) {
                            $subField->parse($rowFields[$subField->getFieldKey()]['value'], $parsingData, [
                                'field_path' => $this->getOption('field_path') . "[" . $this->getFieldKey() . "][rows][1]",
                                'xpath_suffix' => $xpath_suffix,
                                'repeater_count_rows' => count($repeaterRows),
                                'inside_repeater' => true
                            ]);
                            $rowData[$subField->getFieldKey()] = clone $subField;
                        }
                        $values[] = [
                            'countRows' => count($repeaterRows),
                            'fields' => $rowData
                        ];
                    }
                    break;
                default:
                    switch ($xpath['is_variable']){
                        case 'csv':
                            $this->setDelimiter($xpath['separator']);
                            $this->setIgnoreEmpties(true);
                            break;
                        default:
                            $this->setDelimiter(false);
                            $this->setMode('fixed');
                            break;
                    }
                    foreach ($xpath['rows'] as $key => $rowFields) {
                        $rowData = [];
                        $subFields = $this->getSubFields();
                        /** @var Field $subField */
                        foreach ($subFields as $subField) {
                            if (isset($rowFields[$subField->getFieldKey()])) {
                                $subField->parse($rowFields[$subField->getFieldKey()]['value'], $parsingData, [
                                    'field_path' => $this->getOption('field_path') . "[" . $this->getFieldKey() . "][rows][" . $key . "]",
                                    'xpath_suffix' => $args['xpath_suffix'],
                                    'repeater_count_rows' => 0,
                                    'inside_repeater' => true
                                ]);
                                $rowData[$subField->getFieldKey()] = clone $subField;
                            }
                        }
                        $values[] = $rowData;
                    }
                    break;
            }
            $this->setOption('values', $values);
        }
    }

    /**
     * @inheritdoc
     */
    public function import($importData, $args = []) {
        $isUpdated = parent::import($importData, $args);
        // Get current rows.
        if (!$this->getParent()) {
            $group = $this->repeatableService->get_object_by_id($this->repeatableGroup->get_id(), get_post($this->getPostID()));
            $items = $group->get_posts();
        } else {
            $group = $this->repeatableService->get_object_by_id($this->repeatableGroup->get_id(), $this->getRepeatableGroupRow());
            $items = $group->get_posts();
        }
        if ($isUpdated) {
            // Delete current data.
            if (!empty($items)) {
                /** @var \Types_Field_Group_Repeatable_Item $item */
                foreach ($items as $item) {
                    $this->delete_item($item->get_wp_post());
                }
                $items = [];
            }
        }

        // Import new data.
        $values = $this->getOption('values');
        if (!empty($values)){
            switch ($this->getMode()) {
                case 'xml':
                    $countRows = 0;
                    for ($k = 0; $k < $values[$this->getPostIndex()]['countRows']; $k++) {
                        $row_id = $this->getRow($items, $k);
                        if ($row_id && !is_wp_error($row_id)) {
                            $groupRow = get_post($row_id);
                            $importData['i'] = $k;
                            // Init importData in all sub fields.
                            /** @var Field $subField */
                            foreach ($values[$this->getPostIndex()]['fields'] as $subFieldKey => $subField) {
                                $subField->importData = $importData;
                            }
                            if ($this->isImportRow($values[$this->getPostIndex()]['fields'])) {
                                /** @var Field $subField */
                                foreach ($values[$this->getPostIndex()]['fields'] as $subFieldKey => $subField) {
                                    $subField->setRepeatableGroupRow($groupRow);
                                    $subField->import($importData);
                                }
                                $countRows++;
                            }
                        }
                    }
                    break;
                case 'csv':
                    $fields = array_shift($values);
                    if (!empty($fields)) {
                        // Init importData in all sub fields
                        /** @var Field $subField */
                        foreach ($fields as $subFieldKey => $subField) {
                            $subField->importData = $importData;
                        }
                        if ($this->isImportRow($fields)) {
                            $countRows = $this->getCountRows($fields);
                            for ($k = 0; $k < $countRows; $k++) {
                                $row_id = $this->getRow($items, $k);
                                if ($row_id && !is_wp_error($row_id)) {
                                    $groupRow = get_post($row_id);
                                    $this->setRowIndex($k);
                                    /** @var Field $subField */
                                    foreach ($fields as $subFieldKey => $subField) {
                                        $parentField = $subField->getParent();
                                        if ($parentField) {
                                            $parentField->setRowIndex($k);
                                        }
                                        $subField->setRepeatableGroupRow($groupRow);
                                        $subField->import($importData);
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'fixed':
                    $countRows = 0;
                    foreach ($values as $row_number => $fields) {
                        if (!empty($fields)) {
                            $row_id = $this->getRow($items, $countRows);
                            if ($row_id && !is_wp_error($row_id)) {
                                $groupRow = get_post($row_id);
                                $countRows++;
                                // Init importData in all sub fields
                                /** @var Field $subField */
                                foreach ($fields as $subFieldKey => $subField) {
                                    $subField->importData = $importData;
                                }
                                if ($this->isImportRow($fields)) {
                                    /** @var Field $subField */
                                    foreach ($fields as $subFieldKey => $subField) {
                                        $subField->setRepeatableGroupRow($groupRow);
                                        $subField->import($importData);
                                    }
                                } else {
                                    $countRows--;
                                }
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param \Types_Field_Group_Repeatable_Item[] $current_rows
     * @param int $index
     */
    protected function getRow($current_rows, $index) {
        $row_id = FALSE;
        // Try to find existing row to update fields in it.
        if (!empty($current_rows)) {
            $i = 0;
            /** @var \Types_Field_Group_Repeatable_Item $item */
            foreach ($current_rows as $row) {
                if ($i === $index) {
                    $row_id = $row->get_wp_post()->ID;
                    break;
                }
                $i++;
            }
        }
        // Create new repeater group row if existing one was not found.
        if (empty($row_id)) {
            $row_id = $this->createRow($index);
        }
        return $row_id;
    }

    /**
     * @param $index
     * @return false|int|\WP_Error
     */
    protected function createRow($index) {
        // Create row post.
        $groupRow = $this->getRepeatableGroupRow();
	    $row_id = wp_insert_post([
		    'post_title' => 'Row ' . ($index + 1),
		    'post_type' => $groupRow ? $groupRow->post_name : $this->groupPost->post_name,
		    'post_status' => 'publish'
	    ]);
	    try {
		    $association = $this->associationFactory->create(
			    $this->relationship,
			    $groupRow ? $groupRow->ID : $this->getPostID(),
			    $row_id,
			    0
		    );
		    \PMTI_Plugin::getInstance()->get_database_layer_factory()->association_persistence()->insert_association($association);

	    } catch (\Exception $e) {
		    wp_delete_post($row_id);
		    unset($row_id);
	    }
        return $row_id ?? false;
    }

    /**
     * Delete a item of a repeatable group
     *
     * - deletes post
     * - deletes translations
     * - deletes associations
     *
     * @param WP_Post $item
     *
     * @return bool
     */
    public function delete_item( WP_Post $item ) {
        try {
            do_action( 'toolset_do_m2m_full_init' );

            $rfg_mapper           = new ToolsetLegacy();
            $post_type_repository = Toolset_Post_Type_Repository::get_instance();
            $relationship_service = new Toolset_Relationship_Service();

            if ( $rfg_mapper->delete_item_by_post(
                $item,
                $post_type_repository,
                $relationship_service,
                $this->get_wpml() )
            ) {
                // all as expected
                return true;
            }
        } catch ( \Exception $e ) {
            return false;
        }

        // no error, but also not deleted
        return false;
    }

    /**
     * Get global WPML (sitepress) class
     * @return null|SitePress
     */
    private function get_wpml() {
        global $sitepress;

        return $sitepress instanceof SitePress ? $sitepress : null;
    }

    /**
     * @param $fields
     * @return bool
     */
    protected function isImportRow($fields){
        $isImportRow = $this->isIgnoreEmpties() ? false : true;
        if (!$isImportRow){
            /** @var Field $field */
            foreach ($fields as $field){
                if ($field->isNotEmpty()){
                    $isImportRow = true;
                    break;
                }
            }
        }
        return $isImportRow;
    }

    /**
     * @param $fields
     * @return int
     */
    protected function getCountRows($fields){
        $countRows = 0;
        /** @var Field $field */
        foreach ($fields as $field){
            if ($field->getType() == 'repeatable_group') continue;
            $field->importData = $this->getImportData();
            $count = $field->getCountValues();
            if ($count > $countRows){
                $countRows = $count;
            }
        }
        return $countRows;
    }

    /**
     * @return int
     */
    public function getCountValues() {
        $countRows = 0;
        /** @var Field $field */
        foreach ($this->getSubFields() as $field){
            $field->importData = $this->getImportData();
            $count = $field->getCountValues();
            if ($count > $countRows){
                $countRows = $count;
            }
        }
        return $countRows;
    }

    /**
     * Get repeatable group sub fields.
     *
     * @return array|void
     */
    public function initSubFields() {
        $post = $this->getData('post');
        switch ($post['custom_type']) {
            case 'shop_customer':
            case 'import_users':
                $optionName = 'wpcf-usermeta';
                break;
            case 'taxonomies':
                $optionName = 'wpcf-termmeta';
                break;
            default:
                $optionName = 'wpcf-field';
                break;
        }

        $this->fieldsData = wpcf_admin_fields_get_fields_by_group(
            $this->groupPost->ID,
            'slug',
            false,
            false,
            false,
            TYPES_CUSTOM_FIELD_GROUP_CPT_NAME,
            $optionName,
            true
        );

        foreach ($this->getFieldsData() as $fieldData) {
            $field = FieldFactory::create($fieldData, $post, $this->getFieldName(), $this);
            $this->subFields[] = $field;
        }
    }

    /**
     * @return string
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode) {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getDelimiter() {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

    /**
     * @return boolean
     */
    public function isIgnoreEmpties() {
        return $this->ignoreEmpties;
    }

    /**
     * @param boolean $ignoreEmpties
     */
    public function setIgnoreEmpties($ignoreEmpties) {
        $this->ignoreEmpties = $ignoreEmpties;
    }

    /**
     * @return array
     */
    public function getFieldsData() {
        return $this->fieldsData;
    }

    /**
     * @return string
     */
    public function getRowIndex() {
        return $this->rowIndex;
    }

    /**
     * @param string $index
     */
    public function setRowIndex($index) {
        $this->rowIndex = $index;
    }
}
