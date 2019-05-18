<?php
/**
 */
class LogDetailTable extends Doctrine_Table 
{
     /**
     *
     * Retorna true en el caso que existan detalles en el LogDetail asociado al Log y false en el caso contrario
     * @param integer $log_ig
     * @return Devuelve todos los LoDetail asociados.
     */
    function checkLogId($log_ig)
    {
	$q = Doctrine_Query::create()
		->from('LogDetail ld')
		->innerJoin('ld.Log l')
		->where('log_id = ?', $log_ig)
		->orderBy('log_detail_param ASC');

	return $q->execute();
    }


}
