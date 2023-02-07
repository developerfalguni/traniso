<?php

class Gst {
	function __construct() {
		
	}

	function gstr1_template_excel($rows) {
		ini_set('memory_limit', '512M');

		$filename    = 'GSTR1_'.uniqid().".xlsx";
		// $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(FCPATH.'assets/gst/GSTR1_Excel_Workbook_Template-V1.2.xlsx');
		$sheet       = $spreadsheet->getActiveSheet();
		
		\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

		$spreadsheet->getSheetByName('b2b');
		$i = 5;
		foreach ($rows['invoices'] as $r) {
			$j = 'A';

			if (strlen($r['gst_no']) > 0) {
				$pos = substr($r['gst_no'], 0, 2).'-'.$rows['states'][substr($r['gst_no'], 0, 2)]['name'];

				$sheet->setCellValue($j++.$i, $r['gst_no']);
				$sheet->setCellValue($j++.$i, $r['id2_format']);
				$sheet->setCellValue($j.$i, $r['invoice_date']);
				// $sheet->setCellValue($j++.$i, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($r['invoice_date'])));
				$sheet->getStyle($j++.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
				$sheet->setCellValue($j++.$i, number_format($r['invoice_value'], 2, '.', ''));
				$sheet->setCellValue($j++.$i, $pos);
				$sheet->setCellValue($j++.$i, substr($r['reverse_charge'], 0, 1));
				$sheet->setCellValue($j++.$i, 'Regular');
				$sheet->setCellValue($j++.$i, '');
				$sheet->setCellValue($j++.$i, number_format($r['gst_rate'], 2, '.', ''));
				$sheet->setCellValue($j++.$i, number_format($r['taxable_value'], 2, '.', ''));
				$sheet->setCellValue($j++.$i, $r['cess_amount']);

				$i++;
			}
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}
}
