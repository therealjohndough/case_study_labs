<?php

class PMTI_Import_List extends PMTI_Model_List {
	public function __construct() {
		parent::__construct();
		$this->setTable(PMTI_Plugin::getInstance()->getTablePrefix() . 'imports');
	}
}