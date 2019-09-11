<?php

class NotificationUser {

    var $CI;

    function NotificationUser() {
        $this->CI = & get_instance();
        $this->CI->config->load('email');
    }

    /**
     *
     * Envia un e-mail a una dirección de correo especifica
     * @param string $to (destinatario del mensaje)
     * @param string $subject (asunto del mensaje)
     * @param string $msg (cuerpo del mensaje)
     */
    function mail($to, $subject, $msg, $attaches = null) {
        $this->CI->load->library('email');

        //$this->CI->email->set_newline("\r\n");

        $this->CI->email->from('igeo@correo.velociti.cl', 'Sistema de información IGEO');
        $this->CI->email->to($to);

        $this->CI->email->subject("[iGeo] " . $subject);
        $this->CI->email->message($msg);

        if (!is_null($attaches)) {
            foreach ($attaches as $attach) {
                $this->CI->email->attach($attach);
            }
        }

        $this->CI->email->send();
        /*
         * Descomentar solo para depurar en caso de fallas.
         */
        //echo $this->CI->email->print_debugger();
        
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
