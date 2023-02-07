<?php

use mikehaertl\wkhtmlto\Pdf;

class Gst extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('export');
		$this->_folder 		= 'images/';
		$this->_path   		= FCPATH . $this->_folder;
		$this->_companyId = $this->export->getCompanyID();
		$this->invoiceType = 'ExportGst';
	}

	function index($type = 'Export') {
		redirect($this->_clspath.$this->_class.'/edit/'.$type);
	}

	function getInvoice($invoice_id = 0) {

		$response = [];
		
		if($invoice_id > 0){

			$invoice = $this->accounting->getInvoice($invoice_id);
			
			if($invoice['ledger_category'] == 'PARTY')
				$invoice['ledger_name'] = $this->kaabar->getField('parties', $invoice['ledger_id'], 'id', 'name');
			elseif($invoice['ledger_category'] == 'AGENT')
				$invoice['ledger_name'] = $this->kaabar->getField('new_agents', $invoice['ledger_id'], 'id', 'company_name');
			elseif($invoice['ledger_category'] == 'CONSIGNEE')
				$invoice['ledger_name'] = $this->kaabar->getField('consignees', $invoice['ledger_id'], 'id', 'consignee_name');
			elseif($invoice['ledger_category'] == 'VENDOR')
				$invoice['ledger_name'] = $this->kaabar->getField('vendors', $invoice['ledger_id'], 'id', 'name');

			$rows = $this->accounting->getInvoiceDetails($invoice_id);


			foreach ($rows as $key => $value) {
				
				
				$currency_name = $this->kaabar->getRow('currencies', $value['currency_id']);
				$rows[$key]['currency_name'] = $currency_name['code'].' - '.$currency_name['name'];

				$is_inr =  form_dropdown('is_inr['.$value['id'].']', array('Yes'=>'Yes', 'No'=>'No'), $value['is_inr'], 'class="form-control form-control-sm Unchanged IsINR d-none"');

				$unit =  form_dropdown('unit['.$value['id'].']', getSelectOptions('units'), $value['unit_id'], 'class="Unchanged form-control form-control-sm Unit"');
						
				$gst = form_dropdown('gst['.$value['id'].']', getEnumSetOptions('invoices', 'gst_per'), $value['gst'], 'class="Unchanged form-control form-control-sm GST"');

				unset($rows[$key]['is_inr']);
				$rows[$key]['is_inr'] = $is_inr;

				unset($rows[$key]['unit_id']);
				$rows[$key]['unit_id'] = $unit;

				unset($rows[$key]['gst']);
				$rows[$key]['gst'] = $gst;


				$deleteBtn = '<button type="button" class="btn btn-danger btn-sm DeleteCheckbox"><i class="fa fa-minus"></i></button>';

				$rows[$key]['delete_btn'] = $deleteBtn;
			}

			$einvoice = $this->kaabar->getRow('einvoices', $invoice_id, 'voucher_id');
			$invoice['einv'] = $einvoice;
			$invoice['einv']['cnl_img'] = '';

			if(isset($einvoice['status']) == 'CNL'){
				$company = $this->kaabar->getRow('companies', $this->_companyId);
				$invoice['einv']['cnl_img'] = $company['cancel_img'];
			}

			$invoice['einv_sts'] = $einvoice ? true : false;;

			$response['code'] = 1;
			$response['success'] = true;
			$response['rows'] = $rows;
			$response['invoice'] = $invoice;

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

	function getPendingJobsItems($job_id) {
		$sql = "SELECT VJD.*, BI.code AS bill_item_code, BI.name AS bill_item_name, BI.category, STAX.name AS stax_code, L.name as vendor_name, C.name as currency_name, CC.name as sell_currency_name, VJD.file
		FROM costsheets VJD LEFT OUTER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		 	LEFT OUTER JOIN jobs J ON VJD.job_id = J.id
			LEFT OUTER JOIN stax_categories STAX ON BI.stax_category_id = STAX.id
			LEFT OUTER JOIN vendors L ON VJD.vendor_id = L.id
			LEFT OUTER JOIN currencies C ON VJD.currency_id = C.id
			LEFT OUTER JOIN currencies CC ON VJD.sell_currency_id = CC.id
		WHERE VJD.job_id = ? AND VJD.status = 'Pending' AND VJD.billing_type = 'TX'
		ORDER BY BI.category, VJD.sr_no, BI.stax_category_id, VJD.particulars";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function getJob($job_id = 0) {

		$response = [];
		
		if($job_id){

			//// Get Job Details
			$job = $this->export->getJob($job_id);
			if($job['billing_party_category'] == 'PARTY')
				$job['billing_party_name'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'AGENT')
				$job['billing_party_name'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'company_name');
			elseif($job['billing_party_category'] == 'CONSIGNEE')
				$job['billing_party_name'] = $this->kaabar->getField('consignees', $job['billing_party_id'], 'id', 'consignee_name');
			elseif($job['billing_party_category'] == 'VENDOR')
				$job['billing_party_name'] = $this->kaabar->getField('vendors', $job['billing_party_id'], 'id', 'name');

			///// Get Costsheet Details
			$rows = $this->getPendingJobsItems($job_id);

			if($rows){
				foreach ($rows as $key => $value) {
					
					$hsn = $this->kaabar->getRow('ledgers', $value['bill_item_id'], 'id');

					$sell_is_inr =  form_dropdown('kbr_is_inr['.$value['id'].']', array('Yes'=>'Yes', 'No'=>'No'), $value['sell_is_inr'], 'class="form-control form-control-sm Unchanged SellIsINR d-none"');

					$sell_unit =  form_dropdown('kbr_unit['.$value['id'].']', getSelectOptions('units'), $value['sell_unit_id'], 'class="Unchanged form-control form-control-sm SellUnit"');

					$gst = form_dropdown('kbr_gst['.$value['id'].']', getEnumSetOptions('invoices', 'gst_per'), '18', 'class="Unchanged form-control form-control-sm GST"');

					unset($rows[$key]['sell_is_inr']);
					unset($rows[$key]['sell_unit_id']);
					$rows[$key]['sell_is_inr'] = $sell_is_inr;
					$rows[$key]['sell_unit_id'] = $sell_unit;
					$rows[$key]['hsn_code'] = $hsn['sac_hsn'];
					$rows[$key]['gst'] = $gst;

					$total_gst = $value['sell_amount']*18/100;
					
					$cgst = $total_gst/2;
					$sgst = $total_gst/2;
					$igst = 0;
					$gst_amount = $total_gst;
					$gross_amount = ($value['sell_amount']+$total_gst);


					$rows[$key]['cgst'] = $cgst;
					$rows[$key]['sgst'] = $sgst;
					$rows[$key]['igst'] = $igst;
					$rows[$key]['gst_amount'] = $gst_amount;
					$rows[$key]['gross_amount'] = $gross_amount;
					
					$deleteBtn = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));

					$rows[$key]['delete_btn'] = $deleteBtn;
				}
			}

			$response['code'] = 1;
			$response['success'] = true;
			$response['rows'] = $rows;
			$response['job'] = $job;


		}
		else
		{
			$response['code'] = 0;
			$response['success'] = false;
			$response['messages'] = 'Job No Required';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function edit($type = 'Export') {

		$data['id'] = array('id' => 0);
		$data['type'] = $type;
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function ajaxEdit($type = 'Export') {
		
		$response = [];

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('billingParty_id', 'Party Name', 'trim|required');
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		
		if ($this->form_validation->run() == false) {
			
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
				
		}
		else {

			$row_id = $this->input->post('id');
			$job_id = $this->input->post('job_list');

			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				
				$einv = $this->kaabar->getRow('einvoices', ['voucher_id' => $row_id]);

				if($einv)
				{
					$response['success'] = false;
		        	$response['messages'] = 'eInvoice Generated, You can not update Invoice...!';	
				}
				else
				{
					$data = array(
						'job_id'			=> $job_id,
						'vType'     		=> $type,
						'ledger_id'     	=> $this->input->post('billingParty_id'),
						'ledger_category'   => $this->input->post('billingParty_category'),
						'date'     			=> $this->input->post('date'),
						'sub_amount'     	=> $this->input->post('sub_amount'),
						'cgst'     			=> $this->input->post('cgst_amount'),
						'sgst'     			=> $this->input->post('sgst_amount'),
						'igst'     			=> $this->input->post('igst_amount'),
						'total_gst'     	=> $this->input->post('total_gst'),
						'additional_charge' => $this->input->post('additional_charge'),
						'roundoff'     		=> $this->input->post('roundoff'),
						'net_amount'     	=> $this->input->post('net_amount'),
					);

					if($row_id == 0){
						$data['created_by'] = Auth::getCurrUID();
						$data['updated_by'] = Auth::getCurrUID();
						$data['company_id'] = $this->kaabar->getCompanyID();
						$data['invoice_type'] = $this->invoiceType;
						
					}else{
						$data['updated_by'] = Auth::getCurrUID();
					}

					$id = $this->kaabar->save('invoices', $data, ['id' => $row_id]);



					if($id){

						$this->_updateInvDetails($id, $type);
						if($row_id == 0){
							$this->kaabar->createKaabarNo('invoices', $id, 'TLTX');
						}

						$sql = "SELECT VJD.id as job_costsheet_id, J.id
							FROM costsheets VJD
							 	LEFT OUTER JOIN jobs J ON VJD.job_id = J.id
							WHERE VJD.job_id = ? AND J.id = ? AND VJD.status = ?";
							$query = $this->db->query($sql, array($job_id, $job_id, 'Pending'));
							$jobStatus = $query->num_rows();

						if($jobStatus == 0)
							$this->kaabar->save('jobs', ['status' => 'Billed'], ['id' => $job_id]);

						$response['success'] = true;
		        		$response['messages'] = 'Saved Successfully';	
					}
					else
					{
						$response['success'] = false;
		        		$response['messages'] = 'Something went wrong';	
					}
				}
			}
			else{
				$response['success'] = false;
	        	$response['messages'] = 'You dont have permission, Contact System administrator';	
			}
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function _updateInvDetails($invoice_id, $type) {
		
		
		$delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');

		//////For EXISTING Entry
		$bill_item_codes = $this->input->post('bill_item_code');
		
		if ($bill_item_codes != null) {
			// 
			$job_item_id	= $this->input->post('job_item_id');
			$sr_no 			= $this->input->post('sr_no');
			$bill_item_id 	= $this->input->post('bill_item_id');
			$hsn_code       = $this->input->post('hsn_code');
			$is_inr       	= $this->input->post('is_inr');
			$currency_id    = $this->input->post('currency');
			$ex_rate        = $this->input->post('ex_rate');
			$currency_amt 	= $this->input->post('currency_amt');
			$rate      		= $this->input->post('rate');
			$inr_rate       = $this->input->post('inr_rate');
			$unit       	= $this->input->post('unit');
			$qty        	= $this->input->post('qty');
			$amount         = $this->input->post('amount');
			$gst        	= $this->input->post('gst');
			$cgst        	= $this->input->post('cgst');
			$sgst 			= $this->input->post('sgst');
			$igst      		= $this->input->post('igst');
			$gst_amount     = $this->input->post('gst_amount');
			$gross_amount   = $this->input->post('gross_amount');

			foreach ($bill_item_codes as $index => $bill_item_code) {
				$data = array(
					//'job_costsheet_id'	=> $job_item_id[$index],
					'bill_item_id'		=> $bill_item_id[$index],
					'sr_no'				=> $sr_no[$index],
					'particulars'		=> $bill_item_code,
					'hsn_code'			=> $hsn_code[$index],
					'is_inr'			=> $is_inr[$index],
					'currency_id'		=> $currency_id[$index],
					'ex_rate'			=> $ex_rate[$index],
					'currency_amt'		=> $currency_amt[$index],
					'inr_rate'			=> $inr_rate[$index],
					'rate'				=> $rate[$index],
					'unit_id'			=> $unit[$index],
					'qty'				=> $qty[$index],
					'amount'			=> $amount[$index],
					'gst'				=> $gst[$index],
					'cgst'				=> $cgst[$index],
					'sgst'				=> $sgst[$index],
					'igst'				=> $igst[$index],
					'gst_amount'		=> $gst_amount[$index],
					'gross_amount'		=> $gross_amount[$index],
				);

				$this->kaabar->save('invoice_details', $data, ['id' => $index]);
			}
		}

		if ($delete_ids != null) {
			$job_item_id	= $this->input->post('job_item_id');
			$invoice_item_id	= $this->input->post('invoice_item_id');
			
			foreach ($delete_ids as $index => $tmp) {
				if(array_key_exists($tmp, $job_item_id) AND array_key_exists($tmp, $invoice_item_id)){
					$this->kaabar->save('costsheets', ['status' => 'Pending', 'invoice_item_id' => NULL], ['id' => $job_item_id[$index]]);
				}

				if(array_key_exists($tmp, $invoice_item_id)){
					$this->kaabar->delete('invoice_details', ['id' => $index]);
				}
			}
		}


		//////For NEW Entry
		$kbr_bill_item_codes = $this->input->post('kbr_bill_item_code');

		if ($kbr_bill_item_codes != null) {
			// 
			foreach ($delete_ids as $del => $delete_id) {
				if(array_key_exists($delete_id, $kbr_bill_item_codes))
					unset($kbr_bill_item_codes[$delete_id]);
			}
			
			$job_item_id	= $this->input->post('kbr_job_item_id');
			$sr_no 			= $this->input->post('kbr_sr_no');
			$bill_item_id 	= $this->input->post('kbr_bill_item_id');
			$hsn_code       = $this->input->post('kbr_hsn_code');
			$is_inr       	= $this->input->post('kbr_is_inr');
			$currency_id    = $this->input->post('kbr_currency');
			$ex_rate        = $this->input->post('kbr_ex_rate');
			$currency_amt 	= $this->input->post('kbr_currency_amt');
			$rate      		= $this->input->post('kbr_rate');
			$inr_rate       = $this->input->post('kbr_inr_rate');
			$unit       	= $this->input->post('kbr_unit');
			$qty        	= $this->input->post('kbr_qty');
			$amount         = $this->input->post('kbr_amount');
			$gst        	= $this->input->post('kbr_gst');
			$cgst        	= $this->input->post('kbr_cgst');
			$sgst 			= $this->input->post('kbr_sgst');
			$igst      		= $this->input->post('kbr_igst');
			$gst_amount     = $this->input->post('kbr_gst_amount');
			$gross_amount   = $this->input->post('kbr_gross_amount');

			foreach ($kbr_bill_item_codes as $index => $bill_item_code) {
				if($bill_item_code AND $sr_no[$index]){
					$data = array(
						'invoice_id'		=> $invoice_id,
						'job_costsheet_id'	=> $job_item_id[$index],
						'bill_item_id'		=> $bill_item_id[$index],
						'sr_no'				=> $sr_no[$index],
						'particulars'		=> $bill_item_code,
						'hsn_code'			=> $hsn_code[$index],
						'is_inr'			=> $is_inr[$index],
						'currency_id'		=> $currency_id[$index],
						'ex_rate'			=> $ex_rate[$index],
						'currency_amt'		=> $currency_amt[$index],
						'inr_rate'			=> $inr_rate[$index],
						'rate'				=> $rate[$index],
						'unit_id'			=> $unit[$index],
						'qty'				=> $qty[$index],
						'amount'			=> $amount[$index],
						'gst'				=> $gst[$index],
						'cgst'				=> $cgst[$index],
						'sgst'				=> $sgst[$index],
						'igst'				=> $igst[$index],
						'gst_amount'		=> $gst_amount[$index],
						'gross_amount'		=> $gross_amount[$index],
					);

					$id = $this->kaabar->save('invoice_details', $data);

					if($job_item_id[$index] > 0){
						$this->kaabar->save('costsheets', ['invoice_item_id' => $id, 'status' => 'Billed'], ['id' => $job_item_id[$index]]);
					}
				}
			}
		}
	}

	function pdf($voucher_book_id = 1, $id = 0, $letterhead = 1, $all_bl_containers = 0) {

		$voucher_book_id = intval($voucher_book_id);
		$id = intval($id);

		$this->load->helper('numwords');

		$default_company = $this->session->userdata('default_company');
		$voucher_book    = $this->kaabar->getRow('voucher_books', $voucher_book_id);

		switch ($voucher_book['voucher_type_id']) {
			case 2: $invoice_type = 'credit_note'; break;
			case 3: $invoice_type = 'debit_note';  break;
			case 4: $invoice_type = 'invoice'; 	   break;
			default:
				echo "Only Credit Note, Debit Note and Invoice can be printed.";
				return;
		}

		$data['invoice_type']    = humanize($invoice_type);
		$data['company']         = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city'] 			 = $data['company']['city_id'];
		$data['state']			 = $data['company']['city_id'];
		$data['voucher_book']    = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$invoice = $this->accounting->getInvoice($id);

			

		if($invoice['ledger_category'] == 'PARTY'){
			$invoice['ledger'] = $this->kaabar->getRow('parties', $invoice['ledger_id'], 'id');

			if(isset($invoice['ledger']['state']))
				$state = $this->kaabar->getRow('states', $invoice['ledger']['state'], 'id');
				if(isset($state))
					$invoice['ledger']['statename'] = $state['name'];

			if(isset($invoice['ledger']['name']))
				$invoice['debit_party_name'] = $invoice['ledger']['name']; 
			


			$address = isset($invoice['ledger']['address']) ? $invoice['ledger']['address'] : '';
			$cityid = isset($invoice['ledger']['city_id']) ? $invoice['ledger']['city_id'] : '';
			$pincode = isset($invoice['ledger']['pincode']) ? $invoice['ledger']['pincode'] : '';
			$statename = isset($state['name']) ? $state['name'] : '';
			$state_gst = isset($state['gst']) ? '( '.$state['gst'].' )' : '';

			$invoice['debit_party_address'] = $address.' '.$cityid.' '.$pincode.' '.$statename.' '.$state_gst;
		}
		elseif($invoice['ledger_category'] == 'AGENT'){
			$invoice['ledger'] = $this->kaabar->getRow('new_agents', $invoice['ledger_id'], 'id');
			$contry = $this->kaabar->getRow('countries', $invoice['ledger']['country'], 'id');
			$invoice['debit_party_name'] = $invoice['ledger']['company_name']; 
			$invoice['debit_party_address'] = $invoice['ledger']['address1'].' '.$invoice['ledger']['address2'].' '.$invoice['ledger']['city'].' '.$contry['name'];
		}
		elseif($invoice['ledger_category'] == 'CONSIGNEE'){
			$invoice['ledger'] = $this->kaabar->getRow('consignees', $invoice['ledger_id'], 'id');
			$contry = $this->kaabar->getRow('countries', $invoice['ledger']['country'], 'id');
			$invoice['debit_party_name'] = $invoice['ledger']['consignee_name']; 
			$invoice['debit_party_address'] = $invoice['ledger']['address1'].' '.$invoice['ledger']['address2'].' '.$invoice['ledger']['city'].' '.$contry['name'];

		}
		elseif($invoice['ledger_category'] == 'VENDOR'){
			$invoice['ledger'] = $this->kaabar->getRow('vendors', $invoice['ledger_id'], 'id');
			$state = $this->kaabar->getRow('states', $invoice['ledger']['state'], 'id');
			$invoice['debit_party_name'] = $invoice['ledger']['name']; 
			$invoice['debit_party_address'] = $invoice['ledger']['address1'].' '.$invoice['ledger']['city_id'].' '.$invoice['ledger']['pincode'].' '.$state['name'].' '.$state['gst'];
		}
		
		$data['voucher'] = $invoice;
		$data['voucher_details'] = $this->accounting->getInvoiceDetails($id);
		
		//$data['currency']        = $this->kaabar->getField('currencies', $data['voucher']['currency_id'], 'id', 'code');
		
		if ($voucher_book['job_type'] != 'N/A' && $data['voucher']['job_id'] > 0) {
			$job = $this->export->getJob($data['voucher']['job_id']);
			
			if($job['billing_party_category'] == 'PARTY')
				$job['party'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'AGENT')
				$job['party'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'company_name');
			///////// Find Shipper Name using Category
			if($job['shipper_category'] == 'PARTY'){

				$job['shipper'] = $this->kaabar->getRow('parties', $job['shipper_id'], 'id');
				
				if(isset($job['shipper']['state']))
					$jstate = $this->kaabar->getRow('states', $job['shipper']['state'], 'id');

				if(isset($job['shipper']['name']))
					$job['shipper_name'] = $job['shipper']['name']; 
				
				$jsaddress = isset($job['shipper']['address']) ? $job['shipper']['address'] : '';
				$jscityid = isset($job['shipper']['city_id']) ? $job['shipper']['city_id'] : '';
				$jspincode = isset($job['shipper']['pincode']) ? $job['shipper']['pincode'] : '';
				$jsstatename = isset($jstate['name']) ? $jstate['name'] : '';
				$jsstate_gst = isset($stjstateate['gst']) ? $jstate['gst'] : '';

				$invoice['debit_party_address'] = $jsaddress.' '.$jscityid.' '.$jspincode.' '.$jsstatename.' '.$jsstate_gst;
				
			}
			elseif($job['shipper_category'] == 'AGENT'){

				$job['shipper'] = $this->kaabar->getRow('new_agents', $job['shipper_id'], 'id');
				$jcontry = $this->kaabar->getRow('countries', $job['shipper']['country'], 'id');
				$job['shipper_name'] = $job['shipper']['company_name']; 
				$job['shipper_address'] = $job['shipper']['address1'].' '.$job['shipper']['address2'].' '.$job['shipper']['city'].' '.$jcontry['name'];
			}
			elseif($job['shipper_category'] == 'CONSIGNEE'){
				$job['shipper'] = $this->kaabar->getRow('consignees', $job['shipper_id'], 'id');
				$jcontry = $this->kaabar->getRow('countries', $job['shipper']['country'], 'id');
				$job['shipper_name'] = $job['shipper']['consignee_name']; 
				$job['shipper_address'] = $job['shipper']['address1'].' '.$job['shipper']['address2'].' '.$job['shipper']['city'].' '.$jcontry['name'];
			}
			elseif($job['shipper_category'] == 'VENDOR'){
				
				$job['shipper'] = $this->kaabar->getRow('vendors', $job['shipper_id'], 'id');
				$jstate = $this->kaabar->getRow('states', $job['shipper']['state'], 'id');
				$job['shipper_name'] = $job['shipper']['name']; 
				$job['shipper_address'] = $job['shipper']['address1'].' '.$job['shipper']['city_id'].' '.$job['shipper']['pincode'].' '.$jstate['name'].' '.$jstate['gst'];
			}


			$data['job'] = $job;
			
			if ($data['job']['type'] == 'Import') {
				$this->load->model('import');
				$data['containers']          = $this->import->getContainerList($data['job']['id']);
				$hss_parties                 = $this->import->getHighSeas($data['job']['id']);
				$data['hss_buyer']           = array_pop($hss_parties);
				$data['import_details']      = $this->kaabar->getRow('import_details', $data['voucher']['job_id'], 'job_id');
				$data['discharge_port']      = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']) . ' - INDIA';
				$data['shipment_port']       = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
				$data['destination_port']    = $this->kaabar->getField('ports', $data['job']['dest_port_id']);
				$data['destination_country'] = $this->kaabar->getField('countries', $data['job']['dest_country_id']);
			}
			else {
				
				$this->load->model('export');

				$containers = $this->kaabar->getRows('containers', $data['voucher']['job_id'], 'job_id');
				$cont_20 = 0;
				$cont_40 = 0;
				if($containers){
					foreach ($containers as $key => $value) {
						$cont_20 += $value['size'] === '20' ? 1 : 0;
						$cont_40 += $value['size'] === '40' ? 1 : 0;
					}
				}
				$data['cont_2040'] = $cont_20.' X 20 | '.$cont_40.' X 40';
				$data['containers'] = $containers;
				// $data['invoice']           = $this->export->getJobInvoices($data['job']['id']);
				// $data['sb_no']             = $this->export->getJobSBs($data['job']['id']);
				// $data['product']           = $this->kaabar->getRow('products', $data['job']['product_id']);
				// $data['loading_port']      = $this->kaabar->getField('indian_ports', $data['job']['loading_port_id']);
				// $data['custom_port']       = $this->kaabar->getField('indian_ports', $data['job']['custom_port_id']);
				// $data['discharge_port']    = $this->kaabar->getRow('ports', $data['job']['discharge_port_id']);
				// $data['discharge_country'] = $this->kaabar->getRow('countries', $data['discharge_port']['country_id']);
			}
			//$data['vessel']       = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);

			if ($invoice_type == 'credit_note')
				$page = $invoice_type;
			else
				$page = strtolower($data['job']['type']) . '_invoice';
			


			$filename = strtoupper($data['company']['code'] . '_' . 
				str_replace('/', '_', $data['voucher']['idkaabar_code']) . '_' . 
				($data['job']['type'] == 'Import' ? $data['job']['bl_no'] : $data['job']['sb_no']));
		}
		else { 
			$dr_ledger_id='';
			if(isset($data['voucher']['dr_ledger_id'])){
				$dr_ledger_id=$data['voucher']['dr_ledger_id'];
			}

			$ledger = $this->kaabar->getRow('ledgers',$dr_ledger_id);

			if ($ledger['party_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('parties', $ledger['party_id']);
			}
			else if ($ledger['agent_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('agents', $ledger['agent_id']);
			}
			else if ($ledger['staff_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('staffs', $ledger['staff_id']);
			}
			
			$id2_format='';
			if(isset($data['voucher']['id2_format'])){
				$id2_format=$data['voucher']['id2_format'];
			}
			$filename = strtoupper($data['company']['code'] . '_' . str_replace('/', '_', $id2_format));

			if ($data['voucher_book']['job_type'] == 'Transportation') {
				$page = 'transportation_invoice';
				$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT J.bl_no SEPARATOR ', ') AS bl_no, 
					GROUP_CONCAT(DISTINCT J.be_no SEPARATOR ', ') AS be_no
				FROM voucher_details VT INNER JOIN trips T ON VT.trip_id = T.id
					INNER JOIN jobs J ON T.job_id = J.id
				WHERE VT.voucher_id = ?
				GROUP BY VT.voucher_id", array($data['voucher']['id']));
				$data['job']          = $query->row_array();
				$data['voucher_details'] = $this->accounting->getVoucherTransportationDetails($data['voucher']['id']);
				$data['debit_ledger'] = $this->kaabar->getRow('ledgers', $data['voucher']['dr_ledger_id']);
			}
			else {
				$page = 'simple_invoice';
			}
		}

		$page = 'nongst_invoice';
		$data['service_taxes'] = $this->accounting->getServiceTaxes();
		$data['page_title']    = $data['voucher_book']['print_name'];
		$data['max_items']     = 13;

		$data['border'] = 0; //'LTRB';


		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		
		//$fontname = TCPDF_FONTS::addTTFfont('times.php', '', '', 96);
		
		//$pdf->setFontSubsetting(false);
		$pdf->SetFont('times', 'BI', 20);
		//$this->setFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(Auth::get('username'));
		$pdf->SetTitle($filename);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetMargins(5, 10, 10, true);
		$pdf->SetAutoPageBreak(TRUE, 10);

		$this->load->model('export_print');

		$this->export_print->$page($pdf, $data, $letterhead);

		$pdf->Output("$filename.pdf", 'I');
	}

	function print($id = 0, $pdf = 0) {

		$id = intval($id);

		$data['invoice'] = $this->kaabar->getRow('invoices', $id);
		$data['invoice_details'] = $this->kaabar->getRows('invoice_details', $data['invoice']['id'], 'invoice_id');
		
		$data['user'] = $this->kaabar->getRow('users', Auth::getCurrUID());
		
		if($data['invoice']['ledger_category'] == "AGENT"){
			$data['party'] = $this->kaabar->getRow('new_agents', ['id' => $data['invoice']['ledger_id']]);
		}elseif($data['invoice']['ledger_category'] == "CONSIGNEE"){
			$data['party'] = $this->kaabar->getRow('consignees', ['id' => $data['invoice']['ledger_id']]);	
		}elseif($data['invoice']['ledger_category'] == "VENDOR"){
			$data['party'] = $this->kaabar->getRow('vendors', ['id' => $data['invoice']['ledger_id']]);	
		}
		elseif($data['invoice']['ledger_category'] == "CUSTOMER"){
			$data['party'] = $this->kaabar->getRow('parties', ['id' => $data['invoice']['ledger_id']]);	
		}

		
		$data['job'] = $this->kaabar->getRow('jobs', ['id' => $data['invoice']['job_id']]);

		$data['container'] = $this->kaabar->getRows('containers', ['job_id' => $data['job']['id']]);
	
		$data['company'] = $this->kaabar->getRow('companies', ['id' => $this->_companyId]);

		// $data['logo'] = $this->kaabar->getImage($this->_folder, $data['company']['id'], $data['company']['logo']);

		$data['max_items'] = $max_items = 20;

		$filename = strtoupper('GST Bill - '.$data['invoice']['idkaabar_code']);
        $data['page_title'] = 'Tax Invoice';

        if($pdf > 0)
        {
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle(strtoupper('GST Bill - '.$data['invoice']['idkaabar_code']));
			$pdf->setPrintHeader(FALSE);
			$pdf->setPrintFooter(TRUE);
			$pdf->SetMargins(5, 5, 5, 5);
			$pdf->SetAutoPageBreak(TRUE, 5);
			$pdf->SetFooterMargin(5);
			$pdf->SetAuthor('Chetan Patel - Connect IT Hub');
			$pdf->SetDisplayMode('real', 'default');
	        
			// Pass Data
			$pdf->page_title = $data['page_title'];
			$pdf->user = $data['user'];
			$pdf->company = $data['company'];
			// $pdf->logo = $data['logo'];
			$pdf->invoice = $data['invoice'];
			$pdf->invoice_details = $data['invoice_details'];
			$pdf->job = $data['job'];
			$pdf->container = $data['container'];
			$pdf->party = $data['party'];
		    $pdf->AddPage('P', 'A4');
			$page = strtolower('export_invoice');
			$this->load->model('Gst_invoice');

			$this->Gst_invoice->$page($pdf, $data, $letterhead = null);
			$file = preg_replace('/[^a-zA-Z0-9_.]/', '_', $filename.'.pdf');
			$pdf->Output($file, 'D');
	
        }
        else
        {
        	
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle(strtoupper('GST Bill - '.$data['invoice']['idkaabar_code']));
			$pdf->setPrintHeader(FALSE);
			$pdf->setPrintFooter(TRUE);
			$pdf->SetMargins(5, 5, 5, 5);
			$pdf->SetAutoPageBreak(TRUE, 5);
			$pdf->SetFooterMargin(5);
			$pdf->SetAuthor('Chetan Patel - Connect IT Hub');
			$pdf->SetDisplayMode('real', 'default');
	        
			// Pass Data
			$pdf->page_title = $data['page_title'];
			$pdf->user = $data['user'];
			$pdf->company = $data['company'];
			// $pdf->logo = $data['logo'];
			$pdf->invoice = $data['invoice'];
			$pdf->invoice_details = $data['invoice_details'];
			$pdf->job = $data['job'];
			$pdf->container = $data['container'];
			$pdf->party = $data['party'];
	        $pdf->AddPage('P', 'A4');
			$page = strtolower('invoice');

			$this->load->model('export_print');

			$this->export_print->$page($pdf, $data, $letterhead = null);
			$file = preg_replace('/[^a-zA-Z0-9_.]/', '_', $filename.'.pdf');
			$pdf->Output($file, 'I');
		}
    }

	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){

				$this->db->where(array('id' => $id));
				$result = $this->db->get('invoices')->num_rows();
				$invoice = $this->db->get('invoices')->row_array();
				
				if($result == 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'GST Billing not Found';			
				}
				else
				{
					$einv = $this->kaabar->getRow('einvoices', ['voucher_id' => $row_id]);

					if($einv)
					{
						$response['success'] = false;
			        	$response['messages'] = 'eInvoice Generated, You can not update Invoice...!';	
					}
					else
					{
						$job = $this->kaabar->getRow('jobs', ['id' => $invoice['job_id']]);

						if($invoice['job_id'] AND $job){
							$this->kaabar->save('jobs', ['status' => 'Pending'], ['id' => $job['id']]);
							$this->kaabar->save('costsheets', ['invoice_item_id' => 0, 'status' => 'Pending'], ['job_id' => $job['id']]);	
						}
						$this->db->delete('invoices', ['id' => $id]);
						$this->db->delete('invoice_details', ['invoice_id' => $id]);
						
						$response['status'] = 'success';
						$response['msg'] = 'Successfully Deleted GST Billing';	
					}
				}
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select GST Billing First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function jobsList() {
		$json = [];
		if ($this->input->is_ajax_request()) {
			if(!empty($this->input->get("searchTerm"))){
				$this->db->like('idkaabar_code', $this->input->get("searchTerm"));
			}
			$query = $this->db->select('id,idkaabar_code as text')
							->where('type', 'Export')
							->where('status', 'Pending')
							->limit(10)
							->get('jobs');
			$json = $query->result_array();
			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

    function invoiceList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("searchTerm"))){
				$this->db->like('idkaabar_code', $this->input->get("searchTerm"));
			}
			
			$query = $this->db->select('id,idkaabar_code as text')
							->where('vType', 'Export')
							->where('invoice_type', 'ExportGst')
							->limit(10)
							->order_by('id', 'DESC')
							->get('invoices');
			$json = $query->result_array();

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

    function delDetails($table, $job_id, $id) {
    	$unlink = '';
		$json = [];
		if ($this->input->is_ajax_request()) {

			if($custID AND $id){

				if($table == 'attachments'){
					$docdir = $this->office->getDocFolder($this->_path, $custID);
					$file = $this->kaabar->getField($table, $id, 'id', 'name');
					$unlink = $this->_path.$docdir.$file;
				}
				if($this->db->delete($table, ['id' => $id])){
					if($unlink) unlink($unlink);
					$json = ['success' => true, 'messages' => 'Successfully Deleted'];
				}
				else
					$json = ['success' => false, 'messages' => 'Something Wrong on Database, Try Again'];	
			}
			else
				$json = ['success' => false, 'messages' => 'Something Wrong, Try Again'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

    function einvoice() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			$voucher_id = $this->input->post("voucher_id");
			
			if($voucher_id){

				if(Settings::getEwbCredential() == FALSE){
					$json['success'] = false;
					$json['messages'] = Settings::get('eway_default_source').' API Disabled, Please try to contact Administrator';
				}
				else
				{

					$voucher = $this->accounting->getInvoice($voucher_id);
					$company = $this->kaabar->getRow('companies', $this->_default_company['id']);
					
					if($voucher['ledger_category'] == 'PARTY')
						$voucher['ledger'] = $this->kaabar->getRow('parties', $voucher['ledger_id']);
					elseif($voucher['ledger_category'] == 'AGENT')
						$voucher['ledger'] = $this->kaabar->getRow('new_agents', $voucher['ledger_id']);
					elseif($voucher['ledger_category'] == 'CONSIGNEE')
						$voucher['ledger'] = $this->kaabar->getRow('consignees', $voucher['ledger_id']);
					elseif($voucher['ledger_category'] == 'VENDOR')
						$voucher['ledger'] = $this->kaabar->getRow('vendors', $voucher['ledger_id']);

					$buyer_state = $this->kaabar->getRow('states', $voucher['ledger']['state']);

					$items = $this->accounting->getInvoiceDetails($voucher_id);
					$i = 1;
					foreach ($items as $key => $item) {

						$gross_amount = ($item['amount']+$item['igst']+$item['cgst']+$item['sgst']);

						$vi = [
							'SlNo' => "$i",
							'IsServc' => "Y",
							'HsnCd' => $item['hsn_code'],
							'Qty'=> intval(number_format((float)$item['qty'], 2, '.', '')),
							'UnitPrice' => intval(number_format((float)$item['inr_rate'], 2, '.', '')),
							'TotAmt' => intval(number_format((float)$item['amount'], 2, '.', '')),
							'AssAmt' => intval(number_format((float)$item['amount'], 2, '.', '')),
							'GstRt' => intval(number_format((float)$item['gst'], 2, '.', '')),
							'IgstAmt' => intval(number_format((float)$item['igst'], 2, '.', '')), 
							'CgstAmt' => intval(number_format((float)$item['sgst'], 2, '.', '')), 
							'SgstAmt' => intval(number_format((float)$item['cgst'], 2, '.', '')), 
							'TotItemVal' => intval(number_format((float)$gross_amount, 2, '.', '')),
						];

						$AssVal += $item['amount'];
						$CgstVal += $item['cgst'];
						$SgstVal += $item['sgst'];
						$IgstVal += $item['igst'];
						$TotInvVal += $gross_amount;

						$voucher_items[] = $vi;
						$i++; 
					}

					

					$einv = [
						'Version' 		=> '1.1',
						'TranDtls'		=> [
							'TaxSch'=> 'GST',
							'SupTyp'=> 'B2B',
						],
						'DocDtls'		=> [
							'Typ'=> 'INV',
							'No'=> $voucher['idkaabar_code'],
							'Dt'=> date('d/m/Y', strtotime($voucher['date']))
						],
						'SellerDtls'	=> [
								'Gstin'		=> $company['gst_no'],
								'LglNm'		=> $company['name'],
								'TrdNm'		=> $company['name'],
								'Addr1'		=> $company['address'],
								'Loc'		=> $company['city_id'],
								'Pin'		=> intval($company['pincode']),
								'Stcd'		=> substr($company['gst_no'],0,2),
						],
						'BuyerDtls'		=> [
							'Gstin'			=> $voucher['ledger']['gst_no'],
							'LglNm'			=> $voucher['ledger']['name'],
							'TrdNm'			=> $voucher['ledger']['name'],
							'Pos'			=> $buyer_state['gst'],
							'Addr1'			=> $voucher['ledger']['address1'].' '.$voucher['ledger']['address2'],
							'Loc'			=> $voucher['ledger']['city'],
							'Pin'			=> intval($voucher['ledger']['pincode']),
							'Stcd'			=> $buyer_state['gst'],
						],
						'ItemList'		=> $voucher_items,
						'ValDtls'		=> [
							'AssVal'=> intval($AssVal),
							'CgstVal'=> intval($CgstVal),
							'SgstVal'=> intval($SgstVal),
							'IgstVal'=> intval($IgstVal),
							'RndOffAmt'=> intval(round(($TotInvVal - round($TotInvVal, 0)), 2)),
							'TotInvVal'=> intval(round(round($TotInvVal, 0)), 2),
						],
					];

					/*echo '<pre>';
					print_r($einv);*/
					//echo '<pre>' . json_encode($einv, JSON_NUMERIC_CHECK) . '</pre>';
					//exit;

					if($einv)
					{
						$this->load->library('einvoice');
					    $response = Einvoice::generate($einv, 'Invoice?');
					    $result = $response['result'];

					    if(array_search('200', $response) AND $result['Status'] == 1){

					    	$resultdata = json_decode($result['Data']);
					    	$einvdata = [
					    		'voucher_id' 	=> $voucher_id,
					    		'ack_no'		=> $resultdata->AckNo,
					    		'ack_date'		=> $resultdata->AckDt,
					    		'irn_no'		=> $resultdata->Irn,
					    		'signed_invoice'=> $resultdata->SignedInvoice,
					    		'signed_qr_code'=> $resultdata->SignedQRCode,
					    		'status'		=> $resultdata->Status,
					    		'ewbNo'		 	=> $resultdata->EwbNo,
					    		'ewbDate'		=> $resultdata->EwbDt,
					    		'ewbValidTill'	=> $resultdata->EwbValidTill,
					    		'ExtractedSignedInvoiceData'=> json_encode($resultdata->ExtractedSignedInvoiceData),
					    		'ExtractedSignedQrCode'		=> json_encode($resultdata->ExtractedSignedQrCode),
					    		'QrCodeImage'	=> $resultdata->QrCodeImage,
					    		'JwtIssuer'	=> $resultdata->JwtIssuer,
					    	];

					    	$this->kaabar->save('einvoices', $einvdata);

					    	$json['success'] = true;
							$json['messages'] = 'eInvoice Generated Successfully';
						}
						else
						{
							if($result['ErrorDetails']){
								foreach ($result['ErrorDetails'] as $k => $v) {
									$errorMsg[] = '<i class="feather icon-angle-double-right"></i> '. $v['ErrorCode'].' - '.$v['ErrorMessage'];
								}
								if(!empty($errorMsg))
								{
									$json['success'] = false;
									$json['messages'] = $errorMsg;
								}
								else
								{
									$json['success'] = false;
									$json['messages'] = 'Something Wrong Please try again';	
								}
							}
							else
							{
								$json['success'] = false;
								$json['messages'] = 'Something Wrong Please try again';	
							}
						}

					}
				}
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Something Wrong, Please try to contact Administrator';
			}
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

    function caneinvoice() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			$voucher_id = $this->input->post("voucher_id");
			$irn_no = $this->input->post("irn_no");
			$cancel_rsn = $this->input->post("cancel_rsn");
			$cancel_remark = $this->input->post("cancel_remark");
			
			if($voucher_id AND $irn_no AND $cancel_rsn){

				if(Settings::getEwbCredential() == FALSE){
					$json['success'] = false;
					$json['messages'] = Settings::get('eway_default_source').' API Disabled, Please try to contact Administrator';
				}
				else
				{
					$einvoices = $this->kaabar->getRow('einvoices', ['voucher_id' => $voucher_id, 'irn_no' => trim($irn_no)]);
					$einv = [
						'Irn' => $irn_no,
						'CnlRsn' => $cancel_rsn,
						'CnlRem' => $cancel_remark,
					];

					// echo '<pre>';
					// print_r($einv);
					// //echo '<pre>' . json_encode($einv, JSON_NUMERIC_CHECK) . '</pre>';
					// exit;

					if($einv)
					{
						$this->load->library('einvoice');
					    $response = Einvoice::generate($einv, 'Invoice/Cancel?');
					    $result = $response['result'];

					    if(array_search('200', $response) AND $result['Status'] == 1){

					    	$resultdata = json_decode($result['Data']);
					    	
					    	$caneinvdata = [
					    		'cancel_date'	=> $resultdata->CancelDate,
					    		'cancel_rsn'	=> $cancel_remark,
					    		'status'		=> 'CNL',
					    	];

					    	$this->kaabar->save('einvoices', $caneinvdata, ['id' => $einvoices['id']]);
					    	$json['success'] = true;
							$json['messages'] = 'eInvoice Canceled Successfully';	
					    	
					    	
						}
						else
						{
							
							if($result['ErrorDetails']){
								foreach ($result['ErrorDetails'] as $k => $v) {
									$errorMsg[] = '<i class="feather icon-angle-double-right"></i> '. $v['ErrorCode'].' - '.$v['ErrorMessage'];
								}

								if(!empty($errorMsg))
								{
									$json['success'] = false;
									$json['messages'] = $errorMsg;
								}
								else
								{
									$json['success'] = false;
									$json['messages'] = 'Something Wrong Please try again';	
								}
							}
							else
							{
								$json['success'] = false;
								$json['messages'] = 'Something Wrong Please try again';	
							}
						}

					}
				}
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Something Wrong, Please try to contact Administrator';
			}
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }
}