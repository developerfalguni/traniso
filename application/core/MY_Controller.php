<?php
use League\Csv\Writer;
use mikehaertl\wkhtmlto\Pdf;


class MY_Controller extends CI_Controller {
	function __construct() {
		parent::__construct();

		$this->output->enable_profiler(PROFILER);
		
		$this->_clspath	= $this->router->fetch_directory();
		$this->_class 	= $this->router->fetch_class();
		$this->_method 	= $this->router->fetch_method();
		$this->_uri 	= $this->uri->segment_array();
		
		$this->load->helper('inflector');
		$this->_table      = plural($this->_class);

		$this->_docs       = DOCS_URL.$this->_clspath.$this->_class;
		$this->_fields     = array('id', 'name');
		$this->_orderby    = 'id';
		$this->_search     = ['name'];
		$this->_javascript = '';
		$this->_data['page_title'] = humanize($this->_class);
		$this->_data['docs_url']   = $this->_docs;
		
		$this->_is_ajax = $this->input->is_ajax_request();

		
		$this->_entries = $this->config->item('ENTRY_FIELD');
		$this->_transfer_dir = $this->config->item('TRANSFER_DIR');
        $this->_upload_dir = $this->config->item('UPLOAD_DIR');
        $this->_variants_dir = $this->config->item('VARIANTS_DIR');
        $this->_metadata_filename = $this->config->item('METADATA_FILENAME');
		
		$this->load->model('kaabar');

		$this->_default_company = $this->session->userdata("default_company");

		if ($this->_default_company['id'] == false) {

			$row = $this->kaabar->getRow('companies', Settings::get('default_company'));
			$default_company = array(
				'id'             => $row['id'], 
				'code'           => $row['code'],
				'name'           => $row['name'],
				'gst_no'         => $row['gst_no'],
				'financial_year' => $this->kaabar->getFinancialYear(date('d-m-Y'))
			);
			$this->session->set_userdata("default_company", $default_company);
			$this->kaabar->setCompany($default_company['id']);
			$this->_default_company = $this->session->userdata("default_company");


		}

		$est = $this->kaabar->getField('companies', Settings::get('default_company'), 'id', 'establishment');
		$finYear = $this->kaabar->getBackFinYear(date('Y-m-d', strtotime($est)));
		$years   = explode('_', $finYear);
		$firstYear = $years[0];

		$currfinYear = $this->kaabar->getFinYear();
		$currfinYears   = explode('_', $currfinYear);

		for($year = $firstYear; $year <= date('Y'); $year++) {
			if($year == $currfinYears[1])
				break;
			$yearsList[] = [$year.'_'.($year+1) => $year.'_'.($year+1)];
		}

		$this->_yearsList = $this->kaabar->custom_filter($yearsList);
		$this->_currfinYear = $currfinYear;

	}

	public static function handle_file_post($files) {
		// This is a very basic implementation of a classic PHP upload function, please properly
	    // validate all submitted files before saving to disk or database, more information here
	    // http://php.net/manual/en/features.file-upload.php
	    foreach($files as $file) {
	    	FilePond\move_file($file, $this->_upload_dir);
	    }
	}

	public static function handle_base64_encoded_file_post($files) {
		foreach ($files as $file) {
			$file = @json_decode($file);
			// Skip files that failed to decode
	        if (!is_object($file)) continue;
	        // write file to disk
	        FilePond\write_file(
	            $this->_upload_dir, 
	            base64_decode($file->data), 
	            FilePond\sanitize_filename($file->name)
	        );
	    }
	}

	public static function handle_transfer_ids_post($ids, $filepath, $index) {
		$CI =& get_instance();

		foreach ($ids as $id) {
	    	$id = preg_replace('/\s+/', '', $id[$index]);
			// create transfer wrapper around upload
	    	$transfer = FilePond\get_transfer($CI->_transfer_dir, $id);
	        // transfer not found
	        if (!$transfer) continue;
			// move files
	        $files = $transfer->getFiles(defined('TRANSFER_PROCESSOR') ? $CI->transfer_processor : null);
	        foreach($files as $key => $file) {
	        	//FilePond\move_file($file, $CI->_upload_dir);
				FilePond\move_file($file, $filepath);
				$data['file'] = $file;
			}
			// remove transfer directory
	        FilePond\remove_transfer_directory($CI->_transfer_dir, $id);
	    }
	    return $data;
	}
	
