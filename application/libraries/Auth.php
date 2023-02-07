<?php

if (! defined('ADMIN')) {
	define('ADMIN', 1);
}
if (! defined('ADMIN_GROUP')) {
	define('ADMIN_GROUP', 1);
}

define ('LOGIN_TRIES', 5);

// Authorization & Authentication Class for user
class Auth {
	const CREATE    = 1;
	const READ      = 2;
	const UPDATE    = 4;
	const DELETE    = 8;
	public static $_ci = '';
	public static $_credential = [];

	function __construct() {
		self::$_ci =& get_instance();
		self::$_credential = self::$_ci->session->credential;
	}

	public static function login($data = [], $return = FALSE) {
		if (self::$_ci->input->post('login_try') > LOGIN_TRIES) {
			setSessionError("Your IP address <b>" . self::$_ci->input->ip_address() . "</b> has been recorded for security reasons.");
			log_message('error', self::$_ci->input->ip_address() . " - Five Invalid Login Attempted.");
			redirect(self::$_ci->uri->uri_string());
		}
		
		self::$_ci->load->library('form_validation');
		self::$_ci->form_validation->set_error_delimiters('', '');
		self::$_ci->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]|max_length[50]');
		self::$_ci->form_validation->set_rules('password', 'Password', 'trim|required|min_length[1]');
		
		$data['show_menu'] = FALSE;
		$data['login_try'] = ['login_try' => self::$_ci->input->post('login_try')+1];
		$data['username']  = self::$_ci->input->post('username');
		$data['password']  = self::$_ci->input->post('password');
		if (self::$_ci->form_validation->run() == FALSE) {

			$est = self::$_ci->kaabar->getField('companies', Settings::get('default_company'), 'id', 'establishment');
			$finYear = self::$_ci->kaabar->getBackFinYear(date('Y-m-d', strtotime($est)));
			$years   = explode('_', $finYear);
			$firstYear = $years[0];

			$currfinYear = self::$_ci->kaabar->getFinYear();
			$currfinYears   = explode('_', $currfinYear);

			for($year = $firstYear; $year <= date('Y'); $year++) {
				if($year == $currfinYears[1])
					break;
				$yearsList[] = [$year.'_'.($year+1) => $year.'_'.($year+1)];
			}

			$data['yearsList'] = self::$_ci->kaabar->custom_filter($yearsList);
			$data['currfinYear'] = $currfinYear;

			if (strlen(validation_errors()) > 0) setSessionError(validation_errors());
		}
		else {
			if (isset($data['auth_version']) && $data['auth_version'] == 3) {
				$result = self::check3(self::$_ci->input->post('username'), self::$_ci->input->post('password'));
				if ($result === TRUE) {
					if ($data['remember_me']) {
						$date = new DateTime();
						$date->add(new DateInterval('P7D'));
						$encrypted = openssl_encrypt($data['username'], 'aes128', self::$_ci->config->item('encryption_key'), 0, 1123581321345589);
						set_cookie('remember_me', $encrypted, 604800);
					}
					return TRUE;
				}
				elseif ($result == -1)
					setSessionError('You don&rsquo;t have permission to access from internet.');
				else
					setSessionError('Invalid username / password entered');
			}
			else if (isset($data['auth_version']) && $data['auth_version'] == 2) {
				$result = self::check2(self::$_ci->input->post('username'), self::$_ci->input->post('password'));
				if ($result === TRUE) {
					if ($data['remember_me']) {
						$date = new DateTime();
						$date->add(new DateInterval('P7D'));
						$encrypted = openssl_encrypt($data['username'], 'aes128', self::$_ci->config->item('encryption_key'), 0, 1123581321345589);
						set_cookie('remember_me', $encrypted, 604800);
					}
					return TRUE;
				}
				elseif ($result == -1)
					setSessionError('You don&rsquo;t have permission to access from internet.');
				else
					setSessionError('Invalid username / password entered');
			}
			else {
				$result = self::check(self::$_ci->input->post('username'), self::$_ci->input->post('password'));
				if ($result === TRUE)
					return TRUE;
				elseif ($result == -1)
					setSessionError('You don&rsquo;t have permission to access from internet.');
				else
					setSessionError('Invalid username / password entered');
			}
		}

