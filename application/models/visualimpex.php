<?php

class Visualimpex extends CI_Model {
	var $_db;
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
		
		$dsn = 'mysql://' . Settings::get('visualimpex_db_user') . ':' . Settings::get('visualimpex_db_password') . '@' . Settings::get('visualimpex_db_host') . '/' . Settings::get('visualimpex_db_name');
		$this->_db = $this->load->database($dsn, true);
		//$this->_db = $this->load->database('vi', true);
	}

	function setCompany($id) {
		$this->_company_id = $id;
	}

	function getCompanyID() {
		return $this->_company_id;
	}
	
	function getVessels() {
		$sql = "SELECT V.id, CONCAT(V.name, ' / ', V.voyage_no) AS name FROM vessels V ORDER BY V.name";
		$query = $this->db->query($sql);
	
		$values = array();
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			foreach($result as $row)
				$values[$row['id']] = $row['name'];
		}
		return $values;
	}
	
	function getFinancialYear() {
		$fy = array();
		$query = $this->_db->query("SELECT WORKBLOCK AS id, CONCAT(DATE_FORMAT(START_DATE, '%Y'), '-', DATE_FORMAT(END_DATE, '%Y')) AS financial_year 
			FROM workblok 
			ORDER BY START_DATE DESC");
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$fy[$r['id']] = $r['financial_year'];
		}
		return $fy;
	}

	function ajaxExportJobs($search) {
		$years = $this->_fy_year;

		$sql   = "SELECT CJ.Job_No, P.PARTY_NAME, CJ.Impx_PCode
			FROM commonjobs CJ INNER JOIN prt_mast P ON CJ.Party_Code = P.PARTY_CODE
			WHERE CJ.Job_Type = 'E' AND 
				-- CJ.Job_No LIKE '%/$years' AND
				CJ.Job_No LIKE '%$search%' 
			ORDER BY Job_No 
			LIMIT 0, 50";
		$this->kaabar->getJson($sql, $this->_db);
	}

	function import($workblock) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '256M');

		// Pending CNSR_CODE FROM iinv_dtl known as Shipper

		// Import Jobs
		$sql = "SELECT CJ.Doc_Recd, ISD.JOB_NO, ISD.VESSEL_NAME, ISD.VOYAGE_NO, ISD.ROTN_NO, ISD.ROTN_DATE, ISD.MAWB_NO, 
			DATE_FORMAT(ISD.MAWB_DATE, '%Y-%m-%d') AS MAWB_DATE, ISD.NO_OF_PKG, ISD.PKG_UNIT, IWR.BE_TYPE, IWR.MARKS, IWR.PORT_OF_SH, 
			IWR.CONT_ORIG, INV.INV_NO, INV.INV_DATE, INV.INV_VALUE, INV.CURRENCY, IPD.PROD_DESC, IPD.RITC_NO,
			ROUND(SUM(IPD.QTY), 4) AS QTY, IPD.UNIT, PM.PARTY_NAME, PA.ADDRESS, CNSR.CNSR_NAME
		FROM commonjobs CJ INNER JOIN ishp_dtl ISD ON CJ.Job_No = ISD.JOB_NO
			INNER JOIN iworkreg IWR ON ISD.JOB_NO = IWR.JOB_NO
			INNER JOIN prt_mast PM ON IWR.PARTY_CODE = PM.PARTY_CODE
			INNER JOIN prt_addr PA ON (IWR.PARTY_CODE = PA.PARTY_CODE AND IWR.PARTY_ADDR = PA.ADDR_CODE)
			INNER JOIN iinv_dtl INV ON ISD.JOB_NO = INV.JOB_NO
			INNER JOIN iproddtl IPD ON ISD.JOB_NO = IPD.JOB_NO
			INNER JOIN cnsr_mst CNSR ON INV.CNSR_CODE = CNSR.CNSR_CODE
		WHERE CJ.WrkBlk = ? AND CJ.Job_Type = 'I'
		GROUP BY ISD.JOB_NO
		ORDER BY ISD.JOB_NO DESC";
		$query = $this->_db->query($sql, array($workblock));
		$rows = $query->result_array();

		foreach($rows as $j) {
			// preg_match("/IMP\/([0-9]{5})\/[0-9]{4}-[0-9]{4}/", $j['JOB_NO'], $regs);
			// if (! isset($regs[1])) continue;
			
			$query = $this->db->query("SELECT * FROM currencies WHERE code LIKE ?", array($j['CURRENCY']));
			$currency = $query->row_array();
			$iv_currency_id = (! isset($currency['id']) ? 0 : $currency['id']);

			$query = $this->db->query("SELECT * FROM ports WHERE name LIKE trim('" . (! isset($j['PORT_OF_SH']) ? '' : $j['PORT_OF_SH']) . "')");
			$port = $query->row_array();
			$shipment_port_id = (! isset($port['id']) ? 0 : $port['id']);

			$query = $this->db->query("SELECT * FROM package_types WHERE code LIKE trim(?)", array($j['PKG_UNIT']));
			$package_type = $query->row_array();
			$package_type_id = (! isset($package_type['id']) ? 0 : $package_type['id']);

			$query = $this->db->query("SELECT * FROM countries WHERE name LIKE trim('" . (! isset($j['CONT_ORIG']) ? '' : addcslashes($j['CONT_ORIG'], "'")) . "')");
			$country = $query->row_array();
			$origin_country_id = (! isset($country['id']) ? 0 : $country['id']);
			unset($port);
			unset($country);
			
			$query = $this->db->query("SELECT id FROM jobs 
				WHERE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(bl_no, ' ', ''), '-', ''), '/', ''), '(', ''), ')', '')) = ?", 
				array(strtolower($j['MAWB_NO']))
			);
			$row = $query->row_array();
			if ($row) {
				$data = array(
					'packages'          => $j['NO_OF_PKG'],
					'package_type_id'   => $package_type_id,
					'invoice_no'        => $j['INV_NO'],
					'invoice_date'      => $j['INV_DATE'],
					'marks'             => $j['MARKS'],
					'details'           => $j['PROD_DESC'],
					'net_weight'        => $j['QTY'],
					'net_weight_unit'   => ($j['UNIT'] == 'CMT' ? 'CBM' : $j['UNIT']),
					'shipment_port_id'  => $shipment_port_id,
					'origin_country_id' => $origin_country_id,
					'vi_job_no'         => $j['JOB_NO'],
					'vi_job_date'       => substr($j['Doc_Recd'], 0, 10),
					'vi_party_name'     => $j['PARTY_NAME'],
					'vi_address'        => $j['ADDRESS'],
					'vi_shipper_name'   => $j['CNSR_NAME'],
					'vi_ritc_no'        => $j['RITC_NO'],
				);
				$job_id = $this->kaabar->save('jobs', $data, ['id' => $row['id']]);

				// Import Containers
				$query = $this->_db->query("SELECT * FROM impcontdet WHERE JOB_NO = ?", array($j['JOB_NO']));
				$rows = $query->result_array();
				foreach($rows as $r) {
					$query = $this->db->query("SELECT * FROM containers WHERE job_id = ? AND number = ?", array($job_id, $r['CONT_NO']));
					$row = $query->row_array();
					if ($row == false) $row['id'] = 0;
					
					$container = array(
						'job_id'            => $job_id,
						'container_type_id' => ($r['CONT_SIZE'] == 20 ? 2 : 9),
						'number'            => $r['CONT_NO'],
						'seal'              => (is_null($r['SEAL_NO']) ? '' : $r['SEAL_NO'])
					);
					$container_id = $this->kaabar->save('containers', $container, ['id' => $row['id']]);
					if ($row['id'] == 0) {
						$container['container_id'] = $container_id;
						$container['container_no'] = $container['number'];
						unset($container['number']);
						unset($container['seal']);

						$query = $this->db->query("SELECT * FROM deliveries_stuffings WHERE job_id = ? AND container_no = ?", array($job_id, $r['CONT_NO']));
						$row = $query->row_array();
						if ($row == false) $row['id'] = 0;
						
						$this->kaabar->save('deliveries_stuffings', $container, ['id' => $row['id']]);
					}
				}
			}
		}
	}

	function export($id, $job_no) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '256M');

		// Pending CNSR_CODE FROM iinv_dtl known as Shipper

		$sql   = "SELECT * FROM jobs WHERE id = ?";
		$query = $this->db->query($sql, array($id));
		$job   = $query->row_array();

		$sql    = "SELECT * FROM child_jobs WHERE vi_job_no = ?";
		$query  = $this->db->query($sql, $job_no);
		$child_job = $query->row_array();
		if ($child_job) {
			setSessionError('Visual Impex Job already added in Job No. <a href="/export/child_job/edit/' . $child_job['job_id'] . '/' . $child_job['id'] . '">' . $child_job['vi_job_no'] . '</a>');
			return;
		}
		$child_job['id'] = 0;

		// Export Jobs
		$sql   = "SELECT Job_No, Party_Code, Impx_PCode FROM commonjobs WHERE Job_No = ?";
		$query = $this->_db->query($sql, array($job_no));
		$row   = $query->row_array();

		if ($row) {
			$query      = $this->_db->query("SELECT Inv_ID, INV_NO, INV_DATE, TOI, INV_CUR, INV_VALUE FROM einvdtl WHERE Job_No = ?", array($job_no));
			$einvdtl    = $query->result_array();

			$query      = $this->_db->query("SELECT eproddtl.Inv_ID, eproddtl.PROD_SN, eproddtl.PROD_DESC, eproddtl.PRODCODE, eproddtl.RITC_NO, 
				eproddtl.QTY, eproddtl.UNIT
				FROM eproddtl WHERE JOB_NO = ?", array($job_no));
			$eproddtl   = $query->result_array();

			$query      = $this->_db->query("SELECT STUFFED_AT, Factory_Address, GR_WT, GR_UNIT, NET_WT, NETWT_UNIT FROM eshipdtl WHERE JOB_NO = ?", array($job_no));
			$eshipdtl   = $query->row_array();

			$query      = $this->_db->query("SELECT Cons_Code, CONS_NAME, INV_DTL, MARKS, ActNoOfPkgs, ActNoOfPkgUnit, DEST_PORT, DestPortCode, UNECE_DestPortCode, DEST_CNTRY, ROUND(FOB_VAL / TotInv_CRate, 2) AS fob_value, TotInv_Cur 
				FROM eworkreg WHERE JOB_NO = ?", array($job_no));
			$eworkreg   = $query->row_array();

			$query      = $this->_db->query("SELECT AR4No, AR4Date FROM ar4dtl WHERE Job_No = ?", array($job_no));
			$ar4dtl     = $query->result_array();
			$ares 		= array();
			foreach ($ar4dtl as $are) {
				$ares[$are['AR4No']] = $are['AR4Date'];
			}

			$query      = $this->_db->query("SELECT Cont_No, Seal_No, Cont_Size, Cont_Type FROM expcontdtl WHERE JOB_NO = ?", array($job_no));
			$expcontdtl = $query->result_array();
			// $query      = $this->_db->query("SELECT CONS_NAME FROM cons_mst WHERE CONS_CODE = ?", array($eworkreg['Cons_Code']));
			// $cons_mst   = $query->row_array();
			// $query      = $this->_db->query("SELECT * FROM cons_add WHERE CONS_CODE = ?", array($eworkreg['Cons_Code']));
			// $cons_add   = $query->row_array();

			$query      = $this->db->query("SELECT id, country_id FROM ports WHERE code = ?", array($eworkreg['DestPortCode']));
			$ports      = $query->row_array();
			if (!$ports) {
				$country_id = $this->kaabar->getField('countries', $eworkreg['DEST_CNTRY'], 'name', 'id');
				$ports = array(
					'id'         => 0,
					'country_id' => $country_id,
					'code'       => $eworkreg['DestPortCode'],
					'name'       => $eworkreg['DEST_PORT'],
					'unece_code' => $eworkreg['UNECE_DestPortCode'],
				);
				$ports['id'] = $this->kaabar->save('ports', $ports);
			}

			$query      = $this->db->query("SELECT id FROM package_types WHERE name = ?", array($eworkreg['ActNoOfPkgUnit']));
			$package_types = $query->row_array();

			// Getting Consignee ID
			// $query     = $this->db->query("SELECT id FROM consignees WHERE vi_code = ?", array($eworkreg['Cons_Code']));
			// $consignee = $query->row_array();
			// if (! $consignee) {
			// 	$consignee = array(
			// 		'vi_code' => $eworkreg['Cons_Code'],
			// 		'name'    => $cons_mst['CONS_NAME'],
			// 		'address' => $cons_add['ADDRESS'],
			// 		'contact' => $cons_add['TEL_NO'],
			// 		'email'   => $cons_add['EMAIL'],
			// 	);
			// 	$consignee_id = $this->kaabar->save('consignees', $consignee);
			// }
			// else {
			// 	$consignee_id = $consignee['id'];
			// }

			// $job['consignee_id']      = $consignee_id;
			$job['consignee']         = $eworkreg['CONS_NAME'];
			//$job['discharge_port_id'] = $ports['id'];
			$this->db->update('jobs', $job, array('id' => $id));

			if (count($ares) > 0) {
				$child_job['are_no']   = implode(', ', array_keys($ares));
				$child_job['are_date'] = reset($ares);
			}
			$child_job['job_id']            = $id;
			$child_job['vi_job_no']         = $job_no;
			$child_job['stuffing_type']     = $job['stuffing_type'];
			$child_job['shipper_site_id']   = $job['shipper_site_id'];
			$child_job['godown_id']         = $job['godown_id'];
			$child_job['cfs_id']            = $job['cfs_id'];
			$child_job['packages']          = $eworkreg['ActNoOfPkgs'];
			$child_job['package_type_id']   = (isset($package_types['id']) ? $package_types['id'] : 0);
			$child_job['net_weight']        = $eshipdtl['NET_WT'];
			$child_job['net_weight_unit']   = $eshipdtl['NETWT_UNIT'];
			$child_job['gross_weight']      = $eshipdtl['GR_WT'];
			$child_job['gross_weight_unit'] = $eshipdtl['GR_UNIT'];
			$child_job['fob_value']         = $eworkreg['fob_value'];
			$child_job['fob_currency']      = $eworkreg['TotInv_Cur'];
			$child_job['marks']             = $eworkreg['MARKS'];
			$child_job_id                   = $this->kaabar->save('child_jobs', $child_job, array('id' => $child_job['id']));

			// Saving Products
			foreach ($einvdtl as $eid) {

				// Add Currency
				$query = $this->db->query("SELECT id FROM currencies WHERE code = ?", array($eid['INV_CUR']));
				$currency = $query->row_array();
				if (! $currency) {
					$currency_id = $this->kaabar->save('currencies', array('code' => $eid['INV_CUR']));
				}
				else {
					$currency_id = $currency['id'];
				}

				$invoice = array(
					'job_id'        => $id,
					'child_job_id'  => $child_job_id,
					'invoice_no'    => $eid['INV_NO'],
					'invoice_date'  => $eid['INV_DATE'],
					'toi'           => $eid['TOI'],
					'currency_id'   => $currency_id,
					'invoice_value' => $eid['INV_VALUE']
				);
				$invoice_id = $this->kaabar->save('job_invoices', $invoice);

				foreach ($eproddtl as $prd) {
					if ($prd['Inv_ID'] == $eid['Inv_ID']) {
						$product_details = array(
							'job_invoice_id' => $invoice_id,
							'sr_no'             => $prd['PROD_SN'],
							'hs_code'           => $prd['RITC_NO'],
							'description'       => $prd['PROD_DESC'],
							'quantity'          => $prd['QTY'],
							'quantity_unit'     => $prd['UNIT']
						);
						$this->db->insert('export_product_details', $product_details);
					}
				}
			}

			// Saving Containers
			foreach ($expcontdtl as $c) {
				$query = $this->db->query("SELECT * FROM containers WHERE child_job_id = ? AND number = ?", array($child_job_id, $c['Cont_No']));
				$row   = $query->row_array();
				if ($row == false) $row['id'] = 0;

				$container = array(
					'job_id'            => $id,
					'child_job_id'      => $child_job_id,
					'container_type_id' => ($c['Cont_Size'] == 20 ? 2 : 9),
					'number'            => $c['Cont_No'],
					'seal'              => (is_null($c['Seal_No']) ? '' : $c['Seal_No'])
				);
				$container_id = $this->kaabar->save('containers', $container, ['id' => $row['id']]);
				// if ($row['id'] == 0) {
				// 	$container['container_id'] = $container_id;
				// 	$container['container_no'] = $container['number'];
				// 	unset($container['seal']);
				// 	unset($container['child_job_id']);
				// 	unset($container['number']);

				// 	$query = $this->db->query("SELECT * FROM deliveries_stuffings WHERE job_id = ? AND container_no = ?", array($job_id, $r['CONT_NO']));
				// 	$row = $query->row_array();
				// 	if ($row == false) $row['id'] = 0;
					
				// 	$this->kaabar->save('deliveries_stuffings', $container, $row);
				// }
			}
		}
	}
}