	function _index($starting_row = 0) {
		
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = '';
			redirect($this->_clspath.$this->_class);
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$this->_data['search'] = $search;
		$this->_data['show_search'] = true;
		
		if (! isset($this->_data['list'])) {
			$this->_data['list'] = array(
				'heading' 	=> array('ID', 'Name'),
				'class' 	=> array(
					'id' 	=> 'ID', 
					'name'  => 'Text'
					),
				'link_col'  => 'id',
				'link_url'  => $this->_clspath.$this->_class."/edit/");
		}
		
		$this->load->library('pagination');
		$config['base_url'] 	= site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment']  = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		if (! isset($this->_data['total_rows']))
			$config['total_rows'] = $this->kaabar->countAll($this->_table, $this->_search, $search);
		else
			$config['total_rows'] = $this->_data['total_rows'];
		$config['per_page'] = Settings::get('rows_per_page');
		$this->pagination->initialize($config);

		$this->_data['list']['data'] = $this->kaabar->getAll($this->_table, $this->_fields, $this->_search, $search, $starting_row, $config['per_page'], $this->_orderby);
		
		if (! isset($this->_data['buttons']))
			$this->_data['buttons'] = array(
				anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"')
			);
		
		if (! isset($this->_data['page']))
			$this->_data['page'] = 'list';
		
		$this->load->view('index', $this->_data);
	}

	function _date_in_financial_year($date, $args) {
		$args_arr = explode(',', $args);
		$years    = explode('_', $args_arr[0]);

		$date       = new \DateTime($date);
		$start_date = new \DateTime($years[0].'-04-01 00:00:00');
		$end_date   = new \DateTime($years[1].'-03-31 23:59:59');
	
		if ($date < $start_date OR $date > $end_date) {
			$CI =& get_instance();
			$CI->form_validation->set_message('_date_in_financial_year', 'The date is outside of Financial Year range ('.$args_arr[0].').');

			return false;
		}

		return true;
	}

	function deletion($id = 0, $field = 'id') {
		$can_delete = TRUE;
		
		// Find all Tables having Columns ending with %$field
		$query = $this->db->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$this->db->database."' AND COLUMN_NAME LIKE '%$field'");
		$tables = $query->result_array();

		// Check if the existing $id is used in any of the tables.
		foreach ($tables as $t) {
			$query = $this->db->query("SELECT id, " . $t['COLUMN_NAME'] . " FROM " . $t['TABLE_NAME'] . " WHERE " . $t['COLUMN_NAME'] . " = $id");
			if ($query->num_rows() > 0) {
				$found = $query->row_array();
				$can_delete = FALSE;
				break;
			}
		}

		// Delete if not used.
		if ($can_delete) {
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert(humanize($this->_table) . ' Deleted Successfully', 'success');
		}
		else
			setSessionError('Cannot Delete. ' . humanize($field) . ' is currently in use in <strong>' . humanize($t['TABLE_NAME']) . '</strong>, ID: <strong>'.$found['id'].'</strong>');

