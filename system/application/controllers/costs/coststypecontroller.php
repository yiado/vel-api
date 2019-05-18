<?php

/**
 * @package Controller
 * @subpackage CostsTypeController
 */
class CostsTypeController extends APP_Controller 
{

    function CostsTypeController() {
        parent :: APP_Controller();
    }

   
    /**
     * get
     * 
     * Lista todos los tipos de costos existentes
     */
    function get() 
    {
        $text_autocomplete = $this->input->post('query');
        $costsType = Doctrine_Core::getTable('CostsType')->retrieveAll($text_autocomplete);

        if ($costsType->count()) {
            echo '({"total":"' . $costsType->count() . '", "results":' . $this->json->encode($costsType->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    /**
     * add
     * 
     * Agrega un nombre de costo al sistema
     * 
     * @post string costs_type_name
     */
    function add ()
    {
        $costType = new CostsType();
        $costType[ 'costs_type_name' ] = $this->input->post ( 'costs_type_name' );

        try
        {
            $costType->save ();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
    
     /**
     * update
     * 
     * Modifica un nombre de costo al sistema
     * 
     * @post int costs_type_id
     * @post string costs_type_name
     */
    function update ()
    {
        $costType = Doctrine_Core::getTable ( 'CostsType' )->find ( $this->input->post ( 'costs_type_id' ) );
        $costType[ 'costs_type_name' ] = $this->input->post ( 'costs_type_name' );
        $costType->save ();
        echo '{"success": true}';
    }
    
    
     /**
     * delete
     * 
     * Elimina un nombre de costo en laso de no estar asociado al costo
     * @post int costs_type_id
     */
    function delete ()
    {
        $costs_type_id = $this->input->post ( 'costs_type_id' );
        $checkCostTypeInCost = Doctrine::getTable ( 'CostsType' )->checkCostTypeInCost ( $costs_type_id );
        if ( $checkCostTypeInCost === false )
        {
            $costType = Doctrine::getTable ( 'CostsType' )->find ( $costs_type_id );
            if ( $costType->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = 'Error: ';
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'General' , 'cost_not_to_delete_message_being_associated' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }
  

}