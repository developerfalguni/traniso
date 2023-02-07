<?php

use mikehaertl\wkhtmlto\Pdf;

class Job_status extends MY_Controller {
	var $_folder;
	var $_share;
	var $_path;
	var $_share_url;
	var $_path_url;

	function __construct() {
		parent::__construct();
		$this->_folder = 'documents/jobs/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url  = base_url('share');
		$this->_path_url   = base_url($this->_folder);
		$this->load->model('export');
		$this->_company = $this->export->getCompanyID();
	}

	function index($job_id = null) {

		$data['page_title'] = "Job Status";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$data['job_id']   = $job_id;
		$this->load->view('index', $data);
	}

	function get($job_id = null) {

		$cont_20 = 0;
		$cont_40 = 0;
		$response = [];
		
		if($job_id){

			$response['success'] = true;
			$job = $this->export->getJob($job_id);
			///////// Find Billing 1 Name using Category
			if($job['billing_party_category'] == 'PARTY')
				$job['party_name'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'AGENT')
				$job['party_name'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'company_name');

			///////// Find Shipper Name using Category
			if($job['shipper_category'] == 'PARTY')
				$job['shipper_name'] = $this->kaabar->getField('parties', $job['shipper_id'], 'id', 'name');
			elseif($job['shipper_category'] == 'AGENT')
				$job['shipper_name'] = $this->kaabar->getField('new_agents', $job['shipper_id'], 'id', 'company_name');
			elseif($job['shipper_category'] == 'CONSIGNEE')
				$job['shipper_name'] = $this->kaabar->getField('consignees', $job['shipper_id'], 'id', 'consignee_name');
			elseif($job['shipper_category'] == 'VENDOR')
				$job['shipper_name'] = $this->kaabar->getField('vendors', $job['shipper_id'], 'id', 'name');

			///////// Find Billing 2 Name using Category
			if($job['billing_party1_category'] == 'PARTY')
				$job['party_name2'] = $this->kaabar->getField('parties', $job['billing_party1_id'], 'id', 'name');
			elseif($job['billing_party1_category'] == 'AGENT')
				$job['party_name2'] = $this->kaabar->getField('new_agents', $job['billing_party1_id'], 'id', 'company_name');
			elseif($job['billing_party1_category'] == 'CONSIGNEE')
				$job['party_name2'] = $this->kaabar->getField('consignees', $job['billing_party1_id'], 'id', 'consignee_name');
			elseif($job['billing_party1_category'] == 'VENDOR')
				$job['party_name2'] = $this->kaabar->getField('vendors', $job['billing_party1_id'], 'id', 'name');

			$job['sub_type'] = explode(',', $job['sub_type']);

			$label = $this->export->getLabelClass();
			$job['status'] = '<span class="badge '.$label[$job['status']].'">'.$job['status'].'</span>';

			$containers = $this->kaabar->getRows('containers', $job_id, 'job_id');
			$costsheets = $this->accounting->getCostSheetDetails($job_id);
			$attach_documents = $this->kaabar->getRows('attachments', ['parent_id' => $job_id, 'type' => 'JOB']);
			$docdir = $this->export->getDocFolder($this->_path, $job_id);

			foreach ($attach_documents as $ad_key => $ad_value) {
				$attach_documents[$ad_key]['filename'] = $this->_path_url.$docdir.$ad_value['name'];
			}

			if($containers){
				foreach ($containers as $key => $value) {
					$cont_20 += $value['size'] === '20' ? 1 : 0;
					$cont_40 += $value['size'] === '40' ? 1 : 0;
				}
			}

			if($costsheets){
				foreach ($costsheets as $key => $value) {

					$docdir = $this->export->getDocFolder($this->_path, $job_id);
				
					$file = $this->_path.$docdir.$value['file'];
					$download = $this->_path_url.$docdir.$value['file'];

					if(file_exists($file) AND $value['file'] != null)
						$costsheets[$key]['upfile'] = $download;
					else
						$costsheets[$key]['upfile'] = NULL;


				}
			}

			$job['cont'] = $cont_20.' X 20 | '.$cont_40.' X 40';
			
			$response['jobs'] = $job;
			$response['containers'] = $containers;
			$response['costsheets'] = $costsheets;
			$response['attach_documents'] = $attach_documents;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Job No Required';
		}	

		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
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
