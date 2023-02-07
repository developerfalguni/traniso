<?php

class Attach_document extends MY_Controller {
	var $_party_path;
	var $_job_path;
	var $_staff_path;
	var $_share;
	var $_share_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_party_path   = FCPATH . 'documents/parties/';
		$this->_job_path     = FCPATH . 'documents/jobs/';
		$this->_staff_path   = FCPATH . 'documents/staffs/';
		$this->_share        = FCPATH . 'share/';
		$this->_share_url    = base_url('share');
		$this->load->model('import');
	}
	
	function index($md5 = '', $page_no = 1) {
		$this->load->helper('filelist');
		$pending = getFileList($this->_share, array('pdf', 'jpeg', 'jpg', 'png', 'bmp', 'gif', 'prn', 'txt', 'htm', 'html'));

		// Adding New Document for file from Share
		$attach    = $md5;
		$job_id    = $this->input->post('job_id');
		$party_id  = $this->input->post('party_id');
		$staff_id  = $this->input->post('staff_id');
		$doc_ids   = $this->input->post('document_id');

		// Adding if its a Job Document
		if ($job_id != false && $doc_ids != false) {
			
			$docdir = $this->import->getDocFolder($this->_job_path, $job_id);

			$new_doc_ids = array();
			foreach($doc_ids as $page => $doc_id) {
				if ($doc_id === '0') continue;
				if (isset($new_doc_ids[$doc_id]))
					$new_doc_ids[$doc_id] .= ', ' . $page;
				else
					$new_doc_ids[$doc_id] = $page;
			}

			if (count($new_doc_ids) > 0) {
				$file = $pending[$attach]['name'];
				if (file_exists($this->_share.$file)) {
					$newfile = uniqid().'.'.$pending[$attach]['type'];
					rename($this->_share.$file, $this->_job_path.$docdir.$newfile);
				}

				foreach($new_doc_ids as $doc_id => $page) {
			
					$did = explode('/', $doc_id);
					$document_id      = $did[0];
					$document_type_id = $did[1];

					if ($document_id > 0) {
						$row = $this->kaabar->getRow('attached_documents', $document_id);
						if (strlen($row['file']) == 0 || ! file_exists($this->_job_path.$docdir.$row['file'])) {
							$row['pages'] = $page;
							$row['file']  = $newfile;
						}
						else {
							$document_type = $this->kaabar->getRow('document_types', $document_type_id);
							$document_id = 0;
							$row = array(
								'id'		       => $document_id,
								'job_id' 	       => $job_id,
								'date'			   => date('d-m-Y'),
								'document_type_id' => $document_type_id,
								'file' 		       => $newfile,
								'pages' 	       => $page,
								'is_compulsory'    => $document_type['is_compulsory'],
								'is_pending'       => $document_type['is_pending']
							);
						}
					}
					else {
						$document_type = $this->kaabar->getRow('document_types', $document_type_id);
						$row = array(
							'id'		       => $document_id,
							'job_id' 	       => $job_id,
							'date'			   => date('d-m-Y'),
							'document_type_id' => $document_type_id,
							'file' 		       => $newfile,
							'pages' 	       => $page,
							'is_compulsory'    => $document_type['is_compulsory'],
							'is_pending'       => $document_type['is_pending']
						);
					}
					$this->kaabar->save('attached_documents', $row, array('id' => $document_id));
				}
				setSessionAlert('Changes saved successfully', 'success');
				redirect($this->_clspath.$this->_class);
			}
		}

		// Check if documents are KYC of Party
		if ($party_id != false && $doc_ids != false) {
			
			$docdir = $this->import->getDocFolder($this->_party_path, $party_id);

			$new_doc_ids = array();
			foreach($doc_ids as $page => $doc_id) {
				if ($doc_id === '0') continue;
				if (isset($new_doc_ids[$doc_id]))
					$new_doc_ids[$doc_id] .= ', ' . $page;
				else
					$new_doc_ids[$doc_id] = $page;
			}

			if (count($new_doc_ids) > 0) {
				$file = $pending[$attach]['name'];
				if (file_exists($this->_share.$file)) {
					$newfile = uniqid().'.'.$pending[$attach]['type'];
					rename($this->_share.$file, $this->_party_path.$docdir.$newfile);
				}

				foreach($new_doc_ids as $doc_id => $page) {
			
					$did = explode('/', $doc_id);
					$document_id      = $did[0];
					$document_type_id = $did[1];

					if ($document_id > 0) {
						$row = $this->kaabar->getRow('kyc_documents', $document_id);
						$row['pages'] = $page;
						$row['file']  = $newfile;
					}
					else {
						$document_type = $this->kaabar->getRow('kyc_document_types', $document_type_id);
						$row = array(
							'id'		=> $document_id,
							'party_id'  => $party_id,
							'kyc_document_type_id' => $document_type_id,
							'file' 		=> $newfile,
							'pages' 	=> $page
						);
					}
					$this->kaabar->save('kyc_documents', $row, array('id' => $document_id));
				}
				setSessionAlert('Changes saved successfully', 'success');
				redirect($this->_clspath.$this->_class);
			}
		}


		// Check if documents are of Staff
		if ($staff_id != false && $doc_ids != false) {
			
			$docdir = $this->import->getDocFolder($this->_staff_path, $staff_id);

			$new_doc_ids = array();
			foreach($doc_ids as $page => $doc_id) {
				if ($doc_id === '0') continue;
				if (isset($new_doc_ids[$doc_id]))
					$new_doc_ids[$doc_id] .= ', ' . $page;
				else
					$new_doc_ids[$doc_id] = $page;
			}

			if (count($new_doc_ids) > 0) {
				$file = $pending[$attach]['name'];
				if (file_exists($this->_share.$file)) {
					$newfile = uniqid().'.'.$pending[$attach]['type'];
					rename($this->_share.$file, $this->_staff_path.$docdir.$newfile);
				}

				foreach($new_doc_ids as $doc_id => $page) {
			
					$did = explode('/', $doc_id);
					$document_id      = $did[0];
					$document_type_id = $did[1];

					if ($document_id > 0) {
						$row = $this->kaabar->getRow('staff_documents', $document_id);
						$row['pages'] = $page;
						$row['file']  = $newfile;
					}
					else {
						$document_type = $this->kaabar->getRow('staff_document_types', $document_type_id);
						$row = array(
							'id'		=> $document_id,
							'staff_id'  => $staff_id,
							'staff_document_type_id' => $document_type_id,
							'file' 		=> $newfile,
							'pages' 	=> $page
						);
					}

					$this->kaabar->save('staff_documents', $row, array('id' => $document_id));
				}
				setSessionAlert('Changes saved successfully', 'success');
				redirect($this->_clspath.$this->_class);
			}
		}

		// echo "<pre>";
		// print_r($md5);
		// echo "<pre>";
		// print_r($pending);

		// exit;

		if (strlen($md5) > 0) {

			if (file_exists($pending[$md5]['server_path'])) {
				$data['view'] = $pending[$md5];
				$data['view']['url'] = $this->_share_url . '/' . $pending[$md5]['name'];
				if (in_array($data['view']['type'], array('prn', 'txt', 'htm', 'html'))) {
					$this->load->helper('file');
					$data['view']['contents'] = read_file($pending[$md5]['server_path']);
				}
			}
		}
		
		$data['javascript']	= array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');

		$data['md5'] 		= $md5;
		$data['page_no'] 	= $page_no;
		$data['pending'] 	= $pending;
		$data['page_title'] = humanize($this->_class);
		$data['page'] 		= $this->_clspath.$this->_class;
		$data['docs_url'] 	= $this->_docs;
		$this->load->view('index', $data);
	}

	function ajaxJobs() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT J.id, PL.name AS party_name, 
				J.bl_no, DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date,
				J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date,
				CONCAT(J.packages, ' ', PK.code) AS packages, 
				CONCAT(J.net_weight, ' ', J.net_weight_unit) AS net_weight
			FROM (jobs J INNER JOIN parties PL ON J.party_id = PL.id)
				LEFT OUTER JOIN package_types PK ON J.package_type_id = PK.id
			WHERE J.type = 'Import' AND 
				  J.bl_no LIKE '%$search%' OR 
				  DATE_FORMAT(J.bl_date, '%d-%m-%Y') LIKE '%$search%' OR
				  J.be_no LIKE '%$search%' OR
				  DATE_FORMAT(J.be_date, '%d-%m-%Y') LIKE '%$search%' OR
				  CONCAT(J.packages, ' ', PK.code) LIKE '%$search%' OR
				  CONCAT(J.net_weight, ' ', J.net_weight_unit) LIKE '%$search%' OR
				  PL.name LIKE '%$search%'
			ORDER BY PL.name
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxStaff() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT S.id, S.category, CONCAT(S.title, ' ', S.firstname, ' ', S.middlename, ' ', S.lastname) AS name
			FROM staffs S
			WHERE S.category LIKE '%$search%' OR 
				  S.firstname LIKE '%$search%' OR 
				  S.middlename LIKE '%$search%' OR
				  S.lastname LIKE '%$search%' 
			ORDER BY S.category, S.firstname, S.lastname
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function getJobDocuments($job_id) {
		$rows = $this->import->getAttachedDocuments($job_id, 1);

		header('Content-type: text/xml');
		echo '<taconite><replaceContent select=".DocumentType">';
		echo '<option value="0">--- Choose Document Type ---</option>';
		foreach ($rows as $row) {
			echo '<option value="' . $row['id'] . '/' . $row['document_type_id'] . '">' . $row['name'] . ' <span class="orange">' . $row['remarks'] . '</span></option>';
		}
		echo '</replaceContent></taconite>';
	}

	function getPartyDocuments($party_id) {
		$this->load->model('office');
		$rows = $this->office->getAttachedKycs($party_id, 1);

		header('Content-type: text/xml');
		echo '<taconite><replaceContent select=".DocumentType">';
		echo '<option value="0">--- Choose Document Type ---</option>';
		foreach ($rows as $row) {
			echo '<option value="' . $row['id'] . '/' . $row['kyc_document_type_id'] . '">' . $row['name'] . '</option>';
		}
		echo '</replaceContent></taconite>';
	}

	function getStaffDocuments($staff_id) {
		$this->load->model('office');
		$rows = $this->office->getAttachedStaffDocs($staff_id, 1);

		header('Content-type: text/xml');
		echo '<taconite><replaceContent select=".DocumentType">';
		echo '<option value="0">--- Choose Document Type ---</option>';
		foreach ($rows as $row) {
			echo '<option value="' . $row['id'] . '/' . $row['staff_document_type_id'] . '">' . $row['name'] . '</option>';
		}
		echo '</replaceContent></taconite>';
	}
}
