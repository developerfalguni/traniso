<?php

class Bilty extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('export');
	}
	
	function index($starting_row = 0) {
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getLR($lr_id = 0){

		$response = [];
		
		if($lr_id > 0){

			$bilty = $this->kaabar->getRow('bilties', $lr_id);
			$parties_add = $this->kaabar->getRows('party_addresses', ['party_id' => $lr_id]);
				
			$response['success'] = true;
			$response['bilty'] = $bilty;
			$response['parties_add'] = $parties_add;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select LR';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function edit($id = 0) {

		$data['id'] = array('id' => 0);

		$data['page_title'] = 'LR Create';
		$data['page']       = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
		
	}

	function ajaxEdit() {

		$response = [];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('from_place', 'From Place', 'trim|required');
		$this->form_validation->set_rules('to_place', 'To Place', 'trim|required');
		$this->form_validation->set_rules('lr_no', 'LR No', 'trim|required');
		$this->form_validation->set_rules('consignee', 'Consignee Name', 'trim|required');
		$this->form_validation->set_rules('consignee_add', 'Consignee Address', 'trim|required');
		$this->form_validation->set_rules('consignor', 'Consignor Name', 'trim|required');
		$this->form_validation->set_rules('consignor_add', 'Consignor Address', 'trim|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		
		if ($this->form_validation->run() == false) {
			//setSessionError(validation_errors());
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
    		}
        }
		else
		{

			$row_id = $this->input->post('id');

			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {

				$data = array(
					'date'            => $this->input->post('date'),
					'from_place'         => $this->input->post('from_place'),
					'to_place'         => $this->input->post('to_place'),
					'lr_no'         => $this->input->post('lr_no'),
					'vehicle_no'           => $this->input->post('vehicle_no'),
					'driver_no'          => $this->input->post('driver_no'),
					'party_contact'          => $this->input->post('party_contact'),
					'consignor'  => $this->input->post('consignor'),
					'consignee'          => $this->input->post('consignee'),
					'consignee_add'          => $this->input->post('consignee_add'),
					'consignor_add'   => $this->input->post('consignor_add'),
					'loading_from'   => $this->input->post('loading_from'),
					'gstpaid_by'   	  => $this->input->post('gstpaid_by'),
					'insurance'       => $this->input->post('insurance'),
					'packages'   	  => $this->input->post('packages'),
					'unit'       => $this->input->post('unit'),
					'commodity'       => $this->input->post('commodity'),
					'weight'         => $this->input->post('weight'),
					'charge_weight'    => $this->input->post('charge_weight'),
					'freight_type'        => $this->input->post('freight_type'),
					'freight_rate'        => $this->input->post('freight_rate'),
					'guarantee_charge'          => $this->input->post('guarantee_charge'),
					'bilty_charge'         => $this->input->post('bilty_charge'),
					'other_charge'    => $this->input->post('other_charge'),
					'advance_amt'        => $this->input->post('advance_amt'),
					'total_freight'        => $this->input->post('total_freight'),
					'roundoff'          => $this->input->post('roundoff'),
					'balance_amt'         => $this->input->post('balance_amt'),
					
				);
				
				
				$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);
				
				// $this->_updateAddress($id, 0);
				
				if($id){
					$response['success'] = true;
		        	$response['messages'] = 'Succesfully Saved';	
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'Somathing Went Wrong';	
				}
			}
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){
				$this->db->where(array('id' => $id));
				$result = $this->db->get($this->_table)->num_rows();
				
				if($result = 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'Bilty not Found';			
				}
				else
				{
					$this->db->delete($this->_table, ['id' => $id]);
											
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted Bilty';			
				}
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Bilty First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function lrList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('lr_no', $this->input->get("q"));
			}
			
			$query = $this->db->select('id, lr_no as text')
							->limit(10)
							->get('bilties');
							
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New LR'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }
}

