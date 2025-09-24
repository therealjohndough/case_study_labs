<?php

use wpai_toolset_types_add_on\groups\Group;
use wpai_toolset_types_add_on\groups\GroupFactory;
use wpai_toolset_types_add_on\relationships\Relationship;

/**
 * Class PMTI_Import_Record
 */
class PMTI_Import_Record extends PMTI_Model_Record {

    /**
     * @var Group[]
     */
    public $groups = [];

    /**
     * @var Relationship[]
     */
    public $relationships = [];

    /**
     * Initialize model instance
     * @param array [optional] $data Array of record data to initialize object with
     */
    public function __construct($data = []) {
        parent::__construct($data);
        $this->setTable(PMXI_Plugin::getInstance()
                ->getTablePrefix() . 'imports');
    }

    /**
     * @param array $parsingData [import, count, xml, logger, chunk, xpath_prefix]
     */
    public function parse($parsingData){

        add_filter('user_has_cap', [
            $this,
            '_filter_has_cap_unfiltered_html'
        ]);
        kses_init(); // do not perform special filtering for imported content

        $parsingData['chunk'] == 1 and $parsingData['logger'] and call_user_func($parsingData['logger'], __('Composing toolset fields...', PMTI_Plugin::TEXT_DOMAIN));

        if (!empty($parsingData['import']->options['wpcs_groups'])){
            $toolsetGroups = $parsingData['import']->options['wpcs_groups'];
            if (!empty($toolsetGroups)) {
                foreach ($toolsetGroups as $toolsetGroupID => $status) {
                    if (!$status) {
                        continue;
                    }
                    $this->groups[] = GroupFactory::create(['id' => $toolsetGroupID], $parsingData['import']->options);
                }
            }
            foreach ($this->groups as $group){
                $group->parse($parsingData);
            }
        }

        if (!empty($parsingData['import']->options['wpcs_relationships'])) {
            $toolsetRelationships = $parsingData['import']->options['wpcs_relationships'];
            if (!empty($toolsetRelationships)) {
                foreach ($toolsetRelationships as $toolsetRelationshipID => $value) {
                    $relationship = new Relationship(['id' => $toolsetRelationshipID], $parsingData['import']->options);
                    $relationship->setImportType($value['import_type']);
                    if (isset($value['delim'])) {
                        $relationship->setDelim($value['delim']);
                    }
                    $relationship->parse($value['value'], $parsingData);
                    $this->relationships[] = $relationship;
                }
            }
        }

        remove_filter('user_has_cap', [
            $this,
            '_filter_has_cap_unfiltered_html'
        ]);
        kses_init(); // return any filtering rules back if they has been disabled for import procedure
    }

    /**
     * @param $importData [pid, i, import, articleData, xml, is_cron, xpath_prefix]
     */
    public function import($importData){
        $importData['logger'] and call_user_func($importData['logger'], __('<strong>Toolset Types ADD-ON:</strong>', PMTI_Plugin::TEXT_DOMAIN));
        foreach ($this->groups as $group) {
            $group->import($importData);
        }
        if (!empty($this->relationships)) {
	        foreach ($this->relationships as $relationship) {
	        	if (!empty($relationship->relationship)) {
			        $relationship->import($importData);
		        }
	        }
        }
    }

    /**
     * @param $importData [pid, import, logger, is_update]
     */
    public function saved_post($importData){
        foreach ($this->groups as $group){
            $group->saved_post($importData);
        }
    }

    /**
     * @param $caps
     * @return mixed
     */
    public function _filter_has_cap_unfiltered_html($caps) {
        $caps['unfiltered_html'] = TRUE;
        return $caps;
    }

    /**
     * @param $var
     * @return bool
     */
    public function filtering($var) {
        return ("" == $var) ? FALSE : TRUE;
    }
}
