<?php
class APP_Input extends CI_Input {

	function APP_Input() {
		parent :: CI_Input();
	}

	function post($index = '', $xss_clean = FALSE) {

		$value = $this->_fetch_from_array($_POST, $index, $xss_clean);

		if ($value == "") { // si es nulo
				
			return NULL;
				
		} else {
				
			return $value;
				
		}
	}

	function postdate ($index = '', $xss_clean = FALSE) {

		$value = $this->_fetch_from_array($_POST, $index, $xss_clean);

		$CI = & get_instance();
		$dbms = $CI->db->dbdriver;

		$date_format = $CI->config->item('date_format');

		return mdate($date_format[$dbms], mysql_to_unix(str_ireplace('T', ' ', $value)));
			
	}
	
	function file ( $index = '', $xss_clean = FALSE ) {
		
		$value = $this->_fetch_from_array($_FILES, $index, $xss_clean);

		if ($value == "") { // si es nulo
				
			return NULL;
				
		} else {
				
			return $value;
				
		}
		
	}

	function postall ($xss_clean = FALSE) {

		$array = array ();

		foreach ($_POST as $key => $value) {

			$array[$key] = $this->post($key, $xss_clean);
		}
			
		return $array;

	}

}