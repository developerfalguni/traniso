<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Bill extends MY_Controller {
	var $_table2 = '';
	var $_table3 = '';

	function __construct() {
		parent::__construct();
	
		$this->_table2 = 'bill_details';
		$this->_table3 = 'bill_detail_transportations';
		$this->load->model('export');
	}
	
	function index($job_id = 0) {
		$job_id = intval($job_id);
		if ($job_id <= 0 OR $this->export->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}
				
		$data['list'] = array(
			'heading' => array('Type', 'Bill No', 'Date', 'Party Name', 'Amount', 'Audited'),
			'class' => array(
				//'id'       => 'ID',
				'type'       => 'Code nowrap',
				'id2_format' => array('class' => 'Code nowrap', 'link' => 'id'),
				'date'       => 'Date',
				'party_name' => 'Text',
				'amount'     => 'Numeric alignright big',
				'audited'    => 'Label'),
			'link_col' => "id2_format",
			'link_url' => $this->_clspath.$this->_class."/edit/$job_id/");
		$data['list']['show_total'] = array('amount' => 0);
		$data['label_class'] = $this->export->getLabelClass();

		$data['list']['data'] = $this->_getBills($job_id);

		$data['job_id']       = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		$data['jobs']         = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$job_id/0", '<i class="fa fa-plus"></i> Add New', 'class="btn btn-success"'));
		
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
		$this->form_validation->set_rules('party_id', 'Party Name', 'trim|required');
		// $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim');
		
		$row = $this->kaabar->getRow($this->_table, $id); 
		if($row == false) {
			$job = $this->kaabar->getRow('jobs', $job_id);
			$party_id = $job['party_id'];
			$product_name = $this->kaabar->getField('products', $job['product_id']);
			$row = array(
				'id'            => 0,
				'company_id'    => $this->export->getCompanyID(),
				'type'          => 'Invoice',
				'id2'           => '',
				'id2_format'    => '',
				'date'          => date('d-m-Y'),
				'job_id'        => $job_id,
				'party_id'      => $party_id,
				'party_site_id' => 0,
				'product_name'  => $product_name,
				'exchange_rate' => 1,
				'audited'       => 'No',
				'service_tax'   => Settings::get('service_tax'),
				'edu_cess'      => Settings::get('edu_cess'),
				'hedu_cess'     => Settings::get('hedu_cess'),
				'amount'        => 0,
				'remarks'       => '',
			);
		}
		
		$data['job_id'] = array('job_id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		$data['id']     = array('id' => $id);
		$data['row']    = $row;
		$data['party_name'] = $this->kaabar->getField('parties', $row['party_id']);
		$data['party_site_name'] = $this->kaabar->getField('party_sites', $row['party_site_id']);
		
		$status = $this->kaabar->getField('jobs', $job_id, 'id', 'status');
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']    = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['details'] = $this->_getBillDetails($id);
			// $data['bill_detail_transportation'] = $this->_get_bill_detail_transportation($id);
			$data['bill_templates'] = $this->kaabar->getRows('bill_templates');
			
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

			checkDuplicateFormSubmit($this->_clspath.$this->_class."/index/$job_id/$id");
			
			$data = array(
				'company_id'    => $this->export->getCompanyID(),
				'job_id'        => $job_id,
				'type'          => $id > 0 ? $row['type'] : $this->input->post('type'),
				'id2'           => $row['id2'],
				'id2_format'    => $this->input->post('id2_format'),
				'date'          => $this->input->post('date'),
				'party_id'      => $this->input->post('party_id'),
				'party_site_id' => $this->input->post('party_site_id'),
				'product_name'  => $this->input->post('product_name'),
				'exchange_rate' => $this->input->post('exchange_rate'),
				'service_tax'   => $this->input->post('service_tax'),
				'edu_cess'      => $this->input->post('edu_cess'),
				'hedu_cess'     => $this->input->post('hedu_cess'),
				'amount'        => $row['type'] == 'Transportation' ? 0 : $this->input->post('amount'),
				'remarks'       => $this->input->post('remarks'),
				'audited'       => $this->input->post('audited'),
			);

			if ($row['id2'] == 0) {
				$d = array_merge($data, $this->_getNextInvoiceNo($data));
				$data = $d;
			}
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			if ($row['type'] == 'Transportation') {
				$this->_bill_detail_transportation($id);
			}
			else {
				$this->_update_detail($id);
			}
			// $expense_amount = $this->export->updateBillStatus($job_id);
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function _getNextInvoiceNo($data) {
		$years      = explode('_', $this->kaabar->getFinancialYear($data['date']));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';
		$year       = substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2);
		
		$party_id   = $this->kaabar->getField('jobs', $data['job_id'], 'id', 'party_id');
		$party_code = $this->kaabar->getField('parties', $party_id, 'id', 'code');

		$code       = '';
		switch($data['type']) {
			case 'Invoice': $code = ''; break;
			case 'Debit Note': $code = 'DN/'; break;
		}
		
		$this->db->query('LOCK TABLES ci_sessions WRITE, bills AS I READ');
		$sql = "SELECT MAX(I.id2) AS id2
		FROM bills I 
		WHERE I.type = ? AND I.date >= ? AND I.date <= ?";
		$query = $this->db->query($sql, array($type, $start_date, $end_date));
		$id_row = $query->row_array();
		if (! $id_row) {
			$id_row['id2'] = 0;
			$id_row['id2_format'] = '';
		}
		$this->db->query('UNLOCK TABLES');

		$id_row['id2']++;
		$id_row['id2_format'] = $year . "/$party_code/$code" . str_pad($id_row['id2'], 2, '0', STR_PAD_LEFT);

		return $id_row;
	}

	function createPartywiseJobNo($id, $date, $party_id) {
		$years      = explode('_', $this->kaabar->getFinancialYear($date));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';

		$party_code = $this->kaabar->getField('parties', $party_id, 'id', 'code');

		$this->db->query("LOCK TABLES jobs WRITE");
		$query = $this->db->query("SELECT MAX(id2) AS id2 FROM jobs WHERE company_id = ? AND date >= ? AND date <= ? AND party_id = ?", 
			array($this->_company_id, $start_date, $end_date, $party_id));
		$id_row = $query->row_array();
		$id_row['id2']++;
		$id_row['id2_format'] = ($party_code . "/" . str_pad($id_row['id2'], 2, '0', STR_PAD_LEFT) . '/' . substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2));
		$this->db->update('jobs', array('id2' => $id_row['id2'], 'id2_format' => $id_row['id2_format']), "id = $id");
		$this->db->query("UNLOCK TABLES");
	}

	function _update_detail($id) {
		$data          = array();
		$delete_ids    = $this->input->post('delete_id') == false ? ['0' => 0] : $this->input->post('delete_id');
		$bill_item_ids = $this->input->post('bill_item_id');
		$total         = 0;
		if ($bill_item_ids != null) {
			$sr_nos       = $this->input->post('sr_no');
			$particulars  = $this->input->post('particulars');
			$currency_ids = $this->input->post('currency_id');
			$taxables     = $this->input->post('taxable');
			$units        = $this->input->post('units');
			$prices       = $this->input->post('price');
			$amounts      = $this->input->post('bd_amount');
			foreach ($bill_item_ids as $index => $bill_item_id) {
				if (! in_array($index, $delete_ids) && intval($bill_item_id) > 0 && $amounts[$index] > 0) {
					$row = array(
						'sr_no'        => $sr_nos[$index],
						'bill_item_id' => $bill_item_id,
						'particulars'  => $particulars[$index],
						'currency_id'  => $currency_ids[$index],
						'taxable'      => $taxables[$index],
						'units'        => $units[$index],
						'price'        => $prices[$index],
						'amount'       => $amounts[$index],
					);
					$this->kaabar->save($this->_table2, $row, ['id' => $index]);
					$total = $amounts[$index];
					if ($row['taxable'] == 'Yes') {
						$service_tax   = round($total * $this->input->post('service_tax') / 100, 0);
						$edu_cess      = round($service_tax * $this->input->post('edu_cess') / 100);
						$hedu_cess     = round($service_tax * $this->input->post('hedu_cess') / 100);
						$data['amount'] += round(($total + $service_tax + $edu_cess + $hedu_cess), 0);
					}
					else {
						$data['amount'] = bcadd($data['amount'], $amounts[$index], 0);
					}
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				$this->db->delete($this->_table2, ['id' => $index]);
			}
		}

		$bill_item_ids = $this->input->post('new_bill_item_id');
		if ($bill_item_ids != null) {
			$sr_nos       = $this->input->post('new_sr_no');
			$particulars  = $this->input->post('new_particulars');
			$currency_ids = $this->input->post('new_currency_id');
			$taxables     = $this->input->post('new_taxable');
			$units        = $this->input->post('new_units');
			$prices       = $this->input->post('new_price');
			$amounts      = $this->input->post('new_amount');
			foreach ($bill_item_ids as $index => $bill_item_id) {
				if (intval($bill_item_id) > 0 && $amounts[$index] > 0) {
					$row = array(
						'bill_id'      => $id,
						'sr_no'        => $sr_nos[$index],
						'bill_item_id' => $bill_item_id,
						'particulars'  => $particulars[$index],
						'currency_id'  => $currency_ids[$index],
						'taxable'      => $taxables[$index],
						'units'        => $units[$index],
						'price'        => $prices[$index],
						'amount'       => $amounts[$index],
					);
					$this->kaabar->save($this->_table2, $row);
					$total = $amounts[$index];
					if ($row['taxable'] == 'Yes') {
						$service_tax   = round($total * $this->input->post('service_tax') / 100, 0);
						$edu_cess      = round($service_tax * $this->input->post('edu_cess') / 100);
						$hedu_cess     = round($service_tax * $this->input->post('hedu_cess') / 100);
						$data['amount'] += round(($total + $service_tax + $edu_cess + $hedu_cess), 0);
					}
					else {
						$data['amount'] = bcadd($data['amount'], $amounts[$index], 0);
					}
				}
			}
		}
		if (count($data) > 0) {
			$this->kaabar->save($this->_table, $data, array('id' => $id));
		}
	}

	function _bill_detail_transportation($id) {
		$delete_ids    = $this->input->post('delete_id') == false ? ['0' => 0] : $this->input->post('delete_id');
		$rates = $this->input->post('rate');
		$total         = 0;
		if ($rates != null) {
			$sr_nos  = $this->input->post('sr_no');
			$amounts = $this->input->post('amount');
			
			foreach ($rates as $index => $rate) {
				if (! in_array($index, $delete_ids) && intval($rate) > 0) {
					$row = array(
						'sr_no' => $sr_nos[$index],
						'rate'  => $rate						
					);
					$this->kaabar->save($this->_table3, $row, ['id' => $index]);
					$total = bcadd($total, $amounts[$index], 2);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index) {
				$this->db->delete($this->_table3, ['id' => $index]);
			}
		}

		$from_location_ids = $this->input->post('new_from_location_id');
		if ($from_location_ids != null) {
			$sr_nos          = $this->input->post('new_sr_no');
			$dates           = $this->input->post('new_date');
			$vehicle_nos     = $this->input->post('new_vehicle_no');
			$lr_nos          = $this->input->post('new_lr_no');
			$container_nos   = $this->input->post('new_container_no');
			$to_location_ids = $this->input->post('new_to_location_id');
			$cargos          = $this->input->post('new_cargo');
			$quantitys       = $this->input->post('new_quantity');
			$quantity_units  = $this->input->post('new_quantity_unit');
			$rates           = $this->input->post('new_rate');
			$advances        = $this->input->post('new_advance');
			$amounts         = $this->input->post('new_amount');
			
			foreach ($from_location_ids as $index => $from_location_id) {
				if (intval($from_location_id) > 0 && $amounts[$index] > 0) {
					$row = array(
						'bill_id'          => $id,
						'sr_no'            => $sr_nos[$index],
						'date'             => $dates[$index],
						'vehicle_no'       => $vehicle_nos[$index],
						'lr_no'            => $lr_nos[$index],
						'container_no'     => $container_nos[$index],
						'from_location_id' => $from_location_id,
						'to_location_id'   => $to_location_ids[$index],
						'cargo'            => $cargos[$index],
						'quantity'         => $quantitys[$index],
						'quantity_unit'    => $quantity_units[$index],
						'rate'             => $rates[$index],
						'advance'          => $advances[$index],
						'amount'           => $amounts[$index],
					);
					$this->kaabar->save($this->_table3, $row);					
				}
			}
		}		
	}

	function _get_bill_detail_transportation($id) {
		$sql = "SELECT BDT.*, DATE_FORMAT(BDT.date, '%d-%m-%Y') AS date, FL.name AS from_location, TL.name AS to_location
		FROM bill_detail_transportations BDT
		INNER JOIN cities FL ON BDT.from_location_id = FL.id
		INNER JOIN cities TL ON BDT.to_location_id = TL.id
		WHERE BDT.bill_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function deleteJob($job_id, $id) {
		$row = $this->kaabar->getRow('payment_expense', $id, 'expense_id');
		if (! $row) {
			$this->db->query("DELETE FROM $this->_table2 WHERE bill_id = ?", array($id));
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Deleted Successfully', 'success');
			redirect($this->_clspath.$this->_class."/index/$job_id");
		}
		else 
			setSessionError('Cannot Delete Bill, Receipt is entered.');
		
		redirect($this->agent->referrer());
	}

	function _getBills($job_id) {
		$sql = "SELECT B.id, B.type, B.id2_format, DATE_FORMAT(B.date, '%d-%m-%Y') AS date, P.name AS party_name,
			B.amount, B.audited, B.remarks
		FROM bills B LEFT OUTER JOIN parties P ON B.party_id = P.id
		WHERE B.company_id = ? AND B.job_id = ?";
		$query = $this->db->query($sql, array($this->export->getCompanyID(), $job_id));
		return $query->result_array();
	}

	function _getBillDetails($id) {
		$sql = "SELECT BD.id, BD.sr_no, BD.bill_item_id, BI.name AS bill_item, BD.particulars, BD.calc_type, 
			CONCAT(CT.size, ' ', CT.name) AS container_type, BD.currency_id, BD.units, BD.price, BI.taxable, BD.amount
			FROM $this->_table2 BD INNER JOIN bill_items BI ON BD.bill_item_id = BI.id
				LEFT OUTER JOIN container_types CT ON BD.container_type_id = CT.id
			WHERE BD.bill_id = ?
			ORDER BY BD.id";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	function _getPendingExpenses($job_id, $type = 'Invoice') {
		$sql = "SELECT ED.bill_item_id, BI.name AS bill_item, ED.particulars, BI.calc_type, BI.container_type_id, 
			BI.currency_id, ED.quantity, ED.rate, BI.taxable, ED.amount
		FROM expense_details ED INNER JOIN expenses E ON ED.expense_id = E.id
			INNER JOIN bill_items BI ON ED.bill_item_id = BI.id
		WHERE E.job_id = ? AND BI.taxable = ?";
		$query = $this->db->query($sql, array($job_id, ($type == 'Invoice' ? 'Yes' : 'No')));
		return $query->result_array();
	}

	function loadPendingExpense($job_id, $type) {
		$rows = $this->_getPendingExpenses($job_id, $type);
		if ($rows) {
			header('Content-type: text/xml');
			echo '<taconite>
	<eval><![CDATA[';
			foreach ($rows as $r) {
				echo '
				$("tr.TemplateRow input:eq(1)").val("' . $r['bill_item_id'] . '");
				$("tr.TemplateRow .BillItem").val("' . $r['bill_item'] . '");
				$("tr.TemplateRow .Particulars").val("' . $r['particulars'] . '");
				$("tr.TemplateRow .Currency").val("' . $r['currency_id'] . '");
				$("tr.TemplateRow .Units").val("' . $r['quantity'] . '");
				$("tr.TemplateRow .Price").val("' . $r['rate'] . '");
				$("tr.TemplateRow .Amount").val("' . $r['amount'] . '");
				$("tr.TemplateRow .Taxable").val("' . $r['taxable'] . '");
				$("tr.TemplateRow .AddButton").click();
				';
			}
        echo 'reCalculate();
        ]]>
	</eval>
</taconite>';
		}
		else {
			echo "Nothing Found";
		}
	}

	function loadTemplate($job_id, $template_id) {
		$containers = array();
		$sql = "SELECT PC.container_type_id, SUM(PC.containers) AS containers
		FROM job_containers PC
		WHERE PC.job_id = ?";
		$query = $this->db->query($sql, array($job_id));
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$containers[$r['container_type_id']] = $r['containers'];
		}

		$sql = "SELECT BI.*, BTI.price
		FROM bill_items BI INNER JOIN bill_template_items BTI ON BI.id = BTI.bill_item_id
		WHERE BTI.bill_template_id = ?";
		$query = $this->db->query($sql, array($template_id));
		$rows = $query->result_array();
		if ($rows) {
			header('Content-type: text/xml');
			echo '<taconite>
	<eval><![CDATA[';
			foreach ($rows as $r) {
				echo '
				$("tr.TemplateRow input:eq(1)").val("' . $r['id'] . '");
				$("tr.TemplateRow .BillItem").val("' . $r['name'] . '");
				$("tr.TemplateRow .Particulars").val("' . $r['name'] . '");
				$("tr.TemplateRow .Currency").val("' . $r['currency_id'] . '");
				$("tr.TemplateRow .Units").val("1");
				$("tr.TemplateRow .Price").val("' . $r['price'] . '");
				$("tr.TemplateRow .Amount").val("' . (isset($containers[$r['container_type_id']]) ? $containers[$r['container_type_id']] : 0) . '");
				$("tr.TemplateRow .Taxable").val("' . $r['taxable'] . '");
				$("tr.TemplateRow .AddButton").click();
				';
			}
        echo 'reCalculate();
        ]]>
	</eval>
</taconite>';
		}
		else {
			echo "Nothing Found";
		}
	}

	function preview($id, $pdf = 0, $letterhead = 0) {
		$id = intval($id);

		$this->load->helper('numwords');
		$this->load->model('export');

		$default_company         = $this->session->userdata('default_company');
		$data['company']         = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']            = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']           = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['invoice']         = $this->kaabar->getRow($this->_table, $id);
		$data['invoice_details'] = $this->kaabar->getRows($this->_table2, $id, 'bill_id');
		// $data['bill_detail_transportation'] = $this->_get_bill_detail_transportation($id);
		$data['currency']        = $this->kaabar->getNameValuePair('currencies', NULL, 'id', 'id', 'code');
		$data['job']             = $this->kaabar->getRow('jobs', $data['invoice']['job_id']);
		$data['product_name']    = $this->kaabar->getField('products', $data['job']['product_id']);
		$query = $this->db->query("SELECT GROUP_CONCAT(EI.invoice_no_date SEPARATOR ', ') invoice_no_date,
				GROUP_CONCAT(DISTINCT CONCAT(CJ.sb_no, IF(CJ.sb_date = '0000-00-00', '', CONCAT(' / ', DATE_FORMAT(CJ.sb_date, '%d-%m-%Y')))) SEPARATOR ', ') AS sb_no_date,
				GROUP_CONCAT(DISTINCT CONCAT(CJ.bl_no, IF(CJ.bl_date = '0000-00-00', '', CONCAT(' / ', DATE_FORMAT(CJ.bl_date, '%d-%m-%Y')))) SEPARATOR ', ') AS bl_no_date , CJ.bl_date, CJ.bl_no,
				U.code AS package_type, SUM(CJ.packages) AS packages, ROUND(SUM(CJ.net_weight), 3) AS net_weight, CJ.net_weight_unit
				FROM child_jobs CJ LEFT OUTER JOIN (
						SELECT EI.child_job_id, GROUP_CONCAT(DISTINCT CONCAT(EI.invoice_no, IF(EI.invoice_date = '0000-00-00', '', CONCAT(' / ', DATE_FORMAT(EI.invoice_date, '%d-%m-%Y')))) SEPARATOR ', ') AS invoice_no_date
						FROM job_invoices EI INNER JOIN child_jobs CJ ON EI.child_job_id = CJ.id
						WHERE CJ.job_id = ?
						GROUP BY EI.child_job_id
					) EI ON CJ.id = EI.child_job_id
					LEFT OUTER JOIN units U ON CJ.package_type_id = U.id
				WHERE CJ.job_id = ? 
				GROUP BY CJ.job_id", array($data['job']['id'], $data['job']['id']));
		$data['child_job']       = $query->row_array();
		$data['containers']      = $this->kaabar->getRows('deliveries_stuffings', $data['job']['id'], 'job_id');
		$data['container_types'] = $this->kaabar->getNameValuePair('container_types', NULL, 'id', 'id', 'CONCAT(size, code)');
		$data['shipper']         = $this->kaabar->getRow('parties', $data['job']['party_id']);
		$data['party']           = $this->kaabar->getRow('parties', $data['invoice']['party_id']);
		$data['party_city']      = $this->kaabar->getCity($data['party']['city_id']);
		$data['party_site']      = $this->kaabar->getRow('party_sites', $data['invoice']['party_site_id']);
		if (isset($data['party_site']['city_id']))
			$data['party_site_city'] = $this->kaabar->getCity($data['party_site']['city_id']);
		$data['vessel']          = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);
		$data['shipment_port']   = $this->kaabar->getField('indian_ports', $data['job']['custom_port_id']) . ' - INDIA';
		$data['discharge_port']  = $this->kaabar->getField('ports', $data['job']['fpod']);

		$data['page_title'] = humanize($data['invoice']['type']);
		$data['letterhead'] = $letterhead;
		$data['max_items']  = 10;

		if ($pdf) {
			$filename = str_replace('/', '_', $data['invoice']['id2_format']);
			$html = $this->load->view("reports/".underscore($data['invoice']['type']), $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'        => FCPATH.'wkhtmltopdf',

			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view("reports/".underscore($data['invoice']['type']), $data);
		}
	}

	function ajaxLocations() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT id, name
			FROM cities
			WHERE name LIKE '%$search%' 
			ORDER BY name LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}
}
