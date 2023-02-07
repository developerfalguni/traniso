<?php

class Bill_template extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
		
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

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
		
		$data['list'] = array(
			'heading' => array('ID', 'Type', 'Cargo', 'Product', 'Port', 'Berth No', 'Bill Items'),
			'class' => array(
				'id'           => 'ID',
				'type'         => 'Code',
				'cargo_type'   => 'Code',
				'product_name' => 'Text',
				'indian_port'  => 'Text',
				'berth_no'     => 'Text',
				'bill_items'   => 'Code'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$data['list']['data'] = $this->office->getBillTemplates($search);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';

		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('type', 'Type', 'trim|required');
		$this->form_validation->set_rules('cargo_type', 'Cargo Type', 'trim|required');
		// $this->form_validation->set_rules('product_id', 'Product', 'trim');

		$row = $this->office->getBillTemplate($id);
		if($row == false) {
			$row = array(
				'company_id'     => 0,
				'type'           => 'Import',
				'cargo_type'     => 'Container',
				'product_id'     => 0,
				'indian_port_id' => 72,
				'berth_no'       => '',
				'remarks'        => '',
				'bill_items'     => array()
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['bill_templates'] = $this->office->getBillTemplates();
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page'] = $this->_clspath.$this->_class.'_edit';
	
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
				$sr_nos     = $this->input->post('sr_no');
				$new_sr_nos = $this->input->post('new_sr_no');
				
				$berth_no = join(',', $this->input->post('berth_no'));
				if (is_null($berth_no))
					$berth_no = '';

				if ($sr_nos != null) {
					$bill_item_ids = $this->input->post('bill_item_id');
					$particulars = $this->input->post('particulars');
					$calc_types = $this->input->post('calc_type');
					$unit_types = $this->input->post('unit_type');
					foreach ($sr_nos as $index => $sr_no) {
						if (! in_array("$index", $delete_ids) && $bill_item_ids[$index] > 0) {
							$row = array(
								'type'           => $this->input->post('type'),
								'cargo_type'     => $this->input->post('cargo_type'),
								'product_id'     => $this->input->post('product_id'),
								'indian_port_id' => $this->input->post('indian_port_id'),
								'berth_no'       => $berth_no,
								'remarks'        => $this->input->post('remarks'),
								'sr_no'          => $sr_no,
								'bill_item_id'   => $bill_item_ids[$index],
								'particulars'    => $particulars[$index],
								'calc_type'      => $calc_types[$index],
								'unit_type'      => $unit_types[$index]
							);
							$id = $this->kaabar->save($this->_table, $row, array('id' => $index));
						}
					}
				}

				if ($delete_ids != null) {
					foreach ($delete_ids as $index) {
						if ($index > 0) {
							$this->db->delete($this->_table, array('id' => $index));
						}
					}
				}

				if ($new_sr_nos != null) {
					$bill_item_ids = $this->input->post('new_bill_item_id');
					$particulars = $this->input->post('new_particulars');
					$calc_types = $this->input->post('new_calc_type');
					$unit_types = $this->input->post('new_unit_type');
					foreach ($new_sr_nos as $index => $sr_no) {
						if ($bill_item_ids[$index] > 0) {
							$row = array(
								'company_id'     => $this->office->getCompanyID(),
								'type'           => $this->input->post('type'),
								'cargo_type'     => $this->input->post('cargo_type'),
								'product_id'     => $this->input->post('product_id'),
								'indian_port_id' => $this->input->post('indian_port_id'),
								'berth_no'       => $berth_no,
								'remarks'        => $this->input->post('remarks'),
								'sr_no'          => $sr_no,
								'bill_item_id'   => $bill_item_ids[$index],
								'particulars'    => $particulars[$index],
								'calc_type'      => $calc_types[$index],
								'unit_type'      => $unit_types[$index]
							);
							$id = $this->kaabar->save($this->_table, $row);

							$sql = "SELECT PR.party_id, MAX(PR.wef_date) AS wef_date
								FROM bill_templates BT INNER JOIN party_rates PR ON (
								PR.indian_port_id = BT.indian_port_id AND 
								PR.berth_no       = BT.berth_no AND 
								PR.type           = BT.type AND 
								PR.cargo_type     = BT.cargo_type AND 
								PR.product_id     = BT.product_id
							)
							WHERE BT.company_id = ? AND
								  BT.indian_port_id = ? AND 
								  BT.type = ? AND
								  BT.cargo_type = ? AND
								  BT.product_id = ? AND 
								  PR.wef_date <= ?
							GROUP BY BT.indian_port_id, BT.type, BT.cargo_type, BT.product_id, PR.party_id";
							$query = $this->db->query($sql, array(
								$this->office->getCompanyID(), $this->input->post('indian_port_id'), $this->input->post('type'), $this->input->post('cargo_type'), $this->input->post('product_id'), date('Y-m-d')
							));
							$rows = $query->result_array();
							$bulk_insert = array();
							foreach ($rows as $r)
								$bulk_insert[] = array(
									'wef_date'       => $r['wef_date'],
									'party_id'       => $r['party_id'],
									'indian_port_id' => $r['indian_port_id'],
									'type'           => $r['type'], 
									'cargo_type'     => $r['cargo_type'], 
									'product_id'     => $r['product_id'],
								);
							if (count($bulk_insert) > 0)
								$this->db->insert_batch('party_rates', $bulk_insert);
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

	function loadTemplates($id) {
		if (intval($id) == 0) return;
		
		$row = $this->office->getBillTemplate($id);

		header('Content-type: text/xml');
		echo '<taconite>
	<eval><![CDATA[ 
		';
        foreach ($row['bill_items'] as $b) {
        	echo '$("tr.TemplateRow input:eq(0)").val(\'' . $b['sr_no'] . '\');
        $("tr.TemplateRow input:eq(1)").val(\'' . $b['bill_item_id'] . '\');
        $("tr.TemplateRow input:eq(2)").val(\'' . $b['code'] . '\');
        $("tr.TemplateRow input:eq(3)").val(\'' . $b['particulars'] . '\');
        $("tr.TemplateRow select:eq(0)").val(\'' . $b['calc_type'] . '\');
        $("tr.TemplateRow select:eq(1)").val(\'' . $b['unit_type'] . '\');
        $("button.AddButton").click();
    	';
        }
    echo ']]>
	</eval>
</taconite>';
	}
}
