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
            <div class="input">
                <?php
                unset($field['data']['options']['default']);
                if (!empty($field['data']['options'])):
                    foreach ($field['data']['options'] as $option) {
                        ?>
                        <div class="input">
                            <input type="radio"
                                   id="<?php echo $this->getFieldKey() . '_' . $option['value']; ?>"
                                   name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][multiple_value]"
                                   value="<?php echo $option['value']; ?>"
                                    <?php if ($option['value'] == $current_field['multiple_value']) echo 'checked="checked"'; ?>
                            />
                            <label for="<?php echo $this->getFieldKey() . '_' . $option['value']; ?>"><?php echo $option['title']; ?></label>
                        </div>
                        <?php
                    }
                endif;
                ?>
            </div>
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
        class="switcher-target-is_multiple_field_value_<?php echo sanitize_key($field_name); ?>_<?php echo $this->getFieldKey(); ?>_no">
        <div class="input sub_input">
            <div class="input">
                <input type="text" class="smaller-text widefat rad4"
                       name="wpcs_fields<?php echo $field_name; ?>[<?php echo $this->getFieldKey(); ?>][value]"
                       style="width:300px;"
                       value="<?php echo $this->getPostValue(); ?>"/>
                <a href="#help" class="wpallimport-help" style="top:0;"
                   title="<?php _e('Specify the value.', PMTI_Plugin::TEXT_DOMAIN) ?>">?</a>
            </div>
        </div>
    </div>
</div>