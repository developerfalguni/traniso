<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Expense extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->_table2 = 'expense_details';
		$this->load->model('export');
	}
	
	function index($job_id, $starting_row = 0) {
		$job_id = intval($job_id);
		if ($job_id <= 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}

		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		
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
				
		$data['list']['heading'] = array('ID', 'Date', 'Bill No', 'Supplier Name', 'Particulars', 'Amount', 'Audited');
		$data['list']['class'] = array(
			'id'            => 'Text',
			'date'          => 'Date',
			'bill_no'       => 'Text',
			'supplier_name' => 'Text',
			'particulars'   => 'Text',
			'amount'        => 'Numeric big alignright',
			'audited'       => 'Label');
		$data['label_class'] = $this->export->getLabelClass();

		$data['list']['link_col'] = 'id';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/$job_id/";
		$data['list']['show_total'] = array('amount' => 0);
		
		// $this->load->library('pagination');
		// $config['base_url']    = site_url($this->_clspath.$this->_class."/index/$job_id");
		// $config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		// $config['total_rows']  = $this->_countExpenses($job_id, $search);
		// $config['per_page']    = Settings::get('rows_per_page');
		// $this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_getExpenses($job_id, $search);
		$data['buttons'] = array(
			anchor($this->_clspath.$this->_class."/edit/$job_id/0", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'),
			anchor("/reports/expense/previewJob/$job_id/0", 'Preview', 'class="btn btn-default Popup"'),
			anchor("/reports/expense/previewJob/$job_id/1", 'PDF', 'class="btn btn-default Popup"')
		);

		$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));

		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);		
	}

	function edit($job_id, $id = 0) {
		$job_id = intval($job_id);
		$id = intval($id);
		if ($job_id <= 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('bill_no', 'Bill No', 'trim|required');
		$this->form_validation->set_rules('supplier_id', 'Supplier Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id); 
		if($row == false) {
			$row = array(
				'id'                 => 0,
				'company_id'         => $this->export->getCompanyID(),
				'job_id'             => $job_id,
				'date'               => date('d-m-Y'),
				'bill_no'            => '',
				'supplier_id'        => 0,
				'exchange_rate'      => 1,
				'audited'            => 'No',
				'service_tax'        => round(Settings::get('service_tax') + (Settings::get('service_tax') * (Settings::get('edu_cess') + Settings::get('hedu_cess')) / 100), 2),
				'service_tax_amount' => 0,
				'tds'                => Settings::get('tds'),
				'tds_amount'         => 0,
				'amount'             => 0,
				'remarks'            => ''
			);
		}
		
		$data['job_id']       = array('job_id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		$data['id']           = array('id' => $id);
		$data['row']          = $row;
		$data['supplier']     = $this->kaabar->getRow('suppliers', $row['supplier_id']);
		if (! $data['supplier']) 
			$data['supplier'] = array(
				'name'   => '',
				'pan_no' => '',
			);
		$status = $this->kaabar->getField('jobs', $job_id, 'id', 'status');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']    = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['details'] = $this->_getExpenseDetails($id);
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			if ($status == 'Completed' && ! (Auth::isAdmin() OR Auth::get('username') == 'auditor')) {
				setSessionError('Job Completed, cannot modify. Contact Admin');
				redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
			}

			// checkDuplicateFormSubmit($this->_clspath.$this->_class."/index/$job_id");
			
			$supplier_id = $this->input->post('supplier_id');
			if (! $supplier_id) {
				$supplier_id = $this->kaabar->save('suppliers', array(
					'name'   => $this->input->post('supplier_name'),
					'pan_no' => $this->input->post('pan_no')
				));
			}

			$data = array(
				'company_id'         => $this->export->getCompanyID(),
				'job_id'             => $job_id,
				'date'               => $this->input->post('date'),
				'bill_no'            => $this->input->post('bill_no'),
				'supplier_id'        => $supplier_id,
				'exchange_rate'      => $this->input->post('exchange_rate'),
				'audited'            => $this->input->post('audited'),
				'service_tax'        => $this->input->post('service_tax'),
				'service_tax_amount' => $this->input->post('service_tax_amount'),
				'tds'                => $this->input->post('tds'),
				'tds_amount'         => $this->input->post('tds_amount'),
				'amount'             => $this->input->post('amount'),
				'remarks'            => $this->input->post('remarks')
			);
			
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_update_detail($id);
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function _update_detail($id) {
		$delete_ids  = $this->input->post('delete_id') == false ? ['0' => 0] : $this->input->post('delete_id');
		$particulars = $this->input->post('particulars');
		$ntax_amount = 0;
		$tax_amount  = 0;
		if ($particulars != null) {
			$bill_item_ids = $this->input->post('bill_item_id');
			$quantitys     = $this->input->post('quantity');
			$rates         = $this->input->post('rate');
			$amounts       = $this->input->post('ib_amount');
			$taxable       = $this->input->post('taxable');
			foreach ($particulars as $index => $particular) {
				if (! in_array($index, $delete_ids) && strlen(trim($particular)) > 0 && $amounts[$index] > 0) {
					$row = array(
						'bill_item_id' => $bill_item_ids[$index],
						'particulars'  => $particular,
						'quantity'     => $quantitys[$index],
						'rate'         => $rates[$index],
						'amount'       => $amounts[$index],
					);
					$this->kaabar->save($this->_table2, $row, ['id' => $index]);

					if ($taxable[$index] == 'Yes')
						$tax_amount = bcadd($tax_amount, $amounts[$index], 2);
					else
						$ntax_amount = bcadd($ntax_amount, $amounts[$index], 2);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				$this->db->delete($this->_table2, ['id' => $index]);
			}
		}

		$new_particulars = $this->input->post('new_particulars');
		if ($new_particulars != null) {
			$bill_item_ids = $this->input->post('new_bill_item_id');
			$quantitys     = $this->input->post('new_quantity');
			$rates         = $this->input->post('new_rate');
			$amounts       = $this->input->post('new_amount');
			$taxable       = $this->input->post('new_taxable');
			foreach ($new_particulars as $index => $particular) {
				if (strlen(trim($particular)) > 0 && $amounts[$index] > 0) {
					$row = array(
						'expense_id'   => $id,
						'bill_item_id' => $bill_item_ids[$index],
						'particulars'  => $particular,
						'quantity'     => $quantitys[$index],
						'rate'         => $rates[$index],
						'amount'       => $amounts[$index],
					);
					$this->kaabar->save($this->_table2, $row);
					if ($taxable[$index] == 'Yes')
						$tax_amount = bcadd($tax_amount, $amounts[$index], 2);
					else
						$ntax_amount = bcadd($ntax_amount, $amounts[$index], 2);
				}
			}
		}
		$row = array();
		$row['service_tax_amount'] = round($tax_amount * $this->input->post('service_tax') / 100, 0);
		$row['tds_amount']         = round(($ntax_amount + $tax_amount) * $this->input->post('tds') / 100, 0);
		$row['amount']             = ($ntax_amount + $tax_amount + $row['service_tax_amount']) - $row['tds_amount'];
		$this->kaabar->save($this->_table, $row, array('id' => $id));
	}

	function deleteJob($job_id, $id) {
		$row = $this->kaabar->getRow('payment_expense', $id, 'expense_id');
		if (! $row) {
			$this->db->query("DELETE FROM $this->_table2 WHERE expense_id = ?", array($id));
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Deleted Successfully', 'success');
			redirect($this->_clspath.$this->_class."/index/$job_id");
		}
		else 
			setSessionError('Cannot Delete Expense, Payment has been made.');
		
		redirect($this->agent->referrer());
	}

	function _countExpenses($job_id, $search) {
		$sql = "SELECT COUNT(T.id) AS numrows
		FROM (
			SELECT E.id
			FROM expenses E INNER JOIN suppliers S ON E.supplier_id = S.id
				LEFT OUTER JOIN expense_details ED ON E.id = ED.expense_id
			WHERE E.job_id = ? AND E.company_id = ? AND (
				DATE_FORMAT(E.date, '%d-%m-%Y') LIKE '%$search%' OR
				E.bill_no LIKE '%$search%' OR
				S.name LIKE '%$search%' OR
				S.pan_no LIKE '%$search%')
			GROUP BY E.id
		) T";
		$query = $this->db->query($sql, array($job_id, $this->export->getCompanyID()));
		$row   = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _getExpenses($job_id, $search = '') {
		$sql = "SELECT E.id, DATE_FORMAT(E.date, '%d-%m-%Y') AS date, E.bill_no, S.name AS supplier_name, 
			GROUP_CONCAT(ED.particulars SEPARATOR ', ') AS particulars, E.amount, E.audited
		FROM expenses E INNER JOIN suppliers S ON E.supplier_id = S.id
			LEFT OUTER JOIN expense_details ED ON E.id = ED.expense_id
		WHERE E.job_id = ? AND E.company_id = ? AND (
			DATE_FORMAT(E.date, '%d-%m-%Y') LIKE '%$search%' OR
			E.bill_no LIKE '%$search%' OR
			S.name LIKE '%$search%' OR
			S.pan_no LIKE '%$search%')
		GROUP BY E.id
		ORDER BY E.date";
		$query = $this->db->query($sql, array($job_id, $this->export->getCompanyID()));
		return $query->result_array();
	}

	function _getExpenseDetails($id) {
		$sql = "SELECT ID.*, BI.name AS bill_item, BI.taxable, C.code AS currency_code
		FROM expense_details ID INNER JOIN bill_items BI ON ID.bill_item_id = BI.id
			INNER JOIN currencies C ON BI.currency_id = C.id
		WHERE expense_id = ?
		ORDER BY ID.id";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function preview($id = 0, $pdf = 0) {
		$id = intval($id);
		
		$this->load->helper('numwords');
		
		$default_company    = $this->session->userdata('default_company');
		$data['company']    = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']       = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']      = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['bills']      = $this->kaabar->getRow($this->_table, $id);
		$data['rows']       = $this->kaabar->getRows('expense_details', $id, 'expense_id');
		$data['page_title'] = humanize($this->_class);

		if ($pdf) {
			$filename = str_replace('/', '-', $data['bills']['bill_no']);
			$html = $this->load->view("reports/expense_preview", $data, true);
			
			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/expense_preview", $data);
		}
	}
}
