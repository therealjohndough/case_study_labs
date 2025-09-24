<div class="input">
    <div class="main_choise">
        <input type="radio"
               id="is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_yes"
               class="switcher"
               name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][is_multiple_value]"
               value="1" <?php echo empty($current_field['is_multiple_value']) ? '' : 'checked="checked"'; ?>/>
        <label
            for="is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_yes"
            class="chooser_label"><?php _e("Select value for all records", PMTI_Plugin::TEXT_DOMAIN); ?></label>
    </div>
    <div class="wpallimport-clear"></div>

    <div
        class="switcher-target-is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_yes">
        <div class="input sub_input">
            <?php foreach ($field['data']['options'] as $key => $option):?>
                <div class="input">
                    <input type="hidden"
                           name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][multiple_value][<?php echo $key; ?>]"
                           value="0"
                    />
                    <input type="checkbox"
                           id="<?php echo $this->getFieldKey() . '_' . $key; ?>"
                           name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][multiple_value][<?php echo $key; ?>]"
                           value="<?php echo $option['set_value']; ?>"
                        <?php if (!empty($current_field['multiple_value']) && $option['set_value'] == $current_field['multiple_value'][$key]) echo 'checked="checked"'; ?>
                    />
                    <label for="<?php echo $this->getFieldKey() . '_' . $key; ?>"><?php echo $option['title'];?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<div class="clear"></div>
<div class="input">
    <div class="main_choice">
        <input type="radio"
               id="is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_no"
               class="switcher"
               name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][is_multiple_value]"
               value="0" <?php echo empty($current_field['is_multiple_value']) ? 'checked="checked"' : ''; ?>/>
        <label
            for="is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_no"
            class="chooser_label"><?php _e('Set with XPath', PMTI_Plugin::TEXT_DOMAIN) ?></label>
    </div>
    <div class="wpallimport-clear"></div>
    <div
        class="switcher-target-is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_no set-with-xpath">
        <div class="input sub_input">
            <div class="input">
                <input type="text" class="text widefat rad4 repetitive"
                       name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][value]"
                       value="<?php echo $this->getPostValue(); ?>"/>
                <input type="text" class="small rad4 field_delimiter" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][delimiter]" value="<?php echo $this->getDelimiter(); ?>"/>
            </div>
        </div>
    </div>
</div>