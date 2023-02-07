<?php

class Export extends CI_Model {
	var $_company_id;
	var $_fy_year;
	var $_allowedFileds;
	var $_fields;

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

	function getAllowedFileds() {
		return array(
	    	'J.id',
	    	'J.id2_format as job_no', 
	    	'DATE_FORMAT(J.date, "%d-%m-%Y") as job_date',
	    	'P.name as billing_party',
	    	'J.id as amount',
	    	'A.name as shipper',
	    	'J.sb_no',
	    	'AA.name as consignee',
	    	'J.shipment_type',
	    	'PPO.name as custom_port',
	    	'PP.name as pod',
	    	'J.id as document',
	    	'J.id as project_costsheet',
	    	'J.id as actual_costsheet',
			'J.id as quotation',
			'J.id as reason_not_billed',
			'J.id as upload',
		);
	}

	function getFields() {
	    return array(
			'id' 				=> 'J.id', 
			'job_no' 			=> 'J.id2_format', 
	    	'job_date' 			=> 'DATE_FORMAT(J.date, "%d-%m-%Y")',
	    	'billing_party' 	=> 'P.name',
	    	'amount' 			=> 'J.id',
	    	'shipper' 			=> 'A.name',
	    	'sb_no' 			=> 'J.sb_no',
	    	'consignee' 		=> 'AA.name',
	    	'shipment_type' 	=> 'J.shipment_type',
	    	'custom_port' 		=> 'PPO.name',
	    	'pod' 				=> 'PP.name',
	    	'document' 			=> 'J.id',
	    	'project_costsheet' => 'J.id',
	    	'actual_costsheet' 	=> 'J.id',
			'quotation' 		=> 'J.id',
			'reason_not_billed' => 'J.id',
			'upload' 			=> 'J.id',
		);
	}

	///// GET All EWB LIST Which are in Current Tracking
	function getPedningJobs($from_date, $to_date, $search = NULL, $parsed_search = NULL, $offset = 0, $limit = 10)
    {
    	$this->_fields = $this->getFields();
    	$this->_allowedFileds = $this->getAllowedFileds();
    	//unset($this->_allowedFileds[0]);

    	foreach ($this->_fields as $s => $se) {
    		$likeSearch[$se] = $search;
    	}

    	$this->db->select($this->_allowedFileds)
	    	->join('parties P', 'J.party_id = P.id', 'LEFT')
	    	->join('parties A', 'J.shipper_id = A.id', 'LEFT')
	    	->join('parties AA', 'J.consignee_id = AA.id', 'LEFT')
	    	->join('indian_ports PPO', 'J.custom_port_id = PPO.id', 'LEFT')
	    	->join('ports PP', 'J.discharge_port_id = PP.id', 'LEFT')
	    	->group_by('J.id')
			->where_in('J.status', ['Pending', 'Program', 'Delivery', 'Bills', 'Completed'])
			->where('J.date >= ', convDate($from_date))
	    	->where('J.date <= ', convDate($to_date));

	    	if(is_array($parsed_search)) {
	    		if(count($parsed_search) > 0){
    		    	foreach($parsed_search as $key => $value){
	    			   	// Finding the YMD dates and converting to DMY
	    				$value['value'] = _convDate($value['value']);
	    				if(strlen($value['value']) > 0){
	    					$this->db->group_start();
                        	switch ($value['filter']) {
								case 1:
									if(isset($this->_fields[$value['field']])){
										$this->db->where($this->_fields[$value['field']], $value['value']);	
									}
									break;
							  	case 2:
							  		if(isset($this->_fields[$value['field']])){
							  			$this->db->where($this->_fields[$value['field']]. ' != ', $value['value']);
							  		}
									break;
							  	case 3:
							  		if(isset($this->_fields[$value['field']])){
							    		$this->db->like($this->_fields[$value['field']], $value['value'], 'after');	
							    	}
									break;
							    case 4:
							    	if(isset($this->_fields[$value['field']])){
							    		$this->db->like($this->_fields[$value['field']], $value['value'], 'both');
							    	}
									break;
							  	case 5:
							  		if(isset($this->_fields[$value['field']])){
							    		$this->db->not_like($this->_fields[$value['field']], $value['value']);	
							    	}
									break;
							  	case 6:
							  		if(isset($this->_fields[$value['field']])){
							    		$this->db->like($this->_fields[$value['field']], $value['value'], 'before');
							    	}
									break;
							}
							$this->db->group_end();
                        }
					}
				}
			}

			if($search != NULL)
			{
			    $this->db->group_start();
					$this->db->or_like($likeSearch);
				$this->db->group_end();
			}

			/*echo "<pre>";
 			$chetan = $this->getCompiledSelect();
 			print_r($chetan);
 			exit;*/

 			/// FOR PAGINATION
 			$this->db->limit($limit, $offset);  // Produces: LIMIT 20, 10
 			$this->db->order_by('J.id', 'ASC');
			$result = $this->db->get('jobs J')->result_array();
			return $result;
	}

