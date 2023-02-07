<?php

class Staff_document extends MY_Controller {
	var $_share;
	var $_path;
	var $_path_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_table      = 'staff_documents';
		$folder            = 'documents/staffs/';
		$this->_share      = FCPATH . 'share/';
		$this->_path       = FCPATH . $folder;
		$this->_path_url   = base_url($folder);
		$this->load->model('office');
	}
	
	function index($staff_id, $id = 0, $page_no = 1) {
		$staff_id = intval($staff_id);
		if ($staff_id <= 0) {
			echo closeWindow();
		}

		$docdir = $this->office->getDocFolder($this->_path, $staff_id);

		$this->load->helper('filelist');
		$pending = getFileList($this->_share);

		$staff_docs = $this->kaabar->getRow($this->_table, $id);
		if ($staff_docs == FALSE) {
			echo closeWindow();
		}

		if ($staff_docs['id'] > 0) {
			$file = $staff_docs['file'];
			$data['view'] = get_file_info($this->_path.$docdir.$file);
			if ($data['view'] != false) {
				$data['view']['type'] = strtolower(substr($data['view']['name'], -3));
				$data['view']['url'] = $this->_path_url . '/' . $docdir . $data['view']['name'];
			}
		}

		if (isset($data['view']['type']) && strtolower($data['view']['type']) == 'pdf')
			$data['javascript']	= array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');

		$data['party']		= $this->kaabar->getRow('ledgers', $staff_id);
		$data['staff_docs']	= $staff_docs;
		$data['documents']  = $this->office->getAttachedStaffDocs($staff_id);
		$data['staff_id']   = array('staff_id' => $staff_id);
		$data['page_no'] 	= $page_no;
		$data['page_title'] = humanize($this->_class);
		$data['hide_menu']  = true;
		$data['hide_footer'] = true;
		$data['page'] 		= $this->_clspath.$this->_class.'_edit';
		$data['docs_url'] 	= $this->_docs;
		$this->load->view('index', $data);
	}

	function detach($staff_id, $id) {
		$dirarr = array();
		for($i=0; $i < strlen($staff_id); $i++) {
			$dirarr[] = substr($staff_id, $i, 1);
		}
		$docdir = implode('/', $dirarr) . '/';
		$file = $this->kaabar->getField($this->_table, $id, 'id', 'file');
		if (file_exists($this->_path.$docdir.$file)) {
			rename($this->_path.$docdir.$file, $this->_share.$file);
		}
		$this->kaabar->delete($this->_table, $id);
		setSessionAlert('Document Deleted Successfully.', 'success');
		
		echo closeWindow();
		//redirect($this->_clspath.$this->_class.'/index/'.$staff_id);
	}
}
