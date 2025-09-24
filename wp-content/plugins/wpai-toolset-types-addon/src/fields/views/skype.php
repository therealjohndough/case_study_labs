<style type="text/css">
    #wpt-skype-edit-button-popup h3 {
        clear: both;
    }

    #wpt-skype-edit-button-popup .button-secondary {
        display: block;
        clear: both;
        margin-top: 2em;
    }

    #wpt-skype-edit-button-popup .wpcf-form-item-select {
        float: left;
        margin-right: 20px;
    }

    #wpt-skype-edit-button-popup .main {
        width: 350px;
    }

    #wpt-skype-edit-button-popup-preview {
        background-color: #e5eef3;
        border: 1px solid #c1c1c1;
        padding: 10px 20px;
        position: absolute;
        top: 40px;
        right: 20px;
        width: 200px;
        z-index: 1000;
    }

    #TB_ajaxContent #wpt-skype-edit-button-popup-preview-button p {
        line-height: 36px;
        margin: 0;
        padding: 0;
    }


    #wpt-skype-edit-button-popup-preview p.bold {
        font-weight: 600;
        text-align: center;
        margin-bottom: 10px;
    }

    #wpt-skype-edit-button-popup-preview-button.dark-background {
        background-color: #b2b5b8;
    }

    #wpt-skype-edit-button-popup-preview-button.dropdown small {
        display: block;
    }

    #wpt-skype-edit-button-popup-preview-button small {
        display: none;
    }

    #wpt-skype-edit-button-popup-preview-button li {
        text-align: left;
    }

    #wpt-skype-edit-button-popup-preview-button {
        background-color: #fff;
        min-height: 120px;
        text-align: center;
        width: 200px;
    }

    #wpt-skype-edit-button-popup input {
        margin-top: 5px;
        vertical-align: top;
    }

    .field_type-skype {
        padding-top: 0;
    }

    select.js-wpt-skype {
        width: 100px !important;
        display: inline-block;
    }

    #tpl-wpt-skype-edit-button h4 {
        margin-top: 0;
        margin-bottom: 0;
    }

    #tpl-wpt-skype-edit-button .description {
        margin-top: -5px;
        margin-bottom: 10px;
    }

    #tpl-wpt-skype-edit-button .js-wpt-skype-color:first-of-type {
        margin-right: 10px;
    }

    #tpl-wpt-skype-edit-button .wpallimport-collapsed-content-inner {
        position: relative;
        height: 260px;
    }

    .wpallimport-plugin .rad4-bottom{
        margin: -1px 0;
        border-top: 1px solid #ddd;
        background: #f1f2f2;
    }

    #wpt-skype-edit-button-popup-preview .description {
        margin-top: 10px !important;
    }

    #tpl-wpt-skype-edit-button .wpallimport-collapsed-header {
        padding-left: 25px;
    }

    #tpl-wpt-skype-edit-button .wpallimport-collapsed-content-inner {
        padding: 15px 25px 25px 25px;
        margin-top: 0;
    }

    #tpl-wpt-skype-edit-button .wpt-form-item {
        margin-top: 10px;
    }

    #tpl-wpt-skype-edit-button h3 {
	    font-size: 1.2em;
    }

    #tpl-wpt-skype-edit-button label {
        vertical-align: top;
        font-size: 12px;
    }

    .wpallimport-plugin .form-table #tpl-wpt-skype-edit-button label {
        margin-bottom: 10px !important; /* Override !important from generic styles */
    }

    #tpl-wpt-skype-edit-button .description {
        font-size: 12px;
    }

    #tpl-wpt-skype-edit-button p.inner-label {
        margin: 20px 0 10px 0;
        font-weight: 600;
        color: #40acad;
    }
}

</style>

