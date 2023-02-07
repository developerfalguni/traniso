<?php

class Transport extends CI_Model {
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
			''           => '',
			'Yes'        => 'label-success',
			'No'         => 'label-danger',

			'Pending'   => 'label-danger',
			'Completed' => 'label-success',
		);
	}

	function getInsuranceType(){
		return array(
			'Owner'        => 'At Owner Risk',
			'Carrier'      => 'At Carrier Risk',
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

	function countDrivers($search = '') {
		$sql = "SELECT COUNT(D.id) AS numrows
		FROM drivers D 
		WHERE D.name LIKE '%$search%' OR 
			D.address LIKE '%$search%' OR 
			D.contact LIKE '%$search%' OR 
			D.license_no LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getDrivers($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT D.id, D.name, D.address, D.contact, D.license_no, 
			DATE_FORMAT(D.license_issue_date, '%d-%m-%Y') AS license_issue_date,
			DATE_FORMAT(D.license_expiry_date, '%d-%m-%Y') AS license_expiry_date,
			S.name AS state_name
		FROM drivers D LEFT OUTER JOIN states S ON D.license_state_id = S.id
		WHERE D.name LIKE '%$search%' OR 
			D.address LIKE '%$search%' OR 
			D.contact LIKE '%$search%' OR 
			D.license_no LIKE '%$search%'
		ORDER BY D.id
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countTransporterRates($search = '') {
		$sql = "SELECT COUNT(R.id) AS numrows
		FROM transporter_rates R 
			INNER JOIN ledgers L ON R.ledger_id = L.id
			LEFT OUTER JOIN indian_ports IP ON R.from_location_id = IP.id
			LEFT OUTER JOIN locations ST ON R.to_location_id = ST.id
		WHERE IP.name LIKE '%$search%' OR
			ST.name LIKE '%$search%' OR
			L.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getTransporterRates($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT R.id, R.ledger_id, L.name AS transporter_name, IP.name AS from_location, ST.name AS to_location			
		FROM transporter_rates R 
			INNER JOIN ledgers L ON R.ledger_id = L.id
			LEFT OUTER JOIN indian_ports IP ON R.from_location_id = IP.id
			LEFT OUTER JOIN locations ST ON R.to_location_id = ST.id
		WHERE IP.name LIKE '%$search%' OR
			ST.name LIKE '%$search%' OR
			L.name LIKE '%$search%'
		ORDER BY R.id
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countRates($search = '') {
		$sql = "SELECT COUNT(R.id) AS numrows
		FROM transport_rates R INNER JOIN ledgers L ON R.ledger_id = L.id
			LEFT OUTER JOIN locations SF ON R.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON R.to_location_id = ST.id
			LEFT OUTER JOIN products PRD ON R.product_id = PRD.id
		WHERE R.company_id = ? AND (
			SF.name LIKE '%$search%' OR
			ST.name LIKE '%$search%' OR
			L.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getRates($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT R.id, R.ledger_id, L.name AS ledger_name, SF.name AS from_location, ST.name AS to_location, R.price_20, R.price_40, R.price, DATE_FORMAT(R.wef_date, '%d-%m-%Y') AS wef_date
		FROM transport_rates R INNER JOIN ledgers L ON R.ledger_id = L.id
			LEFT OUTER JOIN locations SF ON R.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON R.to_location_id = ST.id
			LEFT OUTER JOIN products PRD ON R.product_id = PRD.id
		WHERE R.company_id = ? AND (
			SF.name LIKE '%$search%' OR
			ST.name LIKE '%$search%' OR
			L.name LIKE '%$search%')
		ORDER BY R.id
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getRate($id) {
		$result = [];
		$sql = "SELECT R.id, R.company_id, R.ledger_id, L.name AS ledger_name
		FROM transport_rates R 
			INNER JOIN ledgers L ON R.ledger_id = L.id
		WHERE R.id = ?";
		$query = $this->db->query($sql, [$id]);
		$result = $query->row_array();
		if (! $result) {
			$result = array(
				'id'          => 0, 
				'company_id'  => $this->getCompanyID(),
				'ledger_id'   => 0,
				'ledger_name' => '',
				'price_20'    => array(),
				'price_40'    => array(),
				'price'       => array(),
			);
		}

		$sql = "SELECT R.id, R.from_location_id, FL.name AS from_location, R.to_location_id, TL.name AS to_location, P.id AS product_id, 
			P.name AS product_name, DATE_FORMAT(R.wef_date, '%d-%m-%Y') AS wef_date, R.price_20, R.price_40, R.price, R.weight
		FROM transport_rates R 
			LEFT OUTER JOIN locations FL ON R.from_location_id = FL.id
			LEFT OUTER JOIN locations TL ON R.to_location_id = TL.id
			LEFT OUTER JOIN products P ON R.product_id = P.id
		WHERE R.ledger_id = ?";
		$query = $this->db->query($sql, [$result['ledger_id']]);
		$result['rates'] = $query->result_array();

		return $result;
	}

	function countTripMasters($search = '') {
		$sql = "SELECT COUNT(T.id) AS numrows
		FROM (
			SELECT T.vehicle_id AS id
			FROM trip_masters T INNER JOIN vehicles V ON T.vehicle_id = V.id
				LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
				LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
				LEFT OUTER JOIN products P ON T.product_id = P.id
			WHERE T.company_id = ? AND (
				V.registration_no LIKE '%$search%' OR
				SF.name LIKE '%$search%' OR
				ST.name LIKE '%$search%' OR
				P.name LIKE '%$search%')
			GROUP BY T.vehicle_id
		) T";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getTripMasters($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT T.id, T.vehicle_id, V.registration_no, V.group_name, 
			GROUP_CONCAT(DISTINCT P.name SEPARATOR ', ') AS product_name, COUNT(SF.id) AS locations
		FROM trip_masters T INNER JOIN vehicles V ON T.vehicle_id = V.id
			LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
			LEFT OUTER JOIN products P ON T.product_id = P.id
		WHERE T.company_id = ? AND (
			V.group_name LIKE '%$search%' OR
			V.registration_no LIKE '%$search%' OR
			SF.name LIKE '%$search%' OR
			ST.name LIKE '%$search%' OR
			P.name LIKE '%$search%')
		GROUP BY T.vehicle_id
		ORDER BY V.registration_no
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getTripMaster($vehicle_id) {
		$sql = "SELECT TM.id, TM.from_location_id, FL.name AS from_location, TM.to_location_id, TL.name AS to_location, 
			TM.product_id, P.name AS product_name, TM.type, TM.fuel, TM.allowance
		FROM trip_masters TM LEFT OUTER JOIN locations FL ON TM.from_location_id = FL.id
			LEFT OUTER JOIN locations TL ON TM.to_location_id = TL.id
			LEFT OUTER JOIN products P ON TM.product_id = P.id
		WHERE TM.company_id = ? AND TM.vehicle_id = ?
		ORDER BY FL.name, TL.name";
		$query = $this->db->query($sql, array($this->_company_id, $vehicle_id));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result['trips'][$row['from_location']][] = $row;
		}
		return $result;
	}

	function countTrips($search = '') {
		$sql = "SELECT COUNT(T.id) AS numrows
		FROM (
			SELECT DISTINCT T.id
			FROM trips T LEFT OUTER JOIN vehicles V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0)
				LEFT OUTER JOIN (
						SELECT TA.trip_id, ROUND(SUM(TA.amount), 2) AS advance
						FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
						WHERE T.company_id = ?
						GROUP BY TA.trip_id
					) TA ON T.id = TA.trip_id
				LEFT OUTER JOIN (
						SELECT PA.trip_id, ROUND(SUM(PA.amount), 2) AS fuel
						FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
						WHERE T.company_id = ?
						GROUP BY PA.trip_id
					) PA ON T.id = PA.trip_id
				LEFT OUTER JOIN ledgers PS ON T.party_ledger_id = PS.id
				LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
				LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
				LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
			WHERE T.company_id = ? AND (
				T.date LIKE '%$search%' OR
				T.lr_no LIKE '%$search%' OR
				T.party_reference_no LIKE '%$search%' OR
				T.registration_no LIKE '%$search%' OR
				PS.name LIKE '%$search%' OR
				TL.name LIKE '%$search%' OR
				T.container_no LIKE '%$search%' OR
				SF.name LIKE '%$search%' OR 
				ST.name LIKE '%$search%' OR
				T.remarks LIKE '%$search%')
			GROUP BY T.id
			ORDER BY T.lr_no DESC
			) T";
		$query = $this->db->query($sql, array($this->_company_id, $this->_company_id, $this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getTrips($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT CONCAT(T.cargo_type, '/', T.id) AS id, DATE_FORMAT(T.date, '%d-%m-%Y') AS date, T.lr_no, T.party_reference_no, 
			IF(ISNULL(V.id), 0, 1) AS self, T.registration_no, PS.name AS party_name, TL.name AS transporter_name,
			T.container_no, T.container_size,
			SF.name AS from_location, ST.name AS to_location, TA.advance, PA.fuel, T.remarks
		FROM trips T LEFT OUTER JOIN vehicles V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0)
			LEFT OUTER JOIN (
					SELECT TA.trip_id, ROUND(SUM(TA.amount), 2) AS advance
					FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
					WHERE T.company_id = ?
					GROUP BY TA.trip_id
				) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
					SELECT PA.trip_id, ROUND(SUM(PA.amount), 2) AS fuel
					FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
					WHERE T.company_id = ?
					GROUP BY PA.trip_id
				) PA ON T.id = PA.trip_id
			LEFT OUTER JOIN ledgers PS ON T.party_ledger_id = PS.id
			LEFT OUTER JOIN ledgers TL ON T.transporter_ledger_id = TL.id
			LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
		WHERE T.company_id = ? AND (
			T.date LIKE '%$search%' OR
			T.lr_no LIKE '%$search%' OR
			T.party_reference_no LIKE '%$search%' OR
			T.registration_no LIKE '%$search%' OR
			PS.name LIKE '%$search%' OR
			TL.name LIKE '%$search%' OR
			T.container_no LIKE '%$search%' OR
			SF.name LIKE '%$search%' OR 
			ST.name LIKE '%$search%' OR
			T.remarks LIKE '%$search%')
		GROUP BY T.id
		ORDER BY T.lr_no DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id, $this->_company_id, $this->_company_id));
		return $query->result_array();
	}

	function get_trip_details($id) {
		$sql = "SELECT FCD.id, T.id AS trip_id, T.type, T.cargo_type, VE.id AS vehicle_id, 
			DATE_FORMAT(T.date, '%d-%m-%Y') AS date, T.registration_no,
			SF.name AS from_location, ST.name AS to_location, P.name AS product_name, 
			V.name AS vessel_name, T.trips, T.lr_no, FCD.fuel, FCD.allowance, FCD.remarks
		FROM fuel_challan_details FCD 
			INNER JOIN trips T ON FCD.trip_id = T.id
			LEFT OUTER JOIN vehicles VE ON T.registration_no = VE.registration_no
			LEFT OUTER JOIN products P ON T.product_id = P.id
			LEFT OUTER JOIN locations SF ON T.from_location_id = SF.id
			LEFT OUTER JOIN locations ST ON T.to_location_id = ST.id
			LEFT OUTER JOIN vessels V ON T.vessel_id = V.id
		WHERE FCD.fuel_challan_id = $id";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function getTripJobs($id) {
		$sql = "SELECT GROUP_CONCAT(TJ.job_id SEPARATOR ', ') AS job_id, 
			SUM(J.packages) AS packages, SUM(J.cbm) AS cbm
		FROM trip_jobs TJ LEFT OUTER JOIN jobs J ON TJ.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
		WHERE TJ.trip_id = ?
		GROUP BY TJ.trip_id";
		$query = $this->db->query($sql, array($id));
		return $query->row_array();
	}

	function getLocations($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT *
		FROM locations
		WHERE name LIKE '%$search%'
		ORDER BY id
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countLocations($search = '') {
		$sql = "SELECT COUNT(id) AS numrows
		FROM locations
		WHERE name LIKE '%$search%'
		ORDER BY id";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}
}
