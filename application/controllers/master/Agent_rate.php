<?php

define('MAX_RANGE', 15);

class Agent_rate extends MY_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model('office');
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
			//redirect($this->_class);
		}

		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}

		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('ID', 'Agent', 'Type', 'Port', 'Product', 'Destuffing Type'),
			'class' => array(
				'id'              => 'ID',
				'agent_name'      => 'Text',
				'type'            => 'Text',
				'port_name'       => 'Text',
				'product_name'    => 'Text',
				'destuffing_type' => 'Text'
				),
			'link_col' => 'id',
			'link_url' => $this->_clspath.$this->_class."/edit/");

		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->office->countAgentRates($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getAgentRates($search, $starting_row, $config['per_page']);

		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="icon-white icon-plus"></i> Add', 'class="btn btn-success"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function copy() {
		$new_agent_id  = $this->input->post('new_agent_id');
		$agent_rate_id = $this->input->post('agent_rate_id');

		$ar = $this->kaabar->getRow('agent_rates', array('id' => $agent_rate_id));

		$datas = $this->kaabar->getRows('agent_rates', array(
			'agent_id'        => $ar['agent_id'],
			'indian_port_id'  => $ar['indian_port_id'],
			'product_id'      => $ar['product_id'],
			'currency_id'     => $ar['currency_id'],
			'type'            => $ar['type'],
			'destuffing_type' => $ar['destuffing_type']
		));
		foreach($datas as $data) {
			$agent_rate_id = $data['id'];
			$data['id'] = 0;
			$data['agent_id'] = $new_agent_id;
			$id = $this->kaabar->save('agent_rates', $data, array('id' => 0));

			$sql = "INSERT INTO agent_tariffs (agent_rate_id, type, tariff_type, from_day, to_day, price_20, price_40)
				SELECT ?, type, tariff_type, from_day, to_day, price_20, price_40
				FROM agent_tariffs 
				WHERE agent_rate_id = ?";
			$this->db->query($sql, array($id, $agent_rate_id));
		}

		redirect($this->_clspath.$this->_class.'/edit/'.$id);
	}

	function edit($id = 0) {
		$id = intval($id);

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('agent_id', 'agent_id', 'trim|required');

		$row = $this->office->getAgentRate($id);
		if ($row == false) {
			$row = array(
				'id'              => 0,
				'agent_id'        => 0,
				'agent_name'      => '',
				'indian_port_id'  => 72,
				'product_id'      => 0,
				'type'            => 'Import',
				'destuffing_type' => 'Factory',
				'rates'           => array()
			);
		}
		$data['id']  = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['rows']       = $this->kaabar->getRows($this->_table, array('agent_id' => $row['agent_id']));

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$particularss = $this->input->post('particulars');
				$delete_ids = $this->input->post('delete_id') == false? array("0" => "0") : $this->input->post('delete_id');
				if ($particularss) {
					$agent_id      = $this->input->post("agent_id");
					$currency_ids  = $this->input->post("currency_id");
					$calc_types    = $this->input->post("calc_type");
					$price_20s     = $this->input->post("price_20");
					$price_40s     = $this->input->post("price_40");
					$taxables      = $this->input->post("taxable");
					foreach ($particularss as $index => $particular) {
						$data = array(
							'agent_id'    => $agent_id,
							'currency_id' => $currency_ids[$index],
							'particulars' => $particular,
							'calc_type'   => $calc_types[$index],
							'price_20'    => $price_20s[$index],
							'price_40'    => $price_40s[$index],
							'taxable'     => $taxables[$index],
						);
						$this->kaabar->save($this->_table, $data, array('id' => $index));
					}
				}

				if ($delete_ids != null) {
					foreach ($delete_ids as $index => $tmp) {
						if ($index > 0) {
							$this->kaabar->delete($this->_table, array('id' => $index));
						}
					}
				}

				$particularss = $this->input->post('new_particulars');
				if ($particularss) {
					$agent_id     = $this->input->post("agent_id");
					$currency_ids = $this->input->post("new_currency_id");
					$calc_types   = $this->input->post("new_calc_type");
					$price_20s    = $this->input->post("new_price_20");
					$price_40s    = $this->input->post("new_price_40");
					$taxables     = $this->input->post("new_taxable");
					foreach ($particularss as $index => $particular) {
						if (strlen(trim($particular)) > 0) {
							$data = array(
								'agent_id'        => $agent_id,
								'indian_port_id'  => $this->input->post('indian_port_id'),
								'product_id'      => $this->input->post('product_id'),
								'currency_id'     => $currency_ids[$index],
								'type'            => $this->input->post('type'),
								'destuffing_type' => $this->input->post('destuffing_type'),
								'particulars'     => $particular,
								'calc_type'       => $calc_types[$index],
								'price_20'        => $price_20s[$index],
								'price_40'        => $price_40s[$index],
								'taxable'         => $taxables[$index],
							);
							$id = $this->kaabar->save($this->_table, $data);
						}
					}
				}
				$this->_updateTariff($this->input->post('agent_rate_id'));

				setSessionAlert('SAVED', 'success');
			}
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _updateTariff($agent_rate_id = 0) {
		if ($agent_rate_id == 0) return;

		$from_days  = $this->input->post('from_day');
		$delete_ids = $this->input->post('tariff_delete_id') == false? array("0" => "0") : $this->input->post('tariff_delete_id');
		if ($from_days) {
			$to_days      = $this->input->post("to_day");
			$price_20s    = $this->input->post("price_20");
			$price_40s    = $this->input->post("price_40");
			foreach ($from_days as $index => $from_day) {
				$data = array(
					'tariff_type' => $this->input->post('tariff_type'),
					'from_day'    => $from_day,
					'to_day'      => $to_days[$index],
					'price_20'    => $price_20s[$index],
					'price_40'    => $price_40s[$index],
				);
				$this->kaabar->save('agent_tariffs', $data, array('id' => $index));
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0) {
					$this->kaabar->delete('agent_tariffs', array('id' => $index));
				}
			}
		}

		$from_days = $this->input->post('new_from_day');
		if ($from_days) {
			$agent_id  = $this->input->post("agent_id");
			$to_days   = $this->input->post("new_to_day");
			$price_20s = $this->input->post("new_price_20");
			$price_40s = $this->input->post("new_price_40");
			foreach ($from_days as $index => $from_day) {
				if (strlen(trim($from_day)) > 0) {
					$data = array(
						'agent_rate_id' => $agent_rate_id,
						'tariff_type'   => $this->input->post('tariff_type'),
						'from_day'      => $from_day,
						'to_day'        => $to_days[$index],
						'price_20'      => $price_20s[$index],
						'price_40'      => $price_40s[$index],
					);
					$id = $this->kaabar->save('agent_tariffs', $data);
				}
			}
		}
	}

	function ajax() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));

			$sql = "SELECT id, type, name FROM agents
			WHERE (type = 'Line' OR type = 'CFS') AND name LIKE '%$search%' 
			ORDER BY name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxAgentRates() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			
			$sql = "SELECT AR.id, A.name, IP.name AS port_name, PRD.name AS product_name, AR.type, AR.destuffing_type
			FROM agent_rates AR INNER JOIN agents A ON AR.agent_id = A.id
				INNER JOIN indian_ports IP ON AR.indian_port_id = IP.id
				INNER JOIN products PRD ON AR.product_id = PRD.id
			WHERE A.name LIKE '%$search%' 
			GROUP BY AR.agent_id";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function loadRates($id) {
		if (intval($id) == 0) return;
		
		$rows = $this->kaabar->getRows($this->_table, array('agent_id' => $agent_id));

		header('Content-type: application/json');
		echo json_encode($rows);
	}

	function ajaxTariff($id) {
		if (intval($id) == 0) return;
		
		$rows = $this->kaabar->getRows('agent_tariffs', array('agent_rate_id' => $id));
		
		header('Content-type: application/json');
		echo json_encode($rows);
	}
}
