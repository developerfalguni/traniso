<?php

class Stax_category extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_table2 = 'stax_rates';
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'applicable_date', 'name', 'tax_code', 'other_code');
		$this->_search = array('applicable_date', 'name', 'tax_code', 'other_code');
		
		$this->_data['list'] = array(
				'heading' 	=> array('ID', 'Applicable Date', 'Name', 'Tax Code', 'Other Code'),
				'class' 	=> array(
					'id'              => 'ID',
					'applicable_date' => 'DateTime',
					'name'            => 'Text',
					'tax_code'        => 'Code',
					'other_code'      => 'Code'
					),
				'link_col'  => 'id',
				'link_url'  => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}

	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'              => 0,
				'applicable_date' => date('d-m-Y'),
				'name'            => '',
				'tax_code'        => '',
				'other_code'      => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
				
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['stax_rates'] = $this->accounting->getStaxRates($id);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			$data = array(
				'applicable_date' => $this->input->post('applicable_date'),
				'name'            => $this->input->post('name'),
				'tax_code'        => $this->input->post('tax_code'),
				'other_code'      => $this->input->post('other_code')
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_updateRates($id);
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _updateRates($id) {
		//$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
		$wef_dates = $this->input->post('wef_date');
		$new_wef_dates = $this->input->post('new_wef_date');

		if ($wef_dates != null) {
			$staxs        = $this->input->post('stax');
			$edu_cesss    = $this->input->post('edu_cess');
			$hedu_cesss   = $this->input->post('hedu_cess');
			$swachh_cesss = $this->input->post('swachh_cess');
			$krishi_cesss = $this->input->post('krishi_cess');
			foreach ($wef_dates as $index => $wef_date) {
				//if (! in_array("$index", $delete_ids) && $staxs[$index] > 0) {
				if ($staxs[$index] > 0) {
					$data = array(
						'wef_date'    => $wef_date,
						'stax'        => $staxs[$index],
						'edu_cess'    => $edu_cesss[$index],
						'hedu_cess'   => $hedu_cesss[$index],
						'swachh_cess' => $swachh_cesss[$index],
						'krishi_cess' => $krishi_cesss[$index],
					);
					$this->kaabar->save($this->_table2, $data, ['id' => $index]);
				}
			}
		}
		
		/*if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				if ($index > 0) {
					$this->kaabar->delete($this->_table2, $index);
				}
			}
		}*/

		if ($new_wef_dates != null) {
			$staxs        = $this->input->post('new_stax');
			$edu_cesss    = $this->input->post('new_edu_cess');
			$hedu_cesss   = $this->input->post('new_hedu_cess');
			$swachh_cesss = $this->input->post('new_swachh_cess');
			$krishi_cesss = $this->input->post('new_krishi_cess');
			foreach ($new_wef_dates as $index => $wef_date) {
				if ($staxs[$index] > 0) {
					$data = array(
						'stax_category_id' => $id,
						'wef_date'         => $wef_date,
						'stax'             => $staxs[$index],
						'edu_cess'         => $edu_cesss[$index],
						'hedu_cess'        => $hedu_cesss[$index],
						'swachh_cess'      => $swachh_cesss[$index],
						'krishi_cess'      => $krishi_cesss[$index],
					);
					$vdid = $this->kaabar->save($this->_table2, $data, array('id' => 0));
				}
			}
		}
	}

	function delete($id = 0, $field = 'id') {
		$sql = "SELECT stax_category_id FROM ledgers WHERE stax_category_id = $id";
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) {
			$this->kaabar->delete($this->_table, $id);
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Deleted Successfully.', 'success');
			redirect($this->_clspath.$this->_class.'/index');
		}
		else {
			setSessionError('STAX Category currently in Use. Cannot Delete.');
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
}
