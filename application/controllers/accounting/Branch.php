<?php

class Branch extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->_fields = array('id', 'code', 'name', 'company', 'address', 'contact', 'email');
		$this->_search = array('code', 'name', 'company', 'address', 'contact', 'email');

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
			$this->session->set_userdata();
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Code', 'Name', 'Company', 'Address', 'Contact', 'Email');

		$data['list']['class'] = array(
			'id'      => 'ID', 
			'code'    => 'Code',
			'name'    => 'Text',
			'company' => 'Text',
			'address' => 'Text',
			'contact' => 'Text',
			'email'   => 'Text');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->_data['label_class'] = $this->accounting->getLabelClass();

		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->accounting->countBranches($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
	
		$data['list']['data'] = $this->accounting->getBranches($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success btn-sm" id="AddNew"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Branch Code', 'trim|required');
		$this->form_validation->set_rules('name', 'Branch Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);

		if($row == false) {
			$row = array(
				'id'             => 0,
				'series'         => '',
				'code'           => '',
				'name'           => '', 
				'address'        => '',
				'company_id'	 => 1,
				'city_id'        => 0,
				'contact'        => '',
				'email'          => '',
				'pan_no'         => '',
				'gst_no'         => '',
				'tan_no'         => '',
				'service_tax_no' => '',
				'cha_no'         => '',
				'cha_license_no' => '',
				'remarks'        => '',
				'uuid'           => '',
			);
		}
		$data['id']   = array('id' => $id);
		$data['row']  = $row;
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$default_company = $this->accounting->getCompanyID();
			$data['current_company'] = $this->kaabar->getRow('companies', $default_company);


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
					'series' 		 => $this->input->post('series'),
					'company_id'     => $this->input->post('company_id'),
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
				);
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
