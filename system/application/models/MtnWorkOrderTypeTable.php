<?php

/**
 */
class MtnWorkOrderTypeTable extends Doctrine_Table
{
    /*
     * Retorna todos los Tipos de OT
     */

    function retrieveAll ( $text_autocomplete = NULL , $show_predictive_ot = true )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderType mwot' )
                ->where ( 'mwot.mtn_work_order_type_abbreviation  = ?' , 'CR' )
                ->orwhere ( 'mwot.mtn_work_order_type_id >3' );
  

        return $q->execute ();
    }
    
    /*
     * Retorna todos los Tipos de OT
     */

    function retrieveTotal ( $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderType mwot' )
                ->orderBy ( 'mwot.mtn_work_order_type_name ASC' );


        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'mwot.mtn_work_order_type_name LIKE ?' , $text_autocomplete . '%' );
        }
        $q->andWhere('mwot.mtn_maintainer_type_id = ?', $maintainer_type);
        
        return $q->execute ();
    }
    
    function retrieveTotalSolo ( $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderType mwot' )
                ->orderBy ( 'mwot.mtn_work_order_type_name ASC' );


        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'mwot.mtn_work_order_type_name LIKE ?' , $text_autocomplete . '%' );
        }
        $q->andWhere('mwot.mtn_maintainer_type_id = ?', $maintainer_type);
        $q->andWhere('mwot.mtn_work_order_type_id != ?', 8);
        
        return $q->execute ();
    }
    
    function retrieveTotalSoloAsset ( $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderType mwot' )
                ->orderBy ( 'mwot.mtn_work_order_type_name ASC' );


        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'mwot.mtn_work_order_type_name LIKE ?' , $text_autocomplete . '%' );
        }
        $q->andWhere('mwot.mtn_maintainer_type_id = ?', $maintainer_type);
        $q->andWhere('mwot.mtn_work_order_type_id != ?', 2);
        $q->andWhere('mwot.mtn_work_order_type_id != ?', 3);
        
        return $q->execute ();
    }

    function retrievePreventive ( $text_autocomplete = NULL , $show_predictive_ot = true )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderType mwot' )
                ->where ( 'mwot.mtn_work_order_type_abbreviation  = ?' , 'CR' )
                ->orwhere ( 'mwot.mtn_work_order_type_abbreviation  = ?' , 'PR' )
                ->orwhere ( 'mwot.mtn_work_order_type_abbreviation  = ?' , 'PD' )
                ->orwhere ( 'mwot.mtn_work_order_type_id >3' );

        return $q->execute ();
    }

    function retrieveAllHigher ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderType mwot' )
                ->where ( 'mwot.mtn_work_order_type_id >3' );

        return $q->execute ();
    }

}
