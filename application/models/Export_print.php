<?php
class Export_print extends CI_Model {
	
	var $borderall;
	var $borderl;
	var $bordert;
	var $borderr;
	var $borderb;
	var $borderlt;
	var $bordertr;
	var $borderrb;
	var $borderlb;
	var $bordertb;
	var $borderltr;
	var $borderltb;

	function __construct() {

		parent::__construct();

		$this->borderall = [
			'T' => array('width' => 0.1),
			'R' => array('width' => 0.1),
			'B' => array('width' => 0.1),
			'L' => array('width' => 0.1),
		];
		$this->borderl = [
			'L' => array('width' => 0.1),
		];
		$this->bordert = [
			'T' => array('width' => 0.1),
		];
		$this->borderr = [
			'R' => array('width' => 0.1),
		];
		$this->borderb = [
			'B' => array('width' => 0.1),
		];
		$this->borderlt = [
			'L' => array('width' => 0.1),
			'T' => array('width' => 0.1),
		];
		$this->bordertr = [
			'T' => array('width' => 0.1),
			'R' => array('width' => 0.1),
		];
		$this->borderrb = [
			'R' => array('width' => 0.1),
			'B' => array('width' => 0.1),
		];
		$this->borderlb = [
			'L' => array('width' => 0.1),
			'B' => array('width' => 0.1),
		];
		$this->bordertb = [
			'T' => array('width' => 0.1),
			'B' => array('width' => 0.1),
		];
		$this->borderltr = [
			'T' => array('width' => 0.1),
			'R' => array('width' => 0.1),
		];
		$this->borderltb = [
			'L' => array('width' => 0.1),
			'T' => array('width' => 0.1),
			'B' => array('width' => 0.1),
		];
		
	}

