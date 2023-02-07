<?php

use mikehaertl\wkhtmlto\Pdf;

class Costsheet extends MY_Controller {
	var $_table2;
	var $_company_id;
	var $_type;
	var $_folder;
	var $_share;
	var $_path;
	var $_share_url;
	var $_path_url;

	function __construct() {
		parent::__construct();
	
		$this->_table2 = 'child_jobs';
		$this->load->model('export');
		
		$this->_company_id = $this->export->getCompanyID();
		$this->_type = 'Export';

		$this->_folder = 'documents/jobs/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url  = base_url('share');
		$this->_path_url   = base_url($this->_folder);
	
	}

	function index() {
		$data['page_title'] = "Manage Costsheet";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}	

	function get($job_id = 0) {

		$response = [];
		
		if($job_id){

			$rows = $this->accounting->getCostSheetDetails($job_id);

			foreach ($rows as $key => $value) {

				$docdir = $this->export->getDocFolder($this->_path, $job_id);
				
				$file = $this->_path.$docdir.$value['file'];
				$download = $this->_path_url.$docdir.$value['file'];

				if(file_exists($file) AND $value['file'] != null)
					$rows[$key]['upfile'] = $download;
				else
					$rows[$key]['upfile'] = NULL;
				
				$billing_type =  form_dropdown('kbr_billing_type['.$value['id'].']', getEnumSetOptions('costsheets', 'billing_type'), $value['billing_type'], 'class="form-control form-control-sm Unchanged billingType"');

				$is_inr =  form_dropdown('kbr_is_inr['.$value['id'].']', array('Yes'=>'Yes', 'No'=>'No'), $value['is_inr'], 'class="form-control form-control-sm Unchanged IsINR d-none"');

				$unit =  form_dropdown('kbr_unit['.$value['id'].']', getSelectOptions('units'), $value['unit_id'], 'class="Unchanged form-control form-control-sm Unit"');
				
				$sell_is_inr =  form_dropdown('kbr_sell_is_inr['.$value['id'].']', array('Yes'=>'Yes', 'No'=>'No'), $value['sell_is_inr'], 'class="form-control form-control-sm Unchanged SellIsINR d-none"');

				$sell_unit =  form_dropdown('kbr_sell_unit['.$value['id'].']', getSelectOptions('units'), $value['sell_unit_id'], 'class="Unchanged form-control form-control-sm SellUnit"');

				unset($rows[$key]['is_inr']);
				unset($rows[$key]['unit_id']);
				unset($rows[$key]['sell_is_inr']);
				unset($rows[$key]['sell_unit_id']);
				unset($rows[$key]['billing_type']);

				$rows[$key]['is_inr'] = $is_inr;
				$rows[$key]['sell_is_inr'] = $sell_is_inr;
				$rows[$key]['unit_id'] = $unit;
				$rows[$key]['sell_unit_id'] = $sell_unit;

				$rows[$key]['billing_type'] = $billing_type;

				$deleteBtn = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));

				$rows[$key]['delete_btn'] = $deleteBtn;
			}
			
			$response['code'] = 1;
			$response['success'] = true;
			$response['rows'] = $rows;

			$job = $this->export->getJob($job_id);
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

