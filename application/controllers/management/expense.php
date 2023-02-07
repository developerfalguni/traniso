<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Expense extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'job_no'  => 'IF(ISNULL(VD.id), 0, 1)',
			'bill_no' => 'IF(ISNULL(VV.id), 0, 1)',
			'debit'   => 'DL.name',
			'credit'  => 'CL.name',
			'cleared' => "IF(V.reconciliation_date = '0000-00-00', 0, 1)",
		);
	}
	
	function index() {
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = humanize($this->_class) . " Voucher Report";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$data['rows']  = $this->_get($from_date, $to_date, $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class . ' Register');
		$data['page_desc'] = "For the Period $from_date - $to_date";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_list_preview', $data, true);
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

	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows, array('voucher_book_id', 'id2', 'id3'));
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$company = $this->session->userdata('default_company');

		$sql = "SELECT V.id, V.voucher_book_id, V.id2, V.id3, CONCAT(V.id2_format, '/', V.id3) AS id2_format, 
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, GROUP_CONCAT(CONCAT(J.id2_format, ': ', VD.amount) SEPARATOR '<br />') AS job_no, 
			DL.name AS debit_name, CL.name AS credit_name, V.amount, V.cheque_no, DATE_FORMAT(V.cheque_date, '%d-%m-%Y') AS cheque_date, 
			DATE_FORMAT(V.reconciliation_date, '%d-%m-%Y') AS realization_date, V.remarks,
			GROUP_CONCAT(CONCAT(VO.invoice_no, ': ', VV.amount) SEPARATOR '<br />') AS bill_nos
		FROM vouchers V 
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN voucher_details VD ON V.id = VD.voucher_id
			LEFT OUTER JOIN jobs J ON VD.job_id = J.id
			LEFT OUTER JOIN voucher_vouchers VV ON V.id = VV.voucher_id
			LEFT OUTER JOIN vouchers VO ON VV.voucher_id2 = VO.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 7)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY V.id
		ORDER BY V.cheque_no";
		$query = $this->db->query($sql, array(
			$company['id'], convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}
}