	function filecover($pdf, $data, $letterhead) {

        //// Total Width of PAge IS 288
		$width = $pdf->GetPageWidth() - 20;

		$pdf->setX(10);
		$pdf->setY(10);
        //// Top Line Section
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell($width, 0, strtoupper($data['title']), '', 1, 'C', 0, false, 1, false, 'T', 'T');        
        
        $job_type = explode(',', $data['job']['sub_type']);
     
        $clearing = in_array('Clearing', $job_type) ? 'check.png' : 'uncheck.png';
        $transport = in_array('Transportation', $job_type) ? 'check.png' : 'uncheck.png';
        $forwarding = in_array('Forwarding', $job_type) ? 'check.png' : 'uncheck.png';

        $pdf->SetFont('helvetica', 'B', 15);
	    $pdf->setXY(20, 30);
	    
        $pdf->Image(FCPATH.'assets/dist/img/'.$forwarding, $pdf->getX(), $pdf->getY(), 6, 6, '', '', 'C', false, 300, '', false, false, 0, false, false, true);
        $pdf->setX($pdf->getX()+7);
        $pdf->Cell($width/3, 0, 'Forwarding', '', 0, 'L', 0, false, 1, false, 'T', 'T');
         
        $pdf->Image(FCPATH.'assets/dist/img/'.$clearing, $pdf->getX(), $pdf->getY(), 6, 6, '', '', 'C', false, 300, '', false, false, 0, false, false, true);
        $pdf->setX($pdf->getX()+7);
        $pdf->Cell($width/3, 0, 'Clearing', '', 0, 'L', 0, false, 1, false, 'T', 'T');
         
        $pdf->Image(FCPATH.'assets/dist/img/'.$transport, $pdf->getX(), $pdf->getY(), 6, 6, '', '', 'C', false, 300, '', false, false, 0, false, false, true);
        $pdf->setX($pdf->getX()+7);
        $pdf->Cell($width/3, 0, 'Transport', '', 1, 'L', 0, false, 1, false, 'T', 'T');
    	
    	$pdf->SetFont('helvetica', 'B', 11);
    	$pdf->setXY(20, 45);
    	$fcl = 'uncheck.png';
        $air = 'uncheck.png';
    	$lcl = 'uncheck.png';

    	$fcl = $data['job']['shipment_type'] === 'FCL' ? 'check.png' : 'uncheck.png';
		$air = $data['job']['shipment_type'] === 'Air' ? 'check.png' : 'uncheck.png';
        $lcl = $data['job']['shipment_type'] === 'LCL' ? 'check.png' : 'uncheck.png';
        
        $pdf->Image(FCPATH.'assets/dist/img/'.$fcl, $pdf->getX(), $pdf->getY(), 4, 4, '', '', 'C', false, 300, '', false, false, 0, false, false, true);
        $pdf->setX($pdf->getX()+7);
        $pdf->Cell($width/3, 0, 'Sea FCL', '', 1, 'L', 0, false, 1, false, 'T', 'T'); 

        $y = $pdf->getY();

        $pdf->setXY(20, 55);
    	$pdf->Image(FCPATH.'assets/dist/img/'.$air, $pdf->getX(), $pdf->getY(), 4, 4, '', '', 'C', false, 300, '', false, false, 0, false, false, true);
        $pdf->setX($pdf->getX()+7);
        $pdf->Cell($width/3, 0, 'AIR', '', 1, 'L', 0, false, 1, false, 'T', 'T'); 

        $pdf->setXY(20, 65);
    	
        $pdf->Image(FCPATH.'assets/dist/img/'.$lcl, $pdf->getX(), $pdf->getY(), 4, 4, '', '', 'C', false, 300, '', false, false, 0, false, false, true);
        $pdf->setX($pdf->getX()+7);
        $pdf->Cell($width/3, 0, 'LCL', '', 1, 'L', 0, false, 1, false, 'T', 'T');    

        $pdf->setCellPaddings(1, 1, 1, 1);

        $pdf->setXY(80, 45);
        $pdf->Cell(20, 0, 'JOB No', '', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->SetFont('helvetica', '', 11);
    	$pdf->Cell(50, 0, $data['job']['idkaabar_code'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11);
    	$pdf->Cell(10, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(10, 0, 'Date', '', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->SetFont('helvetica', '', 11);
    	$pdf->Cell(30, 0, $data['job']['date'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T'); 
        
      	$pdf->setXY(80, 55);
      	$pdf->SetFont('helvetica', 'B', 11);
    	$pdf->Cell(20, 0, 'Bill No', '', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(50, 0, '', 'B', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(10, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->SetFont('helvetica', 'B', 11);
    	$pdf->Cell(10, 0, 'Date', '', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(30, 0, '', 'B', 1, 'L', 0, false, 1, false, 'T', 'T'); 
        
        $pdf->setXY(80, 65);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(20, 0, 'Forex Bill', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(50, 0, '', 'B', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(10, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(10, 0, 'Date', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(30, 0, '', 'B', 1, 'L', 0, false, 1, false, 'T', 'T'); 

        $pdf->setXY(10, 85);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(30, 0, 'Customer Name', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(90, 0, $data['job']['party_name'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(10, 0, 'A/C', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(60, 0, $data['job']['shipper_name'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T'); 

        $pdf->setXY(10, 100);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(15, 0, 'Liner', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(80, 0, $data['job']['line_name'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(30, 0, 'Booking No', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(65, 0, $data['job']['booking_no'] .' / '.$data['job']['booking_date'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T'); 

        $pdf->setXY(10, 115);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(20, 0, 'Cntr Lot', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(35, 0, $data['job']['cntr_lot'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(25, 0, 'Pickup Dt.', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(35, 0, '', 'B', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(30, 0, 'Stuffing Dt.', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(35, 0, '', 'B', 1, 'L', 0, false, 1, false, 'T', 'T');

        $pdf->setXY(10, 130);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(20, 0, 'Invoice No', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(35, 0, $data['job']['invoice_no'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(25, 0, 'Invoice Dt.', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(35, 0, $data['job']['invoice_date'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(25, 0, 'Shipping Bill', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(40, 0, $data['job']['sb_no'].' / '.$data['job']['sb_date'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T'); 
        
        $pdf->setXY(10, 145);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(10, 0, 'POR', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(45, 0, $data['job']['por_name'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(15, 0, 'POL', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(45, 0, $data['job']['pol_name'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(20, 0, 'POD/FPOD', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(45, 0, $data['job']['pod_name'].' / '.$data['job']['fpod_name'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T');

        $pdf->setXY(10, 160);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(20, 0, 'MBL No', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(70, 0, $data['job']['mbl_no'].' / '.$data['job']['mbl_date'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(20, 0, 'HBL No', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(75, 0, $data['job']['hbl_no'].' / '.$data['job']['hbl_date'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T');

        $pdf->setXY(10, 175);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(25, 0, 'MBL Type', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(65, 0, $data['job']['mbl_type'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(25, 0, 'HBL Type', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(70, 0, $data['job']['hbl_type'], 'B', 1, 'L', 0, false, 1, false, 'T', 'T');


        $pdf->setXY(10, 190);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(15, 0, 'ETA', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(30, 0, $data['job']['eta_date'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(15, 0, 'ETD', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(30, 0, $data['job']['etd_date'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T'); 
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(25, 0, 'CFS/Yard', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(65, 0, '', 'B', 1, 'L', 0, false, 1, false, 'T', 'T');

        $pdf->setXY(10, 205);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(12, 0, 'CHA', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(78, 0, $data['job']['cha_name'], 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(12, 0, 'TPT', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(83, 0, '', 'B', 1, 'L', 0, false, 1, false, 'T', 'T');

        $pdf->setXY(10, 220);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(15, 0, 'Agent', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(75, 0, '', 'B', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->Cell(5, 0, '', '', 0, 'L', 0, false, 1, false, 'T', 'T');
     	$pdf->SetFont('helvetica', 'B', 11); 
        $pdf->Cell(30, 0, 'Lift on Payment', '', 0, 'L', 0, false, 1, false, 'T', 'T');
        $pdf->SetFont('helvetica', '', 11); 
        $pdf->Cell(65, 0, '', 'B', 1, 'L', 0, false, 1, false, 'T', 'T');


        $pdf->Image(FCPATH.'assets/dist/img/logo-print.png', $pdf->getX(), ($pdf->getY()+15), '', '', '', '', 'C', false, 300, '', false, false, 0, false, false, true);
    }

    function export_invoice($pdf, $data, $letterhead) {

		
		
		extract($data);
        $calibri = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri.ttf', 'TrueTypeUnicode', '', 32);
        $calibribold = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri-bold.ttf', 'TrueTypeUnicode', '', 32);
		/*$calibri = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/Calibri Regular/Calibri Regular.ttf', 'TrueTypeUnicode', '', 32);
        $calibribold = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/Calibri Bold/Calibri Bold.TTF', 'TrueTypeUnicode', '', 32);*/
		$width = $pdf->GetPageWidth() - 10;
 		$pdf->AddPage();

		

		//public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M') {

			//public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x=null, $y=null, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false) {

			//public function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array()) {
			
		//$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));

		$pdf->voucher = $voucher;
		$pdf->job = $job;
		$pdf->invoice_type = $invoice_type;

 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);
		
		$pdf->Ln();

		$pdf->setY($pdf->getY());
		$pdf->SetFont($calibribold, '', 10);
		$pdf->SetFillColor(153,204,255); // Grey
		$pdf->SetTextColor(0,0,0);
		$pdf->SetLineStyle(array('width' => 0.01));
		$pdf->setCellPadding(1);

		$pdf->Cell(($width*40/100), '', 'BILL NO', 1, 0, 'L', true, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*60/100), '', 'INVOICE DETAILS', 1, 1, 'C', true, false, 1, false, 'T', 'C');
		$pdf->SetFont($calibri, '', 10);
		$y = $pdf->getY();
		$x = ($width*40/100)+5;
		$pdf->MultiCell($width*40/100, 22.6, $voucher['debit_party_name']. "\n". $voucher['debit_party_address'], 'LR', 'L', false, 1, '', '', true, 0, false, true, 22.6, 'M', true);
		
		$pdf->setCellPaddings(1, 0, 0, 0);
		
		$pdf->Cell(($width*40/100), '', isset($voucher['ledger']['gst_nos']) ? 'GSTIN : '.$voucher['ledger']['gst_nos'] : 'GSTIN : ', 'LR', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setCellPaddings(1, 0, 0, 1);
		$pdf->Cell(($width*40/100), '', isset($voucher['ledger']['statename']) ? 'Place of Supply : '.$voucher['ledger']['statename'] : 'Place of Supply : ', 'LRB', 1, 'L', false, false, 1, false, 'T', 'C');
		


		$pdf->SetFont($calibri, '', 10);
		$pdf->setY($y);
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'MBL No', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['mbl_no']) ? $job['mbl_no'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['mbl_date']) ? $job['mbl_date'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'HBL No', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['hbl_no']) ? $job['hbl_no'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['hbl_date']) ? $job['hbl_date'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'POR', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['por_name']) ? $job['por_name'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'POL', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['pol_name']) ? $job['pol_name'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'POD', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['pod_name']) ? $job['pod_name'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'FPOD', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['fpod_name']) ? $job['fpod_name'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'INV No', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['invoice_no']) ? $job['invoice_no'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['invoice_date']) ? $job['invoice_date'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'SB NO', 'B', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['sb_no']) ? $job['sb_no'] : '', 'B', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', 'B', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['sb_date']) ? $job['sb_date'] : '', 'BR', 1, 'L', false, false, 1, false, 'T', 'C');
 		
		
		
			$container_types   = [];
			$container_numbers = [];
			// echo "<pre>";
			// print_r($containers);
			foreach ($containers as $c) {
				if (isset($container_types[$c['container_type']]))
					$container_types[$c['container_type']] += 1;
				else
					$container_types[$c['container_type']] = 1;

				$container_numbers[] = $c['number'];
			}
			foreach($container_types as $ct => $count) {
				$c1 = $count.'X'.$ct;
			}

		$pdf->MultiCell($width, 12, 'Container No : '.$cont_2040."\n".implode(', ', $container_numbers), 'LRB', 'L', false, 1, '', '', true, 0, false, true, 12, 'M', true);
		
		//Sr. No	Description of supply	HSN	Rate	Qty	Total Amt	SGST		CGST		IGST	TOTAL
		//				Rate	Amt	Rate	Amt	18%	
		///// Start Header Section		
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell($width*5/100, 10, 'Sr. No', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*25/100, 10, 'Particulars', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*8/100, 10, 'HSN/SAC', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*8/100, 10, 'Rate', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*6/100, 10, 'QTY', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*10/100, 10, 'Amount', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$y = $pdf->getY();
		$x = $pdf->getX();

		if(substr($company['gst_no'],0,2) == $voucher['ledger']['state']){

			$pdf->Cell($width*12/100, 5, 'CGST', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->Cell($width*12/100, 5, 'SGST', 'L', 1, 'C', 1, false, 1, false, 'T', 'C');
			//$pdf->setY($y+5);
			$pdf->setX($x);
			$pdf->Cell($width*4/100, 5, '%', 'LT', 0, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->Cell($width*8/100, 5, 'Amt', 'LT', 0, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->Cell($width*4/100, 5, '%', 'LT', 0, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->Cell($width*8/100, 5, 'Amt', 'LT', 0, 'C', 1, false, 1, false, 'T', 'C');
			$x = $pdf->getX();
			$pdf->setY($y);
			$pdf->setX($x);
			
		}
		else
		{
			$pdf->Cell($width*24/100, 5, 'IGST', 'L', 1, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->setX($x);
			$pdf->Cell($width*8/100, 5, '%', 'LT', 0, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, 5, 'Amt', 'LT', 0, 'C', 1, false, 1, false, 'T', 'C');
			$x = $pdf->getX();
			$pdf->setY($y);
			$pdf->setX($x);
			
		}	

		
		$pdf->Cell($width*14/100, 10, 'Total', 'LR', 1, 'C', 1, false, 1, false, 'T', 'C');		
		
		///// Complete Header Section

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$total = [
			'amount'     => 0,
			'tax_amount' => 0,
			'cgst'       => 0,
			'sgst'       => 0,
			'igst'       => 0,
			'cgst_amount'       => 0,
			'sgst_amount'       => 0,
			'igst_amount'       => 0,
			'gst_amount'       => 0,
			'total'      => 0,
			'gross_amount' => 0,
		];
		$remarks = '';
		$i       = 1;
		$gst_tax = [];

		
		foreach ($voucher_details as $r) {

			
			
			$r['cgst_amount'] = $r['cgst'];
			$r['sgst_amount'] = $r['sgst'];
			$r['igst_amount'] = $r['igst'];

			unset($r['cgst']);
			unset($r['sgst']);
			unset($r['igst']);

			$vchcode = substr(isset($voucher['ledger']['gst_no']), 0, 2);
			$cmpcode = substr($company['gst_no'], 0, 2);
			if($vchcode == $cmpcode){
				// CGST // SGST
				$r['cgst'] = $r['gst'] / 2;
				$r['sgst'] = $r['gst'] / 2;
				$r['igst'] = 0;
			}
			else
			{
				// IGST
				$r['cgst'] = 0;
				$r['sgst'] = 0;
				$r['igst'] = $r['gst'];
			}


			$cgst   = bcdiv(bcmul($r['amount'], $r['cgst'], 2), 100, 2);
			$sgst   = bcdiv(bcmul($r['amount'], $r['sgst'], 2), 100, 2);
			$igst   = bcdiv(bcmul($r['amount'], $r['igst'], 2), 100, 2);
			$amount = round($r['amount'] + $cgst + $sgst + $igst);


			$cgst_amount = round($r['cgst_amount']);
			$sgst_amount = round($r['sgst_amount']);
			$igst_amount = round($r['igst_amount']);
			$gst_amount = round($r['gst_amount']);
			$grandamount = round($r['gross_amount']);

			$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
			$total['cgst']   = bcadd($total['cgst'], $cgst, 2);
			$total['sgst']   = bcadd($total['sgst'], $sgst, 2);
			$total['igst']   = bcadd($total['igst'], $igst, 2);
			$total['total']  = bcadd($total['total'], $amount, 2);

			// $cgst_amount   = bcdiv(bcmul($r['amount'], $r['cgst_amount'], 2), 100, 2);
			// $sgst_amount   = bcdiv(bcmul($r['amount'], $r['sgst_amount'], 2), 100, 2);
			// $igst_amount   = bcdiv(bcmul($r['amount'], $r['igst_amount'], 2), 100, 2);

			$total['cgst_amount']   = bcadd($total['cgst_amount'], $cgst_amount, 2);
			$total['sgst_amount']   = bcadd($total['sgst_amount'], $sgst_amount, 2);
			$total['igst_amount']   = bcadd($total['igst_amount'], $igst_amount, 2);
			$total['gst_amount']   = bcadd($total['gst_amount'], $gst_amount, 2);
			$total['gross_amount']  = bcadd($total['gross_amount'], $grandamount, 2);
			

			if (isset($gst_tax[$r['hsn_code']]['cgst'])) {
				$gst_tax[$r['hsn_code']]['cgst']['amount'] += $r['amount'];
				$gst_tax[$r['hsn_code']]['cgst']['tax']    += $cgst;
				$gst_tax[$r['hsn_code']]['sgst']['amount'] += $r['amount'];
				$gst_tax[$r['hsn_code']]['sgst']['tax']    += $sgst;
				$gst_tax[$r['hsn_code']]['igst']['amount'] += $r['amount'];
				$gst_tax[$r['hsn_code']]['igst']['tax']    += $igst;
			}
			else {
				$gst_tax[$r['hsn_code']]['cgst'] = ['rate' => $r['cgst'], 'amount' => $r['amount'], 'tax' => $cgst];
				$gst_tax[$r['hsn_code']]['sgst'] = ['rate' => $r['sgst'], 'amount' => $r['amount'], 'tax' => $sgst];
				$gst_tax[$r['hsn_code']]['igst'] = ['rate' => $r['igst'], 'amount' => $r['amount'], 'tax' => $igst];
			}

			

			// echo '<pre>';
			// print_r($r);exit;

			$unit = $this->kaabar->getRow('units', $r['unit_id']);
			$pdf->setCellPaddings(1, 1, 1, 1);
			$pdf->Cell($width*5/100, 5, $i++, 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*25/100, 5, $r['particulars'], 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*8/100, 5, $r['hsn_code'], 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*8/100, 5, $r['rate'], 'LT', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*6/100, 5, $r['qty'], 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*10/100, 5, $r['amount'], 'LT', 0, 'R', 0, false, 1, false, 'T', 'C');


			if(substr($company['gst_no'],0,2) == $voucher['ledger']['state']){
				$pdf->Cell($width*4/100, 5, $r['gst']/2, 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell($width*8/100, 5, $r['cgst_amount'], 'LT', 0, 'R', 0, false, 1, false, 'T', 'C');
				$pdf->Cell($width*4/100, 5, $r['gst']/2, 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell($width*8/100, 5, $r['gst_amount'], 'LT', 0, 'R', 0, false, 1, false, 'T', 'C');
			}
			else
			{
				$pdf->Cell($width*8/100, 5, $r['gst'], 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell($width*16/100, 5, $r['gst_amount'], 'LT', 0, 'R', 0, false, 1, false, 'T', 'C');
			}
			$pdf->Cell($width*14/100, 5, inr_format($r['gross_amount'], 2, '.', ''), 'LRT', 1, 'R', 0, false, 1, false, 'T', 'C');		

			// $pdf->Cell(10, 5,  $i++, 'LRB', 0, 'C', 0, false, 1, false, 'T', 'C');
			// $pdf->Cell(100, 5, $r['particulars'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
			// $pdf->Cell(15, 5, $r['hsn_code'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
			// $pdf->Cell(10, 5, $unit['code'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
			// $pdf->Cell(15, 5, $r['rate'], 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			// $pdf->Cell(15, 5, $r['currency_amt'], 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			// $pdf->Cell(25, 5, inr_format($r['amount'], 2, '.', ''), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		}
		
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell($width*52/100, 5, 'Total', 'LTB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*10/100, 5, inr_format($total['amount']), 'LTB', 0, 'R', 0, false, 1, false, 'T', 'C');

		if(substr($company['gst_no'],0,2) == $voucher['ledger']['state']){
			$pdf->Cell($width*4/100, 5, '', 'LTB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*8/100, 5, inr_format($total['cgst_amount']), 'LTB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*4/100, 5, '', 'LTB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*8/100, 5, inr_format($total['sgst_amount']), 'LTB', 0, 'R', 0, false, 1, false, 'T', 'C');
		}
		else {

			$pdf->Cell($width*8/100, 5, '', 'LTB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, 5, inr_format($total['gst_amount']), 'LTB', 0, 'R', 0, false, 1, false, 'T', 'C');
			
		}

		
		$pdf->Cell($width*14/100, 5, inr_format($total['gross_amount'], 2, '.', ''), 'LRTB', 1, 'R', 0, false, 1, false, 'T', 'C');	

		$y = $pdf->getY();
		$pdf->Ln();
		$pdf->SetFont($calibri, '', 10);
		$pdf->MultiCell($width*60/100, 10, '<b>Total Amount (In words) : </b>'.numberToWords($total['gross_amount']), '', 'L', false, 1, '', '', true, 0, true, true, 10, 'M', true);


		$pdf->SetTextColor(0,112,192);
		$pdf->setCellPaddings(1, 0, 1, 0);
		$pdf->Cell($width*60/100, '', 'Our Bank Details (INR Payment)', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'In Favour Of : TRANISO LOGISTICS', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'Current A/c No. : 59226666677777', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'Bank Name : HDFC BANK LIMITED', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'Branch & IFS Code: ROTARY CIRCLE & HDFC0007364', 0, 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->Ln();
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($calibribold, '', 8);
		$pdf->Cell($width*60/100, '', 'Terms & Conditions', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont($calibri, '', 8);
		$pdf->Cell($width*60/100, '', '1. ) Interest @ 18 % P.A will be charged, if payment not made within due dates', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '2. ) TDS to deduct on taxable invoice only , not to be deducted on reimbursement invoice', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '3.) In case of Any Dispute in Invoice Let us know within 7 days of receipt', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '4.) In case of any GST issue, contact us before 10th Day of Every Month. Once GST is filled We will not Change Invoice.', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '5.) Subject to Gandhidham Jurisdiction.', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		

		$pdf->setY($y);
		$pdf->ln();
		$pdf->ln();
		$pdf->SetFont($calibri, '', 9);
		$pdf->setX(($width*60/100)+5);
		$pdf->setCellPaddings(1, 0.5, 1, 0.3);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Taxable Amt', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format($total['amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');

		if(substr($company['gst_no'],0,2) == $voucher['ledger']['state']){
			$pdf->setX(($width*60/100)+5);
			$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, '', 'SGST AMT', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*14/100, '', inr_format($total['cgst_amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->setX(($width*60/100)+5);
			$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, '', 'CGST AMT', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*14/100, '', inr_format($total['sgst_amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->setX(($width*60/100)+5);
			$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, '', 'IGST AMT', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*14/100, '', inr_format($total['igst_amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
		}
		else
		{
			$pdf->setX(($width*60/100)+5);
			$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, '', 'SGST AMT', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*14/100, '', inr_format(0, 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->setX(($width*60/100)+5);
			$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, '', 'CGST AMT', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*14/100, '', inr_format(0, 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->setX(($width*60/100)+5);
			$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*16/100, '', 'IGST AMT', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*14/100, '', inr_format($total['gst_amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
		}

		
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Total', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format($total['gross_amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Round Off', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format(0, 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'LB', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Invoice Total', 'LTB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format($total['gross_amount'], 2, '.', ''), 'LTRB', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		$pdf->Ln();
		$pdf->SetTextColor(0,112,192);
		$pdf->SetFont($calibribold, '', 11);
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*40/100, '', 'For , Traniso Logistics', '', 1, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setY($pdf->getY()+15);
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*40/100, '', 'Authorised Signatory', '', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		
		//// Draw Border
		$pdf->setX(5);
		$pdf->setY(5);

		$pdf->Cell($width, 280, '', 'LTRB', 0, 'L', false, false, 1, false, 'T', 'C');
		//$pdf->SetY(230);
 		//$this->pdf_invoice_footer($pdf, $voucher['id'], $company, true);
	}

	function nongst_invoice($pdf, $data, $letterhead) {

		
		
		extract($data);
        $calibri = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri.ttf', 'TrueTypeUnicode', '', 32);
        $calibribold = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri-bold.ttf', 'TrueTypeUnicode', '', 32);
		/*$calibri = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/Calibri Regular/Calibri Regular.ttf', 'TrueTypeUnicode', '', 32);
        $calibribold = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/Calibri Bold/Calibri Bold.TTF', 'TrueTypeUnicode', '', 32);*/
		$width = $pdf->GetPageWidth() - 10;
 		$pdf->AddPage();

		

		//public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M') {

			//public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x=null, $y=null, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false) {

			//public function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array()) {
			
		//$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));
        $jobdata=array();
        if(isset($job))
            $jobdata=$job;

		$pdf->voucher = $voucher;
		$pdf->job = $jobdata;
		$pdf->invoice_type = $invoice_type;

 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);
		
		$pdf->Ln();

		$pdf->setY($pdf->getY());
		$pdf->SetFont($calibribold, '', 10);
		$pdf->SetFillColor(153,204,255); // Grey
		$pdf->SetTextColor(0,0,0);
		$pdf->SetLineStyle(array('width' => 0.01));
		$pdf->setCellPadding(1);

		$pdf->Cell(($width*40/100), '', 'BILL NO', 1, 0, 'L', true, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*60/100), '', 'INVOICE DETAILS', 1, 1, 'C', true, false, 1, false, 'T', 'C');
		$pdf->SetFont($calibri, '', 10);
		$y = $pdf->getY();
		$x = ($width*40/100)+5;
		$pdf->MultiCell($width*40/100, 22.6, $voucher['debit_party_name']. "\n". $voucher['debit_party_address'], 'LR', 'L', false, 1, '', '', true, 0, false, true, 22.6, 'M', true);
		
		$pdf->setCellPaddings(1, 0, 0, 0);
		
		$pdf->Cell(($width*40/100), '', isset($voucher['ledger']['gst_nos']) ? 'GSTIN : '.$voucher['ledger']['gst_nos'] : 'GSTIN : ', 'LR', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setCellPaddings(1, 0, 0, 1);
		$pdf->Cell(($width*40/100), '', isset($voucher['ledger']['statename']) ? 'Place of Supply : '.$voucher['ledger']['statename'] : 'Place of Supply : ', 'LRB', 1, 'L', false, false, 1, false, 'T', 'C');
		


		$pdf->SetFont($calibri, '', 10);
		$pdf->setY($y);
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'MBL No', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['mbl_no']) ? $job['mbl_no'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['mbl_date']) ? $job['mbl_date'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'HBL No', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['hbl_no']) ? $job['hbl_no'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['hbl_date']) ? $job['hbl_date'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'POR', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['por_name']) ? $job['por_name'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'POL', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['pol_name']) ? $job['pol_name'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'POD', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['pod_name']) ? $job['pod_name'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'FPOD', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['fpod_name']) ? $job['fpod_name'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'INV No', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['invoice_no']) ? $job['invoice_no'] : '', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', '', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['invoice_date']) ? $job['invoice_date'] : '', 'R', 1, 'L', false, false, 1, false, 'T', 'C');
		$pdf->setX($x);
		$pdf->Cell(($width*10/100), '', 'SB NO', 'B', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['sb_no']) ? $job['sb_no'] : '', 'B', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*10/100), '', 'Date', 'B', 0, 'L', false, false, 1, false, 'T', 'C');
		$pdf->Cell(($width*20/100), '', isset($job['sb_date']) ? $job['sb_date'] : '', 'BR', 1, 'L', false, false, 1, false, 'T', 'C');
 		
		
		
			$container_types   = [];
			$container_numbers = [];
			// echo "<pre>";
			if(isset($containers))
            {
    			foreach ($containers as $c) {
    				if (isset($container_types[$c['container_type']]))
    					$container_types[$c['container_type']] += 1;
    				else
    					$container_types[$c['container_type']] = 1;

    				$container_numbers[] = $c['number'];
    			}
    			foreach($container_types as $ct => $count) {
    				$c1 = $count.'X'.$ct;
    			}
            }
            //new one
            $cont_2040_new='';
            if(isset($cont_2040))
                $cont_2040_new=$cont_2040;
		$pdf->MultiCell($width, 12, 'Container No : '.$cont_2040_new."\n".implode(', ', $container_numbers), 'LRB', 'L', false, 1, '', '', true, 0, false, true, 12, 'M', true);
		
		//Sr. No	Description of supply	HSN	Rate	Qty	Total Amt	SGST		CGST		IGST	TOTAL
		//				Rate	Amt	Rate	Amt	18%	
		///// Start Header Section		
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell($width*5/100, 10, 'Sr. No', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*40/100, 10, 'Particulars', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*12/100, 10, 'HSN/SAC', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*13/100, 10, 'Rate', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*10/100, 10, 'QTY', 'L', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell($width*20/100, 10, 'Amount', 'L', 1, 'C', 1, false, 1, false, 'T', 'C');
		
		///// Complete Header Section

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$total = [
			'amount'     => 0,
			'tax_amount' => 0,
			'cgst'       => 0,
			'sgst'       => 0,
			'igst'       => 0,
			'cgst_amount'       => 0,
			'sgst_amount'       => 0,
			'igst_amount'       => 0,
			'gst_amount'       => 0,
			'total'      => 0,
			'gross_amount' => 0,
		];
		$remarks = '';
		$i       = 1;
		$gst_tax = [];

		
		foreach ($voucher_details as $r) {

			
			
			$r['cgst_amount'] = $r['cgst'];
			$r['sgst_amount'] = $r['sgst'];
			$r['igst_amount'] = $r['igst'];

			unset($r['cgst']);
			unset($r['sgst']);
			unset($r['igst']);

			$vchcode = substr(isset($voucher['ledger']['gst_no']), 0, 2);
			$cmpcode = substr($company['gst_no'], 0, 2);
			if($vchcode == $cmpcode){
				// CGST // SGST
				$r['cgst'] = $r['gst'] / 2;
				$r['sgst'] = $r['gst'] / 2;
				$r['igst'] = 0;
			}
			else
			{
				// IGST
				$r['cgst'] = 0;
				$r['sgst'] = 0;
				$r['igst'] = $r['gst'];
			}


			$cgst   = bcdiv(bcmul($r['amount'], $r['cgst'], 2), 100, 2);
			$sgst   = bcdiv(bcmul($r['amount'], $r['sgst'], 2), 100, 2);
			$igst   = bcdiv(bcmul($r['amount'], $r['igst'], 2), 100, 2);
			$amount = round($r['amount'] + $cgst + $sgst + $igst);


			$cgst_amount = round($r['cgst_amount']);
			$sgst_amount = round($r['sgst_amount']);
			$igst_amount = round($r['igst_amount']);
			$gst_amount = round($r['gst_amount']);
			$grandamount = round($r['gross_amount']);

			$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
			$total['cgst']   = bcadd($total['cgst'], $cgst, 2);
			$total['sgst']   = bcadd($total['sgst'], $sgst, 2);
			$total['igst']   = bcadd($total['igst'], $igst, 2);
			$total['total']  = bcadd($total['total'], $amount, 2);

			// $cgst_amount   = bcdiv(bcmul($r['amount'], $r['cgst_amount'], 2), 100, 2);
			// $sgst_amount   = bcdiv(bcmul($r['amount'], $r['sgst_amount'], 2), 100, 2);
			// $igst_amount   = bcdiv(bcmul($r['amount'], $r['igst_amount'], 2), 100, 2);

			$total['cgst_amount']   = bcadd($total['cgst_amount'], $cgst_amount, 2);
			$total['sgst_amount']   = bcadd($total['sgst_amount'], $sgst_amount, 2);
			$total['igst_amount']   = bcadd($total['igst_amount'], $igst_amount, 2);
			$total['gst_amount']   = bcadd($total['gst_amount'], $gst_amount, 2);
			$total['gross_amount']  = bcadd($total['gross_amount'], $grandamount, 2);
			

			if (isset($gst_tax[$r['hsn_code']]['cgst'])) {
				$gst_tax[$r['hsn_code']]['cgst']['amount'] += $r['amount'];
				$gst_tax[$r['hsn_code']]['cgst']['tax']    += $cgst;
				$gst_tax[$r['hsn_code']]['sgst']['amount'] += $r['amount'];
				$gst_tax[$r['hsn_code']]['sgst']['tax']    += $sgst;
				$gst_tax[$r['hsn_code']]['igst']['amount'] += $r['amount'];
				$gst_tax[$r['hsn_code']]['igst']['tax']    += $igst;
			}
			else {
				$gst_tax[$r['hsn_code']]['cgst'] = ['rate' => $r['cgst'], 'amount' => $r['amount'], 'tax' => $cgst];
				$gst_tax[$r['hsn_code']]['sgst'] = ['rate' => $r['sgst'], 'amount' => $r['amount'], 'tax' => $sgst];
				$gst_tax[$r['hsn_code']]['igst'] = ['rate' => $r['igst'], 'amount' => $r['amount'], 'tax' => $igst];
			}

			
			$unit = $this->kaabar->getRow('units', $r['unit_id']);
			$pdf->setCellPaddings(1, 1, 1, 1);
			$pdf->Cell($width*5/100, 5, $i++, 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*40/100, 5, $r['particulars'], 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*12/100, 5, $r['hsn_code'], 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*13/100, 5, $r['rate'], 'LT', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*10/100, 5, $r['qty'], 'LT', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell($width*20/100, 5, $r['amount'], 'LT', 1, 'R', 0, false, 1, false, 'T', 'C');
			
		}
		
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell($width*80/100, 5, 'Total', 'LTB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*20/100, 5, inr_format($total['gross_amount'], 2, '.', ''), 'LRTB', 1, 'R', 0, false, 1, false, 'T', 'C');	

		$y = $pdf->getY();
		$pdf->Ln();
		$pdf->SetFont($calibri, '', 10);
		$pdf->MultiCell($width*60/100, 10, '<b>Total Amount (In words) : </b>'.numberToWords($total['gross_amount']), '', 'L', false, 1, '', '', true, 0, true, true, 10, 'M', true);


		$pdf->SetTextColor(0,112,192);
		$pdf->setCellPaddings(1, 0, 1, 0);
		$pdf->Cell($width*60/100, '', 'Our Bank Details (INR Payment)', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'In Favour Of : TRANISO LOGISTICS', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'Current A/c No. : 59226666677777', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'Bank Name : HDFC BANK LIMITED', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', 'Branch & IFS Code: ROTARY CIRCLE & HDFC0007364', 0, 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->Ln();
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($calibribold, '', 8);
		$pdf->Cell($width*60/100, '', 'Terms & Conditions', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont($calibri, '', 8);
		$pdf->Cell($width*60/100, '', '1. ) Interest @ 18 % P.A will be charged, if payment not made within due dates', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '2. ) TDS to deduct on taxable invoice only , not to be deducted on reimbursement invoice', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '3.) In case of Any Dispute in Invoice Let us know within 7 days of receipt', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '4.) In case of any GST issue, contact us before 10th Day of Every Month. Once GST is filled We will not Change Invoice.', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*60/100, '', '5.) Subject to Gandhidham Jurisdiction.', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		

		$pdf->setY($y);
		$pdf->ln();
		$pdf->ln();
		$pdf->SetFont($calibri, '', 9);
		$pdf->setX(($width*60/100)+5);
		$pdf->setCellPaddings(1, 0.5, 1, 0.3);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Taxable Amt', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format($total['amount'], 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Round Off', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format(0, 2, '.', ''), 'LTR', 1, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*10/100, '', '', 0, 0, 'LB', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*16/100, '', 'Invoice Total', 'LTB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width*14/100, '', inr_format($total['gross_amount'], 2, '.', ''), 'LTRB', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
		$pdf->SetTextColor(0,112,192);
		$pdf->SetFont($calibribold, '', 11);
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*40/100, '', 'For , Traniso Logistics', '', 1, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setY($pdf->getY()+15);
		$pdf->setX(($width*60/100)+5);
		$pdf->Cell($width*40/100, '', 'Authorised Signatory', '', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		
		//// Draw Border
		$pdf->setX(5);
		$pdf->setY(5);

		$pdf->Cell($width, 280, '', 'LTRB', 0, 'L', false, false, 1, false, 'T', 'C');
		//$pdf->SetY(230);
 		//$this->pdf_invoice_footer($pdf, $voucher['id'], $company, true);
	}

    function pdf_header($pdf, $company, $city, $state, $letterhead) {
        

		$border = 0;
		$width  = $pdf->GetPageWidth() - 20;
        
        $calibri = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri.ttf', 'TrueTypeUnicode', '', 32);
        $calibribold = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/calibri-bold.ttf', 'TrueTypeUnicode', '', 32);
		
       

		$pdf->setX(10);
		$pdf->SetFont($calibribold, '', 12);
		$pdf->SetTextColor(0,112,192);
		$pdf->Cell($width/3, '', $company['name'], '', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->setX(10);
		$pdf->SetFont($calibri, '', 10);
		$pdf->SetTextColor(0,0,0);
		$address = $company['address'].', '.$company['city_id'].' - '.$company['pincode'];
		$pdf->MultiCell($width/3, 14, $address, '', 'L', false, 1, '', '', true, 0, false, false, 14, 'T', true);
		$pdf->setX(10);
		$pdf->Cell($width/3, '', 'E-Mail : '.strtolower($company['email']), '', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont($calibribold, '', 10);
		$pdf->setX(10);
		$pdf->Cell($width/3, '', 'GSTIN : '.$company['gst_no'], '', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->setX(10);
		$pdf->Cell($width/3, '', 'PAN NO : '.$company['pan_no'], '', 1, 'L', 0, false, 1, false, 'T', 'C');
			
		$pdf->ln();
		
		$pdf->SetFont($calibribold, '', 11);
		$pdf->SetTextColor(0,112,192);
		$y = $pdf->getY();
		$pdf->setX(10);
		$pdf->Cell(($width/3)*40/100, '', 'Invoice No', '', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->setX(10);
		$pdf->Cell(($width/3)*40/100, '', 'Invoice Dt', '', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->setX(10);
		$pdf->Cell(($width/3)*40/100, '', 'Job No', '', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->setXY((($width/3)*40/100)+5, $y);
		$pdf->SetFont($calibri, '', 11);
		$pdf->SetTextColor(0,0,0);

		$pdf->Cell(($width/3)*60/100, '', ': '.$pdf->voucher['idkaabar_code'], '', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->setX((($width/3)*40/100)+5);
		$pdf->Cell(($width/3)*60/100, '', ': '.$pdf->voucher['date'], '', 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->setX((($width/3)*40/100)+5);

        $idkaabar_code='';
        if(isset($pdf->job['idkaabar_code']))
            $idkaabar_code=$pdf->job['idkaabar_code'];


		$pdf->Cell(($width/3)*60/100, '', ': '.$idkaabar_code, '', 0, 'L', 0, false, 1, false, 'T', 'C');
		
		
		$pdf->Image(FCPATH.'php_uploads/' . $company['logo'], $width/3, 10, $width/3, null, 'png', '', 'M', true, 300, 'C', false, false, $border, true, false, false, false);
		
		$pdf->Image(FCPATH.'php_uploads/qrcode.jpg', (($width/3)+($width/3)), 10, 30, null, 'jpg', '', 'M', true, 300, 'R', false, false, $border, true, false, false, false);
		$pdf->setY(40);
		$pdf->Cell($width-5, '', 'IRN', '', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->setY($y);
		$pdf->Cell(($width/3)+($width/3)+30, '', '', '', 0, 'R', 0, false, 1, false, 'T', 'C');

		$pdf->MultiCell(($width/3)-22, '', 'fd3e755ee9231a5a4603f8477dd701a76c643b8f24a41240b2da9a24a876d3bc', '', 'L', false, 1, '', '', true, 0, false, false, 15, 'T', true);
		
		$pdf->SetFont($calibribold, '', 16);
		$pdf->SetTextColor(0,112,192);
		$pdf->Cell($width, '', $pdf->invoice_type, '', 0, 'R', 0, false, 1, false, 'T', 'C');

		
	}

	function pdf_invoice_footer($pdf, $id, $company, $pto = false) {
		// $border = 0;
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
		$width  = $pdf->GetPageWidth() - 20;

		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(40, 3, 'GSTIN', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(40, 3, 'PAN No', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(40, 3, '', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(70, 3, 'For '.$company['name'], 'LTR', 1, 'R', 0, false, 1, false, 'T', 'T');
		
		$y = $pdf->GetY();
		$pdf->SetFont('', '', 8);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(40, 5, $company['gst_no'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(40, 5, $company['pan_no'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(40, 5, '', 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(60,60,60);
		$pdf->Cell(60, 3, 'CHA License No', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(60, 3, 'CIN No', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(60, 5, $company['cha_license_no'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(60, 5, isset($company['cin_no']) ? $company['cin_no'] : '', 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', '', 8);
		$pdf->MultiCell(120, 21.5, $company['remarks'], 'LTRB', 'L', false, 1, 10, $pdf->GetY(), true, 0, false, true, 21.5, 'T', true);

		$pdf->SetXY(130, $y);
		$pdf->Cell(70, 35, 'Authorised Signatory', 'RB', 1, 'R', 0, false, 1, false, 'T', 'B');
		  	
		$pdf->Cell($width, 3, 'Subject to Gandhidham Jurisdiction', 0, 1, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell($width, 3, 'E.&O.E.', 0, 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetXY(3, 260);
		$pdf->StartTransform();
		$pdf->Rotate(90);
		$pdf->write1DBarcode($id, 'C128', -25, 260, 50, 4, 0.2, $barcode_style, 'N');
	}
}
