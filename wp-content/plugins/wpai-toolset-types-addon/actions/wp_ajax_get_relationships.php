<?php

/**
 *  Render Toolset groups
 */
function pmti_wp_ajax_get_relationships() {

    if (!check_ajax_referer('wp_all_import_secure', 'security', FALSE)) {
        exit(json_encode(array('html' => __('Security check', PMTI_Plugin::TEXT_DOMAIN))));
    }

    if (!current_user_can(PMXI_Plugin::$capabilities)) {
        exit(json_encode(array('html' => __('Security check', PMTI_Plugin::TEXT_DOMAIN))));
    }

    ob_start();

    $toolset_groups = PMXI_Plugin::$session->wpcs_groups;

    $toolset_obj = FALSE;

    if (!empty($toolset_groups)) {
        foreach ($toolset_groups as $key => $group) {
            if ($group['id'] == $_GET['toolset']) {
                $toolset_obj = $group;
                break;
            }
        }
    }

    $import = new PMXI_Import_Record();

    if (!empty($_GET['id'])) {
        $import->getById($_GET['id']);
    }

    $is_loaded_template = (!empty(PMXI_Plugin::$session->is_loaded_template)) ? PMXI_Plugin::$session->is_loaded_template : FALSE;

    if ($is_loaded_template) {
        $default = PMTI_Plugin::get_default_import_options();
        $template = new PMXI_Template_Record();
        if (!$template->getById($is_loaded_template)->isEmpty()) {
            $options = (!empty($template->options) ? $template->options : []) + $default;
        }
    } elseif (!$import->isEmpty()) {
        $options = $import->options;
    } else {
        $options = PMXI_Plugin::$session->options;
    }

    $relationship = new wpai_toolset_types_add_on\relationships\Relationship($toolset_obj, $options);
    $relationship->view();

    exit(json_encode(['html' => ob_get_clean()]));
}