<?php

class Vessel extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->load->model('office');
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('ID', 'Type', 'Terminal', 'Name', 'Voyage', 'ETA', 'ETD', 'Agent Name'),
			'class' => array(
				'id'          => 'ID',
				'type'        => 'Label',
				'terminal'    => 'Code',
				'name'        => 'Text bold',
				'voyage_no'   => 'Code',
				'eta_date'    => 'Date',
				'etd_date'    => 'Date',
				'agent_name'  => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		$data['label_class'] = $this->office->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->office->countVessels($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getVessels($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';

		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('type', 'Vessel Type', 'trim|required');
		$this->form_validation->set_rules('name', 'Vessel Name', 'trim|required');
		$this->form_validation->set_rules('voyage_no', 'Voyage No', 'trim|required|callback__is_duplicate['.$id.', name]');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'                    => 0,
				'service_id'            => 0,
				'agent_id'              => 0,
				'type'                  => '',
				'prefix'                => 'MV',
				'name'                  => '', 
				'voyage_no'             => '', 
				'vcn_no'                => '', 
				'rotation_no'           => '', 
				'igm_no'                => '', 
				'igm_date'              => '00-00-0000', 
				'egm_no'                => '', 
				'egm_date'              => '00-00-0000', 
				'indian_port_id'        => 190,
				'berth_no'              => '',
				'terminal_id'           => 0,
				'gld_date'              => '00-00-0000',
				'eta_date'              => '00-00-0000 00:00:00',
				'etd_date'              => '00-00-0000 00:00:00',
				'berthing_date'         => '00-00-0000 00:00:00',
				'barging_date'          => '00-00-0000 00:00:00',
				'sailing_date'          => '00-00-0000 00:00:00',
				'pgr_begin_date'        => '00-00-0000 00:00:00',
				'doc_cutoff_date'       => '00-00-0000 00:00:00',
				'gate_cutoff_date'      => '00-00-0000 00:00:00',
				'ens_cutoff_date'       => '00-00-0000 00:00:00',
				'import_exchange_rate'  => 0,
				'export_exchange_rate'  => 0,
				'total_bl_quantity'     => 0,
				'draft_survey_quantity' => 0,
				'remarks'               => '',
			);
		}
		
		$data['id']  = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['ledgers'] = $this->accounting->getVesselLedgers($id);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			$data = array(
				'service_id'            => $this->input->post('service_id'),
				'agent_id'              => $this->input->post('agent_id'),
				'type'                  => $this->input->post('type'),
				'prefix'                => $this->input->post('prefix'),
				'name'                  => $this->input->post('name'),
				'voyage_no'             => $this->input->post('voyage_no'),
				'vcn_no'                => $this->input->post('vcn_no'),
				'rotation_no'           => $this->input->post('rotation_no'),
				'igm_no'                => $this->input->post('igm_no'),
				'igm_date'              => $this->input->post('igm_date'),
				'egm_no'                => $this->input->post('egm_no'),
				'egm_date'              => $this->input->post('egm_date'),
				'indian_port_id'        => $this->input->post('indian_port_id'),
				'berth_no'              => $this->input->post('berth_no'),
				'terminal_id'           => $this->input->post('terminal_id'),
				'eta_date'              => $this->input->post('eta_date'),
				'etd_date'              => $this->input->post('etd_date'),
				'berthing_date'         => $this->input->post('berthing_date'),
				'barging_date'          => $this->input->post('barging_date'),
				'sailing_date'          => $this->input->post('sailing_date'),
				'pgr_begin_date'        => $this->input->post('pgr_begin_date'),
				'doc_cutoff_date'       => $this->input->post('doc_cutoff_date'),
				'gate_cutoff_date'      => $this->input->post('gate_cutoff_date'),
				'ens_cutoff_date'       => $this->input->post('ens_cutoff_date'),
				'import_exchange_rate'  => $this->input->post('import_exchange_rate'),
				'export_exchange_rate'  => $this->input->post('export_exchange_rate'),
				'total_bl_quantity'     => $this->input->post('total_bl_quantity'),
				'draft_survey_quantity' => $this->input->post('draft_survey_quantity'),
				'remarks'               => $this->input->post('remarks')
			);

			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_updateLedgers($id);

			// Update indian_port_id of all jobs where vessel_id = $id
			$this->db->update('jobs', array('indian_port_id' => $this->input->post('indian_port_id')), array('vessel_id' => $id));
			
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _is_duplicate($voyage_no, $args) {
		$args_arr = explode(',', $args);
		$query = $this->db->query("SELECT id FROM vessels WHERE id != ? AND name = ? AND voyage_no = ?", array(
			$args_arr[0], $_POST[trim($args_arr[1])], $voyage_no));
		$row   = $query->row_array();
		if ($row) {
			$this->form_validation->set_message('_is_duplicate', 'Duplicate Vessel found with Voyage No. ' . $voyage_no);
			return FALSE;
		}
		else {
			return TRUE;
		}
	}

	function _updateLedgers($id) {
		$default_company = $this->session->userdata('default_company');
		$delete_ids      = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$codes           = $this->input->post('vcode');
		$new_codes       = $this->input->post('new_code');

		if ($codes != null) {
			$names  = $this->input->post('vname');
			$agids  = $this->input->post('vagid');
			$opbals = $this->input->post('vopbal');
			$drcrs  = $this->input->post('vdrcr');
			foreach ($codes as $index => $code) {
				if (! in_array("$index", $delete_ids)) {
					$data = array(
						'code'             => strtoupper($code),
						'name'             => $names[$index],
						'account_group_id' => $agids[$index],
						'opening_balance'  => $opbals[$index],
						'dr_cr'            => $drcrs[$index]
					);
					$this->kaabar->save('ledgers', $data, ['id' => $index]);
				}
			}
		}
		
		/*if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					// If used, Cannot Delete
					// $this->kaabar->delete('ledgers', $index);
					setSessionError('Delete is Disabled to maintain database integrity.');
				}
			}
		}*/

		if ($new_codes != null) {
			$vlids  = $this->input->post('new_vlid');
			$names  = $this->input->post('new_name');
			$agids  = $this->input->post('new_agid');
			$opbals = $this->input->post('new_opbal');
			$drcrs  = $this->input->post('new_drcr');
			foreach ($new_codes as $index => $new_code) {
				
				if ($vlids[$index] > 0)
					$row = $this->kaabar->getRow('ledgers', $vlids[$index]);
				else 
					$row = array('id' => 0);

				if (strlen(trim($new_code)) > 0 && strlen(trim($names[$index])) > 0) {
					$data = array(
						'company_id'       => $default_company['id'],
						'category'         => 'Vessel',
						'code'             => strtoupper($new_code),
						'name'             => $names[$index],
						'account_group_id' => $agids[$index],
						'opening_balance'  => $opbals[$index],
						'dr_cr'            => $drcrs[$index],
						'vessel_id'        => $id
					);
					$vlid = $this->kaabar->save('ledgers', $data, ['id' => $id]);
				}
			}
		}
	}

	function delete($id = 0, $field = 'id') {
		$this->kaabar->delete($this->_table, $id);
		$this->db->update('ledgers', array('vessel_id' => 0), array('vessel_id' => $id));
		$this->db->update('jobs', array('vessel_id' => 0), array('vessel_id' => $id));
		$this->kaabar->delete($this->_table, $id);
		setSessionAlert('Vessel Deleted Successfully', 'success');
		
		redirect($this->agent->referrer());
	}

	function ajax($table = FALSE, $field = 'name') {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, prefix, name, voyage_no, CONCAT(prefix, name, voyage_no) AS vessel, DATE_FORMAT(etd_date, '%d-%m-%Y') AS etd_date FROM $this->_table
			WHERE name LIKE '%$search%' OR voyage_no LIKE '%$search%' 
			ORDER BY name
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxLedgers() {
		if ($this->input->is_ajax_request()) {
			$default_company = $this->session->userdata('default_company');
			$company_id = $default_company['id'];
			unset($default_company);
			
			$search   = addslashes(strtolower($this->input->post_get('term')));
		
			$sql = "SELECT L.id, L.code, L.name, L.account_group_id, L.opening_balance, L.dr_cr
			FROM ledgers L
			WHERE (L.company_id = $company_id AND 
				L.category = 'Vessel') AND 
				(L.code LIKE '%$search%' OR L.name LIKE '%$search%')
			ORDER BY name LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo 
			"Access Denied";
		}
	}
}
