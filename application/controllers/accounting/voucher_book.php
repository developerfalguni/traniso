<?php

class Voucher_book extends MY_Controller {
	function __construct() {
		parent::__construct();
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
			'heading' => array('ID', 'Voucher Type', 'Code', 'Name', 'Default Ledger', 'Job Type', 'Auto Numering', 'Lock'),
			'class' => array(
				'id'             => 'ID', 
				'voucher_type'   => 'Text',
				'code'           => 'Text',
				'name'           => 'Text',
				'default_ledger' => 'Text',
				'job_type'       => 'Text',
				'auto_numbering' => 'Label',
				'date_lock'      => 'Date'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		$data['label_class'] = $this->accounting->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class."/index");
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_count($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _count($search = '') {
		$sql = "SELECT COUNT(VB.id) AS numrows
		FROM voucher_books VB
			LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			LEFT OUTER JOIN voucher_details VJD ON VB.id = VJD.voucher_id
		WHERE VB.company_id = ? AND (
			VT.name LIKE '%$search%' OR
			VB.code LIKE '%$search%' OR
			VB.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company['id']));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _get($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT VB.id, VT.name AS voucher_type, VB.code, VB.name, CONCAT(VB.dr_cr, ' - ', L.name) AS default_ledger, 
			VB.job_type, VB.auto_numbering, DATE_FORMAT(VB.date_lock, '%d-%m-%Y') AS date_lock
		FROM voucher_books VB
			LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			LEFT OUTER JOIN ledgers L ON VB.default_ledger_id = L.id
		WHERE VB.company_id = ? AND (
			VT.name LIKE '%$search%' OR
			VB.code LIKE '%$search%' OR
			VB.name LIKE '%$search%')
		ORDER BY VB.code
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company['id']));
		return $query->result_array();
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Voucher Book Code', 'trim|required');
		$this->form_validation->set_rules('name', 'Voucher Book Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'                => 0,
				'voucher_type_id'   => 0,
				'code'              => '',
				'name'              => '',
				'print_name'        => '',
				'dr_cr'             => 'Dr',
				'default_ledger_id' => 0,
				'id2_format'        => '[[book]]/[[year]]/[[num]]',
				'job_type'          => 'N/A',
				'auto_numbering'    => 'No',
				'date_lock'         => '00-00-0000'
			);

			if ($this->input->method() == 'post') {
				$post = $this->input->post(array_keys($row));
				foreach($post as $f => $v) {
					if ($v) $row[$f] = $v;
				}
			}
		}
		
		$data['id'] = ['id' => $id];
		$data['row'] = $row;
		$data['row']['default_ledger_name'] = $this->kaabar->getField('ledgers', $row['default_ledger_id'], 'id', 'CONCAT(code, " - ", name)');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			$default_company = $this->session->userdata('default_company');
			
			if ($this->input->post('voucher_type_id') == 3 OR $this->input->post('voucher_type_id') == 4)
				$auto_numbering = 'No';
			else
				$auto_numbering = ($this->input->post('auto_numbering') ? 'Yes' : 'No');

			$data = array(
				'company_id'        => $default_company['id'],
				'voucher_type_id'   => $this->input->post('voucher_type_id'),
				'code'              => $this->input->post('code'),
				'name'              => $this->input->post('name'),
				'print_name'        => $this->input->post('print_name'),
				'dr_cr'             => $this->input->post('dr_cr'),
				'default_ledger_id' => $this->input->post('default_ledger_id'),
				'id2_format'        => $this->input->post('id2_format'),
				'job_type'          => $this->input->post('job_type'),
				'auto_numbering'    => $auto_numbering,
				'date_lock'         => $this->input->post('date_lock'),
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class);
		}
	}

	function renumber() {
		$default_company = $this->session->userdata('default_company');
		$query = $this->db->query("SELECT id, code FROM voucher_books WHERE company_id = ? AND auto_numbering = 'Yes'", 
			array($default_company['id'])
		);
		$rows = $query->result_array();

		$years      = explode('_', $default_company['financial_year']);
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';
		$year       = substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2);

		foreach ($rows as $voucher_book) {
			$query = $this->db->query("SET @newid = 0");
			$query = $this->db->query("UPDATE vouchers SET id2 = (@newid := @newid + 1), id2_format = CONCAT('" . $voucher_book['code'] . "/$year/', LPAD(@newid, 3, '0'))
				WHERE voucher_book_id = ?
				ORDER BY id", 
				array($voucher_book['id'])
			);
		}
		setSessionAlert('Vouchers have been re-numbered successfully.', 'success');
		redirect($this->agent->referrer());
	}
}
