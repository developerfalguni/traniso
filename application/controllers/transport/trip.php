<?php

class Trip extends MY_Controller {
	var $_company_id;
	var $_table2;
	var $_table3;

	function __construct() {
		parent::__construct();
	
		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
		}
		unset($default_company);

		$this->_table2 = 'trip_advances';
		$this->_table3 = 'pump_advances';
		$this->load->model('transport');
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
		
		// $data['list']['heading'] = array('ID', 'Date', 'LR No.', 'Party Ref.', 'Registration No', 'Party Name', 'Transporter', 'Container No', 'Container Size', 'From','To','Remarks');
		// $data['list']['class'] = array(
		// 	'id'                 => 'Text', 
		// 	'date'               => 'Text',
		// 	'lr_no'              => 'Text',
		// 	'party_reference_no' => 'Text',
		// 	'registration_no'    => 'Text',
		// 	'party_name'         => 'Text',
		// 	'container_no'       => 'Text',
		// 	'container_size'     => 'Text',
		// 	'from_location'      => 'Text',
		// 	'to_location'        => 'Text',
		// 	'remarks'            => 'Text'
		// 	);
		$data['list']['link_col'] = "id";
		// $data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->transport->countTrips($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['rows'] = $this->transport->getTrips($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(
			// anchor($this->_clspath.$this->_class."/edit/Self/Bulk/0", '<i class="icon-white icon-plus"></i> Add Bulk', 'class="btn btn-success"'),
			anchor($this->_clspath.$this->_class."/edit/Container/0", '<i class="icon-white icon-plus"></i> Container', 'class="btn btn-success"'),
		);
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($cargo_type = 'Bulk', $id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('registration_no', 'Vehicle No', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'                    => 0, 
				'company_id'            => $this->transport->getCompanyID(),
				'cargo_type'            => $cargo_type,
				'date'                  => date('d-m-Y'),
				'registration_no'       => '',
				'party_ledger_id'       => 0,
				'party_rate'            => 0,
				'transporter_ledger_id' => 0,
				'transporter_rate'      => 0,
				'party_reference_no'    => '',
				'job_id'                => 0,
				'container_id'          => 0,
				'container_size'        => '',
				'container_no'          => '',
				'job_id2'               => 0,
				'container_id2'         => 0,
				'container_no2'         => '',
				'lr_no'                 => '',
				'weight'                => '',
				'cbm'                   => '',
				'product_name'          => '',
				'from_location_id'      => '',
				'to_location_id'        => '',
				'remarks'               => '',
			);
		}
		$data['id']                 = array('id' => $id);
		$data['row']                = $row;

		$data['from_location']    = $this->kaabar->getField('locations', $row['from_location_id'], 'id', 'name');
		$data['to_location']      = $this->kaabar->getField('locations', $row['to_location_id'], 'id', 'name');
		$data['party_name']       = $this->kaabar->getField('ledgers', $row['party_ledger_id'], 'id', 'name');
		$data['transporter_name'] = $this->kaabar->getField('ledgers', $row['transporter_ledger_id'], 'id', 'name');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			// $data['trip_jobs']     = $this->kaabar->getRows($this->_table2, $id, 'trip_id');
			$data['trip_advances'] = $this->_trip_advances($id);
			$data['pump_advances'] = $this->_pump_advances($id);
			
			// Receipts
			$sql = "SELECT T.id, V.id AS voucher_id, V.id2_format, V.voucher_book_id, V.id2, V.id3, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, VT.advance AS amount
			FROM trips T INNER JOIN voucher_trips VT ON T.id = VT.trip_id
				INNER JOIN vouchers V ON VT.voucher_id = V.id
				INNER JOIN voucher_books VB ON VB.company_id = ? AND V.voucher_book_id = VB.id
				INNER JOIN voucher_types VTS ON VB.voucher_type_id = VTS.id
			WHERE T.id = ? AND VTS.name = 'Receipt'";
			$query = $this->db->query($sql, array($this->_company_id, $row['id']));
			$data['trip_receipt'] = $query->result_array();

			// Payments
			$sql = "SELECT T.id, V.id AS voucher_id, V.id2_format, V.voucher_book_id, V.id2, V.id3, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, VT.advance AS amount
			FROM trips T INNER JOIN voucher_trips VT ON T.id = VT.trip_id
				INNER JOIN vouchers V ON VT.voucher_id = V.id
				INNER JOIN voucher_books VB ON VB.company_id = ? AND V.voucher_book_id = VB.id
				INNER JOIN voucher_types VTS ON VB.voucher_type_id = VTS.id
			WHERE T.id = ? AND VTS.name = 'Payment'";
			$query = $this->db->query($sql, array($this->_company_id, $row['id']));
			$data['trip_payment'] = $query->result_array();

			$data['voucher_details'] = $this->accounting->getVoucherJobDetails($row['id']);
			$data['cargo_type'] = $cargo_type;

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_'.strtolower($cargo_type).'_edit';
			$data['docs_url']   = $this->_docs.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$cargo_type/$id");

			$data = array(
				'company_id'            => $this->transport->getCompanyID(),
				'cargo_type'            => $cargo_type,
				'date'                  => $this->input->post('date'),
				'lr_no'                 => $this->input->post('lr_no'),
				'party_reference_no'    => $this->input->post('party_reference_no'),
				'registration_no'       => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('registration_no'))),
				'transporter_ledger_id' => $this->input->post('transporter_ledger_id'),
				'transporter_rate'      => $this->input->post('transporter_rate'),
				'job_id'                => $this->input->post('job_id'),
				'container_id'          => $this->input->post('container_id'),
				'container_size'        => $this->input->post('container_size'),
				'weight'                => $this->input->post('weight'),
				'cbm'                   => $this->input->post('cbm'),
				'container_no'          => $this->input->post('container_no'),
				'job_id2'               => $this->input->post('job_id2'),
				'container_id2'         => $this->input->post('container_id2'),
				'container_no2'         => $this->input->post('container_no2'),
				'party_ledger_id'       => $this->input->post('party_ledger_id'),
				'party_rate'            => $this->input->post('party_rate'),
				'product_name'          => $this->input->post('product_name'),
				'from_location_id'      => $this->input->post('from_location_id'),
				'to_location_id'        => $this->input->post('to_location_id'),
				'remarks'               => $this->input->post('remarks')
			);
			
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_update_trip_advances($id);
			$this->_update_pump_advances($id);
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class."/edit/$cargo_type/$id");
		}
	}

	function _update_trip_advances($id) {
		$delete_ids = $this->input->post('ta_delete_id') == false ? ['0' => 0] : $this->input->post('ta_delete_id');
		$dates     = $this->input->post('ta_date');
		if ($dates != null) {
			$advance_bys  = $this->input->post('ta_advance_by');
			$rto_challans = $this->input->post('ta_rto_challan');
			$amounts      = $this->input->post('ta_amount');

			foreach ($dates as $index => $date) {
				if (strlen(trim($date)) == 10) {
					$row = array(
						'advance_by'  => $advance_bys[$index],
						'date'        => $date,
						'rto_challan' => $rto_challans[$index],
						'amount'      => round($amounts[$index], 2),
					);

					$voucher_id = $this->kaabar->getField('trip_advances', $index, 'id', 'voucher_id');
					if ($voucher_id > 0) {
						$row['voucher_id'] = $this->kaabar->save('vouchers', array('amount' => round($amounts[$index], 2)), array('id' => $voucher_id));
					}
					else if ($voucher_id == 0 && $row['advance_by'] == 'Self' && $this->input->post('transporter_ledger_id') == 0) {
						$data = array(
							'voucher_book_id' => 6,  // Payments
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => 3, // Cash in hand
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(6, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($voucher_id == 0 && $row['advance_by'] == 'Self' && $this->input->post('transporter_ledger_id') > 0) {
						$data = array(
							'voucher_book_id' => 6, // Payments
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => $this->input->post('transporter_ledger_id'),
							'cr_ledger_id'    => 3, // Cash in hand
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(6, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($voucher_id == 0 && $row['advance_by'] == 'Party' && $this->input->post('transporter_ledger_id') == 0) {
						$data = array(
							'voucher_book_id' => 10,  // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $this->input->post('party_ledger_id'),
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$eid = $this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($voucher_id == 0 && $row['advance_by'] == 'Party' && $this->input->post('transporter_ledger_id') > 0) {
						// Voucher Entry 1
						$data = array(
							'voucher_book_id' => 10,  // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $this->input->post('party_ledger_id'),
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
						// voucher entry 2
						$data['id3']          = 2;
						$data['dr_ledger_id'] = $this->input->post('transporter_ledger_id');
						$data['cr_ledger_id'] = 60; // Trip Advance
						$this->kaabar->save('vouchers', $data);
					}

					$this->kaabar->save('trip_advances', $row, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0) {
					$row = $this->kaabar->getRow('trip_advances', $index);
					$this->kaabar->delete('voucher_trips', array('trip_id' => $row['trip_id'], 'voucher_id' => $row['voucher_id']));
					$this->kaabar->delete('vouchers', array('id' => $row['voucher_id']));
					$this->kaabar->delete('voucher_trips', array('trip_id' => $row['trip_id'], 'voucher_id' => $row['receipt_voucher_id']));
					$this->kaabar->delete('vouchers', array('id' => $row['receipt_voucher_id']));
					$this->kaabar->delete('trip_advances', ['id' => $index]);
				}
			}
		}

		$dates = $this->input->post('ta_new_date');
		if ($dates != null) {
			$advance_bys  = $this->input->post('ta_new_advance_by');
			$rto_challans = $this->input->post('ta_new_rto_challan');
			$amounts      = $this->input->post('ta_new_amount');

			foreach ($dates as $index => $date) {
				if (strlen(trim($date)) == 10) {
					$row = array(
						'trip_id'     => $id,
						'advance_by'  => $advance_bys[$index],
						'date'        => $date,
						'rto_challan' => $rto_challans[$index],
						'amount'      => round($amounts[$index], 2),
						'voucher_id'  => 0,
					);

					if ($row['advance_by'] == 'Self' && $this->input->post('transporter_ledger_id') == 0) {
						$data = array(
							'voucher_book_id' => 6,  // Payment
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => 3, // Cash in hand
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(6, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($row['advance_by'] == 'Self' && $this->input->post('transporter_ledger_id') > 0) {
						$data = array(
							'voucher_book_id' => 6,  // Payment
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => $this->input->post('transporter_ledger_id'),
							'cr_ledger_id'    => 3, // Cash in hand
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(6, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($row['advance_by'] == 'Party' && $this->input->post('transporter_ledger_id') == 0) {
						$data = array(
							'voucher_book_id' => 10,  // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $this->input->post('party_ledger_id'),
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($row['advance_by'] == 'Party' && $this->input->post('transporter_ledger_id') > 0) {
						// Voucher Entry 1
						$data = array(
							'voucher_book_id' => 10,  // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $this->input->post('party_ledger_id'),
							'amount'          => round($amounts[$index], 2),
							'narration'       => '',
							'category'        => 'Trips',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
						// voucher entry 2
						$data['id3']          = 2;
						$data['dr_ledger_id'] = $this->input->post('transporter_ledger_id');
						$data['cr_ledger_id'] = 60; // Trip Advance
						$this->kaabar->save('vouchers', $data);
					}

					$this->kaabar->save('trip_advances', $row);
				}
			}
		}
	}

	function _update_pump_advances($id) {
		$delete_ids = $this->input->post('pa_delete_id') == false ? ['0' => 0] : $this->input->post('pa_delete_id');
		$dates      = $this->input->post('pa_date');
		if ($dates != null) {
			$slip_nos  = $this->input->post('pa_slip_no');
			$agent_ids = $this->input->post('pa_agent_id');
			$amounts   = $this->input->post('pa_amount');

			foreach ($dates as $index => $date) {
				if (strlen(trim($date)) == 10) {
					$row = array(
						'date'     => $date,
						'slip_no'  => $slip_nos[$index],
						'agent_id' => $agent_ids[$index],
						'amount'   => round($amounts[$index], 2),
					);

					$pa_row = $this->kaabar->getRow('pump_advances', $index);
					$agent_ledger_id = $this->kaabar->getField('ledgers', array(
							'company_id' => $this->_company_id,
							'agent_id'   => $agent_ids[$index]
						), 'id', 'id');
					if ($pa_row['voucher_id'] > 0) {
						$row['voucher_id']  = $this->kaabar->save('vouchers', array('amount' => round($amounts[$index], 2)), array('id' => $pa_row['voucher_id']));

						if ($this->input->post('transporter_ledger_id') > 0) {
							$row['voucher_id2'] = $this->kaabar->save('vouchers', array(
								'dr_ledger_id' => $this->input->post('transporter_ledger_id'), 
								'amount'       => round($amounts[$index], 2)
							), array('id' => $pa_row['voucher_id2']));
						}
					}
					else if ($pa_row['voucher_id'] == 0 && $this->input->post('transporter_ledger_id') == 0 && $agent_ledger_id > 0) {
						$data = array(
							'voucher_book_id' => 10,   // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $agent_ledger_id,
							'amount'          => round($amounts[$index], 2),
							'narration'       => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('registration_no'))) . ' - ' . $slip_nos[$index],
							'category'        => 'Pumps',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($pa_row['voucher_id'] == 0 && $this->input->post('transporter_ledger_id') > 0 && $agent_ledger_id > 0) {
						// Voucher Entry 1
						$data = array(
							'voucher_book_id' => 10,   // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 1,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $agent_ledger_id,
							'amount'          => round($amounts[$index], 2),
							'narration'       => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('registration_no'))) . ' - ' . $slip_nos[$index],
							'category'        => 'Pumps',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));

						// Voucher Entry 2
						$data['id3']          = 2;
						$data['dr_ledger_id'] = $this->input->post('transporter_ledger_id');
						$data['cr_ledger_id'] = 60; // Trip Advance
						$row['voucher_id2']   = $this->kaabar->save('vouchers', $data);
					}

					$this->kaabar->save('pump_advances', $row, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0) {
					$row = $this->kaabar->getRow('pump_advances', $index);
					$this->kaabar->delete('voucher_trips', array('trip_id' => $row['trip_id'], 'voucher_id' => $row['voucher_id']));
					$this->kaabar->delete('vouchers', array('id' => $row['voucher_id']));
					$this->kaabar->delete('pump_advances', ['id' => $index]);
				}
			}
		}

		$dates = $this->input->post('pa_new_date');
		if ($dates != null) {
			$slip_nos  = $this->input->post('pa_new_slip_no');
			$agent_ids = $this->input->post('pa_new_agent_id');
			$amounts   = $this->input->post('pa_new_amount');

			foreach ($dates as $index => $date) {
				if (strlen(trim($date)) == 10) {
					$row = array(
						'trip_id'  => $id,
						'date'     => $date,
						'slip_no'  => $slip_nos[$index],
						'agent_id' => $agent_ids[$index],
						'amount'   => round($amounts[$index], 2),
					);

					$agent_ledger_id = $this->kaabar->getField('ledgers', array(
							'company_id' => $this->_company_id,
							'agent_id'   => $agent_ids[$index]
						), 'id', 'id');
					if ($this->input->post('transporter_ledger_id') == 0 && $agent_ledger_id > 0) {
						$data = array(
							'voucher_book_id' => 10,   // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 0,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $agent_ledger_id,
							'amount'          => round($amounts[$index], 2),
							'remarks'         => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('registration_no'))) . ' - ' . $slip_nos[$index],
							'category'        => 'Pumps',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));
					}
					else if ($this->input->post('transporter_ledger_id') > 0 && $agent_ledger_id > 0) {
						// Voucher Entry 1
						$data = array(
							'voucher_book_id' => 10,   // Journal
							'id2_format'      => '',
							'id2'             => 0,
							'id3'             => 1,
							'date'            => $date,
							'dr_ledger_id'    => 60, // Trip Advance
							'cr_ledger_id'    => $agent_ledger_id,
							'amount'          => round($amounts[$index], 2),
							'remarks'         => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('registration_no'))) . ' - ' . $slip_nos[$index],
							'category'        => 'Pumps',
						);
						$d = array_merge($data, $this->accounting->getNextVoucherNo(10, $date, 0, 1));
						$data = $d;
						$row['voucher_id'] = $this->kaabar->save('vouchers', $data);
						$this->kaabar->save('voucher_trips', array('voucher_id' => $row['voucher_id'], 'trip_id' => $id));

						// Voucher Entry 2
						$data['id3']          = 2;
						$data['dr_ledger_id'] = $this->input->post('transporter_ledger_id');
						$data['cr_ledger_id'] = 60; // Trip Advance
						$row['voucher_id2']   = $this->kaabar->save('vouchers', $data);
					}

					$this->kaabar->save('pump_advances', $row);
				}
			}
		}
	}

	function _trip_advances($id) {
		$sql = "SELECT TA.*, DATE_FORMAT(TA.date, '%d-%m-%Y') AS date, 
			CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS voucher_url, 
			CONCAT(RVT.name,'/edit/',RVB.id,'/',RV.id) AS receipt_voucher_url
		FROM trip_advances TA 
			LEFT OUTER JOIN vouchers V ON TA.voucher_id = V.id
			LEFT OUTER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			LEFT OUTER JOIN vouchers RV ON TA.receipt_voucher_id = RV.id
			LEFT OUTER JOIN voucher_books RVB ON RV.voucher_book_id = RVB.id
			LEFT OUTER JOIN voucher_types RVT ON RVB.voucher_type_id = RVT.id
		WHERE TA.trip_id > 0 AND TA.trip_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function _pump_advances($id) {
		$sql = "SELECT PA.*, DATE_FORMAT(PA.date, '%d-%m-%Y') AS date, A.name AS agent_name,
			CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS voucher_url
		FROM pump_advances PA LEFT OUTER JOIN agents A ON PA.agent_id = A.id
			LEFT OUTER JOIN vouchers V ON PA.voucher_id = V.id
			LEFT OUTER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
		WHERE PA.trip_id > 0 AND PA.trip_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function delete($id = 0, $field = 'id') {
		$this->db->query("DELETE FROM $this->_table3 WHERE trip_id = ?", array($id));
		$this->db->query("DELETE FROM $this->_table2 WHERE trip_id = ?", array($id));
		$this->db->query("DELETE FROM $this->_table WHERE id = ?", array($id));
		setSessionAlert('Trip Deleted Successfully', 'success');
		redirect($this->_clspath.$this->_class);
	}

	function getLRNo($lr_no) {
		$company = $this->session->userdata('default_company');
		$sql = "SELECT T.id, T.type, T.cargo_type, DATE_FORMAT(T.date, '%d-%m-%Y') AS date,
			T.registration_no, T.gatepass_no, DATE_FORMAT(T.gatepass_date, '%d-%m-%Y') AS gatepass_date,
			T.lr_no
		FROM trips T 
		WHERE T.company_id = ? AND T.lr_no = ? 
		ORDER BY T.id";
		$query = $this->db->query($sql, array($company['id'], $lr_no));
		$rows = $query->result_array();
		if (count($rows) == 0) {
			echo 'No';
			return;
		}

		header('Content-type: text/xml');
		echo '<taconite>
	<replaceContent select="tbody#Entries">
	';
	foreach($rows as $r) {
		echo '<tr>
		<td><a href="/transport/trip/edit/'.strtolower($r['type']).'/'.strtolower($r['cargo_type']).'/'.$r['id'].'">' . $r['id'] . '</a></td>
		<td>' . $r['date'] . '</td>
		<td>' . $r['registration_no'] . '</td>
		<td>' . $r['gatepass_no'] . '</td>
		<td>' . $r['gatepass_date'] . '</td>
		<td>' . $r['lr_no'] . '</td>
	</tr>';
	}
	echo '
	</replaceContent>
</taconite>';
	}

	function ajaxBL($type, $vessel_id) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$vessel_id = ($vessel_id > 0 ? 'J.vessel_id = ' . $vessel_id . ' AND': '');
			$sql = "SELECT J.bl_no, P.name AS party_name, GROUP_CONCAT(HSP.name SEPARATOR ', ') AS hss_party, 
				CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, 
				PRD.name AS product_name, J.packages AS bl_pieces, 
				IF(J.cbm > 0, J.cbm, IF(J.net_weight_unit = 'MTS', ROUND(J.net_weight*1.42, 4), J.net_weight)) AS bl_cbm
			FROM jobs J INNER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
				LEFT OUTER JOIN high_seas HSS ON J.id = HSS.job_id
				LEFT OUTER JOIN parties HSP ON HSS.party_id = HSP.id
				LEFT OUTER JOIN products PRD ON J.product_id = PRD.id
			WHERE J.cargo_type = '$type' AND J.bl_no LIKE '%$search%'
			GROUP BY J.id
			ORDER BY J.id DESC
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxContainer($type, $id = 0) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT DS.id, J.id2_format AS job_no, DS.job_id, DS.container_no, CT.size AS container_size, J.bl_no, 
				P.name AS party_name, PL.id AS party_ledger_id, PL.name AS party_ledger_name, PRD.name AS product_name
			FROM deliveries_stuffings DS
				INNER JOIN container_types CT ON DS.container_type_id = CT.id
				INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN ledgers PL ON J.party_id = PL.party_id
				LEFT OUTER JOIN products PRD ON J.product_id = PRD.id
			WHERE J.cargo_type = ? AND 
				DS.container_no LIKE '%$search%' AND
				DS.container_id NOT IN (
					SELECT container_id FROM trips WHERE id != ? AND container_id > 0
					UNION
					SELECT container_id2 FROM trips WHERE id != ? AND container_id2 > 0)
			GROUP BY DS.id
			ORDER BY DS.id DESC
			LIMIT 0, 50";
			$rows = $this->db->query($sql, [$type, $id, $id])->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxParty() {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			unset($default_company);
			
			$search           = strtolower($this->input->post_get('term'));
			$from_location_id = $this->input->post('from_location_id');
			$to_location_id   = $this->input->post('to_location_id');
			$container_size   = $this->input->post('container_size');

			if (Auth::isAdmin() OR Auth::get('username') == 'deepa' OR Auth::get('username') == 'sunny') {
				$sql = "SELECT L.id, L.code, L.name, COALESCE(TR.price_$container_size, 0) AS rate
				FROM ledgers L LEFT OUTER JOIN transport_rates TR ON (L.id = TR.ledger_id AND TR.from_location_id = $from_location_id AND
					TR.to_location_id = $to_location_id)
				WHERE L.company_id = $company_id AND 
					L.category = 'Party' AND
					L.name LIKE '%$search%'
				ORDER BY name LIMIT 0, 50";
			}
			else {
				$sql = "SELECT L.id, L.code, L.name, COALESCE(TR.price_$container_size, 0) AS rate
				FROM ledgers L 
					LEFT OUTER JOIN (
						SELECT TR1.*, COALESCE(TR2.wef_date, CURDATE()) AS to_wef_date
						FROM transport_rates TR1 LEFT OUTER JOIN transport_rates TR2 ON 
							TR1.company_id = TR2.company_id AND
							TR1.ledger_id = TR2.ledger_id AND
							TR1.from_location_id = TR2.from_location_id AND 
							TR1.to_location_id = TR2.to_location_id AND 
							TR1.product_id = TR2.product_id AND
							TR1.wef_date < TR2.wef_date
						GROUP BY id
					) TR ON (L.id = TR.ledger_id AND 
						TR.from_location_id = $from_location_id AND
						TR.to_location_id = $to_location_id AND
						CURDATE() >= TR.wef_date AND 
						CURDATE() <= TR.to_wef_date)
				WHERE L.company_id = $company_id AND 
					L.category = 'Party' AND
					L.name LIKE '%$search%'
				ORDER BY name LIMIT 0, 50";
			}
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo 
			"Access Denied";
		}
	}

	function ajaxTransporter() {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			unset($default_company);
			
			$search           = strtolower($this->input->post_get('term'));
			$from_location_id = $this->input->post('from_location_id');
			$to_location_id   = $this->input->post('to_location_id');
			$container_size   = $this->input->post('container_size');

			if (Auth::isAdmin() OR Auth::get('username') == 'deepa' OR Auth::get('username') == 'sunny') {
				$sql = "SELECT L.id, L.code, L.name, COALESCE(TR.price_$container_size, 0) AS rate
				FROM ledgers L
					INNER JOIN agents A ON L.agent_id = A.id
					LEFT OUTER JOIN transport_rates TR ON (L.id = TR.ledger_id AND TR.from_location_id = $from_location_id AND
					TR.to_location_id = $to_location_id)
				WHERE L.company_id = $company_id AND 
					L.category = 'Agent' AND
					FIND_IN_SET('Transporter', A.type) AND
					L.name LIKE '%$search%'
				ORDER BY name LIMIT 0, 50";
			}
			else {
				$sql = "SELECT L.id, L.code, L.name, COALESCE(TR.price_$container_size, 0) AS rate
				FROM ledgers L
					INNER JOIN agents A ON L.agent_id = A.id
					LEFT OUTER JOIN (
						SELECT TR1.*, COALESCE(TR2.wef_date, CURDATE()) AS to_wef_date
						FROM transport_rates TR1 LEFT OUTER JOIN transport_rates TR2 ON 
							TR1.company_id = TR2.company_id AND
							TR1.ledger_id = TR2.ledger_id AND
							TR1.from_location_id = TR2.from_location_id AND 
							TR1.to_location_id = TR2.to_location_id AND 
							TR1.product_id = TR2.product_id AND
							TR1.wef_date < TR2.wef_date
						GROUP BY id
					) TR ON (L.id = TR.ledger_id AND 
						TR.from_location_id = $from_location_id AND
						TR.to_location_id = $to_location_id AND
						CURDATE() >= TR.wef_date AND 
						CURDATE() <= TR.to_wef_date)
				WHERE L.company_id = $company_id AND 
					L.category = 'Agent' AND
					FIND_IN_SET('Transporter', A.type) AND
					L.name LIKE '%$search%'
				ORDER BY name LIMIT 0, 50";
			}
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo 
			"Access Denied";
		}
	}

	function ajaxVehicle($category = NULL) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$category = (is_null($category) ? ''  : "type = '$category' AND");
			$sql = "SELECT id, registration_no, type
			FROM vehicles
			WHERE $category (registration_no LIKE '%$search%')
			ORDER BY registration_no
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxAgent() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, name
			FROM agents
			WHERE type = 'Pump' AND (name LIKE '%$search%')
			ORDER BY name
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}
}
