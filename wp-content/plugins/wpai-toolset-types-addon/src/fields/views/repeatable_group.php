<div class="wpcs-repeater">
    <div class="input" style="margin-bottom: 10px;">
        <div class="input">
            <input type="radio" id="is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_no" class="switcher wpcs-variable_repeater_mode" name="wpcs_fields<?php echo $field_name; ?>[<?php echo $field['key'];?>][is_variable]" value="no" <?php echo 'yes' != $current_field['is_variable'] ? 'checked="checked"': '' ?>/>
            <label for="is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_no" class="chooser_label"><?php _e('Fixed Repeater Mode', PMTI_Plugin::TEXT_DOMAIN )?></label>
        </div>
        <div class="input">
            <input type="radio" id="is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_yes" class="switcher wpcs-variable_repeater_mode" name="wpcs_fields<?php echo $field_name; ?>[<?php echo $field['key'];?>][is_variable]" value="yes" <?php echo 'yes' == $current_field['is_variable'] ? 'checked="checked"': '' ?>/>
            <label for="is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_yes" class="chooser_label"><?php _e('Variable Repeater Mode (XML)', PMTI_Plugin::TEXT_DOMAIN )?></label>
        </div>
        <div class="input">
            <input type="radio" id="is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_yes_csv" class="switcher wpcs-variable_repeater_mode" name="wpcs_fields<?php echo $field_name; ?>[<?php echo $field['key'];?>][is_variable]" value="csv" <?php echo 'csv' == $current_field['is_variable'] ? 'checked="checked"': '' ?>/>
            <label for="is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_yes_csv" class="chooser_label"><?php _e('Variable Repeater Mode (CSV)', PMTI_Plugin::TEXT_DOMAIN )?></label>
        </div>
        <div class="input sub_input">
            <input type="hidden" name="wpcs_fields<?php echo $field_name; ?>[<?php echo $field['key'];?>][is_ignore_empties]" value="0"/>
            <input type="checkbox" value="1" id="is_ignore_empties<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>" name="wpcs_fields<?php echo $field_name; ?>[<?php echo $field['key'];?>][is_ignore_empties]" <?php if ( ! empty($current_field['is_ignore_empties'])) echo 'checked="checked';?>/>
            <label for="is_ignore_empties<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>"><?php _e('Ignore blank fields', PMTI_Plugin::TEXT_DOMAIN); ?></label>
            <a href="#help" class="wpallimport-help" style="top:0;" title="<?php _e('If the value of the element or column in your file is blank, it will be ignored. Use this option when some records in your file have a different number of repeating elements than others.', PMTI_Plugin::TEXT_DOMAIN) ?>">?</a>
        </div>
        <div class="wpallimport-clear"></div>
        <div class="switcher-target-is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_yes">
            <div class="input sub_input">
                <div class="input">
                    <p>
                        <?php printf(__("For each %s do ..."), '<input type="text" name="wpcs_fields' . $field_name . '[' . $field["key"] . '][foreach]" value="'. esc_html($current_field["foreach"]) .'" class="wpcs_foreach widefat rad4"/>'); ?>
                        <a href="http://www.wpallimport.com/documentation/advanced-custom-fields/repeater-fields/" target="_blank"><?php _e('(documentation)', PMTI_Plugin::TEXT_DOMAIN); ?></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="switcher-target-is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_yes_csv">
            <div class="input sub_input">
                <div class="input">
                    <p>
                        <?php printf(__("Separator Character %s"), '<input type="text" name="wpcs_fields' . $field_name . '[' . $field["key"] . '][separator]" value="'. ( (empty($current_field["separator"])) ? '|' : $current_field["separator"] ) .'" class="wpcs_variable_separator widefat rad4"/>'); ?>
                        <a href="#help" class="wpallimport-help" style="top:0;" title="<?php _e('Use this option when importing a CSV file with a column or columns that contains the repeating data, separated by separators. For example, if you had a repeater with two fields - image URL and caption, and your CSV file had two columns, image URL and caption, with values like \'url1,url2,url3\' and \'caption1,caption2,caption3\', use this option and specify a comma as the separator.', PMTI_Plugin::TEXT_DOMAIN) ?>">?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <table class="widefat wpcs-input-table row_layout">
        <tbody>
        <?php
        if (!empty($current_field['rows'])): foreach ($current_field['rows'] as $key => $row): if ("ROWNUMBER" == $key) continue; ?>
            <tr class="row">
                <td class="order" style="padding:8px;"><?php echo $key; ?></td>
                <td class="wpcs_input-wrap">
                    <table class="widefat toolset_input" style="border:none;">
                        <tbody>
                        <?php if (!empty($fields)): ?>
                            <?php foreach ($fields as $subField): ?>
                                <tr class="field sub_field field_type-<?php echo $subField->getType();?> field_key-<?php echo $subField->getFieldKey();?>">
                                    <td>
                                        <div class="inner input">
                                            <?php
                                            $subField->setFieldInputName($field_name . "[" . $field['key'] . "][rows][" . $key . "]");
                                            $subField->view();
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        <tr class="row-clone">
            <td class="order" style="padding:8px;"></td>
            <td class="wpcs_input-wrap">
                <table class="widefat toolset_input" style="border:none;">
                    <tbody>
                    <?php if (!empty($fields)): ?>
                        <?php foreach ($fields as $subField): ?>
                            <tr class="field sub_field field_type-<?php echo $subField->getType();?> field_key-<?php echo $subField->getFieldKey();?>">
                                <td>
                                    <div class="inner input">
                                        <?php
                                        $subField->setFieldInputName($field_name . "[" . $field['key'] . "][rows][ROWNUMBER]");
                                        $subField->view();
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>


    <div class="wpallimport-clear"></div>
    <div class="switcher-target-is_variable_<?php echo str_replace(array('[',']'), '', $field_name);?>_<?php echo $field['key'];?>_no">
        <div class="input sub_input">
            <ul class="hl clearfix repeater-footer">
                <li class="right">
                    <a href="javascript:void(0);" class="wpcs-button delete_row" style="margin-left:15px;"><?php _e('Delete Row', PMTI_Plugin::TEXT_DOMAIN); ?></a>
                </li>
                <li class="right">
                    <a class="add-row-end wpcs-button" href="javascript:void(0);"><?php _e("Add Row", PMTI_Plugin::TEXT_DOMAIN);?></a>
                </li>
            </ul>
        </div>
    </div>
</div>