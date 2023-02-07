<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Invoice extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->_table   = 'vouchers';
	}
	
	function index($voucher_book_id = 0, $starting_row = 0) {
		$voucher_book_id = intval($voucher_book_id);
		$starting_row = intval($starting_row);
		if (! $this->accounting->isVoucherBook($voucher_book_id)) {
			setSessionError('Invalid Voucher Book or Voucher Book does not exists');
			redirect();
		}

		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search');
			$search = false;
			redirect($this->_clspath.$this->_class."/index/$voucher_book_id");
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class."/index/$voucher_book_id");
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('No', 'Job No', 'Date', 'Cheque No', 'Debit', 'Name', 'Credit', 'Name', 'Amount', 'Docs'),
			'class' => array(
				'voucher_no' => array('class' => 'Text', 'link' => 'id'),
				'id2_format' => 'Code',
				'date'       => 'Date',
				'cheque_no'  => 'Text',
				'dr_code'    => '',
				'dr_name'    => 'tiny orange',
				'cr_code'    => '',
				'cr_name'    => 'tiny orange',
				'amount'     => 'alignright bold',
				'document'   => 'Label'
				),
			'link_col' => "voucher_no",
			'link_url' => $this->_clspath.$this->_class."/edit/$voucher_book_id/");
		$data['label_class'] = $this->accounting->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class."/index/$voucher_book_id");
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		$config['total_rows']  = $this->accounting->countInvoices($voucher_book_id, $search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->accounting->getInvoices($voucher_book_id, $search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$voucher_book_id", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = $this->accounting->getBookName($voucher_book_id);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($voucher_book_id, $id = 0) {
		$voucher_book_id = intval($voucher_book_id);
		$id = intval($id);

		if (! $this->accounting->isVoucherBook($voucher_book_id)) {
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
		$data['voucher_type'] = $this->kaabar->getField('voucher_types', $voucher_book['voucher_type_id']);
		if ($id == 0 && $voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Dr')
			$dr_ledger = $this->kaabar->getRow('ledgers', $voucher_book['default_ledger_id']);
		else if ($id == 0 && $voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Cr')
			$cr_ledger = $this->kaabar->getRow('ledgers', $voucher_book['default_ledger_id']);

		$row = $this->accounting->getVoucher($voucher_book_id, $id);
		if ($row == false) {
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
				'currency_id'          => 1,
				'currency_amount'      => 0,
				'exchange_rate'        => 0,
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
				'advance_amount'       => 0,
				'category'             => 'N/A',
				'reverse_charge'       => 'No',
				'place_of_supply'      => '',
				'remarks'              => ($voucher_book['job_type'] == 'Transportation' ? "AS PER NOTIFICATION NO.30/2012 OF SERVICE TAX DTD.20.06.2012 THE LIABILITY OF PAYMENT\nOF SERVICE TAX LIES WITH CONSIGNEE WE REQUEST YOU TO PAY\nTHE SERVICE TAX ON THIS BILL AT YOUR END CONFIRM." : ''),
				'status'               => 'Pending',
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

			if ($this->input->method() == 'post') {
				$post = $this->input->post(array_keys($row));
				foreach($post as $f => $v) {
					if ($v) $row[$f] = $v;
				}
			}
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
		
		$data['id']  = ['id' => $id];
		$data['row'] = $row;
		$data['row']['dr_closing'] = $this->accounting->getClosing($data['row']['dr_ledger_id']);
		$data['row']['cr_closing'] = $this->accounting->getClosing($data['row']['cr_ledger_id']);

		$data['reference_name']  = $this->kaabar->getField('ledgers', $row['reference_ledger_id'], 'id', 'CONCAT(code, " ", name)');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['vouchers']          = $this->accounting->getBLVouchers($row['job_id']);
			$data['export_activities'] = $this->kaabar->getRow('export_activities', array('job_id' => $row['job_id']));
			$data['row']['documents']  = $this->kaabar->getRows('voucher_documents', $row['id'], 'voucher_id', 'id, voucher_id');
			$data['voucher_book']      = $voucher_book;
			$data['default_company']   = $this->_company;
			$data['page_title'] = $this->accounting->getBookName($voucher_book_id);

			if ($voucher_book['job_type'] == 'Transportation') {
				$years = explode('_', $this->_company['financial_year']);
				$data['from_date'] = date('01-04-'.$years[0]);
				$data['to_date']   = date('d-m-Y');
			}

			else if ($voucher_book['job_type'] != 'N/A') {
				$this->load->model('import');
				$data['vessel_ledgers'] = $this->accounting->getVesselLedgers($row['vessel_id']);
				$data['importer']       = $this->kaabar->getRow('ledgers', $row['party_id'], 'party_id');
				$high_seas = $this->import->getHighSeas($row['job_id']);
				array_pop($high_seas);
				$data['high_seas'] = $high_seas;
			}


			if ($voucher_book['job_type'] == 'Transportation') {			
				$data['voucher_details'] = $this->accounting->getVoucherTransportationDetails($row['id']);
			}
			else {
				$data['voucher_details'] = $this->accounting->getVoucherJobDetails($row['id']);
			}
			$data['page']     = $this->_clspath.($voucher_book['job_type'] == 'Transportation' ? 'transport_' : '') . $this->_class . '_edit';
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$voucher_book_id/$id");

			$amount = 0;
			if ($row['dr_stax_category_id'] > 0 OR $row['cr_stax_category_id'] > 0) {
				$stax_rates = $this->db->query("SELECT * FROM stax_rates WHERE wef_date <= ? AND stax_category_id IN ? ORDER BY wef_date DESC LIMIT 0, 1", 
					[convDate($this->input->post('date')), [$row['dr_stax_category_id'], $row['cr_stax_category_id']]])->row_array();
				if (! $stax_rates) {
					$stax_rates = [
						'stax'        => Settings::get('service_tax', 14),
						'edu_cess'    => Settings::get('edu_cess', 0),
						'hedu_cess'   => Settings::get('hedu_cess', 0),
						'swachh_cess' => Settings::get('swachh_cess', 0.5),
						'krishi_cess' => Settings::get('krishi_cess', 0.5),
					];
				}
				$amount  = round($this->input->post('stax_on_amount') * $stax_rates['stax'] / 100, 0);
				$amount += round($this->input->post('stax_on_amount') * $stax_rates['swachh_cess'] * $stax_rates['krishi_cess'] / 100, 0);
				$amount += (round($amount * $stax_rates['edu_cess'] / 100, 0) + 
							round($amount * $stax_rates['hedu_cess'] / 100, 0));
			}
			else if ($row['dr_tds_class_id'] > 0 OR $row['cr_tds_class_id'] > 0) {
				$amount = (
					$this->input->post('tds_amount') +
					$this->input->post('tds_surcharge') +
					$this->input->post('tds_edu_cess') +
					$this->input->post('tds_hedu_cess')
				);
			}
			$data = array(
				'voucher_book_id'      => $voucher_book_id,
				'job_id'               => $this->input->post('job_id'),
				'id2_format'           => ($this->input->post('id2_format') ? $this->input->post('id2_format') : $row['id2_format']),
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
				'currency_id'          => $this->input->post('currency_id'),
				'currency_amount'      => $this->input->post('currency_amount'),
				'exchange_rate'        => $this->input->post('exchange_rate'),
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
				'status'               => $row['status'],
				'category'             => $this->input->post('category'),
				'reverse_charge'       => $this->input->post('reverse_charge'),
				'place_of_supply'      => $this->input->post('place_of_supply'),
			);

			if ($row['audited'] == 'No') {
				if (Auth::get('username') == 'auditor') redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);

				if ($id == 0) {
					$copy = $data;
					$d = array_merge($data, $this->accounting->getNextVoucherNo($voucher_book_id, $data['date'], $data['job_id']));
					$data = $d;
				}
				$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
				
				if ($voucher_book['job_type'] == 'Transportation')
					$this->_updateTransportInvoice($voucher_book_id, $id);
				else
					$this->_updateInvoice($voucher_book_id, $id);
				
				setSessionAlert('Changes saved successfully', 'success');
			}
			else {
				setSessionError('Voucher Audited, Can not be modified...');
			}

			redirect($this->_clspath.$this->_class."/edit/$voucher_book_id/$id");
		}
	}
	
	function _updateTransportInvoice($voucher_book_id, $id) {
		$delete_ids  = $this->input->post('delete_id') == false ? ['0' => 0] : $this->input->post('delete_id');

		$advance = 0;

		$particulars = $this->input->post('vjd_particulars');
		if ($particulars != null) {
			$trip_ids = $this->input->post('vjd_trip_id');
			$sr_nos   = $this->input->post('vjd_sr_no');
			$units    = $this->input->post('vjd_units');
			$rates    = $this->input->post('vjd_rate');
			$advances = $this->input->post('vjd_advance');
			$amounts  = $this->input->post('vjd_amount');
			$cgsts    = $this->input->post('vjd_cgst');
			$sgsts    = $this->input->post('vjd_sgst');
			$igsts    = $this->input->post('vjd_igst');

			foreach ($particulars as $index => $particular) {
				if (! in_array("$index", $delete_ids)) {
					$amount = $amounts[$index];
					if ($trip_ids[$index] > 0 AND $units[$index] == 0)
						$amount = $rates[$index];
					else
						$amount = round($units[$index] * $rates[$index], 2);

					$data = array(
						'voucher_id'  => $id,
						'sr_no'       => $sr_nos[$index],
						'particulars' => $particular,
						'units'       => $units[$index],
						'rate'        => $rates[$index],
						'amount'      => $amount,
						'cgst'        => floatval($cgsts[$index]),
						'sgst'        => floatval($sgsts[$index]),
						'igst'        => floatval($igsts[$index]),
					);
					$this->kaabar->save('voucher_details', $data, ['id' => $index]);
					$advance += $advances[$index];
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete('voucher_details', $index);
				}
			}
		}

		$bill_item_ids = $this->input->post('new_bill_item_id');
		if ($bill_item_ids != null) {
			$trip_ids    = $this->input->post('new_trip_id');
			$sr_nos      = $this->input->post('new_sr_no');
			$particulars = $this->input->post('new_particulars');
			$units       = $this->input->post('new_units');
			$rates       = $this->input->post('new_rate');
			$advances    = $this->input->post('new_advance');
			$amounts     = $this->input->post('new_amount');
			$cgsts       = $this->input->post('new_cgst');
			$sgsts       = $this->input->post('new_sgst');
			$igsts       = $this->input->post('new_igst');

			foreach ($bill_item_ids as $index => $bill_item_id) {
				if ($bill_item_id > 0 AND intval($trip_ids[$index]) > 0) {
					$bi_row = $this->kaabar->getRow('ledgers', $bill_item_id);
					$data          = array(
						'voucher_id'   => (isset($debit_note_id) && $reimbersment == 'Yes' ? $debit_note_id : $id),
						'bill_item_id' => $bill_item_id,
						'sac_hsn'      => $bi_row['sac_hsn'],
						'sr_no'        => intval($sr_nos[$index]),
						'particulars'  => $particulars[$index],
						'trip_id'      => intval($trip_ids[$index]),
						'units'        => floatval($units[$index]),
						'rate'         => floatval($rates[$index]),
						'amount'       => floatval($amounts[$index]),
						'cgst'         => floatval($cgsts[$index]),
						'sgst'         => floatval($sgsts[$index]),
						'igst'         => floatval($igsts[$index]),
					);
					$this->kaabar->save('voucher_details', $data);
					$advance += $advances[$index];
				}
			}
		}
		$this->db->update($this->_table, array('advance_amount' => $advance), array('id' => $id));
		$this->accounting->updateVoucherServiceTax($id);
	}

	function _updateInvoice($voucher_book_id, $id) {
		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$particulars = $this->input->post('vjd_particulars');
		$new_particulars = $this->input->post('new_particulars');

		if ($particulars != null) {
			$sr_nos           = $this->input->post('vjd_sr_no');
			$sac_hsns         = $this->input->post('vjd_sac_hsn');
			$units            = $this->input->post('vjd_units');
			$rates            = $this->input->post('vjd_rate');
			$currency_amounts = $this->input->post('vjd_currency_amount');
			$amounts          = $this->input->post('vjd_amount');
			$cgsts            = $this->input->post('vjd_cgst');
			$sgsts            = $this->input->post('vjd_sgst');
			$igsts            = $this->input->post('vjd_igst');

			foreach ($particulars as $index => $particular) {
				if (! in_array("$index", $delete_ids)) {
					$amount = ($amounts[$index] > 0 ? $amounts[$index] : round($units[$index] * $rates[$index], 2));
					if ($currency_amounts[$index] > 0 && $this->input->post('exchange_rate') > 1)
						$amount = round($currency_amounts[$index] * $this->input->post('exchange_rate'));
					$data = array(
						'voucher_id'      => $id,
						'sr_no'           => $sr_nos[$index],
						'particulars'     => $particular,
						'sac_hsn'         => $sac_hsns[$index],
						'currency_amount' => $currency_amounts[$index],
						'units'           => $units[$index],
						'rate'            => $rates[$index],
						'amount'          => $amount,
						'cgst'            => $cgsts[$index],
						'sgst'            => $sgsts[$index],
						'igst'            => $igsts[$index],
					);
					$this->kaabar->save('voucher_details', $data, ['id' => $index]);
				}
			}
		}
		
		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete('voucher_details', $index);
				}
			}
		}

		// Check if Debit Note has to be created also.
		if ($this->input->post('create_id')) {
			
			$job_type = $this->kaabar->getField('voucher_books', $voucher_book_id, 'id', 'job_type');
			$query = $this->db->query("SELECT id FROM voucher_books WHERE company_id = ? AND voucher_type_id = 3 AND job_type = ?", 
				array($this->_company['id'], $job_type));
			$row = $query->row_array();
			$voucher_book_id = $row['id'];

			$job_id = $this->input->post('job_id');
			$job = $this->kaabar->getRow('jobs', $job_id);
			$vessel_ledgers = $this->accounting->getVesselLedgers($job['vessel_id']);

			foreach ($vessel_ledgers as $vl) {
				if ($vl['account_id'] == 19) {
					$credit_id      = $vl['id'];
				}
			}

			$data = array(
				'voucher_book_id'     => $voucher_book_id,
				'job_id'              => $this->input->post('job_id'),
				'id2_format'          => '',
				'id2'                 => 0,
				'id3'                 => $this->input->post('id3'),
				'date'                => $this->input->post('date'),
				'invoice_no'          => $this->input->post('invoice_no'),
				'cheque_no'           => $this->input->post('cheque_no'),
				'cheque_date'         => $this->input->post('cheque_date'),
				'reconciliation_date' => $this->input->post('reconciliation_date'),
				'dr_ledger_id'        => $this->input->post('dr_ledger_id'),
				'cr_ledger_id'        => $credit_id,
				'amount'              => $this->input->post('amount'),
				'invoice_date'        => $this->input->post('invoice_date'),
				'invoice_amount'      => $this->input->post('invoice_amount'),
				'tds'                 => $this->input->post('tds'),
				'tds_amount'          => $this->input->post('tds_amount'),
				'tds_surcharge'       => $this->input->post('tds_surcharge'),
				'tds_edu_cess'        => $this->input->post('tds_edu_cess'),
				'tds_hedu_cess'       => $this->input->post('tds_hedu_cess'),
				'stax_on_amount'      => $this->input->post('stax_on_amount'),
				'pieces'              => $this->input->post('pieces'),
				'cbm'                 => $this->input->post('cbm'),
				'product_details'     => $this->input->post('product_details'),
				'narration'           => $this->input->post('narration'),
				'reference_ledger_id' => $this->input->post('reference_ledger_id'),
				'remarks'             => $this->input->post('remarks'),
				'place_of_supply'     => $this->input->post('place_of_supply'),
				'status'              => $this->input->post('status'),
			);

			$d = array_merge($data, $this->accounting->getNextVoucherNo($voucher_book_id, $data['date'], $data['job_id']));
			$data = $d;
			$debit_note_id = $this->kaabar->save($this->_table, $data);
		}

		if ($new_particulars != null) {
			$bill_item_ids    = $this->input->post('new_bill_item_id');
			$sr_nos           = $this->input->post('new_sr_no');
			$sac_hsns         = $this->input->post('new_sac_hsn');
			$units            = $this->input->post('new_units');
			$rates            = $this->input->post('new_rate');
			$currency_amounts = $this->input->post('new_currency_amount');
			$amounts          = $this->input->post('new_amount');
			$cgsts            = $this->input->post('new_cgst');
			$sgsts            = $this->input->post('new_sgst');
			$igsts            = $this->input->post('new_igst');

			foreach ($new_particulars as $index => $particular) {
				if ($bill_item_ids[$index] > 0) {
					$reimbursement = $this->kaabar->getField('ledgers', $bill_item_ids[$index], 'id', 'reimbursement');
					$amount = ($amounts[$index] > 0 ? $amounts[$index] : round($units[$index] * $rates[$index], 2));
					if ($currency_amounts[$index] > 0 && $this->input->post('exchange_rate') > 1)
						$amount = round($currency_amounts[$index] * $this->input->post('exchange_rate'));
					$data = array(
						'voucher_id'      => (isset($debit_note_id) && $reimbersment == 'Yes' ? $debit_note_id : $id),
						'bill_item_id'    => $bill_item_ids[$index],
						'sr_no'           => $sr_nos[$index],
						'particulars'     => $particular,
						'sac_hsn'         => $sac_hsns[$index],
						'currency_amount' => $currency_amounts[$index],
						'units'           => $units[$index],
						'rate'            => $rates[$index],
						'amount'          => $amount,
						'cgst'            => $cgsts[$index],
						'sgst'            => $sgsts[$index],
						'igst'            => $igsts[$index],
					);
					$this->kaabar->save('voucher_details', $data);
				}
			}
		}
		$this->accounting->updateVoucherServiceTax($id);
		
		if (isset($debit_note_id))
			$this->accounting->updateVoucherTotal($debit_note_id);
	}

	function voucher_audit($voucher_book_id, $id, $audit_action) {
		$this->kaabar->save($this->_table, array('audited' => $audit_action), array('id' => $id));
		redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
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
		if ($this->input->is_ajax_request()) {
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

	function deleteVoucher($voucher_book_id, $id = 0) {
		$audited = $this->kaabar->getField($this->_table, $id, 'id', 'audited');
		if ($audited == 'No') {
			$this->db->query("UPDATE $this->_table SET tds_payment_id = 0 WHERE tds_payment_id = ?", array($id));
			$this->kaabar->delete('voucher_vouchers', array('voucher_id' => $id));
			$this->kaabar->delete('voucher_details', array('voucher_id' => $id));
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Voucher Deleted Successfully', 'success');

			redirect($this->_clspath.$this->_class.'/index/'.$voucher_book_id);
		}
		else {
			setSessionError('Voucher Audited. Can not be deleted...');
			redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
		}
	}

	function ajaxInvoiceBL($voucher_book_id, $voucher_type = 'Import-Export') {
		if ($this->input->is_ajax_request()) {
			$search     = strtolower($this->input->post_get('term'));
			$is_coastal = ($voucher_type == 'Coastal' ? "J.is_coastal = 'Yes' AND" : '');
			$sql = $import = $export = '';
			$company_id = $this->_company['id'];
			if ($voucher_type == 'Import' OR $voucher_type == 'Import-Export' OR $voucher_type == 'Coastal') {
				$import = "SELECT J.id, LEFT(J.type, 1) AS type, J.id2_format, J.bl_no AS bl_no, P.name AS party, 
					J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, VO.id AS voucher_id
				FROM jobs J
					INNER JOIN parties P ON J.party_id = P.id
					INNER JOIN vessels V ON J.vessel_id = V.id
					LEFT OUTER JOIN vouchers VO ON (J.id = VO.job_id AND VO.voucher_book_id = $voucher_book_id)
					LEFT OUTER JOIN voucher_books VB ON (VB.company_id = $company_id AND VO.voucher_book_id = VB.id)
				WHERE $is_coastal J.type = 'Import' AND (J.id2_format LIKE '%$search%' OR J.bl_no LIKE '%$search%' OR P.name LIKE '%$search%')
				GROUP BY J.id";
			}
			if ($voucher_type == 'Export' OR $voucher_type == 'Import-Export' OR $voucher_type == 'Coastal') {
				$export = "SELECT J.id, LEFT(J.type, 1) AS type, J.id2_format, CJ.sb_no AS bl_no, P.name AS party, 
					J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, VO.id AS voucher_id
				FROM jobs J
					INNER JOIN parties P ON J.party_id = P.id
					INNER JOIN vessels V ON J.vessel_id = V.id
					INNER JOIN child_jobs CJ ON J.id = CJ.job_id
					LEFT OUTER JOIN vouchers VO ON (J.id = VO.job_id AND VO.voucher_book_id = $voucher_book_id)
					LEFT OUTER JOIN voucher_books VB ON (VB.company_id = $company_id AND VO.voucher_book_id = VB.id)
				WHERE $is_coastal J.type = 'Export' AND (J.id2_format LIKE '%$search%' OR CJ.sb_no LIKE '%$search%' OR P.name LIKE '%$search%')
				GROUP BY CJ.id";
			}

			if ($voucher_type == 'Import-Export' OR $voucher_type == 'Coastal')
				$sql = $import . ' UNION ' . $export . " ORDER BY bl_no LIMIT 0, 50";
			else
				$sql = $import . ' ' . $export . " ORDER BY bl_no LIMIT 0, 50";


			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxTrips() {
		$from_date       = convDate($this->input->post('from_date'));
		$to_date         = convDate($this->input->post('to_date'));
		$party_ledger_id = $this->input->post('party_ledger_id');

		$optional = [
			'T.from_location_id'   => $this->input->post('from_location_id'),
			'T.to_location_id'     => $this->input->post('to_location_id'),
			'T.party_reference_no' => $this->input->post('party_reference_no'),
		];

		$sql = "SELECT T.*, DATE_FORMAT(T.date, '%d-%m-%Y') AS date, 
			FS.name AS from_station_name, TS.name AS to_station_name, 
			(COALESCE(TA.advance, 0) + COALESCE(VT.advance, 0)) AS advance
			FROM trips T
				LEFT OUTER JOIN locations FS ON T.from_location_id = FS.id
				LEFT OUTER JOIN locations TS ON T.to_location_id = TS.id
				LEFT OUTER JOIN (
					SELECT TA.trip_id, SUM(TA.amount) AS advance
					FROM trip_advances TA
						INNER JOIN trips T ON TA.trip_id = T.id
					WHERE T.company_id = ? AND TA.advance_by = 'Party'
					GROUP BY TA.trip_id
				) TA ON T.id = TA.trip_id
				LEFT OUTER JOIN (
					SELECT VT.trip_id, SUM(VT.advance) AS advance
					FROM voucher_trips VT
						INNER JOIN vouchers V ON VT.voucher_id = V.id
						INNER JOIN voucher_books VB ON (VB.company_id = ? AND V.voucher_book_id = VB.id)
						INNER JOIN voucher_types VTS ON (VB.voucher_type_id = VTS.id AND VTS.name = 'Receipt')
					GROUP BY VT.trip_id
				) VT ON T.id = VT.trip_id
			WHERE T.party_ledger_id = ? AND T.date >= ? AND T.date <= ? AND
				T.id NOT IN (
					SELECT DISTINCT trip_id
					FROM voucher_details VD
						INNER JOIN vouchers V ON VD.voucher_id = V.id
						INNER JOIN voucher_books VB ON (V.voucher_book_id = VB.id AND VB.company_id = ?)
						INNER JOIN voucher_types VTY ON (VB.voucher_type_id = VTY.id AND VTY.name = 'Invoice')
				)
			";
		foreach ($optional as $key => $value) {
			if ($value) {
				$sql .= " AND $key = '$value'";
			}
		}
		$sql  .= " GROUP BY T.id";
		$query = $this->db->query($sql, array(
			$this->accounting->getCompanyID(),
			$this->accounting->getCompanyID(),
			$party_ledger_id, $from_date, $to_date,
			$this->accounting->getCompanyID(),
		));
		$rows  = $query->result_array();
		$result = '';
		$i = 1;
		foreach ($rows as $key => $r) {
			$result .= '<tr>
			<td class="aligncenter SortHandle"><i class="icon-bars"></i></th>
			<td class="aligncenter"><input type="hidden" name="new_sr_no['.$r['id'].']" value="'.$i++.'" />
				<input type="checkbox" name="new_trip_id['.$r['id'].']" value="'.$r['id'].'" class="SelectCheckBox" /></td>
			<td><input type="hidden" class="form-control form-control-sm BillItemID" name="new_bill_item_id['.$r['id'].']" value="0" />
				<input type="text" class="form-control form-control-sm BillItemCode" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Particular" name="new_particulars['.$r['id'].']" value="" /></td>
			<td class="tiny">'.$r['date'].'</td>
			<td class="tiny">'.$r['registration_no'].'</td>
			<td class="tiny">'.anchor('transport/trip/edit/Container/'.$r['id'], $r['lr_no'], 'target="_blank"').'</td>
			<td class="tiny">'.$r['party_reference_no'].'</td>
			<td class="tiny">'.$r['container_no'].'</td>
			<td class="tiny">'.$r['from_station_name'].'</td>
			<td class="tiny">'.$r['to_station_name'].'</td>
			<td><input type="hidden" name="new_units['.$r['id'].']" value="1" />
			<input type="text" class="form-control form-control-sm Numeric" name="new_rate['.$r['id'].']" value="'.$r['party_rate'].'" ' . ($r['party_rate'] > 0 ? 'readonly="true"' : null) . ' /></td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="new_advance['.$r['id'].']" value="'.$r['advance'].'" /></td>
			<td class="alignright"><input type="text" class="form-control form-control-sm Numeric" name="new_amount['.$r['id'].']" value="'.$r['party_rate'].'" /></td>
			<td><input type="text" class="form-control form-control-sm CGST Numeric" name="new_cgst['.$r['id'].']" value="" /></td>
			<td><input type="text" class="form-control form-control-sm SGST Numeric" name="new_sgst['.$r['id'].']" value="" /></td>
			<td><input type="text" class="form-control form-control-sm IGST Numeric" name="new_igst['.$r['id'].']" value="" /></td>
			<td class="alignright">'.($r['party_rate']-$r['advance']).'</td>
			<td class="aligncenter"><button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus"></i></button></td>
		</tr>';
		}
		echo $result;
	}

	function ajaxVessels() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT V.id, CONCAT(V.prefix,' ', V.name,' ', V.voyage_no) AS name FROM vessels V WHERE CONCAT(V.prefix,' ', V.name,' ', V.voyage_no) LIKE '%$search%' ORDER BY CONCAT(V.prefix,' ', V.name,' ', V.voyage_no) LIMIT 0, 10";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function loadJobDetails($voucher_book_id, $job_id, $ledger_id = 0) {
		if (intval($job_id) == 0) return;
		$this->load->model('import');

		$voucher_book = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$voucher_type = $this->kaabar->getRow('voucher_types', $voucher_book['voucher_type_id']);
		$job          = $this->kaabar->getRow('jobs', $job_id);
		$vessel       = $this->kaabar->getRow('vessels', $job['vessel_id']);
		$product      = $this->kaabar->getRow('products', $job['product_id']);
		$party        = $this->kaabar->getRow('parties', $job['party_id']);
		$hss_parties  = $this->import->getHighSeas($job_id);
		$hss_buyer    = array_pop($hss_parties);

		$hss_seller = '';
		foreach ($hss_parties as $hss) {
			$hss_seller .= '<span class="label label-warning">' . $hss['name'] . '</span> ';
		}

		$vessel_ledgers = $this->accounting->getVesselLedgers($job['vessel_id']);
		$vouchers       = $this->accounting->getPendingBLVouchers($job_id);

		$sql = "SELECT MAX(wef_date) AS wef_date FROM party_rates PR
		WHERE PR.party_id = ? AND
			PR.company_id = ? AND
			PR.type = ? AND
			PR.cargo_type = ? AND
			PR.indian_port_id = ?";
		$query = $this->db->query($sql, array(
			(isset($hss_buyer['party_id']) ? $hss_buyer['party_id'] : $party['id']),
			$this->_company['id'], $job['type'], $job['cargo_type'],
			($job['type'] == 'Import' ? $job['indian_port_id'] : $job['loading_port_id'])
		));
		$wef_date = $query->row_array();

		if ($voucher_book['job_type'] != 'N/A' && $voucher_type['name'] == 'Invoice') {
			$sql = "SELECT BI.id, BI.code, BI.reimbursement, BI.sac_hsn, PR.particulars, PR.calc_type, PR.unit_type,
				COALESCE(PR.rate, 0) AS rate, BI.cgst, BI.sgst, BI.igst, stax_category_id
				FROM party_rates PR
					INNER JOIN ledgers BI ON (BI.reimbursement = 'No' AND PR.bill_item_id = BI.ID)
				WHERE PR.party_id = ? AND
					  PR.wef_date = ? AND
					  PR.company_id = ? AND
					  PR.type = ? AND
					  PR.cargo_type = ? AND
					  PR.indian_port_id = ?";
			$query = $this->db->query($sql, array(
				(isset($hss_buyer['party_id']) ? $hss_buyer['party_id'] : $party['id']), $wef_date['wef_date'], 
				$this->_company['id'], $job['type'], $job['cargo_type'],
				($job['type'] == 'Import' ? $job['indian_port_id'] : $job['loading_port_id'])
			));
		}
		else {
			$sql = "SELECT BI.id, BI.code, BI.reimbursement, BI.sac_hsn, PR.particulars, PR.calc_type, PR.unit_type,
				COALESCE(PR.rate, 0) AS rate, BI.cgst, BI.sgst, BI.igst, stax_category_id
				FROM party_rates PR
					INNER JOIN ledgers BI ON (BI.reimbursement = 'No' AND PR.bill_item_id = BI.ID)
				WHERE PR.party_id = ? AND
					  PR.wef_date = ? AND
					  PR.company_id = ? AND
					  PR.type = ? AND
					  PR.cargo_type = ? AND
					  PR.indian_port_id = ?";
			$query = $this->db->query($sql, array(
				($hss_buyer ? $hss_buyer['party_id'] : $party['id']), $wef_date['wef_date'],
				$this->_company['id'], $job['type'], $job['cargo_type'],
				($job['type'] == 'Import' ? $job['indian_port_id'] : $job['loading_port_id'])
			));
		}
		$bill_templates = $query->result_array();

		if ($job['type'] == 'Export') {
			$query = $this->db->query('SELECT S.job_id,
				COUNT(IF(CT.size = 20, S.id, null)) AS container_20,
				COUNT(IF(CT.size = 40, S.id, null)) AS container_40
			FROM deliveries_stuffings S INNER JOIN jobs J ON S.job_id = J.id
				LEFT OUTER JOIN container_types CT ON S.container_type_id = CT.id
			WHERE S.job_id = ?
			GROUP BY S.job_id', array($job['id']));
			$stuffings = $query->row_array();
		}
			
		$cbm  = ($job['cbm'] == 0 ? $job['net_weight'] : $job['cbm']);
		$units = array(
			'N/A'           => 0,
			'CBM'           => $cbm,
			'Round CBM'     => round($cbm),
			'Wharfage CBM'  => ((ceil($cbm) - round($cbm)) <= 0 ? ceil($cbm) : (floor($cbm) + 0.5)),
			'Ceil CBM'      => ceil($cbm),
			'Floor CBM'     => floor($cbm),
			'Containers 20' => ($job['type'] == 'Export' ? $stuffings['container_20'] : $job['container_20']),
			'Containers 40' => ($job['type'] == 'Export' ? $stuffings['container_40'] : $job['container_40'])
		);

		header('Content-type: text/xml');

		$product_details = $this->kaabar->getField('products', $job['product_id']);
		if (strlen($product_details) == 0)
			$product_details = $job['details'];

		header('Content-type: text/xml');
		echo '<taconite>
	<eval><![CDATA[
		$("#ProductName").val(\'' . str_replace("'", "\'", $product_details) . '\');
		$("#Pieces").val(\'' . $job['packages'] . '\');
        $("#CBM").val(\'' . $cbm . '\');
        $("#Containers").text(\'' . $units['Containers 20'] . ' / ' . $units['Containers 40'] . '\');';

        if ($voucher_type['id'] == 2) {
        	echo '
        $("#Importer").html(\'<span class="label label-info">' . $party['name'] . '</span>\');
		$("#HighSeas").html(\'' . $hss_seller . '\');
		
		$("#VesselLedgers").html(\'<span class="label label-info">' . $vessel['prefix'] . ' ' . $vessel['name'] . ' ' . $vessel['voyage_no'] . '</span>\');
	   	';
		}
		else {
			echo '
		$("#Importer").html(\'<span class="label label-info">' . $party['name'] . '</span>\');
		$("#HighSeas").html(\'' . $hss_seller . '\');
		
		$("#VesselLedgers").html(\'<span class="label label-info">' . $vessel['prefix'] . ' '. $vessel['name'] . ' ' . $vessel['voyage_no'] . '</span>\');
		';
		}

		echo '$("#VouchersFound tbody").empty();
		';
		foreach ($pending_vouchers['vouchers'] as $v) {
			echo '
			$("#VouchersFound tbody").append(\'<tr> \
				<td class="alignmiddle">' . $v['company_code'] . '</td> \
				<td class="alignmiddle">' . anchor('/accounting/' . underscore($v['url']), $v['id2_format'], 'target="_blank"') . '</td> \
				<td class="alignmiddle">' . $v['name'] . '</td> \
				<td class="alignright">';
					if (! isset($pending_vouchers['bills'][$v['code']]))
						echo '<span class="red">' . inr_format($v['amount'], 2) . '</span>';
					else if ($v['amount'] <= $pending_vouchers['bills'][$v['code']]['amount'])
						echo anchor('/accounting/' . underscore($pending_vouchers['bills'][$v['code']]['url']), '<span class="green">' . inr_format($v['amount'], 2) . '</span>', 'target="_blank"');
					else
						echo anchor('/accounting/' . underscore($pending_vouchers['bills'][$v['code']]['url']), '<span class="orange">' . inr_format($v['amount'], 2) . '</span>', 'target="_blank"');
				echo '</td> \
			</tr>\');
		';
		}

        $staxes = array();
        $sr_no = 1;
        foreach ($bill_templates as $bt) {
        	if ($bt['calc_type'] == 'Fixed') {
        		$amount = $bt['rate'];
        	}
        	elseif ($bt['calc_type'] == 'Vouchers') {
        		$amount = (isset($vouchers[$bt['code']]) ? $vouchers[$bt['code']]['amount'] : $bt['rate']);
        	}
        	else {
        		$amount = round($units[$bt['unit_type']] * $bt['rate']);
        	}
        	
        	//if ($amount == 0)
        	//	continue;

        	$sql = "SELECT SC.id, L.code, L.name, SC.name AS stax_category,
        			COALESCE(SR.stax, 0) AS stax, COALESCE(SR.edu_cess, 0) AS edu_cess, COALESCE(SR.hedu_cess, 0) AS hedu_cess
				FROM ledgers L
					INNER JOIN stax_categories SC ON L.stax_category_id = SC.id
					LEFT OUTER JOIN stax_rates SR ON L.stax_category_id = SR.stax_category_id
				WHERE L.category = 'General' AND L.stax_category_id = ?
				ORDER BY SR.wef_date DESC
				LIMIT 0, 1";
			$query  = $this->db->query($sql, [$bt['stax_category_id']]);
			$staxes[$bt['stax_category_id']] = $query->row_array();
			if (isset($staxes[$bt['stax_category_id']]['amount']))
				$staxes[$bt['stax_category_id']]['amount'] += $amount;
			else
				$staxes[$bt['stax_category_id']]['amount'] = $amount;

			echo '
		$("tr.TemplateRow .SrNo").val(\'' . $sr_no++ . '\');
		$("tr.TemplateRow .BillItemID").val(\'' . $bt['id'] . '\');
		$("tr.TemplateRow .BillItemCode").val(\'' . $bt['code'] . '\');
		$("tr.TemplateRow .SAC_HSN").val(\'' . $bt['sac_hsn'] . '\');
		$("tr.TemplateRow .Particular").val(\'' . $bt['particulars'] . '\');
		$("tr.TemplateRow .Currency").val(\'1\');
		$("tr.TemplateRow .Units").val(\'' . $units[$bt['unit_type']] . '\');
		$("tr.TemplateRow .Rate").val(\'' . $bt['rate'] . '\');
		$("tr.TemplateRow .Amount").val(\'' . $amount . '\');
		$("tr.TemplateRow .CGST").val(\'' . $bt['cgst'] . '\');
		$("tr.TemplateRow .SGST").val(\'' . $bt['sgst'] . '\');
		$("tr.TemplateRow .IGST").val(\'' . $bt['igst'] . '\');
		$("tr.TemplateRow").find(".AddButton").click();
		';
        }

		foreach ($staxes as $stid => $st) {
			$stax = 0; $edu_cess = 0; $hedu_cess = 0; $amount = 0;
			if (isset($staxes[$stid]['stax'])) {
				$stax      = round(($staxes[$stid]['amount'] * $staxes[$stid]['stax']) / 100);
				$edu_cess  = round(($stax * $staxes[$stid]['edu_cess']) / 100);
				$hedu_cess = round(($stax * $staxes[$stid]['hedu_cess']) / 100);
				$amount    = ($stax + $edu_cess + $hedu_cess);

			echo '$("tr.TemplateRow .SrNo").val(\'0\');
		$("tr.TemplateRow .BillItemID").val(\'0\');
		$("tr.TemplateRow .BillItemCode").val(\'' . $st['code'] . '\');
		$("tr.TemplateRow .Particular").val(\'' . $st['name'] . '\');
		$("tr.TemplateRow .Currency").val(\'1\');
		$("tr.TemplateRow .Units").val(\'0\');
		$("tr.TemplateRow .Rate").val(\'0\');
		$("tr.TemplateRow .Amount").val(\'' . $amount . '\');
		$("tr.TemplateRow").find(".AddButton").click();
		';
			}
		}
	echo ']]>
	</eval>
</taconite>';
	}

	function invoiceAgaints() {
		$sql = "SELECT * FROM `vouchers` WHERE `voucher_book_id` = '3' AND `date` >= '2014-06-01'";
		$query = $this->db->query($sql);
		$sacco = $query->result_array();
		$al = array();
		foreach ($sacco as $invoice) {
			unset($invoice['id']);
			$invoice['voucher_book_id'] = 5;
			$invoice['id2'] = null;
			$invoice['dr_ledger_id'] = $this->kaabar->getField('ledgers', array('code' => 'SACOO', 'company_id' => 2), 'id', 'id');
			$invoice['cr_ledger_id'] = $this->kaabar->getField('ledgers', array('code' => 'INVOICE', 'company_id' => 2), 'id', 'id');
			$d = array_merge($invoice, $this->accounting->getNextVoucherNo(5, $invoice['date'], $invoice['job_id'], 2));
			$invoice = $d;
			$this->kaabar->save($this->_table, $invoice);
			$al[] =$invoice;
		}
		echo "Done!!! ". count($al) . " entry added";
	}

	function preview($voucher_book_id, $id = 0, $pdf = 0, $letterhead = 0) {
		$voucher_book_id = intval($voucher_book_id);
		$id = intval($id);

		if(! $this->accounting->isVoucherBook($voucher_book_id)) {
			setSessionError('Invalid Voucher Book or Voucher Book does not exists');
			echo closeWindow();
			return;
		}
		
		$this->load->helper('numwords');

		$default_company = $this->session->userdata('default_company');
		$voucher_book    = $this->kaabar->getRow('voucher_books', $voucher_book_id);

		switch ($voucher_book['voucher_type_id']) {
			case 2: $invoice_type = 'credit_note'; break;
			case 3: $invoice_type = 'debit_note';  break;
			case 4: $invoice_type = 'invoice'; 	   break;
			default:
				echo "Only Credit Note, Debit Note and Invoice can be printed.";
				return;
		}

		$data['invoice_type']    = humanize($invoice_type);
		$data['company']         = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']            = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']           = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['voucher_book']    = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$data['voucher']         = $this->accounting->getVoucher($voucher_book_id, $id);
		$data['voucher_details'] = $this->accounting->getVoucherJobDetails($data['voucher']['id']);
		$data['currency']        = $this->kaabar->getField('currencies', $data['voucher']['currency_id'], 'id', 'code');
		if ($voucher_book['job_type'] != 'N/A' && $data['voucher']['job_id'] > 0) {
			$data['job']             = $this->kaabar->getRow('jobs', $data['voucher']['job_id']);
			$data['party']           = $this->kaabar->getRow('parties', $data['job']['party_id']);
			if ($data['job']['type'] == 'Import') {
				$this->load->model('import');
				$data['containers']          = $this->import->getContainerList($data['job']['id']);
				$hss_parties                 = $this->import->getHighSeas($data['job']['id']);
				$data['hss_buyer']           = array_pop($hss_parties);
				$data['import_details']      = $this->kaabar->getRow('import_details', $data['voucher']['job_id'], 'job_id');
				$data['discharge_port']      = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']) . ' - INDIA';
				$data['shipment_port']       = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
				$data['destination_port']    = $this->kaabar->getField('ports', $data['job']['dest_port_id']);
				$data['destination_country'] = $this->kaabar->getField('countries', $data['job']['dest_country_id']);
			}
			else {
				$this->load->model('export');
				$data['containers']        = $this->export->getContainerList($data['job']['id']);
				$data['invoice']           = $this->export->getJobInvoices($data['job']['id']);
				$data['sb_no']             = $this->export->getJobSBs($data['job']['id']);
				$data['product']           = $this->kaabar->getRow('products', $data['job']['product_id']);
				$data['loading_port']      = $this->kaabar->getField('indian_ports', $data['job']['loading_port_id']);
				$data['custom_port']       = $this->kaabar->getField('indian_ports', $data['job']['custom_port_id']);
				$data['discharge_port']    = $this->kaabar->getRow('ports', $data['job']['discharge_port_id']);
				$data['discharge_country'] = $this->kaabar->getRow('countries', $data['discharge_port']['country_id']);
			}
			$data['vessel']       = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);
			$data['package_type'] = $this->kaabar->getField('package_types', $data['job']['package_type_id']);
			

			if ($invoice_type == 'credit_note')
				$page = $invoice_type;
			else
				$page = strtolower($data['job']['type']) . '_invoice';
			
			$filename = strtoupper($data['company']['code'] . '_' . 
				str_replace('/', '_', $data['voucher']['id2_format']) . '_' . 
				($data['job']['type'] == 'Import' ? $data['job']['bl_no'] : $data['job']['sb_no']));
		}
		else {
			$ledger = $this->kaabar->getRow('ledgers', $data['voucher']['dr_ledger_id']);
			if ($ledger['party_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('parties', $ledger['party_id']);
			}
			else if ($ledger['agent_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('agents', $ledger['agent_id']);
			}
			else if ($ledger['staff_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('staffs', $ledger['staff_id']);
			}
			$filename = strtoupper($data['company']['code'] . '_' . str_replace('/', '_', $data['voucher']['id2_format']));

			if ($data['voucher_book']['job_type'] == 'Transportation') {
				$page = 'transportation_invoice';
				$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT J.bl_no SEPARATOR ', ') AS bl_no, 
					GROUP_CONCAT(DISTINCT J.be_no SEPARATOR ', ') AS be_no
				FROM voucher_details VT INNER JOIN trips T ON VT.trip_id = T.id
					INNER JOIN jobs J ON T.job_id = J.id
				WHERE VT.voucher_id = ?
				GROUP BY VT.voucher_id", array($data['voucher']['id']));
				$data['job']             = $query->row_array();
				$data['voucher_details'] = $this->accounting->getVoucherTransportationDetails($data['voucher']['id']);
				$data['debit_ledger']    = $this->kaabar->getRow('ledgers', $data['voucher']['dr_ledger_id']);
			}
			else {
				$page = 'simple_invoice';
			}
		}
		$data['service_taxes'] = $this->accounting->getServiceTaxes();
		$data['page_title']    = humanize($invoice_type);
		$data['letterhead']    = $letterhead;
		$data['max_items']     = 10;

		if ($pdf) {
			$html = $this->load->view("reports/$page", $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'        => FCPATH.'wkhtmltopdf',
				'margin-top'    => 0,
				'margin-bottom' => 0,
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/$page", $data);
		}
	}

	function pdf($voucher_book_id, $id = 0, $letterhead = 0, $all_bl_containers = 0) {

		$voucher_book_id = intval($voucher_book_id);
		$id = intval($id);

		$this->load->helper('numwords');

		$default_company = $this->session->userdata('default_company');
		$voucher_book    = $this->kaabar->getRow('voucher_books', $voucher_book_id);

		switch ($voucher_book['voucher_type_id']) {
			case 2: $invoice_type = 'credit_note'; break;
			case 3: $invoice_type = 'debit_note';  break;
			case 4: $invoice_type = 'invoice'; 	   break;
			default:
				echo "Only Credit Note, Debit Note and Invoice can be printed.";
				return;
		}

		$data['invoice_type']    = humanize($invoice_type);
		$data['company']         = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']            = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']           = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['voucher_book']    = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$data['voucher']         = $this->accounting->getVoucher($voucher_book_id, $id);
		$data['voucher_details'] = $this->accounting->getVoucherJobDetails($data['voucher']['id']);
		$data['currency']        = $this->kaabar->getField('currencies', $data['voucher']['currency_id'], 'id', 'code');
		if ($voucher_book['job_type'] != 'N/A' && $data['voucher']['job_id'] > 0) {
			$data['job']             = $this->kaabar->getRow('jobs', $data['voucher']['job_id']);
			$data['party']           = $this->kaabar->getRow('parties', $data['job']['party_id']);
			if ($data['job']['type'] == 'Import') {
				$this->load->model('import');
				$data['containers']          = $this->import->getContainerList($data['job']['id']);
				$hss_parties                 = $this->import->getHighSeas($data['job']['id']);
				$data['hss_buyer']           = array_pop($hss_parties);
				$data['import_details']      = $this->kaabar->getRow('import_details', $data['voucher']['job_id'], 'job_id');
				$data['discharge_port']      = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']) . ' - INDIA';
				$data['shipment_port']       = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
				$data['destination_port']    = $this->kaabar->getField('ports', $data['job']['dest_port_id']);
				$data['destination_country'] = $this->kaabar->getField('countries', $data['job']['dest_country_id']);
			}
			else {
				$this->load->model('export');
				$data['containers']        = $this->export->getContainerList($data['job']['id']);
				$data['invoice']           = $this->export->getJobInvoices($data['job']['id']);
				$data['sb_no']             = $this->export->getJobSBs($data['job']['id']);
				$data['product']           = $this->kaabar->getRow('products', $data['job']['product_id']);
				$data['loading_port']      = $this->kaabar->getField('indian_ports', $data['job']['loading_port_id']);
				$data['custom_port']       = $this->kaabar->getField('indian_ports', $data['job']['custom_port_id']);
				$data['discharge_port']    = $this->kaabar->getRow('ports', $data['job']['discharge_port_id']);
				$data['discharge_country'] = $this->kaabar->getRow('countries', $data['discharge_port']['country_id']);
			}
			$data['vessel']       = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);
			$data['package_type'] = $this->kaabar->getField('package_types', $data['job']['package_type_id']);
			

			if ($invoice_type == 'credit_note')
				$page = $invoice_type;
			else
				$page = strtolower($data['job']['type']) . '_invoice';
			
			$filename = strtoupper($data['company']['code'] . '_' . 
				str_replace('/', '_', $data['voucher']['id2_format']) . '_' . 
				($data['job']['type'] == 'Import' ? $data['job']['bl_no'] : $data['job']['sb_no']));
		}
		else {
			$ledger = $this->kaabar->getRow('ledgers', $data['voucher']['dr_ledger_id']);
			if ($ledger['party_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('parties', $ledger['party_id']);
			}
			else if ($ledger['agent_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('agents', $ledger['agent_id']);
			}
			else if ($ledger['staff_id'] > 0) {
				$data['party'] = $this->kaabar->getRow('staffs', $ledger['staff_id']);
			}
			$filename = strtoupper($data['company']['code'] . '_' . str_replace('/', '_', $data['voucher']['id2_format']));

			if ($data['voucher_book']['job_type'] == 'Transportation') {
				$page = 'transportation_invoice';
				$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT J.bl_no SEPARATOR ', ') AS bl_no, 
					GROUP_CONCAT(DISTINCT J.be_no SEPARATOR ', ') AS be_no
				FROM voucher_details VT INNER JOIN trips T ON VT.trip_id = T.id
					INNER JOIN jobs J ON T.job_id = J.id
				WHERE VT.voucher_id = ?
				GROUP BY VT.voucher_id", array($data['voucher']['id']));
				$data['job']          = $query->row_array();
				$data['voucher_details'] = $this->accounting->getVoucherTransportationDetails($data['voucher']['id']);
				$data['debit_ledger'] = $this->kaabar->getRow('ledgers', $data['voucher']['dr_ledger_id']);
			}
			else {
				$page = 'simple_invoice';
			}
		}
		$data['service_taxes'] = $this->accounting->getServiceTaxes();
		$data['page_title']    = $data['voucher_book']['print_name'];
		$data['max_items']     = 13;

		$data['border'] = 0; //'LTRB';
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		$pdf->setFontSubsetting(false);
		$pdf->SetFont('times');
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(Auth::get('username'));
		$pdf->SetTitle($filename);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetMargins(10, 10, 10, true);
		$pdf->SetAutoPageBreak(TRUE, 10);

		$this->load->model('gst_invoice');
		$this->gst_invoice->$page($pdf, $data, $letterhead);

		$pdf->Output("$filename.pdf", 'I');
	}
}
