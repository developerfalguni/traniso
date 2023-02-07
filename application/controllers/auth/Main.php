<?php

class Main extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}
		
		$data['counts'] = $this->auth->getCounts();
		$data['page_title'] = 'Auth Dashbaord';
		$data['page'] = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

}
