<?php

use mikehaertl\wkhtmlto\Pdf;

class Payment extends MY_Controller {
	var $_table2;

	function __construct() {
		parent::__construct();
	
		$this->_table2 = 'payment_expense';
		$this->load->model('export');
	}
	
	function index($job_id, $starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$job_id = intval($job_id);
		if ($job_id <= 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}

		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		
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
				
		$data['list']['heading'] = array('ID', 'Supplier Name', 'Date', 'Cheque No', 'Amount');
		$data['list']['class'] = array(
			'id'            => 'Text',
			'supplier_name' => 'Text',
			'date'          => 'Date',
			'cheque_no'     => 'Text',
			'amount'        => 'Numeric big alignright');
		$data['list']['link_col'] = 'id';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/$job_id/";
		$data['list']['show_total'] = array('amount' => 0);

		// $this->load->library('pagination');
		// $config['base_url']    = site_url($this->_clspath.$this->_class."/index/$job_id");
		// $config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		// $config['total_rows']  = $this->_countPayments($job_id, $search);
		// $config['per_page']    = Settings::get('rows_per_page');
		// $this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_getPayments($job_id, $search);
		$data['buttons'] = array(
			anchor($this->_clspath.$this->_class."/edit/$job_id/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'),
			anchor("/reports/payment/previewJob/$job_id/0", 'Preview', 'class="btn btn-default Popup"'),
			anchor("/reports/payment/previewJob/$job_id/1", 'PDF', 'class="btn btn-default Popup"')
		);

		$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);		
	}

