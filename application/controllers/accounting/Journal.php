<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

class Journal extends MY_Controller {
	var $_company;
	var $_table2;
	var $_table3;
	var $_folder;
	var $_share;
	var $_path;
	var $_path_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_table   = 'vouchers';
		$this->_table2  = 'voucher_details';
		$this->_table3  = 'voucher_documents';
		$this->_company = $this->session->userdata('default_company');

		$this->_folder    = 'documents/vouchers/';
		$this->_share     = FCPATH . 'share/';
		$this->_path      = FCPATH . $this->_folder;
		$this->_path_url  = base_url($this->_folder);
	}
		
	function index($voucher_book_id = 0, $starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$voucher_book_id = intval($voucher_book_id);
		$starting_row = intval($starting_row);
		if(! $this->accounting->isVoucherBook($voucher_book_id)) {
			setSessionError('Invalid Voucher Book or Voucher Book does not exists');
			redirect();
		}

		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search');
			$search = false;
			redirect($this->_clspath.$this->_class."/index/$voucher_book_id");
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class."/index/$voucher_book_id");
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('No', 'Date', 'Cheque No', 'Bill No', 'Debit', 'Name', 'Credit', 'Name', 'Amount', 'Docs'),
			'class' => array(
				'voucher_no' => array('class' => 'Text', 'link' => 'id'),
				'date'       => 'Date',
				'cheque_no'  => 'Text',
				'invoice_no' => 'Text',
				'dr_code'    => '',
				'dr_name'    => 'tiny orange',
				'cr_code'    => '',
				'cr_name'    => 'tiny orange',
				'amount'     => 'count',
				'document'   => 'Label'
				),
			'link_col' => "voucher_no",
			'link_url' => $this->_clspath.$this->_class."/edit/$voucher_book_id/");
		$data['label_class'] = $this->accounting->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class."/index/$voucher_book_id");
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		$config['total_rows']  = $this->accounting->countVouchers($voucher_book_id, $search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->accounting->getVouchers($voucher_book_id, $search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$voucher_book_id", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = $this->accounting->getBookName($voucher_book_id);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function show($voucher_book_id, $id, $id3, $company_id) {
		if ($this->_company['id'] == $company_id)
			redirect($this->_clspath.$this->_class."/edit/$voucher_book_id/$id");

		$voucher_book_id = intval($voucher_book_id);
		$id              = intval($id);

		$voucher_book = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		if ($id == 0 && $voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Dr')
			$dr_ledger = $this->kaabar->getRow('ledgers', $voucher_book['default_ledger_id']);
		else if ($id == 0 && $voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Cr')
			$cr_ledger = $this->kaabar->getRow('ledgers', $voucher_book['default_ledger_id']);
		
		$row = $this->accounting->getVoucher($voucher_book_id, $id, 0, $company_id);
		if (isset($dr_ledger)) {
			$row['dr_ledger_id']        = $dr_ledger['id'];
			$row['debit_account']       = $dr_ledger['code'] . ' - '. $dr_ledger['name'];
			$tds_row                    = $this->kaabar->getField('tds_classes', $dr_ledger['tds_class_id'], 'id', 'type');
			$row['dr_tds_class_id']     = ($tds_row == 'Payment' ? $dr_ledger['tds_class_id'] : 0);
			$row['dr_stax_category_id'] = $dr_ledger['stax_category_id'];
			$row['dr_closing']          = 0;
		}
		if (isset($cr_ledger)) {
			$row['cr_ledger_id']        = $cr_ledger['id'];
			$row['credit_account']      = $cr_ledger['code'] . ' - ' . $cr_ledger['name'];
			$tds_row                    = $this->kaabar->getField('tds_classes', $cr_ledger['tds_class_id'], 'id', 'type');
			$row['cr_tds_class_id']  	= ($tds_row == 'Payment' ? $cr_ledger['tds_class_id'] : 0);
			$row['cr_stax_category_id'] = $cr_ledger['stax_category_id'];
			$row['cr_closing']          = 0;
		}

		$data['id']  = array('id' => $id);
		$data['row'] = $row;
		
		$data['reference_name']  = $this->kaabar->getField('ledgers', $row['reference_ledger_id'], 'id', 'CONCAT(code, " ", name)');

		$data['row']['documents'] = $this->kaabar->getRows('voucher_documents', $row['id'], 'voucher_id', 'id, voucher_id');
		$data['voucher_book'] 	  = $voucher_book;
		$data['default_company']  = $this->kaabar->getRow('companies', $company_id);
		$data['default_company']['financial_year'] = $this->_company['financial_year'];

		$this->load->helper("datefn");
		$data['sub_vouchers'] = $this->accounting->getSubVouchers($voucher_book_id, $row['id2']);
		$data['months'] = getMonthsInBetween($row['date'], date('01-m-Y', strtotime('-12 month', strtotime($row['date']))));

		$data['voucher_details'] = $this->accounting->getVoucherJobDetails($row['id']);

		$data['page_title']    = $this->accounting->getBookName($voucher_book_id) . ' ' . humanize($this->_class);
		$data['hide_title']    = true;
		$data['page']          = $this->_clspath.'voucher_show';
		$data['hide_menu']     = true;
		$data['hide_footer']   = true;
		$data['docs_url']      = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($voucher_book_id, $id = 0, $prev_id = 0) {
		$voucher_book_id = intval($voucher_book_id);
		$id = intval($id);

		if(! $this->accounting->isVoucherBook($voucher_book_id)) {
			setSessionError('Invalid Voucher Book or Voucher Book does not exists');
			redirect();
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[10]|callback__checkDate['.$voucher_book_id.','.$id.']');
		$this->form_validation->set_rules('dr_ledger_id', 'Debit Account', 'trim|required|callback__is_ledger');
		$this->form_validation->set_rules('debit_account', 'Debit Account', 'trim');
		$this->form_validation->set_rules('cr_ledger_id', 'Credit Account', 'trim|required|callback__is_ledger');
		$this->form_validation->set_rules('credit_account', 'Credit Account', 'trim');
		$this->form_validation->set_rules('amount', 'Amount', 'trim');
		$this->form_validation->set_rules('narration', 'Narration', 'trim');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
		
		$voucher_book = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		if ($id == 0 && $voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Dr')
			$dr_ledger = $this->kaabar->getRow('ledgers', $voucher_book['default_ledger_id']);
		else if ($id == 0 && $voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Cr')
			$cr_ledger = $this->kaabar->getRow('ledgers', $voucher_book['default_ledger_id']);

		if ($id == 0 && $prev_id > 0) {
			$row = $this->accounting->getVoucher($voucher_book_id, $id, $prev_id);
			$row['id']                  = 0;
			$row['dr_ledger_id']        = 0;
			$row['debit_account']       = '';
			$row['dr_tds_class_id']  	= 0;
			$row['dr_tds_type']         = '';
			$row['dr_stax_category_id'] = 0;
			$row['dr_closing']          = 0;
			$row['ref_dr_ledger_id']    = 0;
			$row['ref_debit_account']   = '';

			$row['cr_ledger_id']        = 0;
			$row['credit_account']      = '';
			$row['cr_tds_class_id']     = 0;
			$row['cr_tds_type']         = '';
			$row['cr_stax_category_id'] = 0;
			$row['cr_closing']          = 0;
			$row['ref_cr_ledger_id']    = 0;
			$row['ref_credit_account']  = '';

			$row['amount']              = 0;

			$row['tds']                  = 0;
			$row['tds_amount']           = 0;
			$row['tds_surcharge']        = 0;
			$row['tds_surcharge_amount'] = 0;
			$row['tds_edu_cess']         = 0;
			$row['tds_edu_cess_amount']  = 0;
			$row['tds_hedu_cess']        = 0;
			$row['tds_hedu_cess_amount'] = 0;
			$row['stax_on_amount']       = 0;
		}
		else
			$row = $this->accounting->getVoucher($voucher_book_id, $id);

		if($row == false) {
			$row = array(
				'id'                   => 0,
				'voucher_book_id'      => $voucher_book_id,
				'job_id'               => 0,
				'date'                 => date('d-m-Y'),
				'id2_format'           => '',
				'id2'                  => 0,
				'id3'                  => 1,
				'invoice_no'           => '',
				'invoice_date'         => '00-00-0000',
				'cheque_no'            => '',
				'cheque_date'          => date('d-m-Y'),
				'reconciliation_date'  => '00-00-0000',
				'dr_ledger_id'         => 0,
				'debit_account'        => '',
				'dr_tds_class_id'      => 0,
				'dr_tds_type'          => '',
				'dr_stax_category_id'  => 0,
				'dr_closing'           => 0,
				'ref_dr_ledger_id'     => 0,
				'ref_debit_account'    => '',
				'cr_ledger_id'         => 0,
				'credit_account'       => '',
				'cr_tds_class_id'      => 0,
				'cr_tds_type'          => '',
				'cr_stax_category_id'  => 0,
				'cr_closing'           => 0,
				'ref_cr_ledger_id'     => 0,
				'ref_credit_account'   => '',
				'amount'               => 0,
				'invoice_amount'       => 0,
				'tds'                  => Settings::get('tds'),
				'tds_amount'           => 0,
				'tds_surcharge'        => Settings::get('tds_surcharge'),
				'tds_surcharge_amount' => 0,
				'tds_edu_cess'         => Settings::get('tds_edu_cess'),
				'tds_edu_cess_amount'  => 0,
				'tds_hedu_cess'        => Settings::get('tds_hedu_cess'),
				'tds_hedu_cess_amount' => 0,
				'stax_on_amount'       => 0,
				'tds_stax_bsr_code'    => '',
				'tds_stax_challan_no'  => '',
				'pieces'               => 0,
				'cbm'                  => 0,
				'product_details'      => '',
				'narration'            => '',
				'reference_ledger_id'  => 0,
				'category' 			   => 'N/A',
				'remarks'              => '',
				'action'               => '',
				'audited'              => 'No',

				'type'         => 'Import',
				'cargo_type'   => 'Bulk',
				'product_id'   => 0,
				'party_id'     => 0,
				'vessel_id'    => 0,
				'bl_no'        => '',
				'packages'     => 0,
				'net_weight'   => 0,
				'container_20' => 0,
				'container_40' => 0,
			);
		}

		if (isset($dr_ledger)) {
			$row['dr_ledger_id']        = $dr_ledger['id'];
			$row['debit_account']       = $dr_ledger['code'] . ' - '. $dr_ledger['name'];
			$row['dr_tds_type']         = $this->kaabar->getField('tds_classes', $dr_ledger['tds_class_id'], 'id', 'type');
			$row['dr_tds_class_id']     = ($row['dr_tds_type'] == 'Payment' ? $dr_ledger['tds_class_id'] : 0);
			$row['dr_stax_category_id'] = $dr_ledger['stax_category_id'];
			$row['dr_closing']          = 0;
		}
		if (isset($cr_ledger)) {
			$row['cr_ledger_id']        = $cr_ledger['id'];
			$row['credit_account']      = $cr_ledger['code'] . ' - ' . $cr_ledger['name'];
			$row['cr_tds_type']         = $this->kaabar->getField('tds_classes', $cr_ledger['tds_class_id'], 'id', 'type');
			$row['cr_tds_class_id']     = ($row['cr_tds_type'] == 'Payment' ? $cr_ledger['tds_class_id'] : 0);
			$row['cr_stax_category_id'] = $cr_ledger['stax_category_id'];
			$row['cr_closing']          = 0;
		}

		// Finding Duplicate invoice_no in Journal Voucher Only
		$this->form_validation->set_rules('invoice_no', 'Bill No', 'trim|callback__is_duplicatebill['.$voucher_book_id.','.$row['id2'].']');
		
		$data['id']  = array('id' => $id);
		$data['row'] = $row;
		$data['row']['dr_closing'] = $this->accounting->getClosing($data['row']['dr_ledger_id']);
		$data['row']['cr_closing'] = $this->accounting->getClosing($data['row']['cr_ledger_id']);
		$job_id = $row['job_id'];

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['documents']       = $this->_documents($row['id']);
			$data['voucher_book']    = $voucher_book;
			$data['default_company'] = $this->_company;
			$data['sub_vouchers'] = $this->accounting->getSubVouchers($voucher_book_id, $row['id2']);
			if ($id == 0 && $prev_id > 0 && isset($data['sub_vouchers']) && count($data['sub_vouchers']) > 0)
				$data['row']['id3'] = count($data['sub_vouchers']) + 1;
			
			$data['voucher_details'] = $this->accounting->getVoucherJobDetails($row['id']);

			$query   = $this->db->query("SELECT COUNT(id) AS numrows FROM voucher_vouchers WHERE voucher_id = ?", array($id));
			$tmp_row = $query->row_array();
			$data['voucher_vouchers'] = $tmp_row['numrows'];
			switch ($row['category']) {
				case 'Invoices':
					$data['invoices'] = $this->_getInvoices($row['dr_ledger_id'], $row['id'], TRUE);
					break;
				case 'Pumps':
					$data['pumps'] = $this->_getPumps($row['cr_ledger_id'], $row['id'], TRUE);
					break;
				case 'Trips':
					$data['trips'] = $this->_getTrips($row['dr_ledger_id'], $row['id'], TRUE);
					break;
			}
			$data['javascript'] = array('pdfjs/web/compatibility.js', 'pdfjs/web/l10n.js', 'pdfjs/build/pdf.js');

			$data['hide_title'] = true;
			$data['page_title'] = $this->accounting->getBookName($voucher_book_id);
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			// checkDuplicateFormSubmit($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$amount = 0;
				// if ($this->input->post('dr_stax_category_id') > 0 OR $this->input->post('cr_stax_category_id') > 0) {
				// 	$amount = round($this->input->post('stax_on_amount') * Settings::get('service_tax') / 100, 0);
				// 	$amount += (round($amount * Settings::get('edu_cess') / 100, 0) + 
				// 				round($amount * Settings::get('hedu_cess') / 100, 0));
				// }
				// else 
				if ($this->input->post('dr_tds_class_id') > 0 OR $this->input->post('cr_tds_class_id') > 0) {
					$amount = (
						$this->input->post('tds_amount') + 
						$this->input->post('tds_surcharge_amount') + 
						$this->input->post('tds_edu_cess_amount') + 
						$this->input->post('tds_hedu_cess_amount')
					);
				}
				$data = array(
					'voucher_book_id'      => $voucher_book_id,
					'job_id'               => $this->input->post('job_id'),
					'id2_format'           => $row['id2_format'],
					'id2'                  => $row['id2'],
					'id3'                  => $this->input->post('id3'),
					'date'                 => $this->input->post('date'),
					'invoice_no'           => $this->input->post('invoice_no'),
					'invoice_date'         => $this->input->post('invoice_date'),
					'cheque_no'            => $this->input->post('cheque_no'),
					'cheque_date'          => $this->input->post('cheque_date'),
					'reconciliation_date'  => $this->input->post('reconciliation_date'),
					'dr_ledger_id'         => $this->input->post('dr_ledger_id'),
					'cr_ledger_id'         => $this->input->post('cr_ledger_id'),
					'amount'               => ($amount > 0 ? $amount : $this->input->post('amount')),
					//'amount'             => $this->input->post('amount'),
					'invoice_amount'       => $this->input->post('invoice_amount'),
					'tds'                  => $this->input->post('tds'),
					'tds_amount'           => $this->input->post('tds_amount'),
					'tds_surcharge'        => $this->input->post('tds_surcharge'),
					'tds_surcharge_amount' => $this->input->post('tds_surcharge_amount'),
					'tds_edu_cess'         => $this->input->post('tds_edu_cess'),
					'tds_edu_cess_amount'  => $this->input->post('tds_edu_cess_amount'),
					'tds_hedu_cess'        => $this->input->post('tds_hedu_cess'),
					'tds_hedu_cess_amount' => $this->input->post('tds_hedu_cess_amount'),
					'stax_on_amount'       => $this->input->post('stax_on_amount'),
					'tds_stax_bsr_code'    => $this->input->post('tds_stax_bsr_code'),
					'tds_stax_challan_no'  => $this->input->post('tds_stax_challan_no'),
					'stax_payment_month'   => ($this->input->post('stax_payment_month') ? join(", ", $this->input->post('stax_payment_month')) : ''),
					'pieces'               => $this->input->post('pieces'),
					'cbm'                  => $this->input->post('cbm'),
					'product_details'      => $this->input->post('product_details'),
					'narration'            => $this->input->post('narration'),
					'reference_ledger_id'  => $this->input->post('reference_ledger_id'),
					'remarks'              => $this->input->post('remarks'),
					'status'               => $this->input->post('status'),
					'category'			   => $this->input->post('category'),
				);

				if ($row['audited'] == 'No') {
					if (Auth::get('username') == 'auditor') redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);

					if ($id == 0 && $prev_id == 0) {
						$d = array_merge($data, $this->accounting->getNextVoucherNo($voucher_book_id, $data['date'], $data['job_id']));
						$data = $d;
					}

					$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
					$this->_updateJournal($id);
					
					setSessionAlert('SAVED', 'success');
				}
				else {
					setSessionError('Voucher Audited, Can not be modified...');
				}
			}
			else 
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
		}
	}
	
	function _updateJournal($id) {
		$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		$amounts = $this->input->post('vd_amount');
		$new_job_ids = $this->input->post('new_job_id');

		if ($amounts != null) {
			foreach ($amounts as $index => $amount) {
				if (! in_array("$index", $delete_ids)) {
					$this->kaabar->save($this->_table2, array('amount' => $amount), array('id' => $index));
				}
			}
		}
		
		if ($delete_ids != null) {
			if (Auth::hasAccess(Auth::DELETE)) {
				foreach ($delete_ids as $index) {
					if ($index > 0) {
						$this->kaabar->delete($this->_table2, $index);
					}
				}
			}
		}

		if ($new_job_ids != null) {
			$this->load->model('import');
			$new_bill_item_ids = $this->input->post('new_bill_item_id');
			$amounts = $this->input->post('new_amount');
			foreach ($new_job_ids as $index => $job_id) {
				if ($job_id > 0 && $new_bill_item_ids[$index] > 0 && $amounts[$index] > 0) {
					$data = array(
						'voucher_id'	=> $id,
						'job_id' 		=> $job_id,
						'bill_item_id' 	=> $new_bill_item_ids[$index],
						'amount'		=> $amounts[$index]
					);
					$vdid = $this->kaabar->save($this->_table2, $data, array('id' => 0));
					$this->import->updatePayments($job_id, $vdid, $data['bill_item_id']);
				}
			}
		}
	}

	function trips($dr_ledger_id, $id) {
		$voucher_id   = $this->input->post('voucher_id');
		if ($voucher_id > 0 && $id == $voucher_id) {
			$this->db->delete('voucher_trips', array('voucher_id' => $id));
		}

		$trip_ids = $this->input->post('trip_id');
		if ($trip_ids != null) {
			foreach ($trip_ids as $index => $trip_id) {
				if (strlen(trim($trip_id)) > 0) {
					$row = array(
						'voucher_id' => $id,
						'trip_id'    => $trip_id,
					);
					$this->kaabar->save('voucher_trips', $row);
				}
			}
		}

		$data['rows']        = $this->_getTrips($dr_ledger_id, $id);
		$data['javascript']  = array('js/jquery.filtertable.min.js');
		$data['voucher_id']  = array('voucher_id' => $id);
		$data['page_title']  = 'Trips';
		$data['hide_title']  = true;
		$data['hide_menu']   = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class.'_trip';
		$this->load->view('index', $data);
	}

	function _getTrips($dr_ledger_id, $id, $attached_only = FALSE) {
		$attached = ($attached_only ? 'WHERE ! ISNULL(VT.id) ' : '');
		$sql = "SELECT VT.id, VT.voucher_id, J.id2_format AS job_no, VT.advance, T.id AS trip_id, 
			DATE_FORMAT(T.date, '%d-%m-%Y') AS date, T.lr_no, T.party_reference_no, 
			IF(ISNULL(V.id), 0, 1) AS self, T.registration_no, PS.name AS party_name, TL.name AS transporter_name, T.transporter_rate,
			T.product_name, T.container_no, T.container_size, SF.name AS from_location, ST.name AS to_location, T.remarks,
			SUM(COALESCE(TA.amount, 0)) AS trip_advance, SUM(COALESCE(PA.amount, 0)) AS pump_advance,
			(T.transporter_rate - (SUM(COALESCE(TA.amount, 0)) + SUM(COALESCE(PA.amount, 0)))) AS balance
		FROM trips T 
			INNER JOIN ledgers TL ON (T.transporter_ledger_id = ? AND T.transporter_ledger_id = TL.id)
			LEFT OUTER JOIN jobs J ON T.job_id = J.id
			LEFT OUTER JOIN vehicles V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0)
			LEFT OUTER JOIN ledgers PS ON T.party_ledger_id = PS.id
			LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
			LEFT OUTER JOIN voucher_trips VT ON T.id = VT.trip_id AND VT.voucher_id = ?
			LEFT OUTER JOIN (
				SELECT TA.trip_id, 
					IF(TA.advance_by = 'Self', ROUND(SUM(TA.amount), 2), 0) AS self_adv, 
					IF(TA.advance_by = 'Party', ROUND(SUM(TA.amount), 2), 0) AS party_adv,
					SUM(TA.amount) AS amount
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = ?
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT PA.trip_id, SUM(PA.amount) AS amount
				FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
				WHERE T.company_id = ?
				GROUP BY PA.trip_id
			) PA ON T.id = PA.trip_id
		$attached 
		GROUP BY T.id
		ORDER BY VT.id DESC, T.date";
		$query = $this->db->query($sql, array($dr_ledger_id, $id, $this->_company['id'], $this->_company['id']));
		return $query->result_array();
	}

	function pumps($dr_ledger_id, $id) {
		$voucher_id   = $this->input->post('voucher_id');
		if ($voucher_id > 0 && $id == $voucher_id) {
			$this->db->delete('voucher_trips', array('voucher_id' => $id));
		}

		$trip_ids = $this->input->post('trip_id');
		if ($trip_ids != null) {
			foreach ($trip_ids as $index => $trip_id) {
				if (strlen(trim($trip_id)) > 0) {
					$row = array(
						'voucher_id'      => $id,
						'trip_id'         => $trip_id,
						'pump_advance_id' => $index,
					);
					$this->kaabar->save('voucher_trips', $row);
				}
			}
		}

		$data['rows']        = $this->_getPumps($dr_ledger_id, $id);
		$data['javascript']  = array('js/jquery.filtertable.min.js');
		$data['voucher_id']  = array('voucher_id' => $id);
		$data['page_title']  = 'Trips';
		$data['hide_title']  = true;
		$data['hide_menu']   = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class.'_pump';
		$this->load->view('index', $data);
	}

	function _getPumps($dr_ledger_id, $id, $attached_only = FALSE) {
		$attached = ($attached_only ? 'WHERE ! ISNULL(VT.id) ' : '');
		$sql = "SELECT VT.id, VT.voucher_id, J.id2_format AS job_no, VT.advance, T.id AS trip_id, PA.id AS pump_advance_id,
			DATE_FORMAT(T.date, '%d-%m-%Y') AS date, T.lr_no, T.party_reference_no, 
			IF(ISNULL(V.id), 0, 1) AS self, T.registration_no, PS.name AS party_name, TL.name AS transporter_name, T.transporter_rate,
			T.product_name, T.container_no, T.container_size, SF.name AS from_location, ST.name AS to_location, T.remarks,
			A.name AS pump_agent, PA.slip_no, PA.amount AS pump_advance
		FROM trips T INNER JOIN pump_advances PA ON T.id = PA.trip_id
			INNER JOIN ledgers A ON (A.id = ? AND A.company_id = ? AND PA.agent_id = A.agent_id)
			LEFT OUTER JOIN jobs J ON T.job_id = J.id
			LEFT OUTER JOIN vehicles V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0)
			LEFT OUTER JOIN ledgers PS ON T.party_ledger_id = PS.id
			LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
			LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
			LEFT OUTER JOIN voucher_trips VT ON (T.id = VT.trip_id AND VT.voucher_id = ? AND PA.id = VT.pump_advance_id)
		$attached 
		GROUP BY PA.id
		ORDER BY VT.id DESC, T.date";
		$query = $this->db->query($sql, array($dr_ledger_id, $this->_company['id'], $id));
		return $query->result_array();
	}

	function _checkDate($date, $args) {
		$args_arr = explode(',', $args);
		if($this->accounting->checkVoucherDate($args_arr[0], $args_arr[1], convDate($date))) {
			return TRUE;
		}
		$this->form_validation->set_message('_checkDate', 'Date not in Financial Year<br />Voucher Already Created on this Date<br />Voucher Date is less than Lock Date.');
		return FALSE;
	}

	function _is_ledger($id) {
		if($this->accounting->isLedger($id))
			return TRUE;
		else {
			$this->form_validation->set_message('_is_ledger', 'Invalid Ledger Code, Ledger does not Exists or Empty.');
			return FALSE;
		}
	}

	function _is_amount($amount) {
		if(intval($amount) > 0)
			return TRUE;
		else {
			$this->form_validation->set_message('_is_amount', 'Amount cannot be Zero or Empty.');
			return FALSE;
		}
	}

	function _is_duplicatebill($invoice_no, $args) {
		$args_arr = explode(',', $args);
		$voucher = $this->accounting->findDuplicateVoucher($args_arr[0], $args_arr[1], $this->input->post('cr_ledger_id'), $invoice_no);
		if($voucher['id'] == 0 || trim(strlen($invoice_no)) == 0)
			return TRUE;
		else {
			$this->form_validation->set_message('_is_duplicatebill', 'Duplicate Bill No found with Voucher No. ' . anchor($this->_clspath.$this->_class."/edit/".$voucher['voucher_book_id'].'/'.$voucher['id'], $voucher['id2'].'/'.$voucher['id3']));
			return FALSE;
		}
	}

	function checkVoucherDate($voucher_book_id, $voucher_id, $date) {
		if ($this->_is_ajax) {
			$status = '<addClass select="#DateCG" value="error" />
				<attr select=".UpdateButton" arg1="disabled" arg2="true" />
				<eval> 
			        $("#modal-sverror").modal();
			    </eval>';
			if ($this->accounting->checkVoucherDate($voucher_book_id, $voucher_id, convDate($date)))
				$status = '<removeClass select="#DateCG" value="error" /><removeAttr select=".UpdateButton" arg1="disabled" />';
			header('Content-type: text/xml');
			echo "<taconite>$status</taconite>";
		}
		else 
			echo "Access Denied";
	}

	// function audit($voucher_book_id, $id, $audit_action) {
	// 	$this->kaabar->save($this->_table, array('audited' => $audit_action), array('id' => $id));
	// 	redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
	// }

	function delete($voucher_book_id, $id = 0) {
		$audited = $this->kaabar->getField($this->_table, $id, 'id', 'audited');
		if ($audited == 'No') {
			if (Auth::hasAccess(Auth::DELETE)) {
				$this->db->query("UPDATE " . $this->_table . " SET tds_payment_id = 0 WHERE tds_payment_id = ?", array($id));
				$this->kaabar->delete('voucher_vouchers', array('voucher_id' => $id));
				$this->kaabar->delete($this->_table, $id);
				setSessionAlert('Voucher Deleted Successfully', 'success');
			}
			else 
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class.'/index/'.$voucher_book_id);
		}
		else {
			setSessionError('Voucher Audited. Can not be deleted...');
			redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
		}
	}

	function ajaxBL() {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			$search     = strtolower($this->input->get('term'));
			$sql = "SELECT J.id, J.id2_format, J.bl_no, P.name AS party
			FROM jobs J INNER JOIN parties P ON J.party_id = P.id
			WHERE J.id2_format LIKE '%$search%' OR 
				J.bl_no LIKE '%$search%' OR 
				P.name LIKE '%$search%'
			ORDER BY bl_no
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else
			echo "Access Denied";
	}

	function _documents($voucher_id) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		$this->load->model('import');
		$this->load->helper('file');

		$result = array();
		$docdir = $this->import->getDocFolder($this->_path, $voucher_id);
		$rows   = $this->kaabar->getRows($this->_table3, $voucher_id, 'voucher_id');
		foreach ($rows as $r) {
			$result[$r['id']] = get_file_info($this->_path.$docdir.$r['file']);
			if ($result[$r['id']] != false) {
				$result[$r['id']]['type'] = strtolower(substr($result[$r['id']]['name'], -3));
				$result[$r['id']]['url'] = $this->_path_url . '/' . $docdir . $result[$r['id']]['name'];
			}
		}
		return $result;
	}

	function attach($voucher_id) {
		$this->load->model('import');

		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = '*';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();

		$image  = $this->upload->data();
		$docdir = $this->import->getDocFolder($this->_path, $voucher_id);

		$newfile = uniqid().$image['file_ext'];
		rename($image['full_path'], $this->_path.$docdir.$newfile);

		$row = array(
			'voucher_id'=> $voucher_id,
			'datetime'	=> date('Y-m-d H:i:s'),
			'file' 		=> $newfile
		);
		$id = $this->kaabar->save($this->_table3, $row);
		setSessionAlert('BILL_ATTACHED', 'success');

		// $this->load->library('user_agent');
		// redirect($this->agent->referrer());
	}

	function detach($voucher_id = 0, $id = 0) {
		$this->load->model('import');

		if (Auth::hasAccess(Auth::DELETE)) {
			
			$docdir = $this->import->getDocFolder($this->_path, $voucher_id);
			$file   = $this->kaabar->getField($this->_table3, $id, 'id', 'file');
			if (file_exists($this->_path.$docdir.$file)) {
				rename($this->_path.$docdir.$file, $this->_share.$file);
			}
			$this->kaabar->delete($this->_table3, $id);
			setSessionAlert('DOC_DELETED', 'success');
		}
		else
			setSessionError('NO_PERMISSION');
		
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function preview($voucher_book_id, $id, $pdf = 0, $sub_voucher = 0, $letterhead = 0) {
		$voucher_book_id = intval($voucher_book_id);
		$id = intval($id);

		$this->load->model('import');
		$this->load->helper('numwords');
		$page = 'payment_preview';
		$default_company         = $this->session->userdata('default_company');		
		$company                 = $this->kaabar->getRow('companies', $default_company['id']);
		$data['company']         = $company;
		$data['city_name']       = $this->kaabar->getField('cities', $company['city_id']);
		$data['pincode']         = $this->kaabar->getField('cities', $company['city_id'], 'id', 'pincode');
		$data['voucher_book']    = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$data['voucher_type']    = $this->kaabar->getField('voucher_types', $data['voucher_book']['voucher_type_id']);
		$data['voucher']         = $this->accounting->getVoucher($voucher_book_id, $id);
		$data['voucher_details'] = $this->accounting->getVoucherJobDetails($id);
		$agent_id                = $this->kaabar->getField('ledgers', $data['voucher']['dr_ledger_id'], 'id', 'agent_id');
		$data['party']           = $this->kaabar->getRow('agents', $agent_id);
		$data['letterhead']      = $letterhead;
		switch ($data['voucher']['category']) {
			case 'Invoices':
				$data['invoices'] = $this->_getInvoices($data['voucher']['dr_ledger_id'], $data['voucher']['id'], TRUE);
				break;
			case 'Pumps':
				$page = 'payment_pumps_preview';
				$data['pumps'] = $this->_getPumps($data['voucher']['dr_ledger_id'], $data['voucher']['id'], TRUE);
				break;
			case 'Trips':
				$page = 'payment_trips_preview';
				$data['trips'] = $this->_getTrips($data['voucher']['dr_ledger_id'], $data['voucher']['id'], TRUE);
				break;
		}

		if ($pdf) {
				$filename = 'Bank Voucher -'.str_replace('/', '-', $data['voucher']['id2_format']);
			$html = $this->load->view('reports/'.$page, $data, true);

			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view('reports/'.$page, $data);
		}
	}
}
