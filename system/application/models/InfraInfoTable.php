<?php

/**
 */
class InfraInfoTable extends Doctrine_Table
{

    function findByNodeId ( $node_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'InfraInfo ff' )
                ->where ( 'ff.node_id = ?' , $node_id );

        return $q->fetchOne ();
    }

}
