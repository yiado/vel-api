<?php
class documentjob extends APP_Controller {

	function documentjob() {
		parent :: APP_Controller();
		
		$this->load->config('module/doc');
	}

	function index ( $doc_document_id=null ) {

		$q = Doctrine_Query :: create()
			 ->from('DocVersion dv')
			 ->innerJoin('dv.DocDocument dd')
			 ->innerJoin('dd.DocCategory dc')
			->where('doc_version_expiration IS NOT NULL')
			->andWhere('doc_version_alert IS NOT NULL')
			->andWhere('doc_version_alert_email IS NOT NULL');
			
		if (!is_null($doc_document_id)) {
			$q->where('dv.doc_document_id = ?', $doc_document_id);
		}
			
		$results = $q->execute(array (), Doctrine_Core :: HYDRATE_ARRAY);

		$CI = & get_instance();
		$CI->load->library('NotificationUser');

		$fechaToDay = strtotime(date('Y-m-d'));

		foreach ($results as $alert) {

			$dateEnd = strtotime($alert['doc_version_expiration']);
			$rest = $dateEnd - $fechaToDay;
			if ($dateEnd >= $fechaToDay || $this->config->item('doc_alert_after_expiration') === true) {
				$dateStart = strtotime ( "-" . $alert[ 'doc_version_alert' ] . "days" , $dateEnd );
				
				if (($this->config->item('doc_alert_interval') != 0 && fmod($rest, $this->config->item('doc_alert_interval')) == 0) || // con intervalo
					($this->config->item('doc_alert_interval') == 0 && $dateStart <= $fechaToDay && $fechaToDay <= $dateEnd) || // sin intervalo
					($this->config->item('doc_alert_interval') == 0 && $this->config->item('doc_alert_after_expiration') === true && $fechaToDay > $dateEnd) || 
					$dateStart == $fechaToDay // fecha de expiracion menos dias de alerta
					) // enviar despues de expirar 
					{
					
					$node = Doctrine_Core :: getTable('Node')->find($alert['DocDocument']['node_id']);

					$to = trim($alert['doc_version_alert_email']); //CORREO DESTINATARIO

					$subject = 'Alerta aviso vencimiento contrato'; //ASUNTO

					$body = 'NOMBRE DOCUMENTO : ' . $alert['DocDocument']['doc_document_filename'] . "\n"; //CUERPO DEL MENSAJE
					$body .= 'CATEGORIA : ' . $alert['DocDocument']['DocCategory']['doc_category_name'] . "\n";
					$body .= 'VERSION : ' . $alert['doc_version_code_client'] . "\n";
					$body .= 'DESCRIPCION : ' . $alert['DocDocument']['doc_document_description'] . "\n";
					$body .= 'FECHA DE EXPIRACION : ' . $alert['doc_version_expiration'] . "\r\n";
					$body .= 'UBICACION : ' . $node->getPath() . "\r\n";
					
					if (!is_null($doc_document_id)) {
						echo $body . '<br><br>';
					}
					
					copy($this->config->item('doc_dir') . $alert['doc_version_filename'], $this->config->item('temp_dir') . $alert['DocDocument']['doc_document_filename']);

					$CI->notificationuser->mail($to, $subject, $body, null, null, array (
						$this->config->item('temp_dir'
					) . $alert['DocDocument']['doc_document_filename']));
					$body = '';

					unlink($this->config->item('temp_dir') . $alert['DocDocument']['doc_document_filename']);
					
				}
			}
		}
	}

}