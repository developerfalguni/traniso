<?php

class Report extends CI_Model {
	var $_company_id;
	var $_fy_year;

	function __construct() {
		parent::__construct();

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);
	}

	function setCompany($id) {
		$this->_company_id = $id;
	}

	function getCompanyID() {
		return $this->_company_id;
	}

	function getFinancialYear() {
		return $this->_fy_year;
	}
	
	function getLabelClass() {
		return array(
			'' 	  => '',
			'Yes' => 'label-success',
			'No'  => 'label-danger'
		);
	}

	function getCollectionStaff() {
		$data = array(
			'-1' => 'Not Assigned',
			'0' => 'All',
		);
		$sql = "SELECT DISTINCT staff_id, S.firstname 
		FROM (
			SELECT DISTINCT monitoring_id AS staff_id FROM ledgers
			UNION
			SELECT DISTINCT finalizing1_id AS staff_id FROM ledgers
			UNION 
			SELECT DISTINCT finalizing2_id AS staff_id FROM ledgers
		) A INNER JOIN staffs S ON A.staff_id = S.id
		GROUP BY A.staff_id";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$data[$r['staff_id']] = $r['firstname'];
		}
		return $data;
	}

	function getTDSDeductees() {
		$company_id = $this->_company_id;
		$data = array(0 => 'All');

		$sql = "SELECT DISTINCT T.id, T.name 
		FROM ledgers L INNER JOIN tds_classes T ON L.tds_class_id = T.id
		WHERE L.company_id = $company_id AND T.type = 'Deductee'
		ORDER BY T.name";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$data[$row['id']] = $row['name'];
		}
		return $data;
	}

	function getTDSPayments() {
		$company_id = $this->_company_id;
		$data = array(0 => 'All');

		$sql = "SELECT T.id, L.code, L.name 
		FROM ledgers L INNER JOIN tds_classes T ON L.tds_class_id = T.id
		WHERE L.company_id = $company_id AND T.type = 'Payment'
		ORDER BY L.name";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$data[$row['id']] = $row['code'] . ' - ' . $row['name'];
		}
		return $data;
	}

	function getCENVAT($from_date, $to_date, $query_only = false) {
		$sql = "SELECT CONCAT(VT.name, '/edit/', V.voucher_book_id, '/', V.id) AS url, 
			V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, PL.name AS party_ledger, 
			IF(NOT ISNULL(P.id), P.name, IF(NOT ISNULL(A.id), A.name, '')) AS party_name, 
			IF(NOT ISNULL(P.id), P.service_tax_no, IF(NOT ISNULL(A.id), A.service_tax_no, '')) AS service_tax_no,
			V.invoice_no, DATE_FORMAT(V.invoice_date, '%d-%m-%Y') AS invoice_date, V.stax_on_amount, 
			V.amount, SL.stax_category_id, SC.name AS stax_category
		FROM ((((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
			INNER JOIN ledgers SL ON V.dr_ledger_id = SL.id)
			INNER JOIN ledgers PL ON V.cr_ledger_id = PL.id)
			LEFT OUTER JOIN stax_categories SC ON SL.stax_category_id = SC.id)
			LEFT OUTER JOIN parties P ON PL.party_id = P.id)
			LEFT OUTER JOIN agents A ON PL.agent_id = A.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND SL.stax_category_id > 0 AND VB.voucher_type_id != 7
		ORDER BY SC.id, V.invoice_date";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		if ($query_only)
			return $query;
		$rows = $query->result_array();
		$data = array();
		foreach ($rows as $row) {
			$data[$row['stax_category_id']][] = $row;
		}
		return $data;
	}


	function getSTAXCategory($from_date, $to_date) {
		// $this->load->helper('datefn');
		// $months = getMonthsInBetween($from_date, $to_date);
		$result = array();

		$sql = "SELECT ST.id, ST.name, GL.id AS ledger_id
		FROM ledgers GL INNER JOIN stax_categories ST ON GL.stax_category_id = ST.id
		WHERE GL.company_id = ? AND GL.category = 'General' AND GL.stax_category_id > 0
		GROUP BY GL.id
		ORDER BY GL.name";
		$query = $this->db->query($sql, array($this->_company_id));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['id']] = array(
				'name'           => $row['name'], 
				'service_charge' => 0,
				'credit_note_sc' => 0,
				'payment'        => 0,
				'opening'        => $this->accounting->getClosingDate($row['ledger_id'], $from_date),
				'credit'         => 0,
				'credit_note'    => 0,
				'debit'          => 0
			);
		}

		// Searching in Invoices
		$sql = "SELECT ST.id, ST.name, SUM(VJD.amount) AS service_charge
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 4 AND 
			  BI.category = 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['name']           = $row['name'];
				$result[$row['id']]['service_charge'] = $row['service_charge'];
			}
			else
				$result[$row['id']] = array(
					'name'           => $row['name'], 
					'service_charge' => $row['service_charge'],
					'credit_note_sc' => 0,
					'payment'        => 0,
					'opening'        => 0,
					'credit'         => 0,
					'credit_note'    => 0,
					'debit'          => 0
				);
		}

		// Searching in Credit Notes
		$sql = "SELECT ST.id, ST.name, SUM(VJD.amount) AS service_charge
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 2 AND 
			  BI.category = 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['name']           = $row['name'];
				$result[$row['id']]['credit_note_sc'] = $row['service_charge'];
			}
			else
				$result[$row['id']] = array(
					'name'           => $row['name'], 
					'service_charge' => 0,
					'credit_note_sc' => $row['service_charge'],
					'payment'        => 0,
					'opening'        => 0,
					'credit'         => 0,
					'credit_note'    => 0,
					'debit'          => 0
				);
		}

		// Searching in Journal Vouchers
		$sql = "SELECT ST.id, ST.name, SUM(V.stax_on_amount) AS service_charge
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id)
			INNER JOIN stax_categories ST ON CL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 5 AND 
			  CL.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['name']           = $row['name'];
				$result[$row['id']]['service_charge'] = bcadd($result[$row['id']]['service_charge'], $row['service_charge']);
			}
			else
				$result[$row['id']] = array(
					'name'           => $row['name'], 
					'service_charge' => $row['service_charge'],
					'credit_note_sc' => 0,
					'payment'        => 0,
					'opening'        => 0,
					'credit'         => 0,
					'credit_note'    => 0,
					'debit'          => 0
				);
		}


		// Fetching Credit entries from Invoices
		$sql = "SELECT ST.id, ST.name, SUM(VJD.amount) AS credit
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 4 AND 
			  BI.category != 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['name']   = $row['name'];
				$result[$row['id']]['credit'] = $row['credit'];
			}
			else
				$result[$row['id']] = array(
					'name'           => $row['name'], 
					'service_charge' => 0,
					'credit_note_sc' => 0,
					'payment'        => 0,
					'opening'        => 0,
					'credit'         => $row['credit'], 
					'credit_note'    => 0,
					'debit'          => 0
				);
		}

		// Fetching Credit entries from Journal Vouchers
		$sql = "SELECT ST.id, ST.name, SUM(V.amount) AS credit
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			INNER JOIN stax_categories ST ON CL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			VB.voucher_type_id = 5 AND 
			CL.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['name']   = $row['name'];
				$result[$row['id']]['credit'] = bcadd($result[$row['id']]['credit'], $row['credit']);
			}
			else
				$result[$row['id']] = array(
					'name'           => $row['name'], 
					'service_charge' => 0,
					'credit_note_sc' => 0,
					'payment'        => 0,
					'opening'        => 0,
					'credit'         => $row['credit'], 
					'credit_note'    => 0,
					'debit'          => 0
				);
		}

		// Fetching Credit Note entries from Invoices
		$sql = "SELECT ST.id, ST.name, SUM(VJD.amount) AS credit
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 2 AND 
			  BI.category != 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['name']        = $row['name'];
				$result[$row['id']]['credit_note'] = $row['credit'];
			}
			else
				$result[$row['id']] = array(
					'name'           => $row['name'], 
					'service_charge' => 0,
					'credit_note_sc' => 0,
					'payment'        => 0,
					'opening'        => 0,
					'credit'         => 0,
					'credit_note'    => $row['credit'],
					'debit'          => 0
				);
		}


		// Fetching Debit Entries From All Vouchers Except Invoice
		$sql = "SELECT ST.id, ST.name, SUM(V.amount) AS debit
		FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN stax_categories ST ON DL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id NOT IN (4,7) AND 
			  DL.category = 'General' AND 
			  DL.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['debit'] = $row['debit'];
			}
			else {
				$result[$row['id']] = array('name' => $row['name'], 'credit' => 0, 'debit' => $row['debit']);
			}
		}

		// Fetching Entries From Payment Vouchers
		$sql = "SELECT ST.id, ST.name, SUM(V.amount) AS debit, GROUP_CONCAT(REPLACE(V.stax_payment_month, ',', ' ') SEPARATOR ' ') AS stax_payment_month
		FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN stax_categories ST ON DL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 7 AND 
			  DL.category = 'General' AND 
			  DL.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['payment'] = $row['debit'];
				//$result[$row['id']]['opening'] = bcadd($result[$row['id']]['opening'], $row['debit']);
			}
			else {
				$result[$row['id']] = array('name' => $row['name'], 'credit' => 0, 'debit' => $row['debit']);
			}
		}

		return $result;
	}

	function getSTAXSummary() {
		$this->load->helper("datefn");
		$years     = explode("_", $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';
		$months    = getMonthsInBetween($from_date, $to_date);
		$result    = array('stax' => array(), 'months' => array());
		foreach ($months as $month => $month_name) {
			$result['months'][$month] = array('month' => $month_name);
		}

		// Get Opening Balances
		$sql = "SELECT ST.id, ST.name, GL.id AS ledger_id
		FROM ledgers GL INNER JOIN stax_categories ST ON GL.stax_category_id = ST.id
		WHERE GL.company_id = ? AND GL.category = 'General' AND GL.stax_category_id > 0
		GROUP BY GL.id
		ORDER BY GL.name";
		$query = $this->db->query($sql, array($this->_company_id));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$amount = $this->accounting->getClosingDate($row['ledger_id'], $from_date);
			$result['stax'][$row['id']] = $row['name'];
			$result['months'][$years[0] . '-04'][$row['id']] = array(
				'name'   => $row['name'], 
				'debit'  => ($amount > 0 ? $amount : 0),
				'credit' => ($amount > 0 ? 0 : abs($amount)),
			);
		}

		// Fetching Credit entries from Invoices
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(VJD.amount) AS credit
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 4 AND 
			  BI.category != 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result['stax'][$row['id']] = $row['name'];
			if (isset($result['months'][$row['date']][$row['id']])) {
				$result['months'][$row['date']][$row['id']]['name'] = $row['name'];
				$result['months'][$row['date']][$row['id']]['credit'] = bcadd($result['months'][$row['date']][$row['id']]['credit'], $row['credit'], 2);
			}
			else
				$result['months'][$row['date']][$row['id']] = array(
					'name'   => $row['name'], 
					'credit' => $row['credit'],
					'debit'  => 0,
				);
		}

		// Fetching Credit entries from Journal Vouchers
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(V.amount) AS credit
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id)
			INNER JOIN stax_categories ST ON CL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 5 AND 
			  CL.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result['stax'][$row['id']] = $row['name'];
			if (isset($result['months'][$row['date']][$row['id']])) {
				$result['months'][$row['date']][$row['id']]['name'] = $row['name'];
				$result['months'][$row['date']][$row['id']]['credit'] = bcadd($result['months'][$row['date']][$row['id']]['credit'], $row['credit'], 2);
			}
			else
				$result['months'][$row['date']][$row['id']] = array(
					'name'   => $row['name'], 
					'credit' => $row['credit'],
					'debit'  => 0,
				);
		}

		// Fetching Credit Note entries from Invoices
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(VJD.amount) AS credit
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 2 AND 
			  BI.category != 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result['stax'][$row['id']] = $row['name'];
			if (isset($result['months'][$row['date']][$row['id']])) {
				$result['months'][$row['date']][$row['id']]['name'] = $row['name'];
				$result['months'][$row['date']][$row['id']]['credit'] = bcadd($result['months'][$row['date']][$row['id']]['credit'], $row['credit'], 2);
			}
			else
				$result['months'][$row['date']][$row['id']] = array(
					'name'   => $row['name'], 
					'credit' => $row['credit'],
					'debit'  => 0,
				);
		}

		// Fetching Debit Entries From All Vouchers Except Invoice
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(V.amount) AS debit
		FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN stax_categories ST ON DL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id NOT IN (4,7) AND 
			  DL.category = 'General' AND 
			  DL.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result['stax'][$row['id']] = $row['name'];
			if (isset($result['months'][$row['date']][$row['id']])) {
				$result['months'][$row['date']][$row['id']]['name'] = $row['name'];
				$result['months'][$row['date']][$row['id']]['credit'] = bcsub($result['months'][$row['date']][$row['id']]['credit'], $row['debit'], 2);
			}
			else
				$result['months'][$row['date']][$row['id']] = array(
					'name'   => $row['name'], 
					'credit' => 0,
					'debit'  => $row['debit'],
				);
		}

		// Fetching Entries From Payment Vouchers
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(V.amount) AS debit, GROUP_CONCAT(REPLACE(V.stax_payment_month, ',', ' ') SEPARATOR ' ') AS stax_payment_month
		FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN stax_categories ST ON DL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 7 AND 
			  DL.category = 'General' AND 
			  DL.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result['stax'][$row['id']] = $row['name'];
			if (isset($result['months'][$row['date']][$row['id']])) {
				$result['months'][$row['date']][$row['id']]['name']  = $row['name'];
				$result['months'][$row['date']][$row['id']]['debit'] = bcadd($result['months'][$row['date']][$row['id']]['debit'], $row['debit'], 2);
			}
			else
				$result['months'][$row['date']][$row['id']] = array(
					'name'   => $row['name'], 
					'credit' => 0,
					'debit'  => $row['debit'],
				);
		}

		return $result;
	}

	function getSTAX($from_date, $to_date) {
		$company_id = $this->_company_id;
		$data = array();

		$sql = "SELECT L.id, L.code, L.stax_category_id FROM ledgers L 
			WHERE L.company_id = ? AND L.category = 'Bill Items' AND L.stax_category_id > 0 
			ORDER BY L.code";
		$query = $this->db->query($sql, array($this->_company_id));
		$rows = $query->result_array();

		$where = "";
		$vjdsql = "SELECT VJD.voucher_id, ";
		foreach ($rows as $row) {
			$data['heading'][$row['id']] = array('stax_category_id' => $row['stax_category_id'], 'code' => $row['code']);

			$vjdsql .= 'SUM(CASE VJD.bill_item_id WHEN ' . $row['id'] . ' THEN IF(VB.voucher_type_id = 2, -VJD.amount, VJD.amount) ELSE 0 END) AS `' . $row['code'] . '`, ';
			$where .= '(BI.code = \'' . $row['code'] . '\' AND VJD.amount > 0) OR ';
		}
		$vjdsql = substr($vjdsql, 0, strlen($vjdsql) - 2);
		$where = substr($where, 0, strlen($where) - 4);
		$vjdsql.= " FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		WHERE VB.company_id = $company_id AND V.date >= '$from_date' AND V.date <= '$to_date' AND 
			  VB.voucher_type_id IN (2,4) AND 
			  BI.category = 'Bill Items' AND 
			($where)
			GROUP BY VJD.voucher_id";

		$sql = "SELECT CONCAT(VT.name, '/edit/', V.voucher_book_id, '/', V.id) AS url, 
			V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, 
			DL.name AS party_name, IF(VB.voucher_type_id = 2, -V.amount, V.amount) AS amount, VJD.*
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ($vjdsql) VJD ON V.id = VJD.voucher_id
		WHERE (VB.company_id = ? AND VB.voucher_type_id IN (2,3,4)) AND 
			(V.date >= ? AND V.date <= ?)
		ORDER BY V.date, V.id2";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date));
		$data['stax'] = $query->result_array();
		return $data;
	}



	function getReimbersment($from_date, $to_date, $query_only = false) {
		$company_id = $this->_company_id;
		$data = array();

		$sql = "SELECT L.id, L.code FROM ledgers L 
			WHERE L.company_id = ? AND L.category = 'Bill Items' AND L.stax_category_id = 0 
			ORDER BY L.code";
		$query = $this->db->query($sql, array($this->_company_id));
		$rows = $query->result_array();

		$where = "";
		$vjdsql = "SELECT VJD.voucher_id, ";
		foreach ($rows as $row) {
			$data['heading'][$row['id']] = $row['code'];

			$vjdsql .= 'SUM(CASE VJD.bill_item_id WHEN ' . $row['id'] . ' THEN VJD.amount ELSE 0 END) AS `' . $row['code'] . '`, ';
			$where .= '(BI.code = \'' . $row['code'] . '\' AND VJD.amount > 0) OR ';
		}
		$vjdsql = substr($vjdsql, 0, strlen($vjdsql) - 2);
		$where = substr($where, 0, strlen($where) - 4);
		$vjdsql.= " FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		WHERE VB.company_id = $company_id AND V.date >= '$from_date' AND V.date <= '$to_date' AND 
			VB.voucher_type_id IN (3,4) AND 
			BI.category = 'Bill Items' AND 
			($where)
		GROUP BY VJD.voucher_id";

		$sql = "SELECT V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, DL.name AS party_name, V.amount, VJD.*
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ($vjdsql) VJD ON V.id = VJD.voucher_id
		WHERE (VB.company_id = ? AND VB.voucher_type_id IN (3,4)) AND 
			(V.date >= ? AND V.date <= ?)
		ORDER BY V.date, V.id2";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date));
		
		if ($query_only)
			return $query;

		$data['reimbersment'] = $query->result_array();
		return $data;
	}


	function getCHA($from_date, $to_date, $search, $parsed_search, $query_only = false) {
		$default_company = $this->session->userdata('default_company');

		$sql = "SELECT J.type, J.cargo_type, PL.group_name, P.name AS party_name, J.bl_no,
			COALESCE(ED.vessel_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)) AS vessel_voyage, IP.name AS indian_port,
			J.packages AS pieces, PK.name AS package_unit,
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight_unit, A.name AS cha_name
		FROM jobs J INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			INNER JOIN vouchers VO ON J.id = VO.job_id
			INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id
			INNER JOIN package_types PK ON J.package_type_id = PK.id
			LEFT OUTER JOIN export_details ED ON J.id = ED.job_id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN ledgers PL ON J.party_id = PL.party_id
			LEFT OUTER JOIN agents A ON J.cha_id = A.id
		WHERE (VB.company_id = ? AND VO.date >= ? AND VO.date <= ? AND VB.voucher_type_id IN (3,4))";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		else {
			$sql .= $where . "ISNULL(A.name) OR 
		A.name LIKE '%$search%' OR
		J.type LIKE '%$search%')";
		}
		$sql .= "
		GROUP BY J.id
		ORDER BY J.type, J.cargo_type, V.name, P.name";
		$query = $this->db->query($sql, array(
			$default_company['id'], convDate($from_date), convDate($to_date)
		));

		if ($query_only)
			return $query;

		return $query->result_array();
	}

	function getBillItem($from_date, $to_date, $search, $parsed_search, $query_only = false) {
		$default_company = $this->session->userdata('default_company');

		$sql = "SELECT J.cargo_type, P.group_name, P.name AS party_name, IF(P.party_id != IMP.id, IMP.name, '') AS importer,
				PRD.name AS product, PRD.category, IP.name AS port, 
				J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, V.berth_no, DATE_FORMAT(VO.date, '%d-%m-%Y') AS bill_date,
				packages AS pieces, IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.bl_no, SUM(VJD.amount) AS amount
			FROM ((((((((jobs J INNER JOIN indian_ports IP ON J.indian_port_id = IP.id)
				INNER JOIN vessels V ON J.vessel_id = V.id)
				INNER JOIN products PRD ON J.product_id = PRD.id)
				INNER JOIN vouchers VO ON J.id = VO.job_id)
				INNER JOIN ledgers P ON VO.dr_ledger_id = P.id)
				INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id)
				INNER JOIN voucher_details VJD ON VO.id = VJD.voucher_id)
				INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
				INNER JOIN parties IMP ON J.party_id = IMP.id
			WHERE (VB.company_id = ? AND VO.date >= ? AND VO.date <= ? AND VB.voucher_type_id IN (3,4) AND J.type = 'Import')";
					$where = ' AND (';
					if (is_array($parsed_search)) {
						foreach($parsed_search as $key => $value)
							if (isset($this->_fields[$key])) {
								if ($key == 'billitem')
									$where .= $this->_fields[$key] . " IN ('" . join("', '", explode(',', $value)) . "') AND ";
								else
									$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
							}
						if (strlen($where) > 6)
							$sql .= substr($where, 0, strlen($where) - 5) . ')';
					}
					else {
						$sql .= $where . "J.type LIKE '%$search%' OR
					  P.name LIKE '%$search%' OR
					  V.name LIKE '%$search%' OR
					  J.bl_no LIKE '%$search%' OR
					  J.sb_no LIKE '%$search%' OR
					  IP.name LIKE '%$search%') ";
					}
					$sql .= "
			GROUP BY J.id
			ORDER BY V.name, V.voyage_no, J.bl_no";
		$query = $this->db->query($sql, array(
			$default_company['id'], convDate($from_date), convDate($to_date)
		));
		if ($query_only)
			return $query;
		return $query->result_array();
	}



	function getCustomDuty($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT DISTINCT J.id, J.type, J.cargo_type, P.name AS party_name, J.bl_no, J.be_no, 
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, IP.name AS indian_port, PRD.category, 
			PRD.name AS product_name, A.name AS cha_name, IT.challan_no, 
			DATE_FORMAT(STR_TO_DATE(IT.payment_date, '%Y-%m-%d'), '%d-%m-%Y') AS payment_date, 
			IC.bank_name, IC.bank_transaction_no, IT.total_duty_amount AS duty_amount
		FROM ((((((icegate_be IT INNER JOIN jobs J ON IT.job_id = J.id)
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id)
			INNER JOIN products PRD ON J.product_id = PRD.id)
			INNER JOIN parties P ON J.party_id = P.id)
			INNER JOIN vessels V ON J.vessel_id = V.id)
			LEFT OUTER JOIN icegate_challans IC ON (IC.be_no = J.be_no AND IC.be_date = DATE_FORMAT(J.be_date, '%d/%m/%Y')))
			LEFT OUTER JOIN agents A ON J.cha_id = A.id
		WHERE ((DATE_FORMAT(STR_TO_DATE(IT.payment_date, '%Y-%m-%d'), '%Y-%m-%d') >= ? AND 
			   DATE_FORMAT(STR_TO_DATE(IT.payment_date, '%Y-%m-%d'), '%Y-%m-%d') <= ?) OR
			  (DATE_FORMAT(STR_TO_DATE(IT.payment_date, '%m/%d/%Y'), '%Y-%m-%d') >= ? AND 
			   DATE_FORMAT(STR_TO_DATE(IT.payment_date, '%m/%d/%Y'), '%Y-%m-%d') <= ?))";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		else {
			$sql .= $where . "J.bl_no LIKE '%$search%' OR
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) LIKE '%$search%' OR
			IC.challan_no LIKE '%$search%' OR
			IC.bank_name LIKE '%$search%' OR
			IC.bank_transaction_no LIKE '%$search%')";
		}
		$sql .= ' ORDER BY V.id, IC.payment_datetime';
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date), convDate($from_date), convDate($to_date))
		);
		return $query->result_array();
	}

	// Accounting Reports
	function getJobDetail($job_id) {
		$sql = "SELECT P.name AS party_name, J.bl_no, J.packages, 
		PKG.code AS package_unit, J.net_weight, J.net_weight_unit,
		CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers
		FROM (jobs J INNER JOIN parties P ON J.party_id = P.id)
			LEFT OUTER JOIN package_types PKG ON J.package_type_id = PKG.id
		WHERE J.id = ?";
		$query = $this->db->query($sql, array($job_id));
		return $query->row_array();
	}

	function getLedgers($from_date, $to_date, $ledger_id, $filter_id = 0, $filter2 = false, $show_desc = false, $company_id = null) {
		$data    = array();
		$result  = array('vouchers' => array());

		$fd = convDate($from_date);
		$td = convDate($to_date);

		if (is_null($company_id)) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
		}

		$ledger = $this->kaabar->getRow('ledgers', array('company_id' => $company_id, 'id' => $ledger_id));
			


		if (!$ledger) 
			return $result;

		
		$opbal = $this->accounting->getClosingDate($ledger['id'], $fd);
		$result['vouchers'][0] = array(
			'date'    => date('d/m/y', strtotime($fd)),
			'ledger2' => 'Opening Balance',
			'amount'  => $opbal,
		);

		$where = '';
		if ($filter_id > 0) 
			$where = " WHERE (L.id = $filter_id OR L2.id = $filter_id)";

		if ($filter2 == 'Bulk')
			$where .= ($filter_id > 0 ? ' OR ' : ' WHERE ') . '(L.account_group_id = 407 OR L2.account_group_id = 407)';
		else if ($filter2 == 'Container')
			$where .= ($filter_id > 0 ? ' OR ' : ' WHERE ') . '(L.account_group_id = 408 OR L.code = "CONHA" OR L2.account_group_id = 408 OR L2.code = "CONHA")';


		$sql = "SELECT TB.id, TB.voucher_type_id, CONCAT(VT.name, '/edit/', TB.voucher_book_id, '/', TB.id) AS url, 
			TB.id3, TB.date, TB.date2, TB.id2_format, TB.job_id,
			L.name AS ledger, L2.id AS ledger2_id, L2.name AS ledger2, TB.cheque_no_date, TB.invoice_no_date, TB.debit, TB.credit, TB.narration
		FROM ((ledgers L INNER JOIN (
			SELECT CLOSE.id, CLOSE.voucher_type_id, CLOSE.voucher_book_id, CLOSE.id2, CLOSE.id3,
				CLOSE.date, CLOSE.date2, CLOSE.id2_format, CLOSE.job_id, CLOSE.cheque_no_date, CLOSE.invoice_no_date, 
				CLOSE.ledger_id, CLOSE.ledger_id2, SUM(ROUND(CLOSE.debit, 2)) AS debit, SUM(ROUND(CLOSE.credit, 2)) AS credit, CLOSE.narration
			FROM (
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					CONCAT(V.invoice_no, ' / ', DATE_FORMAT(V.invoice_date, '%d-%m-%Y')) AS invoice_no_date,
					V.dr_ledger_id AS ledger_id, V.cr_ledger_id AS ledger_id2, SUM(V.amount) AS debit, 0 AS credit, V.narration
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers L ON V.dr_ledger_id = L.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.dr_ledger_id = ? OR L.parent_ledger_id = ?) 
					GROUP BY V.id, V.dr_ledger_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					CONCAT(V.invoice_no, ' / ', DATE_FORMAT(V.invoice_date, '%d-%m-%Y')) AS invoice_no_date,
					V.cr_ledger_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-V.amount) AS credit, V.narration
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers L ON V.cr_ledger_id = L.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND 
						VB.voucher_type_id != 4
					GROUP BY V.id, V.cr_ledger_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date,
					CONCAT(V.invoice_no, ' / ', DATE_FORMAT(V.invoice_date, '%d-%m-%Y')) AS invoice_no_date, 
					V.cr_ledger_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-VJD.amount) AS credit, V.narration
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers L ON V.cr_ledger_id = L.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND 
						BI.category = 'Bill Items' AND 
						VB.voucher_type_id = 4
					GROUP BY V.id, VJD.bill_item_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					CONCAT(V.invoice_no, ' / ', DATE_FORMAT(V.invoice_date, '%d-%m-%Y')) AS invoice_no_date,
					VJD.bill_item_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-VJD.amount) AS credit, V.narration
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VJD.bill_item_id = ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 4
					GROUP BY V.id, VJD.bill_item_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					CONCAT(V.invoice_no, ' / ', DATE_FORMAT(V.invoice_date, '%d-%m-%Y')) AS invoice_no_date,
					VJD.bill_item_id AS ledger_id, V.dr_ledger_id AS ledger_id2, SUM(VJD.amount) AS debit, 0 AS credit, V.narration
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VJD.bill_item_id = ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 2
					GROUP BY V.id, VJD.bill_item_id, V.date
				) CLOSE 
			GROUP BY id
		) AS TB ON L.id = TB.ledger_id)
			INNER JOIN ledgers L2 ON TB.ledger_id2 = L2.id)
			INNER JOIN voucher_types VT ON TB.voucher_type_id = VT.id " .
		$where . "
		ORDER BY date, id";
		$query = $this->db->query($sql, array(
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'],
			$company_id, $fd, $td, $ledger['id'],
		));
		$vouchers = array();
		$jobs     = array();
		foreach ($query->result() as $r) {
			$vouchers[] = $r->id;
			if ($r->job_id > 0) 
				$jobs[$r->job_id] = array();

			if ($r->debit > 0 OR $r->credit < 0) {
				$result['vouchers'][$r->id] = array(
					'date'            => $r->date2,
					'job_id'          => $r->job_id,
					'voucher_type_id' => $r->voucher_type_id,
					'url'             => $r->url,
					'id3'             => $r->id3,
					'no'              => $r->id2_format,
					'ledger2_id'      => $r->ledger2_id,
					'ledger2'         => $r->ledger2,
					'cheque_no_date'  => $r->cheque_no_date,
					'invoice_no_date' => $r->invoice_no_date,
					'debit'           => $r->debit,
					'credit'          => $r->credit,
					'narration'       => $r->narration,
					'details'         => array(), //$this->accounting->getVoucherJobDetails($r['id']),
				);
			}
		}

		if (Auth::getCurrUID() == 3 OR Auth::getCurrUID() == 5) {
			$sql = "SELECT IC.id, DATE_FORMAT(IC.cheque_date, '%d/%m/%y') AS cheque_date, IC.cheque_no, IC.amount
			FROM issued_cheques IC
			WHERE IC.favor_ledger_id = ? AND IC.cancelled = 'No' AND IC.realization_date = '0000-00-00'
			ORDER BY IC.cheque_date";
			$query = $this->db->query($sql, array($ledger['id']));
			foreach ($query->result() as $r) {
				$result['vouchers'][-1][] = $r;
			}
		}

		if ($show_desc) {
			if (count($vouchers) > 0) {
				$sql = "SELECT DISTINCT VJD.*, 
					P.name AS party_name, J.bl_no, J.packages, PKG.code AS package_unit, 
					J.net_weight, J.net_weight_unit, CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers, BI.code AS bill_item_code, BI.name AS bill_item_name, BI.category, STAX.name AS stax_code
				FROM voucher_details VJD LEFT OUTER JOIN ledgers BI ON VJD.bill_item_id = BI.id
				 	LEFT OUTER JOIN jobs J ON VJD.job_id = J.id
					LEFT OUTER JOIN parties P ON J.party_id = P.id
					LEFT OUTER JOIN package_types PKG ON J.package_type_id = PKG.id
					LEFT OUTER JOIN stax_categories STAX ON BI.stax_category_id = STAX.id
				WHERE VJD.voucher_id IN (" . implode(',', $vouchers) . ")
				ORDER BY BI.category, BI.stax_category_id, VJD.particulars";
				$query = $this->db->query($sql);
				foreach ($query->result() as $r) {
					$result['vouchers'][$r->voucher_id]['details'][] = $r;
				}
			}

			if (count($jobs) > 0) {
				$sql = "SELECT J.id, P.name AS party_name, J.bl_no, J.packages, 
					PKG.code AS package_unit, J.net_weight, J.net_weight_unit, 
					CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers
				FROM jobs J INNER JOIN parties P ON J.party_id = P.id
					LEFT OUTER JOIN package_types PKG ON J.package_type_id = PKG.id
				WHERE J.id IN (" . implode(',', array_keys($jobs)) . ")";
				$query = $this->db->query($sql);
				foreach ($query->result() as $r) {
					$result['jobs'][$r->id] = $r;
				}
			}
		}
		return $result;
	}

	function getLedgersDaily($from_date, $to_date, $ledger_id, $filter_id) {
		$data    = array();
		$result  = array();

		$fd = convDate($from_date);
		$td = convDate($to_date);

		$default_company = $this->session->userdata('default_company');
		$company_id = $default_company['id'];

		$ledger = $this->kaabar->getRow('ledgers', array('company_id' => $company_id, 'id' => $ledger_id));
		if (!$ledger) 
			return $result;

		$opbal = $this->accounting->getClosingDate($ledger['id'], $fd);
		$result[0] = array(
			'date'	         => date('d/m/y', strtotime($fd)),
			'ledger2'        => 'Opening Balance',
			'amount'         => $opbal,
		);

		$where = '';
		if ($filter_id > 0)
			$where = " WHERE L2.id = $filter_id ";

		$sql = "SELECT DATE_FORMAT(date, '%d-%b-%Y') AS month, SUM(TB.debit) AS debit, SUM(TB.credit) AS credit
		FROM (ledgers L INNER JOIN (
			SELECT CLOSE.id, CLOSE.voucher_type_id, CLOSE.voucher_book_id, CLOSE.id2, CLOSE.id3,
				CLOSE.date, CLOSE.date2, CLOSE.id2_format, CLOSE.job_id, CLOSE.cheque_no_date, 
				CLOSE.ledger_id, CLOSE.ledger_id2, SUM(ROUND(CLOSE.debit, 2)) AS debit, SUM(ROUND(CLOSE.credit, 2)) AS credit, CLOSE.narration
			FROM (
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					V.dr_ledger_id AS ledger_id, V.cr_ledger_id AS ledger_id2, SUM(V.amount) AS debit, 0 AS credit, V.narration
					FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers L ON V.dr_ledger_id = L.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.dr_ledger_id = ? OR L.parent_ledger_id = ?)
					GROUP BY V.id, V.dr_ledger_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					V.cr_ledger_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-V.amount) AS credit, V.narration
					FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers L ON V.cr_ledger_id = L.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND 
						VB.voucher_type_id != 4
					GROUP BY V.id, V.cr_ledger_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					V.cr_ledger_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-VJD.amount) AS credit, V.narration
					FROM (((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers L ON V.cr_ledger_id = L.id)
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND 
						BI.category = 'Bill Items' AND 
						VB.voucher_type_id = 4
					GROUP BY V.id, VJD.bill_item_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					VJD.bill_item_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-VJD.amount) AS credit, V.narration
					FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VJD.bill_item_id = ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 4
					GROUP BY V.id, VJD.bill_item_id, V.date
				) CLOSE 
			GROUP BY id
		) AS TB ON L.id = TB.ledger_id)
			INNER JOIN ledgers L2 ON TB.ledger_id2 = L2.id " .
		$where . "
		GROUP BY DATE_FORMAT(date, '%d-%m-%Y')
		ORDER BY date";
		$query = $this->db->query($sql, array(
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id']
		));
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result[] = array(
				'month'	      => $r['month'],
				'debit'       => $r['debit'],
				'credit'      => $r['credit']
			);
		}
		return $result;
	}

	function getLedgersMonthly($from_date, $to_date, $ledger_id, $filter_id) {
		$data    = array();
		$result  = array();

		$fd = convDate($from_date);
		$td = convDate($to_date);

		$default_company = $this->session->userdata('default_company');
		$company_id = $default_company['id'];

		$ledger = $this->kaabar->getRow('ledgers', array('company_id' => $company_id, 'id' => $ledger_id));
		if (!$ledger) 
			return $result;

		$opbal = $this->accounting->getClosingDate($ledger['id'], $fd);
		$result[0] = array(
			'date'	         => date('d/m/y', strtotime($fd)),
			'ledger2'        => 'Opening Balance',
			'amount'         => $opbal,
		);

		$where = '';
		if ($filter_id > 0)
			$where = " WHERE L2.id = $filter_id ";

		$sql = "SELECT DATE_FORMAT(date, '%b-%Y') AS month, SUM(TB.debit) AS debit, SUM(TB.credit) AS credit
		FROM (ledgers L INNER JOIN (
			SELECT CLOSE.id, CLOSE.voucher_type_id, CLOSE.voucher_book_id, CLOSE.id2, CLOSE.id3,
				CLOSE.date, CLOSE.date2, CLOSE.id2_format, CLOSE.job_id, CLOSE.cheque_no_date, 
				CLOSE.ledger_id, CLOSE.ledger_id2, SUM(ROUND(CLOSE.debit, 2)) AS debit, SUM(ROUND(CLOSE.credit, 2)) AS credit, CLOSE.narration
			FROM (
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					V.dr_ledger_id AS ledger_id, V.cr_ledger_id AS ledger_id2, SUM(V.amount) AS debit, 0 AS credit, V.narration
					FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers L ON V.dr_ledger_id = L.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.dr_ledger_id = ? OR L.parent_ledger_id = ?)
					GROUP BY V.id, V.dr_ledger_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					V.cr_ledger_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-V.amount) AS credit, V.narration
					FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers L ON V.cr_ledger_id = L.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND 
						VB.voucher_type_id != 4
					GROUP BY V.id, V.cr_ledger_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					V.cr_ledger_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-VJD.amount) AS credit, V.narration
					FROM (((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers L ON V.cr_ledger_id = L.id)
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND 
						BI.category = 'Bill Items' AND 
						VB.voucher_type_id = 4
					GROUP BY V.id, VJD.bill_item_id, V.date
				UNION ALL
					SELECT V.id, VB.voucher_type_id, V.voucher_book_id, V.id2, V.id3, V.date, DATE_FORMAT(V.date, '%d/%m/%y') AS date2, V.id2_format, V.job_id, 
					CONCAT(V.cheque_no, ' / ', DATE_FORMAT(V.cheque_date, '%d-%m-%Y')) AS cheque_no_date, 
					VJD.bill_item_id AS ledger_id, V.dr_ledger_id AS ledger_id2, 0 AS debit, SUM(-VJD.amount) AS credit, V.narration
					FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VJD.bill_item_id = ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 4
					GROUP BY V.id, VJD.bill_item_id, V.date
				) CLOSE 
			GROUP BY id
		) AS TB ON L.id = TB.ledger_id)
			INNER JOIN ledgers L2 ON TB.ledger_id2 = L2.id " .
		$where . "
		GROUP BY DATE_FORMAT(date, '%m-%Y')
		ORDER BY date";
		$query = $this->db->query($sql, array(
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id'], $ledger['id'],
			$company_id, $fd, $td, $ledger['id']
		));
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result[] = array(
				'month'	      => $r['month'],
				'debit'       => $r['debit'],
				'credit'      => $r['credit']
			);
		}
		return $result;
	}

	function getGroupLedgers($upto, $group_id, $company_id, $companies, $group, $staff_id = 0) {
		$data   = array();
		$result = array('companies' => array(), 'ledgers' => array());
		$years  = explode('_', $this->kaabar->getFinancialYear($upto));

		$collection = '';
		if ($staff_id > 0)
			$collection = "(L.monitoring_id = $staff_id OR L.finalizing1_id = $staff_id OR L.finalizing2_id = $staff_id) AND ";
		else if ($staff_id < 0)
			$collection = "(L.monitoring_id = 0 AND L.finalizing1_id = 0 AND L.finalizing2_id = 0) AND ";

		$cmps 	= array_merge(array($company_id), $companies);
		foreach ($cmps as $company_id) {
			$sql = "SELECT L.id, A.default_dr_cr, L.category, L.group_name, L.code, L.name, 
				CM.firstname AS collection_m, CF1.firstname AS collection_f1, CF2.firstname AS collection_f2,
				(ROUND(L.opening_balance, 2) + ROUND(COALESCE(TB.debit, 0) + COALESCE(TB.credit, 0), 2)) AS closing,
				0 AS only_opening,
				DATEDIFF(NOW(), TB.date) AS days
			FROM ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id
				INNER JOIN accounts A ON AG.account_id = A.id
				LEFT OUTER JOIN staffs CM ON L.monitoring_id = CM.id
				LEFT OUTER JOIN staffs CF1 ON L.finalizing1_id = CF1.id
				LEFT OUTER JOIN staffs CF2 ON L.finalizing2_id = CF2.id
				LEFT OUTER JOIN (
				SELECT CLOSE.id, MAX(CLOSE.date) AS date, SUM(ROUND(CLOSE.debit, 2)) AS debit, SUM(ROUND(credit, 2)) AS credit
				FROM (
					SELECT MAX(V.date) AS date, V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
						FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers L ON V.dr_ledger_id = L.id
						WHERE VB.company_id = ? AND V.date <= ? AND L.account_group_id = ?
						GROUP BY V.dr_ledger_id 
					UNION ALL
						SELECT MAX(V.date) AS date, V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
						FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers L ON V.cr_ledger_id = L.id
						WHERE VB.company_id = ? AND V.date <= ? AND L.account_group_id = ? AND VB.voucher_type_id != 4
						GROUP BY V.cr_ledger_id
					UNION ALL
						SELECT MAX(V.date) AS date, V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
							BI.category = 'Bill Items'
						GROUP BY V.cr_ledger_id
					UNION ALL
						SELECT MAX(V.date) AS date, VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
							BI.category != 'Bill Items' AND 
							BI.stax_category_id > 0
						GROUP BY VJD.bill_item_id
					) AS CLOSE
				GROUP BY id
			) AS TB ON L.id = TB.id
			WHERE (L.company_id = ? AND L.account_group_id = ?) AND 
				(L.group_name LIKE ? OR L.code LIKE ? OR L.name LIKE ?) AND 
				$collection
				(L.opening_balance + COALESCE(TB.debit, 0) + COALESCE(TB.credit, 0)) != 0
			ORDER BY L.group_name, L.name";
			$query = $this->db->query($sql, array(
				$company_id, $upto, $group_id,
				$company_id, $upto, $group_id,
				$company_id, $upto, 
				$company_id, $upto, 
				$company_id, $group_id, "%$group%", "%$group%", "%$group%"
			));
			$rows = $query->result_array();
			$result['companies'][$company_id] = word_limiter($this->kaabar->getField('companies', $company_id, 'id', 'name'), 2);
			$ledgers = array();
			foreach ($rows as $r) {
				$ledgers[] = $r['id'];

				$closing = $r['closing'];
				if ($r['default_dr_cr'] == 'Cr' && $closing < 0)
					$closing = abs($r['closing']);
				else if ($r['default_dr_cr'] == 'Cr' && $closing > 0)
					$closing = -$r['closing'];

				$continue = false;
				foreach($result['ledgers'] as $group_name => $codes) {
					foreach ($codes as $code => $l) {
						if ($code == $r['code']) {
							$result['ledgers'][$group_name][$r['code']]['category']      = $r['category'];
							$result['ledgers'][$group_name][$r['code']]['name']          = $r['name'];
							$result['ledgers'][$group_name][$r['code']]['collection_m']  = $r['collection_m'];
							$result['ledgers'][$group_name][$r['code']]['collection_f1'] = $r['collection_f1'];
							$result['ledgers'][$group_name][$r['code']]['collection_f2'] = $r['collection_f2'];
							$result['ledgers'][$group_name][$r['code']]['closing'][$company_id] = array(
								'id'           => $r['id'], 
								'closing'      => $closing,
								'only_opening' => 0,
								'days'         => $r['days']
							);
							$continue = true;
							break;
						}
					}
				}
				if ($continue) continue;

				if (! isset($result['ledgers'][$r['group_name']]))
					$result['ledgers'][$r['group_name']][$r['code']]['code'] = $r['code'];
				if (! isset($result['ledgers'][$r['group_name']][$r['code']]['id']))
					$result['ledgers'][$r['group_name']][$r['code']]['id'] = $r['id'];
				$result['ledgers'][$r['group_name']][$r['code']]['category']      = $r['category'];
				$result['ledgers'][$r['group_name']][$r['code']]['name']          = $r['name'];
				$result['ledgers'][$r['group_name']][$r['code']]['collection_m']  = $r['collection_m'];
				$result['ledgers'][$r['group_name']][$r['code']]['collection_f1'] = $r['collection_f1'];
				$result['ledgers'][$r['group_name']][$r['code']]['collection_f2'] = $r['collection_f2'];
				$result['ledgers'][$r['group_name']][$r['code']]['closing'][$company_id] = array(
					'id'           => $r['id'], 
					'closing'      => $closing,
					'only_opening' => 0,
					'days'         => $r['days']
				);
			}

			if (count($ledgers) > 0) {
				$ledgers = implode(',', $ledgers);
				$sql = "SELECT L.id, L.code, L.group_name, SUM(CLOSE.amount) AS closing
				FROM (
						SELECT L.id, ROUND(opening_balance, 2) AS amount
						FROM ledgers L 
						WHERE L.company_id = ? AND (L.id IN ($ledgers) OR L.parent_ledger_id IN ($ledgers))
					UNION ALL
						SELECT L.id, SUM(V.amount) AS amount
						FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
							INNER JOIN ledgers L ON V.dr_ledger_id = L.id
						WHERE VB.company_id = ? AND (V.dr_ledger_id IN ($ledgers) OR L.parent_ledger_id IN ($ledgers)) AND V.date < ?
						GROUP BY V.dr_ledger_id
					UNION ALL
						SELECT L.id, SUM(-V.amount) AS amount
						FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
							INNER JOIN ledgers L ON V.cr_ledger_id = L.id
						WHERE VB.company_id = ? AND (V.cr_ledger_id IN ($ledgers) OR L.parent_ledger_id IN ($ledgers)) AND V.date < ? AND 
							VB.voucher_type_id != 4
						GROUP BY V.cr_ledger_id
					UNION ALL
						SELECT L.id, SUM(-VJD.amount) AS amount
						FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
							INNER JOIN ledgers L ON V.cr_ledger_id = L.id
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND (V.cr_ledger_id IN ($ledgers) OR L.parent_ledger_id IN ($ledgers)) AND V.date < ? AND 
							BI.category = 'Bill Items' AND 
							VB.voucher_type_id = 4
						GROUP BY V.cr_ledger_id
					UNION ALL
						SELECT BI.id, SUM(-VJD.amount) AS amount
						FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND VJD.bill_item_id IN ($ledgers) AND V.date < ? AND 
							BI.category != 'Bill Items' AND 
							BI.stax_category_id > 0 AND 
							VB.voucher_type_id = 4
						GROUP BY VJD.bill_item_id
					) AS CLOSE INNER JOIN ledgers L ON CLOSE.id = L.id
				GROUP BY L.id";
				$query = $this->db->query($sql, array(
					$company_id,
					$company_id, $years[0]. '-04-01',
					$company_id, $years[0]. '-04-01',
					$company_id, $years[0]. '-04-01',
					$company_id, $years[0]. '-04-01',
				));
				$openings = $query->result_array();
				foreach ($openings as $o) {
					$result['ledgers'][$o['group_name']][$o['code']]['closing'][$company_id]['only_opening'] = ($result['ledgers'][$o['group_name']][$o['code']]['closing'][$company_id]['closing'] == $o['closing'] ? 1 : 0);
				}
			}
		}
		return $result;
	}


	// Financial Reports
	function getTrialBalance($upto, $search, $ledger_ids = NULL) {
		$fy = explode("_", $this->kaabar->getFinancialYear($upto));
		$sql = "SELECT IF(L.parent_ledger_id > 0, LP.id, L.id) AS id, AG.account_id, AG.name AS group_name, 
			IF(L.parent_ledger_id > 0, LP.code, L.code) AS code, 
			IF(L.parent_ledger_id > 0, LP.name, L.name) AS name, 
			L.party_id, L.vessel_id, L.agent_id, L.staff_id,
			SUM(IF(A.affects_gross_profit, 0, (ROUND(L.opening_balance, 2) + ROUND(COALESCE(OB.debit, 0) + COALESCE(OB.credit, 0), 2)))) AS opening,
			SUM(COALESCE(TB.debit, 0)) AS debit, SUM(COALESCE(ABS(TB.credit), 0)) AS credit, 
			(
				SUM(IF(A.affects_gross_profit, 0, (ROUND(L.opening_balance, 2) + ROUND(COALESCE(OB.debit, 0) + COALESCE(OB.credit, 0), 2)))) + 
				SUM(COALESCE(TB.debit, 0)) + SUM(COALESCE(TB.credit, 0))
			) AS closing
		FROM ((ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id)
			INNER JOIN accounts A ON AG.account_id = A.id
			LEFT OUTER JOIN ledgers LP ON L.parent_ledger_id = LP.id)
			LEFT OUTER JOIN (
				SELECT OPEN.id, SUM(ROUND(OPEN.debit, 2)) AS debit, SUM(ROUND(credit, 2)) AS credit
				FROM (
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
						FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers L ON V.dr_ledger_id = L.id
						WHERE VB.company_id = ? AND V.date < ?
						GROUP BY V.dr_ledger_id 
					UNION
						SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
						FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers L ON V.cr_ledger_id = L.id
						WHERE VB.company_id = ? AND V.date < ? AND VB.voucher_type_id != 4
						GROUP BY V.cr_ledger_id
					UNION
						SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date < ? AND VB.voucher_type_id = 4 AND 
							BI.category = 'Bill Items'
						GROUP BY V.cr_ledger_id
					UNION
						SELECT VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
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
			LEFT OUTER JOIN (
				SELECT CLOSE.id, ROUND(SUM(CLOSE.debit), 2) AS debit, ROUND(SUM(credit), 2) AS credit
				FROM (
						SELECT V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
						FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ?
						GROUP BY V.dr_ledger_id 
					UNION
						SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
						FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id != 4
						GROUP BY V.cr_ledger_id
					UNION
						SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
							BI.category = 'Bill Items'
						GROUP BY V.cr_ledger_id
					UNION
						SELECT VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
							BI.category != 'Bill Items' AND 
							BI.stax_category_id > 0
						GROUP BY VJD.bill_item_id
					) AS CLOSE
				GROUP BY id
			) AS TB ON L.id = TB.id
		WHERE L.company_id = ? AND " . 
			($search === NULL ? 'L.id IN (' . implode(',', $ledger_ids) . ')' : 
			" (AG.name LIKE '%$search%' OR
			L.code LIKE '%$search%' OR LP.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR LP.name LIKE '%$search%') ") . 
		"GROUP BY IF(L.parent_ledger_id > 0, LP.id, L.id)
		HAVING (opening != 0 OR debit != 0 OR credit != 0)
		ORDER BY AG.id, L.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, $fy[0]."-04-01",
			$this->_company_id, $fy[0]."-04-01",
			$this->_company_id, $fy[0]."-04-01",
			$this->_company_id, $fy[0]."-04-01",
			$this->_company_id, $fy[0]."-04-01", convDate($upto),
			$this->_company_id, $fy[0]."-04-01", convDate($upto),
			$this->_company_id, $fy[0]."-04-01", convDate($upto),
			$this->_company_id, $fy[0]."-04-01", convDate($upto),
			$this->_company_id
		));
		$rows = $query->result_array();
		$result = array();
		foreach ($rows as $r) {
			$result[$r['group_name']][] = array(
				'id'         => $r['id'],
				'account_id' => $r['account_id'],
				'code'       => $r['code'],
				'name'       => $r['name'],
				'party_id'   => $r['party_id'],
				'vessel_id'  => $r['vessel_id'],
				'agent_id'   => $r['agent_id'],
				'staff_id'   => $r['staff_id'],
				'opening'    => $r['opening'],
				'debit'      => $r['debit'],
				'credit'     => $r['credit'],
				'closing'    => $r['closing']
			);
		}
		return $result;
	}

	function getTrips($from_date, $to_date, $search, $parsed_search) {
		$result = array();
		$sql = "SELECT T.*, FL.name AS from_location, TL.name AS to_location, PLE.name AS party_name, TLE.name AS transporter_name, 
			IF(ISNULL(V.id), 0, 1) AS self, L.id AS ledger_id, DATE_FORMAT(T.date, '%d-%m-%Y') AS date, 
			TA.self_adv, TA.party_adv, PA.amount AS pump_adv,
			ROUND(COALESCE(TA.amount, 0) + COALESCE(PA.amount, 0), 2) AS allowance, 
			ROUND(T.party_rate - ROUND(COALESCE(TA.amount, 0) + COALESCE(PA.amount, 0), 2), 2) AS balance,
			VO.id2_format AS bill_no, DATE_FORMAT(VO.date, '%d-%m-%Y') AS bill_date
		FROM trips T 
			INNER JOIN locations FL ON T.from_location_id = FL.id
			INNER JOIN locations TL ON T.to_location_id = TL.id
			INNER JOIN vehicles V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0 AND ! ISNULL(V.id))
			INNER JOIN ledgers L ON V.id = L.vehicle_id
			LEFT OUTER JOIN ledgers PLE ON T.party_ledger_id = PLE.id
			LEFT OUTER JOIN ledgers TLE ON T.transporter_ledger_id = TLE.id
			LEFT OUTER JOIN voucher_details VD ON T.id = VD.trip_id
			LEFT OUTER JOIN vouchers VO ON VD.voucher_id = VO.id
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
		WHERE (T.date >= ? AND T.date <= ?)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= " 
		GROUP BY T.id
		ORDER BY T.registration_no, T.date DESC";
		$query = $this->db->query($sql, array(
			$this->_company_id, $this->_company_id,
			convDate($from_date), convDate($to_date)
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['ledger_id']]['trips'][] = $row;
		}
		$ledger_ids = implode(',', array_keys($result));
		if (strlen($ledger_ids) == 0)
			$ledger_ids = '0';

		$sql = "SELECT A.deemed_positive, AG.id AS group_id, AG.name AS group_name, L.id AS ledger_id, L.name,
			SUM(L.opening_balance) AS opening, 
			SUM(COALESCE(TB.debit, 0)) AS debit, SUM(COALESCE(ABS(TB.credit), 0)) AS credit, 
			(SUM(L.opening_balance) + SUM(COALESCE(TB.debit, 0)) + SUM(COALESCE(TB.credit, 0))) AS closing
		FROM ledgers L
			INNER JOIN account_groups AG ON L.account_group_id = AG.id
			INNER JOIN accounts A ON AG.account_id = A.id
			LEFT OUTER JOIN ledgers LP ON L.parent_ledger_id = LP.id
			LEFT OUTER JOIN (
			SELECT CLOSE.id, ROUND(SUM(CLOSE.debit), 2) AS debit, ROUND(SUM(credit), 2) AS credit
			FROM (
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.dr_ledger_id IN ($ledger_ids)
					GROUP BY V.dr_ledger_id 
				UNION ALL
					SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id != 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
						BI.category = 'Bill Items'
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0
					GROUP BY VJD.bill_item_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, SUM(VJD.amount) AS debit, 0 AS credit
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 2
					GROUP BY VJD.bill_item_id
				) AS CLOSE
			GROUP BY id
		) AS TB ON L.id = TB.id
		WHERE L.company_id = ? AND A.affects_gross_profit = 1 AND L.id IN ($ledger_ids)
		GROUP BY IF(L.parent_ledger_id > 0, LP.id, L.id)
		HAVING (opening != 0 OR debit != 0 OR credit != 0)
		ORDER BY AG.id, L.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['ledger_id']]['closing'] = $row['closing'];
		}
		return $result;
	}

}
