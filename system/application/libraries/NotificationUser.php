<?php

class NotificationUser {

	var $CI;

    function NotificationUser () {

        $this->CI =& get_instance();
        $this->CI->config->load('email');

    }

    /**
     *
     * Envia un e-mail a una dirección de correo especifica
     * @param string $to (destinatario del mensaje)
     * @param string $subject (asunto del mensaje)
     * @param string $msg (cuerpo del mensaje)
     */

    function mail($to = array(), $subject, $msg, $cc = NULL, $co = NULL, $attaches=null) {

    	//Clase mail
        $this->CI->load->library('email');

        $this->CI->email->set_newline("\r\n");
        $from = $this->CI->config->item('smtp_user');
        $this->CI->email->from($from, 'Sistema de información IGEO');
        $this->CI->email->to($to);

        $this->CI->email->subject($subject);
        $this->CI->email->message($msg);
        
        // attaches
        if (!is_null($attaches)) {
        	foreach ($attaches as $attach) {
        		$this->CI->email->attach($attach);
        	}
        }
        
        //Enviar mail
        $this->CI->email->send();
        //Reset del sender
        $this->CI->email->clear();

    }

    /**
     *
     * Envia un mensaje de texto (sms) a un destinatario especifico
     * @string $msg (cuerpo del mensaje)
     * @string $to (numero de celular)
     */
    function sms($msg, $to) {

    }

}
