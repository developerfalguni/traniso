<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/core/REST_Controller.php';

class Api extends REST_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('idex');
	}

	function import_deliveries_get($id) {
		$sql = "SELECT D.id, COALESCE(C.id, 0) AS container_id, C.container_type_id, J.id AS job_id, D.container_no, CT.size AS size, J.bl_no, 
			COALESCE(D.vehicle_no, '') AS vehicle_no, IF(D.dispatch_weight = 0, C.net_weight, D.dispatch_weight) AS dispatch_weight, 
			COALESCE(D.dispatch_type, '') AS dispatch_type, 
			COALESCE(D.unloading_location, '') AS unloading_location, COALESCE(D.unloading_by, '') AS unloading_by, 
			COALESCE(D.destuffing_agent, '') AS destuffing_agent, DATE_FORMAT(COALESCE(D.unloading_date, '0000-00-00'), '%d-%m-%Y') AS unloading_date, 
			COALESCE(D.gatepass_no, '') AS gatepass_no, DATE_FORMAT(COALESCE(D.gatepass_date, '0000-00-00'), '%d-%m-%Y') AS gatepass_date, 
			COALESCE(D.lr_no, '') AS lr_no, DATE_FORMAT(COALESCE(D.return_date, '0000-00-00'), '%d-%m-%Y') AS return_date
		FROM deliveries_stuffings D INNER JOIN jobs J ON D.job_id = J.id
			LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE D.id = ?";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		$this->response($row, 200);
	}

	function import_deliveries_post() {
		$row = $this->post();
		unset($row['size']);
		unset($row['bl_no']);
		$row['vehicle_no'] = strtoupper(preg_replace('/[^a-z0-9]/i', '', $row['vehicle_no']));

		$this->kaabar->save('deliveries_stuffings', $row);

		$sql = "SELECT D.id, C.id AS container_id, C.container_type_id, J.id AS job_id, D.container_no, CT.size, J.bl_no, 
			COALESCE(D.vehicle_no, '') AS vehicle_no, COALESCE(D.dispatch_type, '') AS dispatch_type, 
			COALESCE(D.unloading_location, '') AS unloading_location, COALESCE(D.unloading_by, '') AS unloading_by, 
			COALESCE(D.destuffing_agent, '') AS destuffing_agent, DATE_FORMAT(COALESCE(D.unloading_date, '0000-00-00'), '%d-%m-%Y') AS unloading_date, 
			COALESCE(D.gatepass_no, '') AS gatepass_no, DATE_FORMAT(COALESCE(D.gatepass_date, '0000-00-00'), '%d-%m-%Y') AS gatepass_date, 
			COALESCE(D.lr_no, '') AS lr_no, DATE_FORMAT(COALESCE(D.return_date, '0000-00-00'), '%d-%m-%Y') AS return_date
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
			INNER JOIN jobs J ON C.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN import_details ID ON J.id = ID.job_id
			LEFT OUTER JOIN deliveries_stuffings D ON C.id = D.container_id
		WHERE C.id = ?";
		$query = $this->db->query($sql, array($row['container_id']));
		$row = $query->row_array();
		$this->response($row, 200);
	}

	function import_deliveries_put($id) {
		$row = $this->put();
		unset($row['size']);
		unset($row['bl_no']);
		$row['vehicle_no'] = strtoupper(preg_replace('/[^a-z0-9]/i', '', $row['vehicle_no']));

		$this->kaabar->save('deliveries_stuffings', $row, array('id' => $id));

		$sql = "SELECT D.id, C.id AS container_id, C.container_type_id, J.id AS job_id, D.container_no, CT.size, J.bl_no, 
			COALESCE(D.vehicle_no, '') AS vehicle_no, COALESCE(D.dispatch_type, '') AS dispatch_type, 
			COALESCE(D.unloading_location, '') AS unloading_location, COALESCE(D.unloading_by, '') AS unloading_by, 
			COALESCE(D.destuffing_agent, '') AS destuffing_agent, DATE_FORMAT(COALESCE(D.unloading_date, '0000-00-00'), '%d-%m-%Y') AS unloading_date, 
			COALESCE(D.gatepass_no, '') AS gatepass_no, DATE_FORMAT(COALESCE(D.gatepass_date, '0000-00-00'), '%d-%m-%Y') AS gatepass_date, 
			COALESCE(D.lr_no, '') AS lr_no, DATE_FORMAT(COALESCE(D.return_date, '0000-00-00'), '%d-%m-%Y') AS return_date
		FROM deliveries_stuffings D INNER JOIN jobs J ON D.job_id = J.id
			LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE D.id = ?";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		$this->response($row, 200);
	}

	function import_deliveries_delete($id){
		$this->db->delete('deliveries_stuffings', array('id'=>$id));
		echo $id;
	}


	function export_deliveries_get($container_id) {
		$sql = "SELECT D.id, C.job_id, C.id AS container_id, C.container_type_id, D.container_no, CT.size, J.sb_no, D.vehicle_no, D.driver_contact_no,
			D.pickup_location, D.pickup_port_id, ED.stuffing_details, ED.stuffing_location,
			DATE_FORMAT(D.stuffing_date, '%d-%m-%Y') AS stuffing_date, DATE_FORMAT(D.gate_in_date, '%d-%m-%Y') AS gate_in_date, 
			DATE_FORMAT(D.gate_out, '%d-%m-%Y') AS gate_out, D.transporter, D.lr_no
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
			INNER JOIN jobs J ON C.job_id = J.id
			INNER JOIN export_details ED ON J.id = ED.job_id
			LEFT OUTER JOIN deliveries_stuffings D ON C.id = D.container_id
		WHERE C.id = ?";
		$query = $this->db->query($sql, array($container_id));
		$row = $query->row_array();
		$this->response($row, 200);
	}

	function export_deliveries_post() {
		$row = $this->post();
		unset($row['size']);
		unset($row['sb_no']);
		unset($row['stuffing_details']);
		unset($row['stuffing_location']);
		$row = array_map(function($v) {
			return (is_null($v)) ? "" : $v;
		}, $row);

		$row['vehicle_no'] = strtoupper(preg_replace('/[^a-z0-9]/i', '', $row['vehicle_no']));

		$this->kaabar->save('deliveries_stuffings', $row);

		$sql = "SELECT D.id, C.job_id, C.id AS container_id, C.container_type_id, D.container_no, CT.size, J.sb_no, D.vehicle_no, D.driver_contact_no,
			D.pickup_location, COALESCE(IP.name, '') AS pickup_port_name, ED.stuffing_details, ED.stuffing_location,
			DATE_FORMAT(D.stuffing_date, '%d-%m-%Y') AS stuffing_date, DATE_FORMAT(D.gate_in_date, '%d-%m-%Y') AS gate_in_date, 
			DATE_FORMAT(D.gate_out, '%d-%m-%Y') AS gate_out, D.transporter, D.lr_no
		FROM containers C INNER JOIN container_types CT ON C.container_type_id = CT.id
			INNER JOIN jobs J ON C.job_id = J.id
			INNER JOIN export_details ED ON J.id = ED.job_id
			LEFT OUTER JOIN deliveries_stuffings D ON C.id = D.container_id
			LEFT OUTER JOIN indian_ports IP ON D.pickup_port_id = IP.id
		WHERE C.id = ?";
		$query = $this->db->query($sql, array($row['container_id']));
		$row = $query->row_array();
		$this->response($row, 200);
	}

	function export_deliveries_put($id) {
		$row = $this->put();
		unset($row['size']);
		unset($row['sb_no']);
		unset($row['stuffing_details']);
		unset($row['stuffing_location']);
		$row = array_map(function($v) {
			return (is_null($v)) ? "" : $v;
		}, $row);

		$row['vehicle_no'] = strtoupper(preg_replace('/[^a-z0-9]/i', '', $row['vehicle_no']));
		
		$this->kaabar->save('deliveries_stuffings', $row, array('id' => $id));

		$sql = "SELECT D.id, C.job_id, C.id AS container_id, C.container_type_id, D.container_no, CT.size, J.sb_no, D.vehicle_no, D.driver_contact_no,
			D.pickup_location, COALESCE(IP.name, '') AS pickup_port_name, ED.stuffing_details, ED.stuffing_location,
			DATE_FORMAT(D.stuffing_date, '%d-%m-%Y') AS stuffing_date, DATE_FORMAT(D.gate_in_date, '%d-%m-%Y') AS gate_in_date, 
			DATE_FORMAT(D.gate_out, '%d-%m-%Y') AS gate_out, D.transporter, D.lr_no
		FROM (((((containers C INNER JOIN container_types CT ON C.container_type_id = CT.id)
			INNER JOIN jobs J ON C.job_id = J.id)
			INNER JOIN parties P ON J.party_id = P.id)
			INNER JOIN export_details ED ON J.id = ED.job_id)
			LEFT OUTER JOIN deliveries_stuffings D ON C.id = D.container_id)
			LEFT OUTER JOIN indian_ports IP ON D.pickup_port_id = IP.id
		WHERE D.id = ?";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		$this->response($row, 200);
	}
}
