<?php

class Import extends CI_Model {
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
			''			=> '',
			'Yes' 		=> 'badge-success',
			'No' 		=> 'badge-danger',

			'Bulk' 		=> 'badge-info',
			'Container'	=> 'badge-warning',

			'Factory' => 'badge-success',
			'Docks'   => 'badge-info',
			'CFS'     => 'badge-info',
			'Godown'  => 'badge-warning',

			'FCL'    => 'badge-success',
			'LCL'    => 'badge-danger',
			'Air'    => 'badge-info',
			'Parcel' => 'badge-warning',
			
			'Completed' => 'badge-success',
			'Pending'   => 'badge-danger',
			'Bills'     => 'badge-warning',
			'Delivery'  => 'badge-info',
			'Cancelled' => 'badge-danger',

			'Clearing'       => 'badge-success',
			'Forwarding'     => 'badge-warning',
			'Transportation' => 'badge-info',
		);
	}
	
	function getJobTypes() {
		return array(
			'Import' => 'Import',
			'Export' => 'Export'
		);
	}

	function getCargoTypes() {
		return array(
			'Bulk' 		=> 'Bulk',
			'Container' => 'Container'
		);
	}

	function getContainerShipmentTypes() {
		return array(
			'FCL'    => 'FCL',
			'LCL'    => 'LCL',
			'Air'    => 'Air',
			'Parcel' => 'Parcel'
		);
	}

	function getBulkShipmentTypes() {
		return array(
			'Bulk'          => 'Bulk',
			'Break Bulk'    => 'Break Bulk',
			'Project Cargo' => 'Project Cargo',
		);
	}

	function getIncoTerms() {
		return array(
			'EXW'	=> 'EXW',
			'FCA'   => 'EXW',
			'CPT' 	=> 'CPT',
			'CIP' 	=> 'CIP',
			'DPU' 	=> 'DPU',
			'DAP' 	=> 'DAP',
			'DDP' 	=> 'DDP',
			'fa' 	=> 'fa',
			'FOB' 	=> 'FOB',
			'CFR' 	=> 'CFR',
			'CIF' 	=> 'CIF',
		);
	}

	function getStatus() {
		return array(
			'Pending'   => 'Pending',
			'Program'   => 'Program',
			'Delivery'  => 'Delivery',
			'Bills'     => 'Bills',
			'Completed' => 'Completed'
		);
	}

	
	function getFreightTerms() {
		return array(
			'Collect' => 'Collect',
			'Post Collect' => 'Post Collect'
		);
	}

	function getDeliveryTypes() {
		return array(
			'Factory' => 'Factory De-Stuffing',
			'Doc' => 'Doc De-Stuffing'
		);
	}

	function getInvoiceTypes() {
		return array(
			'Single' => 'Single',
			'Multiple' => 'Multiple'
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

	function jobsExists($id) {
		$query = $this->db->query("SELECT id FROM jobs WHERE id = $id");
		$row = $query->row_array();
		if ($row == false)
			return 0;
		return $row['id'];
	}

	function blExists($bl_no) {
		$query = $this->db->query("SELECT id FROM jobs WHERE type = 'Import' AND bl_no = ?", 
			array($bl_no)
		);
		$row = $query->row_array();
		if ($row == false)
			return 0;
		return $row['id'];
	}

	function getJobsInfo($job_id, $links = false, $url = false) {
		$sql = "SELECT J.id, DATE_FORMAT(J.date, '%d-%m-%Y') AS date, J.type, J.cargo_type, 
			J.bl_no, DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date,
			J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date, 
			PD.name AS product_name, 
			CONCAT(J.packages, ' ', PK.code) AS packages,
			CONCAT(J.net_weight, ' ', J.net_weight_unit) AS units,
			P.name AS party_name, IP.name AS port_name
		FROM (((jobs J INNER JOIN parties P ON J.party_id = P.id)
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id)
			LEFT OUTER JOIN package_types PK ON J.package_type_id = PK.id)
			LEFT OUTER JOIN products PD ON J.product_id = PD.id
		WHERE J.id = $job_id";
		$query = $this->db->query($sql);
		$data = $query->row_array();

		$high_seas = $this->import->getHighSeas($job_id);
		$data['high_seas'] = array_pop($high_seas);

		if ($links) {
			$data['links'] = $links;
			$data['url'] = $url;
		}
		return $data;
	}

	function getJobsList($job_id) {

		$this->db->select('id, id2_format');
		$this->db->from('jobs');
		$this->db->where('type', 'Import');
		$this->db->where_not_in('id', $job_id);
		$query = $this->db->get();
		$result = $query->result_array();
		
	}
	
	function countJobs($search = '') {
		$sql = "SELECT COUNT(Temp.id) AS numrows
		FROM (
			SELECT J.id
			FROM jobs J INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
				LEFT OUTER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN import_details ID ON J.id = ID.job_id
				LEFT OUTER JOIN products PD ON J.product_id = PD.id
				LEFT OUTER JOIN high_seas HSS ON J.id = HSS.job_id
				LEFT OUTER JOIN parties HP ON HSS.party_id = HP.id
				LEFT OUTER JOIN vessels VL ON J.vessel_id = VL.id
				LEFT OUTER JOIN containers C ON J.id = C.job_id
			WHERE (J.type = 'Import') AND (
				CONCAT(J.type, ' - ', J.cargo_type) LIKE '%$search%' OR
				DATE_FORMAT(J.date, '%d-%m-%Y') LIKE '%$search%' OR
				J.id2_format LIKE '%$search%' OR
				J.be_type LIKE '%$search%' OR
				J.bl_no LIKE '%$search%' OR
				J.be_no LIKE '%$search%' OR
				P.name LIKE '%$search%' OR
				HP.name LIKE '%$search%' OR
				CONCAT(VL.name, ' ', VL.voyage_no) LIKE '%$search%' OR
				C.number LIKE '%$search%' OR
				IP.name LIKE '%$search%' OR
				PD.name LIKE '%$search%' OR
				J.status LIKE '%$search%')
			GROUP BY J.id
		) Temp";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getJob($job_id) {
		
		$sql = "SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			/*old 
			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 
			*/
			B.name as branch_name, C.consignee_name as consignee_name, CC.consignee_name as buyer_name, CCC.consignee_name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = ? AND J.type = 'Import')";

		$query = $this->db->query($sql, array($job_id));

		return $query->row_array();
	}

	function getJobs($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT J.id, J.id2_format, J.be_type, J.cargo_type, J.is_coastal, PD.name AS product_name, DATE_FORMAT(J.date, '%d-%m-%Y') AS date, 
			CONCAT(J.bl_no, ' / ', DATE_FORMAT(bl_date, '%d-%m-%Y')) AS bl_no_date,
			CONCAT(J.be_no, ' / ', DATE_FORMAT(be_date, '%d-%m-%Y')) AS be_no_date,
			P.name AS party_name, GROUP_CONCAT(DISTINCT HP.name ORDER BY HSS.id SEPARATOR ', ') AS hss_parties, 
			CONCAT(VL.name, ' ', VL.voyage_no) AS vessel_name,
			CONCAT(J.packages, ' ', PK.code) AS pieces,
			CONCAT(J.net_weight, ' ', J.net_weight_unit) AS weight,
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			IP.name AS port_name, J.status
		FROM jobs J INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN import_details ID ON J.id = ID.job_id
			LEFT OUTER JOIN package_types PK ON J.package_type_id = PK.id
			LEFT OUTER JOIN products PD ON J.product_id = PD.id
			LEFT OUTER JOIN high_seas HSS ON J.id = HSS.job_id
			LEFT OUTER JOIN parties HP ON HSS.party_id = HP.id
			LEFT OUTER JOIN vessels VL ON J.vessel_id = VL.id
			LEFT OUTER JOIN containers C ON J.id = C.job_id
		WHERE (J.type = 'Import') AND (
			CONCAT(J.type, ' - ', J.cargo_type) LIKE '%$search%' OR
			DATE_FORMAT(J.date, '%d-%m-%Y') LIKE '%$search%' OR
			J.id2_format LIKE '%$search%' OR
			J.be_type LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			HP.name LIKE '%$search%' OR
			CONCAT(VL.name, ' ', VL.voyage_no) LIKE '%$search%' OR
			C.number LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			PD.name LIKE '%$search%' OR
			J.status LIKE '%$search%')
		GROUP BY J.id
		ORDER BY J.date DESC, id2 DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function createJobNo($id, $date) {
		$years      = explode('_', $this->kaabar->getFinancialYear($date));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';

		$this->db->query("LOCK TABLES jobs WRITE");
		$query = $this->db->query("SELECT MAX(id2) AS id2 FROM jobs WHERE type = 'Import' AND date >= ? AND date <= ?", 
			array($start_date, $end_date));
		$id_row = $query->row_array();
		$id_row['id2']++;
		$id_row['idkaabar_code'] = 'IMP/' . str_pad($id_row['id2'], 4, '0', STR_PAD_LEFT) . '/' . substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2);
		$this->db->update('jobs', array('id2' => $id_row['id2'], 'idkaabar_code' => $id_row['idkaabar_code']), "id = $id");
		$this->db->query("UNLOCK TABLES");
	}

	function getDos($job_id) {
		$sql = "SELECT *, DATE_FORMAT(date, '%d-%m-%Y') AS date
		FROM issue_dos
		WHERE job_id = ?
		ORDER BY id";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}
	
	function getHighSeas($id) {
		$sql = "SELECT HSS.id, HSS.party_id, HL.name, HL.address
		FROM high_seas HSS INNER JOIN parties HL ON HSS.party_id = HL.id
		WHERE HSS.job_id = $id
		ORDER BY HSS.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getContainerList($id) {
		$sql = "SELECT CONCAT(CT.size, ' ', CT.code) AS size, C.number, C.seal, C.container_type_id
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE C.job_id = $id
		ORDER BY CT.size, C.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function countContainers($job_id, $search = '') {
		$sql = "SELECT COUNT(C.id) AS numrows
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE C.job_id = ? AND
			(C.number LIKE '%$search%' OR
			C.seal LIKE '%$search%' OR
			CT.code LIKE '%$search%' OR
			CT.name LIKE '%$search%')";
		$query = $this->db->query($sql, array($job_id));
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getContainers($child_job_id, $search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT C.*, CT.size, CT.code, CT.name, DATE_FORMAT(C.seal_date, '%d-%m-%Y') AS seal_date
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE C.job_id = $child_job_id AND
			(C.number LIKE '%$search%' OR
			C.seal LIKE '%$search%' OR
			CT.code LIKE '%$search%' OR
			CT.name LIKE '%$search%')
		ORDER BY C.id
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getDocuments($job_id) {
		$sql = "SELECT D.id, DATE_FORMAT(D.date, '%d-%m-%Y') AS date, D.document_no, D.name
		FROM documents D
		WHERE D.job_id = ?
		ORDER BY date DESC";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}
	

	function addPendingDocuments($job_id) {
		$sql = "INSERT INTO attached_documents (job_id, document_type_id, is_compulsory, is_pending)
			SELECT $job_id, DT.id, DT.is_compulsory, DT.is_pending
		FROM document_types DT INNER JOIN jobs J ON (DT.product_id = J.product_id AND DT.type = J.type AND DT.cargo_type = J.cargo_type)
		WHERE J.id = $job_id AND DT.name NOT LIKE 'Delivery Order%'";

		$this->db->query($sql);
	}

	function getPendingDocuments($job_id) {
		$sql = "SELECT AD.id, AD.document_type_id, DT.name, AD.remarks, AD.is_pending, 
			AD.received, DATE_FORMAT(AD.received_date, '%d-%m-%Y') AS received_date, AD.file
		FROM attached_documents AD INNER JOIN document_types DT ON (AD.document_type_id = DT.id)
		WHERE AD.job_id = $job_id AND AD.is_pending = 'Yes'
		ORDER BY DT.sr_no, AD.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getAttachedDocuments($job_id, $all = 0, $attached_only = 0) {
		$sql = "SELECT AD.id, AD.job_id, AD.document_type_id, DT.sr_no, DATE_FORMAT(AD.date, '%d-%m-%Y') AS date, 
			DT.name, DT.is_compulsory, AD.is_pending, AD.received, DATE_FORMAT(AD.received_date, '%d-%m-%Y') AS received_date, 
			AD.file, AD.pages, AD.visible_user_ids, AD.remarks, AD.action
		FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
		WHERE AD.job_id = $job_id " . ($attached_only ? ' AND LENGTH(AD.file) > 0 ' : ''); 

		if ($all) {
			$sql .= "UNION
				SELECT 0 AS id, $job_id AS job_id, DT.id AS document_type_id, DT.sr_no, '00-00-0000' AS date, 
					DT.name, DT.is_compulsory, DT.is_pending, 'No' AS received, '00-00-0000' AS received_date, 
					'' AS file, 0 AS pages, '' AS visible_user_ids, '' AS remarks, '' AS action 
				FROM document_types DT INNER JOIN jobs J ON (DT.product_id = J.product_id AND DT.type = J.type AND DT.cargo_type = J.cargo_type)
				WHERE J.id = $job_id AND DT.id NOT IN (
					SELECT document_type_id FROM attached_documents WHERE job_id = $job_id
				) ";
		}

		$sql .= "ORDER BY is_compulsory DESC, is_pending DESC, sr_no, id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getAttachedDocument($job_id, $id) {
		$sql = "SELECT AD.id, AD.job_id, DATE_FORMAT(AD.date, '%d-%m-%Y') AS date, AD.document_type_id, DT.sr_no, DT.name, 
			DT.is_compulsory, DT.is_pending, AD.received, AD.file, AD.pages, AD.visible_user_ids, AD.remarks, AD.action
		FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
		WHERE AD.job_id = $job_id AND AD.id = $id";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function updatePayments ($job_id, $vdid, $bill_item_id) {
		$pid 	= $this->kaabar->getField('import_details', $job_id, 'job_id', 'id');
		
		$bicode = $this->kaabar->getField('ledgers', $bill_item_id, 'id', 'code');
		switch ($bicode) {
			case  'CD':
				$this->kaabar->save('import_details', array('cd_voucher_id' => $vdid), array('id' => $pid));
				break;
			case 'STAMP':
				$this->kaabar->save('import_details', array('sd_voucher_id' => $vdid), array('id' => $pid));
				break;
			case 'PQ':
				$this->kaabar->save('import_details', array('ppq_voucher_id' => $vdid), array('id' => $pid));
				break;
			case 'WH':
				$this->kaabar->save('import_details', array('wh_voucher_id' => $vdid), array('id' => $pid));
				break;
			case 'LINE':
				$this->kaabar->save('import_details', array('line_voucher_id' => $vdid), array('id' => $pid));
				break;
		}
	}

	function calculateWharfage($job_id) {
		$data = FALSE;
		$job = $this->kaabar->getRow('jobs', $job_id);
		if ($job['cargo_type'] == 'Container')
			return $data;
		
		$v = $this->kaabar->getRow('vessels', $job['vessel_id']);
		$p = $this->kaabar->getRow('import_details', array('job_id' => $job_id));

		$sql   = "SELECT * FROM port_rents PR WHERE port_id = ? AND FIND_IN_SET(?, berth_no) AND product_id = ?";
		$query = $this->db->query($sql, array($job['indian_port_id'], (intval($v['berth_no']) == 0 ? 1 : $v['berth_no']), $job['product_id']));
		$pr    = $query->row_array();

		$rate = ($p['wh_rate'] > 0 ? $p['wh_rate'] : $pr['wharfage']);
		$stax = ($p['wh_stax'] > 0 ? $p['wh_stax'] : $pr['service_tax']);
		$tds  = ($p['wh_tds']  > 0 ? $p['wh_tds']  : $pr['tds']);

		// Calculate Wharfage
		if ($job['cargo_type'] == 'Bulk' && $pr) {
			$cbm = ($job['cbm'] > 0 ? $job['cbm'] : $job['net_weight']);
			if ($job['net_weight_unit'] == 'MTS')
				$cbm = round($job['net_weight'] * 1.42, 4);
			$cbm = ((ceil($cbm) - round($cbm)) <= 0 ? ceil($cbm) : (floor($cbm) + 0.5));
			$wharfage = round($cbm * $rate);
			$wh_stax  = round($wharfage * $stax / 100);
			$wh_tds   = round($wharfage * $tds / 100);
			$amount   = round($wharfage + $wh_stax);

			$data = array(
				// 'cbm'		  => $cbm,
				'rate'        => $rate, 
				'wharfage'    => $wharfage, 
				'stax'        => $stax, 
				'stax_amount' => $wh_stax, 
				'tds'         => $tds, 
				'tds_amount'  => $wh_tds, 
				'amount'      => $amount
			);
		}

		return $data;
	}

	
	function countWharfages($search = '') {
		$sql = "SELECT COUNT(W.id) AS numrows
		FROM (((import_details W INNER JOIN jobs J ON W.job_id = J.id)
			INNER JOIN parties P ON J.party_id = P.id)
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id)
			LEFT OUTER JOIN ledgers CHA ON J.cha_id = CHA.id
		WHERE (J.vessel_id > 0 AND J.cha_id > 0) AND 
			  (J.bl_no LIKE '%$search%' OR
			  J.be_no LIKE '%$search%' OR
			  P.name LIKE '%$search%' OR
			  V.name LIKE '%$search%' OR
			  CHA.name LIKE '%$search%')";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getWharfages($search, $parsed_search) {
		$sql = "SELECT W.id, W.job_id, W.wh_no, DATE_FORMAT(W.wh_date, '%d-%m-%Y') AS wh_date, W.wh_voucher_no, P.name AS party_name, 
			J.bl_no, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, PRD.name AS product_name, PRD.category, CHA.name AS cha_name, 
			J.packages AS pieces, 
			IF(J.cbm > 0, J.cbm, IF(J.net_weight_unit = 'MTS', ROUND(J.net_weight*1.42, 4), J.net_weight)) AS cbm,
			W.wh_amount, W.wh_stax_amount, W.wh_tds_amount, (W.wh_amount + W.wh_stax_amount - W.wh_tds_amount) AS net_amount
		FROM ((((import_details W INNER JOIN jobs J ON W.job_id = J.id)
			INNER JOIN parties P ON J.party_id = P.id)
			INNER JOIN products PRD ON J.product_id = PRD.id)
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id)
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
		WHERE (J.cargo_type = 'Bulk' AND 
			J.vessel_id > 0 AND 
			J.cha_id > 0 AND 
			J.indian_port_id != 72 AND 
			FIND_IN_SET(13, V.berth_no) = 0)";
				$where = ' AND (';
				if (is_array($parsed_search)) {
					foreach($parsed_search as $key => $value)
						if (isset($this->_fields[$key])) {
							if ($key == 'status' AND $value == "Pending")
								$where .= "LENGTH(TRIM(" . $this->_fields[$key] . ")) = 0 AND ";
							else if ($key == 'status' AND $value == "Completed")
								$where .= "LENGTH(TRIM(" . $this->_fields[$key] . ")) > 0 AND ";
							else
								$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
						}
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ') ';
				}
				else {
					$sql .= $where . "J.bl_no LIKE '%$search%' OR
			  P.name LIKE '%$search%' OR
			  CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) LIKE '%$search%' OR
			  CHA.name LIKE '%$search%') ";
				}
				$sql .= "
		ORDER BY V.id DESC, W.id DESC
		LIMIT 0, 500";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function countBills($job_id) {
		$sql = "SELECT COUNT(V.id) AS numrows
		FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			LEFT OUTER JOIN voucher_job_details VJD ON V.id = VJD.voucher_id
		WHERE VB.voucher_type_id IN (3, 4) AND 
			  VJD.job_id = ?";
		$query = $this->db->query($sql, array($job_id));
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getBills($job_id) {
		$sql = "SELECT CONCAT(VT.name, '/edit/', VB.id, '/', V.id2, '/', V.id3) AS id, C.code AS company,
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, V.id2_format, V.amount
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
			INNER JOIN companies C ON VB.company_id = C.id)
			LEFT OUTER JOIN voucher_job_details VJD ON V.id = VJD.voucher_id
		WHERE VB.voucher_type_id IN (3, 4) AND 
			V.job_id = ?
		GROUP BY V.id";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function calcCFS($job_id) {
		$this->load->helper('datefn');

		$result = array(
			'amount_20' => 0,
			'amount_40' => 0,
			'amount'    => 0,
		);

		$job   = $this->kaabar->getRow('jobs', $job_id);
		$query = $this->db->query("SELECT D.*, 
			DATE_FORMAT(D.gate_out, '%Y-%m-%d') AS gate_out,
			DATE_FORMAT(IF(gatepass_date = '0000-00-00 00:00:00', NOW(), gatepass_date), '%Y-%m-%d') AS gatepass_date, CT.size 
		FROM (deliveries_stuffings D INNER JOIN containers C ON D.container_id = C.id)
			INNER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE D.job_id = ?", array($job_id));
		$containers = $query->result_array();
		$rates      = $this->kaabar->getRows('agent_rates', $job['cfs_id'], 'agent_id');

		foreach ($rates as $r) {
			$tariffs = $this->kaabar->getRows('agent_tariffs', array('agent_rate_id' => $r['id']));
			$amount  = 0;
			$stax    = 0;
			$edu     = 0;
			$hedu    = 0;
			foreach($containers as $c) {
				$break = false;
				$days  = daysDiff($c['gate_out'], $c['gatepass_date']);
				if ($tariffs AND $c['gate_out'] != '0000-00-00' AND $c['gatepass_date'] != '0000-00-00') {
					foreach ($tariffs as $t) {
						if ($t['to_day'] == 0)
							$calc_day = $days;
						else {
							$calc_day = ($t['to_day'] - $t['from_day']) + 1;
							$days -= $calc_day;
							if ($days < 0) {
								$calc_day = $calc_day + $days;
								$break = true;
							}
						}

						if ($c['size'] == 20) {
							$amount += bcmul($calc_day, $t['price_20'], 0);
							$result['amount_20'] = bcadd($result['amount_20'], bcmul($calc_day, $t['price_20'], 0));
						}
						else {
							$amount += bcmul($calc_day, $t['price_40'], 0);
							$result['amount_40'] = bcadd($result['amount_40'], bcmul($calc_day, $t['price_40'], 0));
						}
						if ($break) break;
					}
				}
				else {
					if ($c['size'] == 20) {
						$amount += $r['price_20'];
						$result['amount_20'] = bcadd($result['amount_20'], $r['price_20'], 0);
					}
					else {
						$amount += $r['price_40'];
						$result['amount_40'] = bcadd($result['amount_40'], $r['price_40'], 0);
					}

					if ($r['calc_type'] == 'Fixed')
						break;
				}
			}
			if ($r['taxable'] == true) {
				$stax = round($amount * Settings::get('service_tax') / 100, 2);
				$edu  = round($stax * Settings::get('edu_cess') / 100, 2);
				$hedu = round($stax * Settings::get('hedu_cess') / 100, 2);
			}
			$result['amount'] += ($amount + $stax + $edu + $hedu);
		}
		$this->db->update('import_details', array('cfs_payment' => $result['amount']), array('job_id' => $job_id));
	}

	function calcLine($job_id) {
		$this->load->helper('datefn');

		$job       = $this->kaabar->getRow('jobs', $job_id);
		$gld_date  = $this->kaabar->getField('vessels', $job['vessel_id'], 'id', 'gld_date');
		$free_days = $this->kaabar->getField('import_details', $job_id, 'job_id', 'free_days');
		$query     = $this->db->query("SELECT D.*, DATE_FORMAT(gate_out, '%Y-%m-%d') AS gate_out,
			DATE_FORMAT(IF(gatepass_date = '0000-00-00 00:00:00', NOW(), gatepass_date), '%Y-%m-%d') AS gatepass_date, CT.size 
		FROM deliveries_stuffings D INNER JOIN containers C ON D.container_id = C.id
			INNER JOIN container_types CT ON C.container_type_id = CT.id
			INNER JOIN jobs J ON C.job_id = J.id
		WHERE D.job_id = ?", array($job_id));
		$containers = $query->result_array();
		$rates      = $this->kaabar->getRows('agent_rates', $job['line_id'], 'agent_id');

		$result = array(
			'amount_20' => 0,
			'amount_40' => 0,
			'amount'    => 0,
		);
		$container_20 = 0;
		$container_40 = 0;
		if (count($containers) == 0) {
			for($i = 0; $i < $job['container_20']; $i++) {
				$container_20++;
				$containers[] = array('size' => 20);
			}
			for($i = 0; $i < $job['container_40']; $i++) {
				$container_40++;
				$containers[] = array('size' => 40);
			}
		}
		else {
			foreach ($containers AS $c) {
				if ($c['size'] == 20)
					$container_20++;
				else
					$container_40++;
			}
		}

		$vessel = $this->kaabar->getRow('vessels', $job['vessel_id'], 'id');
		foreach ($rates as $r) {
			$tariffs = $this->kaabar->getRows('agent_tariffs', array('agent_rate_id' => $r['id']));
			$amount  = 0;
			$stax    = 0;
			$edu     = 0;
			$hedu    = 0;
			foreach($containers as $c) {
				if ($gld_date == '00-00-0000') {
					$days = daysDiff($vessel['eta_date'], $c['gatepass_date']);
				}
				else
					$days = daysDiff($gld_date, $c['gatepass_date']);
				$days -= $free_days;


				if ($tariffs AND (
					($gld_date != '0000-00-00' AND $c['gatepass_date'] != '0000-00-00') OR 
					($container_20 > 0 OR $container_40 > 0))) {
					$break = false;
					foreach ($tariffs as $t) {
						if ($t['tariff_type'] == 'Delivery Order') {
							if ($container_20 > 0 AND $t['from_day'] <= $container_20 AND $t['to_day'] >= $container_20) {
								$amount += $t['price_20'];
								$container_20 = 0;
							}
							if ($container_40 > 0 AND $t['from_day'] <= $container_40 AND $t['to_day'] >= $container_40) {
								$amount += $t['price_40'];
								$container_40 = 0;
							}
							continue;
						}
						
						if ($free_days > $t['from_day'] && $free_days > $t['to_day']) continue;

						if ($t['to_day'] == 0)
							$calc_day = $days;
						else {
							$calc_day = ($t['to_day'] - $t['from_day']) + 1;
							$days -= $calc_day;
							if ($days < 0) {
								$calc_day = $calc_day + $days;
								$break = true;
							}
						}
						if ($c['size'] == 20) {
							$amount += bcmul($calc_day, $t['price_20'], 0);
							$result['amount_20'] = bcadd($result['amount_20'], bcmul($calc_day, $t['price_20'], 0), 0);
						}
						else {
							$amount += bcmul($calc_day, $t['price_40'], 0);
							$result['amount_40'] = bcadd($result['amount_40'], bcmul($calc_day, $t['price_40'], 0), 0);
						}
						if ($break) break;
					}
				}
				else {
					if ($c['size'] == 20) {
						$amount += $r['price_20'];
						$result['amount_20'] = bcadd($result['amount_20'], $r['price_20'], 0);
					}
					else {
						$amount += $r['price_40'];
						$result['amount_40'] = bcadd($result['amount_40'], $r['price_20'], 0);
					}

					if ($r['calc_type'] == 'Fixed')
						break;
				}
			}

			if ($r['currency_id'] > 1) {
				$amount = bcmul($amount, $vessel['import_exchange_rate'], 0);
			}

			if ($r['taxable'] == true) {
				$stax = round($amount * Settings::get('service_tax') / 100, 2);
				$edu  = round($stax * Settings::get('edu_cess') / 100, 2);
				$hedu = round($stax * Settings::get('hedu_cess') / 100, 2);
			}
			$result['amount'] += ($amount + $stax + $edu + $hedu);
		}
		$this->db->update('import_details', array('line_payment' => $result['amount']), array('job_id' => $job_id));
	}

	function getPort($id) {
		$sql = "SELECT P.id, CONCAT(P.name, ' (', P.unece_code, '), ', C.name) AS name
		FROM ports P INNER JOIN countries C ON P.country_id = C.id
		WHERE P.id = ?
		ORDER BY P.name";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		if (! $row)
			$row['name'] = '';
		return $row['name'];
	}
}
