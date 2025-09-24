<?php

namespace wpai_toolset_types_add_on\relationships;

use IToolset_Element;
use IToolset_Post;
use IToolset_Relationship_Definition;
use OTGS\Toolset\Common\Relationships\API\RelationshipRole;
use OTGS\Toolset\Common\Relationships\API\RelationshipRoleParentChild;
use OTGS\Toolset\Common\Relationships\DatabaseLayer\DatabaseLayerFactory;
use OTGS\Toolset\Common\Relationships\DatabaseLayer\Version2\PotentialAssociationQuery\PostQuery;
use OTGS\Toolset\Common\Result\SingleResult;
use Toolset_Element_Factory;
use Toolset_Relationship_Cardinality;
use Toolset_Relationship_Role;

require_once(TYPES_ABSPATH . '/vendor/toolset/toolset-common/inc/m2m/API/PotentialAssociationQuery.php');
require_once(TYPES_ABSPATH . '/vendor/toolset/toolset-common/inc/m2m/DatabaseLayer/PotentialAssociation/PostQuery.php');
require_once(TYPES_ABSPATH . '/vendor/toolset/toolset-common/inc/m2m/DatabaseLayer/Version2/PotentialAssociationQuery/PostQuery.php');

class ToolsetLegacyQuery extends PostQuery {

    /** @var IToolset_Relationship_Definition */
    protected $relationship;

    /** @var RelationshipRole */
    protected $target_role;

    /** @var IToolset_Element */
    protected $for_element;

    /** @var array */
    protected $args;

    /** @var int|null */
    protected $found_results;

    /** @var Toolset_Element_Factory */
    protected $element_factory;

    /** @var DatabaseLayerFactory */
    protected $database_layer_factory;

    /**
     * Toolset_Potential_Association_Query constructor.
     *
     * @param IToolset_Relationship_Definition $relationship Relationship to query for.
     * @param RelationshipRoleParentChild $target_role Element role. Only parent
     *     or child are accepted.
     * @param IToolset_Element $for_element Element that may be connected with the result of the query.
     * @param array $args Additional query arguments:
     *     - search_string: string
     *     - count_results: bool
     *     - items_per_page: int
     *     - page: int
     *     - wp_query_override: array
     *     - exclude_elements: IToolset_Element[] Elements to exclude from the results and when checking
     *       whether the target element ($for_element) can accept another association.
     *     - post_status: string[]|string If provided, it will override the standard value ('publish').
     *     POST_STATUS_AVAILABLE is also being accepted.
     * @param Toolset_Element_Factory|null $element_factory_di
     * @param DatabaseLayerFactory|null $database_layer_factory
     */
    public function __construct(
        IToolset_Relationship_Definition $relationship,
        RelationshipRoleParentChild $target_role,
        IToolset_Element $for_element,
        $args,
        DatabaseLayerFactory $database_layer_factory,
        Toolset_Element_Factory $element_factory_di = null
    ) {
        $this->relationship = $relationship;
        $this->for_element = $for_element;
        $this->target_role = $target_role;
        $this->args = $args;

        if ( ! $relationship->get_element_type( $target_role->other()->get_name() )->is_match( $for_element ) ) {
            throw new \InvalidArgumentException( 'The element to connect with doesn\'t belong to the relationship definition provided.' );
        }

        $this->element_factory = ( null === $element_factory_di ? new Toolset_Element_Factory() : $element_factory_di );
        $this->database_layer_factory = $database_layer_factory;
        $this->return_true = static function() { return true; };
    }

