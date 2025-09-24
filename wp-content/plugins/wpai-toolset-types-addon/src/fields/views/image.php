<input
    type="text"
    placeholder=""
    value="<?php echo $this->getPostValue();?>"
    name="wpcs_fields<?php echo $field_name;?>[<?php echo $this->getFieldKey();?>][value]"
    class="text widefat rad4 <?php if ($this->isRepetitive()): ?>repetitive<?php endif; ?>"/>

<?php if ($this->isRepetitive()): ?>
    <input type="text" class="small rad4 field_delimiter" name="wpcs_fields<?php echo $field_name;?>[<?php echo $this->getFieldKey();?>][delimiter]" value="<?php echo $this->getDelimiter(); ?>"/>
<?php endif; ?>

<input
    type="hidden"
    name="wpcs_fields<?php echo $field_name;?>[<?php echo $this->getFieldKey();?>][search_in_media]"
    value="0"/>

<div class="input">

    <input
        type="checkbox"
        id="<?php echo $field_name . $this->getFieldKey() . '_search_in_media';?>"
        name="wpcs_fields<?php echo $field_name;?>[<?php echo $this->getFieldKey();?>][search_in_media]"
        value="1" <?php echo $this->isSearchInMedia() ? 'checked="checked"' : '';?>/>

    <label
        for="<?php echo $field_name . $this->getFieldKey() . '_search_in_media';?>">
        <?php _e('Search through the Media Library for existing images before importing new images', PMTI_Plugin::TEXT_DOMAIN); ?></label>

    <a
        href="#help"
        class="wpallimport-help"
        title="<?php _e('If an image with the same file name is found in the Media Library then that image will be attached to this record instead of importing a new image. Disable this setting if your import has different images with the same file name.', PMTI_Plugin::TEXT_DOMAIN) ?>"
        style="position: relative; top: -2px;">?</a>

</div>

<?php if ($this->isRepetitive()): ?>
    <div class="input">
        <input
            type="checkbox"
            id="<?php echo $field_name . $this->getFieldKey() . '_only_append_new';?>"
            name="wpcs_fields<?php echo $field_name;?>[<?php echo $this->getFieldKey();?>][only_append_new]"
            value="1" <?php echo $this->isAppendMedia() ? 'checked="checked"' : '';?>/>

            <label
                for="<?php echo $field_name . $this->getFieldKey() . '_only_append_new';?>">
                <?php _e('Append only new images and do not touch existing while updating image field.', PMTI_Plugin::TEXT_DOMAIN); ?></label>
    </div>
<?php endif; ?>