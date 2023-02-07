<?php
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '0');

class Tallyexport extends MY_Controller {
	var $_path = '';
	
	function __construct() {
		parent::__construct();
		
		// if (defined('WINDOWS'))
		// 	$this->_path = "c:\\tally\\";
		// else
		// 	$this->_path = FCPATH."tally/";
		
		// if (! file_exists($this->_path)) {
		// 	SetSessionError('Cannot Export. Directory "' . $this->_path . '" does not exists.');
		// 	redirect('main');
		// }
		
		$this->load->library('tally');
		$this->load->model('accounting');
	}
	
	function index() {
		$from_date  = null;
		$to_date    = null;
		$company_id = null;

		if ($this->input->post('from_date')) {
			$from_date  = $this->input->post('from_date');
			$to_date    = $this->input->post('to_date');
			$company_id = $this->input->post('company_id');

			$this->_transfer($company_id, $from_date, $to_date);
			setSessionAlert('Transfered successfully', 'success');
		}
		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['from_date']  = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']    = $to_date ? $to_date : date('d-m-Y');
		$data['company_id'] = $company_id ? $company_id : $default_company['id'];

		$data['docs_url']   = $this->_docs;
		$data['page_title'] = 'Transfer To Tally';
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}


	function _transfer($company_id, $from_date, $to_date) {
		if (! $this->tally->testConnection(Settings::get('tally_server'))) {
			setSessionError('Connection Error: Tally Server Not Running or Invalid IP Address.');
			redirect($this->_clspath.$this->_class);
		}

		$ledgers  = $this->_ledgers($company_id, $from_date, $to_date);
		$this->tally->exportLedgers($ledgers);
		
		$vouchers = $this->_vouchers($company_id, $from_date, $to_date);
		$this->tally->exportVouchers($vouchers);
	}
	
	
	function _ledgers($company_id, $from_date, $to_date) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
				
		$ledgers = array();
		$sql = "SELECT L.id, A.name AS parent, TRIM(CONCAT(L.name, ' - ', L.id)) AS name,
			(ROUND(L.opening_balance, 2) + ROUND(COALESCE(OB.debit, 0) + COALESCE(OB.credit, 0), 2)) AS opening_balance
		FROM ((ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id)
			INNER JOIN accounts A ON AG.account_id = A.id)
			LEFT OUTER JOIN (
				SELECT OPEN.id, MAX(OPEN.date) AS date, SUM(ROUND(OPEN.debit, 2)) AS debit, SUM(ROUND(credit, 2)) AS credit
				FROM (
					SELECT MAX(V.date) AS date, V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
						FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers L ON V.dr_ledger_id = L.id
						WHERE VB.company_id = ? AND V.date < ?
						GROUP BY V.dr_ledger_id 
					UNION
						SELECT MAX(V.date) AS date, V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
						FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers L ON V.cr_ledger_id = L.id
						WHERE VB.company_id = ? AND V.date < ? AND VB.voucher_type_id != 4
						GROUP BY V.cr_ledger_id
					UNION
						SELECT MAX(V.date) AS date, V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date < ? AND VB.voucher_type_id = 4 AND 
							BI.category = 'Bill Items'
						GROUP BY V.cr_ledger_id
					UNION
						SELECT MAX(V.date) AS date, VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date < ? AND VB.voucher_type_id = 4 AND 
							BI.category != 'Bill Items' AND 
							BI.stax_category_id > 0
						GROUP BY VJD.bill_item_id
					) AS OPEN
				GROUP BY id
			) AS OB ON L.id = OB.id
		WHERE L.company_id = ? AND L.category != 'Bill Items'
		ORDER BY L.name";
		$query = $this->db->query($sql, array(
			$company_id, convDate($from_date),
			$company_id, convDate($from_date),
			$company_id, convDate($from_date),
			$company_id, convDate($from_date),
			$company_id,
		));
		$rows = $query->result_array();
		foreach($rows as $r) {
			$ledgers[$r['id']] = array(
				'primary_name'    => htmlentities($r['name']),
				'parent'          => htmlentities($r['parent']),
				'opening_balance' => ($r['opening_balance'] < 0 ? abs($r['opening_balance']) : '-'.$r['opening_balance'])
			);
		}
		return $ledgers;
	}
	
	
	function _vouchers($company_id, $from_date, $to_date) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$uuid = $this->kaabar->getField('companies', $company_id, 'id', 'uuid');
		
		$sql = "SELECT V.id, VT.name AS voucher_type, V.id2_format, DATE_FORMAT(V.date, '%Y%m%d') AS date,
			TRIM(CONCAT(DL.name, ' - ', DL.id)) AS dr_ledger, TRIM(CONCAT(CL.name, ' - ', CL.id)) AS cr_ledger, V.amount, V.remarks
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.amount > 0
		ORDER BY V.date, VT.name, V.id";
		$query = $this->db->query($sql, array($company_id, convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();
		
		$vouchers = array();
		foreach ($rows as $r) {
			$id = str_pad($r['id'], 7, '0', STR_PAD_LEFT);
			$vouchers[] = array(
				'id'           => $r['id'],
				'action'       => 'Create',
				'uuid'         => "$uuid-V$id",
				'voucher_type' => ($r['voucher_type'] == 'Invoice' ? 'Journal' : $r['voucher_type']),
				'reference'    => htmlentities($r['id2_format']),
				'date'         => $r['date'],
				'to'           => htmlentities($r['dr_ledger']),
				'from'         => htmlentities($r['cr_ledger']),
				'amount'       => $r['amount'],
				'remarks'      => htmlentities($r['remarks']),
			);
		}

		return $vouchers;
	}
}
