<?php

/**
 */
class RequestEvaluationTable extends Doctrine_Table {

    function retrieveAll($text_autocomplete = NULL) {

        $q = Doctrine_Query :: create()
                ->from('RequestEvaluation re')
                ->orderBy('re.request_evaluation_name ASC');

        if (!is_null($text_autocomplete)) {
            $q->where('re.request_evaluation_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

}
