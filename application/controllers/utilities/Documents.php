<?php

class Documents extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('export');
	}
	
	function index() {

	}
}

