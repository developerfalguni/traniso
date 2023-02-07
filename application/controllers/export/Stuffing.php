<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stuffing extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table    = 'deliveries_stuffings';
		$this->_table2   = 'stuffing_invoices';

		$folder          = 'documents/photos/';
		$this->_path     = FCPATH . $folder;
		$this->_path_url = base_url($folder);

		$this->load->model('export');
	}
	
	function index($job_id = 0) {
		$job_id       = intval($job_id);
		$data['rows'] = $this->_getStuffing($job_id);
		
		$data['job_id']       = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		$data['jobs']         = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($job_id, $id = 0) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('container_no', 'Container No', 'trim|required');
		
		$default_company = $this->session->userdata('default_company');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = [
				'id'                => 0,
				'job_id'            => $job_id,
				'lr_no'             => '',
				'vehicle_no'        => '',
				'container_no'      => '',
				'seal_no'           => '',
				'wire_seal_no'      => '',
				'excise_seal_no'    => '',
				'container_type_id' => 0,
				'pickup_date'       => date('d-m-Y'),
				'stuffing_date'     => date('d-m-Y'),
				'units'             => 0,
				'unit_id'           => 0,
				'gross_weight'      => 0,
				'nett_weight'       => 0,
				'flexi_tank_no'     => '',
				'remarks'           => '',
				'container_id'      => 0,
			];
		}

		$data['id']                  = ['id' => $id];
		$data['job_id']              = ['id' => $job_id];
		$data['child_job_id']        = ['id' => 0];
		$data['row']                 = $row;
		$data['row']['job_invoices'] = $this->_getInvoices($id);

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']       = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['shipper_id'] = $this->kaabar->getField('jobs', $job_id, 'id', 'shipper_id');
			$data['rows']       = $this->_getStuffing($job_id);
			$data['photos']     = $this->kaabar->getRows('job_photos', $id, 'stuffing_id');
			$data['invoices']   = $this->export->getInvoiceNos($job_id);

			$docdir           = $this->export->getDocFolder($this->_path, $id);
			$data['path_url'] = $this->_path_url.'/'.$docdir;

			$data['javascript'] = ['js/jquery.filtertable.min.js'];

			$data['page_title'] = "Stuffing Details";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id/$id");
			
			$data = array(
				'id'                => $id,
				'job_id'            => $job_id,
				'lr_no'             => $this->input->post('lr_no'),
				'vehicle_no'        => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('vehicle_no'))),
				'container_no'      => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('container_no'))),
				'seal_no'           => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('seal_no'))),
				'wire_seal_no'      => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('wire_seal_no'))),
				'excise_seal_no'    => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('excise_seal_no'))),
				'container_type_id' => $this->input->post('container_type_id'),
				'pickup_date'       => $this->input->post('pickup_date'),
				'stuffing_date'     => $this->input->post('stuffing_date'),
				'units'             => $this->input->post('units'),
				'unit_id'           => $this->input->post('unit_id'),
				'gross_weight'      => $this->input->post('gross_weight'),
				'nett_weight'       => $this->input->post('nett_weight'),
				'flexi_tank_no'     => $this->input->post('flexi_tank_no'),
				'remarks'           => $this->input->post('remarks'),
				'container_id'      => $this->kaabar->getField('containers', array(
						'job_id' => $this->input->post('job_id'),
						'number' => strtoupper(preg_replace('/[^a-z0-9]/i', '', $this->input->post('container_no')))
					), 'id', 'id')
			);
			$id = $this->kaabar->save('deliveries_stuffings', $data, ['id' => $id]);

			$existing_siids = [0];
			if (is_array($this->input->post('job_invoice_id'))) {
				foreach ($this->input->post('job_invoice_id') as $invoice_id) {
					$siid = $this->kaabar->getField('stuffing_invoices', ['stuffing_id' => $id, 'job_invoice_id' => $invoice_id], 'id', 'id');
					if ($siid > 0)
						$this->kaabar->save('stuffing_invoices', ['stuffing_id' => $id, 'job_invoice_id' => $invoice_id], ['id' => $siid]);
					else
						$siid = $this->kaabar->save('stuffing_invoices', ['stuffing_id' => $id, 'job_invoice_id' => $invoice_id]);
					$existing_siids[] = $siid;
				}
			}
			$this->db->query("DELETE FROM stuffing_invoices WHERE stuffing_id = ? AND id NOT IN (" . implode(', ', $existing_siids) . ")", [$id]);

			// Stuffing Photos
			$config = [
				'upload_path'      => './php_uploads/',
				'allowed_types'    => 'jpg|jpeg|png|bmp|tiff',
				'file_ext_tolower' => true,
				'overwrite'        => true,
				'encrypt_name'     => true,
			];
			$this->load->library('upload', $config);

			$types  = $this->input->post('type');
			$files  = $_FILES['image'];
			$images = [];
			foreach ($files['name'] as $index => $image) {
				$_FILES['image[]']['name']     = $files['name'][$index];
				$_FILES['image[]']['type']     = $files['type'][$index];
				$_FILES['image[]']['tmp_name'] = $files['tmp_name'][$index];
				$_FILES['image[]']['error']    = $files['error'][$index];
				$_FILES['image[]']['size']     = $files['size'][$index];

				$config['file_name'] = $image;
				$this->upload->initialize($config);
				if ($this->upload->do_upload("image[]")) {
					$img     = $this->upload->data();
					$docdir  = $this->export->getDocFolder($this->_path, $id);
					$newfile = $types[$index].'_'.uniqid().$img['file_ext'];
					rename($img['full_path'], $this->_path.$docdir.$newfile);

					$row = [
						'job_id'       => $job_id,
						'stuffing_id'  => $id,
						'container_id' => $data['container_id'],
						'type'         => $types[$index],
						'file'         => $newfile,
					];
					$this->kaabar->save('job_photos', $row);
				}
			}

			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function _getStuffing($job_id) {

		$result = [
			'containers' => [],
			'stuffing'   => [],
		];

		$sql   = "SELECT SUM(PC.containers) AS containers, PC.container_type_id, CONCAT(CT.size, ' ', CT.code) AS container_type
		FROM job_containers PC
			INNER JOIN container_types CT ON PC.container_type_id = CT.id
		WHERE PC.job_id > 0 AND PC.job_id = ? 
		GROUP BY PC.container_type_id";
		$query = $this->db->query($sql, array($job_id));
		$rows  = $query->result_array();
		foreach ($rows as $row) {
			$result['containers'][$row['container_type_id']] = array('container_type' => $row['container_type'], 'count' => $row['containers']);
		}

		$sql   = "SELECT S.id, S.job_id, GROUP_CONCAT(COALESCE(SI.job_invoice_id, 0) SEPARATOR ', ') AS job_invoice_id,
			GROUP_CONCAT(JI.invoice_no SEPARATOR ',') AS invoice_nos,  
			S.lr_no, S.vehicle_no, S.container_type_id, S.container_no, S.seal_no, S.wire_seal_no, S.excise_seal_no,
			DATE_FORMAT(S.pickup_date, '%d-%m-%Y %H:%i') AS pickup_date, DATE_FORMAT(S.stuffing_date, '%d-%m-%Y') AS stuffing_date, 
			S.units, S.unit_id, U.code AS unit, S.gross_weight, S.nett_weight, S.flexi_tank_no, S.driver_contact_no, S.remarks, S.container_id,
			CONCAT(CT.size, ' ', CT.code) AS container_type 
		FROM deliveries_stuffings S 
			INNER JOIN container_types CT ON S.container_type_id = CT.id
			LEFT OUTER JOIN units U ON S.unit_id = U.id
			LEFT OUTER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
			LEFT OUTER JOIN job_invoices JI ON SI.job_invoice_id = JI.id
		WHERE S.job_id > 0 AND S.job_id = ?
		GROUP BY S.id";
		$query = $this->db->query($sql, array($job_id));
		$rows  = $query->result_array();
		$result['stuffing'] = $rows;
		foreach ($rows as $row) {
			if (isset($result['containers'][$row['container_type_id']]))
				$result['containers'][$row['container_type_id']]['count']--;
		}
		return $result;
	}

	function _getInvoices($id) {
		$sql = "SELECT SI.job_invoice_id
		FROM stuffing_invoices SI
		WHERE SI.stuffing_id = ?";
		$rows = $this->db->query($sql, [$id])->result_array();
		$result = [];
		foreach ($rows as $row) {
			$result[$row['job_invoice_id']] = $row['job_invoice_id'];
		}
		return $result;
	}

	function deletePhoto($job_id, $id) {
		$row = $this->kaabar->getRow('job_photos', $id);
		$this->kaabar->delete('job_photos', $id);
		$docdir  = $this->export->getDocFolder($this->_path, $row['stuffing_id']);
		unlink($this->_path.$docdir.$row['file']);
		setSessionAlert('Image Deleted Successfully', 'success');
		redirect($this->agent->referrer());
	}

	function deleteStuffing($job_id, $id) {
		$this->db->query("DELETE FROM job_photos WHERE stuffing_id = ?", [$id]);
		$this->kaabar->delete($this->_table, $id);
		setSessionAlert('Deleted Successfully', 'success');
	
		redirect($this->_clspath.$this->_class."/index/$job_id");
	}
}
