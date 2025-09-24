<?php

namespace wpai_toolset_types_add_on;

use PMXI_API;
use wpai_toolset_types_add_on\fields\Field;

/**
 * Class ToolsetService
 * @package wpai_toolset_types_add_on\toolset
 */
final class ToolsetService {

    /**
     *
     * Set field value
     *
     * @param \wpai_toolset_types_add_on\fields\Field $field
     * @param $pid
     * @param $name
     * @param $value
     */
    public static function add_post_meta(Field $field, $pid, $name, $value) {

        $cf_value = apply_filters('pmxi_wpcs_custom_field', $value, $pid, $name);

        switch ($field->getImportType()) {
            case 'shop_customer':
            case 'import_users':
                add_user_meta($pid, $name, $cf_value);
                break;
            case 'taxonomies':
                $option = $field->getTaxonomyType() . '_' . $pid . '_' . $name;
                if (strpos($cf_value, 'field_') === 0 && strpos($name, '_') === 0) {
                    $option = '_' . $field->getTaxonomyType() . '_' . $pid . $name;
                }
                update_option($option, $cf_value);
                add_term_meta($pid, $name, $value);
                break;
            default:
                add_post_meta($pid, $name, $cf_value);
                break;
        }
    }

    /**
     *
     * Set field value
     *
     * @param \wpai_toolset_types_add_on\fields\Field $field
     * @param $pid
     * @param $name
     * @param $value
     */
    public static function update_post_meta(Field $field, $pid, $name, $value) {
        $cf_value = apply_filters('pmxi_wpcs_custom_field', $value, $pid, $name);

        switch ($field->getImportType()) {
            case 'shop_customer':
            case 'import_users':
                update_user_meta($pid, $name, $cf_value);
                break;
            case 'taxonomies':
                update_term_meta($pid, $name, $value);
                break;
            default:
                update_post_meta($pid, $name, $cf_value);
                break;
        }
    }

    /**
     *
     * Get field value
     *
     * @param Field $field
     * @param $pid
     * @param $name
     * @return mixed
     */
    public static function get_post_meta(Field $field, $pid, $name, $single = true) {
        switch ($field->getImportType()) {
            case 'shop_customer':
            case 'import_users':
                $value = get_user_meta($pid, $name, $single);
                break;
            case 'taxonomies':
                $value = get_term_meta($pid, $name, $single);
                break;
            default:
                $value = get_post_meta($pid, $name, $single);
                break;
        }
        return $value;
    }

    /**
     *
     * Delete field value
     *
     * @param Field $field
     * @param $pid
     * @param $name
     * @return mixed
     */
    public static function delete_post_meta(Field $field, $pid, $name) {
        switch ($field->getImportType()) {
            case 'shop_customer':
            case 'import_users':
                $value = delete_user_meta($pid, $name);
                break;
            case 'taxonomies':
                $value = delete_term_meta($pid, $name);
                break;
            default:
                $value = delete_post_meta($pid, $name);
                break;
        }
        return $value;
    }

    /**
     *
     * Assign taxonomy terms with particular post
     *
     * @param $pid
     * @param $assign_taxes
     * @param $tx_name
     * @param bool $logger
     */
    public static function associate_terms($pid, $assign_taxes, $tx_name, $logger = FALSE) {

        global $wpdb;

        $term_ids = wp_get_object_terms($pid, $tx_name, ['fields' => 'ids']);

        $assign_taxes = (is_array($assign_taxes)) ? array_filter($assign_taxes) : false;

        if (!empty($term_ids) && !is_wp_error($term_ids)) {
            $in_tt_ids = "'" . implode("', '", $term_ids) . "'";
            $wpdb->query("UPDATE {$wpdb->term_taxonomy} SET count = count - 1 WHERE term_taxonomy_id IN ($in_tt_ids) AND count > 0");
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->term_relationships} WHERE object_id = %d AND term_taxonomy_id IN ($in_tt_ids)", $pid));
        }

        if (empty($assign_taxes)) return;

        $values = [];
        $term_order = 0;

        $term_ids = [];
        foreach ($assign_taxes as $tt) {
            do_action('wp_all_import_associate_term', $pid, $tt, $tx_name);
            $values[] = $wpdb->prepare("(%d, %d, %d)", $pid, $tt, ++$term_order);
            $term_ids[] = $tt;
        }

        $in_tt_ids = "'" . implode("', '", $term_ids) . "'";
        $wpdb->query("UPDATE {$wpdb->term_taxonomy} SET count = count + 1 WHERE term_taxonomy_id IN ($in_tt_ids)");

        if ($values) {
            if (false === $wpdb->query("INSERT INTO {$wpdb->term_relationships} (object_id, term_taxonomy_id, term_order) VALUES " . join(',', $values) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)")) {
                $logger and call_user_func($logger, __('<b>ERROR</b> Could not insert term relationship into the database', 'wp_all_import_plugin') . ': ' . $wpdb->last_error);
            }
        }

