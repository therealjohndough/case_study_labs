<?php

namespace wpai_toolset_types_add_on\relationships;

use SitePress;
use Toolset_Post_Type_Repository;
use Toolset_Relationship_Service;
use Types_Field_Group_Repeatable_Mapper_Legacy;
use WP_Post;

require_once(TYPES_ABSPATH . '/application/models/field/group/mapper/interface.php');
require_once(TYPES_ABSPATH . '/application/models/field/group/repeatable/mapper/legacy.php');

class ToolsetLegacy extends Types_Field_Group_Repeatable_Mapper_Legacy {

    /** @var \OTGS\Toolset\Common\Relationships\API\Factory */
    private $relationships_factory;

    /** @var \OTGS\Toolset\Common\WPML\WpmlService */
    private $wpml_service;


    /**
     * Types_Field_Group_Repeatable_Mapper_Legacy constructor.
     */
    public function __construct() {
        $this->relationships_factory = new \OTGS\Toolset\Common\Relationships\API\Factory();
        $this->wpml_service = \OTGS\Toolset\Common\WPML\WpmlService::get_instance();
    }

    public function delete_item_by_post(WP_Post $item, Toolset_Post_Type_Repository $post_type_repository, Toolset_Relationship_Service $relationship_service, SitePress $wpml = null) {
        // Check that the item belongs to an repeatable field group
        $post_type_the_item_belongs_to = $post_type_repository->get( $item->post_type );
        if ( $post_type_the_item_belongs_to && ! $post_type_the_item_belongs_to->is_repeating_field_group() ) {
            // no item of a repeatable field group
            throw new \InvalidArgumentException( 'The item is not part of a repeatable field group' );
        }

        // Get children items (nested rfgs)
        if ( $children = $relationship_service->find_children_ids_by_parent_id( $item->ID ) ) {
            // remove children
            foreach ( $children as $child_id ) {
                if ( $item_post = get_post( $child_id ) ) {
                    $this->delete_item_by_post( $item_post, $post_type_repository, $relationship_service, $wpml );
                }
            }
        }

        if( $this->relationships_factory->database_operations()->requires_default_language_post() ) {
            return $this->delete_item_and_translations_by_default_post( $item, $wpml );
        }

        // Delete the post and the whole translation group if the post belongs to one.
        $post_ids_to_delete = [ (int) $item->ID ];

        $item_trid = $this->wpml_service->get_post_trid( $item->ID );
        if ( $item_trid ) {
            $post_ids_to_delete = array_merge(
                $this->wpml_service->get_post_translations( $item_trid ),
                $post_ids_to_delete
            );
        }

        $post_ids_to_delete = array_unique( $post_ids_to_delete );
        foreach( $post_ids_to_delete as $post_id ) {
            wp_delete_post( $post_id );
        }

        return true;
    }
}