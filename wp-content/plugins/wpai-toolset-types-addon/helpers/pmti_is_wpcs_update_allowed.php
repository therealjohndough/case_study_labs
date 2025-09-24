<?php

/**
 * @param $cur_meta_key
 * @param $options
 * @return mixed
 */
function pmti_is_wpcs_update_allowed($cur_meta_key, $options ) {

    if ($options['is_keep_former_posts'] == 'yes') return pmti_return_false($cur_meta_key, $options);

    if ($options['update_all_data'] == 'yes') return pmti_return_true($cur_meta_key, $options);

    if (!$options['is_update_wpcs']) return pmti_return_false($cur_meta_key, $options);

    if ($options['is_update_wpcs'] && $options['wpcs_update_logic'] == 'full_update') return pmti_return_true($cur_meta_key, $options);

    switch ($options['custom_type']) {
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

    if (pmti_should_update_by_option($options, 'mapped')) {
        $mapped_wpcs_groups = $options['wpcs_groups'];
        if ( ! empty($mapped_wpcs_groups)) {
            $all_wpcs_fields = [];
            foreach ($mapped_wpcs_groups as $wpcs_group_id => $is_mapped) {
                if ( ! $is_mapped ) continue;
                $wpcs_fields = \wpcf_admin_fields_get_fields_by_group($wpcs_group_id, 'slug', false, false, false,
                    TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $optionName, true);
                if (!empty($wpcs_fields)) {
                    foreach ($wpcs_fields as $key => $field) {
                        $all_wpcs_fields[] = $field['meta_key'];
                    }
                }
            }
            if ( in_array($cur_meta_key, $all_wpcs_fields)){
                return pmti_return_true($cur_meta_key, $options);
            } else {
                return pmti_return_false($cur_meta_key, $options);
            }
        }
    } else if (pmti_should_update_by_option($options, 'only')) {
        $list = implode( ',', $options['wpcs_list'] );
        if (strpos($list,'[' . $cur_meta_key . ']') !== false) {
            return pmti_return_true($cur_meta_key, $options);
        } else {
            return pmti_return_false($cur_meta_key, $options);
        }
    } else if (pmti_should_update_by_option($options, 'all_except')) {
        $list = implode( ',', $options['wpcs_list'] );
        if (strpos($list,'[' . $cur_meta_key . ']') !== false) {
            return pmti_return_false($cur_meta_key, $options);
        } else {
            return pmti_return_true($cur_meta_key, $options);
        }
    } else if (pmti_should_update_by_option($options, 'full_update')) {
        return pmti_return_true($cur_meta_key, $options);
    }

    return pmti_return_true($cur_meta_key, $options);
}

/**
 * @param $cur_meta_key
 * @param $options
 * @return mixed|void
 */
function pmti_return_true($cur_meta_key, $options) {
    return apply_filters('pmti_is_wpcs_update_allowed', true, $cur_meta_key, $options);
}

/**
 * @param $cur_meta_key
 * @param $options
 * @return mixed|void
 */
function pmti_return_false($cur_meta_key, $options) {
    return apply_filters('pmti_is_wpcs_update_allowed', false, $cur_meta_key, $options);
}

/**
 * @param $options
 * @param $option
 * @return bool
 */
function pmti_should_update_by_option($options, $option) {
    return $options['update_all_data'] == 'no' && $options['is_update_wpcs'] && $options['wpcs_update_logic'] == $option;
}