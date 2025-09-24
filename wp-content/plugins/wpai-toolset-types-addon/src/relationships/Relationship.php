<?php

namespace wpai_toolset_types_add_on\relationships;

use OTGS\Toolset\Common\Relationships\API\Factory;
use PMTI_Plugin;
use Toolset_Association_Factory;
use Toolset_Element_Factory;
use Toolset_Relationship_Database_Issue_Missing_Element;
use Toolset_Relationship_Definition;
use Toolset_Relationship_Definition_Translator;
use Toolset_Relationship_Role;
use Toolset_Relationship_Role_Child;
use Types_Viewmodel_Related_Content;
use Types_Viewmodel_Related_Content_Factory;
use wpai_toolset_types_add_on\fields\Field;


/**
 * Class Relationship
 * @package wpai_toolset_types_add_on\relationships
 */
class Relationship extends Field {

    /**
     *  One to one relationship.
     */
    const TYPE_ONE_TO_ONE = 'onetoone';

    /**
     *  One to many relationship.
     */
    const TYPE_ONE_TO_MANY = 'onetomany';

    /**
     *  Many to many relationship.
     */
    const TYPE_MANY_TO_MANY = 'manytomany';

    /**
     *  Import parent constant.
     */
    const IMPORTING_PARENT = 'import-parent';

    /**
     *  Import child constant.
     */
    const IMPORTING_CHILD = 'impor-child';

    /**
     * @var
     */
    public $post;

    /**
     * @var
     */
    private $importType;

    /**
     * @var
     */
    private $delim;

    /**
     * @var
     */
    public $options;

	/**
	 * @var Toolset_Relationship_Definition
	 */
	public $relationship;

    /**
     * @var
     */
    private $relationshipData;

	/** @var Toolset_Element_Factory */
	private $element_factory;

	/** @var Factory */
	protected $relationships_factory;

    /** @var \OTGS\Toolset\Common\WPML\WpmlService */
    protected $wpml_service;

    /** @var Types_Viewmodel_Related_Content_Factory */
    private $related_content_factory;

    /**
     * Relationship constructor.
     *
     * @param $field
     * @param $post
     * @param string $field_name
     * @param false $parent_field
     */
    public function __construct($field, $post, $field_name = "", $parent_field = false) {
        parent::__construct($field, $post, $field_name, $parent_field);
        $relationships = \PMTI_Plugin::getInstance()->get_database_layer_factory()->relationship_database_operations()->load_all_relationships();
        if (!empty($relationships)) {
            foreach ($relationships as $relationship) {
                if ((int) $relationship->id === (int) $this->data['field']['id']) {
                    $this->relationshipData = $relationship;
                    break;
                }
            }
        }
        if (!empty($this->relationshipData)) {
	        $definition_translator = new Toolset_Relationship_Definition_Translator();
	        $this->relationship = $definition_translator->from_database_row($this->relationshipData);
	        $this->element_factory = new Toolset_Element_Factory();
	        $this->relationships_factory = new Factory(PMTI_Plugin::getInstance()->get_database_layer_factory());
	        $this->wpml_service = new \OTGS\Toolset\Common\WPML\WpmlService();
	        $this->related_content_factory = new Types_Viewmodel_Related_Content_Factory($this->relationships_factory, $this->wpml_service);
        }
    }

    /**
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
     * @return bool|void
     */
    public function import($importData, $args = []) {
        // Check is relationship needs to be imported.
        if (empty($importData['articleData']['ID']) || pmti_is_wpcs_relationship_update_allowed($this->relationshipData->slug, $importData['import']->options)) {
            return $this->saveRelationship($importData);
        }
    }

