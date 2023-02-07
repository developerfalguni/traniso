<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-01-16 13:27:44 --> Query error: Column 'billing_type' cannot be null - Invalid query: INSERT INTO `costsheets` (`job_id`, `bill_item_id`, `sr_no`, `particulars`, `vendor_id`, `is_inr`, `currency_id`, `ex_rate`, `currency_amt`, `inr_rate`, `rate`, `unit_id`, `qty`, `amount`, `sell_is_inr`, `sell_currency_id`, `sell_ex_rate`, `sell_currency_amt`, `sell_inr_rate`, `sell_rate`, `sell_unit_id`, `sell_qty`, `sell_amount`, `file`, `billing_type`) VALUES ('17', '8', '1', 'EXPORT EXW PACKAGE CHARGES (USD)', '13', 'Yes', '1', '1', '0', '250', '250', '1', '20', '5000', 'Yes', '1', '1', '0', '1500', '1500', '1', '20', '30000', NULL, NULL)
ERROR - 2023-01-16 16:03:51 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:15:08 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:15:34 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:15:48 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:15:53 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:15:53 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:15:57 --> Query error: Unknown column 'C.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:39 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:41 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:41 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:42 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:42 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:43 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:18:43 --> Query error: Unknown column 'mC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, mC.name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees mC ON J.consignee_id = mC.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:12 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:14 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:14 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:15 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:15 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:16 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:19:23 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:21:29 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:21:31 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:22:26 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:22:26 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:22:27 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:22:27 --> Query error: Unknown column 'CC.buyer_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.buyer_name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:22:30 --> Query error: Unknown column 'CC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.name as buyer_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:24:28 --> Query error: Unknown column 'CCC.name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CCC.name as notify_name, CP.name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:27:36 --> Query error: Unknown column 'CP.consignee_name' in 'field list' - Invalid query: SELECT J.*, 
			DATE_FORMAT(J.date, '%d-%m-%Y') as date, 
			DATE_FORMAT(J.sb_date, '%d-%m-%Y') as sb_date, 
			DATE_FORMAT(J.invoice_date, '%d-%m-%Y') as invoice_date, 

			DATE_FORMAT(J.booking_date, '%d-%m-%Y') as booking_date, 
			DATE_FORMAT(J.booking_validity, '%d-%m-%Y') as booking_validity, 
			mbl_type, hbl_type,  

			B.name as branch_name, C.consignee_name as consignee_name, CC.consignee_name as buyer_name, CCC.consignee_name as notify_name, CP.consignee_name as clearance_port, 

		POR.name as por_name, POL.name as pol_name, POD.name as pod_name, FPOD.name as fpod_name, 

		VC.name as cha_name, VL.name as line_name, VF.name as forwarder_name, P.name as package_type_name, U.name as unit_name

		FROM jobs J 
			LEFT OUTER JOIN branches B ON J.branch_id = B.id
			LEFT OUTER JOIN consignees C ON J.consignee_id = C.id
			LEFT OUTER JOIN consignees CC ON J.buyer_id = CC.id
			LEFT OUTER JOIN consignees CCC ON J.notify_id = CCC.id
			LEFT OUTER JOIN ports CP ON J.clearance_port_id = CP.id

			LEFT OUTER JOIN ports POR ON J.por_id = POR.id
			LEFT OUTER JOIN ports POL ON J.pol_id = POL.id
			LEFT OUTER JOIN ports POD ON J.pod_id = POD.id
			LEFT OUTER JOIN ports FPOD ON J.fpod_id = FPOD.id

			LEFT OUTER JOIN vendors VC ON J.cha_id = VC.id
			LEFT OUTER JOIN vendors VL ON J.line_id = VL.id
			LEFT OUTER JOIN vendors VF ON J.forwarder_id = VF.id

			LEFT OUTER JOIN package_types P ON J.package_type = P.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id

		WHERE (J.id = '12' AND J.type = 'Import')
ERROR - 2023-01-16 16:41:50 --> Severity: 4096 --> Object of class CI_DB_mysqli_result could not be converted to string D:\xampp\htdocs\traniso\application\controllers\import\Jobs.php 272
ERROR - 2023-01-16 16:42:02 --> Severity: 4096 --> Object of class CI_DB_mysqli_result could not be converted to string D:\xampp\htdocs\traniso\application\controllers\import\Jobs.php 272
ERROR - 2023-01-16 16:44:40 --> Query error: Unknown column 'name' in 'field list' - Invalid query: SELECT name
FROM `new_agents`
WHERE `id` = '4'
ERROR - 2023-01-16 21:28:59 --> Severity: Notice --> Undefined index: address1 D:\xampp\htdocs\traniso\application\controllers\import\Hbl.php 82
ERROR - 2023-01-16 21:28:59 --> Severity: Notice --> Undefined index: name D:\xampp\htdocs\traniso\application\controllers\import\Hbl.php 83
ERROR - 2023-01-16 21:28:59 --> Severity: Notice --> Undefined index: name D:\xampp\htdocs\traniso\application\controllers\import\Hbl.php 85
ERROR - 2023-01-16 21:30:06 --> 404 Page Not Found: import//index
ERROR - 2023-01-16 21:30:25 --> Severity: Notice --> Undefined variable: vendor_name D:\xampp\htdocs\traniso\application\controllers\import\Job_status.php 99
ERROR - 2023-01-16 21:32:15 --> Severity: error --> Exception: Call to undefined method Gst_invoice::export_quote() D:\xampp\htdocs\traniso\application\controllers\sales\quotation\Import_quote.php 399
