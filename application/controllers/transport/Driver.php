<?php

class Driver extends MY_Controller {
	var $_folder;
	var $_path;
	var $_path_url;
	var $_table2;

	function __construct() {
		parent::__construct();
		
		$this->_folder   = 'documents/drivers/';
		$this->_path     = FCPATH . $this->_folder;
		$this->_path_url = base_url($this->_folder);

		$this->_table2 = 'driver_documents';
		$this->load->model('transport');
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Driver Name', 'Address','Contact','License No.', 'Issue Date', 'Expiry Date', 'State');
		$data['list']['class'] = array(
			'id'                  => 'ID', 
			'name'                => 'Text',
			'address'             => 'Text',
			'contact'             => 'Text',
			'license_no'          => 'Text',
			'license_issue_date'  => 'Text',
			'license_expiry_date' => 'Text',
			'state_name'          => 'Text');

		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->transport->countDrivers($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->transport->getDrivers($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="icon-white icon-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Driver Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
			'id'                  => 0, 
			'name'                => '',
			'address'             => '',
			'contact'             => '',
			'license_no'          => '',
			'license_issue_date'  => '0000-00-00',
			'license_expiry_date' => '0000-00-00',
			'license_state_id'    => 0
			);
		}
		
		$data['id']    = array('id' => $id);
		$data['row']   = $row;
		$data['photo'] = $this->transport->getImage($this->_folder, $id, $row['photo']);
				
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$dirarr = array();
			for($i=0; $i < strlen($id); $i++) {
				$dirarr[] = substr($id, $i, 1);
			}
			$data['document_url'] = $this->_path_url . '/' . implode('/', $dirarr) . '/';
			$data['documents']    = $this->kaabar->getRows($this->_table2, $id, 'driver_id');

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'name'                => $this->input->post('name'),
					'address'             => $this->input->post('address'),
					'contact'             => $this->input->post('contact'),
					'license_no'          => $this->input->post('license_no'),
					'license_issue_date'  => $this->input->post('license_issue_date'),
					'license_expiry_date' => $this->input->post('license_expiry_date'),
					'license_state_id'    => $this->input->post('license_state_id'),
				);
				
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class);
		}
	}

	function _getLicenses($id) {
		$docdir = $this->transport->getDocFolder($this->_path, $id);
		$this->load->helper('filelist');
		return getFileList($this->_path.$docdir);
	}

	function attach_photo($id) {
		
		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = 'gif|jpg|png|bmp';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image = $this->upload->data();

		if ($image['is_image'] == TRUE) {
			$docdir = $this->transport->getDocFolder($this->_path, $id);
			rename($image['full_path'], $this->_path.$docdir.$image['file_name']);
			$this->db->update($this->_table, array("photo" =>  $image['file_name']), "id = $id");

			setSessionAlert('IMAGE_UPLOADED', 'success');
		}
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function detach_photo($id) {
		$filename = $this->kaabar->getField($this->_table, $id, 'id', 'photo');
		$docdir   = $this->transport->getDocFolder($this->_path, $id);

		unlink($this->_path.$docdir.$filename);

		$this->db->update($this->_table, array("photo" => ''), "id = $id");

		setSessionAlert('IMAGE_DELETED', 'success');
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function attach_document($id) {
		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = '*';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image  = $this->upload->data();
		$docdir = $this->transport->getDocFolder($this->_path, $id);

		$newfile = uniqid().$image['file_ext'];
		rename($image['full_path'], $this->_path.$docdir.$newfile);

		$this->kaabar->save($this->_table2, array(
			'driver_id' => $id, 
			'date'      => date('Y-m-d'),
			'name'      => $this->input->post('name'),
			'file'      => $newfile
		));
		setSessionAlert('DOC_ATTACHED', 'success');

		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function detach_document($driver_id, $id, $file) {
		if (Auth::hasAccess(Auth::DELETE)) {
			$dirarr = array();
			for($i=0; $i < strlen($driver_id); $i++) {
				$dirarr[] = substr($driver_id, $i, 1);
			}
			$docdir = implode('/', $dirarr) . '/';
			if (file_exists($this->_path.$docdir.$file)) {
				$this->kaabar->delete($this->_table2, $id);
				unlink($this->_path.$docdir.$file);
			}
			setSessionAlert('DOC_DELETED', 'success');
		}
		else
			setSessionError('NO_PERMISSION');
		
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}
}