    /**
     * @param $importData
     */
    public function saveRelationship($importData) {
        $this->importData = $importData;
        $this->log('- Deleting relationships from ' . $this->relationshipData->display_name_plural);
        $this->deletePreviousRelationships($this->getPostID());
        if ($this->relationship->get_cardinality()->is_one_to_one()) {
	        $fieldValue = $this->getFieldValue();
	        if (!empty($fieldValue)) {
		        if (!is_numeric($fieldValue)) {
			        $fieldValue = $this->matchFieldToPost($fieldValue);
			        if (!$fieldValue) {
				        $this->getLogger() and call_user_func($this->getLogger(), __('Post <i>' . $this->getFieldValue() . '</i> not found, not importing relationship', PMTI_Plugin::TEXT_DOMAIN));
				        return;
			        }
		        }
		        if ($this->importingParent()) {
			        $parentId = $this->getPostID();
			        $childId = $fieldValue;
		        } else {
			        $parentId = $fieldValue;
			        $childId = $this->getPostID();
		        }
		        $this->log('- Importing relationship to ' . $this->relationshipData->display_name_plural);
		        $this->saveRelationshipToDb($parentId, $childId);
	        }
        }
	    if ($this->relationship->get_cardinality()->is_one_to_many() || $this->relationship->get_cardinality()->is_many_to_one()) {
		    $fieldRawValue = urldecode($this->getFieldValue());
		    if (!empty($fieldRawValue)) {
			    if (empty($this->delim)) {
				    $fieldValues = preg_split('/\r\n|\r|\n/', $fieldRawValue);
			    } else {
				    $fieldValues = explode($this->delim, $fieldRawValue);
			    }
			    $fieldValues = array_map('trim', $fieldValues);
                $this->log('- Importing relationship to ' . $this->relationshipData->display_name_plural);
			    foreach ($fieldValues as $fieldValue) {
				    $fieldValue = trim($fieldValue);  // cleanup whitespace from user provided values
				    if (!is_numeric($fieldValue)) {
					    $origFieldValue = $fieldValue; // save the original value to use in the message if no post found
					    $fieldValue = $this->matchFieldToPost($fieldValue);
					    if (!$fieldValue) {
						    $this->getLogger() and call_user_func($this->getLogger(), __('Post <i>' . $origFieldValue . '</i> not found, not importing relationship', PMTI_Plugin::TEXT_DOMAIN));
						    continue;
					    }
				    }
				    $fieldValue = trim($fieldValue);
				    if ($this->importingParent()) {
					    $parentId = $this->getPostID();
					    $childId = $fieldValue;
				    } else {
					    $parentId = $fieldValue;
					    $childId = $this->getPostID();
				    }
				    $this->saveRelationshipToDb($parentId, $childId);
			    }
		    }
	    }
	    if ($this->relationship->get_cardinality()->is_many_to_many()) {
		    $fieldRawValue = urldecode($this->getFieldValue());
		    if (!empty($fieldRawValue)) {
			    if (empty($this->delim)) {
				    $fieldValues = preg_split('/\r\n|\r|\n/', $fieldRawValue);
			    } else {
				    $fieldValues = explode($this->delim, $fieldRawValue);
			    }
			    $fieldValues = array_map('trim', $fieldValues);
			    $this->log('- Importing relationship to ' . $this->relationshipData->display_name_plural);
			    $this->log('-- Number of relationships to import: ' . count($fieldValues));
			    $parentId = $this->getPostID();
			    foreach ($fieldValues as $fieldValue) {
				    if (!is_numeric($fieldValue)) {
					    $fieldValue = $this->matchFieldToPost($fieldValue);
					    if (!$fieldValue) {
						    $this->log('Post <i>' . $fieldValue . '</i> not found, not importing relationship');
						    continue;
					    }
				    }
				    if ($this->importingParent()) {
					    $childId = $fieldValue;
				    } else {
					    $parentId = $fieldValue;
					    $childId = $this->getPostID();
				    }
                    $this->saveRelationshipToDb($parentId, $childId);
			    }
		    }
	    }
    }

    /**
     * @param mixed $importType
     */
    public function setImportType($importType) {
        $this->importType = $importType;
    }

    /**
     * @param mixed $delim
     */
    public function setDelim($delim) {
        $this->delim = $delim;
    }

    /**
     * @param $parentId
     * @param $childId
     * @return mixed
     */
    private function checkCardinality($parentId, $childId) {
	    try {
    	    // This will throw when the elements don't exist
	        $parent = $this->element_factory->get_element( $this->relationship->get_parent_domain(), $parentId );
	        $child = $this->element_factory->get_element( $this->relationship->get_child_domain(), $childId );

	        // We need to make sure the association is allowed.
	        $for_role = new Toolset_Relationship_Role_Child();
	        $potential_association_query = new ToolsetLegacyQuery(
		        $this->relationship, $for_role, $parent, [], \PMTI_Plugin::getInstance()->get_database_layer_factory()
	        );

	        $can_associate_check = $potential_association_query->check_single_element( $child );
	        if ( $can_associate_check->is_error() ) {
		        $this->log('-- Current parent cardinality: ' . $can_associate_check->get_message());
		        return false;
	        }
        } catch (\Exception $e) {
	        $this->log('ERROR: ' . $e->getMessage());
	        return false;
        }

	    return true;
    }

