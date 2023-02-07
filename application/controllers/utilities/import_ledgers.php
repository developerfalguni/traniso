<?php

class Import_ledgers extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$default_company = $this->session->userdata("default_company");

		$sql = "SELECT COUNT(L.id) AS numrows FROM ledgers L WHERE company_id = " . $default_company['id'];
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['numrows'] > 0)
			$data['message'] = 'Cannot Import. Ledgers already created.';
		else {
			if ($this->input->post('company_id')) {
				if (Auth::hasAccess(Auth::CREATE | Auth::UPDATE)) {
					$query = $this->db->query("SELECT * FROM ledgers WHERE company_id = " . $this->input->post('company_id') . "  AND category IN ('Bill Items', 'General', 'Party', 'Agent') AND active = 'Yes' ORDER BY category, id");
					$ledgers = $query->result_array();
					foreach ($ledgers as $l) {
						$details = $this->kaabar->getRows('ledger_details', $l['id'], 'ledger_id');

						$l['id'] = 0;
						$l['company_id'] = $default_company['id'];
						$l['opening_balance'] = 0;
						$id = $this->kaabar->save('ledgers', $l);

						foreach ($details as $d) {
							$d['id'] = 0;
							$d['ledger_id'] = $id;
							$id = $this->kaabar->save('ledger_details', $d);
						}
					}
				}
				else {
					$data['message'] = 'Don\'t have enough Permission to Import Ledgers.';
				}
			}
		}

		$rows = $this->kaabar->getRows('companies');
		$companies = array();
		foreach ($rows as $r) {
			if ($r['id'] != $default_company['id'])
				$companies[$r['id']] = $r['code'] . ' - ' . $r['name'];
		}
		$data['companies'] = $companies;
		$data['page'] = $this->_clspath.$this->_class;
		$data['page_title'] = $data['page'];
		$data['hide_title'] = true;
		$this->load->view('index', $data);
	}

};