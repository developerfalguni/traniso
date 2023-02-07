<?php
class Forwarder extends MY_Controller {

	var $_type;
	var $_folder;
	var $_share;
	var $_path;
	var $_share_url;
	var $_path_url;

	function __construct() {
		parent::__construct();
		$this->_table = "jobs";
		$this->load->model('export');

		$this->_folder = 'documents/jobs/forwarder/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url  = base_url('share');
		$this->_path_url   = base_url($this->_folder);
	}
	
	function index($job_id = 0, $starting_row = 0) {

		$docdir = $this->export->getDocFolder($this->_path, $job_id);
		

		$data['job_id']  = $job_id;
		$jobs = $this->kaabar->getRow('jobs', $job_id);
		$data['jobs'] = empty($jobs) ? $row : $jobs;

		$types = [
			'OWN'	=> 'OWN',
			'Nomination' => 'Nomination',
			'Booking Agent' => 'Booking Agent'
		];

		$blTypes = [
			'Original'	=> 'Original',
			'SeaWay' => 'SeaWay',
			'TELEX RELEASE' => 'TELEX RELEASE',
			'RFS BL' => 'RFS BL',
			'FCR' => 'FCR'
		];
		////

		$data['jobs']['booking_type_dd'] = form_dropdown('booking_type', $types, $data['jobs']['booking_type'], 'class="form-control form-control-sm"');
		$data['jobs']['hbl_type_dd'] = form_dropdown('hbl_type', $blTypes, $data['jobs']['hbl_type'], 'class="form-control form-control-sm"');
		$data['jobs']['mbl_type_dd'] = form_dropdown('mbl_type', $blTypes, $data['jobs']['mbl_type'], 'class="form-control form-control-sm"');
		
		$quote_copy = null;
		if($data['jobs']['fwd_quote_copy']){
			$quote_copy = $this->_path_url.$docdir.$data['jobs']['fwd_quote_copy'];
		}
		
		$quote_email = null;
		if($data['jobs']['fwd_quote_email']){
			$quote_email = $this->_path_url.$docdir.$data['jobs']['fwd_quote_email'];
		}

		$data['jobs']['quote_copy'] = $quote_copy;
		$data['jobs']['quote_email'] = $quote_email;
		$data['bl_type'] = explode(',', $data['jobs']['bl_type']);

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	function edit($job_id = 0) {

		$response = [];
		$docdir = $this->export->getDocFolder($this->_path, $job_id);
      
		if(isset($_FILES['quote_email']['name'])){
			$tmp = $_FILES['quote_email']['tmp_name'];
		  	//Make sure we have a file path
		  	if ($tmp != ""){
		    	//Setup our new file path
		    	$quote_email = $this->_path.$docdir.$_FILES['quote_email']['name'];
		    	//Upload the file into the temp dir
		    	if(move_uploaded_file($tmp, $quote_email))
		    		$quote_email_name = $_FILES['quote_email']['name'];
		    	else
		    	    $quote_email_name = null;
		    }
		}

		if(isset($_FILES['quote_copy']['name'])){
			$name = $_FILES['quote_copy']['name'];
			$tmpFilePath = $_FILES['quote_copy']['tmp_name'];
		  	//Make sure we have a file path
		  	if ($tmpFilePath != ""){
		    	//Setup our new file path
		    	$quote_copy = $this->_path.$docdir.$_FILES['quote_copy']['name'];
		    	//Upload the file into the temp dir
		    	if(move_uploaded_file($tmpFilePath, $quote_copy))
		    		$quote_copy_name = $_FILES['quote_copy']['name'];
		    	else
		    	    $quote_copy_name = null;
		    }
		}	
		
		$jobs = array(
			'bl_type'			=> join(',', $this->input->post('bl_type')),
			'pricing_detail'	=> $this->input->post('pricing_detail'),
			'contact_party'		=> $this->input->post('contact_party'),
			'booking_type'		=> $this->input->post('booking_type'),
			'overseas_agent'	=> $this->input->post('overseas_agent'),
			'mbl_no'			=> $this->input->post('mbl_no'),
			'mbl_date'			=> $this->input->post('mbl_date'),
			'mbl_type'			=> $this->input->post('mbl_type'),
			'hbl_no'			=> $this->input->post('hbl_no'),
			'hbl_date'			=> $this->input->post('hbl_date'),
			'hbl_type'			=> $this->input->post('hbl_type'),
			'fwd_buy'			=> $this->input->post('fwd_buy'),
			'fwd_sell'			=> $this->input->post('fwd_sell'),
			'fwd_quote_copy'	=> isset($quote_copy_name) ? $quote_copy_name : $this->input->post('fwd_quote_copy'),
			'fwd_quote_email'	=> isset($quote_email_name) ? $quote_email_name : $this->input->post('fwd_quote_email'),
			'fwd_remarks'		=> $this->input->post('fwd_remarks'),

    	);
    	
   
    	$this->kaabar->save('jobs', $jobs, ['id' => $job_id]);
    	
		$response['success'] = true;
		$response['messages'] = 'Successfull Updated';

		//header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}		        		

	function minifier($code) {
	    $search = array(
	          
	        // Remove whitespaces after tags
	        '/\>[^\S ]+/s',
	          
	        // Remove whitespaces before tags
	        '/[^\S ]+\</s',
	          
	        // Remove multiple whitespace sequences
	        '/(\s)+/s',
	          
	        // Removes comments
	        '/<!--(.|\s)*?-->/'
	    );
	    $replace = array('>', '<', '\\1');
	    $code = preg_replace($search, $replace, $code);
	    return $code;
	}
	
	function deleteattach() {

		$job_id = $this->input->post('job_id');
		$param = $this->input->post('param');

		$response = [];

		if($job_id AND $param)
		{

			$file = $this->kaabar->getField('jobs', $job_id, 'id', $param);
			$docdir = $this->export->getDocFolder($this->_path, $job_id);
			$path = $this->_path.$docdir.$file;
			if($this->kaabar->save('jobs', [$param => ''], ['id' => $job_id])){
				if(file_exists($path))
					unlink($path);

				$response['success'] = true;
				$response['messages'] = 'Attachment Successfull Deleted';
			}
			else{
				$response['success'] = false;
				$response['messages'] = 'Attachment not Deleted';
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
}