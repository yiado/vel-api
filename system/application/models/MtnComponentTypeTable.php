<?php

/**
 * 
 * @author manuteko
 *
 */
class MtnComponentTypeTable extends Doctrine_Table
{
    /*
     * Devuelve todos los tipos de insumos (components)
     */

    function retrieveAll ($text_autocomplete = NULL,$maintainer_type )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnComponentType ct' )
                ->orderBy ( 'ct.mtn_component_type_name ASC' );
        
        if (!is_null($text_autocomplete))
        {
            $q->where('ct.mtn_component_type_name LIKE ?', $text_autocomplete . '%');
        }
        
        $q->andWhere('ct.mtn_maintainer_type_id = ?', $maintainer_type );
        return $q->execute ();
    }

    /**
     *
     * checkDataInComponent
     * Retorna true en el caso que exista un Dato asociado a un Componente y False en el caso contrario
     * @param integer $mtn_component_type_id
     */
    function checkDataInComponent ( $mtn_component_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnComponent mc' )
                ->where ( 'mc.mtn_component_type_id = ?' , $mtn_component_type_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

}
