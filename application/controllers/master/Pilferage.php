<?php

class Pilferage extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('vehicle_m');
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$starting_row = intval($starting_row);

		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}

		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Registration No', 'Date', 'Liters');
		$data['list']['class'] = array(
				'id'              => 'ID',
				'registration_no' => 'Text',
				'datetime'        => 'DateTime',
				'liters'          => 'Numeric');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->vehicle_m->countPilferages($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->vehicle_m->getPilferages($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('registration_no', 'Registration No', 'trim|required');
						
		$row = $this->vehicle_m->getPilferage($id);
		if($row == false) {
			$row = array(
				'id'         => 0,
				'vehicle_id' => 0,
				'pilferages' => array()
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
		$data['registration_no'] = $this->kaabar->getField('vehicles', $row['vehicle_id'], 'id', 'registration_no');
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$dates = $this->input->post("date");
				$delete_ids = $this->input->post('delete_id') == false? array("0" => "0") : $this->input->post('delete_id');
				if ($dates) {
					$times  = $this->input->post("time");
					$liters = $this->input->post("liters");
					foreach ($dates as $index => $date) {
						$data = array(
							'datetime'   => $date . ' '. $times[$index],
							'liters'     => $liters[$index],
						);
						$this->kaabar->save($this->_table, $data, array('id' => $index));
					}
				}	

				if ($delete_ids != null) {
					foreach ($delete_ids as $index => $tmp) {
						if ($index > 0) {
							$this->kaabar->delete($this->_table, array('id' => $index));
						}
					}
				}

				$dates = $this->input->post('new_date');
				if ($dates) {
					$times  = $this->input->post("new_time");
					$liters = $this->input->post("new_liters");
					foreach ($dates as $index => $date) {
						if ($liters[$index] > 0) {
							$data = array(
								'vehicle_id' => $this->input->post('vehicle_id'),
								'datetime'   => $date . ' '. $times[$index],
								'liters'     => $liters[$index],
							);
							$id = $this->kaabar->save($this->_table, $data);
						}
					}
				}

				setSessionAlert('SAVED', 'success');				
			}
			else {
				setSessionError('NO_PERMISSION');
			}
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function ajaxRegistrationNos() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));		
			$sql = "SELECT id, registration_no, category, type FROM vehicles 
			WHERE track_data = 'Yes' AND registration_no LIKE '%$search%' 
			ORDER BY registration_no";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
	
}
