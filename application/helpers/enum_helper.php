<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
 * Function: getEnumSetOptions
 * Param	 : $table
 * +         $field
 * Returns : Array of values
 * Notes   : Returns the possible enum values of a field in a table.
------------------------------------------------------------------------------*/
function getEnumSetOptions ($table, $field, $db = null) {
	$values = null;

	if (is_null($db)) {
		$CI =& get_instance();
		$query = $CI->db->query("DESCRIBE $table $field");
	}
	else
		$query = $db->query("DESCRIBE $table $field");

	if ($query->num_rows() > 0) {
		$result = $query->row();
		$tmpvalues = $result->Type;
		
      	$open = strpos($tmpvalues, '(');
      	$close = strrpos($tmpvalues, ')');
       	if (!$open || !$close) {
			return $values;
        	}
		$values = substr($tmpvalues, $open + 2, $close - $open - 3);
		$tmpvalues = explode('\',\'', $values);
		//sort($tmpvalues);
		$values = null;
		foreach($tmpvalues as $value) {
			$values[$value] = $value;
		}
	}
	else {
		$values = array(null);
	}
	return $values;
}

function getSelectOptions ($table, $field1 = "id", $field2 = "name", 
	$condition = null, $chars_limit = 50, $sql = null, $db = null) {
	$values = null;

	if ($sql == null) {
		$sql = "SELECT $field1, $field2 FROM $table ";
		if($condition) {
			$sql .= " " . $condition . " ";
		}
		$sql .= "ORDER BY $field2";
	}

	if (is_null($db)) {
		$CI =& get_instance();
		$query = $CI->db->query($sql);
	}
	else
		$query = $db->query($sql);
	
	if ($query->num_rows() > 0) {
		$result = $query->result_array();
		foreach($result as $row) {
			//$values[$row[$field1]] = character_limiter($row[$field2], $chars_limit);
			$values[$row[$field1]] = htmlspecialchars($row[$field2]);
		}
	}
	else {
		$values = array();
	}
	return $values;
}

/* End: enum_pi.php */