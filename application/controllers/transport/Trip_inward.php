<?php

use mikehaertl\wkhtmlto\Pdf;

class Trip_inward extends MY_Controller {
	var $_company_id;
	var $_table2 = '';

	function __construct() {
		parent::__construct();

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);

		$this->_table2 = 'trip_inward_details';
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
		
		$data['list']['heading'] = array('ID', 'Type', 'Bill No', 'Date', 'Transporter Bill No', 'Transporter Name', 'Trips', 'Cheque No', 'Cheque Date', 'Processed On');
		$data['list']['class'] = array(
			'id'               => 'ID',
			'type'             => 'Code',
			'rishi_bill_no'    => 'Code',
			'date'             => 'Date',
			'bill_no'          => 'Text',
			'transporter_name' => 'Text',
			'trips'            => 'Numeric',
			'cheque_no'        => 'Code',
			'cheque_date'      => 'Date',
			'processed_date'   => 'Date');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_countTransporterInwards($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_getTransporterInwards($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = 'Transporter ' . humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getInwardODetails($id) {
		$sql = "SELECT ID.*
		FROM trip_inward_details ID 
		WHERE ID.trip_id = 0 AND ID.trip_inward_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('rishi_bill_no', 'Party Bill No', 'trim|required');
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('transporter_ledger_id', 'Transporter Name', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('cheque_no', 'Cheque No', 'trim');
		$this->form_validation->set_rules('cheque_date', 'Cheque Date', 'trim');
		$this->form_validation->set_rules('processed_date', 'Process Date', 'trim');

		$row = $this->kaabar->getRow($this->_table, array('id' => $id, 'company_id' => $this->_company_id));
		if($row == false) {
			$row = array(
				'id'                    => 0,
				'company_id'            => $this->_company_id,
				'id2'                   => 0,
				'type'                  => 'Transporter',
				'date'                  => date('d-m-Y'),
				'rishi_bill_no'         => '',
				'bill_no'               => '',
				'bill_date'             => '',
				'transporter_ledger_id' => 0,
				'cheque_no'             => '',
				'cheque_date'           => '',
				'processed_date'        => '',
				'remarks'               => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
		$data['transporter_name'] = $this->kaabar->getField('ledgers', $row['transporter_ledger_id'], 'id', 'name');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['rows'] = $this->_getTransporterInwardDetails($id);
			$data['inward_odetails'] = $this->_getInwardODetails($id);

			$data['from_date'] = date('01-m-Y');
			$data['to_date']   = date('d-m-Y');

			$data['page_title']  = humanize($this->_class);
			$data['hide_title']  = true;
			$data['hide_footer'] = true;
			$data['page']        = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']    = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'company_id'            => $row['company_id'],
					'rishi_bill_no'         => $this->input->post('rishi_bill_no'),
					'date'                  => $this->input->post('date'),
					'bill_no'               => $this->input->post('bill_no'),
					'bill_date'             => $this->input->post('bill_date'),
					'transporter_ledger_id' => $this->input->post('transporter_ledger_id'),
					'cheque_no'             => $this->input->post('cheque_no'),
					'cheque_date'           => $this->input->post('cheque_date'),
					'processed_date'        => $this->input->post('processed_date'),
					'remarks'               => $this->input->post('remarks')
				);
				if ($id == 0) {
					$data += array('type' => $this->input->post('type'));
				}
				$id = $this->kaabar->save($this->_table, $data, $row);
				if (strlen($row['cheque_no']) == 0) {
					$this->_updateDetails($id);
					$this->_updateOtherDetails($id);
				}
				else
					setSessionAlert('Trip Entires locked due to Cheque No.', 'error');
				
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function preview($id = 0, $pdf = 0, $details = 0) {
		$id = intval($id);
		
		$this->load->helper('numwords');
		
		$default_company            = $this->session->userdata('default_company');
		$data['company']            = $this->kaabar->getRow('companies', $default_company['id']);
		$data['bills']              = $this->kaabar->getRow($this->_table, $id);
		$data['transporter_ledger'] = $this->kaabar->getRow('ledgers', $data['bills']['transporter_ledger_id']);
		$data['rows']               = $this->_getTransporterInwardDetails($id);
		$data['odetails']           = $this->kaabar->getRows('trip_inward_details', array('trip_inward_id' => $id, 'trip_id' => 0));
		$data['details']            = $details;
		
		$data['max_items'] = 40;
		if ($pdf) {
			$filename = str_replace('/', '-', $data['bills']['rishi_bill_no']);
			$html = $this->load->view("reports/trip_inward_preview", $data, true);
			
			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/trip_inward_preview", $data);
		}
	}

	
	function fuel($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class.'/fuel');
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class.'/fuel');
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Type', 'Bill No', 'Date', 'Transporter Bill No', 'Transporter Name', 'Trips', 'Cheque No', 'Cheque Date', 'Processed On');
		$data['list']['class'] = array(
			'id'               => 'ID',
			'type'             => 'Code',
			'rishi_bill_no'    => 'Code',
			'date'             => 'Date',
			'bill_no'          => 'Text',
			'transporter_name' => 'Text',
			'trips'            => 'Numeric',
			'cheque_no'        => 'Code',
			'cheque_date'      => 'Date',
			'processed_date'   => 'Date');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/fuel_edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/fuel');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_countFuelInwards($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_getFuelInwards($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/fuel_edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = 'Transporter ' . humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function fuel_edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('rishi_bill_no', 'Rishi Bill No', 'trim|required');
		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('transporter_ledger_id', 'Transporter Name', 'trim|required|is_natural_no_zero');
		$this->form_validation->set_rules('cheque_no', 'Cheque No', 'trim');
		$this->form_validation->set_rules('cheque_date', 'Cheque Date', 'trim');
		$this->form_validation->set_rules('processed_date', 'Process Date', 'trim');

		$row = $this->kaabar->getRow($this->_table, array('id' => $id, 'company_id' => $this->_company_id));
		if($row == false) {
			$row = array(
				'id'                    => 0,
				'company_id'            => $this->_company_id,
				'id2'                   => 0,
				'type'                  => 'Fuel',
				'date'                  => date('d-m-Y'),
				'rishi_bill_no'         => '',
				'bill_no'               => '',
				'bill_date'             => '',
				'transporter_ledger_id' => 0,
				'cheque_no'             => '',
				'cheque_date'           => '',
				'processed_date'        => '',
				'remarks'               => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
		$data['transporter_name'] = $this->kaabar->getField('ledgers', $row['transporter_ledger_id'], 'id', 'name');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['rows'] = $this->_getFuelInwardDetails($id);

			$data['from_date'] = date('01-m-Y');
			$data['to_date']   = date('d-m-Y');

			$data['page_title']  = humanize($this->_class);
			$data['hide_title']  = true;
			$data['hide_footer'] = true;
			$data['page']        = $this->_clspath.$this->_class.'_fuel_edit';
			$data['docs_url']    = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/fuel_edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'company_id'            => $row['company_id'],
					'rishi_bill_no'         => $this->input->post('rishi_bill_no'),
					'date'                  => $this->input->post('date'),
					'bill_no'               => $this->input->post('bill_no'),
					'bill_date'             => $this->input->post('bill_date'),
					'transporter_ledger_id' => $this->input->post('transporter_ledger_id'),
					'cheque_no'             => $this->input->post('cheque_no'),
					'cheque_date'           => $this->input->post('cheque_date'),
					'processed_date'        => $this->input->post('processed_date'),
					'remarks'               => $this->input->post('remarks')
				);
				if ($id == 0) {
					$data += array('type' => $this->input->post('type'));
				}
				$id = $this->kaabar->save($this->_table, $data, $row);
				if (strlen($row['cheque_no']) == 0)
					$this->_updateDetails($id);
				else
					setSessionAlert('Trip Entires locked due to Cheque No.', 'error');
				
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/fuel_edit/$id");
		}
	}

	function fuel_preview($id = 0, $pdf = 0, $details = 0) {
		$id = intval($id);
		
		$this->load->helper('numwords');
		
		$default_company            = $this->session->userdata('default_company');
		$data['company']            = $this->kaabar->getRow('companies', $default_company['id']);
		$data['bills']              = $this->kaabar->getRow($this->_table, $id);
		$data['transporter_ledger'] = $this->kaabar->getRow('ledgers', $data['bills']['transporter_ledger_id']);
		$data['rows']               = $this->_getFuelInwardDetails($id);
		$data['details']            = $details;
		$data['odetails']           = $this->kaabar->getRows('trip_inward_details', array('trip_inward_id' => $id, 'trip_id' => 0));
		
		$data['max_items'] = 40;
		if ($pdf) {
			$filename = str_replace('/', '-', $data['bills']['rishi_bill_no']);
			$html = $this->load->view("reports/trip_inward_preview", $data, true);
			
			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/trip_inward_preview", $data);
		}
	}

	

	function _updateDetails($id) {
		$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		$amounts = $this->input->post('amount');
		if ($amounts != null) {
			foreach ($amounts as $index => $amount) {
				if (! in_array("$index", $delete_ids)) {
					$row = array(
						'amount' => $amount
					);
					$this->kaabar->save($this->_table2, $row, array('id' => $index));
				}
			}
		}

		if ($delete_ids != null) {	
			foreach ($delete_ids as $index => $tmp) {
				$this->db->delete($this->_table2, array('id' => $index));
			}
		}

		$new_trip_ids = $this->input->post('new_trip_id');
		if($new_trip_ids != null){
			$new_amounts          = $this->input->post('new_amount');
			$new_pump_advance_ids = $this->input->post('new_pump_advance_id');
			
			foreach ($new_trip_ids as $index => $new_trip_id) {
				if (strlen(trim($new_trip_id)) > 0) {
					$row = array(
						'trip_inward_id'  => $id,
						'trip_id'         => $new_trip_ids[$index],
						'amount'          => ($new_amounts[$index] > 0 ? $new_amounts[$index] : 0),
						'pump_advance_id' => intval($new_pump_advance_ids[$index]),
					);
					$this->kaabar->save($this->_table2, $row);
				}
			}
		}
	}

	function _updateOtherDetails($id) {
		$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		$amounts = $this->input->post('oamount');

		if ($amounts != null) {
			$sr_nos      = $this->input->post('osr_no');
			$particulars = $this->input->post('oparticulars');
			
			foreach ($amounts as $index => $amount) {
				if (! in_array("$index", $delete_ids)) {
					$data = array(
						'trip_inward_id' => $id,
						'sr_no'               => $sr_nos[$index],
						'particulars'         => $particulars[$index],
						'amount'              => $amount,
					);
					$this->kaabar->save($this->_table2, $data, array('id' => $index));
				}
			}
		}
		
		if ($delete_ids != null) {
			if (Auth::hasAccess(Auth::DELETE)) {
				foreach ($delete_ids as $index) {
					$this->kaabar->delete($this->_table2, $index);
				}
			}
			else
				setSessionError('NO_PERMISSION');
		}

		$amounts = $this->input->post('new_oamount');
		if ($amounts != null) {
			$sr_nos      = $this->input->post('new_osr_no');
			$particulars = $this->input->post('new_oparticulars');
			
			foreach ($amounts as $index => $amount) {
				if (strlen($particulars[$index]) > 0 OR $amount > 0) {
					$data = array(
						'trip_inward_id' => $id,
						'sr_no'               => $sr_nos[$index],
						'particulars'         => $particulars[$index],
						'amount'              => $amount
					);
					$this->kaabar->save($this->_table2, $data);
				}
			}
		}
	}

	function _countTransporterInwards($search = '') {
		$sql = "SELECT COUNT(CI.id) AS numrows
		FROM trip_inwards CI INNER JOIN ledgers TL ON CI.transporter_ledger_id = TL.id
		WHERE CI.company_id = $this->_company_id AND CI.type = 'Transporter' AND (
			CI.rishi_bill_no LIKE '%$search%' OR
			DATE_FORMAT(CI.date, '%d-%m-%Y') LIKE '%$search%' OR
			CI.bill_no LIKE '%$search%' OR
			TL.name LIKE '%$search%' OR
			CI.cheque_no LIKE '%$search%' OR
			DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			DATE_FORMAT(CI.processed_date, '%d-%m-%Y') LIKE '%$search%')";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _getTransporterInwards($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT CI.id, CI.type, CI.rishi_bill_no, DATE_FORMAT(CI.date, '%d-%m-%Y') AS date, CI.bill_no, TL.name AS transporter_name,
			COUNT(DISTINCT T.id) AS trips, 
			CI.cheque_no, DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') AS cheque_date, DATE_FORMAT(CI.processed_date, '%d-%m-%Y') AS processed_date
		FROM trip_inwards CI INNER JOIN ledgers TL ON CI.transporter_ledger_id = TL.id
			LEFT OUTER JOIN trip_inward_details CID ON CI.id = CID.trip_inward_id
			LEFT OUTER JOIN trips T ON CID.trip_id = T.id
		WHERE CI.company_id = $this->_company_id AND CI.type = 'Transporter' AND (
			CI.rishi_bill_no LIKE '%$search%' OR
			DATE_FORMAT(CI.date, '%d-%m-%Y') LIKE '%$search%' OR
			CI.bill_no LIKE '%$search%' OR
			TL.name LIKE '%$search%' OR
			CI.cheque_no LIKE '%$search%' OR
			DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			DATE_FORMAT(CI.processed_date, '%d-%m-%Y') LIKE '%$search%')
		GROUP BY CI.id
		ORDER BY CI.rishi_bill_no DESC, CI.date DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function _countFuelInwards($search = '') {
		$sql = "SELECT COUNT(CI.id) AS numrows
		FROM trip_inwards CI INNER JOIN ledgers TL ON CI.transporter_ledger_id = TL.id
		WHERE CI.company_id = $this->_company_id AND CI.type = 'Fuel' AND (
			CI.rishi_bill_no LIKE '%$search%' OR
			DATE_FORMAT(CI.date, '%d-%m-%Y') LIKE '%$search%' OR
			CI.bill_no LIKE '%$search%' OR
			TL.name LIKE '%$search%' OR
			CI.cheque_no LIKE '%$search%' OR
			DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			DATE_FORMAT(CI.processed_date, '%d-%m-%Y') LIKE '%$search%')";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _getFuelInwards($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT CI.id, CI.type, CI.rishi_bill_no, DATE_FORMAT(CI.date, '%d-%m-%Y') AS date, CI.bill_no, TL.name AS transporter_name, 
			COUNT(T.id) AS trips, 
			ROUND(SUM(COALESCE(PA.amount, 0) + COALESCE(PA.amount, 0)), 2) AS amount, 
			CI.cheque_no, DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') AS cheque_date, DATE_FORMAT(CI.processed_date, '%d-%m-%Y') AS processed_date
		FROM trip_inwards CI  INNER JOIN ledgers TL ON CI.transporter_ledger_id = TL.id
			LEFT OUTER JOIN trip_inward_details CID ON CI.id = CID.trip_inward_id
			LEFT OUTER JOIN trips T ON CID.trip_id = T.id
			LEFT OUTER JOIN pump_advances PA ON CID.trip_id = PA.trip_id AND CID.pump_advance_id = PA.id
		WHERE CI.company_id = $this->_company_id AND CI.type = 'Fuel' AND (
			CI.rishi_bill_no LIKE '%$search%' OR
			DATE_FORMAT(CI.date, '%d-%m-%Y') LIKE '%$search%' OR
			CI.bill_no LIKE '%$search%' OR
			TL.name LIKE '%$search%' OR
			CI.cheque_no LIKE '%$search%' OR
			DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			DATE_FORMAT(CI.processed_date, '%d-%m-%Y') LIKE '%$search%')
		GROUP BY CI.id
		ORDER BY CI.rishi_bill_no DESC, CI.date DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function _getTransporterInwardDetails($id) {
		$sql = "SELECT T.id AS trip_id, T.job_type, T.party_reference_no AS job_no, T.container_no, T.registration_no, TL.name AS transporter_name,
			DATE_FORMAT(T.date, '%d-%m-%Y') AS date, IF((LENGTH(T.container_no2) != 0), '2 X 20', IF((T.container_size = '40'), '1 X 40', '1 X 20')) AS qty, 
			PL.name AS party_name, T.product_name, FLOC.name AS from_location, TLOC.name AS to_location, PA.slip_no, IF(ISNULL(VTD.id), 0, T.party_rate) AS party_rate,
			T.transporter_rate, ROUND(SUM(PA.amount), 2) AS fuel, PA.pump_inward_id, TA.amount AS advance, VPY.cheque_advance, CID.*, IF(ISNULL(E.id), 0, 1) AS self, CID.amount
		FROM trip_inward_details CID INNER JOIN trips T ON CID.trip_id = T.id
			LEFT OUTER JOIN ledgers PL ON T.party_ledger_id = PL.id
			LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
			LEFT OUTER JOIN locations FLOC ON T.from_location_id = FLOC.id
			LEFT OUTER JOIN locations TLOC ON T.to_location_id = TLOC.id
			LEFT OUTER JOIN rishi.equipments E ON (T.registration_no = E.registration_no AND LENGTH(E.registration_no) > 0)
			LEFT OUTER JOIN voucher_details VTD ON T.id = VTD.trip_id
			LEFT OUTER JOIN (
				SELECT TA.trip_id, 
					IF(TA.advance_by = 'Self', ROUND(SUM(TA.amount), 2), 0) AS self_adv, 
					IF(TA.advance_by = 'Party', ROUND(SUM(TA.amount), 2), 0) AS party_adv,
					SUM(TA.amount) AS amount
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = $this->_company_id
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT PA.trip_id, GROUP_CONCAT(DISTINCT A.name) AS name, GROUP_CONCAT(DISTINCT PA.slip_no) AS slip_no, 
					SUM(PA.amount) AS amount, COALESCE(TID.trip_inward_id, 0) AS pump_inward_id
				FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
					INNER JOIN agents A ON PA.agent_id = A.id
					LEFT OUTER JOIN trip_inward_details TID ON PA.id = TID.pump_advance_id
				WHERE T.company_id = $this->_company_id
				GROUP BY PA.trip_id
			) PA ON T.id = PA.trip_id
			LEFT OUTER JOIN (
				SELECT VT.trip_id, SUM(VT.advance) AS cheque_advance
				FROM voucher_trips VT 
					INNER JOIN vouchers VO ON VT.voucher_id = VO.id
					INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id AND VB.voucher_type_id = 7
				GROUP BY VT.trip_id
			) VPY ON T.id = VPY.trip_id			
		WHERE CID.trip_inward_id = ?
		GROUP BY CID.id";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function _getFuelInwardDetails($id) {
		$sql = "SELECT T.id AS trip_id, T.job_type, T.party_reference_no AS job_no, T.container_no, T.registration_no, TL.name AS transporter_name,
			DATE_FORMAT(T.date, '%d-%m-%Y') AS date, IF((LENGTH(T.container_no2) != 0), '2 X 20', IF((T.container_size = '40'), '1 X 40', '1 X 20')) AS qty, 
			PL.name AS party_name, T.product_name, FLOC.name AS from_location, TLOC.name AS to_location, PA.slip_no, PA.amount AS fuel,
			CID.*, IF(ISNULL(E.id), 0, 1) AS self
		FROM trip_inward_details CID INNER JOIN trips T ON CID.trip_id = T.id
			LEFT OUTER JOIN ledgers PL ON T.party_ledger_id = PL.id
			LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
			LEFT OUTER JOIN locations FLOC ON T.from_location_id = FLOC.id
			LEFT OUTER JOIN locations TLOC ON T.to_location_id = TLOC.id
			LEFT OUTER JOIN rishi.equipments E ON (T.registration_no = E.registration_no AND LENGTH(E.registration_no) > 0)
			LEFT OUTER JOIN pump_advances PA ON (CID.trip_id = PA.trip_id AND CID.pump_advance_id = PA.id)
		WHERE CID.trip_inward_id = ?
		ORDER BY CID.id";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function ajaxTransportTrips() {
		$from_date = convDate($this->input->post('from_date'));
		$to_date   = convDate($this->input->post('to_date'));
		$transporter_ledger_id  = $this->input->post('transporter_ledger_id');

		$optional = array(
			'T.from_location_id'   => $this->input->post('from_location_id'), 
			'T.to_location_id'     => $this->input->post('to_location_id'), 
			'T.party_reference_no' => $this->input->post('party_reference_no'), 
		);

		$optional_sql = '';
		foreach ($optional as $key => $value) {
			if ($value) {
				$optional_sql .= " AND $key = '$value'";
			}
		}


		$sql = "SELECT T.id, T.party_reference_no AS job_no, T.container_no, DATE_FORMAT(T.date, '%d-%m-%Y') AS trip_date, 
		T.registration_no, T.container_no2, T.container_size, 
		PL.name AS party_name, T.product_name, TL.name AS transporter_name, T.lr_no, T.transporter_rate, 
		FLOC.name AS from_location, TLOC.name AS to_location, 0 AS pump_advance_id, ROUND(SUM(COALESCE(PA.amount, 0)), 2) AS fuel, 
		ROUND(SUM(COALESCE(TA.amount, 0)), 2) AS advance, COALESCE(VPY.cheque_advance, 0) AS cheque_advance,
		(T.transporter_rate - (ROUND(SUM(COALESCE(TA.amount, 0)), 2) + ROUND(SUM(COALESCE(PA.amount, 0)), 2) + COALESCE(VPY.cheque_advance, 0))) AS balance,
		IF((LENGTH(T.container_no2) != 0), '2 X 20', IF((T.container_size = '40'), '1 X 40', '1 X 20')) AS qty,
		T.remarks
		FROM trips T 
			LEFT OUTER JOIN ledgers PL ON T.party_ledger_id = PL.id
			LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
			LEFT OUTER JOIN locations FLOC ON T.from_location_id = FLOC.id
			LEFT OUTER JOIN locations TLOC ON T.to_location_id = TLOC.id
			LEFT OUTER JOIN jobs J ON T.job_id = J.id
			LEFT OUTER JOIN (
				SELECT TA.trip_id, 
					IF(TA.advance_by = 'Self', ROUND(SUM(TA.amount), 2), 0) AS self_adv, 
					IF(TA.advance_by = 'Party', ROUND(SUM(TA.amount), 2), 0) AS party_adv,
					SUM(TA.amount) AS amount
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = $this->_company_id
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT PA.trip_id, GROUP_CONCAT(DISTINCT A.name) AS pump_name, SUM(PA.amount) AS amount
				FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
					INNER JOIN agents A ON PA.agent_id = A.id
				WHERE T.company_id = $this->_company_id
				GROUP BY PA.trip_id
			) PA ON T.id = PA.trip_id
			LEFT OUTER JOIN (
				SELECT VT.trip_id, SUM(VT.advance) AS cheque_advance
				FROM voucher_trips VT 
					INNER JOIN vouchers VO ON VT.voucher_id = VO.id
					INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id AND VB.voucher_type_id = 7
				GROUP BY VT.trip_id
			) VPY ON T.id = VPY.trip_id
		WHERE T.transporter_ledger_id = ? AND T.date >= ? AND T.date <= ? AND T.company_id = $this->_company_id AND T.id NOT IN (
				SELECT CID.trip_id 
				FROM trip_inward_details CID INNER JOIN trip_inwards CI ON CID.trip_inward_id = CI.id
				WHERE CI.type = 'Transporter'
			) $optional_sql
		GROUP BY T.id";
		$query = $this->db->query($sql, array(
			$transporter_ledger_id, $from_date, $to_date,
			$this->accounting->getCompanyID(),
		));
		$rows  = $query->result_array();
		$result = '';
		foreach ($rows as $row) {
			$result .= '
			<tr>
				<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="" /></td>
				<td><input type="hidden" class="form-control form-control-sm Validate" name="new_trip_id[]" value="' . $row['id'] . '" />
					<input type="text" class="form-control form-control-sm ContainerNo Validate Focus" value="' . $row['container_no'] . '" /></td>
				<td class="Qty">' . $row['qty'] . '</td>
				<td class="JobNo">' . $row['job_no'] . '</td>
				<td class="VehicleNo">' . $row['registration_no'] . '</td>
				<td class="TripDate">' . $row['trip_date'] . '</td>
				<td class="PartyName">' . $row['party_name'] . '</td>
				<td class="ProductName">' . $row['product_name'] . '</td>
				<td class="FromLocation">' . $row['from_location'] . '</td>
				<td class="ToLocation">' . $row['to_location'] . '</td>
				<td class="alignright">0</td>
				<td class="alignright TransporterRate">' . $row['transporter_rate'] . '</td>
				<td class="alignright Fuel">' . $row['fuel'] . '</td>
				<td class="alignright Allowance">' . $row['advance'] . '</td>
				<td class="alignright ChequeAdvance">' . $row['cheque_advance'] . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="new_amount[]" value="' . $row['balance'] . '" /></td>
				<td class="aligncenter"><button type="button" class="btn btn-danger btn-mini DelButton"><i class="icon-minus"></i></button></td>
			</tr>
			';
		}

		echo $result;
	}

	function ajaxFuelTrips() {
		$from_date = convDate($this->input->post('from_date'));
		$to_date   = convDate($this->input->post('to_date'));
		$transporter_ledger_id  = $this->input->post('transporter_ledger_id');

		$optional = array(
			'T.from_location_id'   => $this->input->post('from_location_id'), 
			'T.to_location_id'     => $this->input->post('to_location_id'), 
			'T.party_reference_no' => $this->input->post('party_reference_no'), 
		);

		$optional_sql = '';
		foreach ($optional as $key => $value) {
			if ($value) {
				$optional_sql .= " AND $key = '$value'";
			}
		}

		$sql = "SELECT T.id, T.party_reference_no AS job_no, T.container_no, DATE_FORMAT(T.date, '%d-%m-%Y') AS trip_date, 
		T.registration_no, T.container_no2, T.container_size, 
		PL.name AS party_name, T.product_name, TL.name AS transporter_name, T.lr_no, T.transporter_rate, 
		FLOC.name AS from_location, TLOC.name AS to_location, PA.id AS pump_advance_id, PA.amount AS fuel, 
		PA.slip_no, T.remarks, IF((LENGTH(T.container_no2) != 0), '2 X 20', IF((T.container_size = '40'), '1 X 40', '1 X 20')) AS qty
		FROM trips T INNER JOIN pump_advances PA ON T.id = PA.trip_id
			INNER JOIN ledgers A ON (PA.agent_id = A.agent_id AND A.company_id = ?)
			LEFT OUTER JOIN ledgers PL ON T.party_ledger_id = PL.id
			LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
			LEFT OUTER JOIN locations FLOC ON T.from_location_id = FLOC.id
			LEFT OUTER JOIN locations TLOC ON T.to_location_id = TLOC.id
			LEFT OUTER JOIN jobs J ON T.job_id = J.id
		WHERE A.id = ? AND T.date >= ? AND T.date <= ? AND T.company_id = ? AND PA.id NOT IN (
				SELECT CID.pump_advance_id
				FROM trip_inward_details CID 
				WHERE CID.pump_advance_id > 0
			) $optional_sql
		GROUP BY PA.id";
		$query = $this->db->query($sql, array(
			$this->accounting->getCompanyID(),
			$transporter_ledger_id, $from_date, $to_date,
			$this->accounting->getCompanyID(),
		));
		$rows  = $query->result_array();
		$result = '';
		foreach ($rows as $row) {
			$result .= '
			<tr>
				<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="" /></td>
				<td><input type="hidden" class="form-control form-control-sm Validate" name="new_trip_id[]" value="' . $row['id'] . '" />
					<input type="text" class="form-control form-control-sm ContainerNo Validate Focus" value="' . $row['container_no'] . '" /></td>
				<td class="Qty">' . $row['qty'] . '</td>
				<td class="JobNo">' . $row['job_no'] . '</td>
				<td class="VehicleNo">' . $row['registration_no'] . '</td>
				<td class="TransporterName">' . $row['transporter_name'] . '</td>
				<td class="TripDate">' . $row['trip_date'] . '</td>
				<td class="PartyName">' . $row['party_name'] . '</td>
				<td class="ProductName">' . $row['product_name'] . '</td>
				<td class="FromLocation">' . $row['from_location'] . '</td>
				<td class="ToLocation">' . $row['to_location'] . '</td>
				<td><input type="hidden" class="form-control form-control-sm PumpAdvanceID" name="new_pump_advance_id[]" value="' . $row['pump_advance_id'] . '" />
					<input type="text" class="form-control form-control-sm FuelSlipNo" value="' . $row['slip_no'] . '" readonly="true" /></td>
				<td class="alignright Fuel">' . $row['fuel'] . '</td>
				<td class="aligncenter"><button type="button" class="btn btn-danger btn-mini DelButton"><i class="icon-minus"></i></button></td>
			</tr>
			';
		}

		echo $result;
	}

	function ajaxContainer($type) {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			if ($type == 'Transporter') {
				$sql = "SELECT T.id, T.party_reference_no AS job_no, T.container_no, DATE_FORMAT(T.date, '%d-%m-%Y') AS trip_date, 
				T.registration_no, T.container_no2, T.container_size, 
				PL.name AS party_name, T.product_name, TL.name AS transporter_name, T.lr_no, T.transporter_rate, 
				FLOC.name AS from_location, TLOC.name AS to_location, 0 AS pump_advance_id, COALESCE(PA.amount, 0) AS fuel,
				COALESCE(TA.amount, 0) AS advance, COALESCE(VPY.cheque_advance, 0) AS cheque_advance,
				(T.transporter_rate - (ROUND(SUM(COALESCE(TA.amount, 0)), 2) + ROUND(SUM(COALESCE(PA.amount, 0)), 2) + COALESCE(VPY.cheque_advance, 0))) AS balance,
				IF((LENGTH(T.container_no2) != 0), '2 X 20', IF((T.container_size = '40'), '1 X 40', '1 X 20')) AS qty,
				T.remarks
				FROM trips T 
					LEFT OUTER JOIN ledgers PL ON T.party_ledger_id = PL.id
					LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
					LEFT OUTER JOIN locations FLOC ON T.from_location_id = FLOC.id
					LEFT OUTER JOIN locations TLOC ON T.to_location_id = TLOC.id
					LEFT OUTER JOIN jobs J ON T.job_id = J.id
					LEFT OUTER JOIN (
						SELECT TA.trip_id, 
							IF(TA.advance_by = 'Self', ROUND(SUM(TA.amount), 2), 0) AS self_adv, 
							IF(TA.advance_by = 'Party', ROUND(SUM(TA.amount), 2), 0) AS party_adv,
							SUM(TA.amount) AS amount
						FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
						WHERE T.company_id = $this->_company_id
						GROUP BY TA.trip_id
					) TA ON T.id = TA.trip_id
					LEFT OUTER JOIN (
						SELECT PA.trip_id, GROUP_CONCAT(DISTINCT A.name) AS pump_name, SUM(PA.amount) AS amount
						FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
							INNER JOIN agents A ON PA.agent_id = A.id
						WHERE T.company_id = $this->_company_id
						GROUP BY PA.trip_id
					) PA ON T.id = PA.trip_id
					LEFT OUTER JOIN (
						SELECT VT.trip_id, SUM(VT.advance) AS cheque_advance
						FROM voucher_trips VT 
							INNER JOIN vouchers VO ON VT.voucher_id = VO.id
							INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id AND VB.voucher_type_id = 7
						GROUP BY VT.trip_id
					) VPY ON T.id = VPY.trip_id
				WHERE T.company_id = $this->_company_id AND T.id NOT IN (
						SELECT CID.trip_id 
						FROM trip_inward_details CID INNER JOIN trip_inwards CI ON CID.trip_inward_id = CI.id
						WHERE CI.type = 'Transporter'
					) AND 
					(T.container_no LIKE '%$search%' OR 
					T.registration_no LIKE '%$search%' OR 
					PL.name LIKE '%$search%' OR 
					T.product_name LIKE '%$search%')
				GROUP BY T.id
				LIMIT 0, 50";
			}
			else {
				$sql = "SELECT T.id, T.party_reference_no AS job_no, T.container_no, DATE_FORMAT(T.date, '%d-%m-%Y') AS trip_date, 
				T.registration_no, T.container_no2, T.container_size, 
				PL.name AS party_name, T.product_name, TL.name AS transporter_name, T.lr_no, T.transporter_rate, 
				FLOC.name AS from_location, TLOC.name AS to_location, PA.id AS pump_advance_id, PA.amount AS fuel, A.name AS pump_name, 
				PA.slip_no, T.remarks, IF((LENGTH(T.container_no2) != 0), '2 X 20', IF((T.container_size = '40'), '1 X 40', '1 X 20')) AS qty
				FROM trips T INNER JOIN pump_advances PA ON T.id = PA.trip_id
					INNER JOIN agents A ON PA.agent_id = A.id
					LEFT OUTER JOIN ledgers PL ON T.party_ledger_id = PL.id
					LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
					LEFT OUTER JOIN locations FLOC ON T.from_location_id = FLOC.id
					LEFT OUTER JOIN locations TLOC ON T.to_location_id = TLOC.id
					LEFT OUTER JOIN jobs J ON T.job_id = J.id
				WHERE T.company_id = $this->_company_id AND PA.id NOT IN (
						SELECT CID.pump_advance_id
						FROM trip_inward_details CID 
						WHERE CID.pump_advance_id > 0
					) AND 
					(T.container_no LIKE '%$search%' OR 
					A.name LIKE '%$search%' OR 
					PA.slip_no LIKE '%$search%' OR 
					T.registration_no LIKE '%$search%' OR 
					PL.name LIKE '%$search%' OR 
					T.product_name LIKE '%$search%')
				GROUP BY PA.id
				LIMIT 0, 50";
			}
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
}
