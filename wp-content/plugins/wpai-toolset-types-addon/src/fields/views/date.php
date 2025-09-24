<input
    type="text"
    placeholder=""
    value="<?php echo esc_attr( $current_field['value'] );?>"
    name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value]"
    class="text widefat <?php echo ($field['data']['date_and_time'] == 'and_time') ? 'datetimepicker' : 'datepicker';?> rad4" style="width: 90%;"/>

<a
    href="#help"
    class="wpallimport-help"
    title="<?php _e('Use any format supported by the PHP strtotime function.', PMTI_Plugin::TEXT_DOMAIN); ?>"
    style="top:0;">?</a>

<?php if ($this->isRepetitive()): ?>
    <input type="text" class="small rad4 field_delimiter" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][delimiter]" value="<?php echo $this->getDelimiter(); ?>"/>
<?php endif; ?>
