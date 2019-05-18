<?php

/**
 * @package Model
 * @subpackage NodeTypeTable 
 */
class NodeTypeTable extends Doctrine_Table
{
    
    function retrieveAllPlan (  )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'NodeType nt' )
                ->innerJoin ( 'nt.NodeTypeCategory ntc' )
                ->leftJoin ( 'nt.PlanCategory pc' )
                ->orderBy ( 'nt.node_type_name' );
        
        return $q->execute ();
    }

    /**
     * retrieveAll
     * 
     * Retorna listado de todos los tipo nodo
     * 
     * @return array
     */
    function retrieveAll (  )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'NodeType' )
                ->orderBy ( 'node_type_name' );
        
        return $q->execute ();
    }

    /**
     * findByCategory
     * 
     * Encuentra por categoria de un tipo de nodo
     * 
     * @return array
     */
    function findByCategory ( $node_type_category_id = NULL, $text_autocomplete = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'NodeType nt' )
                ->innerJoin ( 'nt.NodeTypeCategory ntc' )
                ->orderBy ( 'nt.node_type_name' );

        if ( ! is_null ( $node_type_category_id ) )
        {
            $q->where ( 'nt.node_type_category_id = ?' , $node_type_category_id );
        }
        
         if ( ! is_null ( $text_autocomplete ) )
        {
            $q->andWhere ( 'node_type_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }

    function retriveById ( $node_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'NodeType nt' )
                ->where ( 'nt.node_type_id = ?' , $node_type_id );

        return $q->fetchOne ();
    }

    function checkNodeInNodeType ( $node_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( 'nt.node_type_id = ?' , $node_type_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }
    
    /**
     *
     * nodeTypeInTable
     * Retorna true en el caso que exista un Nombre igual en la Tabla  y False en el caso contrario
     */
    function nodeTypeInTable ( $node_type_name )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'NodeType nt' )
                ->where ( 'nt.node_type_name = ?' , $node_type_name )
                ->limit ( 1 );

        return $q->execute ();
        
    }

}