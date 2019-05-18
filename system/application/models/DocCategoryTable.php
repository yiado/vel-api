<?php

/**
 * @package Model
 * @subpackage DocCategoryTable
 */
class DocCategoryTable extends Doctrine_Table {

    /**
     * retrieveByCate
     * 
     * retorna categoria por id
     * 
     * @param int $nod_id
     */
    function retrieveByCate($doc_category_id, $text_autocomplete = NULL) {
        $q = Doctrine_Query::create()
                ->from('DocCategory dc')
                ->orderBy('doc_category_name ASC');

        if (!is_null($text_autocomplete)) {
            $q->andWhere('doc_category_name LIKE ?', $text_autocomplete . '%');
        }



        return $q->execute();
    }

    /**
     * Devuelve la info de una categoria especifica
     * @param integer $doc_category_id
     */
    function retrieveById($doc_category_id) {

        $q = Doctrine_Query::create()
                ->from('DocCategory dc')
                ->where('dc.doc_category_id = ?', $doc_category_id);

        return $q->fetchOne();
    }

    /**
     * 
     * Retorna true en el caso que exista un plano asociado a la categoria y false en el caso contrario
     * @param integer $node_type_id
     * @return boolean true|false
     */
    function checkCategoryInDocument($doc_category_id) {

        $q = Doctrine_Query::create()
                ->from('DocCategory dc')
                ->innerJoin('dc.DocDocument ddc')
                ->where('dc.doc_category_id = ?', $doc_category_id)
                ->limit(1);

        $results = $q->execute();

        return ($results->count() == 0 ? false : true);
    }

    function finbByNomCategory($nomCategoria) {

        $q = Doctrine_Query::create()
                ->from('DocCategory dc')
                ->where('dc.doc_category_name = ?', $nomCategoria);

        return $q->fetchOne();
    }
    
    function findAll () {
    	
        $q = Doctrine_Query::create()
                ->from('DocCategory dc')
                ->orderBy('doc_category_name');

        return $q->execute();
    	
    }

}
