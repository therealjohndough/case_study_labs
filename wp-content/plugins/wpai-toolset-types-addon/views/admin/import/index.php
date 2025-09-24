<?php use wpai_toolset_types_add_on\relationships\Relationship; ?>
<div class="wpallimport-collapsed closed pmti_options">
    <div class="wpallimport-content-section">
        <div class="wpallimport-collapsed-header">
            <h3><?php _e('Toolset Types Add-On', PMTI_Plugin::TEXT_DOMAIN); ?></h3>
        </div>
        <div class="wpallimport-collapsed-content" style="padding: 0;">
            <div class="wpallimport-collapsed-content-inner">
                <table class="form-table" style="max-width:none;">
                    <tr>
                        <td colspan="3">
                            <?php
                            $wpcs = \wpai_toolset_types_add_on\ToolsetService::getWpcs($post);
                            if (!empty($wpcs)):
                                ?>
                                <p>
                                    <strong><?php _e("Please choose your Field Groups.", PMTI_Plugin::TEXT_DOMAIN); ?></strong>
                                </p>
                                <ul>
                                    <?php
                                    foreach ($wpcs as $key => $toolsetObj) {
                                        $is_show_wpcs_group = apply_filters('wp_all_import_wpcs_is_show_group', true, $toolsetObj); ?>
                                        <li>
                                            <?php if ($is_show_wpcs_group): ?>
                                                <input id="wpcs_<?php echo $post_type . '_' . $toolsetObj['id']; ?>"
                                                       type="checkbox"
                                                       name="wpcs_groups[<?php echo $toolsetObj['id']; ?>]"
                                                       <?php if (!empty($post['wpcs_groups'][$toolsetObj['id']])): ?>checked="checked"<?php endif; ?>
                                                       value="1" rel="<?php echo $toolsetObj['id']; ?>"
                                                       class="pmti_toolset_group"/>
                                                <label for="wpcs_<?php echo $post_type . '_' . $toolsetObj['id']; ?>"><?php echo $toolsetObj['name']; ?></label>
                                            <?php endif; ?>
                                        </li>
                                        <?php
                                    }
                                    PMXI_Plugin::$session->set('wpcs_groups', $wpcs);
                                    PMXI_Plugin::$session->save_data();
                                    ?>
                                </ul>
                                <div class="wpcs_groups"></div>
                                <?php
                            else:
                                ?><p><strong><?php _e("Please create Field Groups.", PMTI_Plugin::TEXT_DOMAIN); ?></strong></p><?php
                            endif;

                            $relationships = \wpai_toolset_types_add_on\ToolsetService::getAllRelationships($post);

                            if (!empty($relationships)):
                                ?>
                                <div class="postbox default rad4">
                                    <h3 class="hndle" style="margin-top:0;">
                                        <span>Relationships</span>
                                    </h3>
                                <?php
                                foreach ($relationships as $key => $relationship):

                                    if ($relationship->cardinality_child_max == 1 && $relationship->cardinality_parent_max == 1) {
                                        $relationShipType = \wpai_toolset_types_add_on\relationships\Relationship::TYPE_ONE_TO_ONE;
                                    } else if ($relationship->cardinality_parent_max == 1 && $relationship->cardinality_child_max != 1) {
                                        $relationShipType = \wpai_toolset_types_add_on\relationships\Relationship::TYPE_ONE_TO_MANY;
                                    } else {
                                        $relationShipType = \wpai_toolset_types_add_on\relationships\Relationship::TYPE_MANY_TO_MANY;
                                    }
                                    ?>
                                    <div class="inside">
                                        <div class="field field_type-relationship">
                                            <p class="label">
                                                <?php if ($relationShipType == \wpai_toolset_types_add_on\relationships\Relationship::TYPE_ONE_TO_ONE) { ?>
                                                    <label><?php echo $relationship->display_name_singular; ?></label>
                                                <?php } else { ?>
                                                    <label><?php echo $relationship->display_name_plural; ?></label>
                                                <?php } ?>
                                            </p>
                                            <div class="wpallimport-clear"></div>
                                            <p class="label" style="display:block; margin:0;"><label></label></p>
                                            <div class="wpcs-input-wrap">
                                                <div class="input">
                                                    <?php
                                                    switch ($relationShipType) {
                                                        case Relationship::TYPE_ONE_TO_ONE:
                                                            ?>
                                                            <input type="text" placeholder=""
                                                                   name="wpcs_relationships[<?php echo $relationship->id ?>][value]"
                                                                   value="<?php if (isset($post['wpcs_relationships'][$relationship->id]['value'])) {
                                                                       echo esc_html($post['wpcs_relationships'][$relationship->id]['value']);
                                                                   } ?>"
                                                                   class="text widefat rad4" style="width: 75%;"
                                                            />
                                                            <input type="hidden"
                                                                   name="wpcs_relationships[<?php echo $relationship->id; ?>][type]"
                                                                   value="<?php echo Relationship::TYPE_ONE_TO_ONE; ?>"
                                                            />
                                                            <input type="hidden"
                                                                   name="wpcs_relationships[<?php echo $relationship->id; ?>][import_type]"
                                                                   value="<?php echo ($post['custom_type'] == $relationship->parent_type) ? Relationship::IMPORTING_PARENT : Relationship::IMPORTING_CHILD; ?>"
                                                            />
                                                            <a href="#help" class="wpallimport-help"
                                                               title="<?php _e('Enter ID, slug, or Title.', PMTI_Plugin::TEXT_DOMAIN); ?>"
                                                               style="top:0;">?</a>
                                                            <?php
                                                            break;
                                                        case Relationship::TYPE_ONE_TO_MANY:
                                                            ?>
                                                            Enter one slug, ID, or title per line or separate with a
                                                            <input type="text" style="width:5%; text-align:center; margin-left: 5px;" name="wpcs_relationships[<?php echo $relationship->id; ?>][delim]" class="small rad4"
                                                                value="<?php if (isset($post['wpcs_relationships'][$relationship->id]['delim'])) {
                                                                    echo $post['wpcs_relationships'][$relationship->id]['delim'];
                                                                } ?>"
                                                            />
                                                            <textarea style="margin-top: 10px;" class="text widefat rad4"
                                                                      name="wpcs_relationships[<?php echo $relationship->id; ?>][value]"><?php if (isset($post['wpcs_relationships'][$relationship->id]['value'])) echo esc_html($post['wpcs_relationships'][$relationship->id]['value']);?></textarea>
                                                            <input type="hidden"
                                                                   name="wpcs_relationships[<?php echo $relationship->id ?>][type]"
                                                                   value="<?php echo Relationship::TYPE_ONE_TO_MANY; ?>"
                                                            />
                                                            <input type="hidden"
                                                                   name="wpcs_relationships[<?php echo $relationship->id; ?>][import_type]"
                                                                   value="<?php echo ($post['custom_type'] == $relationship->parent_type) ? Relationship::IMPORTING_PARENT : Relationship::IMPORTING_CHILD; ?>"
                                                            />
                                                            <?php
                                                            break;
                                                        default:
                                                            ?>
                                                            Enter one slug, ID, or title per line or separate with a
                                                            <input type="text" style="width:5%; text-align:center; margin-left: 5px;" name="wpcs_relationships[<?php echo $relationship->id; ?>][delim]" class="small rad4"
                                                                value="<?php if (isset($post['wpcs_relationships'][$relationship->id]['delim'])) {
                                                                    echo $post['wpcs_relationships'][$relationship->id]['delim'];
                                                                } ?>"
                                                            />
                                                            <textarea style="margin-top: 10px;" class="text widefat rad4"
                                                                      name="wpcs_relationships[<?php echo $relationship->id; ?>][value]"><?php if (isset($post['wpcs_relationships'][$relationship->id]['value'])) echo esc_html($post['wpcs_relationships'][$relationship->id]['value']); ?></textarea>
                                                            <input type="hidden"
                                                                   name="wpcs_relationships[<?php echo $relationship->id ?>][type]"
                                                                   value="<?php echo \wpai_toolset_types_add_on\relationships\Relationship::TYPE_MANY_TO_MANY; ?>"
                                                            />
                                                            <input type="hidden"
                                                                   name="wpcs_relationships[<?php echo $relationship->id; ?>][import_type]"
                                                                   value="<?php echo ($post['custom_type'] == $relationship->parent_type) ? Relationship::IMPORTING_PARENT : Relationship::IMPORTING_CHILD; ?>"
                                                            />
                                                            <?php
                                                            break;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                endforeach;
                                ?>
                                </div>
                                <?php
                            endif;
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>