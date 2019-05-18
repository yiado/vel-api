<?php

/** @package    Controller
 *  @subpackage ComponentTypeController
 */
class ComponentTypeController extends APP_Controller
{
    function ComponentTypeController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Retorna todos los tipoe de componentes
     * 
     */
    function get ()
    {
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $componentTypeTable = Doctrine_Core::getTable ( 'MtnComponentType' );
        $maintainer_type=1; //asset
        $componentType = $componentTypeTable->retrieveAll ( $mtn_component_type_id ,$maintainer_type );

        if ( $componentType->count () )
        {
            echo '({"total":"' . $componentType->count () . '", "results":' . $this->json->encode ( $componentType->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    function getByNode ()
    {
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $componentTypeTable = Doctrine_Core::getTable ( 'MtnComponentType' );
        $maintainer_type=2;//node
        $componentType = $componentTypeTable->retrieveAll ( $mtn_component_type_id,$maintainer_type );

        if ( $componentType->count () )
        {
            echo '({"total":"' . $componentType->count () . '", "results":' . $this->json->encode ( $componentType->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agregar un nuevo tipo de componente
     * 
     * @post integer mtn_component_type_id
     * @post string mtn_component_type_name
     */
    function add ()
    {
        $componentType = new MtnComponentType();
        $componentType[ 'mtn_component_type_name' ] = $this->input->post ( 'mtn_component_type_name' );
        $componentType[ 'mtn_maintainer_type_id' ] = 1;

        try
        {
            $componentType->save ();
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
    function addByNode ()
    {
        $componentType = new MtnComponentType();
        $componentType[ 'mtn_component_type_name' ] = $this->input->post ( 'mtn_component_type_name' );
        $componentType[ 'mtn_maintainer_type_id' ] = 2;

        try
        {
            $componentType->save ();
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
     * Modifica una un tipo de componente
     * 
     * @post integer mtn_component_type_id
     * @post string mtn_component_type_name
     */
    function update ()
    {
        $componentType = Doctrine_Core::getTable ( 'MtnComponentType' )->find ( $this->input->post ( 'mtn_component_type_id' ) );
        $componentType[ 'mtn_component_type_name' ] = $this->input->post ( 'mtn_component_type_name' );
        $componentType->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     * 
     * Elimina un un tipo de componente si no esta asociado al componente
     * @param  integer mtn_component_type_id
     */
    function delete ()
    {
        $mtn_component_type_id = $this->input->post ( 'mtn_component_type_id' );
        $componentType = Doctrine::getTable ( 'MtnComponentType' )->checkDataInComponent ( $mtn_component_type_id );
        if ( $componentType === false )
        {
            $component = Doctrine::getTable ( 'MtnComponentType' )->find ( $mtn_component_type_id );
            if ( $component->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = "Error";
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'Maintenance' , 'you_can_not_delete_because_it_is_associated' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }

}