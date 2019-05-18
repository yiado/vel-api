<?php
/**
 */
class ApplicantTable extends Doctrine_Table {


    function findAll ($query = NULL)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Applicant a' )
                ->orderBy ( 'a.applicant_name ASC' );
        
        if (!is_null($query))
        {
            $q->andWhere('a.applicant_name LIKE ?', $query . '%');
        }

        return $q->execute ();
    }
}
