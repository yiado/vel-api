<?php

/**
 */
class ContractTable extends Doctrine_Table
{

    function retrieveAllAsset ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Contract c' )
                ->innerJoin ( 'c.Provider p' )
                ->innerJoin ( 'c.MtnMaintainerType mmt' )
                ->where('c.mtn_maintainer_type_id = ?', 1);

        return $q->execute ();
    }
    
    function retrieveAllNode ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Contract c' )
                ->innerJoin ( 'c.Provider p' )
                ->innerJoin ( 'c.MtnMaintainerType mmt' )
                ->where('c.mtn_maintainer_type_id = ?', 2);

        return $q->execute ();
    }
    
    function retrieveAllByTypeProbiver ($provider_type_id)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Contract c' )
                ->leftJoin ( 'c.Provider p' )
                ->where('p.provider_type_id = ?', $provider_type_id);

        return $q->execute ();
    }

}
