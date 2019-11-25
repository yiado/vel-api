<?php

class ufjob extends APP_Controller {

    function ufjob() {
        parent :: APP_Controller();
    }

    function index() {

        $this->valorUf();

     
    }

    function valorUf() {


        $xmlSource = "http://indicadoresdeldia.cl/webservice/indicadores.xml";
        $xml = simplexml_load_file($xmlSource);

        $uf_value = (string) $xml->indicador->uf;


        $uf_value = str_replace('$', '', $uf_value);
        $uf_value = str_replace('.', '', $uf_value);
        $uf_value = str_replace(',', '.', $uf_value);

        // echo $uf;

        $fecha = date('Y-m-d');
        $Uf = Doctrine_Core :: getTable('Uf')->findBy('uf_date', $fecha);

        if (!$Uf->count()) {

            $Uf = new Uf();
            $Uf->uf_value = $uf_value;
            $Uf->uf_date = date('Y-m-d');
            $Uf->save();
        }
    }

}
