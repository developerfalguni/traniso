<?php

class Audit {
	function __construct() {
		$this->_ci =& get_instance();

		$this->_exclude_tables = [
			'captcha', 'ci_sessions'
		];
	}
	
	function excludeTables($tables) {
		if (is_array($tables))
			$this->_exclude_tables = array_merge($this->_exclude_tables, $tables);
	}
	
	function createLogs() {
		$add_fields1 = [
			'audit_user_id' => 'INT DEFAULT 0', 
			'audit_ip_addr' => 'VARCHAR(16) DEFAULT "0.0.0.0"',
			'audit_action'  => 'ENUM("C", "R", "U", "D") DEFAULT "C"',
			'audit_updated' => 'DATETIME DEFAULT "0000-00-00 00:00:00"',
		];
		
		$tables = $this->_ci->db->list_tables();
		foreach ($tables as $table) {
			
			if (in_array($table, $this->_exclude_tables)) continue;
			
			$alter_table = true;
			$sql = "ALTER TABLE $table ";
			foreach($add_fields1 as $field => $datatype) {
				if ($this->_ci->db->field_exists($field, $table)) {
					$alter_table = false;
					continue;
				}
				else {
					$sql .= "ADD $field $datatype NOT NULL,";
				}
			}
			if ($alter_table) {
				$sql = substr($sql, 0, strlen($sql)-1);
				$this->_ci->db->query($sql);
			}
		}
	}
}
