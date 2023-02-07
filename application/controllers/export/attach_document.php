<?php

class Attach_document extends MY_Controller {
	var $_job_path;
	var $_share;
	var $_share_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_job_path     = FCPATH . 'documents/jobs/';
		$this->_share        = FCPATH . 'share/';
		$this->_share_url    = base_url('share');
		$this->load->model('export');
	}
	
	function index($md5 = '', $page_no = 1) {
		$this->load->helper('filelist');
		$pending = getFileList($this->_share, array('pdf', 'jpeg', 'jpg', 'png', 'bmp', 'gif'));

		// Adding New Document for file from Share
		$attach       = $md5;
		$job_id       = $this->input->post('job_id');
		$child_job_id = $this->input->post('child_job_id');
		$doc_ids      = $this->input->post('document_id');
		$doc_nos      = $this->input->post('doc_no');

		// Adding if its a Job Document
		if ($job_id != false && $doc_ids != false) {
			
			$docdir = $this->export->getDocFolder($this->_job_path, $job_id);

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
								'id'               => $document_id,
								'job_id'           => $job_id,
								'child_job_id'     => ($document_type['attach_to'] == 'Master Job' ? 0 : $child_job_id),
								'date'             => date('d-m-Y'),
								'document_type_id' => $document_type_id,
								'doc_no'           => $doc_nos[intval($page)],
								'file'             => $newfile,
								'pages'            => $page,
								'is_compulsory'    => $document_type['is_compulsory'],
							);
						}
					}
					else {
						$document_type = $this->kaabar->getRow('document_types', $document_type_id);
						$row = array(
							'id'               => $document_id,
							'job_id'           => $job_id,
							'child_job_id'     => ($document_type['attach_to'] == 'Master Job' ? 0 : $child_job_id),
							'date'             => date('d-m-Y'),
							'document_type_id' => $document_type_id,
							'doc_no'           => $doc_nos[intval($page)],
							'file'             => $newfile,
							'pages'            => $page,
							'is_compulsory'    => $document_type['is_compulsory'],
						);
					}
					$this->kaabar->save('attached_documents', $row, array('id' => $document_id));
				}
				setSessionAlert('Changes saved successfully', 'success');
				redirect($this->_clspath.$this->_class);
			}
		}

		if (strlen($md5) > 0) {
			if (file_exists($pending[$md5]['server_path'])) {
				$data['view'] = $pending[$md5];
				$data['view']['url'] = $this->_share_url . '/' . $pending[$md5]['name'];
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
			$sql = "SELECT J.id, J.id2_format, P.name AS party_name, EI.invoice_no, S.container_no
			FROM jobs J INNER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN job_invoices EI ON J.id = EI.job_id
				LEFT OUTER JOIN deliveries_stuffings S ON J.id = S.job_id
			WHERE J.type = 'Export' AND (
				  J.id2_format LIKE '%$search%' OR 
				  J.booking_no LIKE '%$search%' OR 
				  P.name LIKE '%$search%' OR
				  EI.invoice_no LIKE '%$search%' OR 
				  S.container_no LIKE '%$search%')
			ORDER BY J.id DESC, J.id2 DESC
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxChildJobs($job_id) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT CJ.id, CJ.vi_job_no, CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%d-%m-%Y') AS sb_date
			FROM child_jobs CJ 
			WHERE CJ.job_id = $job_id AND (
				  CJ.vi_job_no LIKE '%$search%' OR
				  CJ.sb_no LIKE '%$search%')
			ORDER BY CJ.vi_job_no
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxDocNos($job_id = 0) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT sb_no AS no
			FROM child_jobs
			WHERE job_id = $job_id
			UNION
			SELECT bl_no AS no
			FROM child_jobs
			WHERE job_id = $job_id
			UNION
			SELECT mr_no AS no
			FROM child_jobs
			WHERE job_id = $job_id
			UNION
			SELECT invoice_no AS no
			FROM job_invoices
			WHERE job_id = $job_id";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function getJobDocuments($job_id, $child_job_id = 0) {
		$rows = $this->export->getAttachedDocs($job_id, $child_job_id, 1);

		header('Content-type: text/xml');
		echo '<taconite><replaceContent select=".DocumentType">';
		echo '<option value="0">--- Choose Document Type ---</option>';
		foreach ($rows as $row) {
			echo '<option value="' . $row['id'] . '/' . $row['document_type_id'] . '">' . $row['name'] . ' <span class="orange">' . $row['remarks'] . '</span></option>';
		}
		echo '</replaceContent></taconite>';
	}

	
}