        wp_cache_delete($pid, $tx_name . '_relationships');
    }

    /**
     * @param $img_url
     * @param $pid
     * @param $logger
     * @param bool $search_in_gallery
     * @return bool|int|\WP_Error
     */
    public static function import_image($img_url, $pid, $logger, $search_in_gallery = FALSE) {
        // Search image attachment by ID.
        if ($search_in_gallery and is_numeric($img_url)) {
            if (wp_get_attachment_url($img_url)) {
                return $img_url;
            }
        }
        return PMXI_API::upload_image($pid, $img_url, "yes", $logger, true);
    }

    /**
     * @param $atch_url
     * @param $pid
     * @param $logger
     * @param bool $fast
     * @param bool $search_in_gallery
     * @return bool|int|\WP_Error
     */
    public static function import_file($atch_url, $pid, $logger, $fast = FALSE, $search_in_gallery = FALSE) {
        // Search file attachment by ID.
        if ($search_in_gallery and is_numeric($atch_url)) {
            if (wp_get_attachment_url($atch_url)) {
                return $atch_url;
            }
        }
        return PMXI_API::upload_image($pid, $atch_url, "yes", $logger, true, "", "files");
    }

    /**
     * @param $values
     * @param array $post_types
     * @return array
     */
    public static function get_posts_by_relationship($values, $post_types = []) {
        global $wpdb;
        $post_ids = [];
        foreach ($values as $ev) {
            $relation = false;
            if (ctype_digit($ev)) {
                $relation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE ID = %s", $ev));
            }
            if (empty($relation)) {
                if (empty($post_types)) {
                    $sql = "SELECT * FROM {$wpdb->posts} WHERE post_type != %s AND ( post_title = %s OR post_name = %s )";
                    $relation = $wpdb->get_row($wpdb->prepare($sql, 'revision', $ev, sanitize_title_for_query($ev)));
                } else {
                    $sql = "SELECT * FROM {$wpdb->posts} WHERE post_type IN ('%s') AND ( post_title = %s OR post_name = %s )";
                    $relation = $wpdb->get_row($wpdb->prepare($sql, implode("','", $post_types), $ev, sanitize_title_for_query($ev)));
                }
            }
            if ($relation) {
                $post_ids[] = (string)$relation->ID;
            }
        }
        return $post_ids;
    }

    /**
     * @param $options
     * @return array
     */
    public static function getWpcs($options) {
        switch ($options['custom_type']) {
            case 'import_users':
            case 'shop_customer':
                $customType = 'wp-types-user-group';
                $metaQuery = [];
                break;
            case 'taxonomies':
                $customType = 'wp-types-term-group';
                $metaQuery = [
                    'relation' => 'OR',
                    [
                        'key' => '_wp_types_associated_taxonomy',
                        'value' => $options['taxonomy_type'],
                        'compare' => 'LIKE'
                    ],
                    [
                        'key' => '_wp_types_associated_taxonomy',
                        'compare' => 'NOT EXISTS'
                    ]
                ];
                break;
            default:
                $customType = 'wp-types-group';
                $metaQuery = [
                    'relation' => 'AND',
                    [
                        'relation' => 'OR',
                        [
                            'key' => '_wp_types_group_post_types',
                            'value' => ',' . $options['custom_type'] . ',',
                            'compare' => 'LIKE'
                        ],
                        [
                            'key' => '_wp_types_group_post_types',
                            'value' => ',,',
                            'compare' => '='
                        ],
                        [
                            'key' => '_wp_types_group_post_types',
                            'value' => 'all',
                            'compare' => '='
                        ]
                    ]
                ];
                break;
        }

        $getPostsCondition = [
            'posts_per_page' => -1,
            'post_type' => $customType,
            'meta_query' => $metaQuery
        ];

        $wpcsPosts = get_posts($getPostsCondition);

        $wpcs = [];

        foreach ($wpcsPosts as $key => $field) {
            $wpcs[] = [
                'id' => $field->ID,
                'slug' => $field->post_name,
                'name' => $field->post_title
            ];
        }

        return $wpcs;
    }

    /**
     * Get all Toolset Relationships.
     *
     * @param $options
     * @return array|object|null
     */
    public static function getAllRelationships($options) {

        global $wpdb;

        $sql = "SELECT 
            r.id, 
            r.display_name_plural, 
            r.display_name_singular, 
            r.cardinality_child_max, 
            r.cardinality_parent_max, 
            r.slug,
            p.type AS parent_type,
            c.type AS child_type
         FROM {$wpdb->prefix}toolset_relationships as r 
             LEFT JOIN {$wpdb->prefix}toolset_type_sets AS p ON r.parent_types = p.set_id
             LEFT JOIN {$wpdb->prefix}toolset_type_sets AS c ON r.child_types = c.set_id
         WHERE 
             r.origin NOT IN ('post_reference_field', 'repeatable_group') AND (p.type = '{$options['custom_type']}' OR c.type = '{$options['custom_type']}')";

        return $wpdb->get_results($sql);
    }

    /**
     * Get all toolset groups/
     *
     * @return array
     */
    public static function getAllWpcs() {
        $userFields = self::getWpcs(array('custom_type' => 'import_users'));
        $taxonomyFields = self::getWpcs(array('custom_type' => 'taxonomies'));
        $postFields = self::getWpcs(array('custom_type' => 'custom_posts'));

        $allGroups = array_merge($userFields, $taxonomyFields, $postFields);

        return $allGroups;
    }
}