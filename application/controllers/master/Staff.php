<?php

class Staff extends MY_Controller {
	var $_folder;
	var $_path;
	var $_path_url;

	function __construct() {
		parent::__construct();
		
		$this->_folder   = 'documents/staffs/';
		$this->_path     = FCPATH . $this->_folder;
		$this->_path_url = base_url($this->_folder);
		$this->load->model('office');
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
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
				
		$data['list']['heading'] = array('ID', 'Company', 'Designation', 'Name', 'Gender', 'DOB', 'Address', 'Tel', 'Category', 'Location', 'PAN No', 'Status');
		$data['list']['class'] = array(
			'id'           => 'ID',
			'company_code' => 'Code',
			'designation'  => 'Text',
			'name'         => 'Text Bold',
			'gender'       => 'Code',
			'dob'          => 'Date',
			'address'      => 'Text',
			'contact'      => 'Text',
			'category'     => 'Text',
			'location'     => 'Text',
			'pan_no'       => 'Text',
			'status'       => 'Label');
		$data['list']['link_col'] = 'id';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class'] = array('Employed' => 'label-success', 'Resigned' => 'label-danger');
		
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows'] = $this->office->countStaffs($search);
		$config['per_page'] = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getStaffs($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(
			anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'),
			anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"')
		);

		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);		
	}

	function edit($id = 0) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('title', 'Title', 'trim');
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
		$this->form_validation->set_rules('middlename', 'Middle Name', 'trim');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('gender', 'Gender', 'required');
		$this->form_validation->set_rules('dob', 'Date of Birth', 'trim');
		$this->form_validation->set_rules('address', 'Address', 'trim');
		$this->form_validation->set_rules('contact', 'Tel', 'trim');
		$this->form_validation->set_rules('date_joined', 'Date Joined', 'trim|required|min_length[10]');
		$this->form_validation->set_rules('date_left', 'Date Left', 'trim|min_length[10]');
		$this->form_validation->set_rules('email', 'Email', 'trim');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'              => 0,
				'company_id'      => 0,
				'parent_id'       => 0,
				'user_id'         => 0,
				'designation'     => '',
				'title'           => 'Mr.',
				'firstname'       => '',
				'middlename'      => '',
				'lastname'        => '',
				'gender'          => 'Male',
				'dob'             => '',
				'address'         => '',
				'city_id'         => 0,
				'contact'         => '',
				'email'           => '',
				'status'          => 'Employed',
				'category'        => 'General',
				'location'        => 'GANDHIDHAM',
				'permission'      => '',
				'date_joined'     => '',
				'date_left'       => '',
				'bank_branch_id'  => 0,
				'bank_account_no' => '',
				'pan_no'          => '',
				'pan_no_verified' => 'No',
				'remarks'         => '',
				'photo'           => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
		$data['reports_to']  = $this->kaabar->getField('staffs', $row['parent_id'], 'id', 'CONCAT(designation, " - ", title, " ", firstname, " ", lastname)');
		$data['bank_branch'] = $this->office->getBankBranch($row['bank_branch_id']);
		$data['photo'] 		 = $this->office->getImage($this->_folder, $id, $row['photo']);
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['documents']  = $this->office->getAttachedStaffDocs($id, 1);
			$data['resources']  = $this->office->getIssuedResources($id);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'staff_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess(Auth::CREATE | Auth::UPDATE)) {
				$data = array(
					'company_id'      => $this->input->post('company_id'),
					'parent_id'       => $this->input->post('parent_id'),
					'user_id'         => $this->input->post('user_id'),
					'designation'     => $this->input->post('designation'),
					'title'           => $this->input->post('title'),
					'firstname'       => $this->input->post('firstname'),
					'middlename'      => $this->input->post('middlename'),
					'lastname'        => $this->input->post('lastname'),
					'gender'          => $this->input->post('gender'),
					'dob'             => $this->input->post('dob'),
					'category'        => $this->input->post('category'),
					'location'        => $this->input->post('location'),
					'date_joined'     => $this->input->post('date_joined'),
					'address'         => $this->input->post('address'),
					'city_id'         => $this->input->post('city_id'),
					'contact'         => $this->input->post('contact'),
					'email'           => $this->input->post('email'),
					'bank_branch_id'  => $this->input->post('bank_branch_id'),
					'bank_account_no' => $this->input->post('bank_account_no'),
					'pan_no'          => $this->input->post('pan_no'),
					'pan_no_verified' => ($this->input->post('pan_no_verified') ? 'Yes' : 'No'),
					'remarks'         => $this->input->post('remarks')
				);
				if (Auth::isAdmin()) {
					$permission = ($this->input->post('permission') ? join(',', $this->input->post('permission')) : '');
					$data['status'] 	= $this->input->post('status');
					$data['permission'] = $permission;
					$data['date_left'] 	= $this->input->post('date_left');
				}
				$id = $this->kaabar->save($this->_table, $data, $row);
				$this->_updateResources($id);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			redirect($this->_clspath.$this->_class."/edit/$id");
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
			$docdir = $this->office->getDocFolder($this->_path, $id);
			rename($image['full_path'], $this->_path.$docdir.$image['file_name']);
			$this->db->update($this->_table, array("photo" =>  $image['file_name']), "id = $id");

