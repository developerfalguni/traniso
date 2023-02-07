<?php

class Consignee extends MY_Controller {
	var $_table2 ;

	function __construct() {
		parent::__construct();
		
		$this->_table2 = 'consignee_addresses';
		$this->load->model('office');
	}
		
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'vi_code', 'name');
		$this->_search = array('vi_code', 'name');
		
		$this->_data['list'] = array(
			'heading' => array('ID', 'Code', 'Name'),
			'class' => array(
				'id'      => 'ID',
				'vi_code' => 'Code',
				'name'    => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('vi_code', 'Consignee Code', 'trim');
		$this->form_validation->set_rules('name', 'Consignee Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'      => 0,
				'vi_code' => '',
				'name'    => '', 
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['addresses'] = $this->kaabar->getRows($this->_table2, $id, 'consignee_id');
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'vi_code' => $this->input->post('vi_code'),
					'name'    => $this->input->post('name'),
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				$this->_updateAddresses($id);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _updateAddresses($id) {
		$vi_codes = $this->input->post('address_vi_code');
		if($vi_codes != null) {
			$addresses = $this->input->post('address');
			$citys     = $this->input->post('city');
			$countrys  = $this->input->post('country');
			$contacts  = $this->input->post('contact');
			$emails    = $this->input->post('email');

			foreach ($vi_codes as $index => $vi_code) {
				$data = array(
					'consignee_id' => $id,
					'vi_code'      => $vi_code,
					'address'      => $addresses[$index],
					'city'         => $citys[$index],
					'country'      => $countrys[$index],
					'contact'      => $contacts[$index],
					'email'        => $emails[$index],
				);
				$this->kaabar->save($this->_table2, $data, array('id' => $index));
			}
		}
	}

	function createAddress() {
		$consignee_id = $this->input->post('consignee_id');
		$new_code     = $this->input->post('new_code');

		if($new_code != null && $consignee_id > 0) {
			$data = array(
				'consignee_id' => $consignee_id,
				'vi_code'      => $new_code
			);
			$this->kaabar->save($this->_table2, $data);
		}

		redirect($this->_clspath.$this->_class.'/edit/'.$consignee_id);
	}

	public function ajax() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));

			$sql = "SELECT C.id, CA.id AS consignee_address_id, C.consignee_name, CA.address, CA.city, CA.country 
			FROM consignees C LEFT OUTER JOIN consignee_addresses CA ON C.id = CA.consignee_id
			WHERE C.consignee_name LIKE '%$search%' OR CA.address LIKE '%$search%' OR CA.city LIKE '%$search%'
			GROUP BY C.consignee_name
			ORDER BY C.consignee_name
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
}
