<?php

class Import_detail extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->load->model('import');
	}
	
	function index($job_id = 0) {
		$this->edit($job_id);
	}

	function edit($job_id = 0) {
		if ($job_id <= 0 OR $this->import->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('invoice_value', 'Invoice Value', 'trim|required');
		$this->form_validation->set_rules('assessment_date', 'Assessment Date', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $job_id, 'job_id');
		if($row == false) {
			$row = array(
				'id'                => 0,
				'job_id'            => $job_id,
				'invoice_value'     => 0,
				'iv_currency_id'    => 2,
				'appraisement_date' => '00-00-0000',
				'assessment_date'   => '00-00-0000',
				'exam_date'         => '00-00-0000',
				'payment_date'      => '00-00-0000',
				'ooc_date'          => '00-00-0000',
				'custom_duty'       => 0,
				'cd_voucher_id'     => 0,
				'cd_paid_direct'    => 'No',
				'cd_date'           => '',
				'ppq'               => 0,
				'ppq_voucher_id'    => 0,
				'ppq_paid_direct'   => 'No',
				'ppq_date'          => '',
				'stamp_duty'        => 0,
				'sd_voucher_id'     => 0,
				'wharfage'          => 0,
				'wh_voucher_id'     => 0,
				'wh_rate'           => 0,
				'wh_stax'           => 0,
				'wh_tds'            => 0,
				'line_payment'      => 0,
				'cfs_payment'       => 0,
				'free_days'         => 0,
				// 'original_bl_received' => '',
				'place_of_delivery' => '',
				'remarks'		    => ''
			);
		}
		
		$row['cfs_id']    = $this->kaabar->getField('jobs', $job_id, 'id', 'cfs_id');
		$data['cfs_name'] = $this->kaabar->getField('ledgers', $row['cfs_id']);
		$vessel_id        = $this->kaabar->getField('jobs', $row['job_id'], 'id', 'vessel_id');
		$data['eta_date'] = $this->kaabar->getField('vessels', $vessel_id, 'id', 'eta_date');
		
		$data['id'] = array('id' => $row['id']);
		$data['job_id'] = array('id' => $job_id);
		$data['row'] = $row;
		//$data['ro_row'] = $this->import->getPaymentExtra($job_id, $container_id);
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']              = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['all_vouchers']      = $this->accounting->getJobVouchers($job_id);
			$data['vouchers']          = $this->accounting->getBLVouchers($job_id);
			$data['transportation']    = $this->accounting->getTransportationExpenses($job_id);
			$data['payments']          = $this->accounting->getPaymentVouchers($job_id);
			$data['pending_documents'] = $this->import->getPendingDocuments($job_id);

			$data['focus_id']   = "Date";
			$data['docs_url']   = $this->_docs;
			$data['page_title'] = humanize($this->_class . ' &amp; Documentation');
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");
					
			if ($this->input->post('wharfage') == 0) {
				$wharfage = $this->import->calculateWharfage($job_id);
			}

			$data = array(
				'job_id' 			=> $job_id,
				'invoice_value' 	=> $this->input->post('invoice_value'),
				'iv_currency_id' 	=> $this->input->post('iv_currency_id'),
				'appraisement_date'	=> $this->input->post('appraisement_date'),
				'assessment_date' 	=> $this->input->post('assessment_date'),
				'exam_date' 		=> $this->input->post('exam_date'),
				'payment_date' 		=> $this->input->post('payment_date'),
				'ooc_date' 			=> $this->input->post('ooc_date'),
				'custom_duty' 		=> $this->input->post('custom_duty'),
				'cd_date' 			=> $this->input->post('cd_date'),
				'cd_voucher_id' 	=> $this->input->post('cd_voucher_id'),
				'cd_paid_direct' 	=> ($this->input->post('cd_paid_direct') == 'Yes' ? 'Yes' : 'No'),
				'ppq' 				=> $this->input->post('ppq'),
				'ppq_date' 			=> $this->input->post('ppq_date'),
				'ppq_voucher_id' 	=> $this->input->post('ppq_voucher_id'),
				'ppq_paid_direct' 	=> ($this->input->post('ppq_paid_direct') == 'Yes' ? 'Yes' : 'No'),
				'stamp_duty' 		=> $this->input->post('stamp_duty'),
				'sd_voucher_id' 	=> $this->input->post('ppq'),
				'wharfage' 			=> (isset($wharfage) ? $wharfage['amount'] : $this->input->post('wharfage')),
				'wh_voucher_id'     => $this->input->post('wh_voucher_id'),
				'wh_rate'           => (isset($wharfage) ? $wharfage['rate'] : $this->input->post('wh_rate')),
				'wh_stax'    		=> (isset($wharfage) ? $wharfage['stax'] : $this->input->post('wh_stax')),
				'wh_tds'            => (isset($wharfage) ? $wharfage['tds'] : $this->input->post('wh_tds')),
				'line_payment' 		=> $this->input->post('line_payment'),
				'cfs_payment' 		=> $this->input->post('cfs_payment'),
				'free_days'		    => $this->input->post('free_days'),
				// 'original_bl_received' => $this->input->post('original_bl_received'),
				'place_of_delivery' => $this->input->post('place_of_delivery'),
				'remarks' 			=> $this->input->post('remarks')
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->kaabar->save('jobs', array('cfs_id' => $this->input->post('cfs_id')), array('id' => $job_id));
			$this->_updatePendingDocuments($job_id);
			$this->_updatePaymentVouchers($job_id);
			setSessionAlert('Changes saved successfully', 'success');
			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}

	function _updatePendingDocuments($job_id) {
		$dids = $this->input->post('did');
		if ($dids != null) {
			$is_pendings = $this->input->post('is_pending');
			$receiveds = $this->input->post('received');
			$rcvd_dates = $this->input->post('received_date');
			foreach ($dids as $index) {
				if ($index > 0)
					$this->kaabar->save('attached_documents', array(
						'is_pending' 	=> (isset($is_pendings[$index]) ? 'Yes' : 'No'), 
						'received' 		=> (isset($receiveds[$index]) ? 'Yes' : 'No'),
						'received_date' => $rcvd_dates[$index]
					), ['id' => $index]);
			}
		}

		$dtids = $this->input->post('new_dtid');
		if ($dtids != null) {
			$dids = $this->input->post('new_did');
			$receiveds = $this->input->post('new_received');
			foreach($dtids as $index => $dtid) {
				$data = array(
					'id' 			=> $dids[$index],
					'document_type_id' => $dtid,
					'job_id'		=> $job_id,
					'is_pending' 	=> 'Yes', 
					'received' 		=> (isset($receiveds[$index]) ? 'Yes' : 'No'),
					'received_date' => (isset($receiveds[$index]) ? date('Y-m-d') : '0000-00-00')
				);
				$this->kaabar->save('attached_documents', $data, array('id' => $dids[$index]));
			}
		}
	}

	function _updatePaymentVouchers($job_id) {
		$vouchers = $this->accounting->getBLVouchers($job_id);
		foreach ($vouchers as $v) {
			$this->import->updatePayments($job_id, $v['voucher_detail_id'], $v['bill_item_id']);
		}
	}

	function ajaxDocuments($job_id) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT AD.id, AD.document_type_id, DT.sr_no, DT.name, AD.remarks, DT.is_pending
			FROM (attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id)
				INNER JOIN jobs J ON (J.id = AD.job_id)
			WHERE J.id = $job_id AND AD.is_pending = 'No' AND DT.name LIKE '%$search%'
			UNION
				SELECT 0 AS id, DT.id AS document_type_id, DT.sr_no, DT.name, '' AS remarks, DT.is_pending 
				FROM document_types DT INNER JOIN jobs J ON (DT.product_id = J.product_id AND DT.type = J.type AND DT.cargo_type = J.cargo_type)
				WHERE J.id = $job_id AND DT.id NOT IN (
					SELECT document_type_id FROM attached_documents WHERE job_id = $job_id
				) AND DT.name LIKE '%$search%'
			ORDER BY is_pending DESC, sr_no";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function calculate($type = 'CFS', $job_id = 0) {
		if ($job_id > 0) {
			if ($type == 'CFS')
				$this->import->calcCFS($job_id);
			else
				$this->import->calcLine($job_id);
			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
		else {
			$job_ids = $this->input->post('job_id');
			foreach ($job_ids as $job_id) {
				if ($type == 'CFS')
					$this->import->calcCFS($job_id);
				else
					$this->import->calcLine($job_id);
			}
			echo 'Done';
		}
	}
}
