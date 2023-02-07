<?php

class Charts extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($type = null, $year = null) {
		if(! Auth::isValidUser()) {
			redirect('main/login');
		}

		if ($type == null)
			redirect();
		elseif ($type == 'Import') {
			$this->load->model('import');
			$dashboard_import = $this->import->getDashboardImport();

			$categories = array();
			$series = array();
			foreach($dashboard_import as $cbm) {
				foreach($cbm as $m) {
					$categories[] = $m['Month'];
					$series['Container20'][] = $m['Container20'];
					$series['Container40'][] = $m['Container40'];
				}
			}
			
			$data['categories'] = $categories;
			$data['series'] = $series;
			$data['page_title'] = 'Import Jobs Chart';
			$data['subtitle'] = Settings::get('company_name');
		}
		elseif ($type == 'Export') {
			$this->load->model('export');
			$dashboard_export = $this->export->getDashboardExport();

			$categories = array();
			$series = array();
			foreach($dashboard_export as $cbm) {
				foreach($cbm as $m) {
					$categories[] = $m['Month'];
					$series['Container20'][] = $m['Container20'];
					$series['Container40'][] = $m['Container40'];
				}
			}
			
			$data['categories'] = $categories;
			$data['series'] = $series;
			$data['page_title'] = humanize($type) . ' Jobs Chart';
			$data['subtitle'] = Settings::get('company_name');
		}
		
		$data['y_axis_title'] = 'No. of ' . humanize($type);
		$this->load->view('charts', $data);
	}
}
