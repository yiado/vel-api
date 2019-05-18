<?php
/**
 */
class HelpTable extends Doctrine_Table {

    function retrieveBySort (  )
    {

        $q = Doctrine_Query::create (  )
                ->from ( 'Help' )
                ->orderBy ( 'help_sort' );
 
        return $q->execute ();
    }
}
