<?php

use mikehaertl\wkhtmlto\Pdf;

class Import_quote extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('export');
		$this->_companyId = $this->export->getCompanyID();
		$this->_type = 'Import';
	}

	function index($type = 'Import') {
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getQuote($quote_id = 0){

		$response = [];
		
		if($quote_id > 0){

			$row = $this->kaabar->getRow('quotations', $quote_id);
			$quo_details = $this->kaabar->getRows('quotation_details', ['quotation_id' => $quote_id]);

			$response['success'] = true;
			$response['row'] = $row;
			$response['quo_details'] = $quo_details;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select Quotation';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function old_edit($type = 'Import' , $id = 0) {

		$vchType = ($type == 'Import' ? "EXP" : "IMP");

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('quote_to', 'From', 'trim|required');
		$this->form_validation->set_rules('ref_num', 'Reference Number', 'required');
		$row = $this->kaabar->getRow('quotations', array('id' => $id, 'type' => $type));
		if($row == false) {
			$row = array(
				'id'            		=> 0,
				'idkaabar'				=> 0,
				'type'					=> $type,
				'quote_to'					=> '',
				'ref_num'				=> '',
				'address'				=> '',
				'date'					=> '',
				'quote_by'				=> '',
				'phone'					=> '',
				'pic'					=> '',
				'quotation_validity'	=> '',
				'pol'					=> '',
				'shipping_line'			=> '',
				'pod'					=> '',
				'transite_time'			=> '',
				'final_destination'		=> '',
				'vessel_schedule'		=> '',
				'toc'					=> '',
				'pick_up'				=> '',
				'tos'					=> '',
				'weight_unit'			=> '',
				'cargo_details'			=> '',
				'other'					=> '',
				'tnc_1'					=> 'If one container is having multiple shipping bill or more than one shipping bill , there will be additional charges of INR 500 per Shipping bill. ',
				'tnc_2'					=> 'Examination Charges : INR 2800 + GST for Mundra and INR 2000 + GST for Hazira will be non receipted conveyance charges and CFS \ Yard \ Line Chagres at actual as per receipt. ',
				'tnc_3'					=> 'Payment Against within 15 days of Bills Receipt (Cosnidering E-Invoices Also)',
				'tnc_4'					=> 'Quotation and Terms will be considered as accepted if work is started. ',
				'tnc_5'					=> 'Insurance of Consignment is to be arrange by yourself for cargo and container both. ',
				'tnc_6'					=> 'If any agency (CFS, Fumigation etc. ) will revise the tariff will be pre inform and quoation will be change.',
				'tnc_7'					=> 'Shipping bill amendment charges Rs 500 (Subject to POD , weight amendment) , Cancelation and Re-filling.',
				'tnc_8'					=> 'Back to town of cargo will be case to case basis. and Shipping bill cancellation charges Rs. 1500',
				'tnc_9'					=> 'Subject to Gandidham juridication only.',
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['type'] = $type;
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
	
			$data['quo_details'] = $this->kaabar->getRows('quotation_details', ['quotation_id' => $id, 'type' => $type]);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'/'.strtolower($type);
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				
				$data = array(
					'type'     			=> $type,
					'quote_to'     			=> $this->input->post('quote_to'),
					'ref_num'     		=> $this->input->post('ref_num'),
					'address'     		=> $this->input->post('address'),
					'date'     			=> $this->input->post('date'),
					'quote_by'     		=> $this->input->post('quote_by'),
					'phone'     		=> $this->input->post('phone'),
					'pic'    			=> $this->input->post('pic'),
					'quotation_validity'=> $this->input->post('quotation_validity'),
					'pol'     			=> $this->input->post('pol'),
					'shipping_line'     => $this->input->post('shipping_line'),
					'pod'     			=> $this->input->post('pod'),
					'transite_time'     => $this->input->post('transite_time'),
					'final_destination' => $this->input->post('final_destination'),
					'vessel_schedule'   => $this->input->post('vessel_schedule'),
					'toc'     			=> $this->input->post('toc'),
					'pick_up'     		=> $this->input->post('pick_up'),
					'tos'     			=> $this->input->post('tos'),
					'weight_unit'     	=> $this->input->post('weight_unit'),
					'cargo_details'     => $this->input->post('cargo_details'),
					'other'     		=> $this->input->post('other'),
					'tnc_1'     		=> $this->input->post('tnc_1'),
					'tnc_2'     		=> $this->input->post('tnc_2'),
					'tnc_3'     		=> $this->input->post('tnc_3'),
					'tnc_4'     		=> $this->input->post('tnc_4'),
					'tnc_5'     		=> $this->input->post('tnc_5'),
					'tnc_6'     		=> $this->input->post('tnc_6'),
					'tnc_7'     		=> $this->input->post('tnc_7'),
					'tnc_8'     		=> $this->input->post('tnc_8'),
					'tnc_9'     		=> $this->input->post('tnc_9'),

				);
				if($id == 0){
					$data['created_by'] = Auth::getCurrUID();
					$data['updated_by'] = Auth::getCurrUID();
					$data['company_id'] = $this->kaabar->getCompanyID();
					
				}else{
					$data['updated_by'] = Auth::getCurrUID();
				}

				$id = $this->kaabar->save('quotations', $data, ['id' => $id]);
				$this->_updateQuoDetails($id, 0, $type);

				if($row['idkaabar'] == 0)
					$this->kaabar->createKaabarNo('quotations', $id, 'QUOTE/'.$vchType);

				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$type/$id");
		}
	}

	function edit($id = 0) {

		$data['id'] = array('id' => 0);
		$data['type'] = "Import";
		$data['tnc_1']					= 'If one container is having multiple shipping bill or more than one shipping bill , there will be additional charges of INR 500 per Shipping bill. ';
		$data['tnc_2']					= 'Examination Charges : INR 2800 + GST for Mundra and INR 2000 + GST for Hazira will be non receipted conveyance charges and CFS \ Yard \ Line Chagres at actual as per receipt. ';
		$data['tnc_3']					= 'Payment Against within 15 days of Bills Receipt (Cosnidering E-Invoices Also).';
		$data['tnc_4']					= 'Quotation and Terms will be considered as accepted if work is started. ';
		$data['tnc_5']				= 'Insurance of Consignment is to be arrange by yourself for cargo and container both. ';
		$data['tnc_6']					= 'If any agency (CFS, Fumigation etc. ) will revise the tariff will be pre inform and quoation will be change.';
		$data['tnc_7']					= 'Shipping bill amendment charges Rs 500 (Subject to POD , weight amendment) , Cancelation and Re-filling.';
		$data['tnc_8']					= 'Back to town of cargo will be case to case basis. and Shipping bill cancellation charges Rs. 1500';
		$data['tnc_9']				= 'Subject to Gandidham juridication only.';
		$data['quo_details'] = $this->kaabar->getRows('quotation_details', ['quotation_id' => $id, 'type' => $data['type']]);
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}
	function ajaxEdit($type = 'Import') {

		$response = [];
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('quote_to', 'From', 'trim|required');
		$this->form_validation->set_rules('ref_num', 'Reference Number', 'required');
		$this->form_validation->set_rules('date', 'Date', 'required');
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

				$vchType = ($type == 'Export' ? "EXP" : "IMP");

				$data = array(
					'type'     			=> $type,
					'quote_to'     			=> $this->input->post('quote_to'),
					'ref_num'     		=> $this->input->post('ref_num'),
					'address'     		=> $this->input->post('address'),
					'date'     			=> $this->input->post('date'),
					'quote_by'     		=> $this->input->post('quote_by'),
					'phone'     		=> $this->input->post('phone'),
					'pic'    			=> $this->input->post('pic'),
					'quotation_validity'=> $this->input->post('quotation_validity'),
					'pol'     			=> $this->input->post('pol'),
					'shipping_line'     => $this->input->post('shipping_line'),
					'pod'     			=> $this->input->post('pod'),
					'transite_time'     => $this->input->post('transite_time'),
					'final_destination' => $this->input->post('final_destination'),
					'vessel_schedule'   => $this->input->post('vessel_schedule'),
					'toc'     			=> $this->input->post('toc'),
					'pick_up'     		=> $this->input->post('pick_up'),
					'tos'     			=> $this->input->post('tos'),
					'weight_unit'     	=> $this->input->post('weight_unit'),
					'cargo_details'     => $this->input->post('cargo_details'),
					'other'     		=> $this->input->post('other'),
					'tnc_1'     		=> $this->input->post('tnc_1'),
					'tnc_2'     		=> $this->input->post('tnc_2'),
					'tnc_3'     		=> $this->input->post('tnc_3'),
					'tnc_4'     		=> $this->input->post('tnc_4'),
					'tnc_5'     		=> $this->input->post('tnc_5'),
					'tnc_6'     		=> $this->input->post('tnc_6'),
					'tnc_7'     		=> $this->input->post('tnc_7'),
					'tnc_8'     		=> $this->input->post('tnc_8'),
					'tnc_9'     		=> $this->input->post('tnc_9'),
					'company_id'     	=> $this->input->post('company_id'),//new change company id
				);
				
				$id = $this->kaabar->save('quotations', $data, ['id' => $row_id]);
				
				$this->_updateQuoDetails($id, 0, $type);

				if($row_id == 0){
					$this->kaabar->createQuotationNo('quotations', $id, 'QUOTE/'.$vchType, $type);
				}
				
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

	function _updateQuoDetails($quotation_id, $id, $type) {
		$delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		$charges_descriptions = $this->input->post('charges_description');
		if ($charges_descriptions != null) {
			// $sr_no = $this->input->post('sr_no');
			$currency = $this->input->post('currency');
			$amount      = $this->input->post('amount');
			$base_on        = $this->input->post('base_on');
			$taxable        = $this->input->post('taxable');

			foreach ($charges_descriptions as $index => $charges_description) {
				if (strlen(trim($currency[$index])) > 0 OR strlen(trim($base_on[$index])) > 0) {
					$data = array(
						'charges_description' => $charges_description,
						// 'sr_no' => $sr_no[$index],
						'currency' => $currency[$index],
						'amount'      => $amount[$index],
						'base_on'       => $base_on[$index],
						'taxable'       => $taxable[$index],
						'type'			=> $type,
					);
					$this->kaabar->save('quotation_details', $data, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				$this->kaabar->delete('quotation_details', ['id' => $index]);
			}
		}

		$new_charges_descriptions = $this->input->post('new_charges_description');
		if($new_charges_descriptions != null) {
			$currency = $this->input->post('new_currency');
			// $sr_no = $this->input->post('new_sr_no');
			$amount      = $this->input->post('new_amount');
			$base_on        = $this->input->post('new_base_on');
			$taxable        = $this->input->post('new_taxable');

			foreach ($new_charges_descriptions as $index => $charges_description) {
				if (strlen(trim($currency[$index])) > 0 OR strlen(trim($base_on[$index])) > 0) {
					$data = array(
						'quotation_id'  => $quotation_id,
						'charges_description' => $charges_description,
						// 'sr_no' => $sr_no[$index],
						'currency' => $currency[$index],
						'amount'      => $amount[$index],
						'base_on'       => $base_on[$index],
						'taxable'       => $taxable[$index],
						'type'			=> $type,
					);
					$this->kaabar->save('quotation_details', $data);
				}
			}
		}
	}

	function print($id = 0, $pdf = 0) {

		$id = intval($id);

		$data['quotation'] = $this->kaabar->getRow('quotations', $id);
		$data['quotation_detail'] = $this->kaabar->getRows('quotation_details', $data['quotation']['id'], 'quotation_id');
		$data['company'] = $this->kaabar->getRow('companies', ['id' => $this->_companyId]);

		$data['max_items'] = $max_items = 20;

		$filename = strtoupper('Quotation - '.$data['quotation']['idkaabar_code']);
        $data['page_title'] = 'QUATATION';

        if($pdf > 0)
        {
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle(strtoupper('Quotation - '.$data['quotation']['idkaabar_code']));
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
			$pdf->input_assets = $data['input_assets'];
			$pdf->og_assets = $data['og_assets'];
			$pdf->label_assets = $data['label_assets'];

			$pdf->AddPage('P', 'A4');

			$page = strtolower('export_quote');
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
			$pdf->SetTitle(strtoupper('Quotation - '.$data['quotation']['idkaabar_code']));
			$pdf->setPrintHeader(FALSE);
			$pdf->setPrintFooter(TRUE);
			$pdf->SetMargins(5, 5, 5, 5);
			$pdf->SetAutoPageBreak(TRUE, 5);
			$pdf->SetFooterMargin(5);
			$pdf->SetAuthor('Chetan Patel - Connect IT Hub');
			$pdf->SetDisplayMode('real', 'default');
	        
			// Pass Data
			$pdf->page_title = $data['page_title'];
			$pdf->company = $data['company'];
			$pdf->quotation = $data['quotation'];
			$pdf->quotation_detail = $data['quotation_detail'];

	        $pdf->AddPage('P', 'A4');
			$page = strtolower('export_quote');
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
				$result = $this->db->get('quotations')->num_rows();
				
				if($result = 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'Quotation not Found';			
				}
				else
				{
					$this->db->delete('quotations', ['id' => $id]);
					$this->db->delete('quotation_details', ['quotation_id' => $id]);
											
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted Quotation';			
				}
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Quotation First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function quoteList() {
		$json['data'] = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('quote_to', $this->input->get("q"));
			}
			
			$query = $this->db->select('id, quote_to as text')
							->where('type', 'Import')
							->limit(10)
							->get('quotations');
							
			$json['data'] = $query->result_array();
			
			$json['count'] = count($json['data']);

			// $json['data'][] = ['id' => 0, 'text' => 'New Quotation'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
   	}

   	
}