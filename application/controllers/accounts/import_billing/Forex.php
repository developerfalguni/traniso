<?php

use mikehaertl\wkhtmlto\Pdf;

class Forex extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('import');
		$this->_companyId = $this->import->getCompanyID();
		$this->invoiceType = 'ImportForex';
	}

	function index($type = 'Import') {
		redirect($this->_clspath.$this->_class.'/edit/'.$type);
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


			foreach ($rows as $key => $value) {
				

				$currency_name = $this->kaabar->getRow('currencies', $value['currency_id']);
				$rows[$key]['currency_name'] = $currency_name['code'].' - '.$currency_name['name'];

				$is_inr =  form_dropdown('is_inr['.$value['id'].']', array('Yes'=>'Yes', 'No'=>'No'), $value['is_inr'], 'class="form-control form-control-sm Unchanged IsINR d-none"');

				$unit =  form_dropdown('unit['.$value['id'].']', getSelectOptions('units'), $value['unit_id'], 'class="Unchanged form-control form-control-sm Unit"');
						
				unset($rows[$key]['is_inr']);
				$rows[$key]['is_inr'] = $is_inr;

				unset($rows[$key]['unit_id']);
				$rows[$key]['unit_id'] = $unit;

				$deleteBtn = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));

				$rows[$key]['delete_btn'] = $deleteBtn;
			}

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
		WHERE VJD.job_id = ?
		ORDER BY BI.category, VJD.sr_no, BI.stax_category_id, VJD.particulars";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function getJob($job_id = 0) {

		$response = [];
		
		if($job_id){

			//// Get Job Details
			$job = $this->import->getJob($job_id);
			if($job['billing_party_category'] == 'PARTY')
				$job['billing_party_name'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'AGENT')
				$job['billing_party_name'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'CONSIGNEE')
				$job['billing_party_name'] = $this->kaabar->getField('consignees', $job['billing_party_id'], 'id', 'name');
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

	function edit_old($type = 'Import' , $id = 0) {

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('ledger_id', 'Party Name', 'trim|required');
		
		$row = $this->kaabar->getRow('invoices', array('id' => $id, 'vtype' => $type));
		if($row == false) {
			$row = array(
				'id'            		=> 0,
				'idkaabar'				=> 0,
				'vType'					=> $type,
				'ledger_id'				=> '',
				'date'					=> '',
				'total_gst'				=> '0.00',
				'additional_charge'		=> '0.00',
				'roundoff'				=> '0.00',
				'net_amount'			=> '0.00'
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['type'] = $type;
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
	
			$data['invoice_details'] = $this->kaabar->getRows('invoice_details', ['invoice_id ' => $id]);
			$data['party_name']     = $this->kaabar->getField('parties', $row['ledger_id']);
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				
				$data = array(
					'vType'     		=> $type,
					'ledger_id'     	=> $this->input->post('ledger_id'),
					'date'     			=> $this->input->post('date'),
					'additional_charge' => $this->input->post('additional_charge'),
					'roundoff'     		=> $this->input->post('roundoff'),
					'net_amount'     	=> $this->input->post('net_amount')
				);

				if($id == 0){
					$data['created_by'] = Auth::getCurrUID();
					$data['updated_by'] = Auth::getCurrUID();
					$data['company_id'] = $this->kaabar->getCompanyID();
					
				}else{
					$data['updated_by'] = Auth::getCurrUID();
				}

				$id = $this->kaabar->save('invoices', $data, ['id' => $id]);
				$this->_updateIvcDetails($id, 0, $type);

				if($row['idkaabar'] == 0)
					$this->kaabar->createKaabarNo('invoices', $id, 'INV/IMP');

				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$type/$id");
		}
	}

	function edit($type = 'Import') {

		$data['id'] = array('id' => 0);
		$data['type'] = $type;
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function ajaxEdit($type = 'Import') {

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
			print_r($row_id);
			print_r($job_id);exit;
			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				
				$data = array(
					'job_id'			=> $job_id,
					'vType'     		=> $type,
					'ledger_id'     	=> $this->input->post('billingParty_id'),
					'ledger_category'   => $this->input->post('billingParty_category'),
					'date'     			=> $this->input->post('date'),
					'additional_charge' => $this->input->post('additional_charge'),
					'roundoff'     		=> $this->input->post('roundoff'),
					'net_amount'     	=> $this->input->post('net_amount')
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

					$this->_updateIvcDetails($id, $type);
					if($row_id == 0){
						$this->kaabar->createKaabarNo('invoices', $id, 'INV/IMP');
						if($job_id > 0)
							$this->kaabar->save('jobs', ['status' => 'Completed'], ['id' => $job_id]);
					}

					$response['success'] = true;
	        		$response['messages'] = 'Saved Successfully';	
				}
				else
				{
					$response['success'] = false;
	        		$response['messages'] = 'Something went wrong';	
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

	function _updateIvcDetails($invoice_id, $type) {
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
			$rate      		= $this->input->post('rate');
			$unit       	= $this->input->post('unit');
			$qty        	= $this->input->post('qty');
			$amount         = $this->input->post('amount');
			
			foreach ($bill_item_codes as $index => $bill_item_code) {
				$data = array(
					///'job_costsheet_id'	=> $job_item_id[$index],
					'bill_item_id'		=> $bill_item_id[$index],
					'sr_no'				=> $sr_no[$index],
					'particulars'		=> $bill_item_code,
					'hsn_code'			=> $hsn_code[$index],
					'is_inr'			=> $is_inr[$index],
					'currency_id'		=> $currency_id[$index],
					'rate'				=> $rate[$index],
					'unit_id'			=> $unit[$index],
					'qty'				=> $qty[$index],
					'amount'			=> $amount[$index]
				);

				$this->kaabar->save('invoice_details', $data, ['id' => $index]);
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				$this->kaabar->delete('invoice_details', ['id' => $index]);
			}
		}
		//////For NEW Entry
		$kbr_bill_item_codes = $this->input->post('kbr_bill_item_code');

		if ($kbr_bill_item_codes != null) {
			// 
			$job_item_id	= $this->input->post('kbr_job_item_id');
			$sr_no 			= $this->input->post('kbr_sr_no');
			$bill_item_id 	= $this->input->post('kbr_bill_item_id');
			$hsn_code       = $this->input->post('kbr_hsn_code');
			$is_inr       	= $this->input->post('kbr_is_inr');
			$currency_id    = $this->input->post('kbr_currency');
			$rate      		= $this->input->post('kbr_rate');
			$inr_rate       = $this->input->post('kbr_inr_rate');
			$unit       	= $this->input->post('kbr_unit');
			$qty        	= $this->input->post('kbr_qty');
			$amount         = $this->input->post('kbr_amount');
			
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
						'rate'				=> $rate[$index],
						'unit_id'			=> $unit[$index],
						'qty'				=> $qty[$index],
						'amount'			=> $amount[$index]
					);
					$id = $this->kaabar->save('invoice_details', $data);
					$this->kaabar->save('costsheets', ['invoice_item_id' => $id], ['id' => $job_item_id[$index]]);
				}
			}
		}
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

		$data['max_items'] = $max_items = 20;

		$filename = strtoupper('Forex Bill - '.$data['invoice']['idkaabar_code']);
        $data['page_title'] = 'Tax Invoice';

        if($pdf > 0)
        {
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle(strtoupper('Forex Bill - '.$data['invoice']['idkaabar_code']));
			$pdf->setPrintHeader(true);
			$pdf->setPrintFooter(true);
			$pdf->SetMargins(5, 38, 5, 5);
			$pdf->SetAutoPageBreak(TRUE, 12);
			$pdf->SetFooterMargin(12);
			$pdf->SetAuthor('Chetan Patel - Connect IT Hub');
			$pdf->SetDisplayMode('real', 'default');

			// Pass Data
			$pdf->page_title = $data['page_title'];
			$pdf->invoiceCopy = $data['invoiceCopy'];

			$logo = $this->kaabar->getField('companies', ['id' => $this->_companyId], 'id', 'logo');
			$logodir = $this->kaabar->getDocFolder($this->_path, $this->_companyId);
			$pdf->image_file = FCPATH.'images/'.$logodir.$logo;

			$pdf->company = $data['company'];
			$pdf->container = $data['container'];
			$pdf->order_terms = $data['quotation'];
			$pdf->input_assets = $data['input_assets'];
			$pdf->og_assets = $data['og_assets'];
			$pdf->label_assets = $data['label_assets'];

			$pdf->AddPage('P', 'A4');

			$page = strtolower('export_invoice');
			$this->load->model('Gst_invoice');
			$this->Gst_invoice->$page($pdf, $data, $letterhead);

			$docdir = $this->kaabar->getDocFolder($this->_path, $id);
	        $filename = preg_replace('/[^a-zA-Z0-9_.]/', '_', $filename.'.pdf');
	        $filepath = $this->_path.$docdir.$filename;
	        $pdf->Output($filepath, 'F');
	        return $filename;
        }
        else
        {
        	
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle(strtoupper('Forex Bill - '.$data['invoice']['idkaabar_code']));
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
			$pdf->Output($file, 'I');
		}
    }


	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){
				$this->db->where(array('id' => $id));
				$result = $this->db->get('invoices')->num_rows();

				if($result = 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'Forex Billing not Found';			
				}
				else
				{
					$this->db->delete('invoices', ['id' => $id]);
					$this->db->delete('invoice_details', ['invoice_id' => $id]);
						
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted Forex Billing';			
				}
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Forex Billing First then delete';		
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

			if(!empty($this->input->get("q"))){
				$this->db->like('idkaabar_code', $this->input->get("q"));
			}
			
			$query = $this->db->select('id,idkaabar_code as text')
							->where('type', 'Import')
							->where('status', 'Bills')
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

			if(!empty($this->input->get("q"))){
				$this->db->like('idkaabar_code', $this->input->get("q"));
			}
			
			$query = $this->db->select('id,idkaabar_code as text')
							->where('vType', 'Import')
							->limit(10)
							->where('invoice_type', 'ImportForex')
							->limit(10)
							->get('invoices');
			$json = $query->result_array();

			$json[] = ['id' => 0, 'text' => 'New Invoice'];
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }
}