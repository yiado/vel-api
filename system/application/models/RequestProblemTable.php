<?php

/**
 */
class RequestProblemTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Recupera los problemas
     */
    function retrieveAll($text_autocomplete = NULL)
    {

        $q = Doctrine_Query :: create()
                ->from('RequestProblem rp')
                ->orderBy('rp.request_problem_name ASC');

        if (!is_null($text_autocomplete))
        {
            $q->where('rp.request_problem_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

}
