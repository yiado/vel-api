<?php
/**
 */
class SolicitudTable extends Doctrine_Table {
    
    function retrieveAll ($filters = array())
    {

        $q = Doctrine_Query::create ()
                ->select('s.*,se.*, st.*, u.*')
                ->from ( 'Solicitud s' )
                ->innerJoin ( 's.SolicitudEstado se' )
                ->innerJoin ( 's.SolicitudType st' )
                ->innerJoin ( 's.User u' )
                ->orderBy('solicitud_folio');
        
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

        
        return $q->execute ();

    }
    
    function findById($solicitud_id) {

        $q = Doctrine_Query::create()
                ->from('Solicitud s')
                ->innerJoin ( 's.SolicitudEstado se' )
                ->innerJoin ( 's.SolicitudType st' )
                ->innerJoin ( 's.User u' )
                ->where('solicitud_id = ?', $solicitud_id);



        return $q->execute ();
    }
    
    function lastFolioWo() {

        $q = Doctrine_Query::create()
                ->from('Solicitud s')
                ->orderBy('solicitud_folio DESC')
                ->limit(1);

        $results = $q->fetchOne();

        $last_folio = (int) ( empty($results->solicitud_folio) ? 0 : $results->solicitud_folio );

        return $last_folio;
    }


}
