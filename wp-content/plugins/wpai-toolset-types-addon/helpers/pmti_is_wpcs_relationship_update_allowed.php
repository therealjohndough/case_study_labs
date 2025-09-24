<?php

/**
 * @param $slug
 * @param $options
 * @return mixed
 */
function pmti_is_wpcs_relationship_update_allowed($slug, $options) {

    if ($options['is_keep_former_posts'] == 'yes') return FALSE;

    if ($options['update_all_data'] == 'yes') return TRUE;

    if (!$options['is_update_wpcs_relationships']) return FALSE;

    if ($options['is_update_wpcs_relationships'] && $options['wpcs_relationships_update_logic'] == 'full_update') return TRUE;

    if (pmti_should_update_relationship_by_option($options, 'only')) {
        $list = implode( ',', $options['wpcs_relationships_list'] );
        return strpos($list,'[' . $slug . ']') !== false;
    } else if (pmti_should_update_relationship_by_option($options, 'all_except')) {
        $list = implode( ',', $options['wpcs_relationships_list'] );
        return strpos($list,'[' . $slug . ']') !== false ? FALSE : TRUE;
    }
    return TRUE;
}

/**
 * @param $options
 * @param $option
 * @return bool
 */
function pmti_should_update_relationship_by_option($options, $option) {
    return $options['update_all_data'] == 'no' && $options['is_update_wpcs_relationships'] && $options['wpcs_relationships_update_logic'] == $option;
}