	function getHeaderName() {
		return array(
			'job_no'			=> 'Job No',
			'job_date'  		=> 'Date',
			'billing_party'		=> 'Billing Party',
			'amount' 			=> 'Invoice Amount',
			'shipper'  			=> 'Shipper',
			'sb_no'  			=> 'SB NO',
			'consignee'  		=> 'Consignee',
			'shipment_type'  	=> 'Shipment Type',
			'custom_port'  		=> 'Clearance Port',
			'pod'  				=> 'POD',
			'document'  		=> 'Document',
			'project_costsheet'	=> 'Project Costsheet',
			'actual_costsheet'  => 'Actual Costsheet',
			'quotation'  		=> 'Quotation',
			'reason_not_billed' => 'Reason to Not Bill',
			'upload'  			=> 'Uploads',
		);
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

	function getInvoiceTypes() {
		return array(
			'Single' => 'Single',
			'Multiple' => 'Multiple'
		);
	}

	function getStatus() {
		return array(
			'Pending'	=> 'Pending',
			'Program'  	=> 'Program',
			'Carting'  	=> 'Carting',
			'Stuffing'  => 'Stuffing',
			'Bills'  	=> 'Bills',
			'Completed'	=> 'Completed',
			'Cancelled'	=> 'Cancelled'
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

	function getBulkShipmentTypes() {
		return array(
			'Bulk'          => 'Bulk',
			'Break Bulk'    => 'Break Bulk',
			'Project Cargo' => 'Project Cargo',
		);
	}

	function getFreightTerms() {
		return array(
			'Collect' => 'Collect',
			'Post Collect' => 'Post Collect'
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
				//new code
				$cdir .= '/'.$dir;

				// Skip if dir already created.
				if (! file_exists($cdir)){
					//new code added
					mkdir($cdir, 0777, TRUE);
				} 
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
			P.name AS party_name, S.name AS shipper_name, IF(J.consignee_id = 1, J.consignee, CON.consignee_name) AS consignee_name,
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

	function getJob($job_id) {
		
		$sql = "SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 

			DATE_FORMAT(J.mbl_date, '%d-%m-%Y') as mbl_date, 
			DATE_FORMAT(J.hbl_date, '%d-%m-%Y') as hbl_date,

			DATE_FORMAT(J.eta_date, '%d-%m-%Y') as eta_date, 
			DATE_FORMAT(J.etd_date, '%d-%m-%Y') as etd_date, 

			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.consignee_name as buyer_name, CCC.consignee_name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, FPODC.name as fpod_country,

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
			LEFT OUTER JOIN countries FPODC ON FPOD.country_id = FPODC.id



			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = ? AND J.type = 'Export')";

		$query = $this->db->query($sql, array($job_id));

		return $query->row_array();
	}

	

	function createJobNo($id, $date, $branch) {


		$years      = explode('_', $this->kaabar->getFinancialYear($date));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';

		$this->db->query("LOCK TABLES jobs WRITE");
		$query = $this->db->query("SELECT MAX(id2) AS id2 FROM jobs WHERE type = 'Export' AND date >= ? AND date <= ?",	 
		array($start_date, $end_date));
		$id_row = $query->row_array();
		$id_row['id2']++;
		$id_row['idkaabar_code'] = $branch.str_pad($id_row['id2'], 4, '0', STR_PAD_LEFT);
		$this->db->update('jobs', array('id2' => $id_row['id2'], 'idkaabar_code' => $id_row['idkaabar_code']), "id = $id");
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


}
