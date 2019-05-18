<?php
/**
 */
class ResponsibleTable extends Doctrine_Table {

    function findAll ($query = NULL)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Responsible r' )
                ->orderBy ( 'r.responsible_name ASC' );
        
        if (!is_null($query))
        {
            $q->andWhere('r.responsible_name LIKE ?', $query . '%');
        }

        return $q->execute ();
    }

}
