<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class State extends MY_Controller {
	function __construct() {
		parent::__construct();
		
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
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
		
		$data['list']['heading'] = array('ID', 'Code', 'Name', 'Union Territory');
		$data['list']['class'] = array(
			'id'              => 'ID', 
			'code'            => 'Code',
			'name'            => 'Text',
			'union_territory' => 'Code');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_count($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}

	function _count($search = '') {
		$sql = "SELECT COUNT(S.id) AS numrows
		FROM states S
		WHERE (S.name LIKE '%$search%')";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _get($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT S.id, S.code, S.name, S.union_territory
		FROM states S
		WHERE (S.name LIKE '%$search%')
		ORDER BY S.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function edit($id = 0) {
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'State Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'              => 0,
				'code'            => '',
				'name'            => '',
				'union_territory' => 'No',
			);

			if ($this->input->method() == 'post') {
				$post = $this->input->post(array_keys($row));
				foreach($post as $f => $v) {
					if ($v) $row[$f] = $v;
				}
			}
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
				
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['docs_url']   = $this->_docs;
			$data['page_title'] = humanize($this->_class);
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			$data = array(
				'code'            => strtoupper($this->input->post('code')),
				'name'            => strtoupper($this->input->post('name')),
				'union_territory' => $this->input->post('union_territory'),
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);

			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class);
		}
	}

	function ajax() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT S.*
				FROM states S
				WHERE S.name LIKE '%$search%'
				ORDER BY S.name";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo "Access Denied";
		}
	}
}
