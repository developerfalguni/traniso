<?php
ini_set('memory_limit', '128M');
ini_set('max_execution_time', '0');

class TallyImport extends MY_Controller {
	function __construct() {
		parent::__construct();
				
		$this->load->library('tally');
	}
	
	function index() {

		$default_company = $this->session->userdata('default_company');

		$years = explode('_', $default_company['financial_year']);

		$from_date  = null;
		$to_date    = null;
		$company_id = null;
		
		if($this->input->post('Submit')) {
			$starting_row = 0;
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$company_id= $this->input->post('company_id');
			
			$this->_transfer($company_id, convDate($from_date), convDate($to_date));
			setSessionAlert('Data transfered to Tally...', 'success');
		}

		$this->load->helper('datefn');
		$data['javascript'] = array('/vendors/daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('/vendors/daterangepicker/daterangepicker-bs3.css');


		$data['from_date']  = $from_date ? $from_date : '01-04-'.$years[0];
		$data['to_date']    = $to_date ? $to_date : '31-03-'.$years[1];
		$data['company_id'] = $company_id ? $company_id : 1;
		$data['years']      = $years;
		$data['page_title'] = 'Transfer To Tally';
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}


	function _transfer($company_id, $from_date, $to_date) {
		if (! $this->tally->testConnection(Settings::get('tally_server'))) {
			setSessionError('Connection Error: Tally Server Not Running or Invalid IP Address.');
			redirect($this->_class);
		}

		$ledgers  = $this->_ledgers ($company_id, $from_date, $to_date);
		$this->tally->importLedgers($ledgers);
		
		/*$vouchers = $this->_vouchers($company_id, $from_date, $to_date);
		$this->tally->importVouchers($vouchers);*/
	}
	
	
	function _ledgers($company_id, $from_date, $to_date) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
				
		$ledgers[] = array(
			'name' => 'Sale of Mineral Water',
			'parent' => 'Sales Accounts',
			'opening_balance' => '0.00',
			'action' => 'Create',
		);
		
		//$sql = "SELECT B.name FROM banks B WHERE B.id IN (SELECT DISTINCT bank_id FROM receipts)";
		$sql = "SELECT B.name FROM banks B";
		$query = $this->db->query($sql);
		$banks = $query->result_array();
		foreach ($banks as $bank) {
			$ledgers[] = array(
				'name' => $bank['name'],
				'parent' => 'Bank Accounts',
				'opening_balance' => '0.00',
				'action' => 'Create'
			);
		}
		
		$sql = "SELECT P.id, PA.id AS party_address_id, P.name, PS.address, PA.mobile as contact
			FROM parties P 
			INNER JOIN party_contacts PA ON P.id = PA.party_id
			INNER JOIN party_sites PS ON P.id = PA.party_id
			ORDER BY P.id";
		$query = $this->db->query($sql);
		$parties = $query->result_array();
		foreach($parties as $party) {
			$month = substr(convDate($from_date), 0, 7);
			$previous_bal = 0;//$this->bill_model->getPreviousDuesNew($party['id'], $company_id, $month);
			$operator = '-';
			if ($previous_bal < 0) {
				$operator = '';
				$previous_bal = abs($previous_bal);
			}

			$ledgers[] = array(
				'primary_name' => $party['id'],
				'name' => $party['id'] . ' - ' . str_replace('&', '&amp;', $party['name']),
				'address' => explode("\n", str_replace('&', '&amp;', $party['address'])),
				'parent' => 'Sundry Debtors',
				'opening_balance' => "$operator$previous_bal"
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
		
		$sql = "SELECT bills.id AS bill_id, bill_no, parties.id AS party_id
		FROM bills 
		WHERE bills.date >= '$from_date' AND bills.date <= '$to_date'
		GROUP BY DATE_FORMAT(deliveries.date, '%Y-%m'), bills.id
		HAVING amount > 0
		ORDER BY bills.date, bills.id";
		$query = $this->db->query($sql);
		$bills = $query->result_array();
		
		$month = null;
		$vouchers = array();
		foreach ($bills as $bill) {
			$bill_id = str_pad($bill['bill_id'], 7, '0', STR_PAD_LEFT);
			$vouchers[] = array('id' => 1001,
				'uuid' => "$uuid-B$bill_id",
				'date' => $bill['bill_date'],
				'remarks' => 'Water Bill No.: ' . $bill['bill_no'] . ' For the Month of ' . $bill['month'],
				'voucher_type' => 'Sales',
				'to' => $bill['party_id'],
				'from' => 'Sale of Mineral Water',
				'amount' => $bill['amount'],
				'action' => 'Create'
			);
		}
			
		$sql = "SELECT receipts.id AS receipt_id,
			DATE_FORMAT(receipts.date, '%Y%m%d') AS receipt_date
		FROM receipts
		WHERE (receipts.date >= '$from_date' AND receipts.date <= '$to_date')
		ORDER BY receipts.date";
		$query = $this->db->query($sql);
		$receipts = $query->result_array();

		$month = null;
		foreach ($receipts as $receipt) {
			$amount = number_format($receipt['amount'], 2, '.', '');
			$receipt_id = str_pad($receipt['receipt_id'], 7, '0', STR_PAD_LEFT);
			$vouchers[] = array('id' => 1001,
				'uuid' => "$uuid-R$receipt_id",
				'date' => $receipt['receipt_date'],
				'remarks' => ($receipt['mode'] == 'Cash' ? null : 'Cheque No: ').$receipt['remarks'],
				'voucher_type' => 'Receipt',
				'to' => ($receipt['mode'] == 'Cash' ? 'Cash' : $receipt['bank']),
				'from' => $receipt['party_id'],
				'amount' => $receipt['amount'],
				'bank_date' => $receipt['bank_date'],
				'action' => 'Create'
			);
		}
		
		return $vouchers;
	}
}

?>