<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class cron extends APP_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function actualizarUf() {
        error_log("inicio actualizacion UF.");
        $uf = Doctrine_Core::getTable('Uf')->retrieveByDate(date("Y-m-d"));
        if (!$uf) {
            $time = strtotime("-1 month", time());
            $year = date("Y", $time);
            $month = date("m", $time);

            $api_uf = $this->config->item('api_uf_sbif');
            $api_url = $api_uf["base_url"] . "/" . $year . "/" . $month . "?apikey=" . $api_uf["key"] . "&formato=" . $api_uf["formato"];
            
            $json = file_get_contents($api_url);
            $ufs = json_decode($json, true);
            foreach ($ufs['UFs'] as $uf) {
                $existeUf = Doctrine_Core::getTable('Uf')->retrieveByDate($uf['Fecha']);
                if(!$existeUf) {
                    $uf['Valor'] = str_replace('.', '', $uf['Valor']);
                    $uf['Valor'] = str_replace(',', '.', $uf['Valor']);

                    $ufDB = new Uf();
                    $ufDB->uf_value = $uf['Valor'];
                    $ufDB->uf_date = $uf['Fecha'];
                    $ufDB->save();
                }
            }
        }
        error_log("fin actualizacion UF");
    }
    
    public function actualizarUTFSM(){
        error_log("inicio actualizacion UTFSM");
        $nodos = Doctrine_Core::getTable('InfraInfo')->findNodosValorUTFSM();
        foreach ($nodos as $nodo){
            $nodo->actualizarValorNodoUTFSM();
        }
        error_log("fin actualizacion UTFSM");
    }
}
