<?php

/**
 * @package Model
 * @subpackage NodeTypeCategoryTable
 */
class NodeTypeCategoryTable extends Doctrine_Table
{

    /**
     * 
     * retrieveAll
     * 
     * Retorna listado de todas las categorias de tipo de nodo
     * 
     * @return array
     */
    function retrieveAll ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query::create (  )
                ->from ( 'NodeTypeCategory' )
                ->orderBy ( 'node_type_category_name' );
        
         if ( ! is_null ( $text_autocomplete ) )
        {
            $q->andWhere ( 'node_type_category_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }

    /**
     *
     * checkNodeInCategory
     * Retorna true en el caso que exista un Nodo asociado a la categoria y False en el caso contrario
     */
    function checkNodeInCategory ( $node_type_category_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->innerJoin ( 'nt.NodeTypeCategory ntc' )
                ->where ( 'ntc.node_type_category_id = ?' , $node_type_category_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

}

