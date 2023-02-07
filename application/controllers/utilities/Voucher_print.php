<?php

class Voucher_print extends MY_Controller {
	function __construct() {
		parent::__construct();
		
	}
	
	function index() {
		$data['page_title'] = "Voucher Details";
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function getInvoice($invoice_id = 0) {

		$response = [];
		
		if($invoice_id > 0){

			$invoice = $this->accounting->getInvoice($invoice_id);
			
			if($invoice['ledger_category'] == 'PARTY')
				$invoice['ledger_name'] = $this->kaabar->getField('parties', $invoice['ledger_id'], 'id', 'name');
			elseif($invoice['ledger_category'] == 'AGENT')
				$invoice['ledger_name'] = $this->kaabar->getField('new_agents', $invoice['ledger_id'], 'id', 'name');
			elseif($invoice['ledger_category'] == 'CONSIGNEE')
				$invoice['ledger_name'] = $this->kaabar->getField('consignees', $invoice['ledger_id'], 'id', 'name');
			elseif($invoice['ledger_category'] == 'VENDOR')
				$invoice['ledger_name'] = $this->kaabar->getField('vendors', $invoice['ledger_id'], 'id', 'name');

			$rows = $this->accounting->getInvoiceDetails($invoice_id);


			// foreach ($rows as $key => $value) {
				
			// 	$currency_name = $this->kaabar->getRow('currencies', $value['currency_id']);
			// 	$rows[$key]['currency_name'] = $currency_name['code'].' - '.$currency_name['name'];

			// 	$is_inr =  form_dropdown('is_inr['.$value['id'].']', array('Yes'=>'Yes', 'No'=>'No'), $value['is_inr'], 'class="form-control form-control-sm Unchanged IsINR d-none"');

			// 	$unit =  form_dropdown('unit['.$value['id'].']', getSelectOptions('units'), $value['unit_id'], 'class="Unchanged form-control form-control-sm Unit"');
						
			// 	$gst = form_dropdown('gst['.$value['id'].']', getEnumSetOptions('invoices', 'gst_per'), $value['gst'], 'class="Unchanged form-control form-control-sm GST"');

			// 	unset($rows[$key]['is_inr']);
			// 	$rows[$key]['is_inr'] = $is_inr;

			// 	unset($rows[$key]['unit_id']);
			// 	$rows[$key]['unit_id'] = $unit;

			// 	unset($rows[$key]['gst']);
			// 	$rows[$key]['gst'] = $gst;


			// 	$deleteBtn = '<button type="button" class="btn btn-danger btn-sm DeleteCheckbox"><i class="fa fa-minus"></i></button>';

			// 	//$deleteBtn = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));

			// 	$rows[$key]['delete_btn'] = $deleteBtn;
			// }

			$response['code'] = 1;
			$response['success'] = true;
			$response['rows'] = $rows;
			$response['invoice'] = $invoice;
			//$response['messages'] = 'Job No Required';

		}
		else
		{
			$response['code'] = 0;
			$response['success'] = false;
			$response['messages'] = 'Invoice No Required';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function getInvoiceType($type) {
		$invoice = $this->db->select('id, idkaabar_code as name')->where("invoice_type" , $type)->get("invoices")->result_array();
		echo json_encode($invoice);
	}
}