<?php

class Settings {
	private static $_ci       = '';
	private static $_table    = '';
	private static $_settings = [];

	public function __construct() {
		self::$_ci    =& get_instance();
		self::$_table = 'settings';
		
		$this->reload();
	}

	public static function reload() {
		self::$_settings = [];
		
		$sql = "SELECT * FROM " . self::$_table . "
			WHERE user_id = 0 OR user_id = " . Auth::getCurrUID() . "
			ORDER BY id";
		$query = self::$_ci->db->query($sql);
		$rows = $query->result_array();
		foreach($rows as $row) {
			self::$_settings[$row['name']] = $row['value'];
		}
	}

	public static function get($name, $default = null) {
		return (isset(self::$_settings[$name]) ? self::$_settings[$name] : $default);
	}

	public static function getSystem() {
		self::$_ci->db->where('user_id', 0);
		self::$_ci->db->not_like('name', '_', 'after');
		self::$_ci->db->order_by('id');
		$query = self::$_ci->db->get(self::$_table);
		return $query->result_array();
	}

	public static function getUser() {
		$sql = "INSERT INTO " . self::$_table . " (user_id, name, value)
			SELECT " . Auth::getCurrUID() . ", name, value
			FROM " . self::$_table . "
			WHERE name NOT IN (
				SELECT name FROM " . self::$_table . " WHERE user_id = " . Auth::getCurrUID() . "
			) AND customize = 'Yes'";
		self::$_ci->db->query($sql);

		self::$_ci->db->where('user_id', Auth::getCurrUID());
		self::$_ci->db->not_like('name', '_', 'after');
		$query = self::$_ci->db->get(self::$_table);
		return $query->result_array();
	}

	public static function set($id, $value)  {
		self::$_ci->kaabar->save(self::$_table, ['value' => $value], ['id' => $id]);
	}

	public static function create($name, $value) {
		$query = self::$_ci->db->query("SELECT * FROM " . self::$_table . " WHERE user_id = ? AND name LIKE ? AND customize = 'No'", 
			[Auth::getCurrUID(), $name]);
		$row = $query->row_array();
		if ($row) {
			$row['value'] = $value;
			self::$_ci->kaabar->save(self::$_table, $row, ['id' => $row['id']]);
		}
		else
			self::$_ci->db->insert(self::$_table, [
				'user_id' => Auth::getCurrUID(), 
				'name'    => $name, 
				'value'   => $value
			]);
	}

	public static function delete() {
		// Pending 
	}

	public static function getEwbCredential() {
		
		$getApiSource = self::$_ci->db->where(['user_id' => 0, 'name' => 'eway_default_source'])->like('name', 'eway_', 'after')->get('settings')->row();

		if($getApiSource->value == 'TEST'){
			$rows = self::$_ci->db->where(['user_id' => 0])
				->like('name', 'eway_test_', 'after')
				->get('settings')
				->result_array();
			foreach ($rows as $key => $value) {
				if($value['name'] == 'eway_test_active' AND $value['value'] == 'No')
					return false;
			}
		}
		elseif($getApiSource->value == 'LIVE'){
			$rows = self::$_ci->db->where(['user_id' => 0])
				->like('name', 'eway_', 'after')
				->not_like('name', 'eway_test_', 'after')
				->get('settings')
				->result_array();
			foreach ($rows as $key => $value) {
				if($value['name'] == 'eway_active' AND $value['value'] == 'No')
					return false;
			}
		}

		return $rows;
	}

	public static function getEinvCredential() {
		
		$getApiSource = self::$_ci->db->where(['user_id' => 0, 'name' => 'einv_default_source'])->like('name', 'einv_', 'after')->get('settings')->row();

		if($getApiSource->value == 'TEST'){
			$rows = self::$_ci->db->where(['user_id' => 0])
				->like('name', 'einv_test_', 'after')
				->get('settings')
				->result_array();
			foreach ($rows as $key => $value) {
				if($value['name'] == 'einv_test_active' AND $value['value'] == 'No')
					return false;
			}
		}
		elseif($getApiSource->value == 'LIVE'){
			$rows = self::$_ci->db->where(['user_id' => 0])
				->like('name', 'einv_', 'after')
				->not_like('name', 'einv_test_', 'after')
				->get('settings')
				->result_array();
			foreach ($rows as $key => $value) {
				if($value['name'] == 'einv_active' AND $value['value'] == 'No')
					return false;
			}
		}

		return $rows;
	}
}