    /**
     * Check whether a specific single element can be associated.
     *
     * The relationship, target role and the other element are those provided in the constructor.
     *
     * @param IToolset_Element $association_candidate Element that wants to be associated.
     * @param bool $check_is_already_associated Perform the check that the element is already associated for distinct
     *     relationships. Default is true. Set to false only if the check was performed manually before.
     *
     * @return SingleResult Result with an user-friendly message in case the association is denied.
     * @since 2.5.6
     */
    public function check_single_element( IToolset_Element $association_candidate, $check_is_already_associated = true ) {

        if ( ! $this->relationship->get_element_type( $this->target_role )->is_match( $association_candidate ) ) {
            return new SingleResult( false, __( 'The element has a wrong type or a domain for this relationship.', 'wpv-views' ) );
        }

        $cardinality_check_result = $this->check_cardinality_for_role( $this->for_element, $this->target_role->other() );
        if ( $cardinality_check_result->is_error() ) {
            return $cardinality_check_result;
        }

        $cardinality_check_result = $this->check_cardinality_for_role( $association_candidate, $this->target_role );
        if ( $cardinality_check_result->is_error() ) {
            return $cardinality_check_result;
        }

        // We also need to check $this->relationship->has_scope() when/if the scope support is implemented.

        /** @var IToolset_Element[] $parent_and_child */
        $parent_and_child = Toolset_Relationship_Role::sort_elements( $association_candidate, $this->for_element, $this->target_role );

        /**
         * toolset_can_create_association
         *
         * Allows for forbidding an association between two elements to be created.
         * Note that it cannot be used to force-allow an association. The filter will be applied only if all
         * conditions defined by the relationship are met.
         *
         * @param bool $result
         * @param int $parent_id
         * @param int $child_id
         * @param string $relationship_slug
         *
         * @since m2m
         */
        $filtered_result = apply_filters(
            'toolset_can_create_association',
            true,
            $parent_and_child[0]->get_id(),
            $parent_and_child[1]->get_id(),
            $this->relationship->get_slug()
        );

        if ( true !== $filtered_result ) {
            if ( is_string( $filtered_result ) ) {
                $message = esc_html( $filtered_result );
            } else {
                $message = __( 'The association was disabled by a third-party filter.', 'wpv-views' );
            }

            return new SingleResult( false, $message );
        }

        return new SingleResult( true );
    }

    /**
     * @param IToolset_Element $element Element to check.
     * @param RelationshipRoleParentChild $role Provided element's role in the relationship.
     *
     * @return SingleResult
     */
    private function check_cardinality_for_role( IToolset_Element $element, RelationshipRoleParentChild $role ) {
        $maximum_limit = $this->relationship->get_cardinality()->get_limit( $role->other()
            ->get_name(), Toolset_Relationship_Cardinality::MAX );

        if ( $maximum_limit !== Toolset_Relationship_Cardinality::INFINITY ) {
            $association_count = $this->get_number_of_already_associated_elements( $role, $element );
            if ( $association_count >= $maximum_limit ) {
                $message = sprintf(
                    __( 'The element %s has already the maximum allowed amount of associations (%d) as %s in the relationship %s.', 'wpv-views' ),
                    $element->get_title(),
                    $maximum_limit, // this will be always a meaningful number - for INFINITY, this block is skipped entirely.
                    $this->relationship->get_role_name( $role ),
                    $this->relationship->get_display_name()
                );

                return new SingleResult( false, esc_html( $message ) );
            }
        }

        return new SingleResult( true );
    }


    private function get_number_of_already_associated_elements(
        RelationshipRoleParentChild $role, IToolset_Element $element
    ) {
        $query = $this->database_layer_factory->association_query();

        return $query
            ->add( $query->relationship_slug( $this->relationship->get_slug() ) )
            ->add( $query->element( $element, $role ) )
            ->add( $query->do_and(
                array_map( static function ( IToolset_Post $post ) use ( $query, $role ) {
                    return $query->not( $query->element( $post, $role->other() ) );
                }, $this->get_exclude_elements() )
            ) )
            ->use_cache(false)
            ->do_not_add_default_conditions() // include all existing associations
            ->get_found_rows_directly();
    }

    private function get_exclude_elements() {
        return array_map( static function ( $element ) {
            if ( ! $element instanceof IToolset_Post ) {
                throw new \InvalidArgumentException(
                    'Invalid element provided in the exclude_elements query argument. Only posts are accepted.'
                );
            }

            return $element;
        }, toolset_ensarr( toolset_getarr( $this->args, 'exclude_elements' ) ) );
    }
}