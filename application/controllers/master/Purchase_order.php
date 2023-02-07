<?php

class Purchase_order extends MY_Controller {
	var $_table2;
	
	function __construct() {
		parent::__construct();
		
		$this->_table2 = 'purchase_order_details';
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
			//$this->session->unset_userdata($this->_class.'_search');
			//redirect($this->_clspath.$this->_class);
		}

		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		if ($search == false) $search = '';
		$data['search'] = $search;
		$data['show_search'] = true;
				
 		$data['list']['heading'] = array('ID', 'Date', 'No', 'Party', 'Remarks');
		$data['list']['class'] = array(
			'id'         => 'ID',
			'date'       => '_Date',
			'id2'        => 'Code',
			'party_name' => 'Text',
			'remarks'    => 'Text');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['per_page'] = Settings::get('rows_per_page');
		$config['total_rows'] = $this->_countPurchaseOrder($search);
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_getPurchaseOrder($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = $this->_class;
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[10]');
		$this->form_validation->set_rules('gatepass_no', 'Gate Pass No', 'trim|required');
		$this->form_validation->set_rules('vehicle_no', 'Vehicle No', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'         => 0,
				'company_id' => 0,
				'id2'        => 0,
				'date'       => date("d-m-Y"),
				'party_id'   => 0,
				'remarks'    => ''
			);
		}
		
		$data['id']        = array('id' => $id);
		$data['row']       = $row;
		$data['party_name'] = $this->kaabar->getField('parties', $row['party_id']);

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['details'] = $this->kaabar->getRows($this->_table2, $row['id'], 'purchase_order_id');
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");

			$company = $this->session->userdata('default_company');
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'company_id' => $company['id'],
					'id2'        => $this->input->post('id2'),
					'date'       => $this->input->post('date'),
					'party_id'   => $this->input->post('party_id'),
					'remarks'    => $this->input->post('remarks')
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				$this->_update_details($id);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
	
	function _update_details($id) {
		$log_nos   = $this->input->post('log_no');
		if ($log_nos != null) {
			$cha_codes = $this->input->post('cha_code');
			$bl_nos    = $this->input->post('bl_no');
			$lengths   = $this->input->post('length');
			$dias      = $this->input->post('dia');
			$hollows   = $this->input->post('hollow');
			$volumes   = $this->input->post('volume');
			$speciess  = $this->input->post('species');
			$marks     = $this->input->post('mark');
			foreach ($log_nos as $index => $log_no) {
				$row = array(
					'log_no'    => $log_nos[$index],
					'cha_code'  => strtoupper($cha_codes[$index]),
					'bl_no'     => strtoupper($bl_nos[$index]),
					'length'    => $lengths[$index],
					'dia'       => $dias[$index],
					'hollow'    => $hollows[$index],
					'volume'    => $volumes[$index],
					'species'   => strtoupper($speciess[$index]),
					'mark'      => strtoupper($marks[$index])
				);
				$this->kaabar->save('log_lists', $row, array('id' => $index));
			}
		}

		$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0 && Auth::hasAccess(Auth::DELETE))
					$this->kaabar->delete($this->_table2, array('id' => $index));
			}
		}

		$llids = $this->input->post('new_llid');
		if ($llids != null) {
			$log_nos   = $this->input->post('new_log_no');
			$cha_codes = $this->input->post('new_cha_code');
			$bl_nos    = $this->input->post('new_bl_no');
			$lengths   = $this->input->post('new_length');
			$dias      = $this->input->post('new_dia');
			$hollows   = $this->input->post('new_hollow');
			$volumes   = $this->input->post('new_volume');
			$speciess  = $this->input->post('new_species');
			$marks     = $this->input->post('new_mark');
			foreach ($llids as $index => $llid) {
				if ($llid > 0) {
					$row = $this->kaabar->getRow($this->_table2, array('log_list_id' => $llid, 'log_delivery_id' => $id));
					if ($row == false) {
						$row = array(
							'log_list_id'     => $llid,
							'log_delivery_id' => $id
						);
						$this->kaabar->save($this->_table2, $row);
					}
				}
				else if ($this->input->post('vessel_id') > 0 && 
					$llid == 0 && 
					strlen($log_nos[$index]) > 0 && 
					strlen($bl_nos[$index]) > 0 && 
					strlen($volumes[$index]) > 0) {
					$row = array(
						'vessel_id' => $this->input->post('vessel_id'),
						'job_id'    => 0,
						'log_no'    => $log_nos[$index],
						'cha_code'  => strtoupper($cha_codes[$index]),
						'bl_no'     => strtoupper($bl_nos[$index]),
						'length'    => $lengths[$index],
						'dia'       => $dias[$index],
						'hollow'    => $hollows[$index],
						'volume'    => $volumes[$index],
						'species'   => strtoupper($speciess[$index]),
						'mark'      => strtoupper($marks[$index])
					);
					$llid = $this->kaabar->save('log_lists', $row);
					$row = array(
						'log_list_id'     => $llid,
						'log_delivery_id' => $id
					);
					$this->kaabar->save($this->_table2, $row);
				}
			}
		}
	}

	function _countPurchaseOrder($search) {
		$sql = "SELECT COUNT(PD.id) AS numrows 
		FROM $this->_table PD INNER JOIN parties P ON PD.party_id = P.id
		WHERE DATE_FORMAT(PD.date, '%d-%m-%Y') LIKE '%$search%' OR
			PD.id2 LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			PD.remarks LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}
	
	function _getPurchaseOrder($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT PD.id, PD.id2, DATE_FORMAT(PD.date, '%d-%m-%Y') AS date, P.name AS party_name, PD.remarks
		FROM $this->_table PD INNER JOIN parties P ON PD.party_id = P.id
		WHERE DATE_FORMAT(PD.date, '%d-%m-%Y') LIKE '%$search%' OR
			PD.id2 LIKE '%$search%' OR
			P.name LIKE '%$search%' OR
			PD.remarks LIKE '%$search%'
		GROUP BY PD.id
		ORDER BY PD.date DESC
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}