		redirect($this->_clspath.$this->_class.'/index');
	}

	function audit($id) {
		$db    = $this->db->database;
		$table = $this->_table;
		$logs  = (new MongoDB\Client)->$db->$table;
		$data['users'] = $this->kaabar->getNameValuePair('users', null, 'id', 'id', 'username');
		$data['logs']  = $logs->find(['id' => $id], ['sort' => ['audit_updated' => -1]]);

		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'audit';
		$this->load->view('plain', $data);
	}
	
	function ajax() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$args   = $this->uri->segment_array();

			$index = array_search('ajax', $args); 							 $index++;
			$table = (isset($args[$index]) ? $args[$index] : $this->_table); $index++;
			$field = (isset($args[$index]) ? $args[$index] : 'name'); 		 $index++;

			$sql    = "SELECT DISTINCT CONCAT($field) AS ajaxField FROM $table WHERE $field LIKE '%$search%' ORDER BY $field";
			$this->kaabar->getAjax($sql);
		}
		else {
			echo "Access Denied";
		}
	}
	
	function json($table = FALSE, $key = 'id', $value = 'name') {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$args   = $this->uri->segment_array();

			$index    = array_search('json', $args); 							$index++;
			$table    = (isset($args[$index]) ? $args[$index] : $this->_table); $index++;
			$key      = (isset($args[$index]) ? $args[$index] : 'id'); 			$index++;
			$fields[] = (isset($args[$index]) ? $args[$index] : 'name'); 		$index++;

			$sql   = "SELECT $key";
			$where = "WHERE ";
			foreach ($fields as $f) {
				$sql   .= ", $f";
				$where .= "$f LIKE '%$search%' OR ";
			}
			$sql .= " FROM $table " . substr($where, 0, -4) . " ORDER BY " . $fields[0] . " LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();

			foreach ($rows as $k => $v) {

				$v['name'] = $v[$value];
				unset($v[$value]);
				$rows[$k] = $v;
			}
			
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function phalcon_json() {
		if ($this->input->is_ajax_request()) {
			$term = strtolower($this->input->post_get('term'));
			$args   = $this->uri->segment_array();
			$data   = [];
			$index  = array_search('phalcon_json', $args); 					  $index++;
			$table  = (isset($args[$index]) ? $args[$index] : $this->_table); $index++;
			$fields = ($this->input->post_get('fields') ? $this->input->post_get('fields') : ['id', 'name']);
			$search = ($this->input->post_get('search') ? $this->input->post_get('search') : ['name']);
			$order  = ($this->input->post_get('order') ? $this->input->post_get('order') : ['name']);

			$where_sql = [];
			foreach ($search as $f) {
				$where_sql[] = "$f LIKE '%$term%'";
			}
			$sql = "SELECT DISTINCT " . join(', ', $fields) .
				" FROM $table" .
				" WHERE " . join(' OR ', $where_sql) .
				" ORDER BY " . join(', ', $order) .
				" LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxJSON($table = FALSE, $field = 'name') {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$table  = (! $table ? $this->_table : $table);
			$sql    = "SELECT DISTINCT id, $field FROM $table WHERE $field LIKE '%$search%' ORDER BY $field LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo "Access Denied";
		}
	}

	function getJSON($table = FALSE, $field = 'name', $field2 = false) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$table  = (! $table ? $this->_table : $table);
			if ($field2 == false)
				$sql = "SELECT DISTINCT $field FROM $table WHERE $field LIKE '%$search%' ORDER BY $field LIMIT 0, 50";
			else
				$sql = "SELECT DISTINCT $field, $field2 FROM $table WHERE $field LIKE '%$search%' OR $field2 LIKE '%$search%' ORDER BY $field LIMIT 0, 50";
			$query  = $this->db->query($sql);
			$result = $query->result_array();

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo "Access Denied";
		}
	}

	function _preview($data, $pdf = 0) {
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($data['page'], $data, true);

			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view($data['page'], $data);
		}
	}

	function _csv($rows, $hide_cols = array('id')) {
		ini_set('memory_limit', '512M');

		$writer = Writer::createFromFileObject(new SplTempFileObject());
		$writer->setNewline("\r\n");

		$header = reset($rows);
		foreach ($hide_cols as $f)
			unset($header[$f]);
		$writer->insertOne(array_keys($header));
		
		foreach ($rows as $row) {
			foreach ($hide_cols as $f)
				unset($row[$f]);
			$writer->insertOne($row);
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $this->_class.date('_d_m_Y') . '.csv"');
		header('Cache-Control: max-age=0');
		echo $writer;
	}

	function _excel($rows, $hide_cols = array('id'), $format = 'Xlsx') {
		ini_set('memory_limit', '512M');

		$filename = $this->_class.date('_d_m_Y').".xlsx";
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		
		$styleSheet = [
			'font' => [
				'name' => 'Times New Roman',
				'size' => 10
			],
		];

		$styleHeading = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			]
		];

		// Header
		$header = array_keys(reset($rows));
		$j = 'A';
		foreach ($header as $f) {
			if (in_array($f, $hide_cols))
				continue;
			$sheet->setCellValue($j . '1', humanize($f));
			$sheet->getColumnDimension($j)->setAutoSize(true);
			$j++;
		}
		$sheet->getStyle('A1:' . $j . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				if (in_array($f, $hide_cols))
					continue;
				$sheet->setCellValue($j++ . $i, html_entity_decode($v));
			}
			$i++;
		}
		$sheet->getStyle('A1:'.$j.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:'.$j.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $format);
		$writer->save('php://output');
	}

	function _pdf($options, $header, $rows, $query = NULL) {
		
		if (isset($options['filename']))
			$filename = $options['filename'];
		else if (isset($options['page_title']))
			$filename = underscore($options['page_title']).'_'.uniqid();
		else
			$filename = $this->_class.uniqid();

		$dest = (isset($options['dest']) ? $options['dest'] : 'I');
		$options['header'] = $header;
		
		$orientation = (isset($options['orientation']) ? $options['orientation'] : PDF_PAGE_ORIENTATION);
		
		$border = 0; //'LTRB';
		$pdf = new MYPDF($orientation, PDF_UNIT, 'A4', true, 'UTF-8', false);
		$pdf->setFontSubsetting(false);
		$pdf->SetFont('times');
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(Auth::get('username'));
		$pdf->SetTitle($filename);
		$pdf->SetMargins(10, 25, 10, true);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(5);
		$pdf->SetAutoPageBreak(TRUE, 10);
		$pdf->SetOptions($options);
		$pdf->SetFillColor(230);
		$pdf->SetLineWidth(0.1);

		$pdf->AddPage($orientation);
		$width = $pdf->getPageWidth();

		// // Table Header
		// $pdf->SetFont('', 'B');
		// $pdf->SetFontSize(8, true);
		// foreach ($header as $h => $v) {
		// 	$pdf->Cell($v['width'], 5, $h, 1, 0, $v['align'], 1, '', 1, false, 'T', 'C');
		// }
		// $pdf->Ln();

		// Table Rows
		$pdf->SetFont('', '');
		$pdf->SetFontSize(8, true);

		$alternate_row = true;
		$show_total    = false;
		$total_rows    = count($rows);
		$i = 1;
		if (! is_null($rows)) {
			foreach($rows as $ri => $r) {
				$alternate_row = !$alternate_row;
				foreach ($header as $h => $v) {
					
					$row_border = 'LR';
					if (($ri+1) == $total_rows)
						$row_border = 'LRB';

					$pdf->SetFontSize($v['size'], true);

					if ($v['field'] == 'increment') {
						$pdf->Cell($v['width'], 5, $i++, $row_border, 0, $v['align'], $alternate_row, '', 1, false, 'T', 'C');
					}
					else {
						$pdf->Cell($v['width'], 5, $r[$v['field']], $row_border, 0, $v['align'], $alternate_row, '', 1, false, 'T', 'C');
						if (isset($v['total'])) {
							$show_total = true;
							$header[$h]['total'] = bcadd($header[$h]['total'], $r[$v['field']], 2);
						}
					}
				}
				$pdf->Ln();
			}
		}
		else {
			$ri = 1;
			while($r = $query->unbuffered_row('array')) {
				$alternate_row = !$alternate_row;
				foreach ($header as $h => $v) {
					
					$row_border = 'LR';
					if (($ri++) == $total_rows)
						$row_border = 'LRB';

					$pdf->SetFontSize($v['size'], true);

					if ($v['field'] == 'increment') {
						$pdf->Cell($v['width'], 5, $i++, $row_border, 0, $v['align'], $alternate_row, '', 1, false, 'T', 'C');
					}
					else {
						$pdf->Cell($v['width'], 5, $r[$v['field']], $row_border, 0, $v['align'], $alternate_row, '', 1, false, 'T', 'C');
						if (isset($v['total'])) {
							$show_total = true;
							$header[$h]['total'] = bcadd($header[$h]['total'], $r[$v['field']], 2);
						}
					}
				}
				$pdf->Ln();
			}
		}

		// Table Footer
		$pdf->SetFont('', 'B', 8);
		if ($show_total) {
			foreach ($header as $v) {
				if (isset($v['total']))
					$pdf->Cell($v['width'], 5, $v['total'], 1, 0, $v['align'], 1, '', 1, false, 'T', 'C');
				else
					$pdf->Cell($v['width'], 5, '', 1, 0, $v['align'], 1, '', 1, false, 'T', 'C');
			}
		}

		$pdf->Output(($dest == 'F' ? FCPATH.'tmp/' : '') . "$filename.pdf", $dest);
	}
}