<div id="tpl-wpt-skype-edit-button">
    <div>
        <input type="hidden" id="post-hidden-1-1535462218" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value][action]" value="<?php if(!isset($current_field['value']['action'])) { echo "chat"; } else echo $current_field['value']['action']; ?>" class="js-wpt-skype-action-value wpt-form-hidden form-hidden" data-wpt-id="wpcf-skype2-0-action" data-wpt-name="wpcf[skype2][0][action]">
        <p style="margin-bottom: 5px;"><?php _e('Skype Name', PMTI_Plugin::TEXT_DOMAIN); ?></p>
        <input type="text" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value][skypename]"
               class="rad4-top js-wpt-skypename-popup js-wpt-skype wpt-form-textfield form-textfield textfield text widefat rad4 <?php if ($this->isRepetitive()): ?>repetitive<?php endif; ?>"
               data-skype-field-name="skypename" data-wpt-type="textfield" data-wpt-id="skype-name"
               data-wpt-name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value][skypename]"
               value="<?php echo esc_attr( $current_field['value']['skypename'] );?>"
        />

        <?php if ($this->isRepetitive()): ?>
            <input type="text" class="small rad4 field_delimiter" name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][delimiter]" value="<?php echo $this->getDelimiter(); ?>"/>
        <?php endif; ?>

        <div class="wpallimport-collapsed wpallimport-section closed">
            <div class="wpallimport-content-section rad4-bottom">
                <div class="wpallimport-collapsed-header">
                    <h3 style="color:#40acad;"><?php _e('Skype Button Options', PMTI_Plugin::TEXT_DOMAIN); ?></h3>
                </div>
                <div class="wpallimport-collapsed-content" style="padding: 0; display: none;">
                    <div class="wpallimport-collapsed-content-inner">
                        <hr>
                        <div id="wpt-skype-edit-button-popup-preview"><p class="bold">Preview of your Skype button</p>
                            <div id="wpt-skype-edit-button-popup-preview-button" class="dark-background">
                                <div id="wpt-skype-preview"></div>
                                <small style="display: inline;">*<?php _e('Hover over to see the menu', PMTI_Plugin::TEXT_DOMAIN); ?></small>
                            </div>
                            <p class="description"><strong><?php _e('Note', PMTI_Plugin::TEXT_DOMAIN); ?></strong>: <?php _e('Skype button background is transparent and will work on any colour backgrounds.', PMTI_Plugin::TEXT_DOMAIN); ?></p>
                        </div>
                        <p class="inner-label"><?php _e('Choose what you\'d like your button to do.', PMTI_Plugin::TEXT_DOMAIN); ?></p>

                        <div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox">
                            <input type="checkbox"
                                   data-wpt-type="checkbox"
                                   data-wpt-id="skype-action-call"
                                   data-wpt-name="skype[action][call]"
                                   value="call"
                                   class="js-wpt-skype js-wpt-skype-action js-wpt-skype-action-call wpt-form-checkbox form-checkbox checkbox"
                                   data-skype-field-name="action"
                                    <?php if($current_field['value']['action'] == 'call') {?> checked="checked" <?php }?>
                            /><label class="wpt-form-label wpt-form-checkbox-label"><?php _e('Call', PMTI_Plugin::TEXT_DOMAIN); ?></label>

                            <div class="description wpt-form-description wpt-form-description-checkbox description-checkbox">
                                <?php _e('Start a call with just a click.', PMTI_Plugin::TEXT_DOMAIN);?>
                            </div>
                        </div>

                        <div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox">
                            <input type="checkbox"
                                   data-wpt-type="checkbox"
                                   data-wpt-id="skype-action-chat"
                                   data-wpt-name="skype[action][chat]"
                                   value="chat"
                                   class="js-wpt-skype js-wpt-skype-action js-wpt-skype-action-chat wpt-form-checkbox form-checkbox checkbox"
                                   data-skype-field-name="action"
                                <?php if($current_field['value']['action'] == 'chat' || !isset($current_field['value']['action']) ) {?> checked="checked" <?php }?>

                            /><label class="wpt-form-label wpt-form-checkbox-label"><?php _e('Chat', PMTI_Plugin::TEXT_DOMAIN); ?></label>

                            <div class="description wpt-form-description wpt-form-description-checkbox description-checkbox">
                                <?php _e('Start the conversation with an instant message.', PMTI_Plugin::TEXT_DOMAIN); ?>
                            </div>
                        </div>

                        <p class="inner-label"><?php _e('Choose how you want your button to look.', PMTI_Plugin::TEXT_DOMAIN); ?></p>

                        <select class="js-wpt-skype js-wpt-skype-color wpt-form-select form-select select"
                                data-wpt-type="select"
                                name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value][color]">
                            <option value="blue" data-skype-field-name="color"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-color" data-wpt-name="skype[color]"
                                <?php if($current_field['value']['color'] == 'blue') {?> selected="selected" <?php }?>

                            >
                                <?php _e('Blue', PMTI_Plugin::TEXT_DOMAIN); ?>
                            </option>
                            <option value="white" data-skype-field-name="color"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-color" data-wpt-name="skype[color]"
                                <?php if($current_field['value']['color'] == 'white') {?> selected="selected" <?php }?>

                            ><?php _e('White', PMTI_Plugin::TEXT_DOMAIN); ?>
                            </option>
                        </select><select class="js-wpt-skype js-wpt-skype-size wpt-form-select form-select select"
                                data-wpt-type="select"
                                name="wpcs_fields<?php echo $field_name;?>[<?php echo $field['meta_key'];?>][value][size]">
                            <option value="10" data-skype-field-name="size"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-size" data-wpt-name="skype[size]"
                                <?php if($current_field['value']['size'] == 10) {?> selected="selected" <?php }?>

                            >10px
                            </option>
                            <option value="12" data-skype-field-name="size"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-size" data-wpt-name="skype[size]"
                                <?php if($current_field['value']['size'] == 12) {?> selected="selected" <?php }?>
                            >12px
                            </option>
                            <option value="14" data-skype-field-name="size"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-size" data-wpt-name="skype[size]"
                                <?php if($current_field['value']['size'] == 14) {?> selected="selected" <?php }?>

                            >14px
                            </option>
                            <option value="16" data-skype-field-name="size"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-size" data-wpt-name="skype[size]"
                                <?php if($current_field['value']['size'] == 16) {?> selected="selected" <?php }?>

                            >16px
                            </option>
                            <option value="24" data-skype-field-name="size"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-size" data-wpt-name="skype[size]"
                                <?php if($current_field['value']['size'] == 24) {?> selected="selected" <?php }?>
                            >24px
                            </option>
                            <option value="32" data-skype-field-name="size"
                                    class="js-wpt-skype wpt-form-option form-option option"
                                    data-wpt-type="option" data-wpt-id="skype-size" data-wpt-name="skype[size]"
                                    selected="selected"
                                <?php if($current_field['value']['size'] == 32) {?> selected="selected" <?php }?>>
                                32px
                            </option>
                        </select>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


