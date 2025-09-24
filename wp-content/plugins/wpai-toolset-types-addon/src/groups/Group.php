<?php

namespace wpai_toolset_types_add_on\groups;

use wpai_toolset_types_add_on\fields\Field;
use wpai_toolset_types_add_on\fields\FieldFactory;

/**
 * Class Group
 * @package wpai_toolset_types_add_on\groups
 */
class Group implements GroupInterface {

    /**
     * @var
     */
    public $post;

    /**
     * @var
     */
    public $group;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var array
     */
    public $fieldsData = [];

    /**
     * Group constructor.
     * @param $group
     */
    public function __construct($group, $post) {
        $this->group = $group;
        $this->post = $post;
        $this->initFields();
    }

    /**
     *  Create field instances
     */
    public function initFields(){
        if ($this->post['custom_type'] == 'import_users' || $this->post['custom_type'] == 'shop_customer') {
            $optionName = 'wpcf-usermeta';
        } else if($this->post['custom_type'] == 'taxonomies') {
            $optionName = 'wpcf-termmeta';
        } else {
            $optionName = 'wpcf-field';
        }

        $this->fieldsData = wpcf_admin_fields_get_fields_by_group($this->group['id'], 'slug', false, false, false,
            TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $optionName, true);

        foreach ($this->getFieldsData() as $fieldData){
            $field = FieldFactory::create($fieldData, $this->getPost());
            $this->fields[] = $field;
        }
    }

    /**
     * @return array
     */
    public function getFieldsData() {
        return $this->fieldsData;
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return mixed
     */
    public function getPost() {
        return $this->post;
    }

    /**
     *  Render group
     */
    public function view() {
        $this->renderHeader();
        foreach ($this->getFields() as $field){
            $field->view();
        }
        $this->renderFooter();
    }

    /**
     *  Render group header
     */
    protected function renderHeader(){
        $filePath = __DIR__ . '/templates/header.php';
        if (is_file($filePath)) {
            extract($this->group);
            include $filePath;
        }
    }

    /**
     *  Render group footer
     */
    protected function renderFooter(){
        $filePath = __DIR__ . '/templates/footer.php';
        if (is_file($filePath)) {
            include $filePath;
        }
    }

    /**
     * @param $parsingData
     * @return array
     */
    public function parse($parsingData) {
        /** @var Field $field */
        foreach ($this->getFields() as $field){
            $xpath = empty($parsingData['import']->options['wpcs_fields'][$field->getFieldKey()]['value']) ? "" : $parsingData['import']->options['wpcs_fields'][$field->getFieldKey()]['value'];
            $field->parse($xpath, $parsingData);
        }
    }

    /**
     * @param $importData
     * @param array $args
     */
    public function import($importData, $args = []) {
        /** @var Field $field */
        foreach ($this->getFields() as $field){
            $field->import($importData, $args);
        }
    }

    /**
     * @param $importData
     */
    public function saved_post($importData){
        /** @var Field $field */
        foreach ($this->getFields() as $field){
            $field->saved_post($importData);
        }
    }
}