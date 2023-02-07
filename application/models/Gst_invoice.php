<?php

class Gst_invoice extends CI_Model {
	function __construct() {
		parent::__construct();

		
		$ITFRupeeRegular = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/ITFRupeeRegular.ttf', 'ITFRupeeRegular', '', 96);

		$goods_services = [
			'Section' => [
				995 => 'Construction Services',
				996 => 'Distributive Trade Services ; Accomodation, Food & Beverage Service; Transport Services; Gas & Electricity Distribution Services',
				997 => 'Financial and related services; real estate services; and rental and leasing services',
				998 => 'Business and Production Services',
				999 => 'Community, Social & Personal Services and other miscellaneous services',
			],
			'Heading' => [
				9954 => 'Construction services',
				9961 => 'Services in wholesale trade',
				9962 => 'Services in retail trade',
				9963 => 'Accommodation, Food and beverage services',
				9964 => 'Passenger transport services',
				9965 => 'Goods Transport Services',
				9966 => 'Rental services of transport vehicles with or without operators',
				9967 => 'Supporting services in transport',
				9968 => 'Postal and courier services',
				9969 => 'Electricity, gas, water and other distribution services',
				9971 => 'Financial and related services',
				9972 => 'Real estate services',
				9973 => 'Leasing or rental services with or without operator',
				9981 => 'Research and development services',
				9982 => 'Legal and accounting services',
				9983 => 'Other professional, technical and business services',
				9984 => 'Telecommunications, broadcasting and information supply services',
				9985 => 'Support services',
				9986 => 'Support services to agriculture, hunting, forestry, fishing, mining and utilities.',
				9987 => 'Maintenance, repair and installation (except construction) services',
				9988 => 'Manufacturing services on physical inputs (goods) owned by others',
				9989 => 'Other manufacturing services; publishing, printing and reproduction services; materials recovery services',
				9991 => 'Public administration and other services provided to the community as a whole; compulsory social security services',
				9992 => 'Education services',
				9993 => 'Human health and social care services',
				9994 => 'Sewage and waste collection, treatment and disposal and other environmental protection services',
				9995 => 'Services of membership organizations',
				9996 => 'Recreational, cultural and sporting services',
				9997 => 'Other services',
				9998 => 'Domestic services',
				9999 => 'Services provided by extraterritorial organizations and bodies.',
			],
			'Group' => [
				99541 => 'Construction services of buildings',
				99542 => 'General construction services of civil engineering works',
				99543 => 'Site preparation services',
				99544 => 'Assembly and erection of prefabricated constructions',
				99545 => 'Special trade construction services',
				99546 => 'Installation services',
				99547 => 'Building completion and finishing services',
				99611 => '',
				99621 => '',
				99631 => 'Accommodation services',
				99632 => 'Other accommodation services',
				99633 => 'Food, edible preparations, alchoholic & non-alchocholic beverages serving services',
				99641 => 'Local transport and sightseeing transportation services of passengers',
				99642 => 'Long-distance transport services of passengers',
				99651 => 'Land transport services of Goods',
				99652 => 'Water transport services of goods',
				99653 => 'Air and space transport services of goods',
				99660 => 'Rental services of transport vehicles with or without operators',
				99671 => 'Cargo handling services',
				99672 => 'Storage and warehousing services',
				99673 => 'Supporting services for railway transport',
				99674 => 'Supporting services for road transport',
				99675 => 'Supporting services for water transport (coastal, transoceanic and inland waterways)',
				99676 => 'Supporting services for air or space transport',
				99679 => 'Other supporting transport services',
				99681 => 'Postal and courier services',
				99691 => 'Electricity and gas distribution services',
				99692 => 'Water distribution and other services',
				99711 => 'Financial services (except investment banking, insurance services and pension services)',
				99712 => 'Investment banking services ',
				99713 => 'Insurance and pension services (excluding reinsurance services)',
				99714 => 'Reinsurance services',
				99715 => 'Services auxiliary to financial services (other than to insurance and pensions)',
				99716 => 'Services auxillary to insurance and pensions',
				99717 => 'Services of holding financial assets',
				99721 => 'Real estate services involving owned or leased property',
				99722 => 'Real estate services on a fee/commission basis or contract basis',
				99731 => 'Leasing or rental services concerning machinery and equipment with or without operator',
				99732 => 'Leasing or rental services concerning other goods',
				99733 => 'Licensing services for the right to use intellectual property and similar products',
				99811 => 'Research and experimental development services in natural sciences and engineering.',
				99812 => 'Research and experimental development services in social sciences and humanities.',
				99813 => 'Interdisciplinary research services.',
				99814 => 'Research and development originals',
				99821 => 'Legal services',
				99822 => 'Accounting, auditing and bookkeeping services',
				99823 => 'Tax consultancy and preparation services',
				99824 => 'Insolvency and receivership services',
				99831 => 'Management consulting and management services; information technology services.',
				99832 => 'Architectural services, urban and land planning and landscape architectural services',
				99833 => 'Engineering services',
				99834 => 'Scientific and other technical services',
				99835 => 'Veterinary services',
				99836 => 'Advertising services and provision of advertising space or time.',
				99837 => 'Market research and public opinion polling services',
				99838 => 'Photography & Videography and their processing services',
				99839 => 'Other professional, technical and business services.',
				99841 => 'Telephony and other telecommunications services',
				99842 => 'Internet telecommunications services',
				99843 => 'On-line content services',
				99844 => 'News agency services',
				99845 => 'Library and archive services',
				99846 => 'Broadcasting, programming and programme distribution services',
				99851 => 'Employment services including personnel search/referral service & labour supply service',
				99852 => 'Investigation and security services',
				99853 => 'Cleaning services',
				99854 => 'Packaging services',
				99855 => 'Travel arrangement, tour operator and related services',
				99859 => 'Other support services',
				99861 => 'Support services to agriculture, hunting, forestry and fishing',
				99862 => 'Support services to mining',
				99863 => 'Support services to electricity, gas and water distribution',
				99871 => 'Maintenance and repair services of fabricated metal products, machinery and equipment',
				99872 => 'Repair services of other goods',
				99873 => 'Installation services (other than construction)',
				99881 => 'Food, beverage and tobacco manufacturing services',
				99882 => 'Textile, wearing apparel and leather manufacturing services',
				99883 => 'Wood and paper manufacturing services',
				99884 => 'Petroleum, chemical and pharmaceutical product manufacturing services',
				99885 => 'Rubber, plastic and other non-metallic mineral product manufacturing service',
				99886 => 'Basic metal manufacturing services',
				99887 => 'Fabricated metal product, machinery and equipment manufacturing services',
				99888 => 'Transport equipment manufacturing services',
				99889 => 'Other manufacturing services',
				99891 => 'Publishing, printing and reproduction services',
				99892 => 'Moulding, pressing, stamping, extruding and similar plastic manufacturing services',
				99893 => 'Casting, forging, stamping and similar metal manufacturing services',
				99894 => 'Materials recovery (recycling) services, on a fee or contract basis',
				99911 => 'Administrative services of the government',
				99912 => 'Public administrative services provided to the community as a whole',
				99913 => 'Administrative services related to compulsory social security schemes.',
				99921 => 'Pre-primary education services',
				99922 => 'Primary education services',
				99923 => 'Secondary Education Services',
				99924 => 'Higher education services',
				99925 => 'Specialised education services',
				99929 => 'Other education & training services and educational support services',
				99931 => 'Human health services',
				99932 => 'Residential care services for the elderly and disabled',
				99933 => 'Other social services with accommodation',
				99934 => 'Social services without accommodation for the elderly and disabled',
				99935 => 'Other social services without accommodation',
				99941 => 'Sewerage, sewage treatment and septic tank cleaning services',
				99942 => 'Waste collection services',
				99943 => 'Waste treatment and disposal services',
				99944 => 'Remediation services',
				99945 => 'Sanitation and similar services',
				99949 => 'Others',
				99951 => 'Services furnished by business, employers and professional organizations Services',
				99952 => 'Services furnished by trade unions',
				99959 => 'Services furnished by other membership organizations',
				99961 => 'Audiovisual and related services',
				99962 => 'Performing arts and other live entertainment event presentation and promotion services',
				99963 => 'Services of performing and other artists',
				99964 => 'Museum and preservation services',
				99965 => 'Sports and recreational sports services',
				99966 => 'Services of athletes and related support services',
				99969 => 'Other amusement and recreational services',
				99971 => 'Washing, cleaning and dyeing services',
				99972 => 'Beauty and physical well-being services',
				99973 => 'Funeral, cremation and undertaking services',
				99979 => 'Other miscellaneous services',
				99980 => 'Domestic services',
				99990 => 'Services provided by extraterritorial organizations and bodies.',
			],
		];
	}

