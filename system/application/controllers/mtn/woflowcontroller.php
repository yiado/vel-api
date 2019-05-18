<?php

/** @package    Controller
 *  @subpackage Woflowcontroller
 */
class Woflowcontroller extends APP_Controller
{
    function Woflowcontroller ()
    {
        parent::APP_Controller ();
    }

    /**
     * Retorna el flujo de estados para una ot
     *
     * @param post data
     */
    function get ()
    {
        $mtn_work_order_id = ( int ) $this->input->post ( 'mtn_work_order_id' );
        $flowStatusWo = Doctrine_Core::getTable ( 'MtnWorkOrderStatus' )->retrieveFlowStatusWo ( $mtn_work_order_id );
        $json_data = $this->json->encode ( array ( 'total' => $flowStatusWo->count () , 'results' => $flowStatusWo->toArray () ) );
        echo $json_data;
    }

    function add ()
    {
        
    }

    function update ()
    {
        
    }

    function delete ()
    {
        
    }

}