		$data['page_title'] = 'Login';
		$data['index_page'] = (isset($data['index_page']) ? $data['index_page'] : 'auth/login');
		$data['docs_url']   = self::$_ci->config->item('docs_url').'Login';
		return self::$_ci->load->view($data['index_page'], $data, $return);
	}

	public static function check($username, $password) {
		$access    = 'Yes';
		$server_ip = explode(".", $_SERVER['SERVER_ADDR']);
		$user_ip   = explode(".", self::$_ci->input->ip_address());
		if (in_array($server_ip[0], [10, 127, 172, 192]) AND $server_ip[0] == $user_ip[0])
			$access = 'No';

		$sql = "SELECT id, username, fullname, created, internet_access,
			DATE_FORMAT(last_login, '%b %d, %Y %h:%i %p') as last_login
		FROM users
		WHERE username = '$username' AND 
			password = '" . self::_addSalt($password) . "' AND
			(status = 'Active' OR status = 'Employed')";
		$query = self::$_ci->db->query($sql);
		if($query->num_rows() == 1) {
			$credential = $query->row_array();
			$user['credential'] = $credential;
			$user['credential']['password'] = $password;

			if ($credential['internet_access'] == 'No' && $access == 'Yes') {
				return -1;
			}

			$user['credential']['current_login'] = date("Y-m-d H:i:s");
			$cur_login = ['last_login' => $user['credential']['current_login']];
			self::$_ci->db->update('users', $cur_login, ['id' => $credential['id']]);

			$sql = "SELECT name, permission 
				FROM user_groups
					INNER JOIN group_content ON user_groups.group_id = group_content.group_id
					INNER JOIN contents ON group_content.content_id = contents.id
				WHERE user_id = " . $credential['id'];
			$query = self::$_ci->db->query($sql);

			//$user['credential']['permissions'] = $query->result_array();
			$rows = $query->result_array();
			$user['credential']['permissions'] = [];
			foreach ($rows as $row) {
				$user['credential']['permissions'][$row['name']] = $row['permission'];
			}

			self::$_ci->session->set_userdata($user);
			return TRUE;
		}

		return FALSE;
	}
	
	public static function check2($username, $password) {
		$access    = 'Yes';
		$server_ip = explode(".", $_SERVER['SERVER_ADDR']);
		$user_ip   = explode(".", self::$_ci->input->ip_address());
		if (in_array($server_ip[0], [10, 127, 172, 192]) AND $server_ip[0] == $user_ip[0])
			$access = 'No';

		$sql = "SELECT id, username, fullname, internet_access
		FROM users
		WHERE username = ? AND password = ? AND 
		(status = 'Active' OR status = 'Employed')";
		$query = self::$_ci->db->query($sql, [$username, self::_addSalt($password)]);
		if ($query->num_rows() == 1) {
			$credential = $query->row_array();
			self::$_credential = $credential;
			self::$_credential['password'] = $password;

			if ($credential['internet_access'] == 'No' && $access == 'Yes') {
				return -1;
			}

			self::$_ci->db->update('users', ['last_login' => date("Y-m-d H:i:s")], ['id' => $credential['id']]);

			$sql = "SELECT P.company_id, C.url, P.permission 
			FROM permissions P INNER JOIN contents C ON P.content_id = C.id
			WHERE P.user_id = ?";
			$query = self::$_ci->db->query($sql, [$credential['id']]);
			$rows = $query->result_array();
			self::$_credential['permissions'] = [];
			foreach ($rows as $row) {
				self::$_credential['permissions'][$row['company_id']][$row['url']] = $row['permission'];
			}

			self::$_ci->session->credential = self::$_credential;
			return TRUE;
		}

		return FALSE;
	}

	public static function check3($username, $password, $auto_login = false) {
		$access    = 'Yes';
		$server_ip = explode(".", $_SERVER['SERVER_ADDR']);
		$user_ip   = explode(".", self::$_ci->input->ip_address());
		if (in_array($server_ip[0], [10, 127, 172, 192]) AND $server_ip[0] == $user_ip[0])
			$access = 'No';

		$isOK = false;
		if ($auto_login) {
			$sql = "SELECT * FROM users WHERE username = ? AND status = 'Active'";
			$credential = self::$_ci->db->query($sql, [$username])->row_array();
			if (isset($credential['id']))
				$isOK = true;
		}
		else {
			// $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND status = 'Active'";
			// $query = self::$_ci->db->query($sql, [$username, self::_addSalt($password)]);
			$sql = "SELECT * FROM users WHERE username = ? AND status = 'Active'";
			$credential = self::$_ci->db->query($sql, [$username])->row_array();
			if (isset($credential['id']) AND strlen($credential['password']) <= 32 AND $credential['password'] == self::_addSalt($password))
				$isOK = true;
			else if (isset($credential['id']) AND password_verify($password, $credential['password']))
				$isOK = true;
		}
		if ($isOK) {
			self::$_credential = $credential;

			if ($credential['internet_access'] == 'No' && $access == 'Yes') {
				return -1;
			}

			$cur_login = ['last_login' => date("Y-m-d H:i:s")];
			self::$_ci->db->update('users', $cur_login, ['id' => $credential['id']]);

			$sql  = "SELECT P.* FROM permissions P INNER JOIN user_groups UG ON P.group_id = UG.group_id WHERE P.user_id = 0 AND UG.user_id = ?
			UNION
			SELECT P.* FROM permissions P WHERE P.group_id = 0 AND P.user_id = ?";
			$rows = self::$_ci->db->query($sql, [$credential['id'], $credential['id']])->result_array();
			
			self::$_credential['permissions'] = [];

			$flatten_perm = function($result, $submenus) use (&$flatten_perm) {
				foreach ($submenus as $menu => $items) {
					if (isset($items['permissions'])) {
						if (isset($result[$menu]))
							$result[$menu] |= $items['permissions'];
						else
							$result[$menu] = $items['permissions'];
					}
					else {
						if (! isset($result[$menu]))
							$result[$menu] = 0;
					}

					if (isset($items['nodes'])) {
						$result += $flatten_perm($result, $items['nodes']);
					}
				}
				return $result;
			};

			foreach ($rows as $row) {
				if (isset(self::$_credential['permissions'][$row['company_id']]))
					self::$_credential['permissions'][$row['company_id']] = $flatten_perm(
						self::$_credential['permissions'][$row['company_id']],
						json_decode($row['permissions'], true)
						);
				else
					self::$_credential['permissions'][$row['company_id']] = $flatten_perm(null, json_decode($row['permissions'], true));
			}


			$default = self::$_ci->kaabar->getRow('companies', Settings::get('default_company', Settings::get('default_company')));
			$default_company = array(
				'id'             => $default['id'], 
				'branch'         => Settings::get('default_branch'), 
				'code'           => $default['code'],
				'name'           => $default['name'],
				'gst_no'         => $default['gst_no'],
				'financial_year' => $string = str_replace("-", "_", self::$_ci->input->post('login_finyear'))
			);

			self::$_ci->session->default_company = $default_company;
			self::$_ci->session->credential = self::$_credential;
			
			return TRUE;
		}

		return FALSE;
	}

	public static function _getFinancialYear($date) {
		$d = 0; $m = 0; $y = 0;
		
		if ($date == "0000-00-00" OR $date == "00-00-0000") {
			return self::$_ci->session->userdata("financial_year");
		}
		elseif (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/", $date, $regs)) {
			$d = $regs[1]; $m = $regs[2]; $y = $regs[3];
		}
		elseif (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $date, $regs)) {
			$d = $regs[1]; $m = $regs[2]; $y = $regs[3];
		}
		elseif (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs)) {
			$d = $regs[3]; $m = $regs[2]; $y = $regs[1];
		}
		elseif (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $date, $regs)) {
			$d = $regs[3]; $m = $regs[2]; $y = $regs[1];
		}
		
		if ($m < 4) return ($y-1) . '_' . $y;
		else 		return $y . '_' . ($y+1);
	}

	public static function autoLogin($username) {
		$sql = "SELECT id, username, fullname, created, DATE_FORMAT(last_login, '%b %d, %Y %h:%i %p') as last_login
		FROM users
		WHERE username = ? AND status = 'Active'";
		$query = self::$_ci->db->query($sql, [$username]);
		if ($query->num_rows() == 1) {
			$credential = $query->row_array();
			return self::check3($credential['username'], FALSE, TRUE);
		}

		return FALSE;
	}

	public static function hasAccess($permission = self::READ, $company_id = 0) {
		$hasPerm = FALSE;

		if (self::isAdmin())
			return TRUE;

		if ($company_id == 0) {
			$company = self::$_ci->session->default_company;
			$company_id = $company['id'];
		}

		if (! isset(self::$_credential['permissions'][$company_id])) 
			return FALSE;

		// Check for URI permission in reverse order.
		$segs = self::$_ci->uri->segment_array();
		$id = array_search("index", $segs);  unset($segs[$id]);
		$id = array_search("edit", $segs); 	 unset($segs[$id]);
		$id = array_search("delete", $segs); unset($segs[$id]);
		$content = '';
		while(count($segs) > 0) {
			$content = implode('/', $segs);
			// Exists in Array
			if (in_array($content, array_keys(self::$_credential['permissions'][$company_id]))) {
				// No Permission
				if ((self::$_credential['permissions'][$company_id][$content] & $permission) == $permission) {
					$hasPerm = TRUE;
					return $hasPerm;
				}
				else {
					$hasPerm = FALSE;
					return $hasPerm;
				}
			}
			array_pop($segs);
		}

		if (in_array("*", array_keys(self::$_credential['permissions'][$company_id]))) {
			if ((self::$_credential['permissions'][$company_id]["*"] & $permission) == $permission) {
				$hasPerm = TRUE;
			}
		}
		return $hasPerm;
	}

	public static function logout() {
		self::$_credential = FALSE;
		self::$_ci->session->sess_destroy();
	}
	
	public static function isAdmin() {
		if ((self::$_credential != FALSE && self::$_credential['id'] == ADMIN) OR 
			self::isMemberOfGroup(self::$_credential['id'], ADMIN_GROUP)) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public static function isValidUser() {
		return (self::$_credential != FALSE) ? TRUE : FALSE;
	}

	public static function getCurrUID() {
		return (self::$_credential != FALSE) ? self::$_credential['id'] : 0;
	}

	public static function get($field) {
		if (self::$_credential != FALSE) {
			return self::$_credential[$field];
		}
		else {
			return null;
		}
	}

	public static function userExists($username) {
		$userfound = FALSE;

		$query = self::$_ci->db->query("SELECT username FROM users WHERE username = ?" , [$username]);
		if ($query->num_rows() == 1) {
			$userfound = TRUE;
		}

		$query = self::$_ci->db->query("SELECT username FROM new_users WHERE username = ?" , [$username]);
		if ($query->num_rows() == 1) {
			$userfound = TRUE;
		}

		return $userfound;
	}

	public static function hasPermission($permission = self::READ) {
		$hasPerm = FALSE;

		if (self::isAdmin()) {
			$hasPerm = TRUE;
			return $hasPerm;
		}

			// Check for URI permission in reverse order.
		$segs = self::$_ci->uri->segment_array();
		$content = '';
		while(count($segs) > 0) {
			$content = implode('/', $segs);
				// Exists in Array
			if (in_array($content, array_keys(self::$_credential['permissions']))) {
					// No Permission
				if ((self::$_credential['permissions'][$content] & $permission) == $permission) {
					$hasPerm = TRUE;
					return $hasPerm;
				}
				else {
					$hasPerm = FALSE;
					return $hasPerm;
				}
			}
			array_pop($segs);
		}

		if (in_array("*", array_keys(self::$_credential['permissions']))) {
			if ((self::$_credential['permissions']["*"] & $permission) == $permission) {
				$hasPerm = TRUE;
			}
		}
		return $hasPerm;
	}

	public static function saveNewUser($newuser) {
		$newuser['username']  = $newuser['username'];
		$newuser['password']  = self::_addSalt($newuser['password']);
		$newuser['auth_code'] = self::_addSalt(date('U'));
		self::$_ci->db->insert('new_users', $newuser);
		return TRUE;
	}

	public static function activateNewUser($newuid = null, $auth_code = null, $default_group = null) {
		if ($newuid == null && $auth_code == null) { return FALSE; }
		elseif ($newuid != null) 	{ $condition = ['id' => $newuid]; }
		elseif ($auth_code != null)	{ $condition = ['auth_code' => $auth_code]; }

		$query = self::$_ci->db->get_where('new_users', $condition);
		if ($query->num_rows() == 0) {
			return FALSE;
		}
		$data = $query->row_array();
		unset($data['id']);
		unset($data['auth_code']);
		$data['status'] = 'Active';

			// Save User and get UID
		self::$_ci->db->insert('users', $data);
		$id = self::$_ci->db->insert_id();

			/*if (is_null($default_group)) {
				self::$_ci->db->insert('groups', ['name' => $data['username']]);
				$default_group = $data['username'];
			}*/

			// Save / Search Group and get GID
			/*if (is_null($default_group)) 
				$group_name = $data['username'];
			else 
				$group_name = $default_group;

			self::$_ci->db->select('id');
			$query = self::$_ci->db->get_where('groups', ['name' => $group_name]);
			$row = $query->row_array();
			if ($query->num_rows() > 0) {
				$gid = $row['id'];
			}
			else {
				self::$_ci->db->insert('groups', ['name' => $group_name]);
				$gid = self::$_ci->db->insert_id();
			}
			
			// Insert Relation  Used ID - Group ID
			self::$_ci->db->insert('user_groups', ['user_id' => $id, 'group_id' => $gid]);
			*/

			// Delete From NewUser Table
			self::$_ci->db->delete('new_users', $condition);
			
			return TRUE;
	}
	
	public static function _addSalt($password, $salt = '9h0$10f$6@r1@') {
		return md5(md5($salt) . md5($password));
	}
	
	public static function changePassword($id, $oldpassword, $newpassword) {
		$user = self::$_ci->db->query("SELECT * FROM users WHERE id = ?", [$id])->row_array();
		$sql = "SELECT * FROM users WHERE username = ? AND status = 'Active'";
		if (strlen($user['password']) <= 32 AND $user['password'] == self::_addSalt($oldpassword)) {
			self::$_ci->db->update('users', ['password' => password_hash($newpassword, PASSWORD_BCRYPT)], ['id' => $id]);
			return 1;
		}
		else if (password_verify($oldpassword, $user['password'])) {
			self::$_ci->db->update('users', ['password' => password_hash($newpassword, PASSWORD_BCRYPT)], ['id' => $id]);
			return 1;
		}
		return 0;
	}

	public static function resetPassword($id, $newpassword = 'navin') {
		// self::$_ci->db->update('users', ['password' => self::_addSalt($newpassword), 'status' => 'Active'], ['id' => $id]);
		self::$_ci->db->update('users', ['password' => password_hash($newpassword, PASSWORD_BCRYPT)], ['id' => $id]);
		return self::$_ci->db->affected_rows();
	}
	
	public static function generatePassword($length = 4) {
		//$chars = explode(',', 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,0,1,2,3,4,5,6,7,8,9');
		$chars = explode(',', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,0,1,2,3,4,5,6,7,8,9');
		$password = '';
		for($i = 0; $i < $length; $i++) {
			$password .= $chars[rand(0,count($chars)-1)];
		}
		return $password;
	}
	
	public static function isMemberOfGroup($user_id, $group_id) {
		self::$_ci->db->select("user_groups.id AS ugid, groups.id, name");
		self::$_ci->db->join('user_groups', "group_id = groups.id", 'inner');
		$query = self::$_ci->db->get_where('groups', ["user_id" => $user_id, "group_id" => $group_id]);
		if ($query->num_rows() == 0)
			return FALSE;
		return TRUE;
	}

	public static function memberOfGroups($id) {
		$groups = [];
		self::$_ci->db->select("user_groups.id AS ugid, groups.id, name");
		self::$_ci->db->join('user_groups', "group_id = groups.id", 'inner');
		$query = self::$_ci->db->get_where('groups', ["user_id" => $id]);
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$groups[$row['id']] = $row['name'];
		}
		return $groups;
	}
	
	public static function getAvailableGroups($id) {
		$sql = "SELECT id, name FROM groups WHERE id NOT IN (SELECT group_id FROM user_groups WHERE user_id = ?) ORDER BY name";
		$query = self::$_ci->db->query($sql, [$id]);
		$rows = $query->result_array();
		$groups = [];
		foreach ($rows as $key => $value) {
			$groups[$value['id']] = $value['name'];
		}
		return $groups;
	}
	
	public static function sendAuthCodeEmail($newuser = null) {
		if (is_null($newuser) && ! is_array($user)) { return FALSE; }

		$to = $newuser['email'];
		$subject = "Confirmation of Email";
		$message = self::$_ci->load->view('auth/email_authcode', $newuser, TRUE);
		// $headers  = "MIME-Version: 1.0\r\n";
		// $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers = "From: donotreply";
		return mail($to, $subject, $message, $headers);
	}

	public static function sendActivatedEmail($user = null) {
		if (is_null($user) && ! is_array($user)) { return FALSE; }

		$to = $user['email'];
		$subject = "Account Activation";
		$message = self::$_ci->load->view('auth/email_welcome', $user, TRUE);
		$headers = "From: donotreply";
		return mail($to, $subject, $message, $headers);
	}
	
	public static function getCounts() {
		$data = [];
		
		$sql = "SELECT COUNT(id) AS cnt FROM new_users";
		$query = self::$_ci->db->query($sql);
		$row = $query->row_array();
		$data['new'] = $row['cnt'];
		
		$sql = "SELECT COUNT(id) AS cnt FROM users WHERE status = 'Active'";
		$query = self::$_ci->db->query($sql);
		$row = $query->row_array();
		$data['active'] = $row['cnt'];
		
		/* $sql = "SELECT COUNT(*) AS cnt FROM ci_sessions";
		$query = self::$_ci->db->query($sql);
		$row = $query->row_array();
		$data['logged'] = $row['cnt']; */
		$data['logged'] = 0;
		
		$sql = "SELECT COUNT(id) AS cnt FROM users WHERE status = 'Suspended'";
		$query = self::$_ci->db->query($sql);
		$row = $query->row_array();
		$data['suspended'] = $row['cnt'];
		
		$sql = "SELECT COUNT(id) AS cnt FROM users WHERE status = 'Disabled'";
		$query = self::$_ci->db->query($sql);
		$row = $query->row_array();
		$data['disabled'] = $row['cnt'];
		
		return $data;
	}
}
