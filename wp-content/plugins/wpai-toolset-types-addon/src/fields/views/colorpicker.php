<input
    type="text"
    placeholder=""
    value="<?php echo esc_attr( $current_field['value'] );?>"
    name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value]"
    class="text widefat rad4" style="width: 90%;"/>

<a
    href="#help"
    class="wpallimport-help"
    title="<?php _e('Specify the hex code the color preceded with a # - e.g. #ea5f1a.', PMTI_Plugin::TEXT_DOMAIN); ?>"
    style="top:0;">?</a>

<?php if ($this->isRepetitive()): ?>
    <input type="text" class="small rad4 field_delimiter" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][delimiter]" value="<?php echo $this->getDelimiter(); ?>"/>
<?php endif; ?>
