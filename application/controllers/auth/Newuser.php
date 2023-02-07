<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newuser extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Username', 'Full Name', 'Email', 'Authorization Code');
		$data['list']['class'] = array(
			'id'        => 'ID', 
			'username'  => 'Code', 
			'fullname'  => 'Text', 
			'email'     => 'Text', 
			'auth_code' => 'Code');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class.'/edit/';
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_count($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);

		$data['list']['data'] = $this->_get($search, $config['per_page'], $starting_row);

		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/create", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = "New User";
		$data['page']       = 'list';
		$this->load->view('index', $data);
	}

	function _count($search) {
		$sql = "SELECT COUNT(id) AS numrows FROM new_users 
		WHERE username LIKE '%$search%' OR fullname LIKE '%$search%' OR email LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return $row['numrows'];
	}
	
	function _get($search, $limit, $offset) {
		$sql = "SELECT * FROM new_users 
		WHERE username LIKE '%$search%' OR fullname LIKE '%$search%' OR email LIKE '%$search%'
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function create() {
		// First, delete old captchas
		$expiration = time()-300; // Two hour limit
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);

		$this->load->helper('captcha');
		$vals = array(
			'word'          => Auth::generatePassword(6),
			'img_path'      => './captcha/',
			'img_url'       => base_url().'captcha/',
			'font_path'     => BASEPATH.'fonts/texb.ttf',
			'img_width'     => 180,
			'img_height'    => 35,
			'expiration'    => 300,
			'word_length'   => 5,
			'font_size'     => 16,
			'img_id'        => 'Imageid',
			'pool'          => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',

			// White background and border, black text and red grid
			'colors'        => array(
				'background' => array(255, 255, 255),
				'border'     => array(255, 255, 255),
				'text'       => array(0, 0, 0),
				'grid'       => array(255, 40, 40)
			)
		);

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]|alpha_numeric|callback__is_unique');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('retype_password', 'Retype Password', 'trim|required|matches[password]');
		$this->form_validation->set_rules('fullname', 'Fullname', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|min_length[6]');

		// If No Captcha Image.. Skip
		// If Captcha, then check if correct.
		if ($this->input->post('captcha')) {
			// Then see if a captcha exists:
			$sql   = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
			$binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
			$query = $this->db->query($sql, $binds);
			$row   = $query->row();

			if ($row->count == 0) {
				setSessionError('You must submit the word that appears in the image');
				$captcha = FALSE;
			}
			else
				$captcha = TRUE;
		}

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$cap = create_captcha($vals);
			$row = array(
				'captcha_time' => $cap['time'],
				'ip_address'   => $this->input->ip_address(),
				'word'         => $cap['word']
			);
			$query = $this->db->insert_string('captcha', $row);
			$this->db->query($query);

			$data['captcha_image'] = array('captcha_image' => $cap['image']);
			$data['page_title']    = 'New User';
			$data['page']          = $this->_clspath.'newuser';
		}
		else {
			if ($captcha) {
				$newuser = array(
					'username' => strtolower($this->input->post('username')),
					'password' => $this->input->post('password'),
					'fullname' => $this->input->post('fullname'),
					'email'    => strtolower($this->input->post('email'))
				);
				$this->auth->saveNewUser($newuser);

				setSessionAlert('Request for New Account creation is submitted. Check you Email for Authorization Code.', 'success');
				redirect();
			}
			else {
				$cap = create_captcha($vals);
				$row = array(
					'captcha_time' => $cap['time'],
					'ip_address'   => $this->input->ip_address(),
					'word'         => $cap['word']
				);
				$query = $this->db->insert_string('captcha', $row);
				$this->db->query($query);

				$data['captcha_image'] = array('captcha_image' => $cap['image']);
				$data['page_title']    = 'New User';
				$data['page']          = $this->_clspath.'newuser';
			}
		}
		$this->load->view('auth/index', $data);
	}
	
	function _is_unique($username) {
		$this->form_validation->set_message('_is_unique', "The <b>" . $username . "</b> username already exists.");
		return ! $this->auth->userExists($username);
	}

	function edit($id) {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}

		$id = intval($id);
		
		$row = $this->_getNewUser($id);
		if ($row == FALSE) {
			redirect($this->_clspath.$this->_class.'/index');
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('fullname', 'Full Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim');
		
		$data['id']              = ['id' => $id];
		$data['row']             = $row;
		$data['member_of']       = $this->auth->memberOfGroups($id);
		$data['available_group'] = $this->auth->getAvailableGroups($id);
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			$this->auth->activateNewUser($id);
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class.'/edit/'.$id);
		}
	}

	function _getNewUser($id) {
		$query = $this->db->get_where('new_users', ['id' => $id]);
		return $query->row_array();
	}
	
	function send_email($id) {
		$user = $this->auth->getNewUser($id);
		$this->auth->sendAuthCodeEmail($user);
		setSessionAlert('Email to "' . $user['email'] . '" is sent.', 'success');
		redirect($this->_clspath.$this->_class);
	}
	
	function activate($auth_code) {
		if (strlen(trim($auth_code)) > 0) {
			$username = $this->auth->activateNewUser(null, $auth_code);
			setSessionAlert('Account "$username" Activated Successfully.', 'success');
		}
		redirect($this->_clspath.$this->_class);
	}
	
	function delete($id = 0, $field = 'id') {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}
		$this->db->delete('new_users', ['id' => $id]);
		setSessionAlert('User Deleted Successfully', 'success');
		redirect($this->_clspath.$this->_class);
	}
}
