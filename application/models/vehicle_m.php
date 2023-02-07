<?php

class Vehicle_m extends CI_Model {
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
	
	function getLabelClass() {
		return array(
			''           => '',
			'Yes'        => 'label-success',
			'No'         => 'label-danger',

			'Bulk' 		=> 'label-info',
			'Container'	=> 'label-warning',

			'Check'      => 'label-info',
			'Checked'    => 'label-info',
			'Verify'     => 'label-warning',
			'Verified'   => 'label-warning',
			'Authorise'  => 'label-success',
			'Authorised' => 'label-success'
		);
	}
	
	function getDocFolder($path, $id) {
		$dirarr = array();
		for($i=0; $i < strlen($id); $i++) {
			$dirarr[] = substr($id, $i, 1);
		}
		$dir = $path . '/' . implode('/', $dirarr);
		
		if (! file_exists($dir)) {
			$cdir = $path;
			foreach ($dirarr as $dir) {
				$cdir .= '/'.$dir;
				// Skip if dir already created.
				if (! file_exists($cdir))
					mkdir($cdir);
			}
		}
		return implode('/', $dirarr) . '/';
	}

	function getImage($path, $id, $image_name = '') {
		$image_url = base_url('images/photo.png');

		$docdir = $this->getDocFolder($path, $id);
		if (strlen($image_name) > 0 && file_exists(FCPATH . $path . $docdir . $image_name)) {
			$image_url = base_url($path  . $docdir . $image_name);
		}
		return $image_url;
	}

