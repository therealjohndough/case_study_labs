<?php
/**
 *  admin_init action.
 */
function pmti_admin_init() {
    wp_enqueue_style('wpai-toolset-add-on-updater', PMTI_ROOT_URL . '/static/css/plugin-update-styles.css', [], PMTI_VERSION);
}