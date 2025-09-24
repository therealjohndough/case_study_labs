<?php
/**
 * @param $field_to_update
 * @param $post_type
 * @param $options
 * @param $m_key
 * @return mixed|void
 */
function pmti_pmxi_custom_field_to_update($field_to_update, $post_type, $options, $m_key ){

    if (strpos($m_key,'wpcf-') !== 0) {
        return $field_to_update;
    }

    return pmti_is_wpcs_update_allowed($m_key, $options);
}