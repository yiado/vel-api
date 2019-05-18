<?php

/** @package    Controller
 *  @subpackage InfraOtherDataAttributeController
 */
class InfraOtherDataAttributeController extends APP_Controller
{
    function InfraOtherDataAttributeController ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista los atributos disponibles para el tipo de nodo
     * @param integer $node_type_id
     * 
     */
    function get ()
    {
        $node_type_id = $this->input->post ( 'node_type_id' );
        
        //Todos los atributos disponibles
        $infraOtherDataAttributeTable = Doctrine_Core::getTable ( 'InfraOtherDataAttribute' );
        $infraOtherDataAttribute = $infraOtherDataAttributeTable->retrieveAll ( $node_type_id );

        $json_data = $this->json->encode ( array ( 'total' => $infraOtherDataAttribute->count () , 'results' => $infraOtherDataAttribute->toArray () ) );
        echo $json_data;
    }
    
    function getsearch ()
    {
        //Todos los atributos disponibles
        $infraOtherDataAttributeTable = Doctrine_Core::getTable ( 'InfraOtherDataAttribute' );
        $infraOtherDataAttribute = $infraOtherDataAttributeTable->retrieveAll ( null , $this->config->item ( 'infra_otherdatafield_enable_search' ) );

        $json_data = $this->json->encode ( array ( 'total' => $infraOtherDataAttribute->count () , 'results' => $infraOtherDataAttribute->toArray () ) );
        echo $json_data;
    }

    /**
     * add
     * 
     * Agrega un nuevo atributo para info
     * 
     * @post string infra_other_data_attribute_name
     * @post string infra_other_data_attribute_type
     */
    function add ()
    {
        $infoAtt = new InfraOtherDataAttribute();
        $infoAtt->infra_other_data_attribute_name = $this->input->post ( 'infra_other_data_attribute_name' );
        $infoAtt->infra_other_data_attribute_type = $this->input->post ( 'infra_other_data_attribute_type' );
        $infoAtt->infra_grupo_id = $this->input->post ( 'infra_grupo_id' );

        try
        {
            $infoAtt->save ();

            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );

            $infra_other_data_attribute_id = $infoAtt->infra_other_data_attribute_id;
            $success = true;
        }
        catch ( Exception $e )
        {
            $infra_other_data_attribute_id = NULL;
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg , 'infra_other_data_attribute_id' => $infra_other_data_attribute_id ) );
        echo $json_data;
    }

    /**
     * update 
     * 
     * Modifica atributo de info
     * 
     * @post int infra_other_data_attribute_id
     * @post string infra_other_data_attribute_name
     * @post string infra_other_data_attribute_type
     */
    function update ()
    {
        $infoAtt = Doctrine_Core::getTable ( 'InfraOtherDataAttribute' )->find ( $this->input->post ( 'infra_other_data_attribute_id' ) );
        $infoAtt->infra_other_data_attribute_name = $this->input->post ( 'infra_other_data_attribute_name' );
        $infoAtt->infra_other_data_attribute_type = $this->input->post ( 'infra_other_data_attribute_type' );
        $infoAtt->infra_grupo_id = $this->input->post ( 'infra_grupo_id' );

        try
        {
            $infoAtt->save ();
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
     * delete
     * 
     * Elimina un Dato Dinamico si no existe un Tipo de Nodo asociado
     * @param  integer $infra_other_data_attribute_id
     */
    function delete ()
    {

        $infra_other_data_attribute_id = $this->input->post ( 'infra_other_data_attribute_id' );
        $checkDataInAttributeNodeType = Doctrine::getTable ( 'InfraOtherDataAttribute' )->checkDataInAttributeNodeType ( $infra_other_data_attribute_id );
        if ( $checkDataInAttributeNodeType === false )
        {

            Doctrine::getTable ( 'InfraOtherDataAttribute' )->deleteAttribute ( $infra_other_data_attribute_id );
            $exito = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        else
        {
            $exito = false;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'Infrastructure' , 'dynamic_data_can_not_be_eliminated' );
        }

        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }
}
