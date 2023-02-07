<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Receivable extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'job_no'  => 'J.id2_format',
			'bill_no' => "CONCAT(V.id2_format, '/', V.id3)",
			'debit'   => 'DL.name',
			'credit'  => 'CL.name',
			'balance' => ''
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

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Receivable Report";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search) {
		$company = $this->session->userdata('default_company');

		$sql = "SELECT V.id, CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url, V.id2, V.id3, V.id2_format, 
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, V.dr_ledger_id, DL.name AS debit_name, 
			V.cr_ledger_id, CL.name AS credit_name, CONCAT(VT1.name,'/edit/',VB1.id,'/',JV.id) AS url1,
			V.amount, V.advance_amount, (V.amount - V.advance_amount) AS net_amount,
			SUM(COALESCE(VV.amount, 0)) AS receipt_amount, ((V.amount - V.advance_amount) - SUM(COALESCE(VV.amount, 0))) AS balance_amount
		FROM vouchers V 
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN voucher_vouchers VV ON V.id = VV.voucher_id2
			LEFT OUTER JOIN vouchers JV ON VV.voucher_id = JV.id 
			LEFT OUTER JOIN voucher_books VB1 ON JV.voucher_book_id = VB1.id
			LEFT OUTER JOIN voucher_types VT1 ON VB1.voucher_type_id = VT1.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id IN (3,4))";
		$where = ' AND (';
		$having = '';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					if ($key == 'balance') 
						$having .= "HAVING balance_amount " . ($value == 0 ? '=' : '>') . " 0";
					else
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY V.id
		$having
		ORDER BY V.date";
		$query = $this->db->query($sql, array(
			$company['id'], convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}

	function preview($pdf = 0) {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$data['rows']  = $this->_get($from_date, $to_date, $search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = 'Bills Receivables';
		$data['page_desc'] = "For the Period $from_date - $to_date";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.'/receivable_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
			$this->kaabar->save($this->_table, array('printed' => 'Yes'), array('id' => $id));
		}
		else {
			$this->load->view($this->_clspath.'/receivable_preview', $data);
		}
	}

	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows, array('id', 'url', 'id2', 'id3', 'url1', 'dr_ledger_id', 'cr_ledger_id'));
	}
}
