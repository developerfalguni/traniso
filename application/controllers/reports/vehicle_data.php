<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Vehicle_data extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'vehicle'  => 'V.registration_no',
			'operator' => "CONCAT(S.firstname, ' ', S.lastname)",
			'location' => 'VD.location',
			'purpose'  => 'VD.purpose',
			'fuel'     => 'VD.fuel_location',
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

		$data['rows'] = $this->_sql($data['from_date'], $data['to_date'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = humanize($this->_class).' Report';
		$data['page']       = $this->_clspath.$this->_class;
		$data['hide_title'] = 'true';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$data['rows']  = $this->_getVoucherPending($from_date, $to_date, $search);

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

	function _sql($from_date, $to_date, $search) {
		$result = array(
			'details' => array(),
			'summary' => array(),
		);

		$sql = "SELECT V.id, VD.vehicle_id AS vehicle_id, V.registration_no, 
			DATE_FORMAT(VD.date, '%d-%m-%Y') AS date, SUM(VD.running_hrs) AS running_hrs, 
			CONCAT(S.firstname, ' ', S.lastname) AS operator, 
			CONCAT(NR.firstname, ' ', NR.lastname) AS operator2, 
			VD.location, VD.purpose, VD.fuel_location, 
			SUM(VD.fuel_reading_supervisor) AS fuel_reading_supervisor, SUM(VD.fuel_reading_sensor) fuel_reading_sensor, 
			(SUM(VD.fuel_reading_supervisor) - SUM(VD.fuel_reading_sensor)) AS difference,
			ROUND(SUM(VD.fuel_reading_supervisor) / SUM(VD.running_hrs), 2) AS average_hr,
			'-' AS pilferage
		FROM ((vehicle_data VD INNER JOIN vehicles V ON V.id = VD.vehicle_id)
			LEFT OUTER JOIN staffs S ON VD.staff_id = S.id)
			LEFT OUTER JOIN staffs NR ON VD.night_reliver_staff_id = NR.id
		WHERE (VD.date >= ? AND VD.date <= ?)";
		$where = ' AND (';
			if (is_array($this->_parsed_search)) {
				foreach($this->_parsed_search as $key => $value)
					if (isset($this->_fields[$key]))
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
			}
		$sql .= "
		GROUP BY VD.date, VD.vehicle_id
		ORDER BY VD.date, VD.vehicle_id, VD.staff_id, VD.fuel_location";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date),
		));
		$rows  = $query->result_array();
		foreach ($rows as $row) {
			$result['details'][$row['id']][$row['date']] = $row;
		}

		$sql = "SELECT P.vehicle_id, V.registration_no, DATE_FORMAT(P.datetime, '%d-%m-%Y') AS date, SUM(P.liters) AS pilferage
		FROM pilferages P INNER JOIN vehicles V ON V.id = P.vehicle_id
		WHERE DATE_FORMAT(P.datetime, '%Y-%m-%d') >= ? AND DATE_FORMAT(P.datetime, '%Y-%m-%d') <= ?
		GROUP BY vehicle_id, DATE_FORMAT(P.datetime, '%Y-%m-%d')";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
		));
		$rows  = $query->result_array();
		foreach ($rows as $row) {
			$result['details'][$row['vehicle_id']][$row['date']]['date']      = $row['date'];
			$result['details'][$row['vehicle_id']][$row['date']]['pilferage'] = $row['pilferage'];
		}

		$sql = "SELECT V.id, COALESCE(VD.vehicle_id, PIL.vehicle_id) AS vehicle_id, V.registration_no, 
			DATE_FORMAT(COALESCE(VD.date, PIL.date), '%d-%m-%Y') AS date, SUM(VD.running_hrs) AS running_hrs, 
			GROUP_CONCAT(DISTINCT CONCAT(S.firstname, ' ', S.lastname) SEPARATOR ', ') AS operator, 
			CONCAT(NR.firstname, ' ', NR.lastname) AS operator2, 
			GROUP_CONCAT(DISTINCT VD.location SEPARATOR ', ') AS location, 
			GROUP_CONCAT(DISTINCT VD.purpose SEPARATOR ', ') AS purpose, VD.fuel_location, 
			SUM(VD.fuel_reading_supervisor) AS fuel_reading_supervisor, 
			SUM(VD.fuel_reading_sensor) AS fuel_reading_sensor,
			SUM(VD.fuel_reading_supervisor - VD.fuel_reading_sensor) AS difference,
			SUM(ROUND(VD.fuel_reading_supervisor / VD.running_hrs, 2)) AS average_hr,
			PIL.pilferage
		FROM (((vehicle_data VD INNER JOIN vehicles V ON VD.vehicle_id = V.id)
			LEFT OUTER JOIN staffs S ON VD.staff_id = S.id)
			LEFT OUTER JOIN staffs NR ON VD.night_reliver_staff_id = NR.id)
			LEFT OUTER JOIN (
				SELECT P.vehicle_id, DATE_FORMAT(P.datetime, '%Y-%m-%d') AS date, SUM(P.liters) AS pilferage
				FROM pilferages P 
				WHERE P.datetime >= ? AND P.datetime <= ?
				GROUP BY vehicle_id
			) PIL ON V.id = PIL.vehicle_id
		WHERE (VD.date >= ? AND VD.date <= ?)";
		$where = ' AND (';
			if (is_array($this->_parsed_search)) {
				foreach($this->_parsed_search as $key => $value)
					if (isset($this->_fields[$key]))
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
			}
		$sql .= "
		GROUP BY VD.vehicle_id
		ORDER BY V.registration_no, VD.fuel_location";
		$query = $this->db->query($sql, array(
			convDate($from_date) . ' 00:00:00', convDate($to_date) . ' 23:59:59',
			convDate($from_date), convDate($to_date)
		));
		$rows  = $query->result_array();
		foreach ($rows as $row) {
			$result['summary'][$row['id']] = $row;
		}

		// $sql = "SELECT PIL.vehicle_id, V.registration_no, DATE_FORMAT(PIL.datetime, '%d-%m-%Y') AS date, SUM(PIL.liters) AS pilferage
		// FROM pilferages PIL INNER JOIN vehicles V ON PIL.vehicle_id = V.id
		// WHERE (PIL.datetime >= ? AND PIL.datetime <= ?) AND vehicle_id IN (" . implode(",", $vehicles) . ")
		// GROUP BY PIL.vehicle_id";
		// $query = $this->db->query($sql, array(convDate($from_date) . ' 00:00:00', convDate($to_date) . ' 23:59:59'));
		// $rows  = $query->result_array();
		// foreach ($rows as $row) {
		// 	if (! isset($result['summary'][$row['vehicle_id']])) {
		// 		$result['summary'][$row['vehicle_id']]['date'] = $row['date'];
		// 		$result['summary'][$row['vehicle_id']]['registration_no'] = $row['registration_no'];
		// 	}
		// 	$result['summary'][$row['vehicle_id']]['pilferage'] = $row['pilferage'];
		// }
		return $result;
	}

	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$query         = $this->_getJournal($from_date, $to_date, $search, true);

		$this->load->helper('excel');
		to_excel($query, 'VehicleData_' . date('d-m-Y'));
	}	
}
