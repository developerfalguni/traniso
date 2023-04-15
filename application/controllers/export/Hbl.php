<?php
use mikehaertl\wkhtmlto\Pdf;
class Hbl extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('export');
		$this->load->model('import');
		$this->load->model('kaabar');
	}
	
	function index() {
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function edit($id = 0) {

		$data['id'] = array('id' => 0);

		$data['page_title'] = 'HBL';
		$data['page']       = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
		
	}
	function get($job_id = 0) {

		$response = [];
		
		if($job_id){ 

			$jobs = $this->kaabar->getRow('jobs',['id' => $job_id]);
			$hblData = $this->kaabar->getRow('hbl_jobs',['job_id' => $job_id]);
			
			if($hblData){

				$jobs = $hblData;
				$job['job_id'] = $job_id;
				$containers = $this->kaabar->getRows('hbl_details', $jobs['id'], 'parent_id');
			}
			else
			{

				$job = $this->export->getJob($job_id);

				$job['job_id'] = $job_id;
				///////// Find Billing 1 Name using Category
				if($job['billing_party_category'] == 'PARTY')
					$party = $this->kaabar->getRow('parties', $job['billing_party_id'], 'id');
				elseif($job['billing_party_category'] == 'AGENT')
					$party = $this->kaabar->getRow('new_agents', $job['billing_party_id'], 'id');

				///////// Find Shipper Name using Category
				
				
				if($job['shipper_category'] == 'PARTY'){
					$shipper = $this->kaabar->getRow('parties', $job['shipper_id'], 'id');
				}
				elseif($job['shipper_category'] == 'AGENT'){
					$shipper = $this->kaabar->getRow('new_agents', $job['shipper_id'], 'id');

					$shipper['name']=$shipper['person_name'];
					$shipper['address']=$shipper['address1'];
					
				}
				elseif($job['shipper_category'] == 'CONSIGNEE')
					$shipper = $this->kaabar->getRow('consignees', $job['shipper_id'], 'id');
				elseif($job['shipper_category'] == 'VENDOR')
					$shipper = $this->kaabar->getRow('vendors', $job['shipper_id'], 'id');

				///////// Find Billing 2 Name using Category
				if($job['billing_party1_category'] == 'PARTY')
					$party2 = $this->kaabar->getRow('parties', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'AGENT')
					$party2 = $this->kaabar->getRow('new_agents', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'CONSIGNEE')
					$party2 = $this->kaabar->getRow('consignees', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'VENDOR')
					$party2 = $this->kaabar->getRow('vendors', $job['billing_party1_id'], 'id');


				$consignee = $this->kaabar->getRow('consignees',['id' => $job['consignee_id']]);
				$notify = $this->kaabar->getRow('consignees',['id' => $job['notify_id']]);
				$receipt_port  = $this->kaabar->getField('ports', ['id' => $job['por_id']]);
				$loading_port  = $this->kaabar->getField('ports', ['id' => $job['pol_id']]);
				$discharge  = $this->kaabar->getField('ports', ['id' => $job['pod_id']]);
				$delivery  = $this->kaabar->getField('ports', ['id' => $job['fpod_id']]);
				

				$jobs['id'] 			= 0;
				$jobs['job_id'] 		= $job['id'];
				$jobs['s_name'] 		= $shipper['name'];
				$jobs['s_address'] 		= $shipper['address'];
				$jobs['c_name'] 		= isset($consignee['consignee_name']) ? $consignee['consignee_name'] : '';
				$jobs['c_address'] 		= isset($consignee['address1']) ? $consignee['address1'] : '';
				$jobs['n_name'] 		= isset($notify['name']) ? $notify['name'] : '';
				$jobs['n_address'] 		= isset($notify['address1']) ? $notify['address1'] : '';
				$jobs['bl_no'] 			= $job['hbl_no'];
				$jobs['bl_type'] 		= $job['hbl_type'];
				$jobs['vessel'] 		= $job['vessel_name'];
				$jobs['voyage'] 		= $job['vessel_voyage'];
				$jobs['booking_no'] 	= $job['booking_no'];
				$jobs['delivery_agent'] = '';
				$jobs['receipt'] 		= $receipt_port ? $receipt_port : '';
				$jobs['loading'] 		= $loading_port ? $loading_port : '';
				$jobs['discharge'] 		= $discharge ? $discharge : '';
				$jobs['delivery'] 		= $delivery ? $delivery : '';
				$jobs['gross_weight'] 	= $job['gross_weight'];
				$jobs['charges_amount'] = '';
				$jobs['payable_at'] 	= '';
				$jobs['no_of_original'] = '';
				$jobs['date_issue'] 	= '';
				$jobs['remarks'] 		= $job['remarks'];

				$containers = $this->kaabar->getRows('containers', $job_id, 'job_id');

				foreach ($containers as $key => $value) {

					$containers[$key]['description'] = $jobs['remarks'];
				}

				$jobs['no_containers'] = count($containers);


				
			}

			
			$response['job'] = $jobs;
			// $container_count = count($containers);
			$response['containers'] = $containers;
			$response['containers_cn'] = $containers;
			//$response['containers'] = $containers;

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

	function ajaxEdit() {

		$response = [];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('loading', 'Loading Port', 'trim|required');
		$this->form_validation->set_rules('delivery', 'Delivery Port', 'required');
		$this->form_validation->set_rules('bl_no', 'BL No', 'required');
		
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
			// echo "<pre>";
			// print_r($this->input->post());exit;
			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'job_id'			=> $this->input->post('job_id'),
					's_name'            => $this->input->post('s_name'),
					's_address'        	=> $this->input->post('s_address'),
					'c_name'           	=> $this->input->post('c_name'),
					'c_address'        	=> $this->input->post('c_address'),
					'n_name'           	=> $this->input->post('n_name'),
					'n_address'         => $this->input->post('n_address'),
					'bl_no'         	=> $this->input->post('bl_no'),
					'bl_type'         	=> $this->input->post('bl_type'),
					'vessel'            => $this->input->post('vessel_name'),
					'voyage'           	=> $this->input->post('voyage'),
					'booking_no'        => $this->input->post('booking_no'),
					'delivery_agent'    => $this->input->post('delivery_agent'),
					'receipt'  			=> $this->input->post('receipt'),
					'loading'           => $this->input->post('loading'),
					'discharge'         => $this->input->post('discharge'),
					'delivery'   		=> $this->input->post('delivery'),
					'gross_weight'   	=> $this->input->post('gross_weight'),
					'no_containers'     => $this->input->post('no_of_original'),
					'charges_amount'    => $this->input->post('charges_amount'),
					'payable_at'    	=> $this->input->post('payable_at'),
					'no_of_original'    => $this->input->post('no_of_original'),
					'date_issue'        => $this->input->post('date_issue'),
					'remarks'           => $this->input->post('remarks')
				);
				
				$id = $this->kaabar->save('hbl_jobs', $data, ['id' => $row_id]);
				
				$this->_updateHbl($id, 0);
							
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

	function _updateHbl($job_id, $id) {
		// $delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		$descriptions = $this->input->post('description');
		if ($descriptions != null) {
			$number 			= $this->input->post('number');
			$mark_number    = $this->input->post('mark_number');
			$measurement	= $this->input->post('measurement');

			foreach ($descriptions as $index => $description) {
				
					$data = array(
						'parent_id'	  	=> $job_id,
						'description' 	=> $description,
						'number' 		=> $number[$index],
						'mark_number'      	=> $mark_number[$index],
						'measurement'	=> $measurement[$index] ? $measurement[$index] : '-',
					);
					$containers = $this->kaabar->getRows('hbl_details', $job_id, 'parent_id');
					if($containers)
						$this->kaabar->save('hbl_details', $data, ['id' => $index]);
					else
						$this->kaabar->save('hbl_details', $data);
				}
			
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
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
   }

   //pdf Falguni
   function pdf($id = 0, $pdf = 0) {
   		$job_id=$this->uri->segment('5');
   		$response = [];
   		if($job_id){ 

			$jobs = $this->kaabar->getRow('jobs',['id' => $job_id]);
			$hblData = $this->kaabar->getRow('hbl_jobs',['job_id' => $job_id]);
			
			if($hblData){

				$jobs = $hblData;
				$job['job_id'] = $job_id;
				$containers = $this->kaabar->getRows('hbl_details', $jobs['id'], 'parent_id');
			}
			else
			{

				$job = $this->export->getJob($job_id);

				$job['job_id'] = $job_id;
				///////// Find Billing 1 Name using Category
				if($job['billing_party_category'] == 'PARTY')
					$party = $this->kaabar->getRow('parties', $job['billing_party_id'], 'id');
				elseif($job['billing_party_category'] == 'AGENT')
					$party = $this->kaabar->getRow('new_agents', $job['billing_party_id'], 'id');

				///////// Find Shipper Name using Category
				
				
				if($job['shipper_category'] == 'PARTY'){
					$shipper = $this->kaabar->getRow('parties', $job['shipper_id'], 'id');
				}
				elseif($job['shipper_category'] == 'AGENT'){
					$shipper = $this->kaabar->getRow('new_agents', $job['shipper_id'], 'id');

					$shipper['name']=$shipper['person_name'];
					$shipper['address']=$shipper['address1'];
					
				}
				elseif($job['shipper_category'] == 'CONSIGNEE')
					$shipper = $this->kaabar->getRow('consignees', $job['shipper_id'], 'id');
				elseif($job['shipper_category'] == 'VENDOR')
					$shipper = $this->kaabar->getRow('vendors', $job['shipper_id'], 'id');
				
				///////// Find Billing 2 Name using Category
				if($job['billing_party1_category'] == 'PARTY')
					$party2 = $this->kaabar->getRow('parties', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'AGENT')
					$party2 = $this->kaabar->getRow('new_agents', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'CONSIGNEE')
					$party2 = $this->kaabar->getRow('consignees', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'VENDOR')
					$party2 = $this->kaabar->getRow('vendors', $job['billing_party1_id'], 'id');


				$consignee = $this->kaabar->getRow('consignees',['id' => $job['consignee_id']]);
				$notify = $this->kaabar->getRow('consignees',['id' => $job['notify_id']]);
				$receipt_port  = $this->kaabar->getField('ports', ['id' => $job['por_id']]);
				$loading_port  = $this->kaabar->getField('ports', ['id' => $job['pol_id']]);
				$discharge  = $this->kaabar->getField('ports', ['id' => $job['pod_id']]);
				$delivery  = $this->kaabar->getField('ports', ['id' => $job['fpod_id']]);

				$shipper_state =isset($shipper['state']) ? $shipper['state'] : '';

				$sql_state="SELECT * FROM `states` where `id`=$shipper_state";
				
				$query_state = $this->db->query($sql_state);

			    $re_state=$query_state->row();


			    $shipper_country =isset($shipper['country']) ? $shipper['country'] : '';

				$sql_country="SELECT * FROM `countries` where `id`=$shipper_country";
				
				$query_country = $this->db->query($sql_country);

			    $re_country=$query_country->row();

				
				$jobs['id'] 			= 0;
				$jobs['job_id'] 		= $job['id'];
				$jobs['item_description'] 		= isset($job['item_description']) ? $job['item_description'] : '';
				$jobs['sb_no'] 		= $job['sb_no'];
				$jobs['job_invoice_no'] 		= isset($job['invoice_no']) ? $job['invoice_no'] : '';
				$jobs['job_net_weight'] 		= isset($job['net_weight']) ? $job['net_weight'] : '';
				$jobs['job_gross_weight'] 		= isset($job['gross_weight']) ? $job['gross_weight'] : '';
				$jobs['job_gross_weight'] 		= isset($job['gross_weight']) ? $job['gross_weight'] : '';
				$jobs['booking_date'] 		= isset($job['booking_date']) ? $job['booking_date'] : '';
				$jobs['package_type_name'] 		= isset($job['package_type_name']) ? $job['package_type_name'] : '';
				$jobs['unit_name'] 		= isset($job['unit_name']) ? $job['unit_name'] : '';

				$jobs['s_name'] 		= isset($shipper['name']) ? $shipper['name'] : '';
				$jobs['s_address'] 		= isset($shipper['address']) ? $shipper['address'] : '';
				$jobs['city_id'] 		= isset($shipper['city_id']) ? $shipper['city_id'] : '';
				$jobs['state_name'] 		= $re_state->name;
				$jobs['country_name'] 		= $re_country->name;
				/*$jobs['state_name'] 		= isset($re_state->name) ? $re_state : '';*/
				/*$jobs['country_name'] 		= isset($re_country->name) ? $re_country : '';*/
				$jobs['pincode'] 		= $shipper['pincode'];
				$jobs['email'] 		= isset($shipper['email']) ? $shipper['email'] : '' ;
				$jobs['gst_nos'] 		= $shipper['gst_nos'];

				
				$jobs['c_name'] 		= isset($consignee['consignee_name']) ? $consignee['consignee_name'] : '';
				$jobs['c_address1'] 		= isset($consignee['address1']) ? $consignee['address1'] : '';
				$jobs['c_address2'] 		= isset($consignee['address2']) ? $consignee['address2'] : '';
				$jobs['c_address3'] 		= isset($consignee['address3']) ? $consignee['address3'] : '';
				$jobs['city'] 		= isset($consignee['city']) ? $consignee['city'] : '';
				$jobs['website'] 		= isset($consignee['website']) ? $consignee['website'] : '';
				$jobs['mobile_no'] 		= isset($consignee['mobile_no']) ? $consignee['mobile_no'] : '';
				$jobs['email_id'] 		= isset($consignee['email_id']) ? $consignee['email_id'] : '';

				
				$jobs['n_name'] 		= isset($notify['name']) ? $notify['name'] : '';
				$jobs['n_address'] 		= isset($notify['address1']) ? $notify['address1'] : '';
				$jobs['n_address1'] 		= isset($notify['address1']) ? $notify['address1'] : '';
				$jobs['n_address2'] 		= isset($notify['address2']) ? $notify['address2'] : '';
				$jobs['n_address3'] 		= isset($notify['address3']) ? $notify['address3'] : '';
				$jobs['n_city'] 		= isset($notify['city']) ? $notify['city'] : '';
				$jobs['n_website'] 		= isset($notify['website']) ? $notify['website'] : '';
				$jobs['n_mobile_no'] 		= isset($notify['mobile_no']) ? $notify['mobile_no'] : '';
				$jobs['n_email_id'] 		= isset($notify['email_id']) ? $notify['email_id'] : '';

				$jobs['bl_no'] 			= $job['hbl_no'];
				$jobs['bl_type'] 		= $job['hbl_type'];
				$jobs['vessel'] 		= isset($job['vessel_name']) ? $job['vessel_name'] : '';
				$jobs['voyage'] 		= isset($job['vessel_voyage']) ? $job['vessel_voyage'] : '';
				$jobs['booking_no'] 	= $job['booking_no'];
				$jobs['delivery_agent'] = '';
				$jobs['receipt'] 		= $receipt_port ? $receipt_port : '';
				$jobs['loading'] 		= $loading_port ? $loading_port : '';
				$jobs['discharge'] 		= $discharge ? $discharge : '';
				$jobs['delivery'] 		= $delivery ? $delivery : '';
				$jobs['gross_weight'] 	= $job['gross_weight'];
				$jobs['charges_amount'] = '';
				$jobs['payable_at'] 	= '';
				$jobs['no_of_original'] = '';
				$jobs['date_issue'] 	= '';
				$jobs['remarks'] 		= $job['remarks'];

				$containers = $this->kaabar->getRows('containers', $job_id, 'job_id');
				
				foreach ($containers as $key => $value) {

					$containers[$key]['description'] = $jobs['remarks'];
				}

				$jobs['no_containers'] = count($containers);
				
			}
			$response['job'] = $jobs;
			// $container_count = count($containers);
			$response['containers'] = $containers;
			$response['containers_cn'] = $containers;


		//print_r($response['containers_cn']);exit();
		/*echo $response['job']['s_name'];
   		echo $job_id;exit();*/

   		/*pdf all Data*/
   		/*shipper*/
   		$pdf_sname=isset($response['job']['s_name']) ? $response['job']['s_name'] : '';
   		$pdf_saddress=isset($response['job']['s_address']) ? $response['job']['s_address'] : '';
   		$pdf_state_name=isset($response['job']['state_name']) ? $response['job']['state_name'] : '';
   		$pdf_pincode=isset($response['job']['pincode']) ? $response['job']['pincode'] : '';
   		$pdf_country_name=isset($response['job']['country_name']) ? $response['job']['country_name'] : '';
   		$pdf_email=isset($response['job']['email']) ? $response['job']['email'] : '';
   		$pdf_gst_nos=isset($response['job']['gst_nos']) ? $response['job']['gst_nos'] : '';
   		$pdf_c_address1=isset($response['job']['c_address1']) ? $response['job']['c_address1'] : '';
   		$pdf_c_address2=isset($response['job']['c_address2']) ? $response['job']['c_address2'] : '';
   		$pdf_city=isset($response['job']['city']) ? $response['job']['city'] : '';
   		$pdf_n_address2=isset($response['job']['n_address2']) ? $response['job']['n_address2'] : '';
   		$pdf_n_city=isset($response['job']['n_city']) ? $response['job']['n_city'] : '';
   		$pdf_vessel_name=isset($response['job']['vessel_name']) ? $response['job']['vessel_name'] : '';
   		$pdf_voyage=isset($response['job']['voyage']) ? $response['job']['voyage'] : '';
   		
   		}

   		$filename = "hbl_report";
   		   		
   		if($pdf > 0)
        {
        	
        
        $calibri = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri.ttf', 'TrueTypeUnicode', '', 32);
        $calibribold = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri-bold.ttf', 'TrueTypeUnicode', '', 32);
		

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

			$pdf->AddPage();
			$pdf->SetFont($calibribold, '', 12);
			/*$pdf->SetTextColor(0,112,192);*/
			$html = '<html>
			<head></head>
			<body><table border="1">
 <tr>
  <th colspan="4" align="center" style="font-family:calibri;font-size:8;line-height: 220%;">BILL OF LADING FOR OCEAN TRANSPORT OR MULTIMODAL TRANSPORT</th>
 </tr>
 <tr>
  <td colspan="2" rowspan="3"> <span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:12;">SHIPPER</span><br/><span style="font-family:calibri;font-size:10;"> '.$pdf_sname.'</span><br/><span style="font-family:calibri;font-size:10;"> '.$pdf_saddress.'</span><br/><span style="font-family:calibri;font-size:10;"> '.$pdf_state_name.' '.$pdf_pincode.' '.$pdf_country_name.'</span><br/><span style="font-family:calibri;font-size:10;"> TEL : 9727777791 MAIL : '.$pdf_email.'</span><br/><span style="font-family:calibri;font-size:10;"> GST NO : '.$pdf_gst_nos.'</span>
	  <hr style="border: 1px solid black;line-height: 20px;"> <span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:12;">CONSIGNEE</span><br/><span style="font-family:calibri;font-size:10;"> '.$response['job']['c_name'].'</span><br/><span style="font-family:calibri;font-size:10;"> '.$pdf_c_address1.' '.$pdf_c_address2.'</span><br/><span style="font-family:calibri;font-size:10;"> Croatia VAT: 65657961246</span><br/> <span style="font-family:calibri;font-size:10;"> '.$pdf_c_address2.' '.$pdf_city.'</span>	
</td>
<td align="center">
<span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:11;line-height: 250%;">BL NO \ MTD NO</span></td>
  <td><span style="font-family:calibri-bold;font-size:10;line-height: 250%;"> '.$response['job']['bl_no'].'</span></td>
 </tr>
 <tr>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:11;line-height:150%">BL TYPE </span></td>
  <td><span style="font-family:calibri-bold;font-size:10;line-height:150%"> '.$response['job']['bl_type'].'</span></td>
  
 </tr>
 <tr>
  <td colspan="2" align="center"><img src="/traniso/php_uploads/bd0f7b72c8788826c4c1b136c99f57fd.png" width="100" height="60"><br/><span style="font-family:calibri;font-size:8;">REG OFF. : GF2, Ground Floor, Riddhi Siddhi Arcade 1, Plot No 13,<br style="line-height:12px;">Sector 8, Nr B.M Pump, Gandhidham - Gujarat (370201) - <b>INDIA</b><br style="line-height:12px;"><a href="www.traniso.in" style="color:black;text-decoration:none">www.traniso.in</a> | +91 9727 626474 | <a href="mailto:manish@traniso.in" style="color:black;text-decoration:none">manish@traniso.in</a><br style="line-height:12px;"><b>REG NO : MTO/DGS/2553/JAN/2025</b></span></td>  
 </tr>
 <tr>
  <td colspan="2"> <span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:12;">NOTIFY</span><br/><span style="font-family:calibri;font-size:10;"> '.$response['job']['n_name'].'</span><br/><span style="font-family:calibri;font-size:10;"> '.$response['job']['n_address'].' ,</span><br/><span style="font-family:calibri;font-size:10;"> TCroatia VAT: 65657961246</span><br/> <span style="font-family:calibri;font-size:10;"> '.$pdf_n_address2.' '.$pdf_n_city.'</span></td>
  <td colspan="2"> <span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:12;">DELIVERY AGENT</span><br/><span style="font-family:calibri;font-size:10;"> '.$response['job']['delivery_agent'].'</span><br/><span style="font-family:calibri;font-size:10;"> '.$response['job']['delivery_agent'].'</span><br/> <span style="font-family:calibri;font-size:10;"> Tel: '.$response['job']['delivery_agent'].'</span></td>
 </tr>
 <tr>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Place of Receipt
 AGENT</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['receipt'].' </span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Port of Loading</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['loading'].' </span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Ocean Vessel / Voy No.</span><br/> <span style="font-family:calibri;font-size:9;"> '.$pdf_vessel_name.' '.$pdf_voyage.'</span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Booking No</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['booking_no'].'</span></td>
 </tr>
  <tr>
  	<td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Port of Discharge </span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['discharge'].' </span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Place of Delivery</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['delivery'].' </span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Total Gross Weight</span><br/> '.$response['job']['gross_weight'].'<span style="font-family:calibri;font-size:9;"> </span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Total No of Container</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['no_containers'].'</span></td>
 </tr>
  <tr>
  <td colspan="4" align="center" style="font-family:calibri-bold;color:rgb(0,112,192);font-size:8;">PARTICULARS FURNISHED BY SHIPPER</td>
 </tr>
  <tr>
  <td align="center" style="border-right-color:white"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Container No.(s)</span><br/><br/><br/> <span style="font-family:calibri;font-size:10;"> AS PER LIST BELOW.</span></td>
  <td align="center" style="border-right-color:white"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Marks and numbers</span></td>
  <td align="center" style="border-right-color:white"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Number of packages, kinds of packages, general
description of goods. (said to contain)</span><br/> <span style="font-family:calibri;font-size:10;"> 03X20 FT CONTAINER. SAID TO CONTAIN.
SHIPPERS LOAD , STOW AND COUNT.
2766 BOXES PACKED IN 63 PALLETS
GLAZED VITRIFIED TILES
S/B No. : 8260216 dt: 04-03-2023
Invoice No. : EX22230728 DT: 04.03.2023
NET WEIGHT : 78229.000
Gross Weight: 79367.000	
HSN CODE: 69072100</span></td>
  <td align="center" style="border-left-color:white"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Gross Weight / Measurement</span><br/><br/><br/> <span style="font-family:calibri;font-size:10;"> 79367.00 KGS</span></td>
 </tr>
 <tr>
 <td colspan="4">
 <table border="1">
 
  <tr style="background-color:yellow;">
  <td align="center"><span style="font-family:calibri;font-size:10;">CONTAINER NO</span></td>
  <td align="center"><span style="font-family:calibri;font-size:10;">LINE SEAL NO</span></td>
  <td align="center"><span style="font-family:calibri;font-size:10;">SHIPPER SEAL</span></td>
  <td align="center"><span style="font-family:calibri;font-size:10;">PACKAGE</span></td>
  <td align="center"><span style="font-family:calibri;font-size:10;">TYPE</span></td>
  <td align="center"><span style="font-family:calibri;font-size:10;">NET WEIGHT</span></td>
  <td align="center"><span style="font-family:calibri;font-size:10;">Gross Weight</span></td>
 </tr>';
 $sum_package = 0;
 $sum_net_weight = 0;
 $sum_gross_weight = 0;
 foreach ($response['containers_cn'] as $key => $value) {
/*print_r($value);*/

$sum_package += $value[$key] = $value['cntr_packages'];
$sum_net_weight += $value[$key] = $value['cntr_net_weight'];
$sum_gross_weight += $value[$key] = $value['cntr_gross_weight'];

$html .= '<tr>
  <td align="center">'.$value[$key] = $value['number'].'</td>
  <td align="center">'.$value[$key] = $value['line_seal'].'</td>
  <td align="center">'.$value[$key] = $value['shipper_seal'].'</td>
  <td align="center">'.$value[$key] = $value['cntr_packages'].'</td>
  <td align="center">'.$value[$key] = $value['container_type'].'</td>
  <td align="center">'.$value[$key] = $value['cntr_net_weight'].'</td>
  <td align="center">'.$value[$key] = $value['cntr_gross_weight'].'</td>
 </tr>';

}
$html .= '
<tr>
  <td colspan="3" align="center">Total</td>
  <td align="center">'.$sum_package.'</td>
  <td align="center"></td>
  <td align="center">'.$sum_net_weight.'</td>
  <td align="center">'.$sum_gross_weight.'</td>

 </tr>
 </table>
 </td>  
 </tr>
 
 <tr>
  <td colspan="4" align="right"><span style="font-family:calibri;font-size:10;line-height:10px;">SHIP ON BOARD <br/>'.$response['job']['date_issue'].' </span></td>
 </tr>
 <tr>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Freight & Charges Amount</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['charges_amount'].'</span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Freight Payable at</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['payable_at'].'</span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Number of Original MTD(s)</span><br/> <span style="font-family:calibri;font-size:9;"> '.$response['job']['no_of_original'].'</span></td>
  <td align="center"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Place and Date of issue</span><br/> <span style="font-family:calibri;font-size:9;"> GANDHIDHAM - '.$response['job']['date_issue'].'</span></td>
 </tr>
 <tr>
  <td colspan="2"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Other Particulars (if any)</span></td>
  <td colspan="2"><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">For TRANISO LOGISTICS</span><br/><br/><br/><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">Authorised Signatory</span><br/><span style="font-family:calibri-bold;color:rgb(0,112,192);font-size:10;">As An Agent(s)</span></td>
 </tr>
 <tr>
  <td colspan="4" align="center"><span style="font-family:calibri;font-size:8;line-height:12px;">One of the MTD(s) must be surrendered, duly endorsed in exchange for the goods. In witness where of the original MTD all of this tenure and date have been signed in the
number indicated below one of which being accomplished the other(s) to be void. - (TERMS CONTINUED ON BACK HEREOF)
</span></td>
 </tr>
</table>
			</body>
			</html>';
			$pdf->writeHTML($html, true, 0, true, 0);
			/*$this->load->model('export_print');

			$this->export_print->$page($pdf, $data, $letterhead = null);*/

			$pdf->Output("$filename.pdf", 'I');
        }
   		
   }
}