	function countVehicles($search = '') {
		$sql = "SELECT COUNT(V.id) AS numrows 
		FROM vehicles V LEFT OUTER JOIN ledgers VL ON V.id = VL.vehicle_id
		WHERE V.company_id = ? AND (
			V.type LIKE '%$search%' OR
			V.registration_no LIKE '%$search%' OR
			V.make LIKE '%$search%' OR
			V.model_no LIKE '%$search%' OR
			VL.code LIKE '%$search%' OR
			VL.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getVehicles($search = '', $offset = 0, $limit = 25) {
		$company_id = $this->_company_id;
		$sql = "SELECT V.id, V.type, V.group_name, V.registration_no, V.make, V.model_no, V.mfg_year, V.track_data, 
			CONCAT(VL.code, ' - ', VL.name) AS ledger_name 
		FROM vehicles V LEFT OUTER JOIN ledgers VL ON V.id = VL.vehicle_id
		WHERE V.company_id = ? AND (
			V.type LIKE '%$search%' OR
			V.registration_no LIKE '%$search%' OR
			V.make LIKE '%$search%' OR
			V.model_no LIKE '%$search%' OR
			VL.code LIKE '%$search%' OR
			VL.name LIKE '%$search%')
		ORDER BY model_no 
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getVehicleDocuments($id) {
		$sql = "SELECT VD.id, DATE_FORMAT(VD.date, '%d-%m-%Y') AS date, 
			VD.name, DATE_FORMAT(VD.validity, '%d-%m-%Y') AS validity, VD.file, VD.archive 
		FROM vehicle_documents VD
		WHERE VD.vehicle_id = ?
		ORDER BY VD.name, VD.validity DESC";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function countVehicleData($search = '') {
		$sql = "SELECT COUNT(TMP.date) AS numrows 
		FROM (SELECT VD.date
			FROM vehicle_data VD INNER JOIN vehicles V ON VD.vehicle_id = V.id
			WHERE V.registration_no LIKE '%$search%' OR
		  		DATE_FORMAT(VD.date, '%d-%m-%Y') LIKE '%$search%'
		  	GROUP BY VD.date
		) TMP";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getVehicleData($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT DATE_FORMAT(VD.date, '%d-%m-%Y') AS date, 
			TD.fuel AS tanker_fuel,
			SUM(IF(VD.fuel_location = 'Tanker', VD.fuel_reading_supervisor, 0)) AS fuel_sup_tanker, 
			SUM(IF(VD.fuel_location = 'Tanker', VD.fuel_reading_sensor, 0)) AS fuel_sen_tanker,
			SUM(IF(VD.fuel_location = 'Tanker', VD.fuel_reading_sensor, 0)) AS fuel_sen_tanker,
			SUM(IF(VD.fuel_location != 'Tanker', VD.fuel_reading_supervisor, 0)) AS fuel_sup_pump, 
			SUM(IF(VD.fuel_location != 'Tanker', VD.fuel_reading_sensor, 0)) AS fuel_sen_pump,
			(SUM(VD.fuel_reading_supervisor) - SUM(VD.fuel_reading_sensor)) AS difference
		FROM (vehicle_data VD INNER JOIN vehicles V ON VD.vehicle_id = V.id)
			LEFT OUTER JOIN tanker_data TD ON VD.date = TD.date
		WHERE V.registration_no LIKE '%$search%' OR
			DATE_FORMAT(VD.date, '%d-%m-%Y') LIKE '%$search%'
		GROUP BY VD.date DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getAllVehicleData($date) {
		$sql = "SELECT V.type, V.id AS vehicle_id, V.registration_no, VD.id, VD.reading_hrs, S.id AS staff_id, CONCAT(S.firstname, ' ', S.middlename, ' ', S.lastname) AS staff, VD.mode, NRS.id AS night_reliver_staff_id, CONCAT(NRS.firstname, ' ', NRS.middlename, ' ', NRS.lastname) AS night_staff, VD.location, VD.purpose, VD.fuel_location, DATE_FORMAT(VD.fuel_time_supervisor, '%d-%m-%Y') AS fuel_date_supervisor, DATE_FORMAT(VD.fuel_time_supervisor, '%H:%i') AS fuel_time_supervisor, VD.fuel_reading_supervisor, DATE_FORMAT(VD.fuel_time_sensor, '%d-%m-%Y') AS fuel_date_sensor, DATE_FORMAT(VD.fuel_time_sensor, '%H:%i') AS fuel_time_sensor, VD.fuel_reading_sensor
		FROM ((vehicle_data VD INNER JOIN vehicles V ON (V.id = VD.vehicle_id AND VD.date = ?))
			LEFT OUTER JOIN staffs S ON VD.staff_id = S.id)
			LEFT OUTER JOIN staffs NRS ON VD.night_reliver_staff_id = NRS.id
		ORDER BY V.registration_no";
		$query = $this->db->query($sql, array($date));
		return $query->result_array();
	}

	function countPilferages($search = '') {
		$sql = "SELECT COUNT(A.id) AS numrows 
		FROM (
			SELECT 1 AS id
			FROM pilferages P INNER JOIN vehicles V ON (P.vehicle_id = V.id AND V.company_id = ?)
			WHERE P.datetime LIKE '%$search%' OR
				V.registration_no LIKE '%$search%' OR
				P.liters LIKE '%$search%'
			GROUP BY vehicle_id, DATE_FORMAT(P.datetime, '%Y-%m-%d')
		) A";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getPilferages($search = '', $offset = 0, $limit = 25) {
		$company_id = $this->_company_id;
		$sql = "SELECT P.id, V.registration_no, DATE_FORMAT(P.datetime, '%d-%m-%Y %H:%i') AS datetime, SUM(P.liters) AS liters
		FROM pilferages P INNER JOIN vehicles V ON (P.vehicle_id = V.id AND V.company_id = ?)
		WHERE P.datetime LIKE '%$search%' OR
			V.registration_no LIKE '%$search%' OR
			P.liters LIKE '%$search%'
		GROUP BY vehicle_id, DATE_FORMAT(P.datetime, '%Y-%m-%d')
		ORDER BY P.datetime
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getPilferage($id) {
		$sql   = "SELECT id, DATE_FORMAT(datetime, '%d-%m-%Y') AS date, vehicle_id FROM pilferages WHERE id = ?";
		$query = $this->db->query($sql, array($id));
		$data  = $query->row_array();

		if($data == false) return false;

		$sql = "SELECT P.id, DATE_FORMAT(P.datetime, '%d-%m-%Y') AS date, DATE_FORMAT(P.datetime, '%H:%i') AS time, P.liters
			FROM pilferages P
			WHERE vehicle_id = ? AND DATE_FORMAT(P.datetime, '%d-%m-%Y') = ?
			ORDER BY P.datetime";
		$query = $this->db->query($sql, array($data['vehicle_id'], $data['date']));
		$data['pilferages'] = $query->result_array();
		return $data;
	}
}