	/**
     * Delete current association for provided post.
     *
	 * @param $pid
	 */
    private function deletePreviousRelationships($pid) {
	    try {
            $results = $this->getCurrentAssociations($pid);
		    if ( ! empty( $results ) ) {
		        global $wpdb;
                foreach ($results as $related_post_id) {
                    if ($this->importingParent()) {
                        toolset_disconnect_posts($this->relationship->get_slug(), $pid, $related_post_id);
                    } else {
                        toolset_disconnect_posts($this->relationship->get_slug(), $related_post_id, $pid);
                    }
                    // Delete intermediary posts.
                    if ($this->relationship->get_intermediary_post_type()) {
                        if ($this->importingParent()) {
                            $intermediary_posts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = '%s' AND post_type = '%s'", $this->relationship->get_display_name_plural() . ": " . $pid . " - " . $related_post_id, $this->relationship->get_intermediary_post_type()) );
                        } else {
                            $intermediary_posts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = '%s' AND post_type = '%s'", $this->relationship->get_display_name_plural() . ": " . $related_post_id . " - " . $pid, $this->relationship->get_intermediary_post_type()) );
                        }
                        if (!empty($intermediary_posts)) {
                            foreach ( $intermediary_posts as $intermediary_post ) {
                                wp_delete_post($intermediary_post->ID);
                            }
                        }
                    }
                }
		    }
	    } catch (\Exception $e) {
		    $this->log('ERROR: ' . $e->getMessage());
	    }
    }

    /**
     * Get current associations to provided post.
     *
     * @param $post_id
     * @return array|false|int[]|\IToolset_Association[]|\IToolset_Element[]|mixed
     */
    private function getCurrentAssociations($post_id) {

        $results = FALSE;

        try {

            $post_type = $this->getImportOption('custom_type');

            $role = $this->get_role($post_type);
            $related_content_viewmodel = $this->get_model_by_relationship( $role );

            $related_content = $related_content_viewmodel->get_related_content_array(
                (int) $post_id, $post_type, 1, 1000, $role
            );

            $related_content_ids = [];
            if (!empty($related_content['data'])) {
                foreach ($related_content['data'] as $item) {
                    $related_content_ids[] = $item['post_id'];
                }
            }
            return $related_content_ids;

        } catch (\Exception $e) {
            $this->log('ERROR: ' . $e->getMessage());
        }
        return $results;
    }

    /**
     * Gets the role for a post type
     *
     * @param string $post_type - Post type of importing post.
     *
     * @return string
     * @since m2m
     */
    private function get_role( $post_type ) {
        return in_array( $post_type, $this->relationship->get_parent_type()->get_types(), true )
            ? Toolset_Relationship_Role::PARENT
            : Toolset_Relationship_Role::CHILD;
    }

    /**
     * Gets the model by the role and relationship
     *
     * @param String $role Role.
     *
     * @return Types_Viewmodel_Related_Content
     * @since m2m
     */
    private function get_model_by_relationship( $role ) {
        return $this->related_content_factory->get_model_by_relationship( $role, $this->relationship );
    }

    /**
     * Check cardinality between parent and child and create new association.
     *
     * @param $parentId
     * @param $childId
     */
    private function saveRelationshipToDb($parentId, $childId) {
        $this->log('-- Importing relationship Post ID ' . $parentId . ' => Child ID ' . $childId);
        if (!$this->checkCardinality($parentId, $childId)) {
            $this->log('-- Maximum cardinality achieved, not importing relationship');
        } else {
            toolset_connect_posts($this->relationship->get_slug(), $parentId, $childId);
        }
    }

    /**
     * @return mixed
     */
    private function matchFieldToPost($titleOrSlug) {

        global $wpdb;

        if ($this->importingParent()) {
            $post_types = $this->relationship->get_child_type()->get_types();
        } else {
            $post_types = $this->relationship->get_parent_type()->get_types();
        }

        $fieldValue = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE (post_name = %s OR post_title = %s) AND post_type IN ( '" . implode("','", $post_types) . "' ) AND post_status = 'publish' LIMIT 1", $titleOrSlug, $titleOrSlug));

        return $fieldValue;
    }

    /**
     * Returns the related content viewmodel
     *
     * It is used for testing purposes, if is set during class instance, it will return, other case it will be
     * generated.
     *
     * @param string $role Relationship element role.
     * @param Toolset_Relationship_Definition $definition The relationship.
     *
     * @return Types_Viewmodel_Related_Content
     * @since m2m
     */
    private function get_related_content_viewmodel( $role, $definition ) {
        return $this->related_content_factory->get_model_by_relationship( $role, $definition );
    }

    /**
     * @return bool
     */
    private function importingParent() {
        if ($this->importType == self::IMPORTING_PARENT) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $message
     */
    private function log($message) {
        $this->getLogger() and call_user_func($this->getLogger(), __($message, PMTI_Plugin::TEXT_DOMAIN));
    }
}