<?php

class Ledger extends MY_Controller {
	var $_table2;
	
	function __construct() {
		parent::__construct();
	
		//$this->load->model('accounting');
	}
		
	function index($category = 'General', $starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class."/index/$category");
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class."/index/$category");
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('ID', 'Code', 'Name', 'Group', 'Parent', 'Account Group', 'TDS Class', 'Dr / Cr', 'Op. Balance'),
			'class' => array(
				'id'              => 'ID',
				'code'            => 'Text',
				'name'            => 'Text',
				'group_name'      => 'Text tiny orange',
				'parent_name'     => 'Text tiny green',
				'account_group'   => 'Text tiny',
				'tds_class'       => 'Text tiny',
				'dr_cr'           => 'Text',
				'opening_balance' => 'Numeric'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		$data['label_class'] = $this->accounting->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class."/index/$category");
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		$config['total_rows']  = $this->accounting->countLedgers($category, $search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->accounting->getLedgers($category, $search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(
			'<div class="btn-group">' . 
				anchor('#', '<i class="fa fa-plus"></i> Add <span class="caret"></span>', 'class="btn btn-success dropdown-toggle" data-toggle="dropdown"') . '<ul class="dropdown-menu">' . 
				'<li>' . anchor($this->_clspath.$this->_class."/edit/General/0", 'General') . '</li>' .
				'<li>' . anchor($this->_clspath.$this->_class."/edit/Bank/0", 'Bank') . '</li>' .
				'<li>' . anchor($this->_clspath.$this->_class."/edit/Party/0", 'Party') . '</li>' .
				'<li>' . anchor($this->_clspath.$this->_class."/edit/Vessel/0", 'Vessel') . '</li>' .
				'<li>' . anchor($this->_clspath.$this->_class."/edit/Agent/0", 'Agent') . '</li>' .
				'<li>' . anchor($this->_clspath.$this->_class."/edit/Staff/0", 'Staff') . '</li>' . 
				'<li>' . anchor($this->_clspath.$this->_class."/edit/Vehicle/0", 'Vehicle') . '</li>' .
				'</ul></div>'
		);

		$data['category']   = $category;
		$data['page_title'] = $category . ' Ledger';
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($category = 'General', $id = 0, $company_id = null) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('account_group_id', 'Account Group', 'trim|required');
		$this->form_validation->set_rules('code', 'Code', "trim|required|callback__is_unique[$id]");
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('opening_balance', 'Opening Balance', 'trim|required');
		$this->form_validation->set_rules('tds_class_id', 'TDS Class', 'trim');
		$this->form_validation->set_rules('stax_category_id', 'STAX Category', 'trim');
		
		if (! is_null($company_id)) {
			$default_company = $this->session->userdata('default_company');
			$row = $this->kaabar->getRow('companies', $company_id);
			$default_company['id'] = $row['id'];
			$default_company['code'] = $row['code'];
			$this->session->set_userdata("default_company", $default_company);
			$this->accounting->setCompany($default_company['id']);
			setSessionAlert('COMPANY_CHANGED', 'info');
		}

		$default_company = $this->session->userdata('default_company');
		$row = $this->kaabar->getRow($this->_table, array('company_id' => $default_company['id'], 'id' => $id));
		if($row == false) {
			$row = array(
				'id'                  => 0,
				'category'            => $category,
				'group_name'          => '',
				'account_group_id'    => 0,
				'parent_ledger_id'    => 0,
				'reference_ledger_id' => 0,
				'code'                => '',
				'name'                => '', 
				'dr_cr'               => 'Dr',
				'opening_balance'     => 0,
				'tds_class_id'        => 0,
				'stax_category_id'    => 0,
				'reimbursement'		  => 'No',
				'active'              => 'Yes',
				'party_id'            => 0,
				'vessel_id'           => 0,
				'agent_id'            => 0,
				'staff_id'            => 0,
				'vehicle_id'          => 0,
				'monitoring_id'       => 0,
				'finalizing1_id'      => 0,
				'finalizing2_id'      => 0,
			);
		}

		$data['parent_code_name'] = $this->kaabar->getField($this->_table, $row['parent_ledger_id'], 'id', 'CONCAT(code, " - ", name)');
		$data['categories'] = $this->accounting->getCategories();

		if (strtolower($row['category']) == 'bank') {
			$this->form_validation->set_rules('account_no', 'Account No', 'trim');
			$this->form_validation->set_rules('address', 'Address', 'trim');
			$this->form_validation->set_rules('city_id', 'City', 'trim');
			$this->form_validation->set_rules('contact', 'Contact', 'trim');
			$this->form_validation->set_rules('fax', 'Fax', 'trim');
			
			$row += array(
				'account_no' => '', 
				'address'    => '',
				'city_id'    => 0,
				'contact'    => '',
				'fax'        => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if (strtolower($category) == 'party') {
			$this->form_validation->set_rules('party_id', 'Party Master', 'trim|required|is_natural_no_zero');
			$data['party_name']       = $this->kaabar->getField('parties', $row['party_id'], 'id', 'name');
			$data['monitoring_name']  = $this->kaabar->getField('staffs', $row['monitoring_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
			$data['finalizing1_name'] = $this->kaabar->getField('staffs', $row['finalizing1_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
			$data['finalizing2_name'] = $this->kaabar->getField('staffs', $row['finalizing2_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
		}
		else if (strtolower($category) == 'vessel') {
			$this->form_validation->set_rules('vessel_id', 'Vessel Master', 'trim|required|is_natural_no_zero');
			$data['vessel_name'] = $this->kaabar->getField('vessels', $row['vessel_id'], 'id', 'CONCAT(prefix, " ", name, " ", voyage_no)');
		}
		else if (strtolower($category) == 'agent') {
			$this->form_validation->set_rules('agent_id', 'Agent Master', 'trim|required|is_natural_no_zero');
			$data['agent_name'] = $this->kaabar->getField('agents', $row['agent_id'], 'id', 'name');
			$data['monitoring_name']  = $this->kaabar->getField('staffs', $row['monitoring_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
			$data['finalizing1_name'] = $this->kaabar->getField('staffs', $row['finalizing1_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
			$data['finalizing2_name'] = $this->kaabar->getField('staffs', $row['finalizing2_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
		}
		else if (strtolower($category) == 'staff') {
			$this->form_validation->set_rules('staff_id', 'Staff Master', 'trim|required|is_natural_no_zero');
			$data['staff_name'] = $this->kaabar->getField('staffs', $row['staff_id'], 'id', 'CONCAT(title, " ", firstname, " ", middlename, " ", lastname)');
		}
		else if (strtolower($category) == 'vehicle') {
			$this->form_validation->set_rules('vehicle_id', 'Vehicle Master', 'trim|required|is_natural_no_zero');
			$data['registration_no'] = $this->kaabar->getField('vehicles', $row['vehicle_id'], 'id', 'registration_no');
		}

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['category']   = $category;
			$data['page_title'] = humanize($category) . ' Ledger';
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.strtolower($row['category']).'_ledger_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$category/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$category = $this->input->post('category');
				$data = array(
					'company_id'          => $default_company['id'],
					'account_group_id'    => $this->input->post('account_group_id'),
					'category'            => $category,
					'group_name'          => ($this->input->post('group_name') ? $this->input->post('group_name') : ''),
					'parent_ledger_id'    => $this->input->post('parent_ledger_id'),
					'reference_ledger_id' => $this->input->post('reference_ledger_id'),
					'code'                => strtoupper($this->input->post('code')),
					'name'                => $this->input->post('name'),
					'dr_cr'               => $this->input->post('dr_cr'),
					'opening_balance'     => ($this->input->post('dr_cr') == 'Cr' ? '-'.abs($this->input->post('opening_balance')) : abs($this->input->post('opening_balance'))),
					'tds_class_id'        => $this->input->post('tds_class_id'),
					'stax_category_id'    => $this->input->post('stax_category_id'),
				    'reimbursement'       => $this->input->post('reimbursement'),
				    'active'       => $this->input->post('active'),
					'party_id'            => $this->input->post('party_id'),
					'vessel_id'           => $this->input->post('vessel_id'),
					'agent_id'            => $this->input->post('agent_id'),
					'staff_id'            => $this->input->post('staff_id'),
					'vehicle_id'          => $this->input->post('vehicle_id'),
					'monitoring_id'       => $this->input->post('monitoring_id'),
					'finalizing1_id'      => $this->input->post('finalizing1_id'),
					'finalizing2_id'      => $this->input->post('finalizing2_id'),
				);
				$id = $this->kaabar->save($this->_table, $data, $row);

				// Updating Group Name of same code in other companies as well.
				$this->db->query('UPDATE ledgers SET group_name = ? WHERE code = ?', array($data['group_name'], $data['code']));

				if (Auth::isAdmin() && $this->input->post('save_to') == 'G') {
					$this->db->query('UPDATE ledgers SET monitoring_id = ?, finalizing1_id = ?, finalizing2_id = ? WHERE group_name = ?', 
						array($this->input->post('monitoring_id'), $this->input->post('finalizing1_id'), $this->input->post('finalizing2_id'), $data['group_name'])
					);
				}
				
				unset($row['id']);
				unset($row['tariffs']);
				foreach ($data as $field => $value) {
					unset($row[$field]);
				}
				
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			if (is_null($company_id))
				redirect($this->_clspath.$this->_class."/edit/$category/$id");
			else
				echo closeWindow();
		}
	}

	public function _is_unique($code, $id) {
		if($this->accounting->isDuplicateCode($code, $id))
			return TRUE;
		else {
			$this->form_validation->set_message('_is_unique', 'Duplicate Code, Code already Exists.');
			return FALSE;
		}
	}
	
	
	function delete($id) {
		if (Auth::hasAccess(Auth::DELETE)) {
			$can_delete = TRUE;

			// Find all Tables having Columns ending with %ledger_id
			$query = $this->db->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND COLUMN_NAME LIKE '%ledger_id'");
			$tables = $query->result_array();

			// Check if the existing $id is used in any of the tables.
			foreach ($tables as $t) {
				if ($t['TABLE_NAME'] == $this->_table2) 
					continue;

				$query = $this->db->query("SELECT " . $t['COLUMN_NAME'] . " FROM " . $t['TABLE_NAME'] . " WHERE " . $t['COLUMN_NAME'] . " = $id");
				if ($query->num_rows() > 0) {
					$can_delete = FALSE;
					break;
				}
			}

			// Delete if not used.
			if ($can_delete) {
				$ldetails = $this->kaabar->getRows($this->_table2, array('ledger_id' => $id));
				foreach ($ldetails as $ld)
					$this->kaabar->delete($this->_table2, $ld['id']);

				$this->kaabar->delete($this->_table, $id);
				setSessionAlert('Ledger Deleted Successfully', 'success');
			}
			else
				setSessionError('Cannot Delete. Ledger is currently in use in <strong>' . humanize($t['TABLE_NAME']) . '</strong>');
		}
		else 
			setSessionError('NO_PERMISSION');
		
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}
	

	function ajax() {
		if ($this->input->is_ajax_request()) {
			$search = addslashes(strtoupper($this->input->get('term')));
			$sql = $this->accounting->getClosingSql($search);
			$this->kaabar->getJson($sql);
		}
		else
			echo "Access Denied";
	}

	function ajaxParent($category, $id) {
		$default_company = $this->session->userdata('default_company');
		$company_id = $default_company['id'];
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT L.id, L.code, L.name, L.group_name
				FROM ledgers L 
				WHERE L.id != $id AND 
					(company_id = $company_id AND category = '$category' AND parent_ledger_id = 0) AND
					(L.code LIKE '%$search%' OR L.name LIKE '%$search%')
				ORDER BY L.code";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxChild($category, $id, $all = 1) {
		$default_company = $this->session->userdata('default_company');
		$company_id = $default_company['id'];
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT L.id, L.code, L.name, L.opening_balance, L.dr_cr
				FROM ledgers L 
				WHERE (company_id = $company_id AND 
					category = '$category' AND 
					parent_ledger_id = $id" . ($all ? '' : ' AND reference_ledger_id = 0') . ") AND
					(L.code LIKE '%$search%' OR L.name LIKE '%$search%')
				ORDER BY L.code";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxLedgers($category, $type = null) {
		if ($this->input->is_ajax_request()) {
			
			$default_company = $this->_default_company;
			$company_id = $default_company['id'];
			unset($default_company);
			
			$search   = addslashes(strtolower($this->input->get('term')));
			$category = urldecode($category);
		
			// type is used for Agent Type, should not be used now, cause Agent Master is now separate from ledgers.
			$sql = "SELECT L.id, L.code, L.name 
				FROM ledgers L
				WHERE (L.company_id = $company_id AND 
					L.category = '$category' AND 
					L.active = 'Yes') AND 
					(L.code LIKE '%$search%' OR L.name LIKE '%$search%')
				ORDER BY name LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo 
			"Access Denied";
		}
	}

	function ajaxReferenceByParty($party_id) {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			unset($default_company);
			
			$search = addslashes(strtolower($this->input->get('term')));
			$sql = "SELECT R.id, R.code, R.name
				FROM ledgers L INNER JOIN ledgers R ON L.reference_ledger_id = R.id
				WHERE (L.company_id = $company_id AND R.company_id = $company_id AND L.party_id = $party_id AND L.category = 'Party' AND L.reference_ledger_id > 0) AND (R.code LIKE '%$search%' OR R.name LIKE '%$search%')
				ORDER BY R.code";
			$this->kaabar->getJson($sql);
		}
		else {
			echo 
			"Access Denied";
		}
	}

	function ajaxReferenceByLedger($ledger_id) {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			unset($default_company);
			
			$search = addslashes(strtolower($this->input->get('term')));
			$sql = "SELECT R.id, R.code, R.name
				FROM ledgers L INNER JOIN ledgers R ON L.reference_ledger_id = R.id
				WHERE (L.company_id = $company_id AND R.company_id = $company_id AND L.parent_ledger_id = $ledger_id AND L.category = 'Party' AND L.reference_ledger_id > 0) AND (R.code LIKE '%$search%' OR R.name LIKE '%$search%')
				ORDER BY R.code";
			$this->kaabar->getJson($sql);
		}
		else {
			echo 
			"Access Denied";
		}
	}
}
