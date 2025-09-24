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
        <?php _e('Search through the Media Library for existing files before importing new files', PMTI_Plugin::TEXT_DOMAIN); ?></label>

    <a
        href="#help"
        class="wpallimport-help"
        title="<?php _e('If a file with the same file name is found in the Media Library then that image will be attached to this record instead of importing a new file. Disable this setting if your import has different files with the same file name.', PMTI_Plugin::TEXT_DOMAIN) ?>"
        style="position: relative; top: -2px;">?</a>

</div>