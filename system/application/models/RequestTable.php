<?php

/**
 */
class RequestTable extends Doctrine_Table
{

    function retriveRequest ( $filters=array ( ), $node_id, $search_branch=true )
    {

        $q = Doctrine_Query::create ()
                ->select('r.*,a.*, rs.*, rp.*')
                ->from ( 'Request r' )
                ->innerJoin ( 'r.RequestProblem rp' )
                ->innerJoin ( 'r.Asset a' )
                ->innerJoin ( 'a.Node n' )
                ->leftJoin ( 'r.RequestStatus rs' );
      
        if ( $search_branch )
        {
            $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );
            $q->andWhere ( 'n.node_parent_id = ?' , $node->node_parent_id )
                    ->andWhere ( 'n.lft >= ?' , $node->lft )
                    ->andWhere ( 'n.rgt <= ?' , $node->rgt );
        }
        else
        {
            $q->andWhere ( 'a.node_id = ?' , $node_id );
        }

        foreach ( $filters as $field => $value )
        {

            if ( ! is_null ( $value ) )
            {

                $q->andWhere ( $field , $value );
            }
        }
        $q->orderBy ( 'request_date_creation' );
    
        return $q->execute ();
    }
    function retriveRequestByNode ( $filters=array ( ), $node_id, $search_branch=true )
    {

        $q = Doctrine_Query::create ()
                ->select('r.*, rs.*, rp.*')
                ->from ( 'Request r' )
                ->innerJoin ( 'r.RequestProblem rp' )
            //    ->innerJoin ( 'r.Asset a' )
                ->innerJoin ( 'r.Node n' )
                ->leftJoin ( 'r.RequestStatus rs' );
      
        if ( $search_branch )
        {
            $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );
            $q->andWhere ( 'n.node_parent_id = ?' , $node->node_parent_id )
                    ->andWhere ( 'n.lft >= ?' , $node->lft )
                    ->andWhere ( 'n.rgt <= ?' , $node->rgt );
        }
        else
        {
            $q->andWhere ( 'r.node_id = ?' , $node_id );
        }

        foreach ( $filters as $field => $value )
        {

            if ( ! is_null ( $value ) )
            {

                $q->andWhere ( $field , $value );
            }
        }
        $q->orderBy ( 'request_date_creation' );
    
        return $q->execute ();
    }

    function retriveProvider ( $filters=array ( ) , $provider_id )
    {


        $q = Doctrine_Query::create ()
                ->from ( 'Request r' )
                ->innerJoin ( 'r.RequestProblem rp' )
                ->innerJoin ( 'r.Asset a' )
                ->leftJoin ( 'r.RequestStatus rs' )
                ->where ( 'a.provider_id = ?' , $provider_id )
        ;


        foreach ( $filters as $field => $value )
        {

            if ( ! is_null ( $value ) )
            {

                $q->andWhere ( $field , $value );
            }
        }
        $q->orderBy ( 'request_date_creation' );
        return $q->execute ();
    }

    function lastFolioWo ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Request r' )
                ->orderBy ( 'request_folio DESC' )
                ->limit ( 1 );

        $results = $q->fetchOne ();

        $last_folio = ( int ) ( empty ( $results->request_folio ) ? 0 : $results->request_folio );

        return $last_folio;
    }

}
