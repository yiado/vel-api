<?php

/**
 * @package Model
 * @subpackage DocDocumentTable
 */
class DocDocumentTable extends Doctrine_Table {

    function findByNodeId($node_id) {

        $q = Doctrine_Query :: create()
                ->from('DocDocument dc')
                ->where('dc.node_id = ?', $node_id)
                ->andWhere('dc.doc_category_id= ?', 12);

        return $q->execute();
    }

    function findByDocumentId($doc_document_id) {

        $q = Doctrine_Query :: create()
                ->from('DocDocument dc')
                ->leftJoin('dc.DocCurrentVersion dvc')
                ->innerJoin('dvc.User us')
                ->innerJoin('dc.DocExtension de')
                ->innerJoin('dc.DocCategory dca')
                ->where('dc.doc_status_id = ?', 0)
                ->andWhere('dc.doc_document_id= ?', $doc_document_id);

        return $q->fetchOne();
    }

    function retrieveByDocumentVencido() {

        $q = Doctrine_Query :: create()
                ->from('DocVersion dv')
                ->innerJoin('dv.DocDocument dc')
                ->leftJoin('dc.DocExtension de')
                ->leftJoin('dc.DocCategory dca')
                ->where('dv.doc_version_expiration IS NOT NULL')
                ->andWhere('dv.doc_version_alert IS NOT NULL');

        return $q->execute();
    }

    function findByNodeDefault($doc_document_id) {

        $q = Doctrine_Query :: create()
                ->from('DocDocument dc')
                ->leftJoin('dc.DocCurrentVersion dvc')
                ->where('dc.doc_document_id = ?', $doc_document_id);

        return $q->fetchOne();
    }

    /**
     * @param int $filters
     */
    function retrieveByNode($filters = array(), $node_id, $search_branch = false) {

        $q = Doctrine_Query :: create()
                ->select('dc.*,dvc.*, us.*, de.*, dca.*')
                ->from('DocDocument dc')
                ->leftJoin('dc.DocCurrentVersion dvc')
                ->innerJoin('dc.Node n')
                ->innerJoin('dvc.User us')
                ->innerJoin('dc.DocExtension de')
                ->innerJoin('dc.DocCategory dca')
                ->where('dc.doc_status_id = ?', 0)
                ->andWhere('dc.doc_status_id = ?', '');




        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                if ($flag === false) {

                    $q->andWhere($field, $value);
                    $flag = true;
                } else {

                    $q->andWhere($field, $value);
                }
            }
        }

        if ($search_branch) {

            $node = Doctrine_Core :: getTable('Node')->find($node_id);

            $q->andWhere('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {

            $q->andWhere('n.node_id = ?', $node_id);
        }


        return $q->execute(array(), Doctrine_Core :: HYDRATE_ARRAY);
    }

    function findById($doc_document_id) {

        $q = Doctrine_Query::create()
        ->from('DocDocument dc')
        ->leftJoin('dc.DocCurrentVersion dvc')
                ->where('dc.doc_document_id = ?', $doc_document_id);

        

        return $q->fetchOne();
    }

    function findVersionDoc($doc_document_id) {

        $q = Doctrine_Query :: create()
                ->from('DocDocument dc')
                ->leftJoin('dc.DocVersion dv')
                ->leftJoin('dc.DocCurrentVersion dCv')
                ->where('dc.doc_document_id = ?', $doc_document_id)
                ->orderBy('dv.doc_version_id DESC');

        return $q->fetchOne();
    }

    function findByVersion($doc_document_id) {

        $q = Doctrine_Query :: create()
                ->from('DocVersion dv')
                ->where('dv.doc_document_id = ?', $doc_document_id)
                ->orderBy('dv.doc_version_id DESC');

        return $q->fetchOne();
    }

    function retrieveByNodeBin($filters = array(), $node_id, $search_branch = false) {

        $q = Doctrine_Query :: create()
                ->select('dc.*,dvc.*, us.*, de.*, dca.*')
                ->from('DocDocument dc')
                ->leftJoin('dc.DocCurrentVersion dvc')
                ->innerJoin('dc.Node n')
                ->innerJoin('dvc.User us')
                ->innerJoin('dc.DocExtension de')
                ->innerJoin('dc.DocCategory dca')
                ->where('dc.doc_status_id = ?', 1);

        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                if ($flag === false) {

                    $q->andWhere($field, $value);
                    $flag = true;
                } else {

                    $q->andWhere($field, $value);
                }
            }
        }


        return $q->execute(array(), Doctrine_Core :: HYDRATE_ARRAY);
    }

    function compFileName($file_input, $node_id) {
        $q = Doctrine_Query :: create()->from('DocDocument dd')->where('dd.doc_document_filename = ?', $file_input)->andWhere('dd.node_id = ?', $node_id)->limit(1);

        $results = $q->execute();
        return ($results->count() == 1 ? true : false);
    }

    function retrieveById($doc_document_id) {
        $q = Doctrine_Query :: create()->from('DocDocument dc')->where('doc_document_id = ?', $doc_document_id);

        return $q->fetchOne();
    }

}