class MYPDF extends TCPDF {
	var $company;
	var $page_title;
	var $page_desc;
	var $datetime;
	var $barcode;

	public function __construct() {
		parent::__construct();

		$this->company    = 'Company';
		$this->page_title = 'Title';
		$this->page_desc  = 'Description';
		$this->datetime   = true;
		$this->barcode    = 'http://idexindia.com';
	}

	public function SetOptions($options) {
		foreach($options as $k => $v)
			$this->$k = $v;
	}

	public function Header() {
		$this->setCellPaddings(1, 0.1, 1, 0.1);
		$this->SetFillColor(230);
		$this->SetLineWidth(0.1);

		$width = $this->getPageWidth() - 20;
		// $image_file = FCPATH.'logo_example.jpg';
		// $this->Image($image_file, 0, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->SetY(5);
		$this->SetFont('times', '', 14);
		$this->Cell($width, 5, $this->company, 0, 2, 'L', 0, '', 0, false, 'T', 'C');
		$this->SetFontSize(12, true);
		$this->Cell($width, 5, $this->page_title, 0, 2, 'L', 0, '', 0, false, 'T', 'C');
		$this->SetFont('', 'I', 8);
		$this->Cell($width, 3, $this->page_desc, 'B', 0, 'L', 0, '', 0, false, 'T', 'C');
		$this->Ln();

		if (isset($this->header)) {
			// Table Header
			$this->SetFont('', 'B');
			$this->SetFontSize(8, true);
			foreach ($this->header as $h => $v) {
				$this->Cell($v['width'], 5, $h, 1, 0, $v['align'], 1, '', 1, false, 'T', 'C');
			}
			$this->Ln();
		}
	}

