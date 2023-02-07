<?php

class Issue_do extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('import');
	}
	
	function index($job_id = 0) {
		$job_id = intval($job_id);
		if ($job_id <= 0 OR $this->import->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}
		
		$data['list'] = [
			'heading' => ['ID', 'DO No' , 'Date', 'Qty Delivered'],
			'class' => [
				'id'            => 'ID',
				'id2_format'    => 'Text',
				'date'          => 'Date',
				'qty_delivered' => 'Code',
			],
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/$job_id/"];
		
		$data['list']['data'] = $this->import->getDos($job_id);
		
		$data['job_id'] = ['job_id' => $job_id];
		$data['jobs'] = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		$data['buttons'] = [anchor($this->_clspath.$this->_class."/edit/$job_id/0", '<i class="fa fa-plus"></i> Add New', 'class="btn btn-success"')];
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($job_id, $id = 0) {
		if ($job_id <= 0 OR $this->import->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
		
		$default_company = $this->session->userdata('default_company');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = [
				'id'            => 0,
				'id2'           => 0,
				'id2_format'    => '',
				'job_id'        => $job_id,
				'date'          => date('d-m-Y'),
				'qty_delivered' => 0,
				'remarks'       => '',
			];
		}
		$data['id']     = ['id' => $id];
		$data['job_id'] = ['job_id' => $job_id];
		$data['row']    = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs'] = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
			
			$data['page_title'] = humanize($this->_class);
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id/$id");
			
			$job_net_weight = $this->kaabar->getField('jobs', $job_id, 'id', 'net_weight');
			$total_delivered_qty = $this->db->query("SELECT SUM(qty_delivered) AS qty FROM issue_dos WHERE job_id = ? AND id != ? GROUP BY job_id", [$job_id, $id])->row_array();
			if ((! $total_delivered_qty['qty']) && ($this->input->post('qty_delivered') > $job_net_weight)) {
				setSessionAlert('Qty can not be exceed than Job qty.', 'error');
				redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
			}
			elseif ($id == 0 && ($total_delivered_qty['qty'] >= $job_net_weight)) {
				setSessionAlert('Qty can not be exceed than Job qty.', 'error');
				redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
			}
			elseif ($this->input->post('qty_delivered') < $job_net_weight && (($total_delivered_qty['qty'] + $this->input->post('qty_delivered')) <= $job_net_weight)) {
				$qty_delivered = $this->input->post('qty_delivered');
			}
			else {
				setSessionAlert('Qty can not be exceed than Job qty.', 'error');
				redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
			}

			$data = [
				'job_id'        => $job_id,
				'date'          => $this->input->post('date'),
				'qty_delivered' => $qty_delivered,
				'remarks'       => $this->input->post('remarks'),
			];
		
			if ($row['id2'] == 0) {
				$d = array_merge($data, $this->_getNextNo($data['date']));
				$data = $d;
			}
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function _getNextNo($date) {
		$company    = $this->session->userdata('default_company');
		$years      = explode('_', $this->kaabar->getFinancialYear($date));
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';
		$year       = substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2);
		
		$code = $company['code'].'/BLK/DO/'.$year.'/';
		
		$this->db->query('LOCK TABLES ci_sessions WRITE, issue_dos READ');
		$query = $this->db->query("SELECT MAX(id2) AS id2 FROM issue_dos WHERE date >= ? AND date <= ?", [$start_date, $end_date]);
		$id_row = $query->row_array();
		$this->db->query('UNLOCK TABLES');

		$id_row['id2']++;
		$id_row['id2_format'] = $code . $id_row['id2'];
		return $id_row;
	}

	function pdf($job_id, $id) {
		$row     = $this->kaabar->getRow($this->_table, ['id' => $id]);
		$company = $this->kaabar->getRow('companies', $this->import->getCompanyID());
		$city    = $this->kaabar->getRow('cities', $company['city_id']);
		$state   = $this->kaabar->getRow('states', $city['state_id']);

		$job    = $this->kaabar->getRow('jobs', $job_id);
		$party  = $this->kaabar->getRow('parties', $job['party_id'], 'id');
		$vessel = $this->kaabar->getField('vessels', $job['vessel_id'], 'id', "CONCAT(prefix, ' ', name, ' ', voyage_no)");
		$prev   = $this->db->query("SELECT SUM(qty_delivered) AS prev_delivered FROM issue_dos WHERE job_id = ? AND id != ?", [$job_id, $id])->row_array();
		if (! $prev)
			$prev['prev_delivered'] = 0;
		
		$data['page_title'] = humanize('Delivery Order');
		
		$filename = 'Delivery Order';

		$border = 0; //'LTRB';
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A5', true, 'UTF-8', false);
		$pdf->setFontSubsetting(false);
		$pdf->SetFont('times');
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(Auth::get('username'));
		$pdf->SetTitle($filename);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetMargins(10, 10, 10, true);
		$pdf->SetAutoPageBreak(TRUE, 0);

		$barcode_style = array(
			'border'       => false,
			'padding'      => 'auto',
			'hpadding'     => 'auto',
			'vpadding'     => 5,
			'fgcolor'      => [0,0,0],
			'bgcolor'      => 0,
			'text'         => true,
			'label'        => '',
			'font'         => 'times',
			'fontsize'     => 16,
			'stretchtext'  => 4,
			'position'     => '',
			'align'        => 'C',
			'stretch'      => false,
			'fitwidth'     => false,
			'cellfitalign' => 'C',
		);

		$pdf->AddPage();

		$pdf->Image(FCPATH.'php_uploads/'.$company['logo'], 0, 5, 50, 22, 'png', '', 'M', true, 300, 'L', false, false, $border, true, false, false, false);

		$pdf->SetFontSize(16, true);
		$pdf->MultiCell(130, 5, $company['name'], $border, 'R', false, 1, 10, 8, true, 0, false, false, 0, 'M', false);
		
		$pdf->SetFontSize(8, true);
		$pdf->MultiCell(130, 4, '"RISHI HOUSE" PLOT NO. 113-116, WARD 6, INDUSTRIAL AREA', $border, 'R', false, 1, 10, 15, true, 0, false, true, 4, 'T', true);
		$pdf->MultiCell(130, 4, 'GANDHIDHAM - 370201, KUTCH, GUJARAT', $border, 'R', false, 1, 10, 19, true, 0, false, true, 4, 'T', true);
		$pdf->MultiCell(130, 4, 'TEL. : (02836) 257346, 257347, 257348 FAX : (02836) 257343', $border, 'R', false, 1, 10, 23, true, 0, false, true, 4, 'T', true);

		$pdf->SetFontSize(10, true);
		$pdf->MultiCell(130, 5, 'To,', $border, 'L', false, 1, 10, 35, true, 0, false, false, 0, 'M', false);
		$pdf->MultiCell(130, 5, 'M/s. Rishi Vistara', $border, 'L', false, 1, 10, 39, true, 0, false, false, 0, 'M', false);
		$pdf->MultiCell(130, 5, 'Kandla / Gandhidham', $border, 'L', false, 1, 10, 43, true, 0, false, false, 0, 'M', false);

		$pdf->SetFontSize(12, true);
		$pdf->MultiCell(130, 5, 'DELIVERY ORDER', $border, 'C', false, 1, 10, 50, true, 0, false, false, 0, 'M', false);

		$pdf->SetXY(10, 60);
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFontSize(8, true);
		
		$pdf->Cell(65, 3, 'D.O. No.', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(65, 3, 'Date ', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(65, 5, $row['id2_format'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(65, 5, $row['date'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetFont('', '', 8);
		$pdf->Cell(130, 3, 'Vessel', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(130, 5, $vessel, 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetFont('', '', 8);
		$pdf->Cell(130, 3, 'Job No.', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(130, 5, $job['id2_format'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(130, 3, 'Name of party to whom delivery is to be given', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(130, 5, $party['name'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(130, 3, 'Name of Cargo / Material', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(130, 5, $job['details'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(65, 3, 'B/E No.', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(65, 3, 'B/E Date', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(65, 5, $job['be_no'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(65, 5, $job['be_date'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(65, 3, 'B/L No.', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(65, 3, 'B/L Date', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(65, 5, $job['bl_no'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(65, 5, $job['bl_date'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(40, 3, 'Quantity. to be delivered', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(30, 3, 'BL Quantity', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(30, 3, 'Prev. Delivered', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(30, 3, 'Balance', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 12);
		$pdf->Cell(40, 10, $row['qty_delivered'] . ' ' . $job['net_weight_unit'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(30, 10, $job['net_weight'] . ' ' . $job['net_weight_unit'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(30, 10, $prev['prev_delivered'] . ' ' . $job['net_weight_unit'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(30, 10, round($job['net_weight'] - $prev['prev_delivered'] - $row['qty_delivered']) . ' ' . $job['net_weight_unit'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(130, 3, 'Remarks', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(130, 30, $row['remarks'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'T');

		$pdf->SetFont('', '', 8);
		$pdf->Cell(42, 30, 'Accounts Dept.', 'LTRB', 0, 'C', 0, false, 1, false, 'T', 'B');
		$pdf->Cell(42, 30, 'Audit Dept.', 'LTRB', 0, 'C', 0, false, 1, false, 'T', 'B');
		$pdf->Cell(46, 30, 'For ' . $company['name'], 'LTRB', 1, 'C', 0, false, 1, false, 'T', 'B');

		$pdf->SetXY(3, 165);
		$pdf->StartTransform();
		$pdf->Rotate(90);
		$pdf->write1DBarcode($row['job_id'].'-'.$row['id'], 'C128', -25, 165, 50, 4, 0.2, $barcode_style, 'N');
		
		$pdf->Output("$filename.pdf", 'I');
	}
}
