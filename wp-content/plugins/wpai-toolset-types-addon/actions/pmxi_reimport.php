<?php
/**
 * @param $entry
 * @param $post
 */
function pmti_pmxi_reimport($entry, $post) {

    $wpcs = \wpai_toolset_types_add_on\ToolsetService::getWpcs($post);

    switch ($post['custom_type']) {
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
    // Collect all toolset fields.
    $all_existing_wpcs = [];
    foreach ($wpcs as $fieldGroup) {
        $fields = \wpcf_admin_fields_get_fields_by_group($fieldGroup['id'], 'slug', false, false, false,
            TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $optionName, true);

        foreach ($fields as $field) {
            if (!empty($field['meta_key'])) {
                $all_existing_wpcs[] = "[" . $field['meta_key'] . "] " . $field['name'];
            } elseif (!is_array($field) && strpos($field, '_repeatable_group') === 0) {
                pmti_get_field_from_repeatable_group($field, $optionName, $all_existing_wpcs);
            }
        }
    }

    // Collect all Toolset relationships.
    $relationships_data = [];
    $relationships = \wpai_toolset_types_add_on\ToolsetService::getAllRelationships($post);
    if (!empty($relationships)) {
        foreach ($relationships as $relationship) {
            $relationships_data[] = "[" . $relationship->slug . "] " . $relationship->display_name_singular;
        }
    }
    ?>
    <div class="input">
        <input type="hidden" name="wpcs_list" value="0"/>
        <input type="hidden" name="is_update_wpcs" value="0"/>
        <input type="checkbox" id="is_update_wpcs_<?php echo $entry; ?>" name="is_update_wpcs"
               value="1" <?php echo $post['is_update_wpcs'] ? 'checked="checked"' : '' ?> class="switcher"/>
        <label for="is_update_wpcs_<?php echo $entry; ?>"><?php _e('Toolset Types Custom Fields', PMTI_Plugin::TEXT_DOMAIN) ?></label>
        <div class="switcher-target-is_update_wpcs_<?php echo $entry; ?>" style="padding-left:17px;">
            <div class="input">
                <input type="radio" id="wpcs_update_logic_full_update_<?php echo $entry; ?>" name="wpcs_update_logic"
                       value="full_update" <?php echo ("full_update" == $post['wpcs_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_update_logic_full_update_<?php echo $entry; ?>"><?php _e('Update all Toolset Types fields', PMTI_Plugin::TEXT_DOMAIN) ?></label>
            </div>
            <div class="input">
                <input type="radio" id="wpcs_update_logic_mapped_<?php echo $entry; ?>" name="wpcs_update_logic"
                       value="mapped" <?php echo ("mapped" == $post['wpcs_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_update_logic_mapped_<?php echo $entry; ?>"><?php _e('Update only mapped Toolset Types groups', PMTI_Plugin::TEXT_DOMAIN) ?></label>
            </div>
            <div class="input">
                <input type="radio" id="wpcs_update_logic_only_<?php echo $entry; ?>" name="wpcs_update_logic"
                       value="only" <?php echo ("only" == $post['wpcs_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_update_logic_only_<?php echo $entry; ?>"><?php _e('Update only these Toolset Types fields, leave the rest alone', PMTI_Plugin::TEXT_DOMAIN) ?></label>
                <div class="switcher-target-wpcs_update_logic_only_<?php echo $entry; ?> pmxi_choosen"
                     style="padding-left:17px;">

                    <span class="hidden choosen_values"><?php if (!empty($all_existing_wpcs)) echo implode(',', $all_existing_wpcs); ?></span>
                    <input class="choosen_input"
                           value="<?php if (!empty($post['wpcs_list']) and "only" == $post['wpcs_update_logic']) echo implode(',', $post['wpcs_list']); ?>"
                           type="hidden" name="wpcs_only_list"/>
                </div>
            </div>
            <div class="input">
                <input type="radio" id="wpcs_update_logic_all_except_<?php echo $entry; ?>" name="wpcs_update_logic"
                       value="all_except" <?php echo ("all_except" == $post['wpcs_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_update_logic_all_except_<?php echo $entry; ?>"><?php _e('Leave these Toolset Types fields alone, update all other Toolset Types fields', PMTI_Plugin::TEXT_DOMAIN) ?></label>
                <div class="switcher-target-wpcs_update_logic_all_except_<?php echo $entry; ?> pmxi_choosen"
                     style="padding-left:17px;">
                    <span class="hidden choosen_values"><?php if (!empty($all_existing_wpcs)) echo implode(',', $all_existing_wpcs); ?></span>
                    <input class="choosen_input"
                           value="<?php if (!empty($post['wpcs_list']) and "all_except" == $post['wpcs_update_logic']) echo implode(',', $post['wpcs_list']); ?>"
                           type="hidden" name="wpcs_except_list"/>
                </div>
            </div>
        </div>
    </div>
    <div class="input">
        <input type="hidden" name="wpcs_relationships_list" value="0"/>
        <input type="hidden" name="is_update_wpcs_relationships" value="0"/>
        <input type="checkbox" id="is_update_wpcs_relationships_<?php echo $entry; ?>" name="is_update_wpcs_relationships"
               value="1" <?php echo $post['is_update_wpcs_relationships'] ? 'checked="checked"' : '' ?> class="switcher"/>
        <label for="is_update_wpcs_relationships_<?php echo $entry; ?>"><?php _e('Toolset Types Relationships', PMTI_Plugin::TEXT_DOMAIN) ?></label>
        <div class="switcher-target-is_update_wpcs_relationships_<?php echo $entry; ?>" style="padding-left:17px;">
            <div class="input">
                <input type="radio" id="wpcs_relationships_update_logic_full_update_<?php echo $entry; ?>" name="wpcs_relationships_update_logic"
                       value="full_update" <?php echo ("full_update" == $post['wpcs_relationships_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_relationships_update_logic_full_update_<?php echo $entry; ?>"><?php _e('Update all Toolset Types relationships', PMTI_Plugin::TEXT_DOMAIN) ?></label>
            </div>
            <div class="input">
                <input type="radio" id="wpcs_relationships_update_logic_only_<?php echo $entry; ?>" name="wpcs_relationships_update_logic"
                       value="only" <?php echo ("only" == $post['wpcs_relationships_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_relationships_update_logic_only_<?php echo $entry; ?>"><?php _e('Update only these Toolset Types relationships, leave the rest alone', PMTI_Plugin::TEXT_DOMAIN) ?></label>
                <div class="switcher-target-wpcs_relationships_update_logic_only_<?php echo $entry; ?> pmxi_choosen"
                     style="padding-left:17px;">
                    <span class="hidden choosen_values"><?php if (!empty($relationships_data)) echo implode(',', $relationships_data); ?></span>
                    <input class="choosen_input"
                           value="<?php if (!empty($post['wpcs_relationships_list']) and "only" == $post['wpcs_relationships_update_logic']) echo implode(',', $post['wpcs_relationships_list']); ?>"
                           type="hidden" name="wpcs_relationships_only_list"/>
                </div>
            </div>
            <div class="input">
                <input type="radio" id="wpcs_relationships_update_logic_all_except_<?php echo $entry; ?>" name="wpcs_relationships_update_logic"
                       value="all_except" <?php echo ("all_except" == $post['wpcs_relationships_update_logic']) ? 'checked="checked"' : '' ?>
                       class="switcher"/>
                <label for="wpcs_relationships_update_logic_all_except_<?php echo $entry; ?>"><?php _e('Leave these Toolset Types relationships alone, update all other Toolset Types relationships', PMTI_Plugin::TEXT_DOMAIN) ?></label>
                <div class="switcher-target-wpcs_relationships_update_logic_all_except_<?php echo $entry; ?> pmxi_choosen"
                     style="padding-left:17px;">
                    <span class="hidden choosen_values"><?php if (!empty($relationships_data)) echo implode(',', $relationships_data); ?></span>
                    <input class="choosen_input"
                           value="<?php if (!empty($post['wpcs_relationships_list']) and "all_except" == $post['wpcs_relationships_update_logic']) echo implode(',', $post['wpcs_relationships_list']); ?>"
                           type="hidden" name="wpcs_relationships_except_list"/>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function pmti_get_field_from_repeatable_group($field, $optionName, &$fields) {
    $repeatable_group_id = preg_replace("/[^0-9]/", "", $field );
    $repeatable_group = get_post($repeatable_group_id);
    $fields[] = "[" . $repeatable_group->post_name . "] " . $repeatable_group->post_title;
    $repeatable_fields = \wpcf_admin_fields_get_fields_by_group($repeatable_group_id, 'slug', false, false, false,
        TYPES_CUSTOM_FIELD_GROUP_CPT_NAME, $optionName, true);
    if (!empty($repeatable_fields)) {
        foreach ($repeatable_fields as $repeatable_field) {
            if (!empty($repeatable_field['meta_key'])) {
                $fields[] = "[" . $repeatable_field['meta_key'] . "] " . $repeatable_group->post_title . " &#8594; " . $repeatable_field['name'];
            } elseif (!is_array($repeatable_field) && strpos($repeatable_field, '_repeatable_group') === 0) {
                pmti_get_field_from_repeatable_group($repeatable_field, $optionName, $fields);
            }
        }
    }
}
?>