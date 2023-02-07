<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Tds_party extends MY_Controller {
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
	
	function index() {
		$from_date   = null;
		$to_date     = null;
		
		if ($this->input->post('from_date')) {
			$from_date   = $this->input->post('from_date');
			$to_date     = $this->input->post('to_date');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
		}
		
		if ($from_date == null) {
			$from_date   = $this->session->userdata($this->_class.'_from_date');
			$to_date     = $this->session->userdata($this->_class.'_to_date');
		}

		$data['from_date']   = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']     = $to_date ? $to_date : date('d-m-Y');
		
		$data['rows'] = $this->_getTDSParties(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['years'] = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = humanize($this->_class) . ' Register';


		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getTDSParties($from_date, $to_date, $query_only = false) {
		$sql = "SELECT DISTINCT DL.name AS party_name, 
			COALESCE(P.address, A.address, S.address) AS address, 
			COALESCE(P.pan_no, A.pan_no, S.pan_no) AS pan_no
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			INNER JOIN tds_classes CT ON CL.tds_class_id = CT.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			LEFT OUTER JOIN tds_classes DT ON DL.tds_class_id = DT.id
			LEFT OUTER JOIN vouchers VTS ON V.tds_payment_id = VTS.id
			LEFT OUTER JOIN parties P ON DL.party_id = P.id
			LEFT OUTER JOIN agents A ON DL.agent_id = A.id
			LEFT OUTER JOIN staffs S ON DL.staff_id = S.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND CL.tds_class_id > 0 AND CT.type = 'Payment'
		ORDER BY CT.id";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		if ($query_only)
			return $query;
		return $query->result_array();
	}

	function preview($pdf = 0) {
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');
		
		$data['rows'] = $this->_getTDSParties(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class) . ' Register';
		$data['page_desc'] = "For the Period $from_date - $to_date";

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
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');
		
		$query = $this->_getTDSParties(convDate($from_date), convDate($to_date), 1);

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
