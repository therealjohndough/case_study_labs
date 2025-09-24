<?php 
/**
 *
 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
 */

class PMTI_Admin_Import extends PMTI_Controller_Admin {
	public function index( $post_type, $post ) {
		$this->data['post_type'] = $post_type;
		$this->data['post'] =& $post;
		$this->render();
	}			
}
