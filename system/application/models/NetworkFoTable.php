<?php

/**
 */
class NetworkFoTable extends Doctrine_Table
{

    function findAll ( $node_id = null )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'NetworkFo nf' )
                ->leftJoin ( 'nf.NetworkClient nc' )
                ->where ( 'node_id = ?' , $node_id )
                ->orderBy ( 'network_fo_fiber, network_fo_par' );

        return $q->execute ();
    }

}
