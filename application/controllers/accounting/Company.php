<?php

class Company extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'code', 'name', 'address', 'contact', 'email');
		$this->_search = array('code', 'name', 'address', 'contact', 'email');
		
		$this->_data['list'] = array(
			'heading' => array('ID', 'Code', 'Name', 'Address', 'Contact', 'Email'),
			'class' => array(
				'id'      => 'ID', 
				'code'    => 'Code',
				'name'    => 'Text',
				'address' => 'Text',
				'contact' => 'Text',
				'email'   => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		$this->_data['label_class'] = $this->kaabar->getLabelClass();
		
		$this->_index($starting_row);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Company Code', 'trim|required');
		$this->form_validation->set_rules('name', 'Company Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'             => 0,
				'code'           => '',
				'name'           => '', 
				'address'        => '',
				'city_id'        => 0,
				'contact'        => '',
				'email'          => '',
				'pan_no'         => '',
				'tan_no'         => '',
				'gst_no'         => '',
				'service_tax_no' => '',
				'cha_no'         => '',
				'cha_license_no' => '',
				'remarks'        => '',
				'logo'           => '',
				'uuid'           => '',
				'letterhead'     => '',
				'letterfoot'     => '',
			);
		}
		$data['id']   = array('id' => $id);
		$data['row']  = $row;
		$data['logo'] = $this->getLogo($row['logo']);
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['javascript'] = array('/vendors/js/jquery.base64.js');

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'code'           => $this->input->post('code'),
					'name'           => $this->input->post('name'),
					'address'        => $this->input->post('address'),
					'city_id'        => $this->input->post('city_id'),
					'contact'        => $this->input->post('contact'),
					'email'          => $this->input->post('email'),
					'pan_no'         => $this->input->post('pan_no'),
					'tan_no'         => $this->input->post('tan_no'),
					'gst_no'         => $this->input->post('gst_no'),
					'service_tax_no' => $this->input->post('service_tax_no'),
					'cha_no'         => $this->input->post('cha_no'),
					'cha_license_no' => $this->input->post('cha_license_no'),
					'remarks'        => $this->input->post('remarks'),
					'letterhead'     => base64_decode($this->input->post('letterhead')),
					'letterfoot'     => base64_decode($this->input->post('letterfoot')),
				);
				if (strlen($row['uuid']) == 0) {
					$this->load->library('uuid');
					$data['uuid'] = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
				}
				$id = $this->kaabar->save($this->_table, $data, $row);

				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	
	function getLogo($image_name) {
		if (strlen(trim($image_name)) == 0)
			return '';
		elseif (file_exists(FCPATH . 'php_uploads/' . $image_name))
			return base_url() . 'php_uploads/' . $image_name;
			
		return '';
	}
	
	function addLogo($id) {
		
		$config['upload_path'] = './php_uploads/';
		$config['allowed_types'] = 'gif|jpg|png|bmp';
		$config['encrypt_name'] = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image = $this->upload->data();

		if ($image['is_image'] == TRUE) {
			$this->db->update($this->_table, array("logo" =>  $image['file_name']), "id = $id");

			setSessionAlert('IMAGE_UPLOADED', 'success');
		}
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function delLogo($id) {
		$this->db->update($this->_table, array("logo" => ''), "id = $id");
		setSessionAlert('IMAGE_DELETED', 'error');
		redirect($this->_clspath.$this->_class."/edit/$id");
	}
}