	function pdf_header($pdf, $company, $city, $state, $letterhead) {
		$border = 0;
		$width  = $pdf->GetPageWidth() - 20;
		
		if ($letterhead) {
			$pdf->Image(FCPATH.'php_uploads/' . $company['logo'], 0, 5, 70, 30, 'png', '', 'M', true, 300, 'L', false, false, $border, true, false, false, false);

			$pdf->SetFontSize(12, true);
			$pdf->MultiCell(190, 5, $company['name'], $border, 'R', false, 1, 10, 10, true, 0, false, false, 0, 'T', false);
			
			$pdf->SetFont('', '', 8);
			$pdf->MultiCell(190, 10, $company['address'] . (isset($city['name']) ? ",\n" . $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] : ''), $border, 'R', false, 1, 10, $pdf->GetY(), true, 0, false, true, 10, 'M', true);
			$pdf->MultiCell(190, 4, $company['contact'] . ' | ' . $company['email'], $border, 'R', false, 1, 10, $pdf->GetY(), true, 0, false, true, 4, 'T', true);
		}
		else {
			$pdf->MultiCell(190, 12, '', $border, 'R', false, 1, 10, $pdf->GetY(), true, 0, false, true, 12, 'T', true);
		}
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
		$pdf->Cell(60, 5, $company['cin_no'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

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

	function simple_invoice($pdf, $data, $letterhead) {

		extract($data);

 		$width = $pdf->GetPageWidth() - 20;
 		$pdf->AddPage();

 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);

 		if(!empty($voucher))
 		{
 			$id2_format='';
 			if(isset($voucher['id2_format'])){
				$id2_format=$voucher['id2_format'];
			}
			$party_site_id='';
	 		if(isset($voucher['party_site_id'])){
					$party_site_id=$voucher['party_site_id'];
				}

			$party_site_id='';
	 		if(isset($voucher['party_site_id'])){
					$party_site_id=$voucher['party_site_id'];
				}
			
			$debit_gst_no='';
 			if(isset($voucher['debit_gst_no'])){
				$debit_gst_no=$voucher['debit_gst_no'];
			}

			$debit_pan_no='';
 			if(isset($voucher['debit_pan_no'])){
				$debit_pan_no=$voucher['debit_pan_no'];
			}

			$place_of_supply='';
			if(isset($voucher['place_of_supply'])){
				$place_of_supply=$voucher['place_of_supply'];
			}

			$bl_sb='';
			if(isset($voucher['bl_sb'])){
				$bl_sb=$voucher['bl_sb'];
			}

			$narration='';
			if(isset($voucher['narration'])){
				$narration=$voucher['narration'];
			}

 		}
		 		
 		$pdf->SetFillColor(230);
		$pdf->SetFont('', 'B', 14);
		$pdf->Cell($width, 10, $page_title, 0, 1, 'C', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, 'To', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Invoice No.', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Date', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 10);
		$pdf->Cell(100, 5, $voucher['debit_party_name'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 8);
		
		$pdf->Cell(45, 5, $voucher['idkaabar_code'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, $voucher['date'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

		if ($party_site_id > 0) {
			$y = $pdf->GetY();
			$pdf->SetFont('', '', 8);
			$pdf->MultiCell(100, 8.6, str_replace('<br />', "\n", $voucher['ps_address']."\n".$ps_city), 'LB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 8.6, 'T', true);
			$pdf->Cell(45, 8.6, '', 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 8.6, '', 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetTextColor(60,60,60);
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, 3, 'GSTIN', 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(50, 3, 'PAN No', 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'State Code', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'Place of Supply of Service', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('', 'B', 8);
			$pdf->Cell(50, 5, $voucher['ps_gst_no'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(50, 5, $voucher['debit_pan_no'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, substr($voucher['ps_gst_no'], 0, 2), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $place_of_supply, 'R', 1, 'L', 0, false, 1, false, 'T', 'C');
		}
		else {
			$y = $pdf->GetY();
			$pdf->SetFont('', '', 8);
			$pdf->MultiCell(100, 8.6, str_replace('<br />', "\n", $voucher['debit_party_address']), 'LB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 8.6, 'T', true);
			$pdf->Cell(45, 8.6, '', 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 8.6, '', 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetTextColor(60,60,60);
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, 3, 'GSTIN', 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(50, 3, 'PAN No', 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'State Code', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'Place of Supply of Service', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('', 'B', 8);

			

			$pdf->Cell(50, 5, $debit_gst_no, 'L', 0, 'L', 0, false, 1, false, 'T', 'C');

			

			$pdf->Cell(50, 5, $debit_pan_no, 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, substr($debit_gst_no, 0, 2), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $place_of_supply, 'R', 1, 'L', 0, false, 1, false, 'T', 'C');
		}

		if (strlen($bl_sb) > 0) {
			$pdf->SetTextColor(60,60,60);
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, 3, 'Vessel', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(50, 3, 'BL No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'Pieces', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'CBM', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('', 'B', 8);
			$pdf->Cell(50, 5, $vessel['prefix'] . ' ' . $vessel['name'] . ' ' . $vessel['voyage_no'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(50, 5, $voucher['bl_sb'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $voucher['packages'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $voucher['nett_weight'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');
		}

		$pdf->SetFont('', '', 8);
		$pdf->Cell(10, 5, 'No.', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(130, 5, 'Particulars', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, 'SAC / HSN', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, 'INR Amount', 'TRB', 1, 'C', 1, false, 1, false, 'T', 'C');
		$i = 1;
		$x = 10;
		$total = [
			'amount'     => 0,
			'tax_amount' => 0,
			'cgst'       => 0,
			'sgst'       => 0,
			'igst'       => 0,
			'total'      => 0,
		];
		$gst_tax = [];
		$i              = 1;
		$print_subtotal = 1;
		
		foreach ($voucher_details as $r) {
			if ($r['amount'] == 0) continue;

			if ($pdf->GetY() > 220) {
				$pdf->Cell(160, 5, 'Page Total', 'LR', 0, 'R', 1, false, 1, false, 'T', 'C');
				$pdf->Cell(30, 5, inr_format($total['amount']), 'LR', 1, 'R', 1, false, 1, false, 'T', 'C');

				$height = 220 - $pdf->GetY();
				$pdf->MultiCell($width, $height, '', 'LR', 'L', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, $height, 'T', true);

				$this->pdf_invoice_footer($pdf, $voucher['id'], $company, $inr, $foreign, true);

				$pdf->AddPage();

				$this->pdf_header($pdf, $company, $city, $state, $letterhead);

		 		$pdf->SetFillColor(230);
				$pdf->SetY(30);

				$pdf->SetFont('', 'B', 14);
				$pdf->Cell($width, 5, $page_title, 0, 1, 'C', 0, false, 1, false, 'T', 'C');

				$pdf->SetTextColor(60,60,60);
				$pdf->SetFont('', '', 8);
				$pdf->Cell(100, 3, 'To', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 3, $page_title.' No.', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 3, 'Date', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(20, 3, 'Type', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');

				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('', '', 10);
				$pdf->Cell(100, 5, $voucher['debit_party_name'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->SetFont('', '', 8);
				$pdf->Cell(45, 5, $voucher['id2_format'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 5, $voucher['date'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(20, 5, $job['cargo_type'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');
				
				$y = $pdf->GetY();
				$pdf->MultiCell(100, 8.6, str_replace('<br />', "\n", $voucher['debit_party_address']), 'LB', 'L', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 8.6, 'T', true);

				$pdf->SetXY(110, $y);
				$pdf->SetTextColor(60,60,60);
				$pdf->Cell(45, 3, 'Port of Shipment', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 3, 'Port of Discharge', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

				$pdf->SetTextColor(0,0,0);
				$pdf->SetX(110);
				$pdf->Cell(45, 5, $shipment_port, 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 5, $discharge_port, 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

				$pdf->SetTextColor(60,60,60);
				$pdf->Cell(100, 3, (isset($hss_buyer['name']) ? 'HSS Buyer' : 'Importer'), 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(90, 3, 'Vessel Name', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->SetTextColor(0,0,0);
				$pdf->Cell(100, 5, (isset($hss_buyer['name']) ? $hss_buyer['name'] : $party['name']), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(90, 5, (isset($vessel['name']) ? $vessel['name'] . ' - ' . $vessel['voyage_no'] : ' '), 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

				$pdf->SetXY(10,$pdf->GetY());
				$pdf->SetTextColor(60,60,60);
				$pdf->Cell(100, 3, (isset($hss_buyer['name']) ? 'Importer' : 'HSS Buyer'), 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 3, 'B/L No & Date', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 3, 'B/E No & Date', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
				
				$pdf->SetTextColor(0,0,0);
				$pdf->Cell(100, 5, (isset($hss_buyer['name']) ? $party['name'] : ''), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 5, $job['bl_no'] . ' / ' . $job['bl_date'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 5, $job['be_no'] . ' / ' . $job['be_date'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

				$pdf->SetXY(10,$pdf->GetY());
				$pdf->SetTextColor(60,60,60);
				$pdf->Cell(100, 3, 'Description of Goods', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');

				if ($job['cargo_type'] == 'Bulk') {
					$pdf->Cell(45, 3, $job['package_unit'], 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(45, 3, $job['nett_weight_unit'], 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
				}
				else {
					$pdf->Cell(20, 3, $job['package_unit'], 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(25, 3, $job['nett_weight_unit'], 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');	
					$pdf->Cell(45, 3, 'Containers', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');	
				}

				$pdf->SetTextColor(0,0,0);
				$pdf->Cell(100, 5, $voucher['product_details'], 'LRB', 0, 'L', 0, false, 1, false, 'T', 'C');

				if ($job['cargo_type'] == 'Bulk') {
					$pdf->Cell(45, 5, $voucher['packages'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(45, 5, $voucher['nett_weight'], 'RB', 1, 'L', 0, false, 1, false, 'T', 'C');
				}
				else {
					$pdf->Cell(20, 5, $voucher['packages'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(25, 5, ($job['nett_weight_unit'] != 'CBM' ? $voucher['nett_weight'] : ''), 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(45, 5, implode(', ', $container_count), 'RB', 1, 'L', 0, false, 1, false, 'T', 'C');
				}

				$pdf->Cell(10, 5, 'No.', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
				$pdf->Cell(130, 5, 'Particulars', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 5, 'SAC / HSN', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 5, 'Amount', 'TRB', 1, 'C', 1, false, 1, false, 'T', 'C');
			}
			
			if ($r['gst'] == 'No') {
				$cgst   = bcdiv(bcmul($r['amount'], $r['cgst'], 2), 100, 2);
				$sgst   = bcdiv(bcmul($r['amount'], $r['sgst'], 2), 100, 2);
				$igst   = bcdiv(bcmul($r['amount'], $r['igst'], 2), 100, 2);
				$amount = round($r['amount'] + $cgst + $sgst + $igst);

				$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
				$total['cgst']   = bcadd($total['cgst'], $cgst, 2);
				$total['sgst']   = bcadd($total['sgst'], $sgst, 2);
				$total['igst']   = bcadd($total['igst'], $igst, 2);
				$total['total']  = bcadd($total['total'], $amount, 2);

				if (isset($gst_tax[$r['sac_hsn']]['cgst'])) {
					$gst_tax[$r['sac_hsn']]['cgst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['cgst']['tax']    += $cgst;
					$gst_tax[$r['sac_hsn']]['sgst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['sgst']['tax']    += $sgst;
					$gst_tax[$r['sac_hsn']]['igst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['igst']['tax']    += $igst;
				}
				else {
					$gst_tax[$r['sac_hsn']]['cgst'] = ['rate' => $r['cgst'], 'amount' => $r['amount'], 'tax' => $cgst];
					$gst_tax[$r['sac_hsn']]['sgst'] = ['rate' => $r['sgst'], 'amount' => $r['amount'], 'tax' => $sgst];
					$gst_tax[$r['sac_hsn']]['igst'] = ['rate' => $r['igst'], 'amount' => $r['amount'], 'tax' => $igst];
				}

				$pdf->Cell(10, 5, $i++, 'LRB', 0, 'C', 0, false, 1, false, 'T', 'C');
				if ($r['currency_id'] > 1) {
					$pdf->Cell(110, 5, $r['particulars'] , 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(20, 5, $r['currency_code'] . ' ' . inr_format($r['currency_amount']) , 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(25, 5, ($r['sac_hsn'] == 0 ? '-' : $r['sac_hsn']), 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(25, 5, inr_format($r['amount']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
				}
				else {
					$pdf->Cell(130, 5, $r['particulars'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(25, 5, ($r['sac_hsn'] == 0 ? '-' : $r['sac_hsn']), 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
					$pdf->Cell(25, 5, inr_format($r['amount']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
				}
			}
		}

		$height = 210 - $pdf->GetY() - ((strlen($narration) > 0 OR strlen($voucher['remarks']) > 0) ? 35 : 0);
		
		if ($height < 0) {
			$pdf->Cell(160, 5, 'Page Total', 'LR', 0, 'R', 1, false, 1, false, 'T', 'C');
			$pdf->Cell(30, 5, inr_format($total['amount']), 'LR', 1, 'R', 1, false, 1, false, 'T', 'C');

			$height = 210 - $pdf->GetY();
			$pdf->MultiCell($width, $height, '', 'LR', 'L', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, $height, 'T', true);

			$this->pdf_invoice_footer($pdf, $voucher['id'], $company, $inr, $foreign, true);

			$pdf->AddPage();

			$this->pdf_header($pdf, $company, $city, $state, $letterhead);

	 		$pdf->SetFillColor(230);
			$pdf->SetY(30);

			$pdf->SetFont('', 'B', 14);
			$pdf->Cell($width, 5, $page_title, 0, 1, 'C', 0, false, 1, false, 'T', 'C');

			$pdf->SetTextColor(60,60,60);
			$pdf->SetFont('', '', 8);
			$pdf->Cell(100, 3, 'To', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, $page_title.' No.', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 3, 'Date', 'LT', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(20, 3, 'Type', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('', '', 10);
			$pdf->Cell(100, 5, $voucher['debit_party_name'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->SetFont('', '', 8);
			$pdf->Cell(45, 5, $voucher['id2_format'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, $voucher['date'], 'L', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(20, 5, $job['cargo_type'], 'LR', 1, 'L', 0, false, 1, false, 'T', 'C');
			
			$y = $pdf->GetY();
			$pdf->MultiCell(100, 8.6, str_replace('<br />', "\n", $voucher['debit_party_address']), 'LB', 'L', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 8.6, 'T', true);

			$pdf->SetXY(110, $y);
			$pdf->SetTextColor(60,60,60);
			$pdf->Cell(45, 3, 'Port of Shipment', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'Port of Discharge', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetTextColor(0,0,0);
			$pdf->SetX(110);
			$pdf->Cell(45, 5, $shipment_port, 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $discharge_port, 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetTextColor(60,60,60);
			$pdf->Cell(100, 3, (isset($hss_buyer['name']) ? 'HSS Buyer' : 'Importer'), 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(90, 3, 'Vessel Name', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(100, 5, (isset($hss_buyer['name']) ? $hss_buyer['name'] : $party['name']), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(90, 5, (isset($vessel['name']) ? $vessel['name'] . ' - ' . $vessel['voyage_no'] : ' '), 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetXY(10, $pdf->GetY());
			$pdf->SetTextColor(60,60,60);
			$pdf->Cell(100, 3, (isset($hss_buyer['name']) ? 'Importer' : 'HSS Buyer'), 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'B/L No & Date', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 3, 'B/E No & Date', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
			
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(100, 5, (isset($hss_buyer['name']) ? $party['name'] : ''), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $job['bl_no'] . ' / ' . $job['bl_date'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(45, 5, $job['be_no'] . ' / ' . $job['be_date'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

			$pdf->SetXY(10,$pdf->GetY());
			$pdf->SetTextColor(60,60,60);
			$pdf->Cell(100, 3, 'Description of Goods', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');

			if ($job['cargo_type'] == 'Bulk') {
				$pdf->Cell(45, 3, $job['package_unit'], 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 3, $job['nett_weight_unit'], 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
			}
			else {
				$pdf->Cell(20, 3, $job['package_unit'], 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 3, $job['nett_weight_unit'], 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');	
				$pdf->Cell(45, 3, 'Containers', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');	
			}

			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(100, 5, $voucher['product_details'], 'LRB', 0, 'L', 0, false, 1, false, 'T', 'C');

			if ($job['cargo_type'] == 'Bulk') {
				$pdf->Cell(45, 5, $voucher['packages'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 5, $voucher['nett_weight'], 'RB', 1, 'L', 0, false, 1, false, 'T', 'C');
			}
			else {
				$pdf->Cell(20, 5, $voucher['packages'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 5, ($job['nett_weight_unit'] != 'CBM' ? $voucher['nett_weight'] : ''), 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(45, 5, implode(', ', $container_count), 'RB', 1, 'L', 0, false, 1, false, 'T', 'C');
			}

			$pdf->Cell(10, 5, 'No.', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
			$pdf->Cell(150, 5, 'Particulars', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
			$pdf->Cell(30, 5, 'Amount', 'TRB', 1, 'C', 1, false, 1, false, 'T', 'C');
		}


		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(165, 5, 'Sub Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['amount']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');

		if ($total['cgst'] > 0 AND $total['sgst'] > 0 OR $total['igst'] > 0) {
			$pdf->SetFillColor(230);
			$pdf->SetTextColor(84,84,84);
			$pdf->SetFont('', 'B', 8);
			$pdf->Cell(15, 10, 'SAC/HSN', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(60, 10, 'Description of Service', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 10, 'Taxable', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->Cell(25, 5, 'CGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 5, 'SGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 5, 'IGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 10, 'Total', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');
			
			$pdf->SetXY($x, $y+5);
			$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 5, 'Amount', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('', '', 8);
			$total['cgst'] = 0;
			$total['sgst'] = 0;
			$total['igst'] = 0;
			foreach ($gst_tax as $code => $taxes) {
				$desc = $this->idex->getField('goods_services', $code);

				$taxes['cgst']['tax'] = round($taxes['cgst']['amount'] * $taxes['cgst']['rate'] / 100);
				$taxes['sgst']['tax'] = round($taxes['sgst']['amount'] * $taxes['sgst']['rate'] / 100);
				$taxes['igst']['tax'] = round($taxes['igst']['amount'] * $taxes['igst']['rate'] / 100);

				$total['cgst'] += $taxes['cgst']['tax'];
				$total['sgst'] += $taxes['sgst']['tax'];
				$total['igst'] += $taxes['igst']['tax'];

				$pdf->Cell(15, 7, $code, 'LB', 0, 'L', 0, '', 1, false, 'T', 'M');
				$pdf->MultiCell(60, 7, $desc, 'LB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 7, 'M', true);
				$pdf->Cell(15, 7, inr_format($taxes['cgst']['amount']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(10, 7, $taxes['cgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(15, 7, inr_format($taxes['cgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(10, 7, $taxes['sgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(15, 7, inr_format($taxes['sgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(10, 7, $taxes['igst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(15, 7, inr_format($taxes['igst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(25, 7, inr_format($taxes['cgst']['amount'] + $taxes['cgst']['tax'] + $taxes['sgst']['tax'] + $taxes['igst']['tax']), 'LBR', 1, 'R', 0, '', 1, false, 'T', 'M');
			}

			$pdf->SetFont('', 'B', 8);
			$pdf->Cell(75, 5, 'Tax Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, inr_format($total['amount']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($total['cgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($total['sgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($total['igst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, '', 'RB', 1, 'R', 1, false, 1, false, 'T', 'C');

		}
		$total['total'] = round($total['amount'] + $total['cgst'] + $total['sgst'] + $total['igst']);

		$pdf->SetFont('', '', 10);
		$pdf->Cell(40, 8, 'Total Amount (in words): ', 'LB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(125, 8, 'INR '.numberToWords($total['total']), 'B', 0, 'L', 0, false, 1, false, 'T', 'C');
		// $pdf->MultiCell(25, 8, 'INR '.numberToWords($total['total']), 'LRB', 'L', false, 1, '', '', true, 0, false, false, 0, 'M', false);
		$ITFRupeeRegular = TCPDF_FONTS::addTTFfont(FCPATH.'vendor/tecnickcom/tcpdf/fonts/ITFRupeeRegular.ttf', 'ITFRupeeRegular', '', 96);

		$pdf->SetFont($ITFRupeeRegular, 'B', 14);
		$pdf->Cell(5, 8, 'D', 'B', 0, 'R', 0, false, 1, false, 'T', 'M');
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(20, 8, inr_format($total['total']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');


		$pdf->SetFont('', '', 8);
		$height = 210 - $pdf->GetY() - ((strlen($narration) > 0 OR strlen($voucher['remarks']) > 0) ? 35 : 0);

		if (strlen($narration) > 0 OR strlen($voucher['remarks']) > 0) {
			$pdf->MultiCell(90, $height, $voucher['narration'], 'LTB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, $height, 'C', true);
			$pdf->SetFont('courier');
			$pdf->MultiCell(100, $height, $voucher['remarks'], 'LTRB', 'L', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, $height, 'C', true);
		}
		else {
			$pdf->MultiCell($width, $height, '', 'LTRB', 'L', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, $height, 'C', true);
		}
		$pdf->SetFont('times');
		
		$inr='';
		if(isset($inr)){
			$inr=$inr;
		}

		$foreign='';
		if(isset($foreign)){
			$foreign=$foreign;
		}

		$this->pdf_invoice_footer($pdf, $voucher['id'], $company, $inr, $foreign);
	}

	function import_invoice($pdf, $data, $letterhead) {

		extract($data);

		$width = $pdf->GetPageWidth() - 20;
 		$pdf->AddPage();

 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);

 		$pdf->Ln();
		$pdf->SetFont('', 'B', 14);
		$pdf->MultiCell(190, 10, $page_title, $border, 'C', false, 1, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 10, 'M', true);

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, 'To', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, humanize($invoice_type).' No', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(22, 3, 'Date', 'TR', 0, 'TR', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 3, 'Job No', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(100, 13, $voucher['debit_party_name']. "\n". $voucher['debit_party_address'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 0, 'T', false);
		$pdf->Cell(45, 5, $voucher['id2_format'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(22, 5, $voucher['date'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 5, $job['id2_format'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetXY(110, $pdf->GetY());
		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(45, 3, 'Port of Discharge', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Port of Shipment', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetXY(110, $pdf->GetY());
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(45, 5, $discharge_port, 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, $shipment_port, 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(50, 3, 'GST No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'PAN No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'State Code', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Place of Supply', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(50, 5, $voucher['debit_gst_no'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 5, $voucher['debit_pan_no'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, substr($voucher['debit_gst_no'], 0, 2), 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, $voucher['place_of_supply'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, 'Shipper', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Vessel Name', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Exchange Rate', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(100, 5, (strlen($job['vi_shipper_name']) > 0 ? $job['vi_shipper_name'] : $this->idex->getField('agents', $job['shipper_id'])), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, (isset($vessel['name']) ? $vessel['name'] . ' - ' . $vessel['voyage_no'] : ' '), 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, ($voucher['exchange_rate'] > 1 ? $voucher['exchange_rate'] : ''), 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, (($job['type'] == 'Import') ? 'Importer' : 'Exporter'), 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'B/E No & Date', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'B/L No & Date', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(100, 5, $party['name'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, ($job['be_date'] == '00-00-0000' ? '' : $job['be_no'] . ' / ' . $job['be_date']), 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, ($job['bl_date'] == '00-00-0000' ? '' : $job['bl_no'] . ' / ' . $job['bl_date']), 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, 'Description of Goods', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, $package_type, 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, $job['net_weight_unit'], 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(100, 5, $job['details'], 'LRB', 'L', false, 0, '', '', true, 0, false, false, 0, 'M', false);
		$pdf->Cell(45, 5, $job['packages'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, $job['net_weight'], 'RB', 1, 'L', 0, false, 1, false, 'T', 'C');

		if ($job['cargo_type'] == 'Container') {
			$container_nos = array();
			$container_type = array();
			foreach ($containers as $r) {
				$container_nos[] = $r['number'];
				$container_types[$r['container_type_id']] = $r['size'];
				if (isset($container_type[$r['container_type_id']]))
					$container_type[$r['container_type_id']]++;
				else
					$container_type[$r['container_type_id']] = 1;
			}
			$container_count = [];
			foreach ($container_type as $ctid => $count) {
				$container_count[] = $count.' x '.$container_types[$ctid];
			}

			$pdf->SetFont('', '', 8);
			$pdf->Cell(190, 3, 'Containers (' . implode(', ', $container_count) . ')', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->SetFont('', 'B', 8);
			$pdf->MultiCell(190, 5,implode(', ', $container_nos), 'LRB', 'L', false, 1, '', '', true, 0, false, false, 0, 'M', false);
		}


		$pdf->SetFillColor(230);
		$pdf->SetTextColor(84,84,84);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(10, 5, 'No', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(110, 5, 'Particulars', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'SAC / HSN', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Qty', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Rate', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, 'INR Amount', 'TRB', 1, 'C', 1, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$total = [
			'amount'     => 0,
			'tax_amount' => 0,
			'cgst'       => 0,
			'sgst'       => 0,
			'igst'       => 0,
			'total'      => 0,
		];
		$remarks = '';
		$i       = 1;
		$gst_tax = [];
		foreach ($voucher_details as $r) {
			if ($r['gst'] == 'No') {
				$cgst   = bcdiv(bcmul($r['amount'], $r['cgst'], 2), 100, 2);
				$sgst   = bcdiv(bcmul($r['amount'], $r['sgst'], 2), 100, 2);
				$igst   = bcdiv(bcmul($r['amount'], $r['igst'], 2), 100, 2);
				$amount = round($r['amount'] + $cgst + $sgst + $igst);

				$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
				// $total['cgst']   = bcadd($total['cgst'], $cgst, 2);
				// $total['sgst']   = bcadd($total['sgst'], $sgst, 2);
				// $total['igst']   = bcadd($total['igst'], $igst, 2);
				// $total['total']  = bcadd($total['total'], $amount, 2);

				if (isset($gst_tax[$r['sac_hsn']]['cgst'])) {
					$gst_tax[$r['sac_hsn']]['cgst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['cgst']['tax']    += $cgst;
					$gst_tax[$r['sac_hsn']]['sgst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['sgst']['tax']    += $sgst;
					$gst_tax[$r['sac_hsn']]['igst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['igst']['tax']    += $igst;
				}
				else {
					$gst_tax[$r['sac_hsn']]['cgst'] = ['rate' => $r['cgst'], 'amount' => $r['amount'], 'tax' => $cgst];
					$gst_tax[$r['sac_hsn']]['sgst'] = ['rate' => $r['sgst'], 'amount' => $r['amount'], 'tax' => $sgst];
					$gst_tax[$r['sac_hsn']]['igst'] = ['rate' => $r['igst'], 'amount' => $r['amount'], 'tax' => $igst];
				}

				$pdf->Cell(10, 5,  $i++, 'LRB', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(110, 5, $r['particulars'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(15, 5, $r['sac_hsn'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(15, 5, $r['units'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(15, 5, $r['rate'], 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 5, inr_format($r['amount'], 2, '.', ''), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
			}
		}

		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(135, 5, 'Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, '', 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, '', 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['amount']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');


		$pdf->SetFillColor(230);
		$pdf->SetTextColor(84,84,84);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(15, 10, 'SAC/HSN', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(60, 10, 'Description of Service', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 10, 'Taxable', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Cell(25, 5, 'CGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(25, 5, 'SGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(25, 5, 'IGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(25, 10, 'Total', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');
		
		$pdf->SetXY($x, $y+5);
		$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 5, 'Amount', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		foreach ($gst_tax as $code => $taxes) {
			$desc = $this->idex->getField('goods_services', $code);

			$taxes['cgst']['tax'] = round($taxes['cgst']['amount'] * $taxes['cgst']['rate'] / 100);
			$taxes['sgst']['tax'] = round($taxes['sgst']['amount'] * $taxes['sgst']['rate'] / 100);
			$taxes['igst']['tax'] = round($taxes['igst']['amount'] * $taxes['igst']['rate'] / 100);

			$total['cgst'] += $taxes['cgst']['tax'];
			$total['sgst'] += $taxes['sgst']['tax'];
			$total['igst'] += $taxes['igst']['tax'];

			$pdf->Cell(15, 7, $code, 'LB', 0, 'L', 0, '', 1, false, 'T', 'M');
			$pdf->MultiCell(60, 7, $desc, 'LB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 7, 'M', true);
			$pdf->Cell(15, 7, inr_format($taxes['cgst']['amount']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 7, $taxes['cgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 7, inr_format($taxes['cgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 7, $taxes['sgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 7, inr_format($taxes['sgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 7, $taxes['igst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 7, inr_format($taxes['igst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 7, inr_format($taxes['cgst']['amount'] + $taxes['cgst']['tax'] + $taxes['sgst']['tax'] + $taxes['igst']['tax']), 'LBR', 1, 'R', 0, '', 1, false, 'T', 'M');
		}

		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(75, 5, 'Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, inr_format($total['amount']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['cgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['sgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['igst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, '', 'RB', 1, 'R', 1, false, 1, false, 'T', 'C');

		$total['total'] = round($total['amount'] + $total['cgst'] + $total['sgst'] + $total['igst']);

		$pdf->SetFont('', '', 10);
		$pdf->Cell(40, 8, 'Total Amount (in words): ', 'LB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(125, 8, numberToWords($total['total']), 'B', 0, 'L', 0, false, 1, false, 'T', 'C');
		// $pdf->MultiCell(25, 8, numberToWords($total['total']), 'LRB', 'L', false, 1, '', '', true, 0, false, false, 0, 'M', false);
		$pdf->Cell(25, 8, inr_format($total['total']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetFont('', '', 8);
		$h = 230-$pdf->GetY();
		$pdf->MultiCell($width, $h, $voucher['narration'], 'LR', 'L', false, 1, '', '', true, 0, false, true, $h, 'B', true);

		// $pdf->SetY(230);
 		$this->pdf_invoice_footer($pdf, $voucher['id'], $company, true);
	}

	function export_invoice($pdf, $data, $letterhead) {

		extract($data);

		$width = $pdf->GetPageWidth() - 20;
 		$pdf->AddPage();
 		
 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);

 		$pdf->Ln();
		$pdf->SetFont('', 'B', 14);
		$pdf->Cell($width, 10, $page_title, 0, 1, 'C', 0, false, 1, false, 'T', 'C');

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, 'To', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, humanize($invoice_type).' No', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(22, 3, 'Date', 'TR', 0, 'TR', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 3, 'Job No', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(100, 22, $voucher['debit_party_name']. "\n". $voucher['debit_party_address'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 0, 'T', false);
		$pdf->Cell(45, 5, $voucher['idkaabar_code'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(22, 5, $voucher['date'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 5, $job['idkaabar_code'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetXY(110, $pdf->GetY());
		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(45, 3, 'Port of Loading', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(22, 3, 'Dest. Port', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 3, 'Dest. Country', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetXY(110, $pdf->GetY());
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(45, 5, $job['clearance_port'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(22, 5, $job['pod_name'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 5, $job['fpod_country'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->setXY(110,$pdf->getY());
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(45, 3, 'Vessel Name', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Description of Goods', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->setXY(110,$pdf->getY());
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(45, 5, (isset($job['vessel_name']) ? $job['vessel_name'] . ' - ' . $job['vessel_voyage'] : ' '), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, $job['item_description'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetDrawColor(100,100,100);
		$pdf->SetTextColor(60,60,60);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(50, 3, 'GST No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'PAN No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'State Code', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'Place of Supply', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(50, 5, $voucher['ledger']['gst_no'], 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 5, substr($voucher['ledger']['gst_no'], 2, -3), 'LR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, substr($voucher['ledger']['gst_no'], 0, 2), 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, '', 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(100, 3, 'Exporter', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'S/B No & Date', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'B/L No & Date', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');
		

		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(100, 13, $job['shipper_name'] ."\n". $job['shipper_address'], 'LR', 'L', false, 0, '', '', true, 0, true, false, 0, 'M', false);
		$pdf->Cell(45, 5, $job['sb_no'].' / '.$job['sb_date'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 5, ((strlen(trim($job['hbl_no'])) > 0) ? $job['hbl_no'] . ' / ' . $job['hbl_date'] : ' '), 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->setXY(110,$pdf->getY());
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(67, 3, 'Party Invoice No', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 3, 'Ex. Rate', 'TR', 1, 'L', 0, false, 1, false, 'T', 'C');

		$pdf->setXY(110,$pdf->getY());
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(67, 5, $job['invoice_no'].' / '.$job['invoice_date'], 'R', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(23, 5, $voucher['exchange_rate'], 'R', 1, 'L', 0, false, 1, false, 'T', 'C');

		if ($job['cargo_type'] == 'Container') {
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

			$pdf->SetFont('', '', 8);
			$pdf->Cell(190, 3, 'Containers', 'LTR', 1, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->SetFont('', 'B', 8);

			$pdf->MultiCell(190, 5, $cont_2040."\n".implode(', ', $container_numbers), 'LRB', 'L', false, 1, '', '', true, 0, false, false, 0, 'M', false);
		}
		
		$pdf->SetFillColor(230);
		$pdf->SetTextColor(84,84,84);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(10, 5, 'No', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(100, 5, 'Particulars', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'SAC / HSN', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(10, 5, 'Qty', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Rate', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'USD Amount', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, 'INR Amount', 'TRB', 1, 'C', 1, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$total = [
			'amount'     => 0,
			'tax_amount' => 0,
			'cgst'       => 0,
			'sgst'       => 0,
			'igst'       => 0,
			'total'      => 0,
		];
		$remarks = '';
		$i       = 1;
		$gst_tax = [];

		// echo "<pre>";
		// print_r($voucher_details);
		// exit;
		foreach ($voucher_details as $r) {

			
			$r['cgst_amount'] = $r['cgst'];
			$r['sgst_amount'] = $r['sgst'];
			$r['igst_amount'] = $r['igst'];

			unset($r['cgst']);
			unset($r['sgst']);
			unset($r['igst']);

			$vchcode = substr($voucher['ledger']['gst_no'], 0, 2);
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

			$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
			$total['cgst']   = bcadd($total['cgst'], $cgst, 2);
			$total['sgst']   = bcadd($total['sgst'], $sgst, 2);
			$total['igst']   = bcadd($total['igst'], $igst, 2);
			$total['total']  = bcadd($total['total'], $amount, 2);

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

			$pdf->Cell(10, 5,  $i++, 'LRB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(100, 5, $r['particulars'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['hsn_code'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(10, 5, $unit['code'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['rate'], 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['currency_amt'], 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($r['amount'], 2, '.', ''), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		}


		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(135, 5, 'Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, '', 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, '', 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['amount']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');

		$pdf->SetFillColor(230);
		$pdf->SetTextColor(84,84,84);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(15, 10, 'SAC/HSN', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(60, 10, 'Description of Service', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 10, 'Taxable', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Cell(25, 5, 'CGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(25, 5, 'SGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(25, 5, 'IGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(25, 10, 'Total', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');
		
		$pdf->SetXY($x, $y+5);
		$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
		$pdf->Cell(15, 5, 'Amount', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$total['cgst'] = 0;
		$total['sgst'] = 0;
		$total['igst'] = 0;

		foreach ($gst_tax as $code => $taxes) {
			$desc = $this->kaabar->getField('goods_services', $code, 'sac_hsn');

			
			$taxes['cgst']['tax'] = round($taxes['cgst']['amount'] * $taxes['cgst']['rate'] / 100);
			$taxes['sgst']['tax'] = round($taxes['sgst']['amount'] * $taxes['sgst']['rate'] / 100);
			$taxes['igst']['tax'] = round($taxes['igst']['amount'] * $taxes['igst']['rate'] / 100);


			$total['cgst'] += $taxes['cgst']['tax'];
			$total['sgst'] += $taxes['sgst']['tax'];
			$total['igst'] += $taxes['igst']['tax'];

			$pdf->Cell(15, 7, $code, 'LB', 0, 'L', 0, '', 1, false, 'T', 'M');
			$pdf->MultiCell(60, 7, $desc, 'LB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 7, 'M', true);
			$pdf->Cell(15, 7, inr_format($taxes['cgst']['amount']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 7, $taxes['cgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 7, inr_format($taxes['cgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 7, $taxes['sgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 7, inr_format($taxes['sgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 7, $taxes['igst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 7, inr_format($taxes['igst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 7, inr_format($taxes['cgst']['amount'] + $taxes['cgst']['tax'] + $taxes['sgst']['tax'] + $taxes['igst']['tax']), 'LBR', 1, 'R', 0, '', 1, false, 'T', 'M');
		}


		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(75, 5, 'Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, inr_format($total['amount']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['cgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['sgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['igst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, '', 'RB', 1, 'R', 1, false, 1, false, 'T', 'C');

		$total['total'] = round($total['amount'] + $total['cgst'] + $total['sgst'] + $total['igst']);

		$pdf->SetFont('', '', 10);
		$pdf->Cell(40, 8, 'Total Amount (in words): ', 'LB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(125, 8, numberToWords($total['total']), 'B', 0, 'L', 0, false, 1, false, 'T', 'C');
		// $pdf->MultiCell(25, 8, numberToWords($total['total']), 'LRB', 'L', false, 1, '', '', true, 0, false, false, 0, 'M', false);
		$pdf->Cell(25, 8, inr_format($total['total']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
		
		$pdf->SetFont('', '', 8);
		$h = 230-$pdf->GetY();
		$pdf->MultiCell($width, $h, $voucher['narration'], 'LR', 'L', false, 1, '', '', true, 0, false, true, $h, 'B', true);

		$pdf->SetY(230);
 		$this->pdf_invoice_footer($pdf, $voucher['id'], $company, true);
	}

	function transportation_invoice($pdf, $data, $letterhead) {

		extract($data);

		$width = $pdf->GetPageWidth() - 20;
 		$pdf->AddPage();

 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);

 		$pdf->Ln();
		$pdf->SetFont('', 'B', 14);
		$pdf->Cell($width, 10, $page_title, 0, 1, 'C', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(90, 3, 'To', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, humanize($invoice_type).' No', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'Date', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$y = $pdf->GetY();
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(90, 13, $debit_ledger['name']. "\n". $party['address'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 13, 'T', true);
		
		$pdf->SetXY(100, $y);
		$pdf->Cell(50, 5, $voucher['id2_format'], 'R', 0, 'L', 0, false, 1, false, 'T', 'T');
		$pdf->Cell(50, 5, $voucher['date'], 'R', 1, 'L', 0, false, 1, false, 'T', 'T');

		$pdf->SetXY(100, $pdf->GetY());
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(50, 3, 'BL No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'BE No', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$pdf->SetXY(100, $pdf->GetY());
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(50, 5, $job['bl_no'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(50, 5, $job['be_no'], 'R', 'L', false, 1, '', '', true, 0, false, true, 5, 'T', true);

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(45, 3, 'GSTIN', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'PAN No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'State Code', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'Place of Supply', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(45, 5, $party['gst_no'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(45, 5, $party['pan_no'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(50, 5, substr($party['gst_no'], 0, 2), 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(50, 5, $voucher['place_of_supply'], 'R', 'L', false, 1, '', '', true, 0, false, true, 5, 'T', true);


		$pdf->MultiCell(190, 15, (strlen($voucher['remarks']) > 0 ? $voucher['remarks'] : 'Transportation Charges for below mentioned trips'), 'LTR', 'L', false, 1, '', '', true, 0, false, true, 15, 'T', true);
		
		$x = $pdf->getX();
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$max_items   = 17;
		$total_lines = count($voucher_details);
		$hard_limit  = ($max_items + 2);
		$lines       = 0;
		$i           = 1;
		$j           = 0;
		$GrandTotal  = 0;
		$other_row = [];
		$paging    = 1;
		
		$pdf->SetFont('', '', 8);
		$pdf->SetFillColor(230);
		$pdf->SetTextColor(84,84,84);
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(10, 5, 'No', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(85, 5, 'Particulars', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'SAC / HSN', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Qty', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, 'Rate', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, 'Advance', 'TRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, 'INR Amount', 'TRB', 1, 'C', 1, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$total = [
			'amount'  => 0,
			'advance' => 0,
			'balance' => 0,
			'rate'    => 0,

			'tax_amount' => 0,
			'cgst'       => 0,
			'sgst'       => 0,
			'igst'       => 0,
			'total'      => 0,
		];
		$gst_tax = [];
		$remarks = '';
		$i       = 1;
		foreach ($voucher_details as $r) {
			if ($r['gst'] == 'No') {
				$cgst    = bcdiv(bcmul($r['amount'], $r['cgst'], 2), 100, 2);
				$sgst    = bcdiv(bcmul($r['amount'], $r['sgst'], 2), 100, 2);
				$igst    = bcdiv(bcmul($r['amount'], $r['igst'], 2), 100, 2);
				$amount  = round($r['amount'] + $cgst + $sgst + $igst);

				$balance          = bcsub($r['amount'], $r['advance'], 0);
				$total['advance'] = bcadd($total['advance'], $r['advance'], 0);
				$total['amount']  = bcadd($total['amount'], $r['amount'], 2);
				$total['balance'] = bcadd($total['balance'], $balance, 0);
				$total['cgst']    = bcadd($total['cgst'], $cgst, 2);
				$total['sgst']    = bcadd($total['sgst'], $sgst, 2);
				$total['igst']    = bcadd($total['igst'], $igst, 2);
				$total['total']   = bcadd($total['total'], $amount, 2);

				if (isset($gst_tax[$r['sac_hsn']]['cgst'])) {
					$gst_tax[$r['sac_hsn']]['cgst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['cgst']['tax']    += $cgst;
					$gst_tax[$r['sac_hsn']]['sgst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['sgst']['tax']    += $sgst;
					$gst_tax[$r['sac_hsn']]['igst']['amount'] += $r['amount'];
					$gst_tax[$r['sac_hsn']]['igst']['tax']    += $igst;
				}
				else {
					$gst_tax[$r['sac_hsn']]['cgst'] = ['rate' => $r['cgst'], 'amount' => $r['amount'], 'tax' => $cgst];
					$gst_tax[$r['sac_hsn']]['sgst'] = ['rate' => $r['sgst'], 'amount' => $r['amount'], 'tax' => $sgst];
					$gst_tax[$r['sac_hsn']]['igst'] = ['rate' => $r['igst'], 'amount' => $r['amount'], 'tax' => $igst];
				}

				$pdf->Cell(10, 5,  $i++, 'LRB', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(85, 5, $r['gst_service'], 'RB', 0, 'L', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(15, 5, $r['sac_hsn'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(15, 5, $r['units'], 'RB', 0, 'C', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(20, 5, inr_format($r['rate']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(20, 5, inr_format($r['advance']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
				$pdf->Cell(25, 5, inr_format($balance), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');
			}
		}

		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(125, 5, 'Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, inr_format($total['amount']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, inr_format($total['advance']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(25, 5, inr_format($total['balance']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');

		
		if ($total['cgst'] > 0 AND $total['sgst'] > 0 OR $total['igst'] > 0) {
			$pdf->SetFillColor(230);
			$pdf->SetTextColor(84,84,84);
			$pdf->SetFont('', 'B', 8);
			$pdf->Cell(15, 10, 'SAC/HSN', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(60, 10, 'Description of Service', 'LB', 0, 'L', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 10, 'Taxable', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->Cell(25, 5, 'CGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 5, 'SGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 5, 'IGST', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(25, 10, 'Total', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');
			
			$pdf->SetXY($x, $y+5);
			$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 5, 'Amount', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(10, 5, 'Rate', 'LB', 0, 'C', 1, '', 1, false, 'T', 'M');
			$pdf->Cell(15, 5, 'Amount', 'LBR', 1, 'C', 1, '', 1, false, 'T', 'M');

			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('', '', 8);
			$total['cgst'] = 0;
			$total['sgst'] = 0;
			$total['igst'] = 0;
			foreach ($gst_tax as $code => $taxes) {
				$desc = $this->idex->getField('goods_services', $code);

				$taxes['cgst']['tax'] = round($taxes['cgst']['amount'] * $taxes['cgst']['rate'] / 100);
				$taxes['sgst']['tax'] = round($taxes['sgst']['amount'] * $taxes['sgst']['rate'] / 100);
				$taxes['igst']['tax'] = round($taxes['igst']['amount'] * $taxes['igst']['rate'] / 100);

				$total['cgst'] += $taxes['cgst']['tax'];
				$total['sgst'] += $taxes['sgst']['tax'];
				$total['igst'] += $taxes['igst']['tax'];

				$pdf->Cell(15, 7, $code, 'LB', 0, 'L', 0, '', 1, false, 'T', 'M');
				$pdf->MultiCell(60, 7, $desc, 'LB', 'L', false, 0, $pdf->GetX(), $pdf->GetY(), true, 0, false, true, 7, 'M', true);
				$pdf->Cell(15, 7, inr_format($taxes['cgst']['amount']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(10, 7, $taxes['cgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(15, 7, inr_format($taxes['cgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(10, 7, $taxes['sgst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(15, 7, inr_format($taxes['sgst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(10, 7, $taxes['igst']['rate'], 'LB', 0, 'C', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(15, 7, inr_format($taxes['igst']['tax']), 'LB', 0, 'R', 0, '', 1, false, 'T', 'M');
				$pdf->Cell(25, 7, inr_format($taxes['cgst']['amount'] + $taxes['cgst']['tax'] + $taxes['sgst']['tax'] + $taxes['igst']['tax']), 'LBR', 1, 'R', 0, '', 1, false, 'T', 'M');
			}

			$pdf->SetFont('', 'B', 8);
			$pdf->Cell(75, 5, 'Total', 'LRB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, inr_format($total['amount']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($total['cgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($total['sgst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, inr_format($total['igst']), 'RB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(25, 5, '', 'RB', 1, 'R', 1, false, 1, false, 'T', 'C');
		}

		$total['total'] = round($total['balance'] + $total['cgst'] + $total['sgst'] + $total['igst']);

		$pdf->SetFont('', '', 10);
		$pdf->Cell(40, 8, 'Total Amount (in words): ', 'LB', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(125, 8, 'INR '.numberToWords($total['total']), 'B', 0, 'L', 0, false, 1, false, 'T', 'C');
		// $pdf->MultiCell(25, 8, 'INR '.numberToWords($total['total']), 'LRB', 'L', false, 1, '', '', true, 0, false, false, 0, 'M', false);
		$pdf->SetFont($ITFRupeeRegular, 'B', 14);
		$pdf->Cell(5, 8, 'D', 'B', 0, 'R', 0, false, 1, false, 'T', 'M');
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(20, 8, inr_format($total['total']), 'RB', 1, 'R', 0, false, 1, false, 'T', 'C');

		
		$h = 230-$pdf->GetY();
		$pdf->SetFont('', '', 10);
		$pdf->MultiCell($width, $h, $voucher['narration'].
			($voucher['reverse_charge'] == 'Yes' ? "\nAmount of Tax subject to Reverse Charges: ".$voucher['reverse_charge']."\nGSTIN No of Person Liable to Pay GST: ".$party['gst_no'] : ''), 'LR', 'L', false, 1, '', '', true, 0, false, true, $h, 'B', true);

 		$this->pdf_invoice_footer($pdf, $voucher['id'], $company, true);

 		// Details page
		$pdf->AddPage();

 		$this->pdf_header($pdf, $company, $city, $state, $letterhead);

 		$pdf->Ln();
		$pdf->SetFont('', 'B', 14);
		$pdf->Cell($width, 10, 'Annexure', 0, 1, 'C', 0, false, 1, false, 'T', 'C');

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(90, 3, 'To', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, humanize($invoice_type).' No', 'TR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'Date', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$y = $pdf->GetY();
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(90, 13, $debit_ledger['name']. "\n". $party['address'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 13, 'T', true);
		
		$pdf->SetXY(100, $y);
		$pdf->Cell(50, 5, $voucher['id2_format'], 'R', 0, 'L', 0, false, 1, false, 'T', 'T');
		$pdf->Cell(50, 5, $voucher['date'], 'R', 1, 'L', 0, false, 1, false, 'T', 'T');

		$pdf->SetXY(100, $pdf->GetY());
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(50, 3, 'BL No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'BE No', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$pdf->SetXY(100, $pdf->GetY());
		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(50, 5, $job['bl_no'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(50, 5, $job['be_no'], 'R', 'L', false, 1, '', '', true, 0, false, true, 5, 'T', true);

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('', '', 8);
		$pdf->Cell(45, 3, 'GSTIN', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(45, 3, 'PAN No', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'State Code', 'LTR', 0, 'L', 0, false, 1, false, 'T', 'C');
		$pdf->Cell(50, 3, 'Place of Supply', 'TR', 1, 'TR', 0, false, 1, false, 'T', 'C');

		$pdf->SetFont('', 'B', 8);
		$pdf->MultiCell(45, 5, $party['gst_no'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(45, 5, $party['pan_no'], 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(50, 5, substr($party['gst_no'], 0, 2), 'LR', 'L', false, 0, '', '', true, 0, false, true, 5, 'T', true);
		$pdf->MultiCell(50, 5, $voucher['place_of_supply'], 'R', 'L', false, 1, '', '', true, 0, false, true, 5, 'T', true);


		$pdf->SetFont('', '', 8);
		$pdf->Cell(10, 5, 'No.', 'LTRB', 0, 'C', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Date', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Lr No', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Party Ref No', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, 'Container No', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, 'Vehicle No', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'From', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'To', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Unit', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Rate', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(15, 5, 'Advance', 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		$pdf->Cell(20, 5, 'Balance', 'TRB', 1, 'L', 1, false, 1, false, 'T', 'C');

		$i = 1;
		$total = array(
			'units'    => 0,
			'quantity' => 0,
			'amount'   => 0,
			'advance'  => 0,
			'balance'  => 0,
		);
		foreach ($voucher_details as $r) {
			$balance               = bcsub($r['amount'], $r['advance'], 0);
			// $total['units']    += $r['units'];
			// $total['quantity'] += $r['quantity'];
			// $total['amount']    = bcadd($total['amount'], $r['amount']);
			// $total['advance']   = bcadd($total['advance'], $r['party_advance']);
			// $total['balance']   = bcadd($total['balance'], $r['balance']);

			$pdf->Cell(10, 5, $i++, 'LTRB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['date'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['lr_no'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['party_reference_no'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(20, 5, $r['container_no'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(20, 5, $r['registration_no'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['from_location'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['to_location'], 'TRB', 0, 'L', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['units'], 'TRB', 0, 'C', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['rate'], 'TRB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(15, 5, $r['advance'], 'TRB', 0, 'R', 0, false, 1, false, 'T', 'C');
			$pdf->Cell(20, 5, $balance, 'TRB', 1, 'R', 0, false, 1, false, 'T', 'C');
		}

		// $pdf->Cell(90, 5, 'Total', 'LTRB', 0, 'R', 1, false, 1, false, 'T', 'C');
		// $pdf->Cell(15, 5, $total['units'], 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		// $pdf->Cell(15, 5, $total['quantity'], 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		// $pdf->Cell(20, 5, $total['amount'], 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		// $pdf->Cell(15, 5, $total['advance'], 'TRB', 0, 'L', 1, false, 1, false, 'T', 'C');
		// $pdf->Cell(20, 5, $total['amount'], 'TRB', 1, 'L', 1, false, 1, false, 'T', 'C');

		$this->pdf_invoice_footer($pdf, $voucher['id'], $company);
	}
}
