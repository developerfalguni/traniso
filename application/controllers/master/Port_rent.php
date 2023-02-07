<?php

class Port_rent extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
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
		
		$data['list']['heading'] = array('ID','Port', 'Berth No', 'Product');
		$data['list']['class'] = array(
			'id'           => 'ID',
			'port_name'    => 'Text',
			'berth_no'     => 'Text',
			'product_name' => 'Text');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows'] = $this->office->countPortRents($search);
		$config['per_page'] = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getPortRents($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('port_id', 'Port', 'trim|required');
		$this->form_validation->set_rules('berth_no', 'Berth No', 'required');
		$this->form_validation->set_rules('product_id', 'Product', 'trim|required');
		$this->form_validation->set_rules('handling_charges', 'Handling Charges', 'trim');
		$this->form_validation->set_rules('wharfage', 'Wharfage Rate', 'trim|required');
		$this->form_validation->set_rules('service_tax', 'Service Tax', 'trim|required');
		$this->form_validation->set_rules('tds', 'TDS', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'          => 0,
				'port_id'     => 0,
				'berth_no'    => '',
				'product_id'  => 0,
				'handling_charges' => 0,
				'wharfage'    => 0,
				'service_tax' => 0,
				'tds'         => 0
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['ground_rents'] = $this->office->getGroundRents($id);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page'] = $this->_clspath.$this->_class.'_edit';
	
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'port_id'          => $this->input->post('port_id'),
					'berth_no'         => join(',', $this->input->post('berth_no')),
					'product_id'       => $this->input->post('product_id'),
					'handling_charges' => $this->input->post('handling_charges'),
					'wharfage'         => $this->input->post('wharfage'),
					'service_tax'      => $this->input->post('service_tax'),
					'tds'              => $this->input->post('tds')
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				$this->_updateGroundRents($id);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _updateGroundRents($id) {
		$delete_ids    = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		$from_days     = $this->input->post('from_day');
		$new_from_days = $this->input->post('new_from_day');

		if ($from_days != null) {
			$wef_dates = $this->input->post('wef_date');
			$to_days = $this->input->post('to_day');
			$rates = $this->input->post('rate');
			foreach ($from_days as $index => $from_day) {
				if (! in_array("$index", $delete_ids) && intval($from_day) > 0 && intval($to_days[$index]) > 0) {
					$row = array(
						'wef_date' => $wef_dates[$index],
						'from_day' => $from_day,
						'to_day'   => $to_days[$index],
						'rate'     => $rates[$index],
					);
					$this->kaabar->save('port_tariffs', $row, array('id' => $index));
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->db->delete('port_tariffs', array('id' => $index));
				}
			}
		}

		if ($new_from_days != null) {
			$wef_dates = $this->input->post('new_wef_date');
			$to_days = $this->input->post('new_to_day');
			$rates = $this->input->post('new_rate');
			foreach ($new_from_days as $index => $from_day) {
				if (intval($from_day) > 0 && intval($to_days[$index]) > 0) {
					$row = array(
						'port_rent_id' => $id,
						'wef_date'     => $wef_dates[$index],
						'from_day'     => $from_day,
						'to_day'       => $to_days[$index],
						'rate'         => $rates[$index],
					);
					$this->kaabar->save('port_tariffs', $row);
				}
			}
		}
	}
}
