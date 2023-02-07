<?php

class Tds_class extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_table2 = 'tds_details';
		$this->load->model('accounting');
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'type', 'name', 'section', 'payment_code');
		$this->_search = array('name', 'type', 'section', 'payment_code');
		
		$this->_data['list'] = array(
				'heading' => array('ID', 'Type', 'Name', 'Section', 'Payment Code'),
				'class'   => array(
					'id'           => 'ID',
					'type'         => 'Code',
					'name'         => 'Text',
					'section'      => 'Code',
					'payment_code' => 'Code'
				),
				'link_col'     => 'id',
				'link_url'     => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}

	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('type', 'Type', 'trim|required');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'           => 0,
				'type'         => 'Nature Of Payment',
				'name'         => '',
				'section'      => '',
				'payment_code' => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
				
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['details'] = $this->accounting->getTDSDetail($id);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			$data = array(
				'type'         => $this->input->post('type'),
				'name'         => $this->input->post('name'),
				'section'      => $this->input->post('section'),
				'payment_code' => $this->input->post('payment_code'),
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_updateDetails($id);
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _updateDetails($id) {
		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$payment_ids = $this->input->post('payment_id');
		$new_payment_ids = $this->input->post('new_payment_id');

		if ($payment_ids != null) {
			$applicable_dates = $this->input->post('applicable_date');
			$tdss = $this->input->post('tds');
			$scs  = $this->input->post('surcharge');
			$ecs  = $this->input->post('edu_cess');
			$hecs = $this->input->post('hedu_cess');
			foreach ($payment_ids as $index => $payment_id) {
				if (! in_array("$index", $delete_ids)) {
					$row = array(
						'payment_id'      => $payment_id,
						'applicable_date' => $applicable_dates[$index],
						'tds'             => $tdss[$index],
						'surcharge'       => $scs[$index],
						'edu_cess'        => $ecs[$index],
						'hedu_cess'       => $hecs[$index],
					);
					$this->kaabar->save($this->_table2, $row, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->db->delete($this->_table2, ['id' => $index]);
				}
			}
		}

		if ($new_payment_ids != null) {
			$payment_id = $this->input->post('new_payment_id');
			$applicable_dates = $this->input->post('new_applicable_date');
			$tdss = $this->input->post('new_tds');
			$scs  = $this->input->post('new_surcharge');
			$ecs  = $this->input->post('new_edu_cess');
			$hecs = $this->input->post('new_hedu_cess');
			foreach ($new_payment_ids as $index => $payment_id) {
				if (strlen(trim($applicable_dates[$index])) > 0) {
					$row = array(
						'deductee_id'     => $id, 
						'payment_id'      => $payment_id,
						'applicable_date' => $applicable_dates[$index],
						'tds'             => $tdss[$index],
						'surcharge'       => $scs[$index],
						'edu_cess'        => $ecs[$index],
						'hedu_cess'       => $hecs[$index],
					);
					$this->kaabar->save($this->_table2, $row);
				}
			}
		}
	}
}
