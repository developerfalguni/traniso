<?php

class Transport_rate extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('transport');
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
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
		
		$data['list']['heading'] = array('ID', 'Ledger', 'From Location', 'To Location', 'Price 20', 'Price 40', 'Price', 'WEF Date');
		$data['list']['class'] = array(
			'id'            => 'ID', 
			'ledger_name'   => 'Text',
			'from_location' => 'Text',
			'to_location'   => 'Text',
			'price_20'      => 'Rupee',
			'price_40'      => 'Rupee',
			'price'         => 'Rupee',
			'wef_date'      => 'Text',);

		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->transport->countRates($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->transport->getRates($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="icon-white icon-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('ledger_id', 'Ledger Name', 'trim|required');
		
		$row = $this->transport->getRate($id);
		if($row == false) {
			$row = array(
				'id'          => 0, 
				'company_id'  => $this->transport->getCompanyID(),
				'ledger_id'   => 0,
				'ledger_name' => '',
				'price_20'    => array(),
				'price_40'    => array(),
				'price'       => array(),
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['focus_id']   = $row['id'] > 0 ? 'LedgerName' : 'FromLocationID';
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);

			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
				$ledger_id   = $this->input->post('ledger_id');
				
				$from_location_ids = $this->input->post('from_location_id');
				if ($from_location_ids != null) {
					$to_location_ids = $this->input->post('to_location_id');
					$product_ids     = $this->input->post('product_id');
					$price_20s       = $this->input->post('price_20');
					$price_40s       = $this->input->post('price_40');
					$prices          = $this->input->post('price');
					$weights         = $this->input->post('weight');
					$wef_dates       = $this->input->post('wef_date');

					foreach ($from_location_ids as $index => $from_location_id) {
						if (! in_array("$index", $delete_ids)) {
							$row = array(
								'from_location_id' => $from_location_id,
								'to_location_id'   => $to_location_ids[$index],
								'product_id'       => $product_ids[$index],
								'price_20'         => $price_20s[$index] > 0 ? $price_20s[$index] : 0,
								'price_40'         => $price_40s[$index] > 0 ? $price_40s[$index] : 0,
								'price'            => $prices[$index] > 0 ? $prices[$index] : 0,
								'weight'           => $weights[$index] > 0 ? $weights[$index] : 0,
								'wef_date'         => $wef_dates[$index],
							);
							$id = $this->kaabar->save($this->_table, $row, array('id' => $index));
						}
					}
				}

				if ($delete_ids != null) {
					foreach ($delete_ids as $index) {
						$this->db->delete($this->_table, array('id' => $index));
					}
				}

				$from_location_ids = $this->input->post('new_from_location_id');
				if ($from_location_ids != null) {
					$to_location_ids = $this->input->post('new_to_location_id');
					$product_ids     = $this->input->post('new_product_id');
					$price_20s       = $this->input->post('new_price_20');
					$price_40s       = $this->input->post('new_price_40');
					$prices          = $this->input->post('new_price');
					$weights         = $this->input->post('new_weight');
					$wef_dates       = $this->input->post('new_wef_date');

					foreach ($from_location_ids as $index => $from_location_id) {
						if ($from_location_id > 0) {
							$row = array(
								'company_id'       => $this->transport->getCompanyID(),
								'ledger_id'        => $ledger_id,
								'from_location_id' => $from_location_id,
								'to_location_id'   => $to_location_ids[$index],
								'product_id'       => $product_ids[$index],
								'price_20'         => $price_20s[$index] > 0 ? $price_20s[$index] : 0,
								'price_40'         => $price_40s[$index] > 0 ? $price_40s[$index] : 0,
								'price'            => $prices[$index] > 0 ? $prices[$index] : 0,
								'weight'           => $weights[$index] > 0 ? $weights[$index] : 0,
								'wef_date'         => $wef_dates[$index],
							);
							$id = $this->kaabar->save($this->_table, $row);
						}
					}
				}
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
}
