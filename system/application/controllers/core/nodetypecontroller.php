<?php

/**
 * @package Controller
 * @subpackage NodeTypeController
 */
class NodeTypeController extends APP_Controller
{

    function NodeTypeController()
    {
        parent::APP_Controller();
    }

    /**
     * getList
     *
     * Retorna tipo de nodo
     *
     * @post int node_type_category_id
     */
    function getList()
    {
        $nodeTypeTable = Doctrine_Core::getTable('NodeType');
        $text_autocomplete = $this->input->post('query');
        $node_type = $nodeTypeTable->findByCategory($this->input->post('node_type_category_id'), $text_autocomplete);

        if ($node_type->count())
        {
            echo '({"total":"' . $node_type->count() . '", "results":' . $this->json->encode($node_type->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agrega un nuevo tipo de nodo
     *
     * @post int node_type_category_id
     * @post string node_type_name
     */
    function add()
    {
        $success = false;

        //Obtenemos la conexi�n actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

        //Iniciamos la transacci�n
        $conn->beginTransaction();

        $file_uploaded = $this->input->file('icon');
        $file_extension = $this->app->getFileExtension($file_uploaded['name']);

        list($width, $height, $type, $attr) = @getimagesize($file_uploaded['tmp_name']);

        if ($file_extension != 'gif' || $width != 16 || $height != 16)
        {
            $msg = $this->translateTag('General', 'icone_not_qualify');
        } else
        {
            try
            {
                $nodeType = new NodeType();
                $nodeType->fromArray($this->input->postall());
                $nodeType->node_type_location  = $this->input->post('node_type_location');

                $node_type_name = $this->input->post('node_type_name');
               

                $nodeTypeInTable = Doctrine::getTable('NodeType')->nodeTypeInTable($node_type_name);
                $result = ($nodeTypeInTable->count() == 0 ? 'false' : 'true');
                
                if ($result === 'false')
                {
                    $nodeType->save();
                    $msg = $this->translateTag('General', 'operation_successful');

                    //Creamos el nombre para el nuevo documento
                    $config['upload_path'] = $this->config->item('node_icon_dir');
                    $config['allowed_types'] = $file_extension;
                    $config['file_name'] = $nodeType->node_type_id . '.gif';

                    //Restringuir tama�o y peso?
                    //Carga de la libreria para el upload
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('icon'))
                    {
                        $msg = $this->upload->display_errors('-', '\n');
                        throw new Exception($msg);
                    } else
                    {
                        // Si todo OK, commit a la base de datos
                        $conn->commit();
                        $msg = $this->translateTag('General', 'operation_successful');
                        $success = true;
                    }
                } else {
                    $success = false;
                    $msg = $this->translateTag('General', 'there_is_a_name');
                }
            } catch (Exception $e)
            {
                $msg = $e->getMessage();
            }
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica un tipo de nodo
     *
     * @post int node_type_id
     * @post int node_type_category_id
     * @post string node_type_name
     */
    function update()
    {
        $node_type_id = $this->input->post('node_type_id');
        $nodeType = Doctrine_Core::getTable('NodeType')->find($node_type_id);

        $file_uploaded = $this->input->file('icon');

        //Obtenemos la conexi�n actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

        //Iniciamos la transacci�n
        $conn->beginTransaction();

        try
        {
            $nodeType->node_type_category_id = $this->input->post('node_type_category_id');
            $nodeType->node_type_name = $this->input->post('node_type_name');            
            $nodeType->node_type_location  = $this->input->post('node_type_location');
           
            $nodeType->save();

            if (!empty($file_uploaded['name']))
            {
                $file_extension = $this->app->getFileExtension($file_uploaded['name']);
                list($width, $height, $type, $attr) = @getimagesize($file_uploaded['tmp_name']);

                if ($file_extension != 'gif' || $width != 16 || $height != 16)
                {
                    $msg = $this->translateTag('General', 'icone_not_qualify');
                    throw new Exception($msg);
                }
                //Creamos el nombre para el nuevo documento
                $config['upload_path'] = $this->config->item('node_icon_dir');
                $config['allowed_types'] = $file_extension;
                $config['file_name'] = $node_type_id . '.gif';
                $config['overwrite'] = true;

                //Restringuir tama�o y peso?
                //Carga de la libreria para el upload
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('icon'))
                {

                    $msg = $this->upload->display_errors('-', '\n');
                    throw new Exception($msg);
                }
            }

            // Si todo OK, commit a la base de datos
            $conn->commit();
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina un tipo de nodo en caso de no tener Nodo asociado
     *
     * @post int &node_type_id
     */
    function delete()
    {
        $node_type_id = $this->input->post('node_type_id');
        $checkNodeInNodeType = Doctrine::getTable('NodeType')->checkNodeInNodeType($node_type_id);

        if ($checkNodeInNodeType === false)
        {
            $nodeType = Doctrine::getTable('NodeType')->find($node_type_id);
            if ($nodeType->delete())
            {
                $exito = true;
                $msg = $this->translateTag('General', 'operation_successful');
            } else
            {
                $exito = false;
                $msg = 'Error: ';
            }
        } else
        {
            $exito = false;
            $msg = $this->translateTag('Infrastructure', 'can_not_delete_node');
        }
        $json_data = $this->json->encode(array('success' => $exito, 'msg' => $msg));
        echo $json_data;
    }

}