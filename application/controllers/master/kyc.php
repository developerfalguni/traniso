<?php

class Kyc extends MY_Controller {
	var $_share;
	var $_path;
	var $_path_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_table      = 'kyc_documents';
		$folder            = 'documents/parties/';
		$this->_share      = FCPATH . 'share/';
		$this->_path       = FCPATH . $folder;
		$this->_path_url   = base_url($folder);
		$this->load->model('office');
	}
	
	function index($party_id, $id = 0, $page_no = 1) {
		$party_id = intval($party_id);
		if ($party_id <= 0) {
			echo closeWindow();
		}

		$docdir = $this->office->getDocFolder($this->_path, $party_id);

		$this->load->helper('filelist');
		$pending = getFileList($this->_share);

		$kyc = $this->kaabar->getRow($this->_table, $id);
		if ($kyc == FALSE) {
			echo closeWindow();
		}

		if ($kyc['id'] > 0) {
			$file = $kyc['file'];
			$data['view'] = get_file_info($this->_path.$docdir.$file);
			if ($data['view'] != false) {
				$data['view']['type'] = strtolower(substr($data['view']['name'], -3));
				$data['view']['url'] = $this->_path_url . '/' . $docdir . $data['view']['name'];
			}
		}

		if (isset($data['view']['type']) && strtolower($data['view']['type']) == 'pdf')
			$data['javascript']	= array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');

		$data['party']       = $this->kaabar->getRow('parties', $party_id);
		$data['kyc']         = $kyc;
		$data['documents']   = $this->office->getKycDocuments($party_id);
		$data['party_id']    = array('party_id' => $party_id);
		$data['page_no']     = $page_no;
		$data['page_title']  = humanize($this->_class);
		$data['hide_title']  = true;
		$data['hide_menu']   = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']    = $this->_docs;
		$this->load->view('index', $data);
	}

	function detach($party_id, $id) {
		$dirarr = array();
		for($i=0; $i < strlen($party_id); $i++) {
			$dirarr[] = substr($party_id, $i, 1);
		}
		$docdir = implode('/', $dirarr) . '/';
		$file = $this->kaabar->getField($this->_table, $id, 'id', 'file');
		if (file_exists($this->_path.$docdir.$file)) {
			rename($this->_path.$docdir.$file, $this->_share.$file);
		}
		$this->kaabar->delete($this->_table, $id);
		setSessionAlert('Document Deleted Successfully.', 'success');
		
		echo closeWindow();
		//redirect($this->_clspath.$this->_class.'/index/'.$party_id);
	}
}
