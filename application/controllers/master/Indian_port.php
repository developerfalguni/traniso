 <?php

class Indian_port extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
		
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$this->_fields = array('id', 'code', 'name', 'unece_code');
		$this->_search = array('code', 'name', 'unece_code');
		
		$this->_data['list'] = array(
			'heading' => array('ID', 'Code', 'Port Name', 'UNECE Code'),
			'class' => array(
				'id' => 'ID',
				'code' => 'Code',
				'name' => 'Text Bold',
				'unece_code' => 'Code'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}

	function edit($id) {
		$this->session->set_userdata('last_edit_page', $this->uri->uri_string());
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Indian Port Name', 'trim|required');
		$this->form_validation->set_rules('unece_code', 'UNECE Code', 'trim');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
			'id' => 0,
			'code' => '',
			'name' => '',
			'unece_code' => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page'] = 'simple_form';
	
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'code' => $this->input->post('code'),
					'name' => $this->input->post('name'),
					'unece_code' => $this->input->post('unece_code')
				);

				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function ajax() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT P.id, P.unece_code, P.name
				FROM $this->_table P 
				WHERE P.name LIKE '%$search%' OR P.unece_code LIKE '%$search%'
				ORDER BY P.name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxICD() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT P.id, P.unece_code, P.name
				FROM $this->_table P 
				WHERE P.name LIKE '%ICD' AND (P.name LIKE '%$search%' OR P.unece_code LIKE '%$search%')
				ORDER BY P.name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxLoad() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT P.id, P.unece_code, P.name
				FROM $this->_table P 
				WHERE P.name NOT LIKE '%ICD' AND (P.name LIKE '%$search%' OR P.unece_code LIKE '%$search%')
				ORDER BY P.name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
}