	function edit($job_id, $id = 0) {
		$job_id = intval($job_id);
		$id = intval($id);
		if ($job_id <= 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('supplier_id', 'Supplier Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id); 
		if($row == false) {
			$row = array(
				'id'          => 0,
				'company_id'  => $this->export->getCompanyID(),
				'job_id'      => $job_id,
				'date'        => date('d-m-Y'),
				'supplier_id' => 0,
				'mode'        => '',
				'bank_id'     => 0,
				'cheque_no'   => '',
				'amount'      => 0,
				'remarks'     => ''
			);
		}
		
		$data['job_id']        = array('job_id' => $job_id);
		$data['child_job_id']  = array('id' => 0);
		$data['id']            = array('id' => $id);
		$data['row']           = $row;
		$data['supplier_name'] = $this->kaabar->getField('suppliers', $row['supplier_id']);
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']    = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['details'] = $this->_getExpenses($id);
			$data['pending'] = $this->_getPendingExpenses($row['job_id'], $row['supplier_id']);
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/index/$job_id");
			
			if (Auth::hasAccess(($id > 0 ? Auth::UPDATE : Auth::CREATE))) {
				$data = array(
					'company_id'  => $this->export->getCompanyID(),
					'job_id'      => $job_id,
					'date'        => $this->input->post('date'),
					'supplier_id' => $this->input->post('supplier_id'),
					'mode'        => $this->input->post('mode'),
					'bank_id'     => $this->input->post('bank_id'),
					'cheque_no'   => $this->input->post('cheque_no'),
					'amount'      => $this->input->post('amount'),
					'remarks'     => $this->input->post('remarks')
				);
				
				$id = $this->kaabar->save($this->_table, $data, $row);
				$this->_update_detail($id);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function _update_detail($id) {
		$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
		$paids      = $this->input->post('paid');
		if ($paids != null) {
			foreach ($paids as $index => $paid) {
				if (! in_array($index, $delete_ids)) {
					$row = array(
						'amount' => $paid
					);
					$this->kaabar->save($this->_table2, $row, array('id' => $index));
				}
			}
		}

		if ($delete_ids != null) {
			if (Auth::hasAccess(Auth::DELETE)) {
				foreach ($delete_ids as $index) {
					$this->db->delete($this->_table2, array('id' => $index));
				}
			}
		}

		$paids = $this->input->post('new_paid');
		if ($paids != null) {
			$expense_ids = $this->input->post('new_expense_id');
			foreach ($paids as $index => $paid) {
				if ($expense_ids[$index] > 0) {
					$row = array(
						'payment_id' => $id,
						'expense_id' => $expense_ids[$index],
						'amount'     => $paid,
					);
					$this->kaabar->save($this->_table2, $row);
				}
			}
		}
	}

	function _countPayments($job_id, $search) {
		$sql = "SELECT COUNT(T.id) AS numrows
		FROM (
			SELECT P.id
			FROM $this->_table P INNER JOIN suppliers S ON P.supplier_id = S.id
				LEFT OUTER JOIN $this->_table2 PE ON P.id = PE.payment_id
			WHERE P.job_id = ? AND P.company_id = ? AND (
				DATE_FORMAT(P.date, '%d-%m-%Y') LIKE '%$search%' OR
				P.cheque_no LIKE '%$search%' OR
				S.name LIKE '%$search%' OR
				S.pan_no LIKE '%$search%')
			GROUP BY P.id
		) T";
		$query = $this->db->query($sql, array($job_id, $this->export->getCompanyID()));
		$row   = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _getPayments($job_id, $search = '') {
		$sql = "SELECT P.id, S.name AS supplier_name, DATE_FORMAT(P.date, '%d-%m-%Y') AS date, P.cheque_no, P.amount
		FROM $this->_table P INNER JOIN suppliers S ON P.supplier_id = S.id
			LEFT OUTER JOIN $this->_table2 PE ON P.id = PE.payment_id
		WHERE P.job_id = ? AND P.company_id = ? AND (
			DATE_FORMAT(P.date, '%d-%m-%Y') LIKE '%$search%' OR
			P.cheque_no LIKE '%$search%' OR
			S.name LIKE '%$search%' OR
			S.pan_no LIKE '%$search%')
		GROUP BY P.id
		ORDER BY P.date";
		$query = $this->db->query($sql, array($job_id, $this->export->getCompanyID()));
		return $query->result_array();
	}

	function _getExpenses($id) {
		$sql = "SELECT PE.id, PE.expense_id, DATE_FORMAT(E.date, '%d-%m-%Y') AS date, E.bill_no, E.amount, PE.amount AS paid
		FROM $this->_table2 PE INNER JOIN expenses E ON PE.expense_id = E.id
		WHERE PE.payment_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function _getPendingExpenses($job_id, $supplier_id) {
		$sql = "SELECT E.*, DATE_FORMAT(E.date, '%d-%m-%Y') AS date
		FROM expenses E 
		WHERE E.job_id = ? AND E.supplier_id = ? AND E.id NOT IN (
			SELECT PE.expense_id FROM payment_expense PE INNER JOIN expenses E ON PE.expense_id = E.id
			WHERE E.supplier_id = ?
		)";
		$query = $this->db->query($sql, array($job_id, $supplier_id, $supplier_id));
		return $query->result_array();
	}

	function loadPendingExpense($job_id, $supplier_id) {
		$rows = $this->_getPendingExpenses($job_id, $supplier_id);
		if ($rows) {
			header('Content-type: text/xml');
			echo '<taconite>
	<eval><![CDATA[';
			foreach ($rows as $r) {
				echo '
				$(".DataEntry tbody").append("<tr class=\"New\"> \
				<td class=\"aligncenter\"><input type=\"checkbox\" name=\"new_expense_id[]\" value=\"' . $r['id'] . '\" /></td> \
				<td>' . $r['bill_no'] . '</td> \
				<td class=\"aligncenter\">' . $r['date'] . '</td> \
				<td class=\"alignright\">' . $r['amount'] . '</td> \
				<td><input type=\"text\" class=\"Numeric col-md-12\" name=\"new_paid[]\" value=\"' . $r['amount'] . '\" /></td> \
				<td></td> \
				</tdr>");
				';
			}
        echo ']]>
	</eval>
</taconite>';
		}
		else {
			echo "Nothing Found";
		}
	}

	function preview($id = 0, $pdf = 0) {
		$id = intval($id);
		
		$this->load->helper('numwords');
		
		$default_company    = $this->session->userdata('default_company');
		$data['company']    = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']       = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']      = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['bills']      = $this->kaabar->getRow($this->_table, $id);
		$data['rows']       = $this->kaabar->getRows('payment_expense', $id, 'payment_id');
		$data['page_title'] = humanize($this->_class);

		if ($pdf) {
			$filename = str_replace('/', '-', $data['bills']['cheque_no']);
			$html = $this->load->view("reports/payment_preview", $data, true);
			
			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/payment_preview", $data);
		}
	}
}
