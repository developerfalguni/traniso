<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu {
	function __construct() {
		$this->_ci =& get_instance();

		$this->_dir    = $this->_ci->router->fetch_directory();
		$this->_class  = $this->_ci->router->fetch_class();
		$this->_method = $this->_ci->router->fetch_method();

		$this->_skip_dir    = ['client/'];
		$this->_skip_method = ['login', 'logout', 'retrieve_password', 'backup'];
		$this->_skip_class  = ['help', 'support', 'newuser', 'barcode', 'icegate_be', 'icegate_sb', 'checkmail', 'concor', 'mict', 'adani', 'dgft', 'traces', 'tally', 'vgm'];
		$this->_allowed     = [
			'main' => [
				'index'           => 1,
				'default_company' => 1,
				'settings'        => 1,
				'global_settings' => 1,
				'ajaxMenu'        => 1,
			],
			'user' => [
				'change_password' => 1,
			],
		];

		$this->_ci->config->set_item('menus', [
			'dashboard' => [
				'name' => 'Dashboard',
				'url'  => site_url('main'),
				'link' => anchor('main', '<i class="nav-icon fa fa-dashboard"></i><p> Dashboard</p>', 'class="nav-link"'),
			],
			'admin' => [
				'name' => 'Admin',
				'link' => anchor('#', '<i class="nav-icon fa fa-lock"></i><p> Admin<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'admin/company/edit/1' => [
						'name' => 'Company Master',
						'url'  => site_url('admin/company/edit/1'),
						'link' => anchor('admin/company/edit/1', '<i class="nav-icon fa fa-angle-right"></i> <p>Company Master</p>', 'class="nav-link"'),
					],
					'admin/user' => [
						'name' => 'User Master',
						'url'  => site_url('admin/user'),
						'link' => anchor('admin/user', '<i class="nav-icon fa fa-angle-right"></i> <p>User Master</p>', 'class="nav-link"'),
					],
					'admin/branch' => [
						'name' => 'Branch Master',
						'url'  => site_url('admin/branch/edit/0'),
						'link' => anchor('admin/branch/edit/0', '<i class="nav-icon fa fa-angle-right"></i> <p>Branch Master</p>', 'class="nav-link"'),
					],
					'admin/permission' => [
						'name' => 'Menu Permission',
						'url'  => site_url('admin/permission'),
						'link' => anchor('admin/permission', '<i class="nav-icon fa fa-angle-right"></i> <p>Menu Permission</p>', 'class="nav-link"'),
					],
				],
			],
			'master' => [
				'name' => 'Masters',
				'link' => anchor('#', '<i class="nav-icon fa fa-cog"></i><p> Masters<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'master/operation' => [
						'name' => 'Operations',
						'link' => anchor('#', '<i class="nav-icon fa fa-circle"></i> <p>Operations<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

						'nodes' => [

							'master/operation/party' => [
								'name' => 'Customer Master',
								'url'  => site_url('master/operation/party'),
								'link' => anchor('master/operation/party', '<i class="nav-icon fa fa-angle-right"></i> <p>Customer Master</p>', 'class="nav-link"'),
							],
							'master/operation/agent' => [
								'name' => 'Agent Master',
								'url'  => site_url('master/operation/agent'),
								'link' => anchor('master/operation/agent', '<i class="nav-icon fa fa-angle-right"></i> <p>Agent Master</p>', 'class="nav-link"'),
							],
							'master/operation/consignee' => [
								'name' => 'Consignee Master',
								'url'  => site_url('master/operation/consignee'),
								'link' => anchor('master/operation/consignee', '<i class="nav-icon fa fa-angle-right"></i> <p>Consignee Master</p>', 'class="nav-link"'),
							],
							'master/operation/vendor' => [
								'name' => 'Vendor Master',
								'url'  => site_url('master/operation/vendor'),
								'link' => anchor('master/operation/vendor', '<i class="nav-icon fa fa-angle-right"></i> <p>Vendor Master</p>', 'class="nav-link"'),
							],
							'master/operation/city' => [
								'name' => 'City List',
								'url'  => site_url('master/operation/city'),
								'link' => anchor('master/operation/city', '<i class="nav-icon fa fa-angle-right"></i> <p>City List</p>', 'class="nav-link"'),
								'hide' => true,
							],
							'master/operation/state' => [
								'name' => 'State List',
								'url'  => site_url('master/operation/state'),
								'link' => anchor('master/operation/state', '<i class="nav-icon fa fa-angle-right"></i> <p>State List</p>', 'class="nav-link"'),
								'hide' => true,
							],
							'master/operation/country' => [
								'name' => 'Country List',
								'url'  => site_url('master/operation/country'),
								'link' => anchor('master/operation/country', 'Country List'),
								'link' => anchor('master/operation/country', '<i class="nav-icon fa fa-angle-right"></i> <p>Country List</p>', 'class="nav-link"'),
								'hide' => true,
							],
							'master/operation/port' => [
								'name' => 'Port List',
								'url'  => site_url('master/operation/port'),
								'link' => anchor('master/operation/port', '<i class="nav-icon fa fa-angle-right"></i> <p>Port List</p>', 'class="nav-link"'),
								'hide' => true,
							],
							'master/operation/unit' => [
								'name' => 'Unit List',
								'url'  => site_url('master/operation/unit'),
								'link' => anchor('master/operation/unit', '<i class="nav-icon fa fa-angle-right"></i> <p>Unit List</p>', 'class="nav-link"'),
								'hide' => true,
							],
							'master/operation/package_type' => [
								'name' => 'Package Types',
								'url'  => site_url('master/operation/package_type'),
								'link' => anchor('master/operation/package_type', '<i class="nav-icon fa fa-angle-right"></i> <p>Package Types</p>', 'class="nav-link"'),
								'hide' => true,
							],
							
						],
					],
					'master/account' => [
						'name' => 'Accounts',
						'link' => anchor('#', '<i class="nav-icon fa fa-circle"></i> <p>Accounts<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

						'nodes' => [
							'master/account/bill_item' => [
								'name' => 'Bill Items',
								'url'  => site_url('master/account/bill_item'),
								'link' => anchor('master/account/bill_item', '<i class="nav-icon fa fa-angle-right"></i> <p>Bill Items</p>', 'class="nav-link"'),
							],
							'master/account/staff' => [
								'name' => 'Staff Master',
								'url'  => site_url('master/account/staff'),
								'link' => anchor('master/account/staff', '<i class="nav-icon fa fa-angle-right"></i> <p>Staff Master</p>', 'class="nav-link"'),
							],
							'master/account/bank' => [
								'name' => 'Bank Master',
								'url'  => site_url('master/account/bank'),
								'link' => anchor('master/account/bank', '<i class="nav-icon fa fa-angle-right"></i> <p>Bank Master</p>', 'class="nav-link"'),
							],
							
						],
					],
				],
			],
			'export/jobs' => [
				'name' => 'Export Jobs',
				'link' => anchor('#', '<i class="nav-icon fa fa-upload"></i><p> Export<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),
				
				'nodes' => [
					'export/jobs' => [
						'name' => 'Job Planning',
						'url'  => site_url('export/jobs'),
						'link' => anchor('export/jobs', '<i class="nav-icon fa fa-angle-right"></i> <p>Job Planning</p>', 'class="nav-link"'),
					],
					'export/jobs/edit' => [
						'name' => 'Job Create',
						'url'  => site_url('export/jobs/edit'),
						'link' => anchor('export/jobs/edit', '<i class="nav-icon fa fa-angle-right"></i> <p>Job Create</p>', 'class="nav-link"'),
						'hide' => true,
					],
					'export/costsheet' => [
						'name' => 'Costsheet',
						'url'  => site_url('export/costsheet'),
						'link' => anchor('export/costsheet', '<i class="nav-icon fa fa-angle-right"></i> <p>Costsheet</p>', 'class="nav-link"'),
					],
					'export/hbl' => [
						'name' => 'HBL',
						'url'  => site_url('export/hbl'),
						'link' => anchor('export/hbl', '<i class="nav-icon fa fa-angle-right"></i> <p>HBL</p>', 'class="nav-link"'),
					],
					'export/job_status' => [
						'name' => 'Job Status',
						'url'  => site_url('export/job_status'),
						'link' => anchor('export/job_status', '<i class="nav-icon fa fa-angle-right"></i> <p>Job Status</p>', 'class="nav-link"'),
					],
					'export/dsr_report' => [
						'name' => 'DSR Report',
						'url'  => site_url('export/dsr_report'),
						'link' => anchor('export/dsr_report', '<i class="nav-icon fa fa-angle-right"></i> <p>DSR Report</p>', 'class="nav-link"'),
					],
				],
			],

			'import/jobs' => [
				'name' => 'Import Jobs',
				'link' => anchor('#', '<i class="nav-icon fa fa-download"></i><p> Import<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'import/jobs/edit/0' => [
						'name' => 'Job Planning',
						'url'  => site_url('import/jobs/edit/0'),
						'link' => anchor('import/jobs/edit/0', '<i class="nav-icon fa fa-angle-right"></i> <p>Job Planning</p>', 'class="nav-link"'),
					],
					'import/costsheet' => [
						'name' => 'Costsheet',
						'url'  => site_url('import/costsheet'),
						'link' => anchor('import/costsheet', '<i class="nav-icon fa fa-angle-right"></i> <p>Costsheet</p>', 'class="nav-link"'),
					],
					'import/hbl' => [
						'name' => 'HBL',
						'url'  => site_url('import/hbl'),
						'link' => anchor('import/hbl', '<i class="nav-icon fa fa-angle-right"></i> <p>HBL</p>', 'class="nav-link"'),
					],
					'import/job_status' => [
						'name' => 'Job Status',
						'url'  => site_url('import/job_status'),
						'link' => anchor('import/job_status', '<i class="nav-icon fa fa-angle-right"></i> <p>Job Status</p>', 'class="nav-link"'),
					],
					'import/dsr_report' => [
						'name' => 'DSR Report',
						'url'  => site_url('import/dsr_report'),
						'link' => anchor('import/dsr_report', '<i class="nav-icon fa fa-angle-right"></i> <p>DSR Report</p>', 'class="nav-link"'),
					],
				],
			],

			'sale' => [
				'name' => 'Sales',
				'link' => anchor('#', '<i class="nav-icon fa fa-shopping-cart"></i><p> Sales<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'sales/quotation' => [
						'name' => 'Quotation',
						'link' => anchor('#', '<i class="nav-icon fa fa-circle"></i> <p>Quotation<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

						'nodes' => [

							'sales/quotation/export_quote' => [
								'name' => 'Export',
								'url'  => site_url('sales/quotation/export_quote'),
								'link' => anchor('sales/quotation/export_quote', '<i class="nav-icon fa fa-angle-right"></i> <p>Export</p>', 'class="nav-link"'),
							],
							'sales/quotation/import_quote' => [
								'name' => 'Import',
								'url'  => site_url('sales/quotation/import_quote'),
								'link' => anchor('sales/quotation/import_quote', '<i class="nav-icon fa fa-angle-right"></i> <p>Import</p>', 'class="nav-link"'),
							],
						],
					],	
				],
			],

			'accounting' => [
				'name' => 'Accounts',
				'link' => anchor('#', '<i class="nav-icon fa fa-rupee"></i><p> Accounts<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'accounting/export' => [
						'name' => 'Export Billing',
						'link' => anchor('#', '<i class="nav-icon fa fa-circle"></i> <p>Export Billing<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

						'nodes' => [
							'accounting/pending/index/export' => [
								'hide' => true,
								'name' => 'Pending Export Jobs',
								'url'  => site_url('accounting/pending/index/export'),
								'link' => anchor('accounting/pending/index/export', '<i class="nav-icon fa fa-angle-right"></i> <p>Pending</p>', 'class="nav-link"'),
							],
							'accounts/export_billing/gst' => [
								'name' => 'GST',
								'url'  => site_url('accounts/export_billing/gst'),
								'link' => anchor('accounts/export_billing/gst', '<i class="nav-icon fa fa-angle-right"></i> <p>GST</p>', 'class="nav-link"'),
							],
							'accounts/export_billing/non_gst' => [
								'name' => 'Non GST',
								'url'  => site_url('accounts/export_billing/non_gst'),
								'link' => anchor('accounts/export_billing/non_gst', '<i class="nav-icon fa fa-angle-right"></i> <p>Non GST</p>', 'class="nav-link"'),
							],
							'accounts/export_billing/forex' => [
								'name' => 'Forex',
								'url'  => site_url('accounts/export_billing/forex'),
								'link' => anchor('accounts/export_billing/forex', '<i class="nav-icon fa fa-angle-right"></i> <p>Forex</p>', 'class="nav-link"'),
							],
						],
					],	
					'accounts/import_billing' => [
						'name' => 'Import Billing',
						'link' => anchor('#', '<i class="nav-icon fa fa-circle"></i> <p>Import Billing<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

						'nodes' => [

							'accounts/import_billing/gst' => [
								'name' => 'GST',
								'url'  => site_url('accounts/import_billing/gst'),
								'link' => anchor('accounts/import_billing/gst', '<i class="nav-icon fa fa-angle-right"></i> <p>GST</p>', 'class="nav-link"'),
							],
							'accounts/import_billing/non_gst' => [
								'name' => 'Non GST',
								'url'  => site_url('accounts/import_billing/non_gst'),
								'link' => anchor('accounts/import_billing/non_gst', '<i class="nav-icon fa fa-angle-right"></i> <p>Non GST</p>', 'class="nav-link"'),
							],
							'accounts/import_billing/forex' => [
								'name' => 'Forex',
								'url'  => site_url('accounts/import_billing/forex'),
								'link' => anchor('accounts/import_billing/forex', '<i class="nav-icon fa fa-angle-right"></i> <p>Forex</p>', 'class="nav-link"'),
							],
						],
					],
				],
			],

			'transport' => [
				'name' => 'Transport',
				'link' => anchor('#', '<i class="nav-icon fa fa-truck"></i><p> Transport<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'transport/bilty' => [
						'name' => 'Create LR',
						'url'  => site_url('transport/bilty'),
						'link' => anchor('transport/bilty', '<i class="nav-icon fa fa-angle-right"></i> <p>Create LR</p>', 'class="nav-link"'),
					],	
					'transport/lr_report' => [
						'name' => 'LR Report',
						'url'  => site_url('transport/lr_report'),
						'link' => anchor('transport/lr_report', '<i class="nav-icon fa fa-angle-right"></i> <p>LR Report</p>', 'class="nav-link"'),
					],
				],	
			],

			'utility' => [
				'name' => 'Utility',
				'link' => anchor('#', '<i class="nav-icon fa fa-wrench"></i><p> Utility<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),

				'nodes' => [
					'utility/master_search' => [
						'name' => 'Master Search',
						'url'  => site_url('utilities/master_search'),
						'link' => anchor('utilities/master_search', '<i class="nav-icon fa fa-angle-right"></i> <p>Master Search</p>', 'class="nav-link"'),
					],	
					'utility/voucher_print' => [
						'name' => 'Voucher Print',
						'url'  => site_url('utilities/voucher_print'),
						'link' => anchor('utilities/voucher_print', '<i class="nav-icon fa fa-angle-right"></i> <p>Voucher Print</p>', 'class="nav-link"'),
					],
					'utility/other_documents_upload' => [
						'name' => 'Other Documents Upload',
						'url'  => site_url('utilities/other_documents_upload'),
						'link' => anchor('utilities/other_documents_upload', '<i class="nav-icon fa fa-angle-right"></i> <p>Other Documents Upload</p>', 'class="nav-link"'),
					],
				],	
			],
			
			// 'accounting' => [
			// 	'name' => 'Accounting',
			// 	'link' => anchor('#', '<i class="nav-icon fa fa-rupee-sign"></i><p> Accounting<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),
			// 	//'hide' => true,

			// 	'nodes' => [
			// 		'accounting/company' => [
			// 			'name' => 'Company List',
			// 			'url'  => site_url('accounting/company'),
			// 			'link' => anchor('accounting/company', '<i class="nav-icon fa fa-angle-right"></i> <p>Company List</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/branch' => [
			// 			'name' => 'Branch List',
			// 			'url'  => site_url('accounting/branch'),
			// 			'link' => anchor('accounting/branch', '<i class="nav-icon fa fa-angle-right"></i> <p>Company Branch</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/account_group' => [
			// 			'name' => 'Account Groups',
			// 			'url'  => site_url('accounting/account_group'),
			// 			'link' => anchor('accounting/account_group', '<i class="nav-icon fa fa-angle-right"></i> <p>Account Groups</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/bill_item' => [
			// 			'name' => 'Bill Items',
			// 			'url'  => site_url('accounting/bill_item'),
			// 			'link' => anchor('accounting/bill_item', '<i class="nav-icon fa fa-angle-right"></i> <p>Bill Items</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/tds_class' => [
			// 			'name' => 'TDS','id="tds_class"',
			// 			'url'  => site_url('accounting/tds_class'),
			// 			'link' => anchor('accounting/tds_class', '<i class="nav-icon fa fa-angle-right"></i> <p>TDS</p>', 'class="nav-link" id="tds_class"'),
			// 		],
			// 		'accounting/stax_category' => [
			// 			'name' => 'Service Tax Category',
			// 			'url'  => site_url('accounting/stax_category'),
			// 			'link' => anchor('accounting/stax_category', '<i class="nav-icon fa fa-angle-right"></i> <p>Service Tax Category</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/goods_service' => [
			// 			'name' => 'Goods & Services',
			// 			'url'  => site_url('accounting/goods_service'),
			// 			'link' => anchor('accounting/goods_service', '<i class="nav-icon fa fa-angle-right"></i> <p>Goods & Services</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/general' => [
			// 			'name' => 'General Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/general'),
			// 			'link' => anchor('accounting/ledger/index/general', '<i class="nav-icon fa fa-angle-right"></i> <p>General Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/bank' => [
			// 			'name' => 'Bank Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/bank'),
			// 			'link' => anchor('accounting/ledger/index/bank', '<i class="nav-icon fa fa-angle-right"></i> <p>Bank Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/party' => [
			// 			'name' => 'Party Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/party'),
			// 			'link' => anchor('accounting/ledger/index/party', '<i class="nav-icon fa fa-angle-right"></i> <p>Party Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/vessel' => [
			// 			'name' => 'Vessel Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/vessel'),
			// 			'link' => anchor('accounting/ledger/index/vessel', '<i class="nav-icon fa fa-angle-right"></i> <p>Vessel Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/agent' => [
			// 			'name' => 'Agent Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/agent'),
			// 			'link' => anchor('accounting/ledger/index/agent', '<i class="nav-icon fa fa-angle-right"></i> <p>Agent Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/staff' => [
			// 			'name' => 'Staff Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/staff'),
			// 			'link' => anchor('accounting/ledger/index/staff', '<i class="nav-icon fa fa-angle-right"></i> <p>Staff Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/ledger/vehicle' => [
			// 			'name' => 'Vehicle Ledgers',
			// 			'url'  => site_url('accounting/ledger/index/vehicle'),
			// 			'link' => anchor('accounting/ledger/index/vehicle', '<i class="nav-icon fa fa-angle-right"></i> <p>Vehicle Ledgers</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/voucher_book' => [
			// 			'name' => 'Voucher Books',
			// 			'url'  => site_url('accounting/voucher_book'),
			// 			'link' => anchor('accounting/voucher_book', '<i class="nav-icon fa fa-angle-right"></i> <p>Voucher Books</p>', 'class="nav-link"'),
			// 		],
			// 		'accounting/voucher_book_renumber' => [
			// 			'name' => 'Voucher Re-Numbering',
			// 			'url'  => site_url('accounting/voucher_book/renumber'),
			// 			'link' => anchor('accounting/voucher_book/renumber', '<i class="nav-icon fa fa-angle-right"></i> <p>Voucher Re-Numbering</p>', 'class="nav-link" id="renumber"'),
			// 		],
			// 		'accounting/voucher_document' => [
			// 			'name' => 'Voucher Documents',
			// 			'url'  => site_url('accounting/voucher_document'),
			// 			'link' => anchor('accounting/voucher_document', '<i class="nav-icon fa fa-angle-right"></i> <p>Voucher Documents</p>', 'class="nav-link"'),
			// 			'hide' => true,
			// 		],
			// 	] + $voucher_book_menu +
			// 	[
			// 		'accounting/reports/ledger' => [
			// 			'name' => 'Ledger',
			// 			'url'  => site_url('accounting/reports/ledger'),
			// 			'link' => anchor('accounting/reports/ledger', '<i class="nav-icon fa fa-file"></i> <p>Ledger</p>', 'class="nav-link"'),
			// 		],
			// 	],
			// ],

			// 'reports' => [
			// 	'name' => 'Reports',
			// 	'link' => anchor('#', '<i class="nav-icon fa fa-chart-pie"></i><p> Reports<i class="right fa fa-angle-left"></i></p>', 'class="nav-link"'),
				

			// 	'nodes' => [
			// 		'reports/bulk_consignment' => [
			// 			'name' => 'Bulk Consignment Summary',
			// 			'url'  => site_url('reports/bulk_consignment'),
			// 			'link' => anchor('reports/bulk_consignment', '<i class="nav-icon fa fa-angle-right"></i> <p>Bulk Consignment Summary</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/icegate_be/index' => [
			// 			'name' => 'Import Icegate Register (OOC)',
			// 			'url'  => site_url('reports/icegate_be/index'),
			// 			'link' => anchor('reports/icegate_be/index', '<i class="nav-icon fa fa-angle-right"></i> <p>Import Icegate Register (OOC)</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/icegate_be/index_be' => [
			// 			'name' => 'Import Icegate Register (BE)',
			// 			'url'  => site_url('reports/icegate_be/index_be'),
			// 			'link' => anchor('reports/icegate_be/index_be', '<i class="nav-icon fa fa-angle-right"></i> <p>Import Icegate Register (BE)</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/icegate_be/group' => [
			// 			'name' => 'Import Icegate Group Register',
			// 			'url'  => site_url('reports/icegate_be/group'),
			// 			'link' => anchor('reports/icegate_be/group', '<i class="nav-icon fa fa-angle-right"></i> <p>Import Icegate Group Register</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/custom_duty' => [
			// 			'name' => 'Custom Duty',
			// 			'url'  => site_url('reports/custom_duty'),
			// 			'link' => anchor('reports/custom_duty', '<i class="nav-icon fa fa-angle-right"></i> <p>Custom Duty</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/import_consignment' => [
			// 			'name' => 'Consignment Register',
			// 			'url'  => site_url('reports/import_consignment'),
			// 			'link' => anchor('reports/import_consignment', '<i class="nav-icon fa fa-angle-right"></i> <p>Consignment Register</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/import_consignment/summary' => [
			// 			'name' => 'Consignment Summary Repor',
			// 			'url'  => site_url('reports/import_consignment/summary'),
			// 			'link' => anchor('reports/import_consignment/summary', '<i class="nav-icon fa fa-angle-right"></i> <p>Consignment Summary Report</p>', 'class="nav-link"'),
			// 			],
			// 		'reports/delivery' => [
			// 			'name' => 'Daily Dispatch Report',
			// 			'url'  => site_url('reports/delivery'),
			// 			'link' => anchor('reports/delivery', '<i class="nav-icon fa fa-angle-right"></i> <p>Daily Dispatch Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/pickup' => [
			// 			'name' => 'Pickup Program',
			// 			'url'  => site_url('reports/pickup'),
			// 			'link' => anchor('reports/pickup', '<i class="nav-icon fa fa-angle-right"></i> <p>Pickup Program</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/stuffing' => [
			// 			'name' => 'Stuffing Program',
			// 			'url'  => site_url('reports/stuffing'),
			// 			'link' => anchor('reports/stuffing', '<i class="nav-icon fa fa-angle-right"></i> <p>Stuffing Program</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/cargo_arrival' => [
			// 			'name' => 'Cargo Arrival',
			// 			'url'  => site_url('reports/cargo_arrival'),
			// 			'link' => anchor('reports/cargo_arrival', '<i class="nav-icon fa fa-angle-right"></i> <p>Cargo Arrival</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/shipment' => [
			// 			'name' => 'Shipment Details',
			// 			'url'  => site_url('reports/shipment'),
			// 			'link' => anchor('reports/shipment', '<i class="nav-icon fa fa-angle-right"></i> <p>Shipment Details</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/vessel_planning' => [
			// 			'name' => 'Vessel Planning',
			// 			'url'  => site_url('reports/vessel_planning'),
			// 			'link' => anchor('reports/vessel_planning', '<i class="nav-icon fa fa-angle-right"></i> <p>Vessel Planning</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/booking' => [
			// 			'name' => 'Booking Report',
			// 			'url'  => site_url('reports/booking'),
			// 			'link' => anchor('reports/booking', '<i class="nav-icon fa fa-angle-right"></i> <p>Booking Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/export_consignment' => [
			// 			'name' => 'Consignment Report',
			// 			'url'  => site_url('reports/export_consignment'),
			// 			'link' => anchor('reports/export_consignment', '<i class="nav-icon fa fa-angle-right"></i> <p>Consignment Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/icegate_sb' => [
			// 			'name' => 'Icegate Register',
			// 			'url'  => site_url('reports/icegate_sb'),
			// 			'link' => anchor('reports/icegate_sb', '<i class="nav-icon fa fa-angle-right"></i> <p>Icegate Register</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/trip_auditor' => [
			// 			'name' => 'Trips Auditor Report',
			// 			'url'  => site_url('reports/trip_auditor'),
			// 			'link' => anchor('reports/trip_auditor', '<i class="nav-icon fa fa-angle-right"></i> <p>Trips Auditor Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/vehicle_income' => [
			// 			'name' => 'Vehiclewise Income Register',
			// 			'url'  => site_url('reports/vehicle_income'),
			// 			'link' => anchor('reports/vehicle_income', '<i class="nav-icon fa fa-angle-right"></i> <p>Vehiclewise Income Register</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/transport_inward' => [
			// 			'name' => 'Transport Inward Report',
			// 			'url'  => site_url('reports/transport_inward'),
			// 			'link' => anchor('reports/transport_inward', '<i class="nav-icon fa fa-angle-right"></i> <p>Transport Inward Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/main' => [
			// 			'name' => 'Job Register',
			// 			'url'  => site_url('reports/main'),
			// 			'link' => anchor('reports/main', '<i class="nav-icon fa fa-angle-right"></i> <p>Job Register</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/auditor' => [
			// 			'name' => 'Auditor',
			// 			'url'  => site_url('reports/auditor'),
			// 			'link' => anchor('reports/auditor', '<i class="nav-icon fa fa-angle-right"></i> <p>Auditor</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/unbilled' => [
			// 			'name' => 'Unbilled Report',
			// 			'url'  => site_url('reports/unbilled'),
			// 			'link' => anchor('reports/unbilled', '<i class="nav-icon fa fa-angle-right"></i> <p>Unbilled Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/comparison' => [
			// 			'name' => 'Comparison Report',
			// 			'url'  => site_url('reports/comparison'),
			// 			'link' => anchor('reports/comparison', '<i class="nav-icon fa fa-angle-right"></i> <p>Comparison Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/account' => [
			// 			'name' => 'Ledger',
			// 			'url'  => site_url('reports/account'),
			// 			'link' => anchor('reports/account', '<i class="nav-icon fa fa-angle-right"></i> <p>Ledger</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/group' => [
			// 			'name' => 'Group Ledger',
			// 			'url'  => site_url('reports/group'),
			// 			'link' => anchor('reports/group', '<i class="nav-icon fa fa-angle-right"></i> <p>Group Ledger</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/journal' => [
			// 			'name' => 'Journal',
			// 			'url'  => site_url('reports/journal'),
			// 			'link' => anchor('reports/journal', '<i class="nav-icon fa fa-angle-right"></i> <p>Journal</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/payment' => [
			// 			'name' => 'Payment',
			// 			'url'  => site_url('reports/payment'),
			// 			'link' => anchor('reports/payment', '<i class="nav-icon fa fa-angle-right"></i> <p>Payment</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/receipt' => [
			// 			'name' => 'Receipt',
			// 			'url'  => site_url('reports/receipt'),
			// 			'link' => anchor('reports/receipt', '<i class="nav-icon fa fa-angle-right"></i> <p>Receipt</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/invoice' => [
			// 			'name' => 'Invoice &amp; Debit Note',
			// 			'url'  => site_url('reports/invoice'),
			// 			'link' => anchor('reports/invoice', '<i class="nav-icon fa fa-angle-right"></i> <p>Invoice &amp; Debit Note</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/payable' => [
			// 			'name' => 'Bills Payable',
			// 			'url'  => site_url('reports/payable'),
			// 			'link' => anchor('reports/payable', '<i class="nav-icon fa fa-angle-right"></i> <p>Bills Payable</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/receivable' => [
			// 			'name' => 'Bills Receivable',
			// 			'url'  => site_url('reports/receivable'),
			// 			'link' => anchor('reports/receivable', '<i class="nav-icon fa fa-angle-right"></i> <p>Bills Receivable</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/bill_item' => [
			// 			'name' => 'Bill Items',
			// 			'url'  => site_url('reports/bill_item'),
			// 			'link' => anchor('reports/bill_item', '<i class="nav-icon fa fa-angle-right"></i> <p>Bill Items</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/container_volume_billwise' => [
			// 			'name' => 'Container Volume Billwise',
			// 			'url'  => site_url('reports/container_volume_billwise'),
			// 			'link' => anchor('reports/container_volume_billwise', '<i class="nav-icon fa fa-angle-right"></i> <p>Container Volume Billwise</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/tds' => [
			// 			'name' => 'TDS',
			// 			'url'  => site_url('reports/tds'),
			// 			'link' => anchor('reports/tds', '<i class="nav-icon fa fa-angle-right"></i> <p>TDS</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/tds_party' => [
			// 			'name' => 'TDS Party List',
			// 			'url'  => site_url('reports/tds_party'),
			// 			'link' => anchor('reports/tds_party', '<i class="nav-icon fa fa-angle-right"></i> <p>TDS Party List</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/tds_transport' => [
			// 			'name' => 'Transportation TDS Report',
			// 			'url'  => site_url('reports/tds_transport'),
			// 			'link' => anchor('reports/tds_transport', '<i class="nav-icon fa fa-angle-right"></i> <p>Transportation TDS Report</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/tds_partywise' => [
			// 			'name' => 'TDS Partywise',
			// 			'url'  => site_url('reports/tds_partywise'),
			// 			'link' => anchor('reports/tds_partywise', '<i class="nav-icon fa fa-angle-right"></i> <p>TDS Partywise</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/cenvat' => [
			// 			'name' => 'CENVAT',
			// 			'url'  => site_url('reports/cenvat'),
			// 			'link' => anchor('reports/cenvat', '<i class="nav-icon fa fa-angle-right"></i> <p>CENVAT</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/stax' => [
			// 			'name' => 'Service Tax',
			// 			'url'  => site_url('reports/stax'),
			// 			'link' => anchor('reports/stax', '<i class="nav-icon fa fa-angle-right"></i> <p>Service Tax</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/stax_bulk_container' => [
			// 			'name' => 'Service Tax (Bulk/Container)',
			// 			'url'  => site_url('reports/stax_bulk_container'),
			// 			'link' => anchor('reports/stax_bulk_container', '<i class="nav-icon fa fa-angle-right"></i> <p>Service Tax (Bulk/Container)</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/reimbersment' => [
			// 			'name' => 'Reimbersment',
			// 			'url'  => site_url('reports/reimbersment'),
			// 			'link' => anchor('reports/reimbersment', '<i class="nav-icon fa fa-angle-right"></i> <p>Reimbersment</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/reconcilation' => [
			// 			'name' => 'Bills Reconcilation',
			// 			'url'  => site_url('reports/reconcilation'),
			// 			'link' => anchor('reports/reconcilation', '<i class="nav-icon fa fa-angle-right"></i> <p>Bills Reconcilation</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/trial_balance' => [
			// 			'name' => 'Trial Balance',
			// 			'url'  => site_url('reports/trial_balance'),
			// 			'link' => anchor('reports/trial_balance', '<i class="nav-icon fa fa-angle-right"></i> <p>Trial Balance</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/profit_loss' => [
			// 			'name' => 'Profit &amp; Loss',
			// 			'url'  => site_url('reports/profit_loss'),
			// 			'link' => anchor('reports/profit_loss', '<i class="nav-icon fa fa-angle-right"></i> <p>Profit &amp; Loss</p>', 'class="nav-link"'),
			// 		],
			// 		'reports/balance_sheet' => [
			// 			'name' => 'Balance Sheet',
			// 			'url'  => site_url('reports/balance_sheet'),
			// 			'link' => anchor('reports/balance_sheet', '<i class="nav-icon fa fa-angle-right"></i> <p>Balance Sheet</p>', 'class="nav-link"'),
			// 		],

			// 	],
			// ],
		]);
	}

	function index() {
		// Skip whole directory
		if (in_array($this->_dir, $this->_skip_dir))
			return;
		
		if (! isset($this->_allowed[$this->_class][$this->_method]) AND (
			$this->_ci->input->is_ajax_request() OR
			$this->_ci->input->is_cli_request() OR
			in_array($this->_method, $this->_skip_method) OR
			in_array($this->_class, $this->_skip_class))
			)
			return;
		else
			Auth::isValidUser() OR redirect('main/login');
		
		$default_company = $this->_ci->session->userdata("default_company");
		$perm            = Auth::get('permissions');

		if (isset($perm[$default_company['id']]))
			$perm = $perm[$default_company['id']];

		$menu = $this->_ci->config->item('menus');
		$flatten_menu = function($result, $submenus) use (&$flatten_menu) {
			foreach ($submenus as $menu => $items) {
				if (isset($items['url']))
					$result[str_replace(['/index', '/edit', '/delete'], '', $items['url'])] = $menu;
				else
					$result[str_replace(['/index', '/edit', '/delete'], '', base_url($menu))] = $menu;

				if (isset($items['nodes'])) {
					$result += $flatten_menu($result, $items['nodes']);
				}
			}
			return $result;
		};
		$result = $flatten_menu([], $menu, true);

		$hasPerm = false;
		// Check for URI permission in reverse order.
		$segs = $this->_ci->uri->segment_array();
		$id = array_search('index', $segs);  unset($segs[$id]);
		$id = array_search('edit', $segs); 	 unset($segs[$id]);
		$id = array_search('delete', $segs); unset($segs[$id]);
		$id = array_search('pdf', $segs);    unset($segs[$id]);
		$id = array_search('preview', $segs);unset($segs[$id]);
		$id = array_search('excel', $segs);  unset($segs[$id]);

		$permission = Auth::READ;
		if ((substr($this->_method, 0, 4) == 'edit' AND $this->_ci->input->method() == 'get'))
			$permission = Auth::READ;
		else if ((substr($this->_method, 0, 4) == 'edit' AND $this->_ci->input->post('id') > 0) OR
			 substr($this->_method, 0, 6) == 'update')
			$permission = Auth::UPDATE;
		else if (substr($this->_method, 0, 4) == 'edit' OR
				 substr($this->_method, 0, 6) == 'attach' OR
				 substr($this->_method, 0, 6) == 'create')
			$permission = Auth::CREATE;
		else if (substr($this->_method, 0, 6) == 'delete')
			$permission = Auth::DELETE;
		
		$content = '';
		while(count($segs) > 0) {
			$content = base_url(implode('/', $segs));
			// Exists in Array
			if (isset($result[$content]) AND isset($perm[$result[$content]])) {
				if (($perm[$result[$content]] & $permission) == $permission) {
					$hasPerm = true;
					break;
				}
			}
			array_pop($segs);
		}

		if (! Auth::isAdmin() AND
			! $hasPerm AND
			! isset($this->_allowed[$this->_class][$this->_method])
			) {
			
			setSessionError('You don&rsquo;t have enough permission');
			if (($permission & Auth::READ) == Auth::READ)
				redirect($this->_ci->agent->referrer());
			else
				redirect('main');
		}
	}
}
