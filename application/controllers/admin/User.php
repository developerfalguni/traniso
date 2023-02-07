<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		// if (! Auth::isAdmin()) {
		// 	setSessionError('You don&rsquo;t have enough permission');
		// 	redirect('main');
		// }

		// $starting_row = intval($starting_row);
		
		// $search = addslashes($this->input->post('search'));
		// if ($search == false && $this->input->post('search_form')) {
		// 	$this->session->unset_userdata($this->_class.'_search'); 
		// 	$search = false;
		// }
		// if ($search && $search != $this->session->userdata($this->_class.'_search')) {
		// 	$this->session->set_userdata($this->_class.'_search', $search);
		// 	redirect($this->_clspath.$this->_class);
		// }
		// else {
		// 	$search = $this->session->userdata($this->_class.'_search');
		// }
		// $data['search'] = $search;
		// $data['show_search'] = true;
		
		// $data['list']['heading'] = array('ID', 'Created', 'Username', 'Full Name', 'Email', 'Net Access', 'Status', 'Roles');
		// $data['list']['class'] = array(
		// 	'id'              => 'Text', 
		// 	'created'         => 'Text',
		// 	'username'        => 'Text', 
		// 	'fullname'        => 'Text', 
		// 	'email'           => 'Text', 
		// 	'internet_access' => 'Label',
		// 	'status'          => 'Label',
		// 	'roles'           => 'Text');
		// $data['list']['link_col'] = "id";
		// $data['list']['link_url'] = $this->_clspath.$this->_class.'/edit/';
		// $data['label_class'] = [
		// 	'No'  => 'label-danger',
		// 	'Yes' => 'label-success',

		// 	'Active'    => 'label-success',
		// 	'Suspended' => 'label-warning',
		// 	'Disabled'  => 'label-danger'
		// ];
		
		// $this->load->library('pagination');
		// $config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		// $config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		// $config['total_rows']  = $this->_count($search);
		// $config['per_page']    = Settings::get('rows_per_page');
		// $this->pagination->initialize($config);

		// $data['list']['data'] = $this->_get($search, $config['per_page'], $starting_row);
		
		// $data['page_title'] = humanize($this->_class);
		// $data['page']       = 'list';
		// $this->load->view('index', $data);
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getUser($user_id = 0){

		$response = [];
		
		if($user_id > 0){

			$row = $this->kaabar->getRow('users', $user_id);

			$response['success'] = true;
			$response['row'] = $row;
			
		}
		else
		{
			$row = $this->kaabar->getRow('users', $user_id);
			$response['success'] = false;
			$response['messages'] = 'User No Required';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function edit($id = 0) {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}

		$id = intval($id);
		
		$row = $this->kaabar->getRow($this->_table, $id);
		// if ($row == FALSE) {
		// 	redirect($this->_clspath.$this->_class);
		// }
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('fullname', 'Full Name', 'trim|required');
		
		$data['id']  = ['id' => $id];
		$data['row'] = $row;
		$data['member_of'] = $this->_memberOfGroups($id);
		$data['available_group'] = $this->_getAvailableGroups($id);
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['random_password'] = Auth::generatePassword();
			$this->session->set_userdata('reset_password', $data['random_password']);
			
			$data['page_title'] = humanize($this->_class).' Master';
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			$row = array(
				'username'        => $this->input->post('username'),
				'fullname'        => $this->input->post('fullname'),
				'email'           => $this->input->post('email'),
				'status'          => $this->input->post('status'),
				'internet_access' => $this->input->post('internet_access')
			);
			$row['last_modified'] = date("Y-m-d H:i:s");
			$this->db->update('users', $row, "id = $id");

			$member_of = array_keys($data['member_of']);
			if ($this->input->post('member_of')) {
				$group_new = array_diff($this->input->post('member_of'), $member_of);
				$group_del = array_diff($member_of, $this->input->post('member_of'));
			}
			else {
				$group_new = array();
				$group_del = $member_of;
			}

			if ($group_new != null) { $this->_addGroups($id, $group_new); }
			if ($group_del != null) { $this->_delGroups($id, $group_del); }

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class.'/edit/'.$id);
		}
	}

	function ajaxEdit() {
		$response = [];
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('fullname', 'Full Name', 'trim|required');
		
		$row_id = $this->input->post('id');
		$data['member_of'] = $this->_memberOfGroups($row_id);
		$data['available_group'] = $this->_getAvailableGroups($row_id);
		
		if ($this->form_validation->run() == false) {

			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
					
			$data['random_password'] = Auth::generatePassword();
			$this->session->set_userdata('reset_password', $data['random_password']);
			
		}
		else {
			$row = array(
				'username'        => $this->input->post('username'),
				'fullname'        => $this->input->post('fullname'),
				'email'           => $this->input->post('email'),
				'status'          => $this->input->post('status'),
				'internet_access' => $this->input->post('internet_access')
			);
			$row['last_modified'] = date("Y-m-d H:i:s");
			$member_of = array_keys($data['member_of']);
			if ($this->input->post('member_of')) {
				$group_new = array_diff($this->input->post('member_of'), $member_of);
				$group_del = array_diff($member_of, $this->input->post('member_of'));
			}
			else {
				$group_new = array();
				$group_del = $member_of;
			}

			$id = $this->kaabar->save($this->_table, $row, ['id' => $row_id]);
			
			if($id){
				$response['success'] = true;
	        	$response['messages'] = 'Succesfully Saved';	
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Somathing Went Wrong';	
			}
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
	
	function change_password() {
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('current_password', 'Current Password', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('new_password', 'New Password' , 'trim|required');
		$this->form_validation->set_rules('retype_password', 'Retype Password', 'trim|required|matches[new_password]');
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
		}
		else {
			$rows_affected = Auth::changePassword(Auth::getCurrUID(),
				$this->input->post('current_password'), $this->input->post('new_password'));
			if($rows_affected == 0) {
				setSessionError('Invalid password entered');
			}
			else {
				setSessionAlert('Password Changed Successfully.', 'success');
			}
		}
		$data['page'] = $this->_clspath.'change_password';
		$data['page_title'] = 'Change Password';
		$this->load->view('index', $data);
	}

	function reset_password($id) {
		if (Auth::isAdmin()) {
			$new_password = $this->session->userdata('reset_password');
			Auth::resetPassword($id, $new_password);
			setSessionAlert('Changes saved successfully', 'success');
		}
		else {
			setSessionAlert('Only admin can reset the Password.');
		}
		redirect($this->_clspath.$this->_class.'/index');
	}

	function retrieve_password() {
		$email_address = $this->input->post('email_address');
		$row = $this->kaabar->getRow('users', $email_address, 'email');
		if ($row) {
			Auth::resetPassword($row['id'], 'empezar');
			setSessionAlert('Check your Email for new Password.', 'success');
		}
		else {
			setSessionError('Invalid Email. Please enter correct email address.');
		}
		
		redirect('main/login');
	}

	function _count($search) {
		$sql = "SELECT COUNT(U.id) AS numrows
		FROM users U 
			LEFT OUTER JOIN (
				SELECT UG.user_id, GROUP_CONCAT(G.name) AS roles
				FROM user_groups UG
					LEFT OUTER JOIN groups G ON UG.group_id = G.id
				GROUP BY UG.user_id
			) UG ON U.id = UG.user_id
		WHERE U.username LIKE '%$search%' OR
			U.fullname LIKE '%$search%' OR
			U.email LIKE '%$search%' OR
			U.status LIKE '%$search%' OR
			UG.roles LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return $row['numrows'];
	}
	
	function _get($search, $limit, $offset) {
		$sql = "SELECT U.id, DATE_FORMAT(U.created, '%d-%m-%Y %h:%i %p') AS created, U.username, U.fullname, 
			U.email, U.internet_access, U.status, GROUP_CONCAT(G.name) AS roles
		FROM users U
			LEFT OUTER JOIN user_groups UG ON U.id = UG.user_id
			LEFT OUTER JOIN groups G ON UG.group_id = G.id
		WHERE U.username LIKE '%$search%' OR
			U.fullname LIKE '%$search%' OR
			U.email LIKE '%$search%' OR
			U.status LIKE '%$search%' OR
			G.name LIKE '%$search%'
		GROUP BY U.id
		ORDER BY U.username
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function _memberOfGroups($id) {
		$rows = $this->db->query("SELECT UG.*, G.name
			FROM user_groups UG INNER JOIN groups G ON UG.group_id = G.id
			WHERE UG.user_id = ?", [$id])->result_array();
		$groups = [];
		foreach ($rows as $row) {
			$groups[$row['group_id']] = $row['name'];
		}
		return $groups;
	}
	
	function _getAvailableGroups($id) {
		$rows = $this->db->query("SELECT G.id, G.name 
			FROM groups G
			WHERE G.id NOT IN (
				SELECT group_id FROM user_groups WHERE user_id = ?
			)
			ORDER BY G.name", [$id])->result_array();
		$groups = [];
		foreach ($rows as $key => $value) {
			$groups[$value['id']] = $value['name'];
		}
		return $groups;
	}

	function _addGroups($id, $gids) {
		if($id && $gids) {
			foreach ($gids as $gid) {
				$data = ['user_id' => $id, 'group_id' => $gid];
				$this->db->insert('user_groups', $data);
			}
		}
	}
	
	function _delGroups($id, $gids) {
		if($gids) {
			$sql = "DELETE FROM user_groups WHERE user_id = $id AND group_id IN (" . 
			join(",", $gids) . ")";
			$this->db->query($sql);
		}
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
}