<?php

class Voucher_document extends MY_Controller {
	var $_folder;
	var $_share;
	var $_path;
	var $_share_url;
	var $_path_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_folder = 'documents/vouchers/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url = base_url('share');
		$this->_path_url  = base_url($this->_folder);
		$this->load->model('import');
	}
	
	function index($voucher_id, $id = 0, $page_no = 1, $md5 = '') {
		$voucher_id = intval($voucher_id);
		if ($voucher_id <= 0) {
			setSessionError('Invalid Voucher No or Voucher does not exists.');
			redirect();
		}

		$docdir = $this->import->getDocFolder($this->_path, $voucher_id);

		$this->load->helper('filelist');
		$pending = getFileList($this->_share);

		// Adding New Document for file from Share
		$attach = $this->input->post('attach');
		if ($attach != null) {

			$file = $pending[$attach]['name'];
			if (file_exists($this->_share.$file)) {
				$newfile = uniqid().'.'.$pending[$attach]['type'];
				rename($this->_share.$file, $this->_path.$docdir.$newfile);
			}

			$row = array(
				'voucher_id'=> $voucher_id,
				'datetime'	=> date('Y-m-d H:i:s'),
				'file' 		=> $newfile
			);
			$id = $this->kaabar->save($this->_table, $row);
			redirect($this->_clspath.$this->_class."/index/$voucher_id/$id");
		}

		if ($id > 0) {
			$file = $this->kaabar->getField($this->_table, $id, 'id', 'file');
			$data['view'] = get_file_info($this->_path.$docdir.$file);
			if ($data['view'] != false) {
				$data['view']['type'] = strtolower(substr($data['view']['name'], -3));
				$data['view']['url'] = $this->_path_url . '/' . $docdir . $data['view']['name'];
			}
		}
		else if (strlen($md5) > 0) {
			if (file_exists($pending[$md5]['server_path'])) {
				$data['view'] = $pending[$md5];
				$data['view']['url'] = $this->_share_url . '/' . $pending[$md5]['name'];
			}
		}

		if (isset($data['view']['type']) && strtolower($data['view']['type']) == 'pdf')
			$data['javascript']	= array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');

		$voucher 			= $this->kaabar->getRow('vouchers', $voucher_id);
		$data['voucher']	= $this->accounting->getVoucher($voucher['voucher_book_id'], $voucher['id2'], $voucher['id3']);
		$data['documents']  = $this->kaabar->getRows($this->_table, $voucher_id, 'voucher_id');
		$data['id'] 		= array('id' => $id);
		$data['voucher_id'] = array('id' => $voucher_id);
		$data['default_company'] = $this->session->userdata('default_company');
		$data['md5'] 		= $md5;
		$data['page_no'] 	= $page_no;
		$data['pending'] 	= $pending;
		$data['page_title'] = humanize($this->_class);

		$data['page'] 		= $this->_clspath.$this->_class;
		$data['docs_url'] 	= $this->_docs;
		$this->load->view('index', $data);
	}

	function attach($voucher_id) {
		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = '*';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image  = $this->upload->data();
		$docdir = $this->import->getDocFolder($this->_path, $voucher_id);

		$newfile = uniqid().$image['file_ext'];
		rename($image['full_path'], $this->_path.$docdir.$newfile);

		$row = array(
			'voucher_id'=> $voucher_id,
			'datetime'	=> date('Y-m-d H:i:s'),
			'file' 		=> $newfile
		);
		$id = $this->kaabar->save($this->_table, $row);
		setSessionAlert('Bill Attached Successfully.', 'success');

		redirect($this->agent->referrer());
	}

	function detach($voucher_id = 0, $id = 0) {
		$docdir = $this->import->getDocFolder($this->_path, $voucher_id);
		$file   = $this->kaabar->getField($this->_table, $id, 'id', 'file');
		if (file_exists($this->_path.$docdir.$file)) {
			rename($this->_path.$docdir.$file, $this->_share.$file);
		}
		$this->kaabar->delete($this->_table, $id);
		setSessionAlert('Document Deleted Successfully.', 'success');
		
		redirect($this->agent->referrer());
	}
}
