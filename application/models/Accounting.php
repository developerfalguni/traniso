<?php

class Accounting extends CI_Model {
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
			'' 		=> '',
			'Yes' 	=> 'label-success',
			'No' 	=> 'label-danger',

			'Bulk' 		=> 'label-info',
			'Container'	=> 'label-warning',

			'Check' 	=> 'label-info',
			'Checked' 	=> 'label-info',
			'Verify' 	=> 'label-warning',
			'Verified' 	=> 'label-warning',
			'Authorise'	=> 'label-success',
			'Authorised'=> 'label-success'
		);
	}

	function getMember() {
		return array(
			'Yes' => 'Yes', 
			'No'  => 'No', 
		);
	}

	function getCategories() {
		return array(
			'General' => 'General', 
			'Bank'    => 'Bank', 
			'Party'   => 'Party', 
			'Agent'   => 'Agent', 
			'Vessel'  => 'Vessel',
			'Staff'   => 'Staff',
			'Vehicle' => 'Vehicle',
		);
	}

	function getDrCr($amount) {
		return ($amount >= 0 ? 'Dr' : 'Cr');
	}

	function getToBy($amount) {
		return ($amount >= 0 ? 'To' : 'By');
	}

	function countBranches($search = '') {
		$sql = "SELECT COUNT(B.id) AS numrows
		FROM branches B INNER JOIN companies C ON B.company_id = C.id
		WHERE B.id LIKE '%$search%' OR
			B.name LIKE '%$search%' OR
			C.id LIKE '%$search%' OR
			C.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getBranches($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT B.id, B.code, B.name, C.name as company, B.address, B.contact, B.email
		FROM branches B INNER JOIN companies C ON B.company_id = C.id
		WHERE B.id LIKE '%$search%' OR
			B.name LIKE '%$search%' OR
			C.id LIKE '%$search%' OR
			C.name LIKE '%$search%'
		ORDER BY B.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countAccountGroups($search = '') {
		$sql = "SELECT COUNT(AG.id) AS numrows
		FROM account_groups AG
		WHERE AG.id LIKE '%$search%' OR 
			AG.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getAccountGroups($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT AG.id, AG.name, A.name AS accounting_head
		FROM account_groups AG INNER JOIN accounts A ON AG.account_id = A.id
		WHERE AG.id LIKE '%$search%' OR 
			AG.name LIKE '%$search%'
		ORDER BY AG.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// $account_group_id as Array 
	function getAllLedgers($account_group_id = null) {
		$data = array();
		
		$sql = "SELECT L.id, CONCAT(L.code, ' - ', L.name) AS name
		FROM ledgers L
		WHERE L.company_id = ? " . 
		($account_group_id == null ? '' : ' AND L.account_group_id IN (' . implode(',', $account_group_id) . ') ') .
		" ORDER BY L.name";
		$query = $this->db->query($sql, array($this->_company_id));
		$rows = $query->result_array();
		foreach ($rows as $row)
			$data[$row['id']] = $row['name'];
		return $data;
	}

	function isDuplicateCode($code, $id) {
		$result = true;
		
		$sql = "SELECT code FROM ledgers L WHERE company_id = ? AND id != ? AND code = ?";
		$query = $this->db->query($sql, array($this->_company_id, $id, $code));
		if ($query->num_rows() > 0)
			$result = false;
		return $result;
	}

	function isLedger($id) {
		$result = false;
		
		$sql = "SELECT id FROM ledgers L WHERE id = $id AND company_id = ?";
		$query = $this->db->query($sql, array($this->_company_id));
		if ($query->num_rows() > 0)
			$result = true;
		return $result;
	}

	function getServiceTaxes() {
		$data = array();
		$sql = "SELECT * FROM ledgers L WHERE company_id = " . $this->_company_id . " AND category = 'General' AND stax_category_id > 0";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$data[$row['id']] = $row;
		}
		return $data;
	}

	function updateVoucherServiceTax($voucher_id) {
		$company_id       = $this->_company_id;
		$stax_rates       = array();
		$transport_charge = 0;

		$voucher = $this->kaabar->getRow('vouchers', $voucher_id, 'id', false);

		// Get Service Tax Rates based on voucher date
		$sql = "SELECT SR.stax_category_id, SR.stax, SR.edu_cess, SR.hedu_cess, L.id, L.code, L.name, 
			SC.name AS stax_category, 'SERVICE TAX ON ' AS particulars
		FROM (stax_rates SR INNER JOIN (
				SELECT stax_category_id, MAX(wef_date) AS wef_date FROM stax_rates
				WHERE wef_date <= ?
				GROUP BY stax_category_id
			) MSR ON (SR.stax_category_id = MSR.stax_category_id AND SR.wef_date = MSR.wef_date))
			INNER JOIN ledgers L ON (L.company_id = ? AND L.category = 'General' AND SR.stax_category_id = L.stax_category_id)
			INNER JOIN stax_categories SC ON L.stax_category_id = SC.id";
		$query = $this->db->query($sql, array($voucher['date'], $company_id));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$stax_rates[$row['stax_category_id']] = $row;
		}

		// Get Amount on which Service Tax is applicable.
		$sql = "SELECT BI.code, GROUP_CONCAT(DISTINCT VJD.particulars) AS particulars, BI.stax_category_id, SUM(VJD.amount) AS amount
		FROM voucher_details VJD INNER JOIN ledgers BI ON (
				BI.company_id = ? AND 
				BI.category = 'Bill Items' AND 
				BI.reimbursement = 'No' AND
				VJD.bill_item_id = BI.id
			)
		WHERE VJD.voucher_id = ?
		GROUP BY BI.stax_category_id";
		$query = $this->db->query($sql, array($company_id, $voucher_id));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($stax_rates[$row['stax_category_id']])) {
				$stax_rates[$row['stax_category_id']]['amount'] = $row['amount'];
				$stax_rates[$row['stax_category_id']]['particulars'] .= $row['particulars'];
			}
			else if ($row['code'] == 'TR')
				$transport_charge = bcadd($transport_charge, $row['amount'], 2);
		}

		// If Service Tax is applicable, update if already added, else add.
		foreach ($stax_rates as $stax_category_id => $stc) {
			if (isset($stc['amount'])) {
				$sql   = "SELECT * FROM voucher_details VJD WHERE VJD.voucher_id = ? AND VJD.bill_item_id = ?";
				$query = $this->db->query($sql, array($voucher_id, $stc['id']));
				$row   = $query->row_array();

				$stax      = round($stc['amount'] * $stc['stax'] / 100);
				$edu_cess  = round($stax * $stc['edu_cess'] / 100);
				$hedu_cess = round($stax * $stc['hedu_cess'] / 100);
				$tax_calc  = '<span class="box_label">Service Charge: </span>' . inr_format($stc['amount']) . '<br />
					<span class="box_label">S.Tax (' . $stc['stax'] . '%): </span>' . inr_format($stax) . "<br />" . 
					($stc['edu_cess']  > 0 ? '<span class="box_label">E. Cess (' . $stc['edu_cess'] . '%): </span>' . inr_format($edu_cess) . "<br />" : '') . 
					($stc['hedu_cess'] > 0 ? '<span class="box_label">HE. Cess (' . $stc['hedu_cess'] . '%): </span>' . inr_format($hedu_cess) : '');
				$stc['particulars'] .= ' UNDER (' . $stc['stax_category'] . ')';
				if ($row) {
					if (strlen(trim($row['particulars'])) == 0)
						$row['particulars'] = $stc['particulars'];
					$row['tax_calculation'] = $tax_calc;
					$row['amount']       = ($stax+$edu_cess+$hedu_cess);
				}
				else {
					$row = array(
						'id'              => 0,
						'voucher_id'      => $voucher_id,
						'bill_item_id'    => $stc['id'],
						'particulars'     => $stc['particulars'],
						'amount'          => ($stax+$edu_cess+$hedu_cess),
						'tax_calculation' => $tax_calc
					);
				}

				$this->kaabar->save('voucher_details', $row, array('id' => $row['id']));
			}
		}
		$this->updateVoucherTotal($voucher_id, $transport_charge);
	}

	function updateVoucherTotal($voucher_id, $transport_charge = 0) {
		$remarks = '';
		if ($transport_charge > 0) {
			$stax = Settings::get('service_tax');
			$stax = bcadd($stax, (Settings::get('service_tax') * Settings::get('edu_cess') / 100), 2);
			$stax = bcadd($stax, (Settings::get('service_tax') * Settings::get('hedu_cess') / 100), 2);
			$service_tax = bcdiv(bcmul($transport_charge, $stax), 100, 0);
			$abatment = bcdiv(bcmul($service_tax, Settings::get('abatment')), 100, 0);
// 			$remarks = 'SERVICE TAX
// NO CENVAT CREDIT AVAILED AND NO BENEFIT UNDER NOTIFICATION NO.12/2003 OF
// SERVICE TAX AVAILED. AS PER NOTIFICATION NO. 35/2004 SERVICE TAX WILL BE
// PAID BY CONSIGNEE
// SERVICE TAX ON TRANSPORTATION                      Rs. ' . str_pad(number_format($service_tax, 2, '.', ''), 12, ' ', STR_PAD_LEFT) . '
// LESS : ' . str_pad(Settings::get('abatment'), 3, ' ', STR_PAD_LEFT) . '% ABATMENT                               Rs. ' . str_pad(number_format($abatment, 2, '.', ''), 12, ' ', STR_PAD_LEFT) . '
//                                                 -------------------
// NET SERVICE TAX PAYABLE                            Rs. ' . str_pad(number_format(($service_tax-$abatment), 2, '.', ''), 12, ' ', STR_PAD_LEFT) . '
//                                                  ==================';
		}

		if (strlen(trim($remarks)) > 0) {
			$this->db->query("UPDATE vouchers V INNER JOIN (
				SELECT voucher_id, ROUND(SUM(amount), 0) AS total 
				FROM voucher_details 
				WHERE voucher_id = ? 
				GROUP BY voucher_id) VJD ON V.id = VJD.voucher_id
			SET V.amount = VJD.total, V.remarks = ? 
			WHERE V.id = ?", array($voucher_id, $remarks, $voucher_id));
		}
		else {
			$this->db->query("UPDATE vouchers V INNER JOIN (
				SELECT voucher_id, ROUND(SUM(amount), 0) AS total 
				FROM voucher_details 
				WHERE voucher_id = ? 
				GROUP BY voucher_id) VJD ON V.id = VJD.voucher_id
			SET V.amount = VJD.total
			WHERE V.id = ?", array($voucher_id, $voucher_id));
		}
	}

	function isVoucherBook($id) {
		$result = false;
		
		$sql = "SELECT id FROM voucher_books VB WHERE id = ? AND company_id = ?";
		$query = $this->db->query($sql, array($id, $this->_company_id));
		if ($query->num_rows() > 0)
			$result = true;
		return $result;
	}

	function getBooksMenu() {
		$sql = "SELECT VB.id, VT.name AS voucher_type, VB.name 
		FROM voucher_books VB INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id 
		WHERE VB.company_id = ?
		ORDER BY VT.name, VB.name";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getBookName($id) {
		$sql = "SELECT CONCAT(VB.name, ' - ', VT.name) AS name
		FROM voucher_books VB INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id 
		WHERE VB.id = ?";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		return $row['name'];
	}

	function getClearingVoucherBooks($type = 'N/A') {
		$sql = "SELECT VB.id, VB.voucher_type_id 
		FROM voucher_books VB 
		WHERE VB.company_id = ? AND VB.job_type = ? AND VB.voucher_type_id IN (3, 4)";
		$query = $this->db->query($sql, array($this->_company_id, $type));
		$rows = $query->result_array();
		$data = array();
		foreach ($rows as $row) {
			$data[$row['voucher_type_id']] = array('voucher_book_id' => $row['id']);
		}
		return $data;
	}

	function checkVoucherDate($voucher_book_id, $id2, $date) {
		return true;
		
		$voucher_book = $this->kaabar->getField('voucher_books', $voucher_book_id, 'id', array('auto_numbering', 'date_lock'));
		if ($voucher_book['auto_numbering'] == 'Yes') 
			return TRUE;

		$result     = FALSE;
		$company_id = $this->_company_id;
		$years      = explode('_', $this->_fy_year);
		$vdate      = strtotime($date);
		$date1      = strtotime($years[0].'-04-01');
		$date2      = strtotime($years[1].'-03-31');
		$date3      = strtotime($date);
		$date_lock  = strtotime($voucher_book['date_lock']);

		// Check if date is greater than Date_lock set in voucher book.
		if ($date_lock > $vdate)
			return FALSE;

		// Check if date is lying in currently financial year.
		if (($date3 - $date1) > 0 && ($date2 - $date3) < 0)
			return $result;
		else if (($date3 - $date1) < 0 && ($date3 - $date2) <= 0)
			return $result;

		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';
		if ($id2 > 0) {
			// Check if its the same record.
			$sql = "SELECT V.id
			FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND
				  V.voucher_book_id = ? AND 
				  V.id2 = ? AND 
				  V.date = ?";
			$query = $this->db->query($sql, array($company_id, $from_date, $to_date, $voucher_book_id, $id2, $date));
			if ($query->num_rows() > 0)
				$result = TRUE;

			// Check if the new date lies between immediate next or prev record.
			if (! $result) {
				$sql = "SELECT V.id2, V.date 
				FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id 
				WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.voucher_book_id = ? AND V.id2 < ?
				ORDER BY id2 DESC LIMIT 0, 1";
				$query = $this->db->query($sql, array($company_id, $from_date, $to_date, $voucher_book_id, $id2));
				$prev  = $query->row_array();

				$sql = "SELECT V.id2, V.date 
				FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id 
				WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.voucher_book_id = ? AND V.id2 > ?
				ORDER BY id2 LIMIT 0, 1";
				$query = $this->db->query($sql, array($company_id, $from_date, $to_date, $voucher_book_id, $id2));
				$next  = $query->row_array();
				$date1 = ($prev ? strtotime($prev['date']) : strtotime($years[0].'-04-01'));
				$date2 = ($next ? strtotime($next['date']) : strtotime($years[1].'-03-31'));

				// Check if date is lying in prev and next dates.
				if (($date3 - $date1) > 0 && ($date2 - $date3) < 0)
					$result = FALSE;
				else if (($date3 - $date1) < 0 && ($date3 - $date2) <= 0)
					$result = FALSE;
				else 
					$result = TRUE;
			}
		}
	  	else {
	  		$sql = "SELECT V.id
			FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND
				V.voucher_book_id = ? AND 
				V.date > ?";
	  		$query = $this->db->query($sql, array($company_id, $from_date, $to_date, $voucher_book_id, $date));
			if ($query->num_rows() == 0)
				$result = TRUE;
	  	}
		return $result;
	}

	function getNextVoucherNo($voucher_book_id, $date, $job_id = 0, $company_id = null) {
		if (is_null($company_id) OR intval($company_id) == 0) {
			$company_id   = $this->_company_id;
			$company_code = $this->kaabar->getField('companies', $this->_company_id, 'id', 'code');
		}
		else
			$company_code = $this->kaabar->getField('companies', $company_id, 'id', 'code');

		$voucher_book = $this->kaabar->getRow('voucher_books', $voucher_book_id);
		$port_code    = '';
		if ($job_id > 0) {
			$job = $this->kaabar->getRow('jobs', $job_id);
			$port_code = $this->kaabar->getField('indian_ports', $job['indian_port_id'], 'id', 'code');
		}

		$years      = explode('_', $this->kaabar->getFinancialYear($date));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';
		$year       = substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2);
		
		$this->db->query('LOCK TABLES ci_sessions WRITE, voucher_books AS VB READ, vouchers AS V READ');
		$sql = "SELECT MAX(V.id2) AS id2 
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.voucher_book_id = ?";
		$query = $this->db->query($sql, array($company_id, $start_date, $end_date, $voucher_book_id));
		$id_row = $query->row_array();
		$this->db->query('UNLOCK TABLES');
		$id_row['id2']++;
		$id_row['id2_format'] = str_replace('[[comp]]', $company_code, 
			str_replace('[[job]]', strtoupper(substr($job['type'], 0, 3)), 
				str_replace('[[book]]', $voucher_book['code'], 
					str_replace('[[port]]', $port_code, 
						str_replace('[[num]]', str_pad($id_row['id2'], 3, '0', STR_PAD_LEFT), 
							str_replace('[[year]]', $year, $voucher_book['id2_format'])
						)
					)
				)
			)
		);
		return $id_row;
	}


	function countVoucherBooks($search = '') {
		$sql = "SELECT COUNT(VB.id) AS numrows
		FROM voucher_books VB LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			LEFT OUTER JOIN voucher_details VJD ON VB.id = VJD.voucher_id
		WHERE VB.company_id = ? AND (
			VT.name LIKE '%$search%' OR
			VB.code LIKE '%$search%' OR
			VB.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getVoucherBooks($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT VB.id, VT.name AS voucher_type, VB.code, VB.name, CONCAT(VB.dr_cr, ' - ', L.name) AS default_ledger, 
			VB.job_type, VB.auto_numbering, DATE_FORMAT(VB.date_lock, '%d-%m-%Y') AS date_lock
		FROM (voucher_books VB LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
			LEFT OUTER JOIN ledgers L ON VB.default_ledger_id = L.id
		WHERE VB.company_id = ? AND (
			VT.name LIKE '%$search%' OR
			VB.code LIKE '%$search%' OR
			VB.name LIKE '%$search%')
		ORDER BY VB.code
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getVesselLedgers($id) {
		if (intval($id) == 0)
			return array();

		$ledgers = array();

		$sql = "SELECT VL.*, AG.account_id 
		FROM ledgers VL INNER JOIN account_groups AG ON VL.account_group_id = AG.id
		WHERE VL.company_id = ? AND VL.vessel_id = ?
		ORDER BY VL.id";
		$query = $this->db->query($sql, array($this->_company_id, $id));
		$ledgers = $query->result_array();
		return $ledgers;
	}

	function getTDSDetail($id) {
		$sql = "SELECT id, payment_id, DATE_FORMAT(applicable_date, '%d-%m-%Y') AS applicable_date, tds, surcharge, edu_cess, hedu_cess 
		FROM tds_details WHERE deductee_id = ? ORDER BY applicable_date";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function getStaxRates($id) {
		$sql = "SELECT id, stax_category_id, DATE_FORMAT(wef_date, '%d-%m-%Y') AS wef_date, stax, edu_cess, hedu_cess 
		FROM stax_rates WHERE stax_category_id = ? ORDER BY wef_date";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();	
	}

	function countBillItems($search = '') {
		$sql = "SELECT COUNT(L.id) AS numrows
		FROM ledgers L INNER JOIN companies C ON (L.company_id = ? AND L.category = 'Bill Items' AND L.company_id = C.id)
			LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
		WHERE L.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR
			C.code LIKE '%$search%' OR
			C.name LIKE '%$search%' OR
			STAX.name LIKE '%$search%'";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getBillItems($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT L.id, C.code AS company_code, L.code, L.name, 
			STAX.name AS stax_category, L.reimbursement, L.active
		FROM ledgers L INNER JOIN companies C ON (L.company_id = ? AND L.category = 'Bill Items' AND L.company_id = C.id)
			LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
		WHERE L.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR
			C.code LIKE '%$search%' OR
			C.name LIKE '%$search%' OR
			STAX.name LIKE '%$search%'
		ORDER BY L.category, L.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function countLedgers($category, $search = '') {
		$sql = "SELECT COUNT(L.id) AS numrows
		FROM (((ledgers L LEFT OUTER JOIN account_groups AG ON L.account_group_id = AG.id)
			LEFT OUTER JOIN tds_classes TDS ON L.tds_class_id = TDS.id)
			LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id)
			LEFT OUTER JOIN ledgers PL ON L.parent_ledger_id = PL.id
		WHERE (L.company_id = ? AND L.category = ?) AND 
			(L.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR
			L.group_name LIKE '%$search%' OR
			L.dr_cr LIKE '%$search%' OR
			L.opening_balance LIKE '%$search%' OR
			AG.name LIKE '%$search%' OR
			TDS.name LIKE '%$search%' OR
			STAX.name LIKE '%$search%' OR
			PL.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company_id, $category));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getLedgers($category, $search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT CONCAT(L.category, '/', L.id) AS id,
			L.code, L.name, L.group_name, PL.name AS parent_name, AG.name AS account_group, 
			TDS.name AS tds_class, STAX.name AS stax_category, L.dr_cr, L.opening_balance
		FROM ledgers L LEFT OUTER JOIN account_groups AG ON L.account_group_id = AG.id
			LEFT OUTER JOIN tds_classes TDS ON L.tds_class_id = TDS.id
			LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
			LEFT OUTER JOIN ledgers PL ON L.parent_ledger_id = PL.id
		WHERE (L.company_id = ? AND L.category = ?) AND 
			(L.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR
			L.group_name LIKE '%$search%' OR
			L.dr_cr LIKE '%$search%' OR
			L.opening_balance LIKE '%$search%' OR
			AG.name LIKE '%$search%' OR
			TDS.name LIKE '%$search%' OR
			STAX.name LIKE '%$search%' OR
			PL.name LIKE '%$search%')
		ORDER BY L.category, L.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id, $category));
		return $query->result_array();
	}

	function getLedger($date, $type, $account_group_id) {
		$sql = "SELECT AG.name AS group_name, L.code, L.name, TB.debit, TB.credit
		FROM (ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id)
			INNER JOIN (
			SELECT CLOSE.id, 
				IF(SUM(amount) > 0, ROUND(SUM(amount), 2), 0) AS debit, 
				IF(SUM(amount) < 0, ROUND(ABS(SUM(amount)), 2), 0) AS credit
			FROM (
					SELECT L.id, ROUND(opening_balance, 2) AS amount
					FROM ledgers L 
					WHERE company_id = ?
				UNION
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS amount
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? 
					GROUP BY V.dr_ledger_id
				UNION
					SELECT V.cr_ledger_id AS id, SUM(-V.amount) AS amount
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND 
						VB.voucher_type_id != 4
					GROUP BY V.cr_ledger_id
				UNION
					SELECT V.cr_ledger_id AS id, SUM(VJD.amount) AS amount
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND 
						BI.category = 'General' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 4
					GROUP BY V.cr_ledger_id
				UNION
					SELECT VJD.bill_item_id AS id, SUM(-VJD.amount) AS amount
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND 
						VJD.bill_item_id = 'Bill Items' AND 
						VB.voucher_type_id = 4
					GROUP BY VJD.bill_item_id
				) AS CLOSE
			GROUP BY id
		) AS TB ON L.id = TB.id
		WHERE (TB.debit > 0 OR TB.credit > 0) AND
			(AG.name LIKE '%$search%' OR
			L.code LIKE '%$search%' OR
			L.name LIKE '%$search%')
		ORDER BY AG.id, L.name";
		if ($return_query) 
			return $this->db->query($sql);

		$sql .= " LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id, $this->_company_id, $this->_company_id, $this->_company_id, $this->_company_id));
		return $query->result_array();
	}

	function countVouchers($voucher_book_id = 0, $search = '') {
		$company_id = $this->_company_id;
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT COUNT(V.id) AS numrows
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
		WHERE (VB.id = $voucher_book_id AND VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND (
			DATE_FORMAT(V.date, '%d-%m-%Y') LIKE '%$search%' OR
			V.id2 LIKE '%$search%' OR
			V.id3 LIKE '%$search%' OR
			V.cheque_no LIKE '%$search%' OR
			V.invoice_no LIKE '%$search%' OR
			V.amount LIKE '%$search%' OR
			DATE_FORMAT(V.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			DL.code LIKE '%$search%' OR
			DL.name LIKE '%$search%' OR
			CL.code LIKE '%$search%' OR
			CL.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getVouchers($voucher_book_id = 0, $search = '', $offset = 0, $limit = 25) {
		$company_id = $this->_company_id;
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT V.id, CONCAT(V.id2, '/', V.id3) AS voucher_no, DATE_FORMAT(V.date, '%d-%m-%Y') AS date,
			V.cheque_no, V.invoice_no, DATE_FORMAT(V.cheque_date, '%d-%m-%Y') AS cheque_date, 
			DL.code AS dr_code, DL.name AS dr_name,
			CL.code AS cr_code, CL.name AS cr_name,
			V.amount, IF(ISNULL(VD.file), IF(V.id3 > 1, '', 'No'), 'Yes') AS document
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN voucher_documents VD ON V.id = VD.voucher_id
		WHERE (VB.id = $voucher_book_id AND VB.company_id = $company_id AND V.date >= '$from_date' AND V.date <= '$to_date') AND (
			DATE_FORMAT(V.date, '%d-%m-%Y') LIKE '%$search%' OR
			CONCAT(V.id2, '/', V.id3) LIKE '%$search%' OR
			V.cheque_no LIKE '%$search%' OR
			V.invoice_no LIKE '%$search%' OR
			V.amount LIKE '%$search%' OR
			DATE_FORMAT(V.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			DL.code LIKE '%$search%' OR
			DL.name LIKE '%$search%' OR
			CL.code LIKE '%$search%' OR
			CL.name LIKE '%$search%')
		ORDER BY V.date DESC, V.id2 DESC, V.id3 DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countInvoices($voucher_book_id = 0, $search = '') {
		$company_id = $this->_company_id;
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT COUNT(V.id) AS numrows
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN jobs J ON V.job_id = J.id
		WHERE (VB.id = $voucher_book_id AND VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND (
			DATE_FORMAT(V.date, '%d-%m-%Y') LIKE '%$search%' OR
			V.id2_format LIKE '%$search%' OR
			V.cheque_no LIKE '%$search%' OR
			V.amount LIKE '%$search%' OR
			DATE_FORMAT(V.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			J.id2_format LIKE '%$search%' OR
			DL.code LIKE '%$search%' OR
			DL.name LIKE '%$search%' OR
			CL.code LIKE '%$search%' OR
			CL.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getInvoices($voucher_book_id = 0, $search = '', $offset = 0, $limit = 25) {
		$company_id = $this->_company_id;
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT V.id, V.id2_format AS voucher_no, J.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date,
			V.cheque_no, DATE_FORMAT(V.cheque_date, '%d-%m-%Y') AS cheque_date, 
			DL.code AS dr_code, DL.name AS dr_name,
			CL.code AS cr_code, CL.name AS cr_name,
			V.amount, IF(ISNULL(VD.file), IF(V.id3 > 1, '', 'No'), 'Yes') AS document
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN jobs J ON V.job_id = J.id
			LEFT OUTER JOIN voucher_documents VD ON V.id = VD.voucher_id
		WHERE (VB.id = $voucher_book_id AND VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND (
			DATE_FORMAT(V.date, '%d-%m-%Y') LIKE '%$search%' OR
			V.id2_format LIKE '%$search%' OR
			V.cheque_no LIKE '%$search%' OR
			V.amount LIKE '%$search%' OR
			DATE_FORMAT(V.cheque_date, '%d-%m-%Y') LIKE '%$search%' OR
			J.id2_format LIKE '%$search%' OR
			DL.code LIKE '%$search%' OR
			DL.name LIKE '%$search%' OR
			CL.code LIKE '%$search%' OR
			CL.name LIKE '%$search%')
		ORDER BY V.date DESC, V.id2 DESC, V.id3 DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date));
		return $query->result_array();
	}

	function getVoucher($voucher_book_id = 0, $id = 0, $prev_id = 0, $company_id = 0) {
		if ($company_id == 0)
			$company_id = $this->_company_id;
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT V.*, J.id2_format AS job_no, J.type, J.cargo_type, J.product_id, J.party_id, J.vessel_id, 
			J.bl_no, J.packages, J.net_weight, J.container_20, J.container_40,
			VB.code AS voucher_book_code, 
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date,
			DATE_FORMAT(V.cheque_date, '%d-%m-%Y') AS cheque_date,
			DATE_FORMAT(V.reconciliation_date, '%d-%m-%Y') AS reconciliation_date,
			DATE_FORMAT(V.invoice_date, '%d-%m-%Y') AS invoice_date,
			TDSD.id AS dr_tds_class_id, TDSD.type AS dr_tds_type, DL.stax_category_id AS dr_stax_category_id, 
			CONCAT(DL.code, ' - ', DL.name) AS debit_account, 
			IF(DL.party_id = 0, DA.name, DP.name) AS debit_party_name, 
			IF(DL.party_id = 0, CONCAT(DA.address, '<br />', DAC.name, '-', DAC.pincode, ', ', DAS.name), CONCAT(DP.address, '<br />', DPC.name, '-', DPC.pincode, ', ', DPS.name)) AS debit_party_address,
			TDSC.id AS cr_tds_class_id, TDSC.type AS cr_tds_type, CL.stax_category_id AS cr_stax_category_id, 
			CONCAT(CL.code, ' - ', CL.name) AS credit_account,
			IF(CL.party_id = 0, CA.name, CP.name) AS credit_party_name, 
			IF(CL.party_id = 0, CONCAT(CA.address, '<br />', CAC.name, '-', CAC.pincode, ', ', CAS.name), CONCAT(CP.address, '<br />', CPC.name, '-', CPC.pincode, ', ', CPS.name)) AS credit_party_address
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN jobs J ON V.job_id = J.id
			LEFT OUTER JOIN parties DP ON DL.party_id = DP.id
			LEFT OUTER JOIN agents DA ON DL.agent_id = DA.id
			LEFT OUTER JOIN cities DPC ON DP.city_id = DPC.id
			LEFT OUTER JOIN states DPS ON DPC.state_id = DPS.id
			LEFT OUTER JOIN cities DAC ON DA.city_id = DAC.id
			LEFT OUTER JOIN states DAS ON DAC.state_id = DAS.id
			LEFT OUTER JOIN parties CP ON CL.party_id = CP.id
			LEFT OUTER JOIN agents CA ON CL.agent_id = CA.id
			LEFT OUTER JOIN cities CPC ON CP.city_id = CPC.id
			LEFT OUTER JOIN states CPS ON CPC.state_id = CPS.id
			LEFT OUTER JOIN cities CAC ON CA.city_id = CAC.id
			LEFT OUTER JOIN states CAS ON CAC.state_id = CAS.id
			LEFT OUTER JOIN tds_classes TDSD ON DL.tds_class_id = TDSD.id
			LEFT OUTER JOIN tds_classes TDSC ON CL.tds_class_id = TDSC.id
		WHERE VB.company_id = ? AND 
			V.date >= ? AND V.date <= ? AND 
			V.voucher_book_id = ? AND 
			V.id = ?
		ORDER BY V.id2, V.id3";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date, $voucher_book_id, ($id > 0 ? $id : $prev_id)));
		return $query->row_array();
	}

	function getSubVoucherList($voucher_book_id = 0, $id2 = 0, $company_id = 0) {
		if ($company_id == 0)
			$company_id = $this->_company_id;
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT V.*, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, DATE_FORMAT(V.cheque_date, '%d-%m-%Y') AS cheque_date,
			CONCAT(DL.code, ' - ', DL.name) AS debit_account, CONCAT(CL.code, ' - ', CL.name) AS credit_account	
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
		WHERE VB.company_id = ? AND 
			V.date >= ? AND V.date <= ? AND 
			V.voucher_book_id = ? AND 
			V.id2 = ?
		ORDER BY V.id3";
		$query = $this->db->query($sql, array($company_id, $from_date, $to_date, $voucher_book_id, $id2));
		return $query->result_array();
	}

	function getSubVouchers($voucher_book_id = 0, $id2 = 0) {
		$years = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';

		$sql = "SELECT V.id, V.id2, V.id3, DATE_FORMAT(V.date, '%d-%b') AS date, 
			DL.code AS dr_code, DL.name AS dr_name,
			CL.code AS cr_code, CL.name AS cr_name,
			V.amount
		FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
		WHERE VB.company_id = ? AND 
			V.date >= ? AND V.date <= ? AND 
			V.voucher_book_id = ? AND V.id2 = ?
		ORDER BY V.id2, V.date, V.id3";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date, $voucher_book_id, $id2));
		return $query->result_array();
	}

	function getCostSheetDetails($voucher_id) {
		$sql = "SELECT VJD.*, BI.code AS bill_item_code, BI.name AS bill_item_name, BI.category, STAX.name AS stax_code, L.name as vendor_name, C.name as currency_name, CC.name as sell_currency_name, VJD.file
		FROM costsheets VJD LEFT OUTER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		 	LEFT OUTER JOIN jobs J ON VJD.job_id = J.id
			LEFT OUTER JOIN stax_categories STAX ON BI.stax_category_id = STAX.id
			LEFT OUTER JOIN vendors L ON VJD.vendor_id = L.id
			LEFT OUTER JOIN currencies C ON VJD.currency_id = C.id
			LEFT OUTER JOIN currencies CC ON VJD.sell_currency_id = CC.id
		WHERE VJD.job_id = ?
		ORDER BY BI.category, VJD.sr_no, BI.stax_category_id, VJD.particulars";
		$query = $this->db->query($sql, array($voucher_id));
		return $query->result_array();
	}

	function getInvoice($voucher_id) {
		$sql = "SELECT I.*, L.name as billing_party, DATE_FORMAT(I.date, '%d-%m-%Y') AS date, J.idkaabar_code as jobNo
		FROM invoices I 
			LEFT OUTER JOIN ledgers L ON I.ledger_id = L.id
			LEFT OUTER JOIN jobs J ON I.job_id = J.id
		WHERE I.id = ?";
		$query = $this->db->query($sql, array($voucher_id));
		return $query->row_array();
	}

	function getInvoiceDetails($voucher_id) {
		$sql = "SELECT ID.*, BI.code AS bill_item_code, BI.name AS bill_item_name, BI.category, C.name as currency_name
		FROM invoice_details ID 
			LEFT OUTER JOIN ledgers BI ON ID.bill_item_id = BI.id
		 	LEFT OUTER JOIN costsheets CS ON ID.job_costsheet_id = CS.id
		 	LEFT OUTER JOIN currencies C ON ID.currency_id = C.id
			
		WHERE ID.invoice_id = ?
		ORDER BY ID.sr_no, ID.particulars";
		$query = $this->db->query($sql, array($voucher_id));
		return $query->result_array();
	}

	function getVoucherJobDetails($voucher_id) {
		$sql = "SELECT VJD.*, 
			P.name AS party_name, J.id2_format, J.bl_no, J.packages, 
			PKG.code AS package_unit, J.net_weight, J.net_weight_unit, 
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			BI.code AS bill_item_code, BI.name AS bill_item_name, BI.category, STAX.name AS stax_code
		FROM costsheets VJD LEFT OUTER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		 	LEFT OUTER JOIN jobs J ON VJD.job_id = J.id
			LEFT OUTER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN package_types PKG ON J.package_type_id = PKG.id
			LEFT OUTER JOIN stax_categories STAX ON BI.stax_category_id = STAX.id
		WHERE VJD.job_id = ?
		ORDER BY BI.category, VJD.sr_no, BI.stax_category_id, VJD.particulars";
		$query = $this->db->query($sql, array($voucher_id));
		return $query->result_array();
	}

	function getVoucherTransportationDetails($voucher_id) {
		$result = array();
		$sql = "SELECT VJD.*, 
			BI.code AS bill_item_code, BI.name AS bill_item_name, BI.category, STAX.name AS stax_code,
			T.cargo_type, DATE_FORMAT(T.date, '%d-%m-%Y') AS date, T.registration_no, T.lr_no, T.party_reference_no, T.container_no,
			FL.name AS from_location, TL.name AS to_location, (COALESCE(TA.advance, 0) + COALESCE(VT.advance, 0)) AS advance, VJD.units, VJD.rate, 
			VJD.amount
		FROM voucher_details VJD LEFT OUTER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			LEFT OUTER JOIN trips T ON VJD.trip_id = T.id
			LEFT OUTER JOIN stax_categories STAX ON BI.stax_category_id = STAX.id
			LEFT OUTER JOIN locations FL ON T.from_location_id = FL.id
			LEFT OUTER JOIN locations TL ON T.to_location_id = TL.id
			LEFT OUTER JOIN (
				SELECT TA.trip_id, SUM(TA.amount) AS advance
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = ? AND TA.advance_by = 'Party'
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT VT.trip_id, SUM(VT.advance) AS advance
				FROM voucher_trips VT INNER JOIN vouchers V ON VT.voucher_id = V.id
					INNER JOIN voucher_books VB ON (VB.company_id = ? AND V.voucher_book_id = VB.id)
					INNER JOIN voucher_types VTS ON (VB.voucher_type_id = VTS.id AND VTS.name = 'Receipt')
				GROUP BY VT.trip_id
			) VT ON T.id = VT.trip_id
		WHERE VJD.voucher_id = ?
		GROUP BY VJD.id, T.id
		ORDER BY IF(VJD.trip_id = 0, 2, 1), VJD.sr_no, BI.category, BI.stax_category_id, FL.name, TL.name";
		$query = $this->db->query($sql, array($this->_company_id, $this->_company_id, $voucher_id));

		return $query->result_array();
	}

	function getTDSVouchers($id) {
		$sql = "SELECT DATE_FORMAT(V.date, '%Y-%m') AS month
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
		WHERE VB.company_id = ? AND V.tds_payment_id = ? 
		LIMIT 0, 1";
		$query = $this->db->query($sql, array($this->_company_id, $id));
		$row   = $query->row_array();
		if ($row)
			return $this->getPendingTDSVouchers($id, $row['month']);

		return array();
	}

	function getVoucherDeliveryContainers($id) {
		$sql = "SELECT V.id2_format, J.bl_no, C.number, C.seal, D.vehicle_no, D.gatepass_no, D.destuffing_agent, D.lr_no,
			V.id AS voucher_id, D.id AS delivery_id
		FROM deliveries_stuffings D INNER JOIN containers C ON D.container_id = C.id)
			INNER JOIN jobs J ON D.job_id = J.id)
			LEFT JOIN voucher_delivery VD ON D.id = VD.delivery_id)
			LEFT OUTER JOIN vouchers V ON VD.voucher_id = V.id
		WHERE VD.voucher_id = ?
		ORDER BY VD.voucher_id DESC, D.id DESC";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();		
	}

	function getPendingTDSVoucherMonths() {
		$sql = "SELECT DATE_FORMAT(V.date, '%b-%Y') AS date, DATE_FORMAT(V.date, '%Y-%m') AS month
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id)
			INNER JOIN tds_classes TC ON CL.tds_class_id = TC.id
		WHERE VB.company_id = ? AND TC.type = 'Payment' AND V.tds_payment_id = 0
		GROUP BY DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY V.date";
		$query = $this->db->query($sql, array($this->_company_id));
		$rows = $query->result_array();
		$data = array();
		foreach ($rows as $row)
			$data[$row['month']] = $row['date'];

		return $data;
	}

	// $month is YYYY-MM
	function getPendingTDSVouchers($id, $month) {
		$tds_ledger_id = $this->kaabar->getField('vouchers', $id, 'id', 'dr_ledger_id');

		$sql = "SELECT V.id, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, V.id2_format, DL.name AS debit_name, V.invoice_amount, V.tds, V.tds_amount, V.tds_surcharge, V.tds_edu_cess, V.tds_hedu_cess, V.tds_payment_id
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id)
			INNER JOIN tds_classes TC ON CL.tds_class_id = TC.id
		WHERE VB.company_id = ? AND 
			DATE_FORMAT(V.date, '%Y-%m') = ? AND 
			V.cr_ledger_id = ? AND 
			TC.type = 'Payment' AND 
			(V.tds_payment_id = 0 OR V.tds_payment_id = ?)
		ORDER BY V.date, V.id2, V.id3";
		$query = $this->db->query($sql, array($this->_company_id, $month, $tds_ledger_id, $id));
		return $query->result_array();
	}

	
	function findDuplicateVoucher($voucher_book_id, $id2, $cr_ledger_id, $invoice_no) {
		$company_id = $this->_company_id;
		$row = array('id' => 0);
		$sql = "SELECT V.id, V.voucher_book_id, V.id2, V.id3 
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
		WHERE VB.company_id = ? AND 
			  V.voucher_book_id = ? AND 
			  V.cr_ledger_id = ? AND 
			  V.invoice_no = ? AND 
			  V.id2 != ?";
		$query = $this->db->query($sql, array($company_id, $voucher_book_id, $cr_ledger_id, $invoice_no, $id2));
		if ($query->num_rows() > 0)
			$row = $query->row_array();
		return $row;
	}

	function getBLVouchers($job_id) {
		// Fetching based on Bill Item code across companies
		$result = array(
			'vouchers' => array(), 
			'bills'    => array()
		);

		$sql = "SELECT VT.id, C.code AS company_code, 
			CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url, VB.id, V.id2, V.id3, V.id2_format,
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, VJD.id AS voucher_detail_id, 
			BI.code, BI.name, V.invoice_no, SUM(IF(VB.voucher_type_id = 11, -VJD.amount, VJD.amount)) AS amount
		FROM voucher_details VJD INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN companies C ON VB.company_id = C.id
		WHERE VJD.job_id = ? AND V.job_id = 0
		GROUP BY BI.id
		ORDER BY VT.id DESC, V.date, BI.name";
		$query = $this->db->query($sql, array($job_id));
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result['vouchers'][$r['code']] = $r;
		}

		$sql = "SELECT CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url, 
			BI.code, SUM(VJD.amount) AS amount
		FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		WHERE VB.company_id = ? AND VJD.job_id = 0 AND V.job_id = ?
		GROUP BY BI.id";
		$query = $this->db->query($sql, array($this->_company_id, $job_id));
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result['bills'][$r['code']] = $r;
		}
		return $result;
	}

	function getJobVouchers($job_id) {
		$sql = "SELECT VT.id, C.code AS company_code, 
			CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url, VB.id, V.id2, V.id3, V.id2_format,
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, VJD.id AS voucher_detail_id, 
			BI.code, BI.name, V.invoice_no, SUM(IF(VB.voucher_type_id = 11, -VJD.amount, VJD.amount)) AS amount
		FROM voucher_details VJD INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN companies C ON VB.company_id = C.id
		WHERE VJD.job_id = ? AND V.job_id = 0
		GROUP BY VJD.id
		ORDER BY V.date, BI.name";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function getTransportationExpenses($job_id) {
		$sql = "SELECT COUNT(T.id) AS trips, 
			SUM(T.transporter_rate) AS transporter_rate,
			SUM(TA.self_adv) AS cash_advance, SUM(PA.amount) AS fuel_advance,
			SUM(COALESCE(PYTRIP.advance, 0)) AS cheque_advance,
			ROUND(SUM(T.transporter_rate) - (SUM(COALESCE(TA.amount, 0)) + SUM(COALESCE(PA.amount, 0)) + SUM(COALESCE(PYTRIP.advance, 0)) + COALESCE(TTID.amount, 0)), 2) AS balance
		FROM trips T 
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
				SELECT PA.trip_id, GROUP_CONCAT(DISTINCT A.name) AS pump_name, SUM(PA.amount) AS amount
				FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
					INNER JOIN agents A ON PA.agent_id = A.id
				WHERE T.company_id = ?
				GROUP BY PA.trip_id
			) PA ON T.id = PA.trip_id
			LEFT OUTER JOIN trip_inward_details TTID ON (T.id = TTID.trip_id AND TTID.pump_advance_id = 0)
			LEFT OUTER JOIN voucher_trips PYTRIP ON (T.id = PYTRIP.trip_id AND PYTRIP.pump_advance_id = 0 AND PYTRIP.advance > 0)
			LEFT OUTER JOIN vouchers PY ON PYTRIP.voucher_id = PY.id
		WHERE T.job_id = ?
		GROUP BY T.job_id";
		$query = $this->db->query($sql, array($this->_company_id, $this->_company_id, $job_id));
		return $query->row_array();
	}

	function getPaymentVouchers($job_id) {
		$sql = "SELECT VT.id, C.code AS company_code, 
			CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url, VB.id, V.id2, V.id3, V.id2_format,
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, VJD.id AS voucher_detail_id, 
			BI.code, BI.name, V.invoice_no, SUM(IF(VB.voucher_type_id = 11, -VJD.amount, VJD.amount)) AS amount
		FROM voucher_details VJD INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN companies C ON VB.company_id = C.id
		WHERE VJD.job_id = ? AND V.job_id = 0
		GROUP BY VJD.id
		ORDER BY V.date, BI.name";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function getPendingBLVouchers($job_id) {
		$company_id = $this->_company_id;
		$stamp_duty = $this->kaabar->getField('import_details', $job_id, 'job_id', 'stamp_duty');

		$sql = "SELECT BI.id, VT.name AS voucher_type, BI.code, BI.name AS particulars, SUM(VJD.amount) AS amount
		FROM voucher_details VJD INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
		WHERE VJD.job_id = ? AND 
			V.job_id = 0 AND
			BI.id NOT IN (
				SELECT VJD.bill_item_id
				FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
				WHERE VJD.job_id = ? AND 
					V.job_id = ?
			)
		GROUP BY VT.name, BI.code
		ORDER BY VT.name, BI.name";
		$query = $this->db->query($sql, array($job_id, $job_id, $job_id));
		$rows = $query->result_array();
		$data = array();

		$import_details = $this->kaabar->getRow('import_details', $job_id, 'job_id');
		foreach ($rows as $row) {
			if (isset($import_details['id'])) {
				if ($row['code'] == 'CD' && $import_details['cd_paid_direct']  == 'Yes') continue;
				if ($row['code'] == 'PQ' && $import_details['ppq_paid_direct'] == 'Yes') continue;
			}
			$data[$row['code']] = array(
				'bill_item_id' => $row['id'],
				'code'         => $row['code'],
				'particulars'  => $row['particulars'],
				'amount'       => $row['amount'],
			);
		}
		if (! isset($data['STAMP']) && $stamp_duty > 0)
			$data['STAMP']['amount'] = $stamp_duty;
		return $data;
	}

	

	function getClosing($id) {
		if (is_null($id)) 
			return 0;
		
		$sql = "SELECT SUM(amount) AS closing
		FROM (
				SELECT id, ROUND(opening_balance, 2) AS amount
				FROM ledgers L 
				WHERE company_id = ? AND id = ?
			UNION
				SELECT V.dr_ledger_id AS id, SUM(V.amount) AS amount
				FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
				WHERE VB.company_id = ? AND V.dr_ledger_id = ?
				GROUP BY V.dr_ledger_id
			UNION
				SELECT V.cr_ledger_id AS id, SUM(-V.amount) AS amount
				FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
				WHERE VB.company_id = ? AND V.cr_ledger_id = ? AND 
					VB.voucher_type_id != 4
				GROUP BY V.cr_ledger_id
			UNION
				SELECT VJD.bill_item_id AS id, SUM(-VJD.amount) AS amount
				FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
					INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
				WHERE VB.company_id = ? AND V.cr_ledger_id = ? AND 
					BI.category = 'Bill Items' AND 
					BI.stax_category_id > 0 AND 
					VB.voucher_type_id = 4
				GROUP BY VJD.bill_item_id
			UNION
				SELECT VJD.bill_item_id AS id, SUM(VJD.amount) AS amount
				FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
					INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
				WHERE VB.company_id = ? AND VJD.bill_item_id = ? AND 
					BI.category != 'Bill Items' AND 
					BI.stax_category_id > 0 AND 
					VB.voucher_type_id = 4
				GROUP BY VJD.bill_item_id
			) AS CLOSE
		WHERE id = $id
		GROUP BY id";
		$query = $this->db->query($sql, array(
			$this->_company_id, $id,
			$this->_company_id, $id,
			$this->_company_id, $id,
			$this->_company_id, $id,
			$this->_company_id, $id
		));
		$row = $query->row_array();
		if ($row)
			return $row['closing'];
		return 0;
	}


	function getClosingSql($search) {
		$company_id = $this->_company_id;

		// Getting parent ids, removing from next Sql.
		$query = $this->db->query("SELECT DISTINCT parent_ledger_id FROM ledgers WHERE company_id = $company_id ORDER BY parent_ledger_id");
		$rows = $query->result_array();
		$parent_ids = array();
		foreach ($rows as $row) {
			$parent_ids[] = $row['parent_ledger_id'];
		}

		$sql = "SELECT L.id, L.code, L.name, L.group_name, TDS.id AS tds_class_id, TDS.type AS tds_type, L.stax_category_id, 
				CONCAT(ROUND(TB.closing, 2), ' ', TB.dr_cr) AS closing
			FROM ((ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id)
				INNER JOIN (
				SELECT CLOSE.id, IF(SUM(amount) > 0, 'Dr', 'Cr') AS dr_cr, ABS(SUM(amount)) AS closing
				FROM (
						SELECT id, ROUND(opening_balance, 2) AS amount
						FROM ledgers L 
						WHERE company_id = $company_id AND 
							(code LIKE '%$search%' OR name LIKE '%$search%')
					UNION
						SELECT V.dr_ledger_id AS id, SUM(V.amount) AS amount
						FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						WHERE VB.company_id = $company_id AND 
							dr_ledger_id IN (SELECT id FROM ledgers WHERE company_id = $company_id AND 
							(code LIKE '%$search%' OR name LIKE '%$search%'))
						GROUP BY V.dr_ledger_id
					UNION
						SELECT V.cr_ledger_id AS id, SUM(-V.amount) AS amount
						FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						WHERE VB.company_id = $company_id AND 
							V.cr_ledger_id IN (SELECT id FROM ledgers WHERE company_id = $company_id AND 
							(code LIKE '%$search%' OR name LIKE '%$search%')) AND 
							VB.voucher_type_id != 4
						GROUP BY V.cr_ledger_id
					UNION
						SELECT V.cr_ledger_id AS id, SUM(-VJD.amount) AS amount
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = $company_id AND 
							V.cr_ledger_id IN (SELECT id FROM ledgers WHERE company_id = $company_id AND 
							(code LIKE '%$search%' OR name LIKE '%$search%')) AND 
							BI.category = 'Bill Items' AND 
							BI.stax_category_id > 0 AND 
							VB.voucher_type_id = 4
						GROUP BY V.cr_ledger_id
					UNION
						SELECT VJD.bill_item_id AS id, SUM(-VJD.amount) AS amount
						FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
							INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
							INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
						WHERE VB.company_id = $company_id AND 
							VJD.bill_item_id IN (SELECT id FROM ledgers WHERE company_id = $company_id AND 
							(code LIKE '%$search%' OR name LIKE '%$search%')) AND 
							BI.category != 'Bill Items' AND 
							BI.stax_category_id > 0 AND 
							VB.voucher_type_id = 4
						GROUP BY VJD.bill_item_id
					) AS CLOSE
				GROUP BY id
			) AS TB ON L.id = TB.id)
				LEFT OUTER JOIN tds_classes TDS ON L.tds_class_id = TDS.id
			WHERE L.id NOT IN (" . join(',', $parent_ids) . ")
			ORDER BY L.name
			LIMIT 0, 20";
		return $sql;
	}

	function getClosingDate($id, $date = FALSE) {
		if (! $date)
			$date = date('Y-m-d');

		$sql = "SELECT SUM(amount) AS closing
		FROM (
				SELECT 1 AS id, ROUND(SUM(opening_balance), 2) AS amount
				FROM ledgers L 
				WHERE L.company_id = ? AND (L.id = ? OR L.parent_ledger_id = ?)
			UNION
				SELECT 1 AS id, SUM(V.amount) AS amount
				FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN ledgers L ON V.dr_ledger_id = L.id
				WHERE VB.company_id = ? AND (V.dr_ledger_id = ? OR L.parent_ledger_id = ?) AND V.date < ?
				GROUP BY V.dr_ledger_id
			UNION
				SELECT 1 AS id, SUM(-V.amount) AS amount
				FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN ledgers L ON V.cr_ledger_id = L.id
				WHERE VB.company_id = ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND V.date < ? AND 
					VB.voucher_type_id != 4
				GROUP BY V.cr_ledger_id
			UNION
				SELECT 1 AS id, SUM(-VJD.amount) AS amount
				FROM (((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
					INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN ledgers L ON V.cr_ledger_id = L.id)
					INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
				WHERE VB.company_id = ? AND (V.cr_ledger_id = ? OR L.parent_ledger_id = ?) AND V.date < ? AND 
					BI.category = 'Bill Items' AND 
					VB.voucher_type_id = 4
				GROUP BY V.cr_ledger_id
			UNION
				SELECT 1 AS id, SUM(-VJD.amount) AS amount
				FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
					INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
				WHERE VB.company_id = ? AND VJD.bill_item_id = ? AND V.date < ? AND 
					BI.category != 'Bill Items' AND 
					BI.stax_category_id > 0 AND 
					VB.voucher_type_id = 4
				GROUP BY VJD.bill_item_id
			) AS CLOSE
		GROUP BY id";
		$query = $this->db->query($sql, array(
			$this->_company_id, $id, $id,
			$this->_company_id, $id, $id, $date,
			$this->_company_id, $id, $id, $date,
			$this->_company_id, $id, $id, $date,
			$this->_company_id, $id, $date
		));
		$row = $query->row_array();
		if ($row)
			return $row['closing'];
		return 0;
	}
}
