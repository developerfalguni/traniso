<?php

class Vehicle extends MY_Controller {
	var $_table2;
	var $_folder;
	var $_path;
	var $_path_url;

	function __construct() {
		parent::__construct();
		
		$this->_table2   = 'vehicle_documents';
		$this->_folder   = 'documents/vehicles/';
		$this->_path     = FCPATH . $this->_folder;
		$this->_path_url = base_url($this->_folder);
		$this->load->model('vehicle_m');
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
		
		$data['list']['heading'] = array('ID', 'Type', 'Group', 'Registration No', 'Make', 'Model No', 'MFG Year', 'Tracking', 'Ledger A/c');
		$data['list']['class'] = array(
			'id'                 => 'ID', 
			'type'               => 'Text',
			'group_name'         => 'Text',
			'registration_no'    => 'Text bold',
			'make'               => 'Text',
			'model_no'           => 'Text',
			'mfg_year'           => 'Code',
			'monthly_fuel_limit' => 'Numeric',
			'track_data'         => 'Code Label',
			'ledger_name'        => 'Text');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class'] = $this->vehicle_m->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->vehicle_m->countVehicles($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->vehicle_m->getVehicles($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}

	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('type', 'Type', 'trim');
		$this->form_validation->set_rules('registration_no', 'Registration No', 'trim');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'                 => 0,
				'company_id'	     => $this->vehicle_m->getCompanyID(),
				'purchase_date'      => '00-00-0000',
				'mfg_year'           => '',
				'type'               => '',
				'registration_no'    => 'GJ',
				'group_name'         => '',
				'make'               => '',
				'model_no'           => '',
				'seller_name'        => '',
				'seller_address'     => '',
				'seller_contact'     => '',
				'dealer_name'        => '',
				'dealer_address'     => '',
				'dealer_person'      => '',
				'dealer_contact'     => '',
				'photo'              => '',
				'track_data'         => 'No',
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
		$data['photo'] = $this->vehicle_m->getImage($this->_folder, $id, $row['photo']);

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['documents'] = $this->vehicle_m->getVehicleDocuments($id);
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'company_id'	     => $this->vehicle_m->getCompanyID(),
					'purchase_date'      => $this->input->post('purchase_date'),
					'mfg_year'           => $this->input->post('mfg_year'),
					'type'               => $this->input->post('type'),
					'registration_no'    => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('registration_no'))),
					'group_name'         => $this->input->post('group_name'),
					'make'               => $this->input->post('make'),
					'model_no'           => $this->input->post('model_no'),
					'seller_name'        => $this->input->post('seller_name'),
					'seller_address'     => $this->input->post('seller_address'),
					'seller_contact'     => $this->input->post('seller_contact'),
					'dealer_name'        => $this->input->post('dealer_name'),
					'dealer_address'     => $this->input->post('dealer_address'),
					'dealer_person'      => $this->input->post('dealer_person'),
					'dealer_contact'     => $this->input->post('dealer_contact'),
					'track_data'         => ($this->input->post('track_data') ? 'Yes' : 'No'),
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				$this->_updateDocuments($id);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _updateDocuments($id) {
		$dates            = $this->input->post('new_date');

		if ($dates != null) {
			$names            = $this->input->post('new_name');
			$validitys        = $this->input->post('new_validity');
			$document_details = $this->input->post('new_document_details');
			$alarms           = $this->input->post('new_alarm');
			
			foreach ($dates as $index => $date) {
				if (strlen(trim($names[$index])) > 0) {
					$data = array(
						'vehicle_id'       => $id,
						'date'             => $date,
						'name'             => $names[$index],
						'document_details' => $document_details[$index],
						'alarm'            => $alarms[$index],
						'validity'         => $validitys[$index]
					);
					$this->kaabar->save($this->_table2, $data);
				}
			}
		}
	}

	function photoadd($id) {
		
		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = 'gif|jpg|png|bmp';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image = $this->upload->data();

		if ($image['is_image'] == TRUE) {
			$docdir = $this->vehicle_m->getDocFolder($this->_path, $id);
			rename($image['full_path'], $this->_path.$docdir.$image['file_name']);
			$this->db->update($this->_table, array("photo" =>  $image['file_name']), "id = $id");

			setSessionAlert('IMAGE_UPLOADED', 'success');
		}
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function photodel($id) {
		$filename = $this->kaabar->getField($this->_table, $id, 'id', 'photo');
		$docdir   = $this->vehicle_m->getDocFolder($this->_path, $id);

		unlink($this->_path.$docdir.$filename);

		$this->db->update($this->_table, array("photo" => ''), "id = $id");

		setSessionAlert('Document Deleted Successfully.', 'success');
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	
	function documentadd($id) {
		$vdid = $this->input->post('vehicle_document_id');
		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = '*';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image = $this->upload->data();

		$docdir = $this->vehicle_m->getDocFolder($this->_path, $id);
		rename($image['full_path'], $this->_path.$docdir.$image['file_name']);
		$this->db->update($this->_table2, array('file'  => $image['file_name']), "id = $vdid");

		setSessionAlert('Document Uploaded Successfully.', 'success');
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function documentdel($id) {
		$vdid = $this->input->post('vehicle_document_id');
		$docdir   = $this->vehicle_m->getDocFolder($this->_path, $id);

		$file   = $this->kaabar->getField($this->_table2, $vdid, 'id', 'file');
		$this->db->delete($this->_table2, array('id' => $vdid));
		unlink($this->_path.$docdir.$file);

		setSessionAlert('Document Deleted Successfully.', 'success');
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function show($id, $vdid) {
		$this->load->helper('file');
		$docdir  = $this->vehicle_m->getDocFolder($this->_path, $id);
		$vehicle = $this->kaabar->getRow($this->_table, $id);
		$document= $this->kaabar->getRow($this->_table2, $vdid);

		$data['view'] = get_file_info($this->_path.$docdir.$document['file']);
		if ($data['view'] != false) {
			$data['view']['type'] = strtolower(substr($data['view']['name'], -3));
			$data['view']['url'] = $this->_path_url . '/' . $docdir . $data['view']['name'];
		}

		$data['javascript']  = array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');
		$data['page_title']  = $vehicle['type'] . ' / ' . $vehicle['registration_no'] . ' / ' . $document['name'];
		$data['page_desc']   = $document['file'];
		$data['hide_menu']   = true;
		$data['hide_title']  = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class.'_document';
		$this->load->view('index', $data);
	}

	function ajax($category = NULL) {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));

			$category_search = "V.category LIKE '%$category%' AND ";
			$sql = "SELECT V.id, V.registration_no, V.category, V.type, V.model_no
			FROM $this->_table V
			WHERE $category_search V.registration_no LIKE '%$search%' 
			ORDER BY V.registration_no
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
}
