<?php

class Export extends CI_Model {
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
			''			=> '',
			'Yes' 		=> 'label-success',
			'No' 		=> 'label-danger',

			'Bulk' 		=> 'label-info',
			'Container'	=> 'label-warning',

			'Factory' => 'label-success',
			'Docks'   => 'label-info',
			'CFS'     => 'label-info',
			'Godown'  => 'label-warning',

			'FCL'    => 'label-success',
			'LCL'    => 'label-danger',
			'Air'    => 'label-info',
			'Parcel' => 'label-warning',
			
			'Completed' => 'label-success',
			'Pending'   => 'label-danger',
			'Bills'     => 'label-warning',
			'Delivery'  => 'label-info',
			'Cancelled' => 'label-danger',

			'Clearing'       => 'label-success',
			'Forwarding'     => 'label-warning',
			'Transportation' => 'label-info',
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

	function getStatus() {
		return array(
			'Pending'  => 'Pending',
			'Completed'=> 'Completed',
			'Cancelled'=> 'Cancelled'
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

	function getStuffingTypes() {
		return array(
			'Factory' => 'Factory',
			'Godown'  => 'Godown',
			'CFS'     => 'CFS',
		);
	}

	function getCertificateOfOrigins() {
		return array(
			'Gandhidham Chamber of Commerce',
			'Gujarat Chamber of Commerce',
			'Export Inspection Council, Gandhidham',
			'Export Inspection Council, Ahmedabad',
		);
	}

	function getEIAs() {
		return array(
			'-',
			'Normal',
			'Tatkal',
			'Retrospectively',
		);
	}

	function getPhytos() {
		return array(
			'-',
			'Kandla',
			'Jamnagar',
			'Gandhinagar',
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

	function sbExists($sb_no, $sb_date) {
		$years = explode('_', $this->kaabar->getFinancialYear($sb_date));
		$query = $this->db->query("SELECT id FROM child_jobs WHERE J.type = 'Export' AND sb_no = ? AND sb_date => ? AND sb_date <= ?",
			array($sb_no, $years[0].'-04-01', $years[1].'-03-31')
		);
		$row = $query->row_array();
		if ($row == false)
			return 0;
		return $row['id'];
	}

	function beExists($be_no, $be_date) {
		$years = explode('_', $this->kaabar->getFinancialYear($be_date));
		$query = $this->db->query("SELECT id FROM child_jobs WHERE J.type = 'Import' AND be_no = ? AND be_date => ? AND be_date <= ?",
			array($sb_no, $years[0].'-04-01', $years[1].'-03-31')
		);
		$row = $query->row_array();
		if ($row == false)
			return 0;
		return $row['id'];
	}

	function blExists($bl_no, $bl_date) {
		$query = $this->db->query("SELECT id FROM child_jobs WHERE J.type = 'Export' AND bl_no = ? AND bl_date = ?",
			array($bl_no, $bl_date)
		);
		$row = $query->row_array();
		if ($row == false)
			return 0;
		return $row['id'];
	}

	function invoiceExists($party_id, $invoice_no, $invoice_date) {
		$years = explode('_', $this->kaabar->getFinancialYear($invoice_date));
		$query = $this->db->query("SELECT id FROM jobs WHERE J.type = 'Export' AND party_id = ? AND invoice_no = ? AND invoice_date >= ? AND invoice_date <= ?",
			array($party_id, $invoice_no, $years[0].'-04-01', $years[1].'-03-31')
		);
		$row = $query->row_array();
		if ($row == false)
			return 0;
		return $row['id'];
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

	function getJobsInfo($job_id, $links = false, $url = false) {
		$sql = "SELECT J.id, J.id2_format, J.cargo_type, DATE_FORMAT(J.date, '%d-%m-%Y') AS date, 
			J.stuffing_type, COALESCE(PS.name, G.name, CFS.name) AS stuffing_place,
			P.name AS party_name, S.name AS shipper_name, IF(J.consignee_id = 1, J.consignee, CON.name) AS consignee_name,
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, T.code AS terminal_code, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, DATE_FORMAT(V.gate_cutoff_date, '%d-%m-%Y') AS gate_cutoff_date, 
			DATE_FORMAT(V.doc_cutoff_date, '%d-%m-%Y') AS doc_cutoff_date, C.name AS cargo_name, U.code AS unit_code,
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			J.fpod
		FROM jobs J LEFT OUTER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN parties S ON J.shipper_id = S.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN terminals T ON V.terminal_id = T.id
			LEFT OUTER JOIN consignees CON ON J.consignee_id = CON.id
			LEFT OUTER JOIN party_sites PS ON J.shipper_site_id = PS.id
			LEFT OUTER JOIN godowns G ON J.godown_id = G.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN products C ON J.product_id = C.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id
			LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
		WHERE J.id = ?";
		$query = $this->db->query($sql, array($job_id));
		$data = $query->row_array();

		if ($data) {
			$sql   = "SELECT CJ.id, CJ.vi_job_no FROM child_jobs CJ WHERE CJ.job_id = ? ORDER BY CJ.vi_job_no";
			$query = $this->db->query($sql, array($job_id));
			$data['child_jobs'] = $query->result_array();

			if ($links) {
				$data['links'] = $links;
				$data['url'] = $url;
			}
		}
		return $data;
	}
	
	function countJobs($search = '') {
		$years     = explode('_', $this->_fy_year);
		$from_date = $years[0].'-04-01';
		$to_date   = $years[1].'-03-31';

		$sql = "SELECT COUNT(T.id) AS numrows
		FROM (
			SELECT J.id
				FROM jobs J LEFT OUTER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN parties S ON J.shipper_id = S.id
				LEFT OUTER JOIN child_jobs CJ ON J.id = CJ.job_id
				LEFT OUTER JOIN job_invoices EI ON J.id = EI.job_id
				LEFT OUTER JOIN agents L ON J.line_id = L.id
				LEFT OUTER JOIN indian_ports POL ON J.custom_port_id = POL.id
				LEFT OUTER JOIN indian_ports GP ON J.loading_port_id = GP.id
				LEFT OUTER JOIN ports POD ON J.discharge_port_id = POD.id
				LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
				LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
				LEFT OUTER JOIN deliveries_stuffings ST ON J.id = ST.job_id
			WHERE J.type = 'Export' AND (
				J.id2_format LIKE '%$search%' OR
				J.stuffing_type LIKE '%$search%' OR
				J.booking_no LIKE '%$search%' OR
				CJ.sb_no LIKE '%$search%' OR
				EI.invoice_no LIKE '%$search%' OR
				J.sub_type LIKE '%$search%' OR
				P.name LIKE '%$search%' OR
				S.name LIKE '%$search%' OR
				L.code LIKE '%$search%' OR
				POL.name LIKE '%$search%' OR
				GP.name LIKE '%$search%' OR
				POD.name LIKE '%$search%' OR
				POD.name LIKE '%$search%' OR
				ST.container_no LIKE '%$search%' OR
				J.status LIKE '%$search%')
			GROUP BY J.id
		) T";
		$query = $this->db->query($sql, array($from_date, $to_date));
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getJobs($search = '', $offset = 0, $limit = 25) {
		$years     = explode('_', $this->_fy_year);
		$from_date = $years[0].'-04-01';
		$to_date   = $years[1].'-03-31';
		//CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
		$sql = "SELECT J.id, J.id2_format, L.code AS line_code, J.booking_no, DATE_FORMAT(J.booking_date, '%d-%m-%Y') AS booking_date, 
			P.name AS party_name, S.name AS shipper_name,
			GROUP_CONCAT(DISTINCT CJ.sb_no SEPARATOR ', ') AS sb_no, 
			GROUP_CONCAT(DISTINCT CONCAT(EI.invoice_no, ' ', DATE_FORMAT(EI.invoice_date, '%d-%m-%Y')) SEPARATOR ', ') AS invoice_no_date, 
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			POL.name AS custom_port, GP.name AS loading_port, 
			POD.name AS discharge_port, J.fpod, 
			J.cargo_type, J.shipment_type, J.sub_type, J.stuffing_type, J.status
		FROM jobs J
			LEFT OUTER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN parties S ON J.shipper_id = S.id
			LEFT OUTER JOIN child_jobs CJ ON J.id = CJ.job_id
			LEFT OUTER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN indian_ports POL ON J.custom_port_id = POL.id
			LEFT OUTER JOIN indian_ports GP ON J.loading_port_id = GP.id
			LEFT OUTER JOIN ports POD ON J.discharge_port_id = POD.id
			LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
			LEFT OUTER JOIN deliveries_stuffings ST ON J.id = ST.job_id
		WHERE J.type = 'Export' AND (
			J.id2_format LIKE '%$search%' OR
			J.stuffing_type LIKE '%$search%' OR
			J.booking_no LIKE '%$search%' OR
			CJ.sb_no LIKE '%$search%' OR
			EI.invoice_no LIKE '%$search%' OR
			J.sub_type LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			S.name LIKE '%$search%' OR
			L.code LIKE '%$search%' OR
			POL.name LIKE '%$search%' OR
			GP.name LIKE '%$search%' OR
			POD.name LIKE '%$search%' OR
			ST.container_no LIKE '%$search%' OR
			J.status LIKE '%$search%')
		GROUP BY J.id
		ORDER BY DATE_FORMAT(J.date, '%Y-%m') DESC, J.id2 DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, array($from_date, $to_date));
		return $query->result_array();
	}
	
	function createJobNo($id, $date) {
		$years      = explode('_', $this->kaabar->getFinancialYear($date));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';

		$this->db->query("LOCK TABLES jobs WRITE");
		$query = $this->db->query("SELECT MAX(id2) AS id2 FROM jobs WHERE type = 'Export' AND date >= ? AND date <= ?", 
			array($start_date, $end_date));
		$id_row = $query->row_array();
		$id_row['id2']++;
		$id_row['id2_format'] = 'EXP/' . str_pad($id_row['id2'], 4, '0', STR_PAD_LEFT) . '/' . substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2);
		$this->db->update('jobs', array('id2' => $id_row['id2'], 'id2_format' => $id_row['id2_format']), "id = $id");
		$this->db->query("UNLOCK TABLES");
	}

	function getBLMasters($job_id) {
		$sql = "SELECT J.id AS job_id, CJ.id AS child_job_id, EI.id AS job_invoice_id, 
			J.id2_format, CJ.vi_job_no, EI.invoice_no,
			CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%d-%m-%Y') AS sb_date,
			CJ.mr_no, DATE_FORMAT(CJ.mr_date, '%d-%m-%Y') AS mr_date,
			CJ.bl_no, DATE_FORMAT(CJ.bl_date, '%d-%m-%Y') AS bl_date
		FROM jobs J INNER JOIN child_jobs CJ ON J.id = CJ.job_id
			LEFT OUTER JOIN job_invoices EI ON CJ.id = EI.child_job_id
		WHERE J.id = ?
		ORDER BY CJ.vi_job_no, EI.invoice_no";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function getInvoiceNos($job_id) {
		$sql = "SELECT EI.id, CONCAT(EI.invoice_no, ' - ', CJ.sb_no) AS invoice_no
		FROM job_invoices EI INNER JOIN child_jobs CJ ON EI.child_job_id = CJ.id
		WHERE CJ.job_id = ?";
		$query = $this->db->query($sql, array($job_id));
		$rows = $query->result_array();
		$result = array(0 => '');
		foreach ($rows as $row) {
			$result[$row['id']] = $row['invoice_no'];
		}
		return $result;
	}

	function getCargoArrivals($job_id) {
		$sql = "SELECT *, DATE_FORMAT(C.date, '%d-%m-%Y') AS date FROM cargo_arrivals C WHERE C.job_id = ? ORDER BY C.id";
		$query = $this->db->query($sql, array('job_id' => $job_id));
		return $query->result_array();
	}

	function getTranshipments($job_id) {
		$sql = "SELECT T.id, P.name AS port_name, P.unece_code, C.name AS country, DATE_FORMAT(T.eta_date, '%d-%m-%Y') AS eta_date
		FROM transhipments T INNER JOIN ports P ON T.port_id = P.id
			INNER JOIN countries C ON P.country_id = C.id
		WHERE T.job_id = $job_id
		ORDER BY T.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getInvoices($child_job_id) {
		$sql = "SELECT EI.id, EI.invoice_no, DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') AS invoice_date, 
			EI.toi, C.code AS currency, EI.invoice_value
		FROM job_invoices EI INNER JOIN currencies C ON EI.currency_id = C.id
		WHERE EI.child_job_id = ?
		ORDER BY EI.id";
		$query = $this->db->query($sql, array($child_job_id));
		return $query->result_array();
	}

	function getJobInvoices($job_id) {
		$sql = "SELECT GROUP_CONCAT(EI.invoice_no SEPARATOR ', ') AS invoice_no
		FROM job_invoices EI
		WHERE EI.job_id = ?
		ORDER BY EI.id";
		$query = $this->db->query($sql, array($job_id));
		return $query->row_array();
	}

	function getJobSBs($job_id) {
		$sql = "SELECT GROUP_CONCAT(CJ.sb_no, ' / ', DATE_FORMAT(CJ.sb_date, '%d-%m-%Y')) AS sb_no
		FROM child_jobs CJ
		WHERE CJ.job_id = ?
		ORDER BY CJ.id";
		$query = $this->db->query($sql, array($job_id));
		return $query->row_array();
	}

	function getContainerList($job_id) {
		$sql = "SELECT DS.id, CONCAT(CT.size, ' ', CT.code) AS container_type, DS.container_no, DS.seal_no
			FROM deliveries_stuffings DS INNER JOIN container_types CT ON DS.container_type_id = CT.id
			WHERE DS.job_id = ?
			ORDER BY CT.size, DS.id";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}

	function countContainers($child_job_id, $search = '') {
		$sql = "SELECT COUNT(DS.id) AS numrows
		FROM deliveries_stuffings DS INNER JOIN container_types CT ON DS.container_type_id = CT.id
		WHERE DS.child_job_id = $child_job_id AND
			(DS.number LIKE '%$search%' OR
			DS.seal LIKE '%$search%' OR
			CT.code LIKE '%$search%' OR
			CT.name LIKE '%$search%')";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getContainers($child_job_id, $search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT C.*, CT.size, CT.code, CT.name, DATE_FORMAT(C.seal_date, '%d-%m-%Y') AS seal_date
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE C.child_job_id = $child_job_id AND
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
		$sql = "SELECT D.id, IF(D.date = NOW(), 'Today', DATE_FORMAT(D.date, '%d-%m-%Y')) AS date, D.job_id, D.name
		FROM documents D
		WHERE D.job_id = ?
		ORDER BY date DESC";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}
	

	function getAttachedDocs($job_id, $child_job_id = 0, $all = 0, $attached_only = 0) {
		$sql = "SELECT AD.id, AD.job_id, AD.child_job_id, AD.document_type_id, DT.sr_no, 
			DATE_FORMAT(AD.date, '%d-%m-%Y') AS date, AD.doc_no, DT.name, AD.is_compulsory, AD.received, 
			DATE_FORMAT(AD.received_date, '%d-%m-%Y') AS received_date, 
			AD.file, AD.pages, AD.remarks
		FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
		WHERE AD.job_id = $job_id AND AD.child_job_id = $child_job_id " . ($attached_only ? ' AND LENGTH(AD.file) > 0 ' : ''); 

		if ($all) {
			$sql .= "
		UNION
		SELECT 0 AS id, $job_id AS job_id, 0 AS child_job_id, DT.id AS document_type_id, DT.sr_no, '00-00-0000' AS date, 
			'' AS doc_no, DT.name, DT.is_compulsory, 'No' AS received, '00-00-0000' AS received_date, 
			'' AS file, 0 AS pages, '' AS remarks
		FROM document_types DT INNER JOIN jobs J ON (DT.type = J.type AND DT.cargo_type = J.cargo_type AND DT.attach_to = '" . ($child_job_id == 0 ? 'Master Job' : 'Visual Job') . "')
		WHERE J.id = $job_id AND DT.id NOT IN (
			SELECT document_type_id FROM attached_documents WHERE job_id = $job_id AND child_job_id = $child_job_id
		) ";
		}

		$sql .= "ORDER BY is_compulsory DESC, sr_no, id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getAttachedDoc($job_id, $id) {
		$sql = "SELECT AD.id, AD.job_id, AD.child_job_id, DATE_FORMAT(AD.date, '%d-%m-%Y') AS date, 
			AD.document_type_id, DT.sr_no, AD.doc_no, DT.name, AD.is_compulsory, AD.received, 
			AD.file, AD.pages, AD.remarks
		FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
		WHERE AD.id = ? AND AD.job_id = ?";
		$query = $this->db->query($sql, array($id, $job_id));
		return $query->row_array();
	}

	function updateBillStatus($job_id) {
		$sql     = "SELECT SUM(E.amount) AS amount FROM expenses E WHERE E.job_id = ?";
		$query   = $this->db->query($sql, array($job_id));
		$expense = $query->row_array();
		if (! $expense)
			$expense['amount'] = 0;
		
		$sql   = "SELECT SUM(B.amount) AS amount FROM bills B WHERE B.job_id = ?";
		$query = $this->db->query($sql, array($job_id));
		$bills = $query->row_array();
		if (! $bills)
			$bills['amount'] = 0;

		if ($bills['amount'] >= $expense['amount']) {
			$this->kaabar->save('jobs', array('status' => 'Completed'), array('id' => $job_id));
		}
		else {
			$this->kaabar->save('jobs', array('status' => 'Bills'), array('id' => $job_id));
		}
	}
}