			setSessionAlert('IMAGE_UPLOADED', 'success');
		}
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function photodel($id) {
		$filename = $this->kaabar->getField($this->_table, $id, 'id', 'photo');
		$docdir   = $this->office->getDocFolder($this->_path, $id);

		unlink($this->_path.$docdir.$filename);

		$this->db->update($this->_table, array("photo" => ''), "id = $id");

		setSessionAlert('IMAGE_DELETED', 'success');
		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function documentadd($id) {
		
		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = '*';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image = $this->upload->data();

		$docdir = $this->office->getDocFolder($this->_path, $id);
		rename($image['full_path'], $this->_path.$docdir.$image['file_name']);
		$data = array(
			'staff_id' => $id,
			'staff_document_type_id' => $this->input->post('staff_document_type_id'),
			'date'  => date('Y-m-d'),
			'file'  => $image['file_name'],
			'pages' => 1
		);
		if ($this->input->post('staff_document_id') == 0)
			$this->db->insert('staff_documents', $data);
		else
			$this->db->update('staff_documents', $data, "id = " . $this->input->post('staff_document_id'));

		setSessionAlert('IMAGE_UPLOADED', 'success');

		redirect($this->_clspath.$this->_class."/edit/$id");
	}

	function _updateResources($id) {
		$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		if ($delete_ids != null) {
			if (Auth::hasAccess(Auth::UPDATE)) {
				foreach ($delete_ids as $index) {
					if ($index > 0)
						$this->kaabar->save('staff_resource', array('return_date' => date('d-m-Y')), array('id' => $index));
				}
			}
			else
				setSessionError('NO_PERMISSION');
		}

		$resource_ids = $this->input->post('new_resource_id');
		if ($resource_ids != null) {
			foreach ($resource_ids as $index => $resource_id) {
				if ($resource_id > 0) {
					$data = array(
						'staff_id'		=> $id,
						'resource_id' 	=> $resource_id,
						'issue_date'	=> date('d-m-Y')
					);
					$vdid = $this->kaabar->save('staff_resource', $data);
				}
			}
		}
	}

	function salary($id) {
		if (! Auth::hasAccess()) {
			$this->lang->load('messages');
			$this->load->helper('language');
			echo lang('NO_PERMISSION');
			die();
		}

		$data['id']   = array('id' => $id);
		$data['rows'] = $this->office->getSalaryDetails($id);
		$data['staff'] = $this->kaabar->getRow('staffs', $id);

		if ($this->input->post('update') == false) {
			$data['page_title']    = 'Salary Details';
			$data['hide_menu']     = true;
			$data['hide_title']    = true;
			$data['hide_footer']   = true;
			$data['page']          = $this->_clspath.'salary_edit';
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class.'/salary/'.$id);
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
				$amounts = $this->input->post('amount');
				$new_amounts = $this->input->post('new_amount');

				if ($amounts != null) {
					$names = $this->input->post('name');
					foreach ($amounts as $index => $amount) {
						$data = array(
							'name' => $names[$index],
							'amount' => $amount
						);
						$this->kaabar->save('salary_details', $data, array('id' => $index));
					}
				}

				if ($delete_ids != null) {
					foreach ($delete_ids as $index => $value) {
						if ($index > 0) {
							$this->db->delete('salary_details', array('id' => $index));
						}
					}
				}

				if ($new_amounts != null) {
					$types = $this->input->post('new_type');
					$names = $this->input->post('new_name');
					foreach ($new_amounts as $index => $amount) {
						if ($amount > 0) {
							$data = array(
								'staff_id' => $id,
								'type' => $types[$index],
								'name' => $names[$index],
								'amount' => $amount
							);
							$this->kaabar->save('salary_details', $data);
						}
					}
				}
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class.'/salary/'.$id);
		}
	}

	function ajax() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			//$default_company = $this->session->userdata('default_company');
			//WHERE company_id = " . $default_company['id'] . " AND (

			$sql = "SELECT id, CONCAT(title, ' ', firstname, ' ', middlename, ' ', lastname) AS name 
			FROM " . $this->_table . "
			WHERE (
				firstname LIKE '%$search%' OR 
				middlename LIKE '%$search%' OR 
				lastname LIKE '%$search%')
			ORDER BY firstname
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxParent() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
		
			$sql = "SELECT id, designation, CONCAT(title, ' ', firstname, ' ', lastname) AS name FROM staffs 
			WHERE designation LIKE '%$search%' OR firstname LIKE '%$search%' 
			ORDER BY designation, firstname";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxBanks() {
		if ($this->_is_ajax) {
			$search = explode(' ', strtolower($this->input->get('term')));
			
			if (count($search) > 1) {
				$sql = "SELECT BB.id, B.name, BB.ifsc, BB.branch, BB.address
				FROM bank_branches BB INNER JOIN banks B ON BB.bank_id = B.id
				WHERE B.name LIKE '%" . $search[0] . "%' AND (BB.branch LIKE '%" . $search[1] . "%' OR BB.district LIKE '%" . $search[1] . "%')
				ORDER BY B.name
				LIMIT 0, 50";
			}
			else {
				$sql = "SELECT BB.id, B.name, BB.ifsc, BB.branch, BB.address
				FROM bank_branches BB INNER JOIN banks B ON BB.bank_id = B.id
				WHERE BB.ifsc LIKE '%" . $search[0] . "%'
				ORDER BY B.name
				LIMIT 0, 50";
			}

			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxResource() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
		
			$sql = "SELECT R.id, R.type, R.model_no 
			FROM resources R 
			WHERE (R.active = 'Yes' AND R.id NOT IN (SELECT DISTINCT resource_id FROM staff_resource WHERE return_date = '0000-00-00')) AND
				(type LIKE '%$search%' OR
				model_no LIKE '%$search%')
			ORDER BY type, model_no";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function preview() {
		$search = $this->session->userdata($this->_class.'_search');

		$data['width']  = 80;
		$data['height'] = 60;
		
		$data['staff'] = $this->office->getStaffs($search);
		
		$data['print'] = $this->input->post('print');
		$data['output_file'] = FCPATH."application/views/reports/".$this->_class;
		$data['page'] = "reports/staff_preview";
		$data['page_title'] = "Staff Information";
		$this->load->view('report', $data);
	}

	function excel() {
		$table = $this->_table;
		$query = $this->db->query("SELECT S.id, C.code, S.title, S.firstname, S.middlename, 
			S.lastname, S.gender, DATE_FORMAT(S.dob, '%d-%m-%Y') AS dob, S.address, S.contact, S.email, 
			S.designation, S.category, S.location, S.status, CONCAT(B.name, ' - ', BB.ifsc) AS ifsc, S.bank_account_no
			FROM (($table S INNER JOIN companies C ON S.company_id = C.id)
				LEFT OUTER JOIN bank_branches BB ON S.bank_branch_id = BB.id)
				LEFT OUTER JOIN banks B ON BB.bank_id = B.id
			ORDER BY S.firstname, S.lastname");

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
