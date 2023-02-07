<?php

use mikehaertl\wkhtmlto\Pdf;

class Reconcilation extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'shipment' => 'J.type',
			'group'    => 'L.group_name',
			'party'    => 'P.name',
			'vessel'   => 'CONCAT(VE.prefix, " ", VE.name, " ", VE.voyage_no)',
			'type'     => 'J.cargo_type',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'cha'      => 'CHA.name',
			'billitem' => 'BI.code'
		);
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['jobs'] = $this->_getReconcilations($data['from_date'], $data['to_date'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$data['rows']  = $this->_getBillPending($from_date, $to_date, $search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class . ' Register');
		$data['page_desc'] = "For the Period $from_date - $to_date";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
			$this->kaabar->save($this->_table, array('printed' => 'Yes'), array('id' => $id));
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	function _getReconcilations($from_date, $to_date, $search, $query_only = false) {
		$company = $this->session->userdata('default_company');

		// Getting Invoices and Debit Note entries
		$sql = "SELECT V.id, CONCAT(LOWER(VT.name), '/edit/', V.voucher_book_id, '/', V.id2, '/', V.id3) AS url, 
			L.group_name, P.name AS party, CONCAT(VE.prefix, ' ', VE.name, ' ', VE.voyage_no) AS vessel, 
			J.id2_format AS job_no, J.id AS job_id, J.type, J.bl_no, J.sb_no, DATE_FORMAT(J.sb_date, '%d-%m-%Y') AS sb_date, 
			J.container_20, J.container_40, CHA.name AS cha_name, 
			BI.code AS bill_item_code, BI.name AS bill_item, 0 AS paid, 
			VJD.amount AS charged, PRD.category, PRD.name AS product_name
		FROM (SELECT * FROM jobs WHERE date >= ? AND date <= ?) J
			INNER JOIN vouchers V ON J.id = V.job_id
			INNER JOIN voucher_books VB ON (VB.company_id = ? AND V.voucher_book_id = VB.id AND VB.voucher_type_id IN (3, 4))
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN voucher_details VJD ON VJD.voucher_id = V.id
			INNER JOIN ledgers BI on VJD.bill_item_id = BI.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN ledgers L ON J.party_id = L.party_id
			LEFT OUTER JOIN vessels VE ON J.vessel_id = VE.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id";
		$where = ' WHERE (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					if ($key == 'billitem')
						$where .= $this->_fields[$key] . " IN ('" . join("', '", explode(',', $value)) . "') AND ";
					else
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 8)
				$sql .= substr($where, 0, strlen($where) - 5). ' )';
		}
		$sql .= "  
		GROUP BY V.job_id, VJD.bill_item_id
		ORDER BY BI.code, V.job_id";
		$query  = $this->db->query($sql, array(convDate($from_date), convDate($to_date), $company['id']));
		$rows   = $query->result_array();
		$result = array(
			'jobs'    => array(), 
			'charges' => array()
		);
		$jobs   = array(0);
		foreach ($rows as $r) {
			$result['jobs'][$r['job_id']]['job_no']             = $r['job_no'];
			$result['jobs'][$r['job_id']]['group_name']         = $r['group_name'];
			$result['jobs'][$r['job_id']]['party']              = $r['party'];
			$result['jobs'][$r['job_id']]['vessel']             = $r['vessel'];
			$result['jobs'][$r['job_id']]['type']               = $r['type'];
			$result['jobs'][$r['job_id']]['bl_no']              = $r['bl_no'];
			$result['jobs'][$r['job_id']]['sb_no']              = $r['sb_no'];
			$result['jobs'][$r['job_id']]['sb_date']            = $r['sb_date'];
			$result['jobs'][$r['job_id']]['container_20']       = $r['container_20'];
			$result['jobs'][$r['job_id']]['container_40']       = $r['container_40'];
			$result['jobs'][$r['job_id']]['cha_name']           = $r['cha_name'];
			$result['jobs'][$r['job_id']]['category']           = $r['category'];
			$result['jobs'][$r['job_id']]['product_name']       = $r['product_name'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']] = array(
				'charged_url' => underscore($r['url']),
				'charged'     => $r['charged'], 
				'due'         => -$r['charged']
			);

			$result['charges'][$r['bill_item_code']] = $r['bill_item_code'];

			$jobs[$r['job_id']] = $r['job_id'];
		}


		// Subrating Credit Note entries from Invoices and Debit Notes
		$sql = "SELECT V.id, CONCAT(LOWER(VT.name), '/edit/', V.voucher_book_id, '/', V.id2, '/', V.id3) AS url, 
			L.group_name, P.name AS party, CONCAT(VE.prefix, ' ', VE.name, ' ', VE.voyage_no) AS vessel, 
			J.id2_format AS job_no, J.id AS job_id, J.type, J.bl_no, J.sb_no, DATE_FORMAT(J.sb_date, '%d-%m-%Y') AS sb_date, 
			J.container_20, J.container_40, CHA.name AS cha_name, 
			BI.code AS bill_item_code, BI.name AS bill_item, 0 AS paid, 
			VJD.amount AS charged, PRD.category, PRD.name AS product_name
		FROM (SELECT * FROM jobs WHERE date >= ? AND date <= ?) J
			INNER JOIN vouchers V ON J.id = V.job_id
			INNER JOIN voucher_books VB ON (VB.company_id = ? AND V.voucher_book_id = VB.id AND VB.voucher_type_id = 2)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN voucher_details VJD ON VJD.voucher_id = V.id
			INNER JOIN ledgers BI on VJD.bill_item_id = BI.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN ledgers L ON J.party_id = L.party_id
			LEFT OUTER JOIN vessels VE ON J.vessel_id = VE.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id";
		$where = ' WHERE (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					if ($key == 'billitem')
						$where .= $this->_fields[$key] . " IN ('" . join("', '", explode(',', $value)) . "') AND ";
					else
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 8)
				$sql .= substr($where, 0, strlen($where) - 5). ' )';
		}
		$sql .= "  
		GROUP BY V.job_id, VJD.bill_item_id
		ORDER BY BI.code, V.job_id";
		$query  = $this->db->query($sql, array(convDate($from_date), convDate($to_date), $company['id']));
		$rows   = $query->result_array();
		foreach ($rows as $r) {
			$result['jobs'][$r['job_id']]['job_no']             = $r['job_no'];
			$result['jobs'][$r['job_id']]['group_name']         = $r['group_name'];
			$result['jobs'][$r['job_id']]['party']              = $r['party'];
			$result['jobs'][$r['job_id']]['vessel']             = $r['vessel'];
			$result['jobs'][$r['job_id']]['type']               = $r['type'];
			$result['jobs'][$r['job_id']]['bl_no']              = $r['bl_no'];
			$result['jobs'][$r['job_id']]['sb_no']              = $r['sb_no'];
			$result['jobs'][$r['job_id']]['sb_date']            = $r['sb_date'];
			$result['jobs'][$r['job_id']]['container_20']       = $r['container_20'];
			$result['jobs'][$r['job_id']]['container_40']       = $r['container_40'];
			$result['jobs'][$r['job_id']]['cha_name']           = $r['cha_name'];
			$result['jobs'][$r['job_id']]['category']           = $r['category'];
			$result['jobs'][$r['job_id']]['product_name']       = $r['product_name'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['charged_url'] = underscore($r['url']);
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['charged']    -= $r['charged'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['due']        += $r['charged'];

			$result['charges'][$r['bill_item_code']] = $r['bill_item_code'];

			$jobs[$r['job_id']] = $r['job_id'];
		}

		
		// Journal and Payments
		$sql = "SELECT DISTINCT V.id, CONCAT(LOWER(VT.name), '/edit/', V.voucher_book_id, '/', V.id2, '/', V.id3) AS url, 
			L.group_name, P.name AS party, CONCAT(VE.name, ' ', VE.voyage_no) AS vessel, J.id AS job_id, 
			J.bl_no, CHA.name AS cha_name, BI.code AS bill_item_code, BI.name AS bill_item, VJD.amount AS paid, 0 AS charged
		FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON (V.voucher_book_id = VB.id AND VB.company_id = ? AND VB.voucher_type_id IN (5, 7))
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN jobs J ON VJD.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN ledgers L ON J.party_id = L.party_id
			LEFT OUTER JOIN vessels VE ON J.vessel_id = VE.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
		WHERE VJD.job_id IN (" . implode(", ", $jobs) . ")";
		$where = ' AND (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]) && $key == 'billitem')
						$where .= $this->_fields[$key] . " IN ('" . join("', '", explode(',', $value)) . "') AND ";
			if (strlen($where) > 8)
				$sql .= substr($where, 0, strlen($where) - 5). ' )';
		}
		$sql .= "  
		GROUP BY VJD.id, VJD.bill_item_id
		ORDER BY BI.code, VJD.job_id";
		$query = $this->db->query($sql, array($company['id']));
		$rows = $query->result_array(); 
		foreach ($rows as $r) {
			if (! isset($result['jobs'][$r['job_id']][$r['bill_item_code']]['paid'])) {
				$result['jobs'][$r['job_id']][$r['bill_item_code']]['paid'] = 0;
			}
			if (! isset($result['jobs'][$r['job_id']][$r['bill_item_code']]['due'])) {
				$result['jobs'][$r['job_id']][$r['bill_item_code']]['due'] = 0;
			}
			$result['jobs'][$r['job_id']]['party']  = $r['party'];
			$result['jobs'][$r['job_id']]['vessel'] = $r['vessel'];
			$result['jobs'][$r['job_id']]['bl_no']  = $r['bl_no'];
			$result['jobs'][$r['job_id']]['cha_name'] = $r['cha_name'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['paid_url'] = underscore($r['url']);
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['paid']     += $r['paid'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['due']      += $r['paid'];

			$result['charges'][$r['bill_item_code']] = $r['bill_item_code'];
		}

		// Adding Receipt Entries 
		$sql = "SELECT DISTINCT V.id, CONCAT(LOWER(VT.name), '/edit/', V.voucher_book_id, '/', V.id2, '/', V.id3) AS url, 
			L.group_name, P.name AS party, CONCAT(VE.name, ' ', VE.voyage_no) AS vessel, J.id AS job_id, 
			J.bl_no, CHA.name AS cha_name, BI.code AS bill_item_code, BI.name AS bill_item, VJD.amount AS paid, 0 AS charged
		FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON (V.voucher_book_id = VB.id AND VB.company_id = ? AND VB.voucher_type_id = 11)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN jobs J ON VJD.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN ledgers L ON J.party_id = L.party_id
			LEFT OUTER JOIN vessels VE ON J.vessel_id = VE.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
		WHERE VJD.job_id IN (" . implode(", ", $jobs) . ")";
		$where = ' AND (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]) && $key == 'billitem')
						$where .= $this->_fields[$key] . " IN ('" . join("', '", explode(',', $value)) . "') AND ";
			if (strlen($where) > 8)
				$sql .= substr($where, 0, strlen($where) - 5). ' )';
		}
		$sql .= "  
		GROUP BY VJD.job_id, VJD.bill_item_id
		ORDER BY BI.code, VJD.job_id";
		$query = $this->db->query($sql, array($company['id']));
		$rows = $query->result_array(); 
		foreach ($rows as $r) {
			$result['jobs'][$r['job_id']]['party']  = $r['party'];
			$result['jobs'][$r['job_id']]['vessel'] = $r['vessel'];
			$result['jobs'][$r['job_id']]['bl_no']  = $r['bl_no'];
			$result['jobs'][$r['job_id']]['cha_name'] = $r['cha_name'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['paid_url'] = underscore($r['url']);
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['paid']     -= $r['paid'];
			$result['jobs'][$r['job_id']][$r['bill_item_code']]['due']      -= $r['paid'];

			$result['charges'][$r['bill_item_code']] = $r['bill_item_code'];
		}
		return $result;
	}
}
