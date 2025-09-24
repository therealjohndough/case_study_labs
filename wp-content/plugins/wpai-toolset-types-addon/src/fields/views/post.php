<input type="text"
       placeholder=""
       value="<?php echo $this->getPostValue();?>"
       name="wpcs_fields<?php echo $field_name;?>[<?php echo $this->getFieldKey();?>][value]"
       class="text widefat rad4 <?php if ($this->isRepetitive()): ?>repetitive<?php endif; ?>" style="width: 90%;"/>

<a href="#help"
   class="wpallimport-help"
   title="<?php _e('Enter the ID, slug, or Title.', PMTI_Plugin::TEXT_DOMAIN); ?>"
   style="top:0;">?</a>