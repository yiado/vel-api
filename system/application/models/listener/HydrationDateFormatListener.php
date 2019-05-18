<?php

class HydrationDateFormatListener extends Doctrine_Record_Listener {

    /**
     * preHydrate
     *
     * Tranforma los campos de fecha al formato de entrada/salida
     * en el momento de hodratar el objeto
     *
     * @param Doctrine_event $Event
     *
     */
    public function preHydrate ( Doctrine_Event $event ) {

		$CI = & get_instance();
		$dbms = $CI->db->dbdriver;
		$date_format = $CI->config->item('date_format');
		$data = $event->data;

        foreach ($event->getInvoker()->getColumns() as $FieldName => $Field) {

            if (!array_key_exists($FieldName, $data)) continue;

            if ($Field['type'] == 'date' && !is_null($data[$FieldName])) {
               $data[$FieldName] = mdate($date_format['in_out_format'], strtotime($data[$FieldName]));
            }

        }

        $event->data = $data;

    }

    /**
     * preInsert
     *
     * Escucha que se dispara en el momento de ingreso de registro
     *
     * @param Doctrine_event $Event
     *
     */
    public function preInsert(Doctrine_Event $event){

        $this->_prepare_date_in($event);

    }

    /**
     * preUpdate
     *
     * Escucha que se dispara en el momento de modifiacion de registro
     *
     * @param Doctrine_event $Event
     *
     */
    public function preUpdate(Doctrine_Event $event){

        $this->_prepare_date_in($event);

    }

    /**
     * _prepare_date_in
     *
     * Tranforma los campos de fecha al formato de entrada/salida
     * al momento de operaciones de modificacion/ingreso de registros
     *
     * @param Doctrine_event $Event
     *
     */
    private function _prepare_date_in ( Doctrine_Event $event ) {

		$CI = & get_instance();
		$dbms = $CI->db->dbdriver;
		$date_format = $CI->config->item('date_format');

        $Model = $event->getInvoker();
        foreach ($Model->getTable()->getColumns() as $FieldName => $Field) {

            if ($Field['type'] == 'date' && !is_null($Model->$FieldName)) {
            	$Model->$FieldName = mdate($date_format[$dbms], mysql_to_unix(str_ireplace('T', ' ', $Model->$FieldName)));
            }

        }

    }

}