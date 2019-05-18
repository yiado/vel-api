<?php

	class json {
		
		function json () {
			
			$CI =& get_instance();
			$CI->load->library('Zend/Json');
			
		}
		
		function encode ( $arr ) {
		    
	        $data = Zend_Json::encode($arr);    //encode the data in json format
		    return $data;
		    
		}
		
		function decode ( $arr, $assoc=FALSE ) {
		    
	        $data = json_decode($arr, $assoc);    //encode the data in json format
		    return $data;
		    
		}
		
	}
	