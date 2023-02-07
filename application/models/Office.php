<?php

class Office extends CI_Model {
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

	function getCalcType() {
		return array(
			'Fixed'          => 'Fixed',
			'Vouchers'       => 'Vouchers',
			'Calculative'    => 'Calculative'
		);
	}

	function getUnitType() {
		return array(
			'N/A'           => 'N/A',
			'CBM'           => 'CBM',
			'Round CBM'     => 'Round CBM',
			'Wharfage CBM'  => 'Wharfage CBM',
			'Ceil CBM'      => 'Ceil CBM',
			'Floor CBM'     => 'Floor CBM',
			'Containers 20' => 'Containers 20',
			'Containers 40' => 'Containers 40'
		);
	}

	function getVesselTypes() {
		return array(
			'Container' => 'Container', 
			'Bulk' 		=> 'Bulk'
		);
	}

	function getVesselPrefix() {
		return array(
			'MV'     => 'MV',  // 'Motor Vessel',
			'MT'     => 'MT',  // 'Motor Tanker',
			// 'SS'  => 'SS',  // 'Steam Ship',
			// 'MSV' => 'MSV', // 'Motor Stand-by Vessel',
			// 'MY'  => 'MY',  // 'Motor Yacht',
			// 'RMS' => 'RMS', // 'Royal Mail Ship',
			// 'RRS' => 'RRS', // 'Royal Research Ship',
			// 'SV'  => 'SV',  // 'Sailing Vessel',
			// 'CS'  => 'CS',  // 'Cable Ship or Cable layer',
		);
	}

