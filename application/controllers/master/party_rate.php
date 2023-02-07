<?php

class Party_rate extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
	
	function index($party_id, $bill_template_id) {
		redirect($this->_clspath.$this->_class.'/edit/'.$party_id.'/'.$bill_template_id);
	}
	
	function create() {
		$party_id         = $this->input->post('party_id');
		$bill_template_id = $this->input->post('bill_template_id');
		$wef_date         = convDate($this->input->post('wef_date'));

		$row = $this->kaabar->getRow('bill_templates', $bill_template_id);

		$sql = "INSERT INTO party_rates (wef_date, party_id, company_id, indian_port_id, berth_no, type, cargo_type, product_id, sr_no, bill_item_id, particulars, calc_type, unit_type, rate)
			SELECT '$wef_date', $party_id, company_id, indian_port_id, berth_no, type, cargo_type, product_id, sr_no, bill_item_id, particulars, calc_type, unit_type, 0
			FROM bill_templates
			WHERE company_id = ? AND indian_port_id = ? AND type = ? AND cargo_type = ? AND product_id = ?
			ORDER BY id";
		$this->db->query($sql, array($this->office->getCompanyID(), $row['indian_port_id'], $row['type'], $row['cargo_type'], $row['product_id']));

		redirect($this->_clspath.$this->_class.'/edit/'.$party_id.'/'.$bill_template_id);
	}

	function copy() {
		$party_id         = $this->input->post('party_id');
		$new_wef_date     = convDate($this->input->post('new_wef_date'));
		$copy_party_id    = $this->input->post('copy_party_id');
		$bill_template_id = $this->input->post('bill_template_id');
		$wef_date         = convDate($this->input->post('wef_date'));

		$row = $this->kaabar->getRow('bill_templates', $bill_template_id);

		$sql = "INSERT INTO party_rates (wef_date, party_id, company_id, indian_port_id, berth_no, type, cargo_type, product_id, sr_no, bill_item_id, particulars, calc_type, unit_type, rate)
			SELECT '$new_wef_date', $party_id, PR.company_id, PR.indian_port_id, PR.berth_no, PR.type, PR.cargo_type, PR.product_id, PR.sr_no, PR.bill_item_id, PR.particulars, PR.calc_type, PR.unit_type, PR.rate
			FROM party_rates PR 
			WHERE PR.party_id = ? AND PR.wef_date = ? AND PR.indian_port_id = ? AND PR.type = ? AND PR.cargo_type = ? AND PR.product_id = ?
			ORDER BY PR.sr_no";
		$this->db->query($sql, array($copy_party_id, $wef_date, $row['indian_port_id'], $row['type'], $row['cargo_type'], $row['product_id']));

		redirect($this->_clspath.$this->_class.'/edit/'.$party_id.'/'.$bill_template_id);
	}

	function edit($party_id, $bill_template_id) {

		$data['party_id'] = intval($party_id);
		$data['bill_template_id'] = intval($bill_template_id);
		$data['party'] = $this->kaabar->getRow('parties', $party_id);
		$data['rows']  = $this->office->getPartyRates($party_id, $bill_template_id);

		if ($this->input->post('rate') == false) {

			$data['page_title']    = humanize($this->_class);
			$data['hide_menu']     = true;
	
			$data['hide_title']    = true;
			$data['hide_footer']   = true;
			$data['page']          = $this->_clspath.$this->_class.'_edit';
	
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class.'/edit/'.$party_id.'/'.$bill_template_id);
			
			$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
			$rates = $this->input->post('rate');
			if ($rates != null) {
				$sr_nos        = $this->input->post('sr_no');
				$bill_item_ids = $this->input->post('bill_item_id');
				$particulars   = $this->input->post('particulars');
				$calc_type     = $this->input->post('calc_type');
				$unit_type     = $this->input->post('unit_type');
				$rates         = $this->input->post('rate');
				foreach ($rates as $index => $rate) {
					$this->kaabar->save($this->_table, array(
						'sr_no'        => $sr_nos[$index],
						'bill_item_id' => $bill_item_ids[$index],
						'particulars'  => $particulars[$index],
						'calc_type'    => $calc_type[$index],
						'unit_type'    => $unit_type[$index],
						'rate'         => $rate
					), ['id' => $index]);
				}
			}

			if ($delete_ids != null) {
				foreach ($delete_ids as $index => $value) {
					if ($index > 0) {
						$this->db->delete($this->_table, ['id' => $index]);
					}
				}
			}

			$berth_no = join(',', $this->input->post('berth_no'));
			if (is_null($berth_no))
				$berth_no = '';

			$new_sr_nos = $this->input->post('new_sr_no');
			if ($new_sr_nos != null) {
				$wef_dates     = $this->input->post('new_wef_date');
				$bill_item_ids = $this->input->post('new_bill_item_id');
				$particulars   = $this->input->post('new_particulars');
				$calc_types    = $this->input->post('new_calc_type');
				$unit_types    = $this->input->post('new_unit_type');
				$rates         = $this->input->post('new_rate');
				foreach ($new_sr_nos as $index => $sr_no) {
					if ($bill_item_ids[$index] > 0) {
						$row = array(
							'party_id'       => $party_id,
							'company_id'     => $this->office->getCompanyID(),
							'wef_date'       => $wef_dates[$index],
							'type'           => $this->input->post('type'),
							'cargo_type'     => $this->input->post('cargo_type'),
							'product_id'     => $this->input->post('product_id'),
							'indian_port_id' => $this->input->post('indian_port_id'),
							'berth_no'       => $berth_no,
							'sr_no'          => $sr_no,
							'bill_item_id'   => $bill_item_ids[$index],
							'particulars'    => $particulars[$index],
							'calc_type'      => $calc_types[$index],
							'unit_type'      => $unit_types[$index],
							'rate'           => $rates[$index],
						);
						$this->kaabar->save($this->_table, $row);
					}
				}
			}
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class.'/edit/'.$party_id.'/'.$bill_template_id);
		}
	}

	function ajaxPartyRates($bill_template_id) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$row = $this->kaabar->getRow('bill_templates', $bill_template_id);
			
			$sql = "SELECT PR.id, L.id AS party_id, L.name, DATE_FORMAT(PR.wef_date, '%d-%m-%Y') AS wef_date, 
				CONCAT(PR.type, ' - ', PR.cargo_type, ' - ', P.name, ' - ', IP.name) AS type
			FROM ((party_rates PR INNER JOIN parties L ON PR.party_id = L.id)
				INNER JOIN indian_ports IP ON PR.indian_port_id = IP.id)
				INNER JOIN products P ON PR.product_id = P.id
			WHERE L.name LIKE '%$search%' AND 
				  PR.indian_port_id = " . $row['indian_port_id'] . " AND
				  PR.type = '" . $row['type'] . "' AND
				  PR.cargo_type = '" . $row['cargo_type'] . "' AND
				  PR.product_id = " . $row['product_id'] . "
			GROUP BY PR.party_id, PR.wef_date, PR.indian_port_id, PR.type, PR.cargo_type, PR.product_id";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}
}
