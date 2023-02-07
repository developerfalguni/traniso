<?php

class Party extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('ID', 'Name', 'Traces Name', 'Address', 'Contact', 'PAN No', 'TAN No', 'IEC No', 'Ledger A/c'),
			'class' => array(
				'id'          => 'ID',
				'name'        => 'Text bold',
				'traces_name' => 'Text',
				'address'     => 'Text',
				'contact'     => 'Text',
				'pan_no'      => 'Text',
				'tan_no'      => 'Text',
				'iec_no'      => 'Text',
				'ledger_name' => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		$data['label_class'] = $this->office->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_count($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(
			anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'),
			anchor("/tracking/traces/captcha/parties", 'PAN Login', 'class="btn btn-info Popup"'),
			anchor("/tracking/traces/track/parties", 'Fetch PAN', 'class="btn btn-info Popup"'),
			anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning"')
		);

		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _count($search = '') {
		$sql = "SELECT COUNT(T.id) AS numrows
		FROM (
			SELECT DISTINCT P.id
			FROM parties P
				LEFT OUTER JOIN ledgers PL ON (PL.company_id = ? AND P.id = PL.party_id)
			WHERE P.name LIKE '%$search%' OR
				P.traces_name LIKE '%$search%' OR
				P.address LIKE '%$search%' OR
				P.contact LIKE '%$search%' OR
				P.pan_no LIKE '%$search%' OR
				P.tan_no LIKE '%$search%' OR
				P.iec_no LIKE '%$search%' OR
				PL.code LIKE '%$search%' OR
				PL.name LIKE '%$search%'
			GROUP BY P.id
		) T";
		$query = $this->db->query($sql, [$this->office->getCompanyID()]);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _get($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT P.id, P.name, P.traces_name, P.address, P.contact, P.pan_no, P.tan_no, P.iec_no, CONCAT(PL.code, ' - ', PL.name) AS ledger_name
		FROM parties P
			LEFT OUTER JOIN ledgers PL ON (PL.company_id = ? AND P.id = PL.party_id)
		WHERE P.name LIKE '%$search%' OR
			P.traces_name LIKE '%$search%' OR
			P.address LIKE '%$search%' OR
			P.contact LIKE '%$search%' OR
			P.pan_no LIKE '%$search%' OR
			P.tan_no LIKE '%$search%' OR
			P.iec_no LIKE '%$search%' OR
			PL.code LIKE '%$search%' OR
			PL.name LIKE '%$search%'
		GROUP BY P.id
		ORDER BY P.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, [$this->office->getCompanyID()]);
		return $query->result_array();
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Party Name', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim');
		$this->form_validation->set_rules('contact', 'Contact', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'              => 0,
				'name'            => '', 
				'address'         => '',
				'city_id'         => 0,
				'contact'         => '',
				'traces_name'	  => '',
				'fax'             => '',
				'email'           => '',
				'pan_no'          => '',
				'pan_no_verified' => 'No',
				'tan_no'          => '',
				'tan_no_verified' => 'No',
				'service_tax_no'  => '',
				'iec_no'          => '',
				'tin_no'          => '',
				'cst_no'          => '',
				'excise_no'       => '',
				'gst_no'          => '',
				'tds_class_id'    => 0,
				'username'        => '',
				'password'        => '',
				'active'          => 'No',
				'remarks'         => ''
			);
		}
		
		$data['id'] = ['id' => $id];
		$data['party_id'] = ['party_id' => $id];
		$data['row'] = $row;
		$data['ledger_id'] = $this->kaabar->getField('ledgers', $id, 'party_id', 'id');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
	
			$data['kyc_documents']  = $this->office->getAttachedKycs($row['id'], 1, 0);
			$data['sites']          = $this->_getSites($id);
			$data['iec_details']    = $this->kaabar->getRow('dgft_iecs', $row['id'], 'party_id');
			$data['contacts']       = $this->kaabar->getRows('party_contacts', ['party_id' => $id, 'party_site_id' => 0]);
			$data['bill_templates'] = $this->office->getPartyBillTemplates($id);
			
			$data['page_title'] = (strlen($row['name']) == 0 ? 'Party' : $row['name']);
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			$data = array(
				'name'            => $this->input->post('name'),
				'address'         => $this->input->post('address'),
				'city_id'         => $this->input->post('city_id'),
				'contact'         => $this->input->post('contact'),
				'fax'             => $this->input->post('fax'),
				'email'           => $this->input->post('email'),
				'pan_no'          => $this->input->post('pan_no'),
				'pan_no_verified' => ($this->input->post('pan_no_verified') ? 'Yes' : 'No'),
				'tan_no'          => $this->input->post('tan_no'),
				'tan_no_verified' => ($this->input->post('tan_no_verified') ? 'Yes' : 'No'),
				'service_tax_no'  => $this->input->post('service_tax_no'),
				'iec_no'          => $this->input->post('iec_no'),
				'tin_no'          => $this->input->post('tin_no'),
				'cst_no'          => $this->input->post('cst_no'),
				'excise_no'       => $this->input->post('excise_no'),
				'gst_no'          => $this->input->post('gst_no'),
				'tds_class_id'    => $this->input->post('tds_class_id'),
				'username'        => $this->input->post('username'),
				'password'        => $this->input->post('password'),
				'active'          => $this->input->post('active'),
				'remarks'         => $this->input->post('remarks')
			);
			if ($row['password'] != $this->input->post('password')) {
				$data['password'] = Auth::_addSalt($this->input->post('password'));
			}
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_updateContacts($id, 0);
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function ajaxEdit($id, $category = null) {

		$response = [];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Party Name', 'trim|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		
		if ($this->form_validation->run() == false) {
			//setSessionError(validation_errors());
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }
		else
		{

			$name = $this->input->post('name');
			if($name != null){

				$data = array(
	        		'name'	=> $name,
	        	);

				$id = $this->kaabar->save($this->_table, $data);

				$response['id'] = $id;
				$response['name'] = $name;
		        $response['success'] = true;
	        	$response['messages'] = 'Succesfully Saved';	
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Somathing Went Wrong';	
			}

		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function _getSites($id) {
		$sql = "SELECT PS.*, CONCAT(PS.address, ' ', C.name, ' ', C.pincode, ' ', S.name) AS address
		FROM party_sites PS 
			LEFT OUTER JOIN cities C ON PS.city_id = C.id
			LEFT OUTER JOIN states S ON C.state_id = S.id
		WHERE PS.party_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function site($party_id, $id = 0) {
		if ($party_id == 0)
			redirect($this->_clspath.$this->_class);

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Site Code', 'trim|required');
		$this->form_validation->set_rules('name', 'Site Name', 'trim|required');
		
		$row = $this->kaabar->getRow('party_sites', $id);
		if($row == false) {
			$row = array(
				'id'             => 0,
				'party_id'       => $party_id,
				'code'           => '',
				'name'           => '',
				'address'        => '',
				'city_id'        => 0,
				'pan_no'         => '',
				'tan_no'         => '',
				'service_tax_no' => '',
				'iec_no'         => '',
				'tin_no'         => '',
				'cst_no'         => '',
				'excise_no'      => '',
				'gst_no'         => '',
			);
		}
		
		$data['party_id'] = array('party_id' => $party_id);
		$data['id']  = ['id' => $id];
		$data['row'] = $row;
		$party_name  = $this->kaabar->getField($this->_table, $party_id);

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['kyc_documents']  = $this->office->getAttachedKycs($party_id, 1, 0);
			$data['sites']          = $this->_getSites($party_id);
			$data['contacts']       = $this->kaabar->getRows('party_contacts', ['party_id' => $party_id, 'party_site_id' => $id]);

			$data['page_title'] = $party_name;
			$data['page']       = $this->_clspath.$this->_class.'_site_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/site/$party_id/$id");
			
			$data = array(
				'party_id'       => $party_id,
				'code'           => $this->input->post('code'),
				'name'           => $this->input->post('name'),
				'address'        => ($this->input->post('address') ? $this->input->post('address') : ''),
				'city_id'        => $this->input->post('city_id'),
				'pan_no'         => $this->input->post('pan_no'),
				'tan_no'         => $this->input->post('tan_no'),
				'service_tax_no' => $this->input->post('service_tax_no'),
				'iec_no'         => $this->input->post('iec_no'),
				'tin_no'         => $this->input->post('tin_no'),
				'cst_no'         => $this->input->post('cst_no'),
				'gst_no'         => $this->input->post('gst_no'),
			);
			$id = $this->kaabar->save('party_sites', $data, $row);
			$this->_updateContacts($party_id, $id);

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/site/$party_id/$id");
		}
	}

	function _updateContacts($party_id, $id) {
		$delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		$designations = $this->input->post('designation');
		if ($designations != null) {
			$person_names = $this->input->post('person_name');
			$mobiles      = $this->input->post('mobile');
			$email        = $this->input->post('pc_email');

			foreach ($designations as $index => $designation) {
				if (strlen(trim($mobiles[$index])) > 0 OR strlen(trim($email[$index])) > 0) {
					$data = array(
						'designation' => $designation,
						'person_name' => $person_names[$index],
						'mobile'      => $mobiles[$index],
						'email'       => $email[$index],
					);
					$this->kaabar->save('party_contacts', $data, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				$this->kaabar->delete('party_contacts', ['id' => $index]);
			}
		}

		$new_designations = $this->input->post('new_designation');
		if($new_designations != null) {
			$person_names = $this->input->post('new_person_name');
			$mobiles      = $this->input->post('new_mobile');
			$email        = $this->input->post('new_email');

			foreach ($new_designations as $index => $designation) {
				if (strlen(trim($mobiles[$index])) > 0 OR strlen(trim($email[$index])) > 0) {
					$data = array(
						'party_id'      => $party_id,
						'party_site_id' => $id,
						'designation'   => $designation,
						'person_name'   => $person_names[$index],
						'mobile'        => $mobiles[$index],
						'email'         => $email[$index],
					);
					$this->kaabar->save('party_contacts', $data);
				}
			}
		}
	}

	function ajaxSite($party_id) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, name, address FROM party_sites WHERE party_id = $party_id AND name LIKE '%$search%' ORDER BY name";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxEmail($party_id = 0, $party_name = null) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			if ($party_id > 0) {
				$sql = "SELECT email, name FROM parties 
				WHERE id = $party_id AND (name LIKE '%$search%' OR email LIKE '%$search%')
				UNION
				SELECT DISTINCT email, person_name FROM party_contacts 
				WHERE party_id = $party_id AND (person_name LIKE '%$search%' OR email LIKE '%$search%')
				UNION
				SELECT DISTINCT email, fullname FROM users 
				WHERE (fullname LIKE '%$search%' OR email LIKE '%$search%')
				ORDER BY email";
				$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
			}
			else {
				$sql = "SELECT P.email, P.name FROM parties P
				WHERE P.name LIKE '%" . urldecode($party_name) . "%' AND LENGTH(P.email) > 0
				UNION
				SELECT DISTINCT PC.email, PC.person_name 
				FROM party_contacts PC INNER JOIN parties P ON PC.party_id = P.id
				WHERE P.name LIKE '%" . urldecode($party_name) . "%' AND LENGTH(PC.email) > 0
				UNION
				SELECT DISTINCT email, fullname FROM users 
				WHERE (fullname LIKE '%$search%' OR email LIKE '%$search%')
				ORDER BY email";
				$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
			}
		}
		else
			echo "Access Denied";
	}

	function delete($id = 0, $field = 'id') {
		$can_delete = TRUE;

		// Find all Tables having Columns ending with %ledger_id
		$query = $this->db->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->db->database."' AND COLUMN_NAME LIKE '%party_id'");
		$tables = $query->result_array();

		// Check if the existing $id is used in any of the tables.
		foreach ($tables as $t) {
			$query = $this->db->query("SELECT " . $t['COLUMN_NAME'] . " FROM " . $t['TABLE_NAME'] . " WHERE " . $t['COLUMN_NAME'] . " = $id");
			if ($query->num_rows() > 0) {
				$can_delete = FALSE;
				break;
			}
		}

		// Delete if not used.
		if ($can_delete) {
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Party Deleted Successfully', 'success');
		}
		else
			setSessionError('Cannot Delete. Party is currently in use in <strong>' . humanize($t['TABLE_NAME']) . '</strong>');
		
		redirect($this->agent->referrer());
	}

	function ajax($table = FALSE, $field = 'name') {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, name, email FROM $this->_table
			WHERE name LIKE '%$search%' 
			ORDER BY name
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function excel() {
		$query = $this->db->query("SELECT * FROM $this->_table ORDER BY name");
		$rows  = $query->result_array();

		$this->_excel($rows);
	}
}
