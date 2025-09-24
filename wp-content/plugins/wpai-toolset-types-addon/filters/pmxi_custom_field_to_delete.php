<?php
/**
 * @param $field_to_delete
 * @param $pid
 * @param $post_type
 * @param $options
 * @param $cur_meta_key
 * @return mixed|void
 */
function pmti_pmxi_custom_field_to_delete($field_to_delete, $pid, $post_type, $options, $cur_meta_key ){

    if (strpos($cur_meta_key,'wpcf-') !== 0) {
        return $field_to_delete;
    }

    return pmti_is_wpcs_update_allowed($cur_meta_key, $options);
}