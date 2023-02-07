<?php

const PENDING   = 0;
const CHECK     = 1;
const VERIFY    = 2;
const AUTHORISE = 4;

class Messages extends CI_Model {
	var $_table;
	var $_action;
	
	function __construct() {
		parent::__construct();
		
		$this->_table = 'messages';
	}
	
	function getMessages($user_id) {
		$sql = "SELECT MSG.id, MSG.from_user_id, MSG.to_user_id, DATE_FORMAT(MSG.created, '%d-%m-%Y %H:%i:%s') AS created, U.username AS from_user, MSG.table_name, MSG.row_id, MSG.action, MSG.url, MSG.message
		FROM " . $this->_table . " MSG INNER JOIN users U ON MSG.from_user_id = U.id
		WHERE MSG.to_user_id = $user_id AND MSG.status = 'Pending'
		ORDER BY MSG.created DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function viewMessages($user_id) {
		$rows = $this->getMessages($user_id);
		$messages = '';
		foreach($rows as $m)
			$messages .= '<span class="tiny">'
				. $m['created'] . '&nbsp;&nbsp;&nbsp;' 
				. '<a href="javascript: replyMessage(' . $m['id'] . ')"><span class="tiny">Reply</span></a> | ' 
				. '<a href="javascript: forwardMessage(' . $m['id'] . ')"><span class="tiny">Forward</span></a><br />'
				. anchor($m['url'], 'From ' . humanize($m['from_user']) . ' ' . $m['action'] . ' ' . humanize($m['table_name']) . '-' . $m['row_id'])	. ($m['message'] ? '<br />' 
				. $m['message'] : '')
				. '</span>
				<hr />';
		return $messages;
	}

	function checkMessage($table, $id) {
		if ($id == 0) return;
		$sql = "SELECT * FROM " . $this->_table . " MSG WHERE MSG.table_name = '$table' AND MSG.row_id = $id AND MSG.status = 'Pending'";
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	
	function getUsers($action = '') {
		$data = array(0 => '');
		$sql = "SELECT id, CONCAT(title, ' ', firstname, ' ', lastname) AS fullname FROM staffs
		WHERE permission LIKE '%$action%'
		ORDER BY firstname, lastname";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach($rows as $row) {
			$data[$row['id']] = $row['fullname'];
		}
		return $data;
	}
	
	function sendMessage($to_user_id, $message, $action, $table, $id, $url) {
		/*$sql = "SELECT id FROM staffs WHERE permission LIKE '%$action%'";
		$query = $this->db->query($sql);
		$users = $query->result_array();*/
		$data = array(
			'id' => 0,
			'created' => date('Y-m-d H:i:s'),
			'from_user_id' => Auth::getCurrUID(),
			'to_user_id' => $to_user_id,
			'table_name' => $table,
			'row_id' => $id,
			'url' => $url,
			'action' => $action,
			'message' => $message,
			'status' => 'Pending'
		);
		if (is_array($to_user_id)) {
			foreach($to_user_id as $touid) {
				if ($touid == 0) continue;
				$data['to_user_id'] = $touid;
				$this->kaabar->save($this->_table, $data, array('id' => 0));
			}
		}
		else
			$this->kaabar->save($this->_table, $data, array('id' => 0));
	}
	
	function updateMessage($table, $id, $status, $to_user_id = 0) {
		if ($id == 0) return;
		$sql = "SELECT MSG.*, U.username
		FROM " . $this->_table . " MSG INNER JOIN users U ON MSG.from_user_id = U.id
		WHERE to_user_id = " . Auth::getCurrUID() . " AND MSG.table_name = '$table' AND MSG.row_id = $id AND MSG.status = 'Pending'";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach($rows as $row) {
			$row['status'] = $status;
			if ($status == 'Returned' OR $status == 'Forwarded') {
					$row['to_user_id'] = $to_user_id;
					$row['message'] .= "\n$status From " . humanize($row['username']);
			}
			unset($row['username']);
			$this->kaabar->save($this->_table, $row, array('id' => $row['id']));
		}
	}
}
