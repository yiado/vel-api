<?php

/**
 */
class ProviderTypeTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Recupera todos los tipo proveedor
     */
    function retrieveAll ( $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'ProviderType' )
                ->orderBy ( 'provider_type_name ASC' );
        
         if (!is_null($text_autocomplete))
        {
            $q->andWhere('provider_type_name LIKE ?', $text_autocomplete . '%');
        }
       $q->andWhere('mtn_maintainer_type_id = ?', $maintainer_type);
        return $q->execute ();
    }

    /**
     * 
     * Recupera la tupla del tipo de proveedor
     * @param integer provider_type_id
     * @return 1 Row
     */
    function retrieveById ( $provider_type_id )
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'ProviderType pt' )
                ->where ( 'pt.provider_type_id = ?' , $provider_type_id );

        return $q->fetchOne ();
    }

    /**
     *
     * Retorna true en el caso que exista un proveedor asociado al tipo y false en el caso contrario
     * @param integer $provider_type_id
     * @return boolean true|false
     */
    function checkProviderTypeInProvider ( $provider_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Provider p' )
                ->innerJoin ( 'p.ProviderType pt' )
                ->where ( 'pt.provider_type_id = ?' , $provider_type_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }

}
