<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Contra extends MY_Controller {
	var $_company;
	var $_table2;
	var $_table3;
	var $_table4;
	var $_folder;
	var $_share;
	var $_path;
	var $_path_url;
	
	function __construct() {
		parent::__construct();
	
		$this->_table   = 'vouchers';
		$this->_table2  = 'voucher_details';
		$this->_table3  = 'voucher_documents';
		$this->_table4  = 'voucher_delivery';
		$this->_company = $this->session->userdata('default_company');

		$this->_folder    = 'documents/vouchers/';
		$this->_share     = FCPATH . 'share/';
		$this->_path      = FCPATH . $this->_folder;
		$this->_path_url  = base_url($this->_folder);
	}
	
	function index($voucher_book_id = 0, $starting_row = 0) {
		$starting_row = intval($starting_row);$voucher_book_id = intval($voucher_book_id);
		$starting_row = intval($starting_row);
		if(! $this->accounting->isVoucherBook($voucher_book_id)) {
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
			'heading' => array('No', 'Date', 'Cheque No', 'Debit', 'Name', 'Credit', 'Name', 'Amount', 'Docs'),
			'class' => array(
				'voucher_no' => array('class' => 'Text', 'link' => 'id'),
				'date'       => 'Date',
				'cheque_no'  => 'Text',
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
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$voucher_book_id", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = $this->accounting->getBookName($voucher_book_id) . ' ' . humanize($this->_class);
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

		$data['id']  = ['id' => $id];
		$data['row'] = $row;
		
		$data['reference_name']  = $this->kaabar->getField('ledgers', $row['reference_ledger_id'], 'id', 'CONCAT(code, " ", name)');

		$data['row']['documents'] = $this->kaabar->getRows('voucher_documents', $row['id'], 'voucher_id', 'id, voucher_id');
		$data['voucher_book'] 	  = $voucher_book;
		$data['default_company']  = $this->kaabar->getRow('companies', $company_id);
		$data['default_company']['financial_year'] = $this->_company['financial_year'];
		$data['hide_messages'] 	  = true;
		$data['hide_title'] 	  = true;

		$data['page_title'] = $this->accounting->getBookName($voucher_book_id) . ' ' . humanize($this->_class);

		$data['sub_vouchers'] = $this->accounting->getSubVouchers($voucher_book_id, $row['id2']);

		$data['voucher_details'] = $this->accounting->getVoucherJobDetails($row['id']);

		$data['page']        = $this->_clspath.'voucher_show';
		$data['hide_menu']   = true;
		$data['hide_footer'] = true;
		$data['docs_url']    = $this->_docs;
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
				'category'             => 'N/A',
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
		
		$data['id']  = ['id' => $id];
		$data['row'] = $row;
		$data['row']['dr_closing'] = $this->accounting->getClosing($data['row']['dr_ledger_id']);
		$data['row']['cr_closing'] = $this->accounting->getClosing($data['row']['cr_ledger_id']);

		$data['reference_name']  = $this->kaabar->getField('ledgers', $row['reference_ledger_id'], 'id', 'CONCAT(code, " ", name)');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
		
			$data['documents']       = $this->_documents($row['id']);
			$data['voucher_book']    = $voucher_book;
			$data['default_company'] = $this->_company;
			$data['sub_vouchers']    = $this->accounting->getSubVouchers($voucher_book_id, $row['id2']);
			if ($id == 0 && $prev_id > 0 && isset($data['sub_vouchers']) && count($data['sub_vouchers']) > 0)
				$data['row']['id3'] = count($data['sub_vouchers']) + 1;
			
			$data['voucher_details'] = $this->accounting->getVoucherJobDetails($row['id']);

			$data['page_title'] = $this->accounting->getBookName($voucher_book_id) . ' ' . humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
			
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
				'category'             => $this->input->post('category'),
			);

			if ($row['audited'] == 'No') {
				if (Auth::get('username') == 'auditor') redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);

				if ($id == 0 && $prev_id == 0) {
					$d = array_merge($data, $this->accounting->getNextVoucherNo($voucher_book_id, $data['date'], $data['job_id']));
					$data = $d;
				}
				$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
				$this->_updateJournal($id);
				setSessionAlert('Changes saved successfully', 'success');
			}
			else {
				setSessionError('Voucher Audited, Can not be modified...');
			}

			redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
		}
	}
	

	function tds_vouchers($id) {
		$id = intval($id);

		if ($this->input->post('vouchers')) {
			$this->db->query("UPDATE " . $this->_table . " SET tds_payment_id = 0 WHERE tds_payment_id = ?", array($id));
			$selects = $this->input->post('select_id');
			if ($selects != null) {
				foreach ($selects as $index => $select) {
					$this->kaabar->save($this->_table, array('tds_payment_id' => $id), ['id' => $index]);
				}
				setSessionAlert('Changes saved successfully', 'success');
			}
		}

		if ($this->input->post('month'))
			$data['rows'] = $this->accounting->getPendingTDSVouchers($id, $this->input->post('month'));
		else
			$data['rows'] = $this->accounting->getTDSVouchers($id);

		$data['months']      = $this->accounting->getPendingTDSVoucherMonths();
		$data['page_title']  = 'TDS Vouchers';
		$data['hide_title']  = true;
		$data['hide_menu']   = true;
		$data['hide_footer'] = true;
		$data['page'] = $this->_clspath.'tds_vouchers';
		$this->load->view('index', $data);
	}


	function _updateJournal($id) {
		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$amounts = $this->input->post('vd_amount');
		$new_job_ids = $this->input->post('new_job_id');

		if ($amounts != null) {
			foreach ($amounts as $index => $amount) {
				if (! in_array("$index", $delete_ids)) {
					$this->kaabar->save($this->_table2, array('amount' => $amount), ['id' => $index]);
				}
			}
		}
		
		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete($this->_table2, $index);
					$this->db->update('import_details', array('cd_voucher_id' => 0), array('cd_voucher_id' => $index));
					$this->db->update('import_details', array('ppq_voucher_id' => 0), array('ppq_voucher_id' => $index));
					$this->db->update('import_details', array('sd_voucher_id' => 0), array('sd_voucher_id' => $index));
					$this->db->update('import_details', array('wh_voucher_id' => 0), array('wh_voucher_id' => $index));
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

	function _updateReceipt($id) {
		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$amounts = $this->input->post('vd_amount');
		$new_job_ids = $this->input->post('new_job_id');

		if ($amounts != null) {
			foreach ($amounts as $index => $amount) {
				if (! in_array("$index", $delete_ids)) {
					$this->kaabar->save($this->_table2, array('amount' => $amount), ['id' => $index]);
				}
			}
		}
		
		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete($this->_table2, $index);
					$this->kaabar->update('import_details', array('cd_voucher_id' => 0), array('cd_voucher_id' => $index));
					$this->kaabar->update('import_details', array('ppq_voucher_id' => 0), array('ppq_voucher_id' => $index));
					$this->kaabar->update('import_details', array('sd_voucher_id' => 0), array('sd_voucher_id' => $index));
					$this->kaabar->update('import_details', array('wh_voucher_id' => 0), array('wh_voucher_id' => $index));
				}
			}
		}

		if ($new_job_ids != null) {
			$this->load->model('import');
			$new_bill_item_ids = $this->input->post('new_bill_item_id');
			$amounts = $this->input->post('new_amount');
			foreach ($new_job_ids as $index => $job_id) {
				if ($job_id > 0 && $amounts[$index] > 0) { // $new_bill_item_ids[$index] > 0 && 
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

	function _updateInvoice($voucher_book_id, $id) {
		$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$particulars = $this->input->post('vjd_particulars');
		$new_particulars = $this->input->post('new_particulars');

		if ($particulars != null) {
			$units         = $this->input->post('vjd_units');
			$rates         = $this->input->post('vjd_rate');
			$amounts       = $this->input->post('vjd_amount');
			foreach ($particulars as $index => $particular) {
				if (! in_array("$index", $delete_ids)) {
					$data = array(
						'voucher_id'	=> $id,
						'particulars' 	=> $particular,
						'units' 		=> $units[$index],
						'rate' 			=> $rates[$index],
						'amount'		=> ($amounts[$index] > 0 ? $amounts[$index] : round($units[$index] * $rates[$index], 2))
					);
					$this->kaabar->save($this->_table2, $data, ['id' => $index]);
				}
			}
		}
		
		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete($this->_table2, $index);
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
			if ($job['type'] == 'Import')
				$vessel_ledgers = $this->accounting->getVesselLedgers($job['vessel_id']);
			else {
				$export_details = $this->kaabar->getRow('export_details', $job_id, 'job_id');
				$vessel_ledgers = $this->accounting->getVesselLedgers($export_details['accounting_vessel_id']);
			}

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
				'status'              => $this->input->post('status')
			);

			$d = array_merge($data, $this->accounting->getNextVoucherNo($voucher_book_id, $data['date'], $data['job_id']));
			$data = $d;
			$debit_note_id = $this->kaabar->save($this->_table, $data);
		}

		if ($new_particulars != null) {
			$bill_item_ids = $this->input->post('new_bill_item_id');
			$units         = $this->input->post('new_units');
			$rates         = $this->input->post('new_rate');
			$amounts       = $this->input->post('new_amount');
			foreach ($new_particulars as $index => $particular) {
				if ($bill_item_ids[$index] > 0) {
					$reimbursement = $this->kaabar->getField('ledgers', $bill_item_ids[$index], 'id', 'reimbursement');
					$data = array(
						'voucher_id'   => (isset($debit_note_id) && $reimbersment == 'Yes' ? $debit_note_id : $id),
						'bill_item_id' => $bill_item_ids[$index],
						'particulars'  => $particular,
						'units'        => $units[$index],
						'rate'         => $rates[$index],
						'amount'       => ($amounts[$index] > 0 ? $amounts[$index] : round($units[$index] * $rates[$index], 2))
					);
					$this->kaabar->save($this->_table2, $data);
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

	function _updateVoucher($id) {
		//ChromePhp::info($_POST); exit;
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
			$this->db->query("UPDATE " . $this->_table . " SET tds_payment_id = 0 WHERE tds_payment_id = ?", array($id));
			$this->kaabar->delete('voucher_vouchers', array('voucher_id' => $id));
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Voucher Deleted Successfully', 'success');

			redirect($this->_clspath.$this->_class.'/index/'.$voucher_book_id);
		}
		else {
			setSessionError('Voucher Audited. Can not be deleted...');
			redirect($this->_clspath.$this->_class.'/edit/'.$voucher_book_id.'/'.$id);
		}
	}

	function ajaxBL($vessel_ledger_id = 0) {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			$search     = strtolower($this->input->post_get('term'));
			$criteria   = ($vessel_ledger_id == 0 ? '' : "VL.id = $vessel_ledger_id AND");
			$sql = "SELECT J.id, LEFT(J.type, 1) AS type, J.id2_format, J.bl_no, P.name AS party, 
				VL.id AS vessel_ledger_id, VL.code, VL.name
			FROM (jobs J INNER JOIN parties P ON J.party_id = P.id) 
				INNER JOIN ledgers VL ON (VL.company_id = $company_id AND VL.vessel_id > 0 AND $criteria J.vessel_id = VL.vessel_id)
			WHERE (J.bl_no LIKE '%$search%' OR P.name LIKE '%$search%' OR VL.name LIKE '%$search%')
			ORDER BY bl_no
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function _documents($voucher_id) {
		
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
		setSessionAlert('Bill Attached Successfully.', 'success');

		redirect($this->agent->referrer());
	}

	function detach($voucher_id = 0, $id = 0) {
		$this->load->model('import');

		$docdir = $this->import->getDocFolder($this->_path, $voucher_id);
		$file   = $this->kaabar->getField($this->_table3, $id, 'id', 'file');
		if (file_exists($this->_path.$docdir.$file)) {
			rename($this->_path.$docdir.$file, $this->_share.$file);
		}
		$this->kaabar->delete($this->_table3, $id);
		setSessionAlert('Document Deleted Successfully.', 'success');
		
		redirect($this->agent->referrer());
	}

	function ajaxInvoiceBL() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT J.id, LEFT(J.type, 1) AS type, J.id2_foramt, J.bl_no, P.name AS party, 
				J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage
			FROM (jobs J INNER JOIN parties P ON J.party_id = P.id)
				INNER JOIN vessels V ON J.vessel_id = V.id
			WHERE J.bl_no LIKE '%$search%' OR P.name LIKE '%$search%'
			ORDER BY bl_no
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}



	function preview($voucher_book_id, $id2 = 0, $pdf = 0, $letterhead = 0) {
		$voucher_book_id = intval($voucher_book_id);
		$id2 = intval($id2);

		if(! $this->accounting->isVoucherBook($voucher_book_id)) {
			setSessionError('Invalid Voucher Book or Voucher Book does not exists');
			echo closeWindow();
			return;
		}
		
		$this->load->model('import');
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

		$data['company'] 	     = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city'] 			 = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state'] 			 = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['voucher_book']    = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$data['voucher']         = $this->accounting->getVoucher($voucher_book_id, $id2, 1);
		$data['voucher_details'] = $this->accounting->getVoucherJobDetails($data['voucher']['id']);
		if ($voucher_book['job_type'] != 'N/A' && $data['voucher']['job_id'] > 0) {
			$data['job']         	 = $this->kaabar->getRow('jobs', $data['voucher']['job_id']);
			$data['party']         	 = $this->kaabar->getRow('parties', $data['job']['party_id']);
			$data['containers']		 = $this->import->getContainerList($data['job']['id']);
			$hss_parties   			 = $this->import->getHighSeas($data['job']['id']);
			$data['hss_buyer']	     = array_pop($hss_parties);
			$data['package_type']	 = $this->kaabar->getField('package_types', $data['job']['package_type_id']);
			$data['vessel']          = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);
			$data['discharge_port']  = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']) . ' - INDIA';
			$data['shipment_port']   = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
			$data['destination_port'] = $this->kaabar->getField('ports', $data['job']['dest_port_id']);
			$data['destination_country'] = $this->kaabar->getField('countries', $data['job']['dest_country_id']);

			if ($invoice_type == 'credit_note')
				$page = $invoice_type;
			else
				$page = strtolower($data['job']['type']) . '_invoice';
			
			$filename = strtoupper($data['company']['code'] . '_' . 
				str_replace('/', '_', $data['voucher']['id2_format']) . '_' . 
				($data['job']['type'] == 'Import' ? $data['job']['bl_no'] : $data['job']['sb_no']));
		}
		else {
			$ledger 	   = $this->kaabar->getRow('ledgers', $data['voucher']['dr_ledger_id']);
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
			$page = 'simple_invoice';
		}
		$data['service_taxes'] = $this->accounting->getServiceTaxes();
		$data['page_title']    = humanize($invoice_type);
		$data['letterhead']	   = $letterhead;
		$data['max_items'] 	   = 13;

		if ($pdf) {
			$html = $this->load->view("reports/$page", $data, true);

			$pdf = new Pdf(array(
				'no-outline',
				'binary'        => FCPATH.'wkhtmltopdf',

			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/$page", $data);
		}
	}
}
