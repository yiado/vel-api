<?php
class bechjob extends APP_Controller {

	function bechjob () {
		parent :: APP_Controller();
	}

	function index () {
		
		$this->calculoMuroPiso();
		
		$this->calculoMuroSucursal();
		
	}
	
	function calculoMuroPiso () {
		
		$q = Doctrine_Query :: create()
			 ->from('Node n')
			 ->innerJoin('n.InfraInfo ii')
			 ->where('node_type_id = ?', 5);
			
		$results = $q->execute(array(), Doctrine_Core :: HYDRATE_SCALAR);
		
		foreach ($results as $piso) {
			
			$resta = $piso['ii_infra_info_area'] - $piso['ii_infra_info_usable_area_total'];
			
			$value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($piso['n_node_id'], 61);
			
            if (!$value) {
                $value = new InfraOtherDataValue();
            }

            $value->infra_other_data_attribute_id = 61;
            $value->node_id = $piso['n_node_id'];
            $value->infra_other_data_value_value = $resta;
            
            $value->save();
			
		}
		
	}
	
	function calculoMuroSucursal () {
		
		$q = Doctrine_Query :: create()
			 ->from('Node n')
			 ->innerJoin('n.InfraInfo ii')
			 ->where('node_type_id = ?', 4);
			
		$results = $q->execute(array(), Doctrine_Core :: HYDRATE_SCALAR);
		
		foreach ($results as $piso) {
			
			$resta = $piso['ii_infra_info_area_total'] - $piso['ii_infra_info_usable_area_total'];
			
			$value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($piso['n_node_id'], 61);
			
            if (!$value) {
                $value = new InfraOtherDataValue();
            }

            $value->infra_other_data_attribute_id = 61;
            $value->node_id = $piso['n_node_id'];
            $value->infra_other_data_value_value = $resta;
            
            $value->save();
			
		}
		
	}

}