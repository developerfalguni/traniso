<?php

class Job_document extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
		$this->load->model('import');

		$this->_folder = 'documents/jobs/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url  = base_url('share');
		$this->_path_url   = base_url($this->_folder);

		$this->_table = 'attachments';
	}
	
	function get($job_id = 0){

		$response = [];
		
		if($job_id > 0){

			$files = $this->kaabar->getRows($this->_table, ['parent_id' => $job_id, 'type' => 'JOB']);
				
			$uploads = [];
			foreach ($files as $key => $value) {
				$file = $value['path'].$value['name'];	
				$docdir = $this->office->getDocFolder($this->_path, $job_id);
				if(file_exists($file) AND $value['name'] != null){
					$download = $this->_path_url.$docdir.$value['name'];
					$uploads[$key]['doc_name'] = $value['doc_name'];
					$uploads[$key]['doc_remark'] = $value['doc_remark'];
					$uploads[$key]['doc_path'] = $value['path'].$value['name'];
					$uploads[$key]['filepath'] = $download;
					$uploads[$key]['filename'] = $value['name'];
					$uploads[$key]['created_on'] = $value['created_on'];
					$uploads[$key]['id'] = $value['id'];

					$uploads[$key]['deleteBtn'] = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));
				}
			}
				
			$response['success'] = true;
			$response['files'] = $uploads;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Please select Job';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
	
	public function attach($id)
	{       
	    $this->load->library('upload');
	    $dataInfo = array();
	    $files = $_FILES;

	    $response = [];
	    $responseData = [];

	    $names = $this->input->post('doc_name');

	    if($names != null){
		    foreach ($names as $key => $value) {

		    	$doc_remarks = $this->input->post('doc_remark');

		    	$data = array(
			        'doc_name' => $value,
			        'doc_remark' => $doc_remarks[$key],
			        'updated_by' => Auth::getCurrUID(),
			    );
			    $this->kaabar->save('attachments', $data, ['id' => $key]);
	    	
		    	$responseData[$key]['success'] = true;
		    	$responseData[$key]['message'] = $data['doc_name'].' Successfully Updated';
		    	
		    }
		}

	    $new_names = $this->input->post('new_doc_name');

	    if($new_names != null){
		    foreach ($new_names as $key => $value) {

		    	$new_doc_remarks = $this->input->post('new_doc_remark');

	    		$_FILES['new_file']['name']= $files['new_file']['name'][$key];
		        $_FILES['new_file']['type']= $files['new_file']['type'][$key];
		        $_FILES['new_file']['tmp_name']= $files['new_file']['tmp_name'][$key];
		        $_FILES['new_file']['error']= $files['new_file']['error'][$key];
		        $_FILES['new_file']['size']= $files['new_file']['size'][$key];    

		        $this->upload->initialize($this->set_upload_options($id));

		        if($this->upload->do_upload('new_file')){

		        	$dataInfo[$key]['info'] = $this->upload->data();
		        	$dataInfo[$key]['msg'] = $value;
		        	$dataInfo[$key]['status'] = true;

		        	$data = array(
				        'parent_id' => $id,
				        'doc_name' => $value,
				        'doc_remark' => $new_doc_remarks[$key],
				        'name' => $dataInfo[$key]['info']['file_name'],
				        'path' => $dataInfo[$key]['info']['file_path'],
				        'size' => $dataInfo[$key]['info']['file_size'],
				        'mime' => $dataInfo[$key]['info']['file_type'],
				        'created_by' => Auth::getCurrUID(),
				        'type' => 'JOB',
				    );
				    $this->kaabar->save('attachments', $data);


		        }
				else
		        {
		        	$dataInfo[$key]['info'] = $this->upload->display_errors();
		        	$dataInfo[$key]['msg'] = $value;
		        	$dataInfo[$key]['status'] = false;
		        }

		    	
		    }


		    foreach ($dataInfo as $k => $v) {

		    	$responseData[$key]['success'] = $v['status'];
		    	if($v['status'] == true)
		    		$responseData[$key]['message'] = 'DOC Name : '. $v['msg'].' Successfully Uploaded';
		    	if($v['status'] == false)
		    		$responseData[$key]['message'] = 'DOC Name : ' .$v['msg']. ' - ' .$v['info'];


		    }

		    $response['success'] = true;
			$response['messages'] = $responseData;
		    
		}
		else{

			$response['success'] = false;
			$response['messages'] = 'Something Wrong';
		}
	    header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
		
	}

	private function set_upload_options($id)
	{   
	    //upload an image options
	    $docdir = $this->office->getDocFolder($this->_path, $id);
	    $config = array();
	    $config['upload_path'] 	 = $this->_path.$docdir; 
		$config['allowed_types'] = 'jpg|jpeg|png|gif|zip|pdf'; 
	    $config['max_size']      = '0';
	    $config['overwrite']     = FALSE;

	    return $config;
	}

	function detach() {
    	$unlink = '';
		$json = [];
		if ($this->input->is_ajax_request()) {


			$doc_id = $this->input->post('doc_id');
			$job_id = $this->input->post('job_id');

			if($job_id AND $doc_id){

				$docdir = $this->office->getDocFolder($this->_path, $job_id);
				$file = $this->kaabar->getField($this->_table, $doc_id, 'id', 'name');
				$unlink = $this->_path.$docdir.$file;
				
				if($this->db->delete($this->_table, ['id' => $doc_id])){
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



}
