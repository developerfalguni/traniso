<?php

class Document_type extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
		
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('ID', 'Product', 'Type', 'Cargo', 'Compulsory', 'Optional'),
			'class' => array(
				'id'           => 'ID',
				'product_name' => 'Text',
				'type'         => 'Code',
				'cargo_type'   => 'Code',
				'compulsory'   => 'count aligncenter',
				'optional'     => 'count aligncenter'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		//$data['label_class'] = $this->office->getLabelClass();
		
		$data['list']['data'] = $this->office->getDocumentTypes($search);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('product_id', 'Product', 'trim|required');
		$this->form_validation->set_rules('type', 'Type', 'trim|required');
		$this->form_validation->set_rules('cargo_type', 'Cargo Type', 'trim|required');
		
		$row = $this->office->getDocumentType($id);
		if($row == false) {
			$row = array(
				'product_id' => 0,
				'type' => 'Import',
				'cargo_type' => 'Bulk',
				'documents' => array()
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['document_types'] = $this->office->getDocumentTypes();

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
				$sr_nos = $this->input->post('sr_no');
				$new_sr_nos = $this->input->post('new_sr_no');

				if ($sr_nos != null) {
					$names          = $this->input->post('name');
					$codes          = $this->input->post('code');
					$is_compulsorys = $this->input->post('is_compulsory');
					$is_pendings    = $this->input->post('is_pending');
					foreach ($sr_nos as $index => $sr_no) {
						if (! in_array("$index", $delete_ids)) {
							$row = array(
								'product_id'    => $this->input->post('product_id'),
								'type'          => $this->input->post('type'),
								'cargo_type'    => $this->input->post('cargo_type'),
								'sr_no'         => $sr_no,
								'code'          => strtoupper($codes[$index]),
								'name'          => $names[$index],
								'is_compulsory' => (isset($is_compulsorys[$index]) ? 'Yes' : 'No'),
								'is_pending'    => (isset($is_pendings[$index]) ? 'Yes' : 'No')
							);
							$id = $this->kaabar->save($this->_table, $row, array('id' => $index));
						}
					}
				}

				if ($delete_ids != null) {
					foreach ($delete_ids as $index) {
						if ($index > 0) {
							$this->db->delete($this->_table, array('id' => $index));
						}
					}
				}

				if ($new_sr_nos != null) {
					$sr_no          = $this->input->post('new_sr_no');
					$codes          = $this->input->post('new_code');
					$names          = $this->input->post('new_name');
					$is_compulsorys = $this->input->post('new_is_compulsory');
					$is_pendings    = $this->input->post('new_is_pending');
					foreach ($new_sr_nos as $index => $sr_no) {
						if (strlen(trim($names[$index])) > 0) {
							$row = array(
								'product_id'    => $this->input->post('product_id'),
								'type'          => $this->input->post('type'),
								'cargo_type'    => $this->input->post('cargo_type'),
								'sr_no'         => $sr_no,
								'code'          => strtoupper($codes[$index]),
								'name'          => $names[$index],
								'is_compulsory' => (isset($is_compulsorys[$index]) ? 'Yes' : 'No'),
								'is_pending'    => (isset($is_pendings[$index]) ? 'Yes' : 'No')
							);
							$id = $this->kaabar->save($this->_table, $row);
						}
					}
				}
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function loadDocuments($id) {
		if (intval($id) == 0) return;
		
		$row = $this->office->getDocumentType($id);

		header('Content-type: text/xml');
		echo '<taconite>
	<eval><![CDATA[ 
		';
        $i = 1;
        foreach ($row['documents'] as $d) {
        	echo '$("tr.TemplateRow input:eq(0)").val(\'' . $d['sr_no'] . '\');
        $("tr.TemplateRow input:eq(1)").val(\'' . $d['code'] . '\');
        $("tr.TemplateRow input:eq(2)").val(\'' . $d['name'] . '\');
        $("tr.TemplateRow input:eq(3)").val(\'Yes\')' . ($d['is_pending'] == 'Yes' ? '.attr("checked", "checked")' : '.removeAttr("checked")') . ';
        $("tr.TemplateRow input:eq(4)").val(\'Yes\')' . ($d['is_compulsory'] == 'Yes' ? '.attr("checked", "checked")' : '.removeAttr("checked")') . ';
        $("tr.TemplateRow").find(".AddButton").click();
    	';
        }
    echo ']]>
	</eval>
</taconite>';
	}
}