	public function Footer() {
		$width = $this->getPageWidth() - 20;
		$width = $this->getPageWidth() - 20;

		if ($this->CurOrientation == 'P')
			$this->SetY(-12);  // P
		else
			$this->SetY(-10);  // L
		$this->SetFont('times', 'I', 8);
		$this->Cell($width/2, 3, ($this->datetime ? 'Generated on '.date('d-m-Y H:i:s') : ''), 'T', 0, 'L', 0, '', 0, false, 'T', 'C');
		$this->SetFont('times', 'I', 8);
		$this->Cell($width/2, 3, 'Page '.$this->getAliasNumPage().' / '.$this->getAliasNbPages(), 'T', 0, 'R', 0, '', 0, false, 'T', 'C');
		
		// $this->SetFont('times', '', 4);
		// $this->Cell(0, 3, 'Software By IDEX Solutions', 0, 0, 'C', 0, 'http://idexindia.com', 0, false, 'T', 'C');

		if ($this->barcode) {
			$style = [
				'border'       => 0,
				'padding'      => 'auto',
				'hpadding'     => 'auto',
				'vpadding'     => 5,
				'fgcolor'      => [0,0,0],
				'bgcolor'      => false,
				'text'         => false,
				'label'        => '',
				'font'         => 'times',
				'fontsize'     => 4,
				'stretchtext'  => 4,
				'position'     => '',
				'align'        => 'C',
				'stretch'      => false,
				'fitwidth'     => false,
				'cellfitalign' => 'C',
			];
			$this->Ln();
			$this->StartTransform();
			$this->Rotate(-90);
			if ($this->CurOrientation == 'P')
				$this->write1DBarcode($this->barcode, 'C128', -45, 290, 50, 4, 0.2, $style, 'N');
			else
				$this->write1DBarcode($this->barcode, 'C128', -45, 205, 50, 4, 0.2, $style, 'N');
			$this->StopTransform();
		}
	}
}
