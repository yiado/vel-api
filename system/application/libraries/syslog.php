<?php
class SysLog {

	function __construct() { }

	function register($log_type_name, $description = null) {

		$CI = & get_instance();

		$logType = Doctrine_Core :: getTable('LogType')->findOneByLogTypeName($log_type_name);

		if ($logType) {
                        date_default_timezone_set("Chile/Continental");
			$log = new Log();
			$log->user_id = $CI->auth->get_user_data('user_id');
			$log->log_type_id = $logType->log_type_id;
                        $log->log_date_time = date('Y-m-d').' '.date ("H:i:s",time());
                        $log->log_ip = $_SERVER['REMOTE_ADDR'];

			if (!is_null($description) && !is_null($logType->log_type_template)) {
				$log->log_description = call_user_func_array('sprintf', array_merge(array (
					$logType->log_type_template
				), $description));
			}

			$log->save();
                        
                        return $log->log_id;

		}
                
                return false;

	}

}