<script>
    wptSkypeData = {
        title: 'Customize plugin'
    };

    var wptSkype = (function ($) {
        var $parent, $skypename, $preview, $fields;
        var $popup = $('#tpl-wpt-skype-edit-button > div');

        function init() {

            $parent = $(this).parents('.js-wpt-field-item');
            $skypename = $('.js-wpt-skypename', $parent);
            $preview = $('.js-wpt-skype-preview', $parent);
            //$('.js-wpt-skypename-popup', $popup).val($skypename.val());

            $('.js-wpt-skype', $popup).on("change", function () {
                wptSkype.preview($popup, this);
            });
            $('.js-wpt-skype', $popup).on("keyup", function () {
                wptSkype.preview($popup, this);
            });
            wptSkype.preview($popup, this, 'init');

            $('#wpt-skype-edit-button-popup').on('click', '.js-wpt-close-thickbox', function () {
                var button = $('.js-wpt-skype-edit-button', $parent);
                var $extra_skype_data = {};
                //$skypename.val($('.js-wpt-skypename-popup', $popup).val());
                $('.js-wpt-skype', $popup).each(function () {
                    var $field_name = $(this).data('skype-field-name');
                    var $val = $(this).val();
                    if ($field_name) {
                        switch ($(this).data('wpt-type')) {
                            case 'checkbox':
                                if ($(this).is(':checked')) {
                                    $('.js-wpt-skype-' + $field_name, $parent).val($val);
                                    button.data($field_name, $val);
                                }
                                break;
                            case 'option':
                                if ($(this).is(':selected')) {
                                    $('.js-wpt-skype-' + $field_name, $parent).val($val);
                                    button.data($field_name, $val);
                                }
                                break;
                            case 'textfield':
                                $('.js-wpt-skype-' + $field_name, $parent).val($val);
                                button.data($field_name, $val);
                                break;
                        }
                    }
                });
                /**
                 * fix data for action
                 */

                tb_remove();
            });

            $('.wpt-form-checkbox').change(function(){
                if (1 < $('.wpt-form-checkbox:checked', $popup).length) {
                    $('.js-wpt-skype-action-value', $popup).val('dropdown');
                    $(this).data('action', 'dropdown');
                } else {
                    $('.js-wpt-skype-action-value', $popup).val($('.js-wpt-skype-action:checked').val());
                }
            });
        }

        function preview($popup, object, mode) {

            var $object = $(object);
            /**
             * be sure, that at lest one action is on
             */
            if ('checkbox' == $object.attr('type')) {
                if (0 == $('.js-wpt-skype-action:checked', $popup).length) {
                    $('.js-wpt-skype-action', $popup).each(function () {
                        if (this != object) {
                            $(this).attr('checked', 'checked');
                        }
                    });
                }
            }

            /**
             * participants
             */
            var $button = $('#wpt-skype-edit-button-popup-preview-button');
            $('#wpt-skype-preview', $button).html('');
            var $skypeNamePopup = $('.js-wpt-skypename-popup', $popup);
            var participants = $skypeNamePopup.length
                ? $skypeNamePopup.val()
                : '';

            /**
             * setup values
             */
            if ('undefined' != typeof mode && 'init' == mode) {
                if (value = $object.data('size')) {
                    $('.js-wpt-skype-size option', $popup).removeAttr('selected');
                    $('.js-wpt-skype-size [value=' + value + ']', $popup).attr('selected', 'selected');
                }
                if (value = $object.data('color')) {
                    $('.js-wpt-skype-color option', $popup).removeAttr('selected');
                    $('.js-wpt-skype-color [value=' + value + ']', $popup).attr('selected', 'selected');
                }
                if (value = $object.data('action')) {
                    switch (value) {
                        case 'dropdown':
                            $('.js-wpt-skype-action', $popup).attr('checked', 'checked');
                            break;
                        case 'chat':
                        case 'call':
                            $('.js-wpt-skype-action', $popup).removeAttr('checked');
                            $('.js-wpt-skype-action-' + value, $popup).attr('checked', 'checked');
                            break;
                        default:
                            $('.js-wpt-skype-action', $popup).removeAttr('checked');
                            $('.js-wpt-skype-action-call', $popup).attr('checked', 'checked');
                            break;
                    }
                }
            }
            /**
             * skypename
             */
            var skypename = "dropdown";
            if ($('.js-wpt-skype-action:checked', $popup).length < 2) {
                skypename = $('.js-wpt-skype-action:checked', $popup).val();
            }

            /**
             * Skype.ui
             */
            if (participants.length > 2) {

                if ('object' == typeof Skype) {
                    data = {
                        name: skypename,
                        element: "wpt-skype-preview",
                        participants: [participants],
                        imageSize: parseInt($('.js-wpt-skype-size option:selected', $popup).val()),
                        imageColor: $('.js-wpt-skype-color option:selected', $popup).val()
                    };

                    /**
                     * show tooltip
                     */
                    if ('dropdown' == data.name) {
                        $('small', $button).show();
                    } else {
                        $('small', $button).hide();
                    }
                    /**
                     * change parent background to see skype in white
                     */
                    if ('white' == data.imageColor) {
                        $button.addClass('dark-background');
                    } else {
                        $button.removeClass('dark-background');
                    }
                    Skype.ui(data);
                }
            }
        }

        return {
            init: init,
            preview: preview
        };

    })(jQuery);

    jQuery(document).ready(wptSkype.init);

    jQuery('#tpl-wpt-skype-edit-button .wpallimport-collapsed').find('.wpallimport-collapsed-header').not('.disabled').click(function (event) {
        var $parent = jQuery(this).parents('.wpallimport-collapsed:first');
        if ($parent.hasClass('closed')) {
            $parent.removeClass('closed');
            $parent.find('.wpallimport-collapsed-content:first').slideDown(400);
            event.stopImmediatePropagation(); // ensure function only fires once per click

        }
        else {
            $parent.addClass('closed');
            $parent.find('.wpallimport-collapsed-content:first').slideUp();
            event.stopImmediatePropagation(); // ensure function only fires once per click

        }
    });

</script>