	function getBerthNo() {
		$data = array('0'=>'-');
		for($i = 1; $i <= 20; $i++) 
			$data[$i] = $i;
		return $data;
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
					mkdir($cdir, 0777, true);
			}
		}
		return implode('/', $dirarr) . '/';
	}

	function countCompanies($search = '') {
		$sql = "SELECT COUNT(id) AS numrows FROM companies WHERE name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getCompanies($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT * FROM companies WHERE name LIKE '%$search%' ORDER BY name LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getBankBranch($id) {
		$sql = "SELECT BB.*, B.name AS bank_name, S.name AS state 
		FROM (bank_branches BB INNER JOIN banks B ON BB.bank_id = B.id)
			INNER JOIN states S ON BB.state_id = S.id
		WHERE BB.id = $id";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	
	function staffExists($id) {
		$sql = "SELECT id FROM staff WHERE company_id = ? AND id = ?";
		$query = $this->db->query($sql, array($company_id, $id));
		$row = $query->row_array();
		return (is_null($row) ? 0 : $row['id']);
	}
	
	function countStaffs($search = null) {
		$company_id = $this->_company_id;
		$sql = "SELECT COUNT(S.id) AS numrows 
		FROM staffs S INNER JOIN companies C ON S.company_id = C.id
		WHERE C.code LIKE '%$search%' OR 
			S.firstname LIKE '%$search%' OR 
			S.lastname LIKE '%$search%' OR 
			S.gender LIKE '%$search%' OR 
			S.address LIKE '%$search%' OR 
			S.contact LIKE '%$search%' OR 
			S.email LIKE '%$search%' OR
			S.designation LIKE '%$search%' OR 
			S.category LIKE '%$search%' OR 
			S.status LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return $row['numrows'];
	}
	
	function getStaffs($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT S.id, C.code AS company_code, S.designation, CONCAT(S.firstname, ' ', S.lastname) AS name, S.gender, 
			DATE_FORMAT(S.dob, '%d-%m-%Y') AS dob, S.address, S.contact, S.category, S.location, S.pan_no, S.status 
		FROM staffs S INNER JOIN companies C ON S.company_id = C.id
		WHERE C.code LIKE '%$search%' OR 
			S.firstname LIKE '%$search%' OR 
			S.lastname LIKE '%$search%' OR 
			S.gender LIKE '%$search%' OR 
			S.address LIKE '%$search%' OR 
			S.contact LIKE '%$search%' OR 
			S.email LIKE '%$search%' OR
			S.designation LIKE '%$search%' OR 
			S.category LIKE '%$search%' OR 
			S.pan_no LIKE '%$search%' OR 
			S.status LIKE '%$search%'
		ORDER BY S.id DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function getImage($path, $id, $image_name = '') {
		$image_url = base_url('images/photo.png');

		$docdir = $this->getDocFolder($path, $id);
		if (strlen($image_name) > 0 && file_exists(FCPATH . $path . $docdir . $image_name)) {
			$image_url = base_url($path  . $docdir . $image_name);
		}
		return $image_url;
	}

	function getAttachedStaffDocs($id, $all = 0, $attached_only = 0) {
		$sql = "SELECT SD.id, SD.staff_id, SD.staff_document_type_id, DATE_FORMAT(SD.date, '%d-%m-%Y') AS date, DT.name, SD.file
		FROM staff_documents SD INNER JOIN staff_document_types DT ON SD.staff_document_type_id = DT.id
		WHERE SD.staff_id = $id " . ($attached_only ? ' AND LENGTH(SD.file) > 0 ' : ''); 

		if ($all) {
			$sql .= "UNION
			SELECT 0 AS id, $id AS id, DT.id AS staff_document_type_id, '00-00-0000' AS date, DT.name, '' AS file
			FROM staff_document_types DT 
			WHERE DT.id NOT IN (
				SELECT staff_document_type_id FROM staff_documents WHERE staff_id = $id
			) ";
		}

		$sql .= "ORDER BY staff_document_type_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getSalaryDetails($staff_id) {
		$sql = "SELECT SD.* FROM salary_details SD WHERE SD.staff_id = ? ORDER BY SD.id";
		$query = $this->db->query($sql, array($staff_id));
		return $query->result_array();
	}

	function countResources($search = '') {
		$sql = "SELECT COUNT(R.id) AS numrows 
		FROM resources R INNER JOIN companies C ON R.company_id = C.id
		WHERE company_id = ? AND (
			category LIKE '%$search%' OR 
			type LIKE '%$search%' OR 
			model_no LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getResources($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT R.id, C.code AS company_code, R.category, R.type, 
			DATE_FORMAT(R.purchase_date, '%d-%m-%Y') AS purchase_date, R.model_no, R.active
		FROM resources R INNER JOIN companies C ON R.company_id = C.id
		WHERE company_id = ? AND (
			  category LIKE '%$search%' OR 
			  type LIKE '%$search%' OR 
			  model_no LIKE '%$search%') 
		ORDER BY category, type, model_no 
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getIssuedResources($staff_id) {
		$sql = "SELECT SR.id, SR.resource_id, R.type, R.model_no
		FROM staff_resource SR INNER JOIN resources R ON SR.resource_id = R.id
		WHERE SR.staff_id = $staff_id AND SR.return_date = '0000-00-00'
		ORDER BY R.type, R.model_no";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function countCities($search = '') {
		$sql = "SELECT COUNT(C.id) AS numrows
		FROM cities C INNER JOIN states S ON C.state_id = S.id
		WHERE C.name LIKE '%$search%' OR
			C.pincode LIKE '%$search%' OR
			S.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getCities($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT C.id, C.name, C.pincode, S.name AS state_name
		FROM cities C INNER JOIN states S ON C.state_id = S.id
		WHERE C.name LIKE '%$search%' OR
			C.pincode LIKE '%$search%' OR
			S.name LIKE '%$search%'
		ORDER BY C.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countCurrencies($search = '') {
		$sql = "SELECT COUNT(id) AS numrows FROM currencies WHERE name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getCurrencies($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT * FROM currencies WHERE name LIKE '%$search%' ORDER BY name LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function countCountries($search = '') {
		$sql = "SELECT COUNT(id) AS numrows FROM countries WHERE name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getCountries($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT * FROM countries WHERE name LIKE '%$search%' ORDER BY name LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countVessels($search = '') {
		$sql = "SELECT COUNT(V.id) AS numrows
		FROM vessels V
		WHERE V.type LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			V.voyage_no LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getVessels($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT V.id, V.type, T.code AS terminal, CONCAT(V.prefix, ' ', V.name) AS name, V.voyage_no, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date,
			DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, A.name AS agent_name, GROUP_CONCAT(VL.code SEPARATOR ', ') AS ledger_name
		FROM vessels V LEFT OUTER JOIN terminals T ON V.terminal_id = T.id
			LEFT OUTER JOIN agents A ON V.agent_id = A.id
			LEFT OUTER JOIN ledgers VL ON (VL.company_id = ? AND V.id = VL.vessel_id)
		WHERE V.type LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			V.voyage_no LIKE '%$search%'
		GROUP BY V.id
		ORDER BY V.id DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}
	
	function countPorts($search = '') {
		$sql = "SELECT COUNT(P.id) AS numrows
		FROM ports P LEFT OUTER JOIN countries C ON P.country_id = C.id
		WHERE P.code LIKE '%$search%' OR
			P.unece_code LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			C.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getPorts($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT P.id, P.code, P.unece_code, P.name, C.name AS country_name
		FROM ports P LEFT OUTER JOIN countries C ON P.country_id = C.id
		WHERE P.code LIKE '%$search%' OR
			P.unece_code LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			C.name LIKE '%$search%'
		ORDER BY P.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countPortRents($search = '') {
		$sql = "SELECT COUNT(PR.id) AS numrows
		FROM (port_rents PR INNER JOIN indian_ports IP ON PR.port_id = IP.id)
			INNER JOIN products P ON PR.product_id = P.id
		WHERE IP.name LIKE '%$search%' OR
			P.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getPortRents($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT PR.id, IP.name AS port_name, PR.berth_no, P.name AS product_name
		FROM (port_rents PR INNER JOIN indian_ports IP ON PR.port_id = IP.id)
			INNER JOIN products P ON PR.product_id = P.id
		WHERE IP.name LIKE '%$search%' OR
			P.name LIKE '%$search%'
		ORDER BY IP.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getGroundRents($id) {
		$sql = "SELECT PT.id, DATE_FORMAT(PT.wef_date, '%d-%m-%Y') AS wef_date, PT.from_day, PT.to_day, PT.rate
		FROM port_tariffs PT
		WHERE PT.port_rent_id = ?
		ORDER BY PT.wef_date";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function countTariffs($category, $search = '') {
		$sql = "SELECT COUNT(T.id) AS numrows
		FROM tariffs T INNER JOIN agents A ON T.agent_id = A.id
		WHERE A.type = ? AND A.name LIKE '%$search%'
		GROUP BY T.agent_id";
		$query = $this->db->query($sql, array($this->_company_id, $category));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getTariffs($category, $search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT T.id, T.type, A.name AS name
		FROM tariffs T INNER JOIN agents A ON T.agent_id = A.id
		WHERE A.type = ? AND A.name LIKE '%$search%'
		GROUP BY T.agent_id
		ORDER BY A.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id, $category));
		return $query->result_array();
	}

	function getTariff($id) {
		$sql = "SELECT T.type, T.ledger_id FROM tariffs T WHERE T.id = $id";
		$query = $this->db->query($sql);
		$row = $query->row_array();

		if ($row == false)
			return $row;

		$sql = "SELECT * FROM tariffs T WHERE T.type = ? AND T.ledger_id = ? ORDER BY T.id";
		$query = $this->db->query($sql, array($row['type'], $row['ledger_id']));
		$rows = $query->result_array();

		foreach ($rows as $ct) {
			$row['tariff'][$ct['id']] = $ct;
		}
		return $row;
	}

	function getDocumentTypes($search = '') {
		$sql = "SELECT D.id, P.name AS product_name, D.type, D.cargo_type, 
			COUNT(IF(D.is_compulsory = 'Yes', D.id, null)) AS compulsory,
			COUNT(IF(D.is_compulsory = 'No',  D.id, null)) AS optional
		FROM document_types D INNER JOIN products P ON D.product_id = P.id
		WHERE D.type LIKE '%$search%' OR
			D.cargo_type LIKE '%$search%' OR
			D.name LIKE '%$search%' OR
			P.name LIKE '%$search%'
		GROUP BY D.product_id, D.type, D.cargo_type
		ORDER BY D.id, P.name, D.type, D.cargo_type";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getDocumentType($id) {
		$sql = "SELECT D.product_id, D.type, D.cargo_type FROM document_types D WHERE D.id = $id";
		$query = $this->db->query($sql);
		$row = $query->row_array();

		if ($row == false)
			return $row;

		$sql = "SELECT * FROM document_types D 
		WHERE D.product_id = ? AND D.type = ? AND D.cargo_type = ?
		ORDER BY D.sr_no";
		$query = $this->db->query($sql, array($row['product_id'], $row['type'], $row['cargo_type']));
		$rows = $query->result_array();

		foreach ($rows as $d) {
			$row['documents'][$d['id']] = $d;
		}
		return $row;
	}

	function getKycDocumentTypes($search = '') {
		$sql = "SELECT D.id, TDS.name AS company_type
		FROM kyc_document_types D INNER JOIN tds_classes TDS ON D.deductee_id = TDS.id
		WHERE TDS.name LIKE '%$search%'
		GROUP BY TDS.id
		ORDER BY TDS.name, D.name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getKycDocumentType($id) {
		$sql = "SELECT D.deductee_id FROM kyc_document_types D WHERE D.id = $id";
		$query = $this->db->query($sql);
		$row = $query->row_array();

		if ($row == false)
			return $row;

		$sql = "SELECT * FROM kyc_document_types D 
		WHERE D.deductee_id = " . $row['deductee_id'] . "
		ORDER BY D.id";
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		foreach ($rows as $d) {
			$row['documents'][$d['id']] = $d;
		}
		return $row;
	}
	
	function getKycDocuments($id) {
		$sql = "SELECT D.id, DATE_FORMAT(D.date, '%d-%m-%Y') AS date, D.party_id, DT.name, D.pages
		FROM kyc_documents D INNER JOIN kyc_document_types DT ON D.kyc_document_type_id = DT.id
		WHERE D.party_id = $id
		ORDER BY D.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getBillTemplates($search = '') {
		$result = array();
		$sql = "SELECT BT.id, BT.type, BT.cargo_type, P.name AS product_name, IP.name AS indian_port, BT.berth_no, COUNT(bill_item_id) AS bill_items
		FROM (bill_templates BT INNER JOIN indian_ports IP ON BT.indian_port_id = IP.id)
			LEFT OUTER JOIN products P ON BT.product_id = P.id
		WHERE BT.company_id = ? AND (
			  BT.type LIKE '%$search%' OR
			  BT.cargo_type LIKE '%$search%' OR
			  IP.name LIKE '%$search%' OR
			  P.name LIKE '%$search%')
		GROUP BY BT.indian_port_id, BT.berth_no, BT.type, BT.cargo_type, BT.product_id
		ORDER BY BT.type, BT.cargo_type DESC, BT.indian_port_id, P.name";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}

	function getPartyBillTemplates($id) {
		$result = array();
		$sql = "SELECT BT.id, BT.type, BT.cargo_type, P.name AS product_name, IP.name AS indian_port, BT.berth_no, 
			IF(ISNULL(PR.id), 0, 1) AS rate_exists
		FROM ((bill_templates BT INNER JOIN indian_ports IP ON BT.indian_port_id = IP.id)
			LEFT OUTER JOIN products P ON BT.product_id = P.id)
			LEFT OUTER JOIN party_rates PR ON (
				PR.party_id       = ? AND 
				PR.indian_port_id = BT.indian_port_id AND 
				PR.berth_no       = BT.berth_no AND 
				PR.type           = BT.type AND 
				PR.cargo_type     = BT.cargo_type AND 
				PR.product_id     = BT.product_id)
		WHERE BT.company_id = ? 
		GROUP BY BT.indian_port_id, BT.berth_no, BT.type, BT.cargo_type, BT.product_id
		ORDER BY BT.type, BT.cargo_type DESC, BT.indian_port_id, P.name";
		$query = $this->db->query($sql, array($id, $this->_company_id));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['type']][] = $row;
		}
		return $result;
	}

	function getBillTemplate($id) {
		$sql = "SELECT BT.indian_port_id, BT.berth_no, BT.type, BT.cargo_type, BT.product_id, BT.remarks 
FROM bill_templates BT 
WHERE BT.company_id = ? AND BT.id = $id";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();

		if ($row == false)
			return $row;

		$sql = "SELECT BT.*, BI.code 
FROM bill_templates BT INNER JOIN ledgers BI ON BT.bill_item_id = BI.id
WHERE BT.company_id = ? AND
	  BT.indian_port_id = ? AND 
	  BT.berth_no = ? AND 
	  BT.type = ? AND 
	  BT.cargo_type = ? AND
	  BT.product_id = ?
ORDER BY BT.sr_no";
		$query = $this->db->query($sql, array(
			$this->_company_id, $row['indian_port_id'], $row['berth_no'], $row['type'], $row['cargo_type'], $row['product_id']
		));
		$rows = $query->result_array();

		foreach ($rows as $d) {
			$row['bill_items'][$d['id']] = $d;
		}
		return $row;
	}


	function countParties($search = '') {
		$company_id = $this->_company_id;
		$sql = "SELECT COUNT(P.id) AS numrows
		FROM parties P LEFT OUTER JOIN ledgers PL ON (PL.company_id = $company_id AND P.id = PL.party_id)
		WHERE P.name LIKE '%$search%' OR
			P.traces_name LIKE '%$search%' OR
			P.address LIKE '%$search%' OR
			P.contact LIKE '%$search%' OR
			P.pan_no LIKE '%$search%' OR
			P.tan_no LIKE '%$search%' OR
			P.iec_no LIKE '%$search%' OR
			PL.code LIKE '%$search%' OR
			PL.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getParties($search = '', $offset = 0, $limit = 25) {
		$company_id = $this->_company_id;
		$sql = "SELECT P.id, P.name, P.traces_name, P.address, P.contact, P.pan_no, P.tan_no, P.iec_no, CONCAT(PL.code, ' - ', PL.name) AS ledger_name
		FROM parties P LEFT OUTER JOIN ledgers PL ON (PL.company_id = $company_id AND P.id = PL.party_id)
		WHERE P.name LIKE '%$search%' OR
			P.traces_name LIKE '%$search%' OR
			P.address LIKE '%$search%' OR
			P.contact LIKE '%$search%' OR
			P.pan_no LIKE '%$search%' OR
			P.tan_no LIKE '%$search%' OR
			P.iec_no LIKE '%$search%' OR
			PL.code LIKE '%$search%' OR
			PL.name LIKE '%$search%'
		ORDER BY P.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getPartySites($id) {
		$sql = "SELECT PS.*, CONCAT(PS.address, ' ', C.name, ' ', C.pincode, ' ', S.name) AS address
		FROM (party_sites PS LEFT OUTER JOIN cities C ON PS.city_id = C.id)
		LEFT OUTER JOIN states S ON C.state_id = S.id
		WHERE PS.party_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function getAttachedKycs($party_id, $all = 0, $attached_only = 0) {
		$sql = "SELECT KD.id, KD.party_id, KD.kyc_document_type_id, DATE_FORMAT(KD.date, '%d-%m-%Y') AS date, 
			DT.name, KD.file
		FROM kyc_documents KD INNER JOIN kyc_document_types DT ON KD.kyc_document_type_id = DT.id
		WHERE KD.party_id = $party_id " . ($attached_only ? ' AND LENGTH(KD.file) > 0 ' : ''); 

		if ($all) {
			$sql .= "UNION
		SELECT 0 AS id, $party_id AS party_id, DT.id AS kyc_document_type_id, '00-00-0000' AS date, 
			DT.name, '' AS file
		FROM kyc_document_types DT INNER JOIN ledgers L ON (L.company_id = $this->_company_id AND L.party_id = $party_id AND DT.deductee_id = L.tds_class_id)
		WHERE DT.id NOT IN (
			SELECT kyc_document_type_id FROM kyc_documents WHERE party_id = $party_id
		) ";
		}
		$sql .= "ORDER BY kyc_document_type_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function getPartyRates($party_id, $bill_template_id) {
		$sql = "SELECT BT.*, P.name AS product_name, IP.name AS indian_port 
		FROM (bill_templates BT LEFT OUTER JOIN products P ON BT.product_id = P.id)
			INNER JOIN indian_ports IP ON BT.indian_port_id = IP.id
		WHERE BT.company_id = ? AND BT.id = ?";
				$query = $this->db->query($sql, array($this->_company_id, $bill_template_id));
				$bt = $query->row_array();

		$sql = "SELECT COALESCE(PR.id, 0) AS id, COALESCE(DATE_FORMAT(PR.wef_date, '%d-%m-%Y'), '00-00-0000') AS wef_date, 
			PR.sr_no, PR.bill_item_id, BI.code, PR.particulars, PR.calc_type, PR.unit_type, COALESCE(PR.rate, 0) AS rate
		FROM party_rates PR INNER JOIN ledgers BI ON PR.bill_item_id = BI.id
		WHERE PR.company_id = ? AND
			  PR.party_id = ? AND 
			  PR.indian_port_id = ? AND 
			  PR.berth_no = ? AND 
			  PR.type = ? AND 
			  PR.cargo_type = ? AND 
			  PR.product_id = ?
		ORDER BY PR.wef_date, PR.sr_no";
		$query = $this->db->query($sql, array($this->_company_id, $party_id, 
			$bt['indian_port_id'], $bt['berth_no'], $bt['type'], $bt['cargo_type'], $bt['product_id']
		));
		$rows = $query->result_array();
		foreach ($rows as $row)
			$bt['rates'][$row['wef_date']][] = $row;

		return $bt;
	}


	function countAgents($search = '') {
		$company_id = $this->_company_id;
		$sql = "SELECT COUNT(A.id) AS numrows
		FROM agents A LEFT OUTER JOIN ledgers AL ON (AL.company_id = $company_id AND A.id = AL.agent_id)
		WHERE A.type LIKE '%$search%' OR
			  A.code LIKE '%$search%' OR
			  A.name LIKE '%$search%' OR
			  A.address LIKE '%$search%' OR
			  A.person LIKE '%$search%' OR
			  A.contact LIKE '%$search%' OR
			  A.pan_no LIKE '%$search%' OR
			  A.cha_no LIKE '%$search%' OR
			  AL.code LIKE '%$search%' OR
			  AL.name LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getAgents($search = '', $offset = 0, $limit = 25) {
		$company_id = $this->_company_id;
		$sql = "SELECT A.id, A.type, A.code, A.name, A.address, A.person, A.contact, A.pan_no, A.cha_no, CONCAT(AL.code, ' - ', AL.name) AS ledger_name
		FROM agents A LEFT OUTER JOIN ledgers AL ON (AL.company_id = $company_id AND A.id = AL.agent_id)
		WHERE A.type LIKE '%$search%' OR
			  A.code LIKE '%$search%' OR
			  A.name LIKE '%$search%' OR
			  A.address LIKE '%$search%' OR
			  A.person LIKE '%$search%' OR
			  A.contact LIKE '%$search%' OR
			  A.pan_no LIKE '%$search%' OR
			  A.cha_no LIKE '%$search%' OR
			  AL.code LIKE '%$search%' OR
			  AL.name LIKE '%$search%'
		ORDER BY A.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countAgentRates($search = '') {
		$sql = "SELECT COUNT(AR.id) AS numrows
		FROM agent_rates AR INNER JOIN agents A ON AR.agent_id = A.id
			INNER JOIN indian_ports P ON AR.indian_port_id = P.id
			INNER JOIN products PRD ON AR.product_id = PRD.id
		WHERE A.name LIKE '%$search%' OR
			AR.type LIKE '%$search%' OR
			AR.destuffing_type LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			PRD.name LIKE '%$search%'
		GROUP BY AR.agent_id";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getAgentRates($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT AR.id, A.name AS agent_name, AR.type, P.name AS port_name, PRD.name AS product_name, AR.destuffing_type
		FROM agent_rates AR INNER JOIN agents A ON AR.agent_id = A.id
			INNER JOIN indian_ports P ON AR.indian_port_id = P.id
			INNER JOIN products PRD ON AR.product_id = PRD.id
		WHERE A.name LIKE '%$search%' OR
			AR.type LIKE '%$search%' OR
			AR.destuffing_type LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			PRD.name LIKE '%$search%'
		GROUP BY AR.agent_id
		ORDER BY A.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getAgentRate($id) {
		$sql = "SELECT AR.id, AR.agent_id, A.name AS agent_name, AR.type, AR.destuffing_type, AR.indian_port_id, 
			AR.product_id, P.name AS port_name
		FROM (agent_rates AR INNER JOIN agents A ON AR.agent_id = A.id)
			INNER JOIN indian_ports P ON AR.indian_port_id = P.id
		WHERE AR.id = ?";
		$query = $this->db->query($sql, array($id));
		$row   = $query->row_array();
		if (! $row)
			return $row;

		$sql = "SELECT AR.id, AR.particulars, AR.calc_type, AR.currency_id, AR.price_20, AR.price_40, AR.taxable
		FROM agent_rates AR WHERE AR.agent_id = ? ORDER BY AR.id";
		$query        = $this->db->query($sql, array($row['agent_id']));
		$row['rates'] = $query->result_array();
		
		return $row;
	}

	function countIssuedCheques($search = '') {
		$sql = "SELECT COUNT(IC.id) AS numrows 
		FROM (issued_cheques IC INNER JOIN companies C ON IC.company_id = C.id)
			INNER JOIN ledgers B ON IC.bank_ledger_id = B.id
		WHERE IC.company_id = ? AND (
			IC.cheque_date LIKE '%$search%' OR
			IC.cheque_no LIKE '%$search%' OR
			B.name LIKE '%$search%' OR
			IC.favor LIKE '%$search%' OR
			IC.realization_date LIKE '%$search%')";
		$query = $this->db->query($sql, array($this->_company_id));
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getIssuedCheques($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT IC.id, C.code AS company_code, B.name AS bank_name, DATE_FORMAT(IC.cheque_date, '%d-%m-%Y') AS cheque_date, LPAD(IC.cheque_no, 6, '0') AS cheque_no, IC.favor, IC.amount, DATE_FORMAT(IC.realization_date, '%d-%m-%Y') AS realization_date, IC.remarks, IC.cancelled
		FROM (issued_cheques IC INNER JOIN companies C ON IC.company_id = C.id)
			INNER JOIN ledgers B ON IC.bank_ledger_id = B.id
		WHERE IC.company_id = ? AND (
			IC.cheque_date LIKE '%$search%' OR
			IC.cheque_no LIKE '%$search%' OR
			B.name LIKE '%$search%' OR
			IC.favor LIKE '%$search%' OR
			IC.realization_date LIKE '%$search%')
		ORDER BY IC.cheque_no
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($this->_company_id));
		return $query->result_array();
	}
}

