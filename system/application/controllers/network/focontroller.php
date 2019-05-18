<?php

/**
 * @package    Controller
 * @subpackage RequestProblemController
 */
class FoController extends APP_Controller
{
    function FoController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista todos los tipos de equipos existentes
     */
    function get ()
    {
        $fo = Doctrine_Core::getTable ( 'NetworkFo' )->findAll ( $this->input->post ( 'node_id' ) );

        if ( $fo->count () )
        {
            echo '({"total":"' . $fo->count () . '", "results":' . $this->json->encode ( $fo->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getDiagram ()
    {
        //caso especial para la descripción por el comodin usado en el like.
        $filters = array (
            'node_id = ?' => $this->input->post ( 'node_id' ) ,
            'plan_category_id = ?' => 1
        );
        $plans = Doctrine_Core::getTable ( 'Plan' )->retrieveByNode ( $filters );

        echo '({"diagram":' . $this->json->encode ( end ( $plans->toArray () ) ) . '})';
    }

    function update ()
    {
        $fo = Doctrine_Core::getTable ( 'NetworkFo' )->find ( $this->input->post ( 'network_fo_id' ) );
        $fo->fromArray ( $this->input->postall () );
        $fo->save ();
        echo '{"success": true}';
    }

    function addFiber ()
    {
        for ( $i = 0; $i < $this->input->post ( 'quantity' ); $i ++  )
        {
            for ( $a = 1; $a <= 1; $a ++  )
            {
                $f = new NetworkFo();
                $f->node_id = $this->input->post ( 'node_id' );
                $f->network_fo_fiber = ($i + $this->input->post ( 'start' ));
                $f->network_fo_par = $a;
                $f->network_fo_status = $this->input->post ( 'network_fo_status' );
                $f->network_fo_commercial_status = $this->input->post ( 'network_fo_commercial_status' );
                $f->save ();
            }
        }
        echo '{"success": true}';
    }
}