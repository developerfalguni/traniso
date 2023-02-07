<?php

class Permission extends MY_Controller {
	function __construct() {
		parent::__construct();

		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}
	}
	
	function index($user_id = 1) {
		$permissions = $this->input->post('permission');
		$permissions2keep = [];
		if ($permissions != null) {
			foreach ($permissions as $company_id => $contents) {
				foreach ($contents as $content_id => $permission) {
					$query = $this->db->query("SELECT id FROM permissions 
						WHERE user_id = ? AND company_id = ? AND content_id = ?", 
						[$user_id, $company_id, $content_id]);
					$row = $query->row();
					if ($row) {
						$this->db->update('permissions', ['permission' => array_sum($permission)], ['id' => $row->id]);
						$permissions2keep[] = $row->id;
					}
					else
						$this->db->insert('permissions', [
							'user_id'    => $user_id, 
							'company_id' => $company_id, 
							'content_id' => $content_id, 
							'permission' => array_sum($permission)
						]);
						$permissions2keep[] = $this->db->insert_id();
				}
			}
			if (count($permissions2keep) > 0)
				$this->db->query("DELETE FROM permissions WHERE user_id = ? AND id NOT IN (" . join(',', $permissions2keep) . ")", [$user_id]);
		}

		$default_company     = $this->session->userdata('default_company');

		$data['company']     = $default_company;
		$data['companies']   = $this->kaabar->getNameValuePair('companies', null, 'id', 'id', 'code');
		$data['user_id']     = $user_id;
		$data['name']        = $this->kaabar->getField('users', $user_id, 'id', 'fullname');
		$data['users']       = $this->kaabar->getRows('users', 'Active', 'status', '*', 'username');
		$data['permissions'] = $this->_getPermission($user_id, $default_company['id']);

		$data['javascript'] = ['/vendors/js/jquery.filtertable.min.js', '/vendors/js/jquery.serialize-object.js'];

		$data['page_title'] = humanize($this->_class);
		$data['page'] = $this->_clspath.$this->_class.'_edit';

		$this->load->view('index', $data);
	}

	function userList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('username', $this->input->get("q"));
			}
			
			$query = $this->db->select('id, username as text')
							->limit(10)
							->get('users');
							
			$json = $query->result_array();
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
   }

	function _getPermission($user_id, $company_id) {
		$row = $this->db->query("SELECT * FROM permissions WHERE user_id = ? AND company_id = ?", [$user_id, $company_id])->row_array();
		if ($row)
			return json_decode($row['permissions'], true);
		return [];
	}

	function edit() {
		$user_id    = $this->input->post('user_id');
		$company_id = $this->input->post('company_id');
		$companies  = ($this->input->post('companies') ? $this->input->post('companies') : []);
		$users      = ($this->input->post('users') ? $this->input->post('users') : []);
		$perm       = json_decode(str_replace('ZZ', '/', $this->input->post('perm')), true);

		$submenu = function ($submenus, $parent = false) use (&$submenu, &$perm) {
			foreach ($submenus as $menu => $items) {
				if (isset($items['permissions'])) {
					if ($parent)
						eval('$perm[\''.join('\'][\'', explode(',', $parent)).'\'][\'nodes\'][\''.$menu.'\'][\'permissions\'] = ' . array_sum($items['permissions']) . ';');
					else
						eval('$perm[\''.$menu.'\'][\'permissions\'] = ' . array_sum($items['permissions']) . ';');
				}

				if (isset($items['nodes'])) {
					if ($parent)
						$submenu($items['nodes'], $parent.',nodes,'.$menu);
					else
						$submenu($items['nodes'], $menu);
				}
			}
		};
		$submenu($perm);

		// Saving for Single User in $user_id
		$row = $this->db->query("SELECT id FROM permissions WHERE user_id = ? AND company_id = ?", [$user_id, $company_id])->row_array();
		if ($row) {
			$row['permissions'] = json_encode($perm);
			$this->db->update('permissions', $row, ['id' => $row['id']]);
		}
		else 
			$this->db->insert('permissions', ['user_id' => $user_id, 'company_id' => $company_id, 'permissions' => json_encode($perm)]);

		// Saving for $user_id in $companies
		foreach ($companies as $company_id) {
			$row = $this->db->query("SELECT id FROM permissions WHERE user_id = ? AND company_id = ?", [$user_id, $company_id])->row_array();
			if ($row) {
				$row['permissions'] = json_encode($perm);
				$this->db->update('permissions', $row, ['id' => $row['id']]);
			}
			else 
				$this->db->insert('permissions', ['user_id' => $user_id, 'company_id' => $company_id, 'permissions' => json_encode($perm)]);
		}

		// Saving for $users in $companies
		foreach ($users as $user_id) {
			foreach ($companies as $company_id) {
				$row = $this->db->query("SELECT id FROM permissions WHERE user_id = ? AND company_id = ?", [$user_id, $company_id])->row_array();
				if ($row) {
					$row['permissions'] = json_encode($perm);
					$this->db->update('permissions', $row, ['id' => $row['id']]);
				}
				else 
					$this->db->insert('permissions', ['user_id' => $user_id, 'company_id' => $company_id, 'permissions' => json_encode($perm)]);
			}
		}


		echo json_encode(['status' => 'OK']);
	}
}
