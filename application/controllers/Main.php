<?php

class Main extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		// Setting Default Company and Financial Year
		$default_company = $this->session->userdata("default_company");

		if ($default_company == false) {
			$row = $this->kaabar->getRow('companies', Settings::get('default_company', Settings::get('default_company')));
			$default_company = array(
				'id'             => $row['id'], 
				'branch'         => Settings::get('default_branch'), 
				'code'           => $row['code'],
				'name'           => $row['name'],
				'gst_no'         => $row['gst_no'],
				'financial_year' => $this->kaabar->_getFinancialYear(date('d-m-Y'))
			);
			$this->session->set_userdata("default_company", $default_company);
			$this->accounting->setCompany($default_company['id']);
		}

		$data['export'] = $this->_getJobCount('Export');
		$data['import'] = $this->_getJobCount('Import');
		$data['invoice'] = $this->_getPendingBilling();
		$data['einvoice'] = $this->_getPendingeInvoice();
		

		$data['page_title'] = 'Dashboard';
		$data['page']       = Auth::isAdmin() ? $this->_class.'/'.Auth::get('username') : $this->_class.'/user';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function getFinYearData($years){


		$response = array();
		$year = explode('_', $years);

		if ($this->input->is_ajax_request()) {

			$sql = "SELECT DATE_FORMAT(J.date, '%b %Y') AS Month,
					SUM(C.amount) AS Purchase, SUM(C.sell_amount) AS Sales
			FROM jobs J
			INNER JOIN costsheets C ON C.job_id = J.id
			WHERE J.date >= ? AND J.date <= ?
			GROUP BY DATE_FORMAT(J.date, '%Y-%m')
			ORDER BY J.date";
			$query = $this->db->query($sql, array($year[0] . '-04-01', $year[1] . '-03-31'));
			$result = $query->result_array();

			$sql1 = "SELECT DATE_FORMAT(I.date, '%b %Y') AS Month,
					SUM(ID.amount) AS Billing
			FROM invoices I
			INNER JOIN invoice_details ID ON ID.invoice_id = I.id
			WHERE I.date >= ? AND I.date <= ?
			GROUP BY DATE_FORMAT(I.date, '%Y-%m')
			ORDER BY I.date";
			$query1 = $this->db->query($sql1, array($year[0] . '-04-01', $year[1] . '-03-31'));
			$result1 = $query1->result_array();

			$months = [
				0 => ['Month' => 'Apr '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				1 => ['Month' => 'May '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				2 => ['Month' => 'Jun '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				3 => ['Month' => 'Jul '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				4 => ['Month' => 'Aug '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				5 => ['Month' => 'Sep '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				6 => ['Month' => 'Oct '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				7 => ['Month' => 'Nov '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				8 => ['Month' => 'Dec '.$year[0], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				9 => ['Month' => 'Jan '.$year[1], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				10 => ['Month' => 'Feb '.$year[1], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				11 => ['Month' => 'Mar '.$year[1], 'Purchase' => 0, 'Sales' => 0, 'Billing' => 0],
				
			];
			
			$sp_categories = array();
			$sp_series = array();

			foreach($months as $month => $m) {
				
				$key = $this->searchForId($m['Month'], $result);
				$key1 = $this->searchForId($m['Month'], $result1);

				$sp_categories[] 		 = $m['Month'];
				$sp_series['Sales'][] 	 = isset($result[$key]) ? $result[$key]['Sales'] : $m['Sales'];
				$sp_series['Purchase'][] = isset($result[$key]) ? $result[$key]['Purchase'] : $m['Purchase'];
				$sp_series['Billing'][]  = isset($result1[$key1]) ? $result1[$key1]['Billing'] : $m['Billing'];
			}

			$response['sp_categories'] = $sp_categories;
			$response['sp_series']     = $sp_series;
			$response['sp_page_title'] = 'Sales - Purchase Report';
			$response['sp_sub_title']  = Settings::get('company_name');
		}
		else
			echo "Access Denied";

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_NUMERIC_CHECK);
		
	}

	function searchForId($id, $array) {
	   foreach ($array as $key => $val) {
	       if ($val['Month'] === $id) {
	           return $key;
	       }
	   }
	   return null;
	}

	function theme($theme) {
		Settings::create("theme", $theme);
		Settings::reload();

		redirect($this->agent->referrer());
	}

	function login() {
		$data = array(
			'company_name' => Settings::get('company_name'),
			'company_logo' => base_url('images/logo.png'),
			'auth_version' => 3,
			'remember_me'  => $this->input->post('remember_me'),
		);
		if (Auth::login($data, FALSE) === TRUE) {
			redirect('main');
		}
	}
	
	function logout() {
		Auth::logout();
		delete_cookie('remember_me');
		redirect('main/login');
	}

	function default_company($company_id = 0, $fy_year = null) {
		$default_company = $this->session->userdata("default_company");

		if ($this->input->post('company_id'))
			$company_id = $this->input->post('company_id');
		else if (intval($company_id) > 0)
			$company_id = intval($company_id);
		else
			$company_id = $default_company['id'];

		if ($this->input->post('financial_year'))
			$financial_year = $this->input->post('financial_year');
		else if ($fy_year != null)
			$financial_year = $fy_year;
		else
			$financial_year = $default_company['financial_year'];

		if ($company_id > 0 && 
			($company_id != $default_company['id'] OR 
			$financial_year != $default_company['financial_year'])) {

			$row = $this->kaabar->getRow('companies', $company_id);
			$default_company = array(
				'id'             => $row['id'], 
				'code'           => $row['code'],
				'name'           => $row['name'],
				'gst_no'         => $row['gst_no'],
				'financial_year' => $financial_year
			);
			$this->session->set_userdata("default_company", $default_company);
			$this->accounting->setCompany($company_id);
			setSessionAlert('Company &amp; Financial Year Changed Successfully.', 'success');
			redirect($this->agent->referrer());
		}

		$rows = $this->kaabar->getRows('companies');
		$companies = array();
		foreach ($rows as $r) {
			$companies[$r['id']] = $r['code'] . ' - ' . $r['name'];
		}
		$years = array();
		for ($y = date('Y')-5; $y <= date('Y'); $y++) {
			$years[$y . '_' . ($y+1)] = $y . ' - ' . ($y+1);
		}

		$data['company_id']     = $company_id;
		$data['financial_year'] = $default_company['financial_year'];
		$data['companies']      = $companies;
		$data['years'] 	        = $years;
		$data['page']           = 'default_company';
		$data['page_title']     = humanize($data['page']);
		$this->load->view('index', $data);
	}

	function settings() {
		if(! Auth::isValidUser()) {
			redirect('main/login');
		}
		if ($this->input->post('value')) {
			$values = $this->input->post('value');
			if (is_array($values)) {
				foreach($values as $id => $value) {
					Settings::set($id, (is_array($value) ? implode(',', $value) : $value));
				}
				Settings::reload();
				setSessionAlert('Changes saved successfully', 'success');
			}
		}
		
		$data['settings'] = Settings::getUser();

		$default_company = $this->session->userdata("default_company");
		$rows = $this->kaabar->getRows('companies');
		$companies = array();
		foreach ($rows as $r) {
			$companies[$r['id']] = $r['code'] . ' - ' . $r['name'];
		}
		$data['companies'] = $companies;

		$data['page']       = "settings";
		$data['page_title'] = humanize($data['page']);
		$data['docs_url']   = $this->config->item('docs_url') . 'Settings';
		$this->load->view('index', $data);
	}

	function global_settings () {
		if(! Auth::isAdmin()) {
			setSessionError('Only Admin user is allowed to view this page.');
			redirect('main/settings');
		}

		if ($this->input->post('value') && Auth::isAdmin()) {
			$values = $this->input->post('value');
			if (is_array($values)) {
				foreach($values as $id => $value)
					Settings::set($id, $value);
				Settings::reload();
				setSessionAlert('Changes saved successfully', 'success');
			}
		}
		
		$data['settings'] = Settings::getSystem();
		$data['apiStatus'] = $this->kaabar->getApiStatus();
		$data['defaultSource'] = $this->kaabar->getDefaultSource();
		
		$data['page']       = 'global_settings';
		$data['page_title'] = "Global Settings";
		$data['docs_url']   = $this->config->item('docs_url') . 'Settings';
		$this->load->view('index', $data);
	}

	function backup() {
		$this->load->library('backup');
		$this->backup->run();
	}

	function ajaxMenu() {
		$default_company = $this->session->userdata("default_company");
		$perm = Auth::get('permissions');
		if (isset($perm[$default_company['id']]))
			$perm = $perm[$default_company['id']];
		$menu = $this->config->item('menus');
		$submenu_url = function($result, $submenus, $permission = 0, $parent = false, $display_name = false) use (&$submenu_url, &$perm) {
			foreach ($submenus as $menu => $items) {
				
				if (isset($items['hide'])) 
					continue;

				if (isset($items['url']) AND 
					(Auth::isAdmin() OR ($permission | isset($perm[$menu])))
				) {
					$result[] = ['url' => $items['url'], 'parent' => ($display_name ? $display_name . ' > ' : ''), 'name' => $items['name']];
				}

				if (isset($items['nodes'])) {
					if ($parent)
						$result += $submenu_url(
							$result, 
							$items['nodes'], ($permission | (isset($perm[$menu]) ? $perm[$menu] : 0)), 
							$menu, 
							$display_name .' > ' . $items['name']
						);
					else
						$result += $submenu_url(
							$result, 
							$items['nodes'], ($permission | (isset($perm[$menu]) ? $perm[$menu] : 0)), 
							$menu, 
							$items['name']
						);
				}
			}
			return $result;
		};
		$result = $submenu_url([], $menu);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
	
	function createlogs() {
		$this->load->library('audit');
		$this->audit->createLogs();
		exit;
	}
		
	function _getJobCount($type = 'Import') {
		$year = explode('_', $this->_default_company['financial_year']);
		$sql = "SELECT *
		FROM jobs J
		WHERE J.type = ? AND J.isDeleted = 'No' AND J.date >= ? AND J.date <= ?";
		$query = $this->db->query($sql, array($type, $year[0] . '-04-01', $year[1] . '-03-31'));
		return $query->num_rows();
	}

	function _getPendingBilling() {
		$year = explode('_', $this->_default_company['financial_year']);
		$sql = "SELECT *
		FROM jobs J
		WHERE J.status = ? AND J.isDeleted = 'No' AND J.date >= ? AND J.date <= ?";
		$query = $this->db->query($sql, array('Pending', $year[0] . '-04-01', $year[1] . '-03-31'));
		return $query->num_rows();
	}

	function _getPendingeInvoice() {
		$count = 0;
		$year = explode('_', $this->_default_company['financial_year']);
		$sql = "SELECT *
		FROM invoices I
		WHERE I.isDeleted = ? AND I.date >= ? AND I.date <= ?";
		$query = $this->db->query($sql, array('No', $year[0] . '-04-01', $year[1] . '-03-31'));
		$data = $query->result_array();
		
		foreach ($data as $key => $value) {
			//$einv = $this->kaabar->getRow('einvoices', ['voucher_id' => $value['id'], 'status' => 'ACT']);
			$einv = $this->kaabar->getRow('einvoices', ['voucher_id' => $value['id']]);
			
			if(!$einv)
				$count++;
		}	
		return $count;
	}
	

	function _getDashboardExport($default_company) {
		$data  = array();
		$years = explode('_', $default_company['financial_year']);

		$sql = "SELECT T.Month, SUM(T.Container20) AS Container_20, SUM(T.Container40) AS Container_40
		FROM (
			SELECT J.date, DATE_FORMAT(J.date, '%b %Y') AS Month,
				SUM(IF(CT.size = 20, PC.containers, 0)) AS Container20, 
				SUM(IF(CT.size = 40, PC.containers, 0)) AS Container40
				FROM jobs J INNER JOIN job_containers PC ON J.id = PC.job_id
					INNER JOIN container_types CT ON PC.container_type_id = CT.id
				WHERE J.type = 'Export' AND J.date >= ? AND J.date <= ?
				GROUP BY DATE_FORMAT(J.date, '%b %Y')
				ORDER BY J.date
		) AS T
		GROUP BY T.Month
		ORDER BY T.date";
		$query = $this->db->query($sql, array($years[0].'-04-01', $years[1].'-03-31'));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$data[$row['Month']] = $row;
		}

		$sql = "SELECT T.Month, 
			SUM(T.ContainerJobs) AS ContainerJobs, 
			SUM(T.F20) AS F20, SUM(T.F40) AS F40,
			SUM(T.FC20) AS FC20, SUM(T.FC40) AS FC40,
			SUM(T.C20) AS C20, SUM(T.C40) AS C40
		FROM (
			SELECT J.date, DATE_FORMAT(J.date, '%b %Y') AS Month,
				COUNT(J.id) AS ContainerJobs,
				SUM(IF(J.sub_type = 'Forwarding' AND CT.size = 20, PC.containers, 0)) AS F20,
				SUM(IF(J.sub_type = 'Forwarding' AND CT.size = 40, PC.containers, 0)) AS F40,
				SUM(IF(FIND_IN_SET('Forwarding', J.sub_type) AND FIND_IN_SET('Clearing', J.sub_type) AND CT.size = 20, PC.containers, 0)) AS FC20,
				SUM(IF(FIND_IN_SET('Forwarding', J.sub_type) AND FIND_IN_SET('Clearing', J.sub_type) AND CT.size = 40, PC.containers, 0)) AS FC40,
				SUM(IF(FIND_IN_SET('Clearing', J.sub_type) AND CT.size = 20, PC.containers, 0)) AS C20,
				SUM(IF(FIND_IN_SET('Clearing', J.sub_type) AND CT.size = 40, PC.containers, 0)) AS C40
				FROM jobs J INNER JOIN job_containers PC ON J.id = PC.job_id
					INNER JOIN container_types CT ON PC.container_type_id = CT.id
				WHERE J.type = 'Export' AND J.date >= ? AND J.date <= ?
				GROUP BY DATE_FORMAT(J.date, '%b %Y')
				ORDER BY J.date
		) AS T
		GROUP BY T.Month
		ORDER BY T.date";
		$query = $this->db->query($sql, array($years[0].'-04-01', $years[1].'-03-31'));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$data[$row['Month']]['F20']  = $row['F20'];
			$data[$row['Month']]['F40']  = $row['F40'];
			$data[$row['Month']]['FC20'] = $row['FC20'];
			$data[$row['Month']]['FC40'] = $row['FC40'];
			$data[$row['Month']]['C20']  = $row['C20'];
			$data[$row['Month']]['C40']  = $row['C40'];
		}
		
		return $data;
	}	

	function containers() {

		$containers = array(
			'0' => 'JWWU2087275',
		);

		$query = $this->db->query("SELECT container_no FROM deliveries_stuffings WHERE job_id = 995");
		$find = $query->result_array();
		foreach ($find as $value) {
			foreach ($value as $v) {
				$tmp1[] = $v;
			}
		}
		$tmp = array_intersect($containers, $tmp1);
		$final = array_diff($containers, $tmp);
	
		foreach ($final as $r) {
			
				$rows = array(
					'job_id'            => 995,
					'container_type_id' => 2,
					'container_no'      => $r,
					'stuffing_date'     => '2015-04-03'
				);
				$this->kaabar->save('deliveries_stuffings', $rows);
			
		}
	}
}