	function edit($id) {
		/*echo $id;exit();*/
		$docdir = $this->export->getDocFolder($this->_path, $id);
		$response = [];
		$kbr_total = 0;
		$new_total = 0;

		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$kbr_particulars = $this->input->post('kbr_bill_item_code');
		$new_particulars = $this->input->post('new_bill_item_code');

		// Count # of uploaded files in array
		if(isset($_FILES['kbr_upload']['name']))
			$kbr_total = count($_FILES['kbr_upload']['name']);

		// Loop through each file
		if($kbr_total > 0){	
			foreach ($_FILES['kbr_upload']['name'] as $key => $value) {

				
			  	//Get the temp file path
			  	$tmpFilePath = $_FILES['kbr_upload']['tmp_name'][$key];

			  	//Make sure we have a file path
			  	if ($tmpFilePath != ""){
			    	//Setup our new file path
			    	$kbrFilePath = $this->_path.$docdir.$_FILES['kbr_upload']['name'][$key];
			    	//Upload the file into the temp dir
			    	
			    	if(move_uploaded_file($tmpFilePath, $kbrFilePath))
			    		$kbrfilesList[$key] = $_FILES['kbr_upload']['name'][$key];
			    	else
			    		$kbrfilesList[$key] = null;
			    	
			  	}
			}
		}
		
		// Count # of uploaded files in array
		if(isset($_FILES['new_upload']['name']))
			$new_total = count($_FILES['new_upload']['name']);
		// Count # of uploaded files in array
		$new_total = count($_FILES['new_upload']['name']);
		if($new_total > 0){
			// Loop through each file
			foreach ($_FILES['new_upload']['name'] as $key => $value) {
			  	//Get the temp file path
			  	$tmpFilePath = $_FILES['new_upload']['tmp_name'][$key];
			  	//Make sure we have a file path
			  	if ($tmpFilePath != ""){
			    	//Setup our new file path
			    	$newFilePath = $this->_path.$docdir.$_FILES['new_upload']['name'][$key];
			    	//Upload the file into the temp dir
			    	if(move_uploaded_file($tmpFilePath, $newFilePath))
			    		$newfilesList[$key] = $_FILES['new_upload']['name'][$key];
			    	else
			    		$newfilesList[$key] = null;
			    }
			}
		}

		if ($kbr_particulars != null) {

			$sr_nos         	= $this->input->post('kbr_sr_no');
			$bill_item_ids    	= $this->input->post('kbr_bill_item_id');
			$vendor         	= $this->input->post('kbr_vendor_id');
			
			$is_inr         	= $this->input->post('kbr_is_inr');
			$currency         	= $this->input->post('kbr_currency');
			$ex_rate         	= $this->input->post('kbr_ex_rate');
			$currency_amt 		= $this->input->post('kbr_currency_amount');
			$inr_rate           = $this->input->post('kbr_inr_rate');
			$rate            	= $this->input->post('kbr_rate');
			$unit            	= $this->input->post('kbr_unit');
			$qty            	= $this->input->post('kbr_units');
			$amounts          	= $this->input->post('kbr_amount');

			$sell_is_inr         = $this->input->post('kbr_sell_is_inr');
			$sell_currency      = $this->input->post('kbr_sell_currency');
			$sell_ex_rate       = $this->input->post('kbr_sell_ex_rate');
			$sell_currency_amt 	= $this->input->post('kbr_sell_currency_amount');
			$sell_inr_rate      = $this->input->post('kbr_sell_inr_rate');
			$sell_rate          = $this->input->post('kbr_sell_rate');
			$sell_unit          = $this->input->post('kbr_sell_unit');
			$sell_qty           = $this->input->post('kbr_sell_units');
			$sell_amounts       = $this->input->post('kbr_sell_amount');
			
			$billing_type 		= $this->input->post('kbr_billing_type');

			foreach ($kbr_particulars as $index => $particular) {

				if (! in_array("$index", $delete_ids)) {

					$amount = ($amounts[$index] > 0 ? $amounts[$index] : round($qty[$index] * $rate[$index], 2));
					$sell_amount = ($sell_amounts[$index] > 0 ? $sell_amounts[$index] : round($sell_qty[$index] * $sell_rate[$index], 2));

					if ($currency_amt[$index] > 0 && $ex_rate[$index] > 1)
						$amount = round($currency_amt[$index] * $ex_rate[$index]);

					if ($sell_currency_amt[$index] > 0 && $sell_ex_rate[$index] > 1)
						$sell_amount = round($sell_currency_amt[$index] * $sell_ex_rate[$index]);

					
					$data = array(
						'job_id'      		=> $id,
						'bill_item_id'    	=> $bill_item_ids[$index],
						'sr_no'           	=> $sr_nos[$index],
						'particulars'     	=> $particular,
						'vendor_id'         => $vendor[$index],

						'is_inr'         	=> $is_inr[$index],
						'currency_id'       => $currency[$index],
						'ex_rate'         	=> $ex_rate[$index],
						'currency_amt'      => $currency_amt[$index],
						'inr_rate'         	=> $inr_rate[$index],
						'rate'         		=> $rate[$index],
						'unit_id'         	=> $unit[$index],
						'qty'         		=> $qty[$index],
						'amount'         	=> $amount,
						
						'sell_is_inr'       => $sell_is_inr[$index],
						'sell_currency_id'  => $sell_currency[$index],
						'sell_ex_rate'      => $sell_ex_rate[$index],
						'sell_currency_amt' => $sell_currency_amt[$index],
						'sell_inr_rate'     => $sell_inr_rate[$index],
						'sell_rate'         => $sell_rate[$index],
						'sell_unit_id'         => $sell_unit[$index],
						'sell_qty'         	=> $sell_qty[$index],
						'sell_amount'       => $sell_amount,

						'billing_type'		=> $billing_type[$index],
					);
					
					if(isset($kbrfilesList[$index]))
						$data['file'] = $kbrfilesList[$index];
					
					$this->kaabar->save($this->_table, $data, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete('costsheets', $index);
				}
			}
		}
		

		if ($new_particulars != null) {

			$sr_nos         	= $this->input->post('new_sr_no');
			$bill_item_ids    	= $this->input->post('new_bill_item_id');
			$vendor         	= $this->input->post('new_vendor_id');
			
			$is_inr         	= $this->input->post('new_is_inr');
			$currency         	= $this->input->post('new_currency');
			$ex_rate         	= $this->input->post('new_ex_rate');
			$currency_amt 		= $this->input->post('new_currency_amount');
			$inr_rate           = $this->input->post('new_inr_rate');
			$rate            	= $this->input->post('new_rate');
			$unit            	= $this->input->post('new_unit');
			$qty            	= $this->input->post('new_units');
			$amounts          	= $this->input->post('new_amount');

			$sell_is_inr         = $this->input->post('new_sell_is_inr');
			$sell_currency      = $this->input->post('new_sell_currency');
			$sell_ex_rate       = $this->input->post('new_sell_ex_rate');
			$sell_currency_amt 	= $this->input->post('new_sell_currency_amount');
			$sell_inr_rate      = $this->input->post('new_sell_inr_rate');
			$sell_rate          = $this->input->post('new_sell_rate');
			$sell_unit          = $this->input->post('new_sell_unit');
			$sell_qty           = $this->input->post('new_sell_units');
			$sell_amounts       = $this->input->post('new_sell_amount');

			$billing_type 		= $this->input->post('new_billing_type');
			
			$data = [];

			foreach ($new_particulars as $index => $particular) {
				if ($bill_item_ids[$index] > 0) {
					$amount = 0;
					$amount = ($amounts[$index] > 0 ? $amounts[$index] : round($qty[$index] * $rate[$index], 2));

					$sell_amount = ($sell_amounts[$index] > 0 ? $sell_amounts[$index] : round($sell_qty[$index] * $sell_rate[$index], 2));

					if ($currency_amt[$index] > 0 && $ex_rate[$index] > 1)
						$amount = round($currency_amt[$index] * $ex_rate[$index]);

					if ($sell_currency_amt[$index] > 0 && $sell_ex_rate[$index] > 1)
						$sell_amount = round($sell_currency_amt[$index] * $sell_ex_rate[$index]);

					$data = array(
						'job_id'      		=> $id,
						'bill_item_id'    	=> $bill_item_ids[$index],
						'sr_no'           	=> $sr_nos[$index],
						'particulars'     	=> $particular,
						'vendor_id'         => $vendor[$index],

						'is_inr'         	=> $is_inr[$index],
						'currency_id'       => $currency[$index],
						'ex_rate'         	=> $ex_rate[$index],
						'currency_amt'      => $currency_amt[$index],
						'inr_rate'         	=> $inr_rate[$index],
						'rate'         		=> $rate[$index],
						'unit_id'         	=> $unit[$index],
						'qty'         		=> $qty[$index],
						'amount'         	=> $amount,
						
						'sell_is_inr'       => $sell_is_inr[$index],
						'sell_currency_id'  => $sell_currency[$index],
						'sell_ex_rate'      => $sell_ex_rate[$index],
						'sell_currency_amt' => $sell_currency_amt[$index],
						'sell_inr_rate'     => $sell_inr_rate[$index],
						'sell_rate'         => $sell_rate[$index],
						'sell_unit_id'      => $sell_unit[$index],
						'sell_qty'         	=> $sell_qty[$index],
						'sell_amount'       => $sell_amount,
						'file'				=> isset($newfilesList[$index]) ? $newfilesList[$index] : NULL,
						'billing_type'		=> $billing_type[$index]
					);
					$this->kaabar->save($this->_table, $data);
				}
				
			}
			
		}

		$response['success'] = true;
		$response['messages'] = 'Successfull Updated';

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function deleteattach() {

		$job_id = $this->input->post('job_id');
		$id = $this->input->post('row_id');

		$response = [];

		if($job_id AND $id)
		{

			$file = $this->kaabar->getField($this->_table, $id, 'id', 'file');
			$docdir = $this->export->getDocFolder($this->_path, $job_id);
			
			$path = $this->_path.$docdir.$file;
			

			if($this->db->update($this->_table, array("file" => ''), "id = $id")){

				if(file_exists($path))
					unlink($path);

				$response['success'] = true;
				$response['messages'] = 'Attachment Successfull Deleted';
			}
			else{
				$response['success'] = false;
				$response['messages'] = 'Something wrong, Try Again';
			}
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Something wrong, Try Again';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function downloadattach() {

		$job_id = $this->input->post('job_id');
		$id = $this->input->post('row_id');

		$response = [];

		if($job_id AND $id)
		{

			$file = $this->kaabar->getField($this->_table, $id, 'id', 'file');
			$docdir = $this->import->getDocFolder($this->_path, $job_id);
			
			$path = $this->_path.$docdir.$file;
			
			if(file_exists($path)){
					
				// $data = file_get_contents($path);
				// $this->load->helper('download');

				$download = $this->_push_file($path, $file); 
				if($download){
					$response['success'] = true;
					$response['messages'] = 'Attachment Successfull Downloaded';
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'File Not Available';	
				}

			}
			else{
				$response['success'] = false;
				$response['messages'] = 'Something wrong, Try Again';
			}
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Something wrong, Try Again';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function _push_file($path, $name)
    {
		// make sure it's a file before doing anything!
		if(is_file($path))
		{
			// required for IE
			if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off'); }

			// get the file mime type using the file extension
			$this->load->helper('file');

			$mime = get_mime_by_extension($path);

			// Build the headers to push out the file properly.
			header('Pragma: public');     // required
			header('Expires: 0');         // no cache
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
			header('Cache-Control: private',false);
			header('Content-Type: '.$mime);  // Add the mime type from Code igniter.
			header('Content-Disposition: attachment; filename="'.basename($name).'"');  // Add the file name
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($path)); // provide file size
			header('Connection: close');
			//readfile($path); // push it out
			if(readfile($path))
				return true;
			else
				return false;
	    }
	}

	function jobsList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('idkaabar_code', $this->input->get("q"));
			}
			
			$query = $this->db->select('id,idkaabar_code as text')
							->where('type', 'Export')
							->limit(10)
							->get('jobs');
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New Job'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

	

}
