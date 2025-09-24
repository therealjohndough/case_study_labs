<textarea
    name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value]"
    class="widefat rad4 <?php if ($this->isRepetitive()): ?>repetitive<?php endif; ?>"><?php echo esc_attr( $current_field['value'] );?></textarea>

<?php if ($this->isRepetitive()): ?>
    <input type="text" class="small rad4 field_delimiter" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][delimiter]" value="<?php echo $this->getDelimiter(); ?>"/>
<?php endif; ?>