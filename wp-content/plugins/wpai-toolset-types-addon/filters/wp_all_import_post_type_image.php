<?php

/**
 * Add post type icons to toolset types CTP.
 *
 * @param $data
 * @return mixed
 */
function pmti_wp_all_import_post_type_image($data) {

    $post_type_option = new Types_Utils_Post_Type_Option();
    $to_display_posts = $post_type_option->get_post_types();

    if (!empty($to_display_posts)) {
        foreach ($to_display_posts as $slug => $ctp) {
            if (!empty($ctp['icon'])) {
                $data[$slug]['image'] = 'dashicons-' . $ctp['icon'];
            }
        }
    }

    return $data;
}