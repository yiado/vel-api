<?php

/**
 */
class CostsTable extends Doctrine_Table
{

    /**
     * Retorna los Costos del sistema;
     *   - Permite buscar por autocompletado de texto
     *
     * @param string $text_autocomplete
     */

    function retrieveAll($filters = array(), $node_id, $search_branch = false)
    {
        $q = Doctrine_Query :: create()
                ->select('c.*, n.*, ct.*, cm.*')
                ->from('Costs c')
                ->innerJoin('c.Node n')
                ->leftJoin('c.CostsType ct')
                ->leftJoin('c.CostsMonth cm');

		$flag = false;
		foreach ($filters as $field => $value) {

			if (!is_null($value)) {

				if ($flag === false) {

					$q->where($field, $value);
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
		
		return $q->execute(array (), Doctrine_Core :: HYDRATE_ARRAY);
    }


    /**
     * retrieveAllCostType
     * 
     * Recupera los meses 
     */
    function retrieveAllCostMonth($text_autocomplete = NULL)
    {

        $q = Doctrine_Query :: create()
                ->from('CostsMonth cm')
                ->orderBy('cm.costs_month_id ASC');

        if (!is_null($text_autocomplete))
        {
            $q->where('cm.costs_month_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

}
