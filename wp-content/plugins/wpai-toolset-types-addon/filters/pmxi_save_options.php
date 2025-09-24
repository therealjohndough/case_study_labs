<?php
/**
 * @param $post
 * @return mixed
 */
function pmti_pmxi_save_options($post) {
	if (PMXI_Plugin::getInstance()->getAdminCurrentScreen()->action == 'options') {
		if ($post['wpcs_update_logic'] == 'only') {
			$post['wpcs_list'] = explode(",", $post['wpcs_only_list']);
		} elseif ($post['wpcs_update_logic'] == 'all_except') {
			$post['wpcs_list'] = explode(",", $post['wpcs_except_list']);
		}
        if ($post['wpcs_relationships_update_logic'] == 'only') {
            $post['wpcs_relationships_list'] = explode(",", $post['wpcs_relationships_only_list']);
        } elseif ($post['wpcs_relationships_update_logic'] == 'all_except') {
            $post['wpcs_relationships_list'] = explode(",", $post['wpcs_relationships_except_list']);
        }
	}	
	return $post;
}
