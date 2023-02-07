<?php

class Attached_document extends MY_Controller {
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
		$this->load->model('import');
	}
	
	function od_index($job_id = 0, $child_job_id = 0, $id = 0, $md5 = '', $page_no = 1) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$job_id = intval($job_id);
		$id = intval($id);
		if ($job_id <= 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}

		if ($this->input->post('job_id')) {
			$is_compulsory = $this->input->post('is_compulsory');
			$this->db->query("UPDATE attached_documents SET is_compulsory = 'No' WHERE job_id = $job_id");
			if ($is_compulsory) {
				foreach ($is_compulsory as $dtid) {
					$did = $this->kaabar->getField('attached_documents', array('job_id' => $job_id, 'child_job_id' => $child_job_id, 'document_type_id' => $dtid), 'id', 'id');
					if ($did == 0) {
						$this->db->insert('attached_documents', array('job_id' => $job_id, 'child_job_id' => $child_job_id, 'document_type_id' => $dtid, 'is_compulsory' => 'Yes'));
					}
					else {
						$this->db->update('attached_documents', array('is_compulsory' => 'Yes'), array('id' => $did));
					}
				}
			}
			setSessionAlert('SAVED', 'success');
		}

		$data['job_id']       = array('job_id' => $job_id);
		$data['child_job_id'] = array('id' => $child_job_id);
		$data['jobs']         = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		$data['documents']    = $this->import->getAttachedDocs($job_id, $child_job_id, 1, 0);
		$data['label_class']  = $this->import->getLabelClass();
		$data['attach']       = $this->kaabar->getRows($this->_table, $job_id, 'job_id');
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function index($job_id = 0, $id = 0, $child_job_id = 0, $md5 = '', $page_no = 1) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$job_id = intval($job_id);
		$id = intval($id);
		if ($job_id <= 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}

		$data['job_id']      = $job_id;
		$data['jobs']        = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		$data['documents']   = $this->kaabar->getRows($this->_table, $job_id, 'job_id');

		foreach ($data['documents'] as $key => $value) {
			$deleteBtn = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));

			$data['documents'][$key]['delete_btn'] = $deleteBtn;
		}

		$data['label_class'] = $this->import->getLabelClass();

		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class;
		$data['hide_title'] = true;
		$data['docs_url']   = $this->_docs;

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	function attach($job_id = 0, $md5 = '', $page_no = 1) {
		// if (! Auth::hasAccess()) {
		// 	setSessionError('NO_PERMISSION');
		// 	redirect('main');
		// }

		$docdir = $this->import->getDocFolder($this->_path, $job_id);
		$response = [];
		$kbr_total = 0;
		$new_total = 0;

		/////////// FOR NEW DOC
		// Count # of uploaded files in array
		if(isset($_FILES['new_file']['name']))
			$new_total = count($_FILES['new_file']['name']);
		// Count # of uploaded files in array
		$new_total = count($_FILES['new_file']['name']);
		if($new_total > 0){
			// Loop through each file
			foreach ($_FILES['new_file']['name'] as $key => $value) {
			  	//Get the temp file path
			  	$tmpFilePath = $_FILES['new_file']['tmp_name'][$key];
			  	//Make sure we have a file path
			  	if ($tmpFilePath != ""){
			    	//Setup our new file path
			    	$newFilePath = $this->_path.$docdir.$_FILES['new_file']['name'][$key];
			    	//Upload the file into the temp dir
			    	if(move_uploaded_file($tmpFilePath, $newFilePath))
			    		$newfilesList[$key] = $_FILES['new_file']['name'][$key];
			    	else
			    		$newfilesList[$key] = null;
			    }
			}
		}

		$new_document_ids = $this->input->post('new_document_name');
		$new_document_ids = array_filter($new_document_ids);

		if ($new_document_ids != null) {

			$date         	= $this->input->post('new_date');
			$remarks 		= $this->input->post('new_remarks');
			//$file         	= $this->input->post('file');
			
			foreach ($new_document_ids as $index => $new_document_id) {

				$data = array(
					'job_id'      		=> $job_id,
					'date'    			=> $date[$index],
					'document_name'  	=> $new_document_id,
					'remarks'       	=> $remarks[$index],
					'received_date'		=> date('Y-m-d'),
					'file'         		=> isset($newfilesList[$index]) ? $newfilesList[$index] : NULL,
				);
				
				$this->kaabar->save($this->_table, $data);
			}
		}

		//// FOR DELETE ROWS
		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete($this->_table, $index);
				}
			}
		}

		/////// FOR EXISTING DOCUMENTS
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

		$kbr_document_ids = $this->input->post('kbr_document_name');
		$kbr_document_ids = array_filter($kbr_document_ids);

		if ($kbr_document_ids != null) {

			$date         	= $this->input->post('kbr_date');
			$remarks 		= $this->input->post('kbr_remarks');
			

			$data = [];

			foreach ($kbr_document_ids as $index => $kbr_document_id) {
				if ($kbr_document_ids[$index] > 0) {

					$data = array(
						'date'    			=> $date[$index],
						'document_name' 	=> $kbr_document_id,
						'remarks'       	=> $remarks[$index],
					);
					if(isset($kbrfilesList[$index]))
						$data['file'] = $kbrfilesList[$index];
					$this->kaabar->save($this->_table, $data, ['id' => $index]);
				}
				
			}
			
		}


		// if ($document_ids != null && Auth::hasAccess(Auth::UPDATE)) {

		// 	$file = $pending[$attach]['name'];
		// 	if (file_exists($this->_share.$file)) {
		// 		$newfile = uniqid().'.'.$pending[$attach]['type'];
		// 		rename($this->_share.$file, $this->_path.$docdir.$newfile);
		// 	}

		// 	foreach($document_ids as $index => $did) {
		// 		$document_type_ids = $this->input->post('document_type_id');
		// 		$dates = $this->input->post('date');
		// 		$pages = $this->input->post('pages');
		// 		$remarks = $this->input->post('remarks');
		// 		$row = array(
		// 			'id'      => $did,
		// 			'job_id'  => $job_id,
		// 			'document_type_id' => $document_type_ids[$index],
		// 			'date'    => $dates[$index],
		// 			'file'    => $newfile,
		// 			'pages'   => $pages[$index],
		// 			'remarks' => $remarks[$index]
		// 		);
		// 		$this->kaabar->save($this->_table, $row, array('id' => $did));
		// 	}

		// 	redirect($this->_clspath.$this->_class."/index/$job_id");
		// }

		// if (strlen($md5) > 0) {
		// 	if (file_exists($pending[$md5]['server_path'])) {
		// 		$data['view'] = $pending[$md5];
		// 		$data['view']['url'] = $this->_share_url . '/' . $pending[$md5]['name'];
		// 		if (in_array($data['view']['type'], array('prn', 'txt', 'htm', 'html'))) {
		// 			$this->load->helper('file');
		// 			$data['view']['contents'] = read_file($pending[$md5]['server_path']);
		// 		}
		// 	}
		// }

		// $data['javascript']	= array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');

		// $data['job_id'] 	= array('id' => $job_id);
		// $data['md5'] 		= $md5;
		// $data['page_no'] 	= $page_no;
		// $data['jobs'] 	 	= $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		// $data['pending'] 	= $pending;
		// $data['page_title'] = humanize($this->_class);
		// $data['hide_title'] = true;
		// $data['page'] 		= $this->_clspath.$this->_class.'_attach';
		// $data['docs_url'] 	= $this->_docs;
		// $this->load->view('index', $data);

		$response['success'] = true;
		$response['messages'] = 'Successfull Updated';

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);

	}

	function edit($job_id = 0, $child_job_id = 0, $id = 0, $page_no = 1) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$job_id = intval($job_id);
		$id 	= intval($id);
		if ($job_id <= 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}

		$docdir = $this->import->getDocFolder($this->_path, $job_id);

		$data['job_id']           = array('id' => $job_id);
		$data['child_job_id']     = array('id' => $child_job_id);
		$data['id']               = array('id' => $id);
		$data['jobs']             = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		$data['documents']        = $this->import->getAttachedDocs($job_id, $child_job_id, 0, 1);
		$data['document']         = $this->import->getAttachedDoc($job_id, $id);
		$data['document']['type'] = pathinfo($this->_folder . $docdir . $data['document']['file'], PATHINFO_EXTENSION);
		$data['document']['url']  = base_url($this->_folder . $docdir . $data['document']['file']);
		if (in_array($data['document']['type'], array('prn', 'txt', 'htm', 'html'))) {
			$this->load->helper('file');
			$data['document']['contents'] = read_file($this->_path . $docdir . $data['view']['file']);
		}

		$data['javascript']	= array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');
		
		foreach ($data['documents'] as $doc) {
			if ($id == $doc['id']) {
				$file = $doc['file'];
				break;
			}
		}

		// Add / Edit / Del Document Type from Attached File
		$document_ids = $this->input->post('new_did');
		if ($document_ids != null && Auth::hasAccess(Auth::UPDATE)) {
			foreach($document_ids as $index => $did) {
				$document_type_ids = $this->input->post('new_dtid');
				$pages   = $this->input->post('new_pages');
				$dates   = $this->input->post('new_date');
				$doc_nos = $this->input->post('new_doc_no');
				$remarks = $this->input->post('new_remarks');
				if ($did > 0 OR $document_type_ids[$index] > 0) {
					$row = array(
						'id'      => $did,
						'job_id'  => $job_id,
						'document_type_id' => $document_type_ids[$index],
						'date'    => $dates[$index],
						'doc_no'  => $doc_nos[$index],
						'file'    => $file,
						'pages'   => $pages[$index],
						'remarks' => $remarks[$index]
					);
					$this->kaabar->save($this->_table, $row, array('id' => $did));
				}
			}


			// Saving Existing Current Document
			$row = array(
				'date' 	=> $this->input->post('date'),
				'pages' => $this->input->post('pages'),
				'visible_user_ids' => (is_array($this->input->post('visible_user_ids')) ? implode(',', $this->input->post('visible_user_ids')) : ''),
				'remarks' 	=> $this->input->post('remarks')
			);
			$this->kaabar->save($this->_table, $row, array('id' => $id));
			setSessionAlert('SAVED', 'success');

			// Reload changes
			$data['documents']  = $this->import->getAttachedDocs($job_id, 0, 1);
			$data['document'] 	= $this->import->getAttachedDoc($job_id, $id);
			$data['document']['url'] = base_url($this->_folder . $docdir . $data['document']['file']);
		}
		
		$data['page_no']    = $page_no;
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function detach($job_id = 0, $id = 0) {
		if (Auth::hasAccess(Auth::DELETE)) {

			$docdir   = $this->import->getDocFolder($this->_path, $job_id);
			$document = $this->import->getAttachedDoc($job_id, $id);
			$this->kaabar->save($this->_table, array('file' => '', 'pages' => ''), array('id' => $id));

			if ($this->kaabar->getField($this->_table, $document['file'], 'file', 'id') == false) {
				$file = $document['file'];
				rename($this->_path.$docdir.$file, $this->_share.$file);
			}
			setSessionAlert('Document Detached Successfully', 'success');
		}
		else
			setSessionError('NO_PERMISSION');

		redirect($this->_clspath.$this->_class."/index/$job_id");
	}

	function delete($job_id = 0, $id = 0) {
		if (Auth::hasAccess(Auth::DELETE)) {
			$docdir   = $this->import->getDocFolder($this->_path, $job_id);
			$document = $this->import->getAttachedDoc($job_id, $id);
			$this->kaabar->delete($this->_table, $id);

			if ($this->kaabar->getField($this->_table, $document['file'], 'file', 'id') == false) {
				$file = $document['file'];
				rename($this->_path.$docdir.$file, $this->_share.$file);
			}
			setSessionAlert('DOC_DELETED', 'success');
		}
		else
			setSessionError('NO_PERMISSION');
		
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function email($job_id, $id) {
		$this->load->helper(array('file', 'email'));
		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');

		if (count($to) > 0) {
			$job      = $this->kaabar->getRow('jobs', $id);
			$docdir   = $this->import->getDocFolder($this->_path, $job_id);
			$document = $this->import->getAttachedDocument($job_id, $id);
			if ($this->kaabar->getField($this->_table, $document['file'], 'file', 'id') == false) {
				$file = $this->_path.$docdir.$document['file'];
			}
			
			$config = array(
				'protocol' => 'smtp',
				'smtp_timeout' => 30,
				'smtp_host' => Settings::get('smtp_host'),
				'smtp_port' => Settings::get('smtp_port'),
				'smtp_user' => Settings::get('smtp_user'),
				'smtp_pass' => Settings::get('smtp_password'),
				'newline'   => "\r\n",
				'crlf'      => "\r\n"
			);
			$this->load->library('email', $config);

			$this->email->from(Settings::get('smtp_user'));
			$this->email->to($to);
			$this->email->cc($cc);
			$this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->attach($file);
			$this->email->send();
			echo $this->email->print_debugger(); exit;
			setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
		}

		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function ajaxDocuments($job_id) {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT AD.id, DT.id AS document_type_id, DT.sr_no, DT.name, AD.remarks, DT.is_compulsory
		FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
		WHERE AD.job_id = $job_id AND LENGTH(AD.file) = 0 AND DT.name LIKE '%$search%'
		UNION
		SELECT 0 AS id, DT.id AS document_type_id, DT.sr_no, DT.name, '' AS remarks, DT.is_compulsory 
		FROM document_types DT INNER JOIN jobs J ON (DT.product_id = J.product_id AND DT.type = J.type AND DT.cargo_type = J.cargo_type)
		WHERE J.id = $job_id AND DT.name LIKE '%$search%' AND DT.id NOT IN (
			SELECT document_type_id 
			FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
			WHERE AD.job_id = $job_id
		)
		ORDER BY is_compulsory DESC, sr_no";
			$this->kaabar->getJson($sql);
		}
		else
			echo "Access Denied";
	}

	function ajaxEmail($job_id = 0) {
		if ($this->_is_ajax) {
			$search    = addslashes(strtolower($this->input->post('term')));
			if ($job_id == 0) {
				$ledger_id = $this->input->post('ledger_id');
				$sql = "SELECT DISTINCT P.name, P.email
					FROM parties P INNER JOIN ledgers L ON P.id = L.party_id
					WHERE L.id IN (" . (is_array($ledger_id) ? implode(",", $ledger_id) : $ledger_id) . ") 
					UNION
					SELECT PC.person_name AS name, PC.email
					FROM party_contacts PC INNER JOIN parties P ON PC.party_id = P.id
						INNER JOIN ledgers L ON P.id = L.party_id
					WHERE L.id IN (" . (is_array($ledger_id) ? implode(",", $ledger_id) : $ledger_id) . ")";
			}
			else {
				$party_id = $this->kaabar->getField('jobs', $job_id, 'id', 'party_id');
				$sql = "SELECT P.name, P.email FROM parties P WHERE P.id  = $party_id
					UNION
					SELECT PC.person_name AS name, PC.email FROM party_contacts PC WHERE PC.party_id = $party_id";
			}
			$this->kaabar->getJson($sql);
		}
		else {
			echo 
			"Access Denied";
		}
	}
}
