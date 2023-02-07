<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Bill_item extends MY_Controller {
	var $_fields;
	var $_company;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'party'    => 'DL.name',
			'debit'    => 'DL.name',
			'credit'   => 'CL.name',
			'be_no'    => 'J.be_no',
			'billitem' => 'BI.code',
		);
		$this->_company = $this->session->userdata('default_company');
	}
	
	function index() {
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
			$search    = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search ? $search : '';
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

		$data['rows']  = $this->_getBillItem($data['from_date'], $data['to_date'], $search, $parsed_search);
		$data['years'] = explode('_', $this->_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];
		$data['page_title'] = humanize($this->_class) . " Register";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$data['company']    = $this->kaabar->getRow('companies', $this->_company['id']);
		$data['page_title'] = humanize($this->_class . ' Register');
		$data['page_desc']  = "For the Period $from_date - $to_date";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
			echo closeWindow();
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
		$rows          = $this->_getBillItem($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows);
	}

	function _getBillItem($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id2_format AS job_no, J.cargo_type, IMP.name AS importer, DL.name AS debit_ledger, 
			CL.name AS credit_ledger, PRD.name AS product, PRD.category, BI.code AS bill_item_code, BI.name AS bill_item,
			VO.invoice_no AS bill_no, DATE_FORMAT(VO.date, '%d-%m-%Y') AS bill_date, J.be_no, SUM(VJD.amount) AS amount
		FROM jobs J 
			INNER JOIN parties IMP ON J.party_id = IMP.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN voucher_details VJD ON J.id = VJD.job_id
			INNER JOIN vouchers VO ON VJD.voucher_id = VO.id
			INNER JOIN ledgers DL ON VO.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON VO.cr_ledger_id = CL.id
			INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
		WHERE (VB.company_id = ? AND VO.date >= ? AND VO.date <= ? AND VB.voucher_type_id = 5 AND J.type = 'Import')";
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
			DL.name LIKE '%$search%' OR
			J.be_no LIKE '%$search%') ";
		}
		$sql .= "
		GROUP BY J.id 
		ORDER BY J.id";
		$query = $this->db->query($sql, array(
			$this->_company['id'], convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}

}
