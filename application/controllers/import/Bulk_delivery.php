<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Bulk_delivery extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table  = 'deliveries_stuffings';
		$this->load->model('import');
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search');
			$search = false;
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			$starting_row = 0;
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
		'heading' => array('ID', 'Date', 'BL No', 'BE No', 'Total Weight', 'Total Trips'),
		'class' => array(
			'id'           => 'Text',
			'date'         => 'Text',
			'bl_no'        => 'Text',
			'be_no'        => 'Text',
			'total_weight' => 'Text',
			'total_trips'  => 'Text'),
		'link_col' => 'id',
		'link_url' => $this->_clspath.$this->_class."/edit/");
		$data['label_class'] = $this->import->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_clspath.$this->_class."/index");
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		$config['total_rows'] = $this->_count($search);
		$config['per_page'] = Settings::get('rows_per_page');
		$this->pagination->initialize($config);

		$data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);

		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/00-00-0000/0/0', '<i class="icon-white icon-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['docs_url'] = $this->_docs;
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}

	function _count($search = '') {
		$sql = "SELECT COUNT(D.id) AS numrows 
		FROM deliveries_stuffings D INNER JOIN jobs J ON D.job_id = J.id
		WHERE D.date != '0000-00-00' AND (DATE_FORMAT(D.date, '%d-%m-%Y') LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%')
		GROUP BY D.date, D.job_id";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _get($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT CONCAT(DATE_FORMAT(D.date, '%d-%m-%Y'), '/', D.job_id) AS id, DATE_FORMAT(D.date, '%d-%m-%Y') AS date, J.bl_no, J.be_no, SUM(D.nett_weight) AS total_weight, COUNT(D.id) AS total_trips
		FROM deliveries_stuffings D INNER JOIN jobs J ON D.job_id = J.id
		WHERE D.date != '0000-00-00' AND (DATE_FORMAT(D.date, '%d-%m-%Y') LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%')
		GROUP BY D.date, D.job_id
		ORDER BY D.date DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function edit($date, $job_id, $id = 0) {
		$id = intval($id);
		$date = $date != '00-00-0000' ? $date : date('d-m-Y');

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('date', 'Date', 'trim');
		$row = $this->_getBulkDelivery($job_id, $date);
		if($row == false) {
			$row = array(
				'id'            => 0,
				'job_id'        => 0,
				'date'          => date('d-m-Y'),
				'gatepass_no'   => '',
				'gatepass_date' => '',
				'vehicle_no'    => '',
				'nett_weight'   => 0,
			);
		}

		$data['id']     = array('id' => $id);
		$data['row']    = $row;
		$data['job_id'] = $job_id;
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['docs_url']   = $this->_docs;
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$date");
			
			$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
			
			$nett_weights = $this->input->post('nett_weight');
			if ($nett_weights != null) {
				foreach ($nett_weights as $index => $nett_weight) {
					if (! in_array("$index", $delete_ids) && $nett_weight > 0) {
						$this->kaabar->save($this->_table, ['date' => $this->input->post('date'), 'nett_weight' => $nett_weight], ['id' => $index]);
					}
				}
			}

			if ($delete_ids != null) {
				foreach ($delete_ids as $index) {
					if ($index > 0) {
						$this->db->delete($this->_table, ['id' => $index]);
					}
				}
			}

			$new_job_ids = $this->input->post('new_job_id');
			if ($new_job_ids != null) {
				$new_vehicle_nos    = $this->input->post('new_vehicle_no');
				$new_gatepass_nos   = $this->input->post('new_gatepass_no');
				$new_gatepass_dates = $this->input->post('new_gatepass_date');
				$new_nett_weights   = $this->input->post('new_nett_weight');

				foreach ($new_job_ids as $index => $new_job_id) {
					if ($new_job_id > 0) {
						$row = [
							'job_id' => $new_job_id,
							'date'   => $this->input->post('date'),
	  						'vehicle_no'    => $new_vehicle_nos[$index],
	  						'gatepass_no'   => $new_gatepass_nos[$index],
	  						'gatepass_date' => $new_gatepass_dates[$index],
	  						'nett_weight'   => $new_nett_weights[$index],
						];
						$id = $this->kaabar->save($this->_table, $row);
					}
				}
			}
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class);
		}
	}

	function _getBulkDelivery($job_id, $date){
		$sql = "SELECT D.id, D.job_id, DATE_FORMAT(D.date, '%d-%m-%Y') AS date, D.gatepass_no, DATE_FORMAT(D.gatepass_date, '%d-%m-%Y') AS gatepass_date, D.vehicle_no, D.nett_weight, J.bl_no
		FROM deliveries_stuffings D
			INNER JOIN jobs J ON D.job_id = J.id
		WHERE D.date = ? AND D.job_id = ?
		ORDER BY D.id";
		$query = $this->db->query($sql, [convDate($date), $job_id]);
		$rows = $query->result_array();
		$result = ['delivery' => '', 'date' => ''];
		$result['delivery'] = $rows;
		foreach ($rows as $row) {
			$result['date'] = $row['date'];
		}
		return $result;
	}

	function ajaxJobs() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			
			$sql = "SELECT id, bl_no, be_no
			FROM jobs
			WHERE cargo_type = 'Bulk' AND (bl_no LIKE '%$search%' OR be_no LIKE '%$search%') 
			LIMIT 0,50";
			$query = $this->db->query($sql);
			$rows  = $query->result_array();
			
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}
}