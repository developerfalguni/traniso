<?php

use League\Csv\Writer;

class Kaabar extends CI_Model {
	var $_company_id;
	var $_fy_year;

	function __construct() {
		parent::__construct();

		$this->load->driver('cache', ['adapter' => 'file']);
		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);

	}

	function setCompany($id) {
		$this->_company_id = $id;
	}

	function getCompanyID() {
		return $this->_company_id;
	}

	function getFinancialYear() {
		return $this->_fy_year;
	}

	function getApiStatus() {
	    return array(
			'Yes' => 'Yes',
			'No' => 'No',
		);
		
	}
	
	function getDefaultSource() {
	    return array(
			'TEST' => 'Test Server',
			'LIVE' => 'Live Server',
		);
		
	}

	function custom_filter($array) { 
    $temp = [];
	  array_walk($array, function($item,$key) use (&$temp){
	      foreach($item as $key => $value)
	         $temp[$key] = trim($value);
	  });
	  return $temp;
	}
	
	function getFinYear(){
		$m = date('m');
		$previous = date("Y",strtotime("-1 year"));
		$current = date("Y"); 
		$next = date("Y",strtotime("+1 year"));

		if($m > 3 AND $m <= 12){
			return $finyear = $current.'_'.$next;
		}
		if($m >= 1 AND $m < 4){
			return $finyear = $previous.'_'.$current;
		}
	}

	function getBackFinYear($date){

		$m = date("m", strtotime($date));
		
		$previous = date("Y", strtotime(date("Y-m-d", strtotime($date)) . "-1 year"));
		$current = date("Y", strtotime($date));
		$next = date("Y", strtotime(date("Y-m-d", strtotime($date)) . "+1 year"));
		
		if($m > 3 AND $m <= 12){
			return $finyear = $current.'_'.$next;
		}
		if($m >= 1 AND $m < 4){
			return $finyear = $previous.'_'.$current;
		}
	}
	
	function getAjax($sql, $db = null) {
		if ($db == null)
			$query = $this->db->query($sql);
		else
			$query = $db->query($sql);
		$rows = $query->result_array();
		if ($rows) {
			foreach ($rows as $row) {
				echo $row['ajaxField'] . "\r\n";
			}
		}
	}
	
	function getJson($sql, $db = null) {
		if ($db == null)
			$query = $this->db->query($sql);
		else
			$query = $db->query($sql);
		if ($query->num_fields() == 1) {
			$result = $query->result_array();
			$fields = $query->list_fields();
			$field  = reset($fields);
			// 	$field = $query->list_fields()[0];
			foreach ($result as $f => $v) {
				$rows[] = $v[$field];
			}
		}
		else {
			$rows = $query->result_array();
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($rows, JSON_UNESCAPED_UNICODE);
	}

	function save($table, $data, $where = null) {
		if (is_null($where))
			$where = ['id' => 0];

		// Finding the DMY dates and converting to YMD
		foreach ($data as $field => $value) {
			if (is_string($value) && (strlen($value) == 10 OR strlen($value) == 16 OR strlen($value) == 19))
				if (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $value, $regs)) 
					$data[$field] = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:$regs[6]";
				elseif (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/", $value, $regs)) 
					$data[$field] = "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]";
				elseif (preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/", $value, $regs))
					$data[$field] = "$regs[3]-$regs[2]-$regs[1]";
		}

		if (defined('AUDIT')) {
			$data['audit_user_id'] = Auth::getCurrUID();
			$data['audit_ip_addr'] = $this->input->ip_address();
			$data['audit_action']  = 'U';
			$data['audit_updated'] = date('Y-m-d H:i:s');
		}


		if (isset($where['id']) AND $where['id'] == 0) {
			if (defined('AUDIT'))
				$data['audit_action']  = 'C';

			$this->db->insert($table, $data);
			$id = $this->db->insert_id();
			
			if (defined('LOGS') AND defined('AUDIT')) {
				$database   = $this->db->database;
				$audit_logs = (new MongoDB\Client)->$database->$table;
				$audit_logs->insertOne(['id' => $id]+$data);
			}
		}
		else {

		
			$rows = $this->db->select("id")->get_where($table, $where)->result_array();

			$this->db->update($table, $data, $where);
			$id = (isset($where['id']) ? $where['id'] : 0);

			if (defined('LOGS') AND defined('AUDIT')) {
				$database   = $this->db->database;
				$audit_logs = (new MongoDB\Client)->$database->$table;
				
				foreach ($rows as $r) {
					$audit_logs->insertOne(['id' => $r['id']]+$data);
				}
			}
		}

		

		return $id;
	}

	function getFolder($path, $id, $create = false) {
		$dirarr = [];
		for ($i=0; $i < strlen($id); $i++) {
			$dirarr[] = substr($id, $i, 1);
		}
		$dir = $path . '/' . implode('/', $dirarr);
		
		if (! file_exists($dir) AND $create) {
			$cdir = $path;
			foreach ($dirarr as $dir) {
				$cdir .= '/'.$dir;
				if (! file_exists($cdir))
					mkdir($cdir);
			}
		}

		return implode('/', $dirarr) . '/';
	}

	function getImage($path, $id, $image_name = '') {

		$image_url = $this->_assets. 'app-assets/images/noimageavailble.jpg';
		$docdir = $this->getDocFolder($path, $id);
		if (strlen($image_name) > 0 && file_exists(FCPATH . $path . $docdir . $image_name)) {
			$image_url = base_url($path  . $docdir . $image_name);
		}
		return $image_url;
	}

	function count($key, $sql) {
		$key = md5($key);
		if (! $result = $this->cache->get($key)) {
			$row = $this->db->query($sql)->row_array();
			if (! $row)
				$row['total_rows'] = 0;

			$this->cache->save($key, $row['total_rows'], 600);
			$result = $row['total_rows'];
		}
		return $result;
	}
	
	function delete($table = null, $id = null) {
		if (! is_null($table) && ! is_null($id) && strlen($table) > 0 && is_array($id)) {
			if (defined('LOGS') AND LOGS == 'Audit') {
				$data['audit_user_id'] = Auth::getCurrUID();
				$data['audit_ip_addr'] = $this->input->ip_address();
				$data['audit_action']  = 'D';
				$data['audit_updated'] = date('Y-m-d H:i:s');
				$database = $this->db->database;
				$audit_logs = (new MongoDB\Client)->$database->$table;
				$audit_logs->insertOne($data);
			}
			$this->db->delete($table, $id);
		}
		else if (! is_null($table) && ! is_null($id) && strlen($table) > 0 && $id > 0) {
			if (defined('LOGS') AND LOGS == 'Audit') {
				$data['audit_user_id'] = Auth::getCurrUID();
				$data['audit_ip_addr'] = $this->input->ip_address();
				$data['audit_action']  = 'D';
				$data['audit_updated'] = date('Y-m-d H:i:s');
				$database = $this->db->database;
				$audit_logs = (new MongoDB\Client)->$database->$table;
				$audit_logs->insertOne($data);
			}
			$this->db->delete($table, ['id' => $id]);
		}
	}
	
	function _getFinancialYear($date) {
		$d = 0; $m = 0; $y = 0;
		
		if ($date == "0000-00-00" OR $date == "00-00-0000") {
			return $this->session->userdata("financial_year");
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

	function _convDate($date) {
		if (is_string($date) && (strlen($date) == 10 OR strlen($date) == 19)) {
			if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $date, $regs)) 
				return "$regs[3]-$regs[2]-$regs[1] $regs[4]:$regs[5]:$regs[6]";
			else if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs))
				return "$regs[3]-$regs[2]-$regs[1]";
		}
		return $date;
	}

	function _checkDate($date, $fy_year) {
		$result     = TRUE;

		// $result     = FALSE;
		$years      = explode('_', $fy_year);
		$date1      = strtotime($years[0].'-04-01');
		$date2      = strtotime($years[1].'-03-31');
		$date3      = strtotime($date);

		// Check if date is lying in currently financial year.
		if (($date3 - $date1) > 0 && ($date2 - $date3) < 0)
			$result = FALSE;
		else if (($date3 - $date1) < 0 && ($date3 - $date2) <= 0)
			$result = FALSE;

		return $result;
	}

	function createQuotationNo($table, $id, $code, $quoteType) {

		$years      = explode('_', $this->getFinancialYear());
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';
		
		$query = $this->db->query("SELECT * FROM $table WHERE id = $id AND company_id = ? AND isDeleted = ? AND type = ?", [$this->getCompanyID(), 'No', $quoteType]);
		$row   = $query->row_array();

		$this->db->query("LOCK TABLES $table WRITE");
		$query = $this->db->query("SELECT MAX(idkaabar) AS idkaabar FROM $table WHERE date >= ? AND date <= ? AND isDeleted = ? AND company_id = ? AND type = ?", 
			array($start_date, $end_date, 'No', $this->getCompanyID(), $quoteType));
		$id_row = $query->row_array();

		$id_row['idkaabar']++;
		
		$id_row['idkaabar_code'] = $code. '/' . substr($years[0], 2, 2) . '-' . substr($years[1], 2, 2). '/'.str_pad($id_row['idkaabar'], 4, '0', STR_PAD_LEFT);
		
		$this->db->update($table, array('idkaabar' => $id_row['idkaabar'], 'idkaabar_code' => $id_row['idkaabar_code']), "id = $id");
		$this->db->query("UNLOCK TABLES");

	}

	function createKaabarNo($table, $id, $code) {

		$years      = explode('_', $this->getFinancialYear());
		$start_date = $years[0] . '-04-01';
		$end_date   = $years[1] . '-03-31';
		
		$query = $this->db->query("SELECT * FROM $table WHERE id = $id AND company_id = ? AND isDeleted = ?", [$this->getCompanyID(), 'No']);
		$row   = $query->row_array();

		$this->db->query("LOCK TABLES $table WRITE");
		$query = $this->db->query("SELECT MAX(idkaabar) AS idkaabar FROM $table WHERE date >= ? AND date <= ? AND isDeleted = ? AND company_id = ?", 
			array($start_date, $end_date, 'No', $this->getCompanyID()));
		$id_row = $query->row_array();
		
		$id_row['idkaabar']++;
		
		$id_row['idkaabar_code'] = $code.str_pad($id_row['idkaabar'], 4, '0', STR_PAD_LEFT);
		
		$this->db->update($table, array('idkaabar' => $id_row['idkaabar'], 'idkaabar_code' => $id_row['idkaabar_code']), "id = $id");
		$this->db->query("UNLOCK TABLES");
	}
	
	function getRow($table, $id, $search_field = 'id', $convdate = true) {
		if (is_array($id)) {
			$query = $this->db->limit(1)->get_where($table, $id);
		}
		else {
			$query = $this->db->limit(1)->get_where($table, array($search_field => $id));
		}
		$row = $query->row_array();

		// Finding the YMD dates and converting to DMY
		if ($convdate && $row) {
			foreach ($row as $f => $v) {
				$row[$f] = $this->_convDate($v);
			}
		}
		return $row;
	}
	
	function getRows($table, $search = NULL, $search_field = 'id', $fields = '*', $order_by = 'id', $convdate = false) {
		$this->db->select($fields);
		$this->db->order_by($order_by);
		if ($search === NULL)
			$query = $this->db->get($table);
		else if (is_array($search))
			$query = $this->db->get_where($table, $search);
		else
			$query = $this->db->get_where($table, [$search_field => $search]);

		$rows = $query->result_array();
		// Finding the YMD dates and converting to DMY
		if ($convdate) {
			foreach ($rows as $index => $row) {
				foreach ($row as $f => $v) {
					$rows[$index][$f] = $this->_convDate($v);
				}
			}
		}
		return $rows;
	}
	
	function countAll($table, $fields = array('name'), $search = '') {
		$nf = array();
		foreach($fields as $f)
			$nf[] = "$f LIKE '%$search%'";
			
		$sql = "SELECT COUNT(id) AS numrows FROM $table WHERE " . implode(' OR ', $nf);
			
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function num_rows($table, $search = []) {
		if (count($search) > 0)
			$sql = "SELECT COUNT(id) AS numrows FROM $table WHERE " . implode(' AND ', $search);
		else
			$sql = "SELECT COUNT(id) AS numrows FROM $table";
		
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row) 
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function getAll($table, $fields = array('*'), $searchfields = array(), $search = '', $offset = 0, $limit = 25, $order_by = 'id') {
		$nf = array();
		foreach($searchfields as $f)
			$nf[] = "$f LIKE '%$search%'";

		if (count($nf) > 0)
			$sql = "SELECT " . implode(', ', $fields) . " FROM $table WHERE " . implode(' OR ', $nf) . " ORDER BY $order_by LIMIT $offset, $limit";
		else 
			$sql = "SELECT " . implode(', ', $fields) . " FROM $table ORDER BY $order_by";

		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function getField($table, $search, $search_field = 'id', $field = 'name') {
		
		if (is_array($field))
			$this->db->select(implode(',', $field), FALSE);
		else
			$this->db->select($field, FALSE);

		if ($search === NULL)
			$query = $this->db->get($table);
		else if (is_array($search))
			$query = $this->db->get_where($table, $search);
		else
			$query = $this->db->get_where($table, array($search_field => $search));

		$row = $query->row_array();
		if ($row == FALSE)
			return FALSE;

		foreach ($row as $f => $v) {
			$row[$f] = $this->_convDate($v);
		}

		if (is_array($field))
			return $row;

		return $row[$field];
	}
	
	function getFieldValues($table, $search = null, $search_field = 'id', $field = 'name') {
		$values = array();
		if (is_array($field))
			return $values;
		
		$sql = "SELECT $field FROM $table" . (is_null($search) ? null : " WHERE $search_field = '$search'") . " ORDER BY id";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach($rows as $row)
			$values[] = $row[$field];
			
		return $values;
	}
	
	function getNameValuePair($table, $search = null, $search_field = 'id', $key = 'name', $value = 'value') {
		$data = array();
		$sql = "SELECT $key, $value FROM $table" . (is_null($search) ? null : " WHERE $search_field = '$search'") . " ORDER BY $value";
		$query = $this->db->query($sql);
		$rows = $query->result_array();
		foreach($rows as $r) {
			$data[$r[$key]] = $r[$value];
		}
		return $data;
	}
	
	function getLabelClass() {
		return array(
			''    => '',
			'Yes' => 'label-success',
			'No'  => 'label-danger'
		);
	}

	function getFreightType() {
		return array(
			'PERMT'     => 'PER MT RATE',
			'FIX'		=> 'FIX RATE',
		);
	}

	function parseSearch($search) {
		if (strpos($search, ':') == 0)
			return $search;

		$parsed = array();
		preg_match_all("/[A-Za-z0-9_\"]*:/", $search, $fields);
		$values = preg_split("/[A-Za-z0-9_\"]*:/", $search);
		array_shift($values);
		foreach($fields[0] as $i => $f) {
			$parsed[trim(str_replace(':', '', $f))] = trim($values[$i]);
			if (strlen(trim($values[$i])) == 0)
				unset($parsed[trim(str_replace(':', '', $f))]);
		}
		return $parsed;
	}

	function parseFilterSearch($search, $param) {

		$parsed = null;
		if(empty($search))
			$search = [];

		$i = 0;
		if($param){
			foreach ($param as $key => $value) {
				$prefix = 'filter';
				$filter = str_replace($prefix, '', $key);
				$parsed[$i]['field'] = $filter;
				if(array_key_exists($filter, $search)){
					$parsed[$i]['value'] = $search[$filter];
				}
				else{
					$parsed[$i]['value'] = '';
				}
				
				$parsed[$i]['filter'] = $value;
				$i++;

			}	
		}
		return $parsed;
	}

	function getCities() {
		$values = array(0 => '');

		$sql = "SELECT C.id, CONCAT(C.name, IF(C.pincode = 0, '', CONCAT(' - ', C.pincode)), ' (', S.name, ')') AS name
		FROM cities C INNER JOIN states S ON C.state_id = S.id
		ORDER BY C.name";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			foreach($result as $row) {
				$values[$row['id']] = $row['name'];
			}
		}
		else {
			$values = array();
		}
		return $values;
	}

	function getCountries() {
		$values = array(0 => '');

		$sql = "SELECT C.*
		FROM countries C 
		ORDER BY C.name";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			foreach($result as $row) {
				$values[$row['id']] = $row['name'];
			}
		}
		else {
			$values = array();
		}
		return $values;
	}

	function getCity($id) {
		$sql = "SELECT C.id, CONCAT(C.name, IF(C.pincode = 0, '', CONCAT(' - ', C.pincode)), ' (', S.name, ')') AS name
		FROM cities C LEFT OUTER JOIN states S ON C.state_id = S.id
		WHERE C.id = ?";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		if ($row)
			return $row['name'];
		return '';
	}

	function getCityState($id) {
		$sql = "SELECT C.*, S.gst, S.name AS state_name
		FROM cities C LEFT OUTER JOIN states S ON C.state_id = S.id
		WHERE C.id = ?";
		return $this->db->query($sql, [$id])->row_array();
	}

	function checkEmail($email) {
		if (is_array($email)) {
			$result = false;
			foreach ($email as $e) {
				if (valid_email($e))
					$result[] = $e;
			}
			return $result;
		}
		else 
			if (valid_email($email))
				return $email;
			else 
				return false;
	}

	function _csv($rows, $hide_cols = array('id')) {
		ini_set('memory_limit', '512M');

		$writer = Writer::createFromFileObject(new SplTempFileObject());
		$writer->setNewline("\r\n");

		$header = reset($rows);
		foreach ($hide_cols as $f)
			unset($header[$f]);
		$writer->insertOne(array_keys($header));
		
		foreach ($rows as $row) {
			foreach ($hide_cols as $f)
				unset($row[$f]);
			$writer->insertOne($row);
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $this->_class.date('_d_m_Y') . '.csv"');
		header('Cache-Control: max-age=0');
		echo $writer;
	}


	
}
