<?php
/**
 * @param $post_type
 * @param $post
 */
function pmti_pmxi_extend_options_custom_fields($post_type, $post) {
	$toolset_controller = new PMTI_Admin_Import();
    $toolset_controller->index($post_type, $post);
}
