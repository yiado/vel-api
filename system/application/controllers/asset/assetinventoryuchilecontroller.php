<?php

/**
 * @package    Controller
 * @subpackage AssetTypeController
 */
class AssetInventoryUChileController extends APP_Controller {

    function AssetInventoryController() {

        parent::APP_Controller();
    }

    function getAllTrasladados() {
        $assetInventory = Doctrine_Core::getTable('AssetInventory');
        $node_id = $this->input->post('node_id');
        $assetCont = $assetInventory->findAllTrasladadosTotales($node_id, $this->auth->get_user_data('user_id'));
        $asset = $assetInventory->findAllTrasladados($node_id, $this->auth->get_user_data('user_id'), $this->input->post('start'), $this->input->post('limit'));

        if ($asset->count()) {
            if ($assetCont->count() < 100) {
                echo '({"total":"' . $assetCont->count() . '", "results":' . $this->json->encode($assetCont->toArray()) . '})';
            } else {
                echo '({"total":"' . $assetInventory->findAllTrasladados($node_id, $this->auth->get_user_data('user_id'), null, null, true) . '", "results":' . $this->json->encode($asset->toArray()) . '})';
            }
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function truncateTable() {
        try {
            $user_id = $this->auth->get_user_data('user_id');
            $statement = Doctrine_Manager::getInstance()->connection();

            $query4 = $statement->execute("DELETE FROM asset_inventory_auxiliar where user_id = " . $user_id);
            $query4 = $statement->execute("DELETE FROM asset_inventory where user_id = " . $user_id);
            $query4 = $statement->execute("DELETE FROM asset_inventory_auxiliar_proceso where user_id = " . $user_id);

            $success = true;
            $msg = $this->translateTag('Asset', 'cleaned_tables_success');
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function upload() {
        $file = $_FILES['inventory_file']['tmp_name'];

        switch ($this->input->post('output_type')) {
            case 'r':
                $this->uploadDiff($file);
                break;
            case 'm':
                $this->uploadMove($file);
                break;
            case 'n':
                $this->uploadMissing($file);
                break;
            case 'o':
                $this->uploadTransferred($file);
                break;
            case 'p':
                $this->uploadUnRegistered($file);
                break;
            case 'q':
                $this->uploadUnchanged($file);
                break;
            case 'c':
                $this->uploadCargar($file);
                break;
        }
    }

    function uploadDiff($file) {
        $this->load->helper('file');
        $this->load->library('PHPExcel');

        $array_node = array();
        $array_asset_exist = array();

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('General', 'movements'));

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('General', 'brand'))
                ->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('D1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('E1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('F1', $this->translateTag('General', 'department'))
                ->setCellValue('G1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('H1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('I1', $this->translateTag('General', 'department'))
                ->setCellValue('J1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('K1', $this->translateTag('General', 'situation'));

        $rcount = 1;

        $lines = file($file); // gets file in array using new lines character
        foreach ($lines as $line) {
            $ids = explode(',', $line);

            $Codigo_recinto = $ids[0];
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));

            if ($nodeOtherData) {
                $ids[0] = @$nodeOtherData->node_id;
                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($ids[1]));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {
                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }

                if (!isset($asset->asset_id)) { // asset not registered
                    if ($asset) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }

                    $rcount++;
                    $sheet->setCellValueExplicit('A' . $rcount, null)
                            ->setCellValueExplicit('B' . $rcount, null)
                            ->setCellValueExplicit('C' . $rcount, $ids[1])
                            ->setCellValueExplicit('D' . $rcount, null);

                    $node = Doctrine_Core::getTable('Node')->find($ids[0]);

                    if (isset($node->node_id)) {
                        $sheet->setCellValueExplicit('E' . $rcount, $node->getPath());
                    } else {
                        $sheet->setCellValueExplicit('E' . $rcount, null);
                    }

                    if (isset($node->node_id)) {
                        $sheet->setCellValueExplicit('F' . $rcount, $valueDepartamento);
                    } else {
                        $sheet->setCellValueExplicit('F' . $rcount, null);
                    }

                    if (isset($node->node_id)) {
                        $sheet->setCellValueExplicit('G' . $rcount, $nombreRecinto);
                    } else {
                        $sheet->setCellValueExplicit('G' . $rcount, null);
                    }

                    $sheet->setCellValueExplicit('H' . $rcount, null);
                    $sheet->setCellValueExplicit('I' . $rcount, null);
                    $sheet->setCellValueExplicit('J' . $rcount, null);

                    $sheet->setCellValueExplicit('K' . $rcount, $this->translateTag('Asset', 'active_not_registered'));

                    $arregloIDNoIgeo[] = $ids[1];

                    $sheet->getStyle('A' . $rcount . ':K' . $rcount)->getFill()->applyFromArray(array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '77FFFF'
                        )
                    ));
                    continue;
                }

                if (!isset($nodeOtherData->node_id)) { // node not registered
                    if ($asset) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }


                    $rcount++;

                    if (isset($asset->asset_id)) {
                        $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name);
                    } else {
                        $sheet->setCellValueExplicit('A' . $rcount, null);
                    }

                    if (isset($asset->asset_id)) {
                        $sheet->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name);
                    } else {
                        $sheet->setCellValueExplicit('B' . $rcount, null);
                    }

                    if (isset($asset->asset_id)) {
                        $sheet->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern);
                    } else {
                        $sheet->setCellValueExplicit('C' . $rcount, null);
                    }

                    if (isset($asset->asset_id)) {
                        $sheet->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValueExplicit('D' . $rcount, null);
                    }

                    $sheet->setCellValueExplicit('E' . $rcount, null);
                    $sheet->setCellValueExplicit('F' . $rcount, null);
                    $sheet->setCellValueExplicit('G' . $rcount, null);
                    $sheet->setCellValueExplicit('H' . $rcount, null);
                    $sheet->setCellValueExplicit('I' . $rcount, null);
                    $sheet->setCellValueExplicit('J' . $rcount, null);

                    $sheet->setCellValueExplicit('K' . $rcount, $this->translateTag('Asset', 'node_not_registered'));

                    continue;
                }

                if (!array_key_exists($ids[0], $array_node)) {
                    $array_node[$ids[0]] = array();
                    $array_asset_exist[$ids[0]] = array();
                }

                if ($asset->node_id != $ids[0]) {
                    $assetInventory = new AssetInventory();
                    $assetInventory->node_id = $ids[0];
                    $assetInventory->asset_id = $asset->asset_id;
                    $assetInventory->user_id = $this->auth->get_user_data('user_id');
                    $assetInventory->save();

                    $array_node[$ids[0]][] = $assetInventory;
                } else if ($asset->node_id == $ids[0]) {
                    $array_asset_exist[$ids[0]][] = $asset->asset_id;

                    // update last inventory date field
                    $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                    $asset->save();
                }

                $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));


                if ($asset) {
                    $otherDataCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);

                    if ($otherDataCodigoRecinto) {

                        $valueCodigoRecinto = $otherDataCodigoRecinto->infra_other_data_value_value;
                    } else {
                        $valueCodigoRecinto = "";
                    }
                } else {
                    $valueCodigoRecinto = "";
                }



                if ($Codigo_recinto == $valueCodigoRecinto) {

                    if (isset($asset->asset_id) and isset($asset->node_id)) { // asset YES registered
                        if ($asset) {
                            $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                            if ($nodeOtherDataIdDepartamento) {
                                $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                                $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                            if ($nodeOtherDataNombreRecinto) {

                                $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreRecinto = "";
                            }
                        } else {
                            $valueNombreNombreRecinto = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                            if ($nodeOtherDataNombreSubRecinto) {

                                $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreSubRecinto = "";
                            }
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }

                        if ($asset) {
                            $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                        } else {
                            $nombreRecinto = "";
                        }

                        $rcount++;
                        $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name);
                        if (isset($asset->brand_id)) {
                            $sheet->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name);
                        } else {
                            $sheet->setCellValueExplicit('B' . $rcount, null);
                        }
                        if (isset($asset->asset_id)) {
                            $sheet->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern);
                        } else {
                            $sheet->setCellValueExplicit('C' . $rcount, null);
                        }

                        if (isset($asset->asset_id)) {
                            $sheet->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $sheet->setCellValueExplicit('D' . $rcount, null);
                        }
                        $sheet->setCellValueExplicit('E' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath());
                        $sheet->setCellValueExplicit('F' . $rcount, $valueDepartamento);
                        $sheet->setCellValueExplicit('G' . $rcount, $nombreRecinto);
                        $sheet->setCellValueExplicit('H' . $rcount, null);
                        $sheet->setCellValueExplicit('I' . $rcount, null);
                        $sheet->setCellValueExplicit('J' . $rcount, null);

                        $sheet->setCellValueExplicit('K' . $rcount, $this->translateTag('Asset', 'conformitie'));
                        $arregloIDNoIgeo[] = $ids[1];

                        $sheet->getStyle('A' . $rcount . ':K' . $rcount)->getFill()->applyFromArray(array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => '77FF77'
                            )
                        ));

                        continue;
                    }
                }
            }
        }


        foreach ($array_node as $node_id => $node) {
            foreach ($node as $inventory) {


                if ($inventory->node_id) {
                    $nodeOtherDataIdDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 8);

                    if ($nodeOtherDataIdDepartamentoCambio) {
                        $valueNombreDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamentoCambio->infra_other_data_option_id);

                        $valueDepartamentoCambio = $valueNombreDepartamentoCambio->infra_other_data_option_name;
                    } else {
                        $valueDepartamentoCambio = "";
                    }
                } else {
                    $valueDepartamentoCambio = "";
                }


                if ($inventory->node_id) {
                    $nodeOtherDataNombreRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 2);

                    if ($nodeOtherDataNombreRecintoCambio) {

                        $valueNombreNombreRecintoCambio = $nodeOtherDataNombreRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nodeOtherDataNombreSubRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 4);

                    if ($nodeOtherDataNombreSubRecintoCambio) {

                        $valueNombreNombreSubRecintoCambio = $nodeOtherDataNombreSubRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreSubRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nombreRecintoCambio = $valueNombreNombreRecintoCambio . "/" . $valueNombreNombreSubRecintoCambio;
                } else {
                    $nombreRecintoCambio = "";
                }

                $asset_move_node = Doctrine_Core::getTable('Node')->find($inventory->node_id)->getPath();
                $asset = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);

                if ($assetOtherDatas) {

                    $value = $assetOtherDatas->asset_other_data_value_value;
                } else {
                    $value = "";
                }

                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {

                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }

                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name)
                        ->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name)
                        ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('E' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath())
                        ->setCellValueExplicit('F' . $rcount, $valueDepartamento)
                        ->setCellValueExplicit('G' . $rcount, $nombreRecinto)
                        ->setCellValue('H' . $rcount, $asset_move_node)
                        ->setCellValue('I' . $rcount, $valueDepartamentoCambio)
                        ->setCellValue('J' . $rcount, $nombreRecintoCambio)
                        ->setCellValue('K' . $rcount, $this->translateTag('Asset', 'transferred'));

                $sheet->getStyle('A' . $rcount . ':K' . $rcount)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'FFFF77'
                    )
                ));
            }

            foreach (Doctrine_Core::getTable('Asset')->getAssetDiff($node_id, $array_asset_exist[$node_id]) as $asset) {
                if ($assetOtherDatas) {

                    $value = $assetOtherDatas->asset_other_data_value_value;
                } else {
                    $value = "";
                }

                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {
                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }


                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name)
                        ->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name)
                        ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('E' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath())
                        ->setCellValueExplicit('F' . $rcount, $valueDepartamento)
                        ->setCellValueExplicit('G' . $rcount, $nombreRecinto)
                        ->setCellValueExplicit('H' . $rcount, null)
                        ->setCellValueExplicit('I' . $rcount, null)
                        ->setCellValueExplicit('J' . $rcount, null)
                        ->setCellValue('K' . $rcount, $this->translateTag('Asset', 'missing'));

                $sheet->getStyle('A' . $rcount . ':K' . $rcount)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'FF7777'
                    )
                ));
            }
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadMissing($file) {
        $this->load->helper('file');
        $this->load->library('PHPExcel');

        $array_node = array();
        $array_asset_exist = array();

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('General', 'movements'));

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('General', 'brand'))
                ->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('D1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('E1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('F1', $this->translateTag('General', 'department'))
                ->setCellValue('G1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('H1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('I1', $this->translateTag('General', 'department'))
                ->setCellValue('J1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('K1', $this->translateTag('General', 'situation'));

        $rcount = 1;

        $lines = file($file); // gets file in array using new lines character
        foreach ($lines as $line) {
            $ids = explode(',', $line);

            $Codigo_recinto = $ids[0];
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));
            $ids[0] = @$nodeOtherData->node_id;

            $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($ids[1]));

            if ($asset) {
                $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                if ($assetOtherDatas) {

                    $value = $assetOtherDatas->asset_other_data_value_value;
                } else {
                    $value = "";
                }
            } else {
                $value = "";
            }


            if (!isset($asset->asset_id)) { // asset not registered
                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {
                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }
                continue;
            }



            if (!isset($nodeOtherData->node_id)) { // node not registered
                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {
                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }
                continue;
            }

            if (!array_key_exists($ids[0], $array_node)) {
                $array_node[$ids[0]] = array();
                $array_asset_exist[$ids[0]] = array();
            }

            if ($asset->node_id != $ids[0]) {
                $assetInventory = new AssetInventory();
                $assetInventory->node_id = $ids[0];
                $assetInventory->asset_id = $asset->asset_id;
                $assetInventory->user_id = $this->auth->get_user_data('user_id');
                $assetInventory->save();

                $array_node[$ids[0]][] = $assetInventory;
            } else if ($asset->node_id == $ids[0]) {
                $array_asset_exist[$ids[0]][] = $asset->asset_id;

                // update last inventory date field
                $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                $asset->save();
            }

            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));


            if ($asset) {
                $otherDataCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);

                if ($otherDataCodigoRecinto) {

                    $valueCodigoRecinto = $otherDataCodigoRecinto->infra_other_data_value_value;
                } else {
                    $valueCodigoRecinto = "";
                }
            } else {
                $valueCodigoRecinto = "";
            }



            if ($Codigo_recinto == $valueCodigoRecinto) {

                if (isset($asset->asset_id) and isset($asset->node_id)) { // asset YES registered
                    if ($asset) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }
                    continue;
                }
            }
        }

        foreach ($array_node as $node_id => $node) {
            foreach ($node as $inventory) {
                $assetArrayTraslado = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);
                $assetArrayTrasl[] = $assetArrayTraslado->asset_id;
            }



            foreach (Doctrine_Core::getTable('Asset')->getAssetDiff($node_id, $array_asset_exist[$node_id]) as $asset) {
                $assetArrayTrasladoFaltante = Doctrine_Core::getTable('Asset')->find($asset->asset_id);
                $assetTableTraslado[] = $assetArrayTrasladoFaltante->asset_id;
            }
        }

        if (isset($assetArrayTraslado)) {
            $result = array_diff($assetTableTraslado, $assetArrayTrasl);
        } else {
            if (isset($assetTableTraslado)) {
                $result = $assetTableTraslado;
            } else {
                $result = 0;
            }
        }

        if ($result == 0) {

            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, null)
                    ->setCellValueExplicit('B' . $rcount, null)
                    ->setCellValueExplicit('C' . $rcount, null)
                    ->setCellValueExplicit('D' . $rcount, null)
                    ->setCellValueExplicit('E' . $rcount, null)
                    ->setCellValueExplicit('F' . $rcount, null)
                    ->setCellValueExplicit('G' . $rcount, null)
                    ->setCellValueExplicit('H' . $rcount, null)
                    ->setCellValueExplicit('I' . $rcount, null)
                    ->setCellValueExplicit('J' . $rcount, null)
                    ->setCellValue('K' . $rcount, $this->translateTag('Asset', 'no_missing'));
        } else {
            foreach ($result as $res) {
                if ($res) {
                    $asset = Doctrine_Core::getTable('Asset')->find($res);

                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }

                    if ($asset) {
                        ;
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }


                    $rcount++;
                    $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name)
                            ->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name)
                            ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('E' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath())
                            ->setCellValueExplicit('F' . $rcount, $valueDepartamento)
                            ->setCellValueExplicit('G' . $rcount, $nombreRecinto)
                            ->setCellValueExplicit('H' . $rcount, null)
                            ->setCellValueExplicit('I' . $rcount, null)
                            ->setCellValueExplicit('J' . $rcount, null)
                            ->setCellValue('K' . $rcount, $this->translateTag('Asset', 'missing'));
                }
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_Faltantes_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadTransferred($file) {
        $this->load->helper('file');
        $this->load->library('PHPExcel');

        $array_node = array();
        $array_asset_exist = array();

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('General', 'movements'));

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('General', 'brand'))
                ->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('D1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('E1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('F1', $this->translateTag('General', 'department'))
                ->setCellValue('G1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('H1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('I1', $this->translateTag('General', 'department'))
                ->setCellValue('J1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('K1', $this->translateTag('General', 'situation'));

        $rcount = 1;

        $lines = file($file); // gets file in array using new lines character
        foreach ($lines as $line) {
            $ids = explode(',', $line);

            $Codigo_recinto = $ids[0];
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));

            if ($nodeOtherData) {
                $ids[0] = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($ids[1]));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }

                if ($asset) {

                    if (!array_key_exists($ids[0], $array_node)) {
                        $array_node[$ids[0]] = array();
                        $array_asset_exist[$ids[0]] = array();
                    }

                    if ($asset->node_id != $ids[0]) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $ids[0];
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$ids[0]][] = $assetInventory;
                    } else if ($asset->node_id == $ids[0]) {
                        $array_asset_exist[$ids[0]][] = $asset->asset_id;

                        // update last inventory date field
                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }
            }
        }

        foreach ($array_node as $node_id => $node) {
            foreach ($node as $inventory) {


                if ($inventory->node_id) {
                    $nodeOtherDataIdDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 8);

                    if ($nodeOtherDataIdDepartamentoCambio) {
                        $valueNombreDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamentoCambio->infra_other_data_option_id);

                        $valueDepartamentoCambio = $valueNombreDepartamentoCambio->infra_other_data_option_name;
                    } else {
                        $valueDepartamentoCambio = "";
                    }
                } else {
                    $valueDepartamentoCambio = "";
                }


                if ($inventory->node_id) {
                    $nodeOtherDataNombreRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 2);

                    if ($nodeOtherDataNombreRecintoCambio) {

                        $valueNombreNombreRecintoCambio = $nodeOtherDataNombreRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nodeOtherDataNombreSubRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 4);

                    if ($nodeOtherDataNombreSubRecintoCambio) {

                        $valueNombreNombreSubRecintoCambio = $nodeOtherDataNombreSubRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreSubRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nombreRecintoCambio = $valueNombreNombreRecintoCambio . "/" . $valueNombreNombreSubRecintoCambio;
                } else {
                    $nombreRecintoCambio = "";
                }

                $asset_move_node = Doctrine_Core::getTable('Node')->find($inventory->node_id)->getPath();
                $asset = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);

                if ($assetOtherDatas) {

                    $value = $assetOtherDatas->asset_other_data_value_value;
                } else {
                    $value = "";
                }


                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {

                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }

                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name)
                        ->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name)
                        ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('E' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath())
                        ->setCellValueExplicit('F' . $rcount, $valueDepartamento)
                        ->setCellValueExplicit('G' . $rcount, $nombreRecinto)
                        ->setCellValue('H' . $rcount, $asset_move_node)
                        ->setCellValue('I' . $rcount, $valueDepartamentoCambio)
                        ->setCellValue('J' . $rcount, $nombreRecintoCambio)
                        ->setCellValue('K' . $rcount, $this->translateTag('Asset', 'transferred'));
            }
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_Trasladados_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadUnRegistered($file) {
        $this->load->helper('file');
        $this->load->library('PHPExcel');

        $array_node = array();
        $array_asset_exist = array();

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('General', 'movements'));

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('General', 'brand'))
                ->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('D1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('E1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('F1', $this->translateTag('General', 'department'))
                ->setCellValue('G1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('H1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('I1', $this->translateTag('General', 'department'))
                ->setCellValue('J1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('K1', $this->translateTag('General', 'situation'));

        $rcount = 1;

        $lines = file($file); // gets file in array using new lines character
        foreach ($lines as $line) {
            $ids = explode(',', $line);

            $Codigo_recinto = $ids[0];
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));

            if ($nodeOtherData) {
                $ids[0] = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($ids[1]));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }


                if (!isset($asset->asset_id)) { // asset not registered
                    $node = Doctrine_Core::getTable('Node')->find($ids[0]);

                    if ($node) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($node) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($node) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($node) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }

                    $rcount++;
                    $sheet->setCellValueExplicit('A' . $rcount, null)
                            ->setCellValueExplicit('B' . $rcount, null)
                            ->setCellValueExplicit('C' . $rcount, $ids[1])
                            ->setCellValueExplicit('D' . $rcount, null);

                    if (isset($node->node_id)) {
                        $sheet->setCellValueExplicit('E' . $rcount, $node->getPath());
                    } else {
                        $sheet->setCellValueExplicit('E' . $rcount, null);
                    }

                    if (isset($node->node_id)) {
                        $sheet->setCellValueExplicit('F' . $rcount, $valueDepartamento);
                    } else {
                        $sheet->setCellValueExplicit('F' . $rcount, null);
                    }

                    if (isset($node->node_id)) {
                        $sheet->setCellValueExplicit('G' . $rcount, $nombreRecinto);
                    } else {
                        $sheet->setCellValueExplicit('G' . $rcount, null);
                    }

                    $sheet->setCellValueExplicit('H' . $rcount, null);
                    $sheet->setCellValueExplicit('I' . $rcount, null);
                    $sheet->setCellValueExplicit('J' . $rcount, null);

                    $sheet->setCellValueExplicit('K' . $rcount, $this->translateTag('Asset', 'active_not_registered'));

                    $arregloIDNoIgeo[] = $ids[1];

                    continue;
                }


                if ($asset) {

                    if (!array_key_exists($ids[0], $array_node)) {
                        $array_node[$ids[0]] = array();
                        $array_asset_exist[$ids[0]] = array();
                    }

                    if ($asset->node_id != $ids[0]) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $ids[0];
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$ids[0]][] = $assetInventory;
                    } else if ($asset->node_id == $ids[0]) {
                        $array_asset_exist[$ids[0]][] = $asset->asset_id;

                        // update last inventory date field
                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_No_Registrados_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadUnchanged($file) {
        $this->load->helper('file');
        $this->load->library('PHPExcel');

        $array_node = array();
        $array_asset_exist = array();

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('General', 'movements'));

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('General', 'brand'))
                ->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('D1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('E1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('F1', $this->translateTag('General', 'department'))
                ->setCellValue('G1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('H1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('I1', $this->translateTag('General', 'department'))
                ->setCellValue('J1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('K1', $this->translateTag('General', 'situation'));

        $rcount = 1;

        $lines = file($file); // gets file in array using new lines character
        foreach ($lines as $line) {
            $ids = explode(',', $line);

            $Codigo_recinto = $ids[0];
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));

            if ($nodeOtherData) {
                $ids[0] = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($ids[1]));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }
                if ($asset) {

                    if (!array_key_exists($ids[0], $array_node)) {
                        $array_node[$ids[0]] = array();
                        $array_asset_exist[$ids[0]] = array();
                    }

                    if ($asset->node_id != $ids[0]) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $ids[0];
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$ids[0]][] = $assetInventory;
                    } else if ($asset->node_id == $ids[0]) {
                        $array_asset_exist[$ids[0]][] = $asset->asset_id;

                        // update last inventory date field
                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }


                $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));


                if ($asset) {
                    $otherDataCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);

                    if ($otherDataCodigoRecinto) {

                        $valueCodigoRecinto = $otherDataCodigoRecinto->infra_other_data_value_value;
                    } else {
                        $valueCodigoRecinto = "";
                    }
                } else {
                    $valueCodigoRecinto = "";
                }



                if ($Codigo_recinto == $valueCodigoRecinto) {

                    if (isset($asset->asset_id) and isset($asset->node_id)) { // asset YES registered
                        if ($asset) {
                            $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                            if ($nodeOtherDataIdDepartamento) {
                                $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                                $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                            if ($nodeOtherDataNombreRecinto) {

                                $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreRecinto = "";
                            }
                        } else {
                            $valueNombreNombreRecinto = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                            if ($nodeOtherDataNombreSubRecinto) {

                                $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreSubRecinto = "";
                            }
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }

                        if ($asset) {
                            $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                        } else {
                            $nombreRecinto = "";
                        }

                        $rcount++;
                        $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name);
                        if (isset($asset->brand_id)) {
                            $sheet->setCellValueExplicit('B' . $rcount, $asset->Brand->brand_name);
                        } else {
                            $sheet->setCellValueExplicit('B' . $rcount, null);
                        }
                        if (isset($asset->asset_id)) {
                            $sheet->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern);
                        } else {
                            $sheet->setCellValueExplicit('C' . $rcount, null);
                        }

                        if (isset($asset->asset_id)) {
                            $sheet->setCellValueExplicit('D' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $sheet->setCellValueExplicit('D' . $rcount, null);
                        }
                        $sheet->setCellValueExplicit('E' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath());
                        $sheet->setCellValueExplicit('F' . $rcount, $valueDepartamento);
                        $sheet->setCellValueExplicit('G' . $rcount, $nombreRecinto);
                        $sheet->setCellValueExplicit('H' . $rcount, null);
                        $sheet->setCellValueExplicit('I' . $rcount, null);
                        $sheet->setCellValueExplicit('J' . $rcount, null);

                        $sheet->setCellValueExplicit('K' . $rcount, $this->translateTag('Asset', 'conformitie'));
                        $arregloIDNoIgeo[] = $ids[1];

                        continue;
                    }
                }
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_Sin_Cambios_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadMove($file) {
        $this->load->helper('file');
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('General', 'movements'));

        $sheet->setCellValue('A1', $this->translateTag('General', 'movements'))
                ->setCellValue('B1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('C1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('D1', $this->translateTag('Asset', 'location_transfer'));

        $rcount = 1;
        $lines = file($file); // gets file in array using new lines character
        foreach ($lines as $line) {
            $ids = explode(',', $line);

            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($ids[0]));
            $ids[0] = @$nodeOtherData->node_id;

            $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($ids[1]));


            if (!isset($asset->asset_id)) { // asset not registered
                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $ids[1])
                        ->setCellValueExplicit('B' . $rcount, null);

                $node = Doctrine_Core::getTable('Node')->find($ids[0]);

                if (isset($node->node_id)) {
                    $sheet->setCellValueExplicit('C' . $rcount, $node->getPath());
                } else {
                    $sheet->setCellValueExplicit('C' . $rcount, null);
                }

                $sheet->setCellValueExplicit('D' . $rcount, $this->translateTag('Asset', 'active_not_registered'));

                continue;
            }

            if (!isset($nodeOtherData->node_id)) { // node not registered
                $rcount++;

                $sheet->setCellValueExplicit('A' . $rcount, $ids[1]);
                if (isset($asset->asset_id)) {
                    $sheet->setCellValueExplicit('B' . $rcount, $asset->asset_num_serie_intern);
                } else {
                    $sheet->setCellValueExplicit('B' . $rcount, null);
                }

                $sheet->setCellValueExplicit('C' . $rcount, null);

                $sheet->setCellValueExplicit('D' . $rcount, $this->translateTag('Asset', 'node_not_registered'));

                continue;
            }


            if ($asset->node_id != $ids[0]) {
                $asset_move_node = Doctrine_Core::getTable('Node')->find($ids[0])->getPath();

                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name)
                        ->setCellValueExplicit('B' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('C' . $rcount, Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath())
                        ->setCellValue('D' . $rcount, $asset_move_node);


                $asset->node_id = $ids[0];
                $asset->save();

                Doctrine_Core::getTable('AssetLog')->logMoveAsset($asset->asset_id, $this->session->userdata('user_id'), 'asset_log_move', $asset_move_node);
            } else if ($asset->node_id == $ids[0]) {

                // update last inventory date field
                $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                $asset->save();
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $sheet->getStyle('A1:D1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:D' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));

        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function move() {
        $asset_inventory_auxiliar_proceso_id = $this->input->post('asset_inventory_auxiliar_proceso_id');

        foreach (explode(',', $asset_inventory_auxiliar_proceso_id) as $asset_inventoy) {
            $assetInventoryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->find($asset_inventoy);
            $asset = Doctrine_Core::getTable('Asset')->findNumInt($assetInventoryAuxiliarProceso->asset_num_serie_intern);

            $node_ruta = $asset->node_id;


            $assetInventory = Doctrine_Core::getTable('AssetInventory')->findOneBy('asset_id', $asset->asset_id);
            $asset->node_id = $assetInventory->node_id;

            $asset->save();

            $asset_log_detail = Doctrine_Core::getTable('Node')->find($node_ruta)->getPath();
            Doctrine_Core::getTable('AssetLog')->logMoveAsset($asset->asset_id, $this->session->userdata('user_id'), 'asset_log_move', $asset_log_detail);

            $assetInventoryAuxiliarProceso->delete();
        }
        $json_data = $this->json->encode(array('success' => true));
        echo $json_data;
    }

    function returnOrigen() {
        $asset_inventory_auxiliar_proceso_id = $this->input->post('asset_inventory_auxiliar_proceso_id');

        foreach (explode(',', $asset_inventory_auxiliar_proceso_id) as $asset_inventoy) {
            $assetInventoryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->find($asset_inventoy);
            $assetInventoryAuxiliarProceso->delete();
        }
        $json_data = $this->json->encode(array('success' => true));
        echo $json_data;
    }

    function uploadCargar() {

        try {
            $file = $_FILES['inventory_file']['tmp_name'];
            $lines = file($file); // gets file in array using new lines character
            $auxiliarActivos = array();
            foreach ($lines as $line) {

                $ids = explode(',', $line);
                $assetCodigoActivo = trim($ids[1]);
                $assetCodigoRecinto = trim($ids[0]);
//VALIDA CONTRA EL MISMO ARCHIVO COLLET QUE NO ESTEN DUPLICADOS
                if (in_array($assetCodigoActivo, $auxiliarActivos)) {
                    $success = false;
                    $msg = 'El Codigo de Recinto: ' . $assetCodigoRecinto . ' Y codigo de Activo: ' . $assetCodigoActivo . ' Estan Duplicados. en el Collet';
                    $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                    echo $json_data;
                    exit;
                } else {
                    $auxiliarActivos[] = $assetCodigoActivo;
                }
//VALIDACION DE DUPLICACION CONTRA LA BASE DE DATOS
                $resp = Doctrine_Core::getTable('AssetInventoryAuxiliar')->retrieveDuplicaco($assetCodigoRecinto, $assetCodigoActivo);

                if ($resp === true) {
                    $success = false;
                    $msg = 'El Codigo de Recinto: ' . $assetCodigoRecinto . ' Y codigo de Activo: ' . $assetCodigoActivo . ' Estan Duplicados.';
                    $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                    echo $json_data;
                    exit;
                }
            }



            foreach ($lines as $line) {

//                $ids = explode(',', $line);
                 list($codigo_barra, $codigo_interno) = explode(',', $line);
                $assetInventoryCarga = new AssetInventoryAuxiliar();
                $assetInventoryCarga->asset_inventory_barra = trim($codigo_barra);
//                echo '<br>';
                $assetInventoryCarga->asset_inventory_interno = trim($codigo_interno);
//                echo '<br>';
//                exit;
                $assetInventoryCarga->user_id = $this->auth->get_user_data('user_id');
                $assetInventoryCarga->save();
            }
            $this->uploadProcesar();

            $msg = $this->translateTag('Asset', 'the_inventory_has_been_loaded_and_processing');
            $success = true;
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => true));
        echo $json_data;
    }

    function uploadProcesar() {

        $array_node = array();
        $array_asset_exist = array();

        $assetInventoryCargaGet = Doctrine_Core::getTable('AssetInventoryAuxiliar')->retrieveAll($this->auth->get_user_data('user_id'));

        foreach ($assetInventoryCargaGet as $assetInventoryCarga) {


            $Codigo_recinto = $assetInventoryCarga->asset_inventory_barra;
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));

            if ($nodeOtherData) {

                if ($nodeOtherData->node_id) {
                    $node_id = $nodeOtherData->node_id;
                } else {
                    $node_id = null;
                }

                $user_id = $this->auth->get_user_data('user_id');

                $assetInventoryCarga->asset_inventory_barra = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($assetInventoryCarga->asset_inventory_interno));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }

                if (!isset($asset->asset_id)) { // asset not registered
                    $node = Doctrine_Core::getTable('Node')->find($assetInventoryCarga->asset_inventory_barra);

                    if (isset($node->node_id)) {
                        $nodeOtherDataIdDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 8);

                        if ($nodeOtherDataIdDepartamentoCambio) {
                            $valueNombreDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamentoCambio->infra_other_data_option_id);

                            if ($valueNombreDepartamentoCambio) {

                                $valueDepartamentoCambio = $valueNombreDepartamentoCambio->infra_other_data_option_name;
                            } else {
                                $valueDepartamentoCambio = "";
                            }
                        } else {
                            $valueDepartamentoCambio = "";
                        }
                    } else {
                        $valueDepartamentoCambio = "";
                    }

                    if (isset($node->node_id)) {
                        $nodeOtherDataNombreRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 2);

                        if ($nodeOtherDataNombreRecintoCambio) {

                            $valueNombreNombreRecintoCambio = $nodeOtherDataNombreRecintoCambio->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecintoCambio = "";
                        }
                    } else {
                        $valueNombreNombreRecintoCambio = "";
                    }

                    if (isset($node->node_id)) {
                        $nodeOtherDataNombreSubRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 4);

                        if ($nodeOtherDataNombreSubRecintoCambio) {

                            $valueNombreNombreSubRecintoCambio = $nodeOtherDataNombreSubRecintoCambio->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecintoCambio = "";
                        }
                    } else {
                        $valueNombreNombreSubRecintoCambio = "";
                    }

                    if (isset($node->node_id)) {
                        $nombreRecintoCambio = $valueNombreNombreRecintoCambio . "/" . $valueNombreNombreSubRecintoCambio;
                    } else {
                        $nombreRecintoCambio = "";
                    }


                    $numero_interni = $assetInventoryCarga->asset_inventory_interno;

                    $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                    $assetInventoryAuxiliarProceso->node_id = $node_id;
                    $assetInventoryAuxiliarProceso->user_id = $user_id;
                    $assetInventoryAuxiliarProceso->asset_num_serie = " ";
                    $assetInventoryAuxiliarProceso->asset_description = " ";
                    $assetInventoryAuxiliarProceso->asset_name = " ";
                    $assetInventoryAuxiliarProceso->brand_name = " ";
                    $assetInventoryAuxiliarProceso->asset_num_serie_intern = $numero_interni;
                    $assetInventoryAuxiliarProceso->codigo_auge = " ";

                    if (isset($node->node_id)) {
                        $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($assetInventoryCarga->asset_inventory_barra)->getPath();
                    } else {

                        $assetInventoryAuxiliarProceso->original_location = $this->translateTag('Asset', 'active_not_logged_and_the_location_is_not_recorded');
                    }

                    $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamentoCambio;
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecintoCambio;


                    $assetInventoryAuxiliarProceso->location_transfer = " ";
                    $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                    $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'active_not_registered');
                    $assetInventoryAuxiliarProceso->save();
                }

                if (!isset($nodeOtherData->node_id)) { // node not registered
                    if ($asset) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }


                    $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                    $assetInventoryAuxiliarProceso->node_id = $node_id;
                    $assetInventoryAuxiliarProceso->user_id = $user_id;
                    if (isset($asset->asset_id)) {
                        $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;
                    } else {
                        $assetInventoryAuxiliarProceso->asset_name = " ";
                    }

                    if (isset($asset->asset_id)) {
                        $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                    } else {
                        $assetInventoryAuxiliarProceso->brand_name = " ";
                    }


                    if (isset($asset->asset_id)) {
                        $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                    } else {
                        $assetInventoryAuxiliarProceso->asset_num_serie_intern = " ";
                    }

                    if (isset($asset->asset_id)) {
                        $assetInventoryAuxiliarProceso->codigo_auge = $value;
                    } else {
                        $assetInventoryAuxiliarProceso->codigo_auge = " ";
                    }

                    $assetInventoryAuxiliarProceso->original_location = " ";
                    $assetInventoryAuxiliarProceso->departamento_original = " ";
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_original = " ";
                    $assetInventoryAuxiliarProceso->location_transfer = " ";
                    $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                    $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'node_not_registered');
                    $assetInventoryAuxiliarProceso->save();
                }

                if (!array_key_exists($assetInventoryCarga->asset_inventory_barra, $array_node)) {
                    $array_node[$assetInventoryCarga->asset_inventory_barra] = array();
                    $array_asset_exist[$assetInventoryCarga->asset_inventory_barra] = array();
                }

                if ($asset) {
                    if ($asset->node_id != $assetInventoryCarga->asset_inventory_barra) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $assetInventoryCarga->asset_inventory_barra;
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$assetInventoryCarga->asset_inventory_barra][] = $assetInventory;
                    } else if ($asset->node_id == $assetInventoryCarga->asset_inventory_barra) {
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra][] = $asset->asset_id;

                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }



                $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));


                if ($asset) {
                    $otherDataCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);

                    if ($otherDataCodigoRecinto) {

                        $valueCodigoRecinto = $otherDataCodigoRecinto->infra_other_data_value_value;
                    } else {
                        $valueCodigoRecinto = "";
                    }
                } else {
                    $valueCodigoRecinto = "";
                }

                if ($Codigo_recinto == $valueCodigoRecinto) {

                    if (isset($asset->asset_id) and isset($asset->node_id)) { // asset YES registered
                        if ($asset) {
                            $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                            if ($nodeOtherDataIdDepartamento) {
                                $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                                if ($valueNombreDepartamento) {
                                    $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                                } else {
                                    $valueDepartamento = "";
                                }
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                            if ($nodeOtherDataNombreRecinto) {

                                $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreRecinto = "";
                            }
                        } else {
                            $valueNombreNombreRecinto = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                            if ($nodeOtherDataNombreSubRecinto) {

                                $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreSubRecinto = "";
                            }
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }

                        if ($asset) {
                            $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                        } else {
                            $nombreRecinto = "";
                        }

                        $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                        $assetInventoryAuxiliarProceso->node_id = $node_id;
                        $assetInventoryAuxiliarProceso->user_id = $user_id;
                        $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;
                        if (isset($asset->brand_id)) {
                            $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                        } else {
                            $assetInventoryAuxiliarProceso->brand_name = " ";
                        }

                        if (isset($asset->asset_id)) {
                            $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                        } else {
                            $assetInventoryAuxiliarProceso->asset_num_serie_intern = " ";
                        }

                        if (isset($asset->asset_id)) {
                            $assetInventoryAuxiliarProceso->codigo_auge = $value;
                        } else {
                            $assetInventoryAuxiliarProceso->codigo_auge = " ";
                        }
//CAMPOS NUEVOS 2
                        $assetInventoryAuxiliarProceso->asset_num_serie = $asset->asset_num_serie;
                        $assetInventoryAuxiliarProceso->asset_description = $asset->asset_description;

                        $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                        $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                        $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                        $assetInventoryAuxiliarProceso->location_transfer = " ";
                        $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                        $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                        $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'conformitie');
                        $assetInventoryAuxiliarProceso->save();
                    }
                }
            }
        }

        foreach ($array_node as $node_id => $node) {
            foreach ($node as $inventory) {
                $assetArrayTraslado = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);
                $assetArrayTrasl[] = $assetArrayTraslado->asset_id;


                if ($inventory->node_id) {
                    $nodeOtherDataIdDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 8);

                    if ($nodeOtherDataIdDepartamentoCambio) {
                        $valueNombreDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamentoCambio->infra_other_data_option_id);

                        if ($valueNombreDepartamentoCambio) {

                            $valueDepartamentoCambio = $valueNombreDepartamentoCambio->infra_other_data_option_name;
                        } else {
                            $valueDepartamentoCambio = "";
                        }
                    } else {
                        $valueDepartamentoCambio = "";
                    }
                } else {
                    $valueDepartamentoCambio = "";
                }


                if ($inventory->node_id) {
                    $nodeOtherDataNombreRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 2);

                    if ($nodeOtherDataNombreRecintoCambio) {

                        $valueNombreNombreRecintoCambio = $nodeOtherDataNombreRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nodeOtherDataNombreSubRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 4);

                    if ($nodeOtherDataNombreSubRecintoCambio) {

                        $valueNombreNombreSubRecintoCambio = $nodeOtherDataNombreSubRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreSubRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nombreRecintoCambio = $valueNombreNombreRecintoCambio . "/" . $valueNombreNombreSubRecintoCambio;
                } else {
                    $nombreRecintoCambio = "";
                }

                $asset_move_node = Doctrine_Core::getTable('Node')->find($inventory->node_id)->getPath();
                $asset = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);


                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }


                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        if ($valueNombreDepartamento) {

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {

                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }

                $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                $assetInventoryAuxiliarProceso->node_id = $asset->node_id;
                $assetInventoryAuxiliarProceso->user_id = $this->auth->get_user_data('user_id');
                $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;
                $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                $assetInventoryAuxiliarProceso->codigo_auge = $value;
                               
                $assetInventoryAuxiliarProceso->asset_num_serie = $asset->asset_num_serie;
                $assetInventoryAuxiliarProceso->asset_description = $asset->asset_description;
                        
                $assetEsta = Doctrine_Core::getTable('Node')->find($asset->node_id);

                if ($assetEsta) {
                    $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                } else {
                    $assetInventoryAuxiliarProceso->original_location = $this->translateTag('Asset', 'join_this_active_and_the_location_is_not_registered');
                }
                $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                $assetInventoryAuxiliarProceso->location_transfer = $asset_move_node;
                $assetInventoryAuxiliarProceso->departamento_transfer = $valueDepartamentoCambio;
                $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = $nombreRecintoCambio;
                $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'transferred');
                $assetInventoryAuxiliarProceso->save();
            }

            foreach (Doctrine_Core::getTable('Asset')->getAssetDiff($node_id, $array_asset_exist[$node_id]) as $asset) {
                $assetArrayTrasladoFaltante = Doctrine_Core::getTable('Asset')->find($asset->asset_id);
                $assetTableTraslado[] = $assetArrayTrasladoFaltante->asset_id;
            }
        }

        if (isset($assetArrayTraslado)) {
            $result = array_diff($assetTableTraslado, $assetArrayTrasl);
        } else {
            if (isset($assetTableTraslado)) {
                $result = $assetTableTraslado;
            } else {
                $result = 0;
            }
        }

        if ($result == 0) {
            
        } else {
            foreach ($result as $res) {
                if ($res) {
                    $asset = Doctrine_Core::getTable('Asset')->find($res);


                    if ($asset) {
                        $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                        if ($assetOtherDatas) {

                            $value = $assetOtherDatas->asset_other_data_value_value;
                        } else {
                            $value = "";
                        }
                    } else {
                        $value = "";
                    }

                    if ($asset) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            if ($valueNombreDepartamento) {

                                $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }

                    $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                    $assetInventoryAuxiliarProceso->node_id = $asset->node_id;
                    $assetInventoryAuxiliarProceso->user_id = $this->auth->get_user_data('user_id');
                    $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;
                    $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                    $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                    $assetInventoryAuxiliarProceso->codigo_auge = $value;
                    $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                    $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                    $assetInventoryAuxiliarProceso->asset_num_serie = $asset->asset_num_serie;
                    $assetInventoryAuxiliarProceso->asset_description = $asset->asset_description;
                    $assetInventoryAuxiliarProceso->location_transfer = " ";
                    $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                    $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'missing');
                    $assetInventoryAuxiliarProceso->save();
                }
            }
        }

//        $msg = "El Inventario ha sido procesado";
//        $success = true;
//
//
//        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
//        echo $json_data;
    }

    function uploadCargaMasiva() {

        switch ($this->input->post('output_type')) {
            case 'canr':
                $this->cargaActivoNoRegistrado();
                break;

            case 'cnnr':
                $this->cargaNodoNoRegistrado();
                break;

            case 'cc':
                $this->cargaConformidades();
                break;

            case 'ctt':
                $this->cargaTrasladado();
                break;

            case 'cf':
                $this->cargaFaltante();
                break;

            case 'ct':
                $this->cargaTodo();
                break;
        }
    }

    function cargaActivoNoRegistrado() {

        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Asset', 'active_not_registered'));

        $user_id = $this->auth->get_user_data('user_id');

        $assetInventaryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->cargaActivoNoRegistrado($user_id);

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('C1', $this->translateTag('General', 'brand'))
                ->setCellValue('D1', $this->translateTag('Asset', 'serial_number')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('E1', $this->translateTag('General', 'description')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('F1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('G1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('H1', $this->translateTag('General', 'department'))
                ->setCellValue('I1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('J1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('K1', $this->translateTag('General', 'department'))
                ->setCellValue('L1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('M1', $this->translateTag('General', 'situation'));
        $rcount = 1;
        foreach ($assetInventaryAuxiliarProceso as $assetInventaryAuxiliar) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $assetInventaryAuxiliar->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $assetInventaryAuxiliar->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $assetInventaryAuxiliar->brand_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $assetInventaryAuxiliar->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $assetInventaryAuxiliar->asset_description, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $assetInventaryAuxiliar->codigo_auge, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G' . $rcount, $assetInventaryAuxiliar->original_location, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H' . $rcount, $assetInventaryAuxiliar->departamento_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J' . $rcount, $assetInventaryAuxiliar->location_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K' . $rcount, $assetInventaryAuxiliar->departamento_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('M' . $rcount, $assetInventaryAuxiliar->situacion, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);

        $sheet->getStyle('A1:M1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:M1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:M' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_Activo_No_Registrado_En_Igeo' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function cargaNodoNoRegistrado() {

        $this->load->library('PHPExcel');
        $user_id = $this->auth->get_user_data('user_id');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Asset', 'node_not_registered'));


        $assetInventaryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->cargaNodoNoRegistrado($user_id);

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('General', 'brand'))
                ->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('D1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('E1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('F1', $this->translateTag('General', 'department'))
                ->setCellValue('G1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('H1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('I1', $this->translateTag('General', 'department'))
                ->setCellValue('J1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('K1', $this->translateTag('General', 'situation'));

        $rcount = 1;
        foreach ($assetInventaryAuxiliarProceso as $assetInventaryAuxiliar) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $assetInventaryAuxiliar->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $assetInventaryAuxiliar->brand_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $assetInventaryAuxiliar->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $assetInventaryAuxiliar->codigo_auge, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $assetInventaryAuxiliar->original_location, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $assetInventaryAuxiliar->departamento_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H' . $rcount, $assetInventaryAuxiliar->location_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I' . $rcount, $assetInventaryAuxiliar->departamento_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K' . $rcount, $assetInventaryAuxiliar->situacion, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);


        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_Nodo_No_Registrado_En_Igeo' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function cargaConformidades() {

        $this->load->library('PHPExcel');
        $user_id = $this->auth->get_user_data('user_id');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Asset', 'conformitie'));


        $assetInventaryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->cargaConformidades($user_id);

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('C1', $this->translateTag('General', 'brand'))
                ->setCellValue('D1', $this->translateTag('Asset', 'serial_number')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('E1', $this->translateTag('General', 'description')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('F1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('G1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('H1', $this->translateTag('General', 'department'))
                ->setCellValue('I1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('J1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('K1', $this->translateTag('General', 'department'))
                ->setCellValue('L1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('M1', $this->translateTag('General', 'situation'));

        $rcount = 1;
        foreach ($assetInventaryAuxiliarProceso as $assetInventaryAuxiliar) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $assetInventaryAuxiliar->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $assetInventaryAuxiliar->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $assetInventaryAuxiliar->brand_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $assetInventaryAuxiliar->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $assetInventaryAuxiliar->asset_description, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $assetInventaryAuxiliar->codigo_auge, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G' . $rcount, $assetInventaryAuxiliar->original_location, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H' . $rcount, $assetInventaryAuxiliar->departamento_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J' . $rcount, $assetInventaryAuxiliar->location_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K' . $rcount, $assetInventaryAuxiliar->departamento_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('M' . $rcount, $assetInventaryAuxiliar->situacion, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);

        $sheet->getStyle('A1:M1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:M1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:M' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventa_Conformidades' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function cargaTrasladado() {

        $this->load->library('PHPExcel');
        $user_id = $this->auth->get_user_data('user_id');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Asset', 'transferred'));


        $assetInventaryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->cargaTrasladado($user_id);

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('C1', $this->translateTag('General', 'brand'))
                ->setCellValue('D1', $this->translateTag('Asset', 'serial_number')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('E1', $this->translateTag('General', 'description')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('F1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('G1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('H1', $this->translateTag('General', 'department'))
                ->setCellValue('I1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('J1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('K1', $this->translateTag('General', 'department'))
                ->setCellValue('L1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('M1', $this->translateTag('General', 'situation'));

        $rcount = 1;
        foreach ($assetInventaryAuxiliarProceso as $assetInventaryAuxiliar) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $assetInventaryAuxiliar->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $assetInventaryAuxiliar->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $assetInventaryAuxiliar->brand_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $assetInventaryAuxiliar->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $assetInventaryAuxiliar->asset_description, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $assetInventaryAuxiliar->codigo_auge, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G' . $rcount, $assetInventaryAuxiliar->original_location, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H' . $rcount, $assetInventaryAuxiliar->departamento_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J' . $rcount, $assetInventaryAuxiliar->location_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K' . $rcount, $assetInventaryAuxiliar->departamento_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('M' . $rcount, $assetInventaryAuxiliar->situacion, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);


        $sheet->getStyle('A1:M1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:M1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:M' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventa_Trasladados' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function cargaFaltante() {

        $this->load->library('PHPExcel');
        $user_id = $this->auth->get_user_data('user_id');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Asset', 'missing'));


        $assetInventaryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->cargaFaltante($user_id);

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('C1', $this->translateTag('General', 'brand'))
                ->setCellValue('D1', $this->translateTag('Asset', 'serial_number')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('E1', $this->translateTag('General', 'description')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('F1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('G1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('H1', $this->translateTag('General', 'department'))
                ->setCellValue('I1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('J1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('K1', $this->translateTag('General', 'department'))
                ->setCellValue('L1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('M1', $this->translateTag('General', 'situation'));

        $rcount = 1;
        foreach ($assetInventaryAuxiliarProceso as $assetInventaryAuxiliar) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $assetInventaryAuxiliar->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $assetInventaryAuxiliar->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $assetInventaryAuxiliar->brand_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $assetInventaryAuxiliar->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $assetInventaryAuxiliar->asset_description, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $assetInventaryAuxiliar->codigo_auge, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G' . $rcount, $assetInventaryAuxiliar->original_location, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H' . $rcount, $assetInventaryAuxiliar->departamento_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J' . $rcount, $assetInventaryAuxiliar->location_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K' . $rcount, $assetInventaryAuxiliar->departamento_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('M' . $rcount, $assetInventaryAuxiliar->situacion, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);


        $sheet->getStyle('A1:M1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:M1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:M' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventa_Faltantes' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function cargaTodo() {

        $this->load->library('PHPExcel');
        $user_id = $this->auth->get_user_data('user_id');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Asset', 'general_report'));


        $assetInventaryAuxiliarProceso = Doctrine_Core::getTable('AssetInventoryAuxiliarProceso')->retrieveAll($user_id);

        $sheet->setCellValue('A1', $this->translateTag('Asset', 'name_asset'))
                ->setCellValue('B1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('C1', $this->translateTag('General', 'brand'))
                ->setCellValue('D1', $this->translateTag('Asset', 'serial_number')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('E1', $this->translateTag('General', 'description')) //->setCellValue('C1', $this->translateTag('Asset', 'internal_number'))
                ->setCellValue('F1', $this->translateTag('Asset', 'auge_code'))
                ->setCellValue('G1', $this->translateTag('Asset', 'original_location'))
                ->setCellValue('H1', $this->translateTag('General', 'department'))
                ->setCellValue('I1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('J1', $this->translateTag('Asset', 'location_transfer'))
                ->setCellValue('K1', $this->translateTag('General', 'department'))
                ->setCellValue('L1', $this->translateTag('Asset', 'venue_name_subrecinto'))
                ->setCellValue('M1', $this->translateTag('General', 'situation'));

        $rcount = 1;

        foreach ($assetInventaryAuxiliarProceso as $assetInventaryAuxiliar) {


            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $assetInventaryAuxiliar->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $assetInventaryAuxiliar->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $assetInventaryAuxiliar->brand_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $assetInventaryAuxiliar->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $assetInventaryAuxiliar->asset_description, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $assetInventaryAuxiliar->codigo_auge, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G' . $rcount, $assetInventaryAuxiliar->original_location, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H' . $rcount, $assetInventaryAuxiliar->departamento_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_original, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J' . $rcount, $assetInventaryAuxiliar->location_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K' . $rcount, $assetInventaryAuxiliar->departamento_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L' . $rcount, $assetInventaryAuxiliar->nombre_subrecinto_transfer, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('M' . $rcount, $assetInventaryAuxiliar->situacion, PHPExcel_Cell_DataType::TYPE_STRING);

            if ($assetInventaryAuxiliar->situacion == "TRASLADADO") {
                $sheet->getStyle('A' . $rcount . ':M' . $rcount)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'FFFF77'
                    )
                ));
            }

            if ($assetInventaryAuxiliar->situacion == "CONFORMIDADES") {
                $sheet->getStyle('A' . $rcount . ':M' . $rcount)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => '77FF77'
                    )
                ));
            }

            if ($assetInventaryAuxiliar->situacion == "ACTIVO NO REGISTRADO EN IGEO") {
                $sheet->getStyle('A' . $rcount . ':M' . $rcount)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => '77FFFF'
                    )
                ));
            }

            if ($assetInventaryAuxiliar->situacion == "FALTANTE") {
                $sheet->getStyle('A' . $rcount . ':M' . $rcount)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'FF7777'
                    )
                ));
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getStyle('A1:M1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:M1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:M' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $report_name = 'informe_inventario_Todo_' . date('Y-m-d');
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($report_name . '.xls'));
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadTransferredCarga() {
        $array_node = array();
        $array_asset_exist = array();

        $assetInventoryCargaGet = Doctrine_Core::getTable('AssetInventoryAuxiliar')->retrieveAll();

        foreach ($assetInventoryCargaGet as $assetInventoryCarga) {


            $Codigo_recinto = $assetInventoryCarga->asset_inventory_barra;
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));

            if ($nodeOtherData) {

                $assetInventoryCarga->asset_inventory_barra = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern($assetInventoryCarga->asset_inventory_interno);

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }

                if ($asset) {

                    if (!array_key_exists($assetInventoryCarga->asset_inventory_barra, $array_node)) {
                        $array_node[$assetInventoryCarga->asset_inventory_barra] = array();
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra] = array();
                    }

                    if ($asset->node_id != $assetInventoryCarga->asset_inventory_barra) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $assetInventoryCarga->asset_inventory_barra;
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$assetInventoryCarga->asset_inventory_barra][] = $assetInventory;
                    } else if ($asset->node_id == $assetInventoryCarga->asset_inventory_barra) {
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra][] = $asset->asset_id;

                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }
            }
        }

        foreach ($array_node as $node_id => $node) {
            foreach ($node as $inventory) {


                if ($inventory->node_id) {
                    $nodeOtherDataIdDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 8);

                    if ($nodeOtherDataIdDepartamentoCambio) {
                        $valueNombreDepartamentoCambio = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamentoCambio->infra_other_data_option_id);

                        if ($valueNombreDepartamentoCambio) {

                            $valueDepartamentoCambio = $valueNombreDepartamentoCambio->infra_other_data_option_name;
                        } else {
                            $valueDepartamentoCambio = "";
                        }
                    } else {
                        $valueDepartamentoCambio = "";
                    }
                } else {
                    $valueDepartamentoCambio = "";
                }


                if ($inventory->node_id) {
                    $nodeOtherDataNombreRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 2);

                    if ($nodeOtherDataNombreRecintoCambio) {

                        $valueNombreNombreRecintoCambio = $nodeOtherDataNombreRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nodeOtherDataNombreSubRecintoCambio = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($inventory->node_id, 4);

                    if ($nodeOtherDataNombreSubRecintoCambio) {

                        $valueNombreNombreSubRecintoCambio = $nodeOtherDataNombreSubRecintoCambio->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecintoCambio = "";
                    }
                } else {
                    $valueNombreNombreSubRecintoCambio = "";
                }

                if ($inventory->node_id) {
                    $nombreRecintoCambio = $valueNombreNombreRecintoCambio . "/" . $valueNombreNombreSubRecintoCambio;
                } else {
                    $nombreRecintoCambio = "";
                }

                $asset_move_node = Doctrine_Core::getTable('Node')->find($inventory->node_id)->getPath();
                $asset = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);

                if ($assetOtherDatas) {

                    $value = $assetOtherDatas->asset_other_data_value_value;
                } else {
                    $value = "";
                }


                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        if ($valueNombreDepartamento) {

                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {

                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }

                $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;
                $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                $assetInventoryAuxiliarProceso->codigo_auge = $value;

                $assetEsta = Doctrine_Core::getTable('Node')->find($asset->node_id);

                if ($assetEsta) {
                    $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                } else {
                    $assetInventoryAuxiliarProceso->original_location = $this->translateTag('Asset', 'join_this_active_and_the_location_is_not_registered');
                }

                $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                $assetInventoryAuxiliarProceso->location_transfer = $asset_move_node;
                $assetInventoryAuxiliarProceso->departamento_transfer = $valueDepartamentoCambio;
                $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = $nombreRecintoCambio;
                $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'transferred');
                $assetInventoryAuxiliarProceso->save();
            }
        }
    }

    function uploadMissingFaltantes() {
        $array_node = array();
        $array_asset_exist = array();

        $assetInventoryCargaGet = Doctrine_Core::getTable('AssetInventoryAuxiliar')->retrieveAll();

        foreach ($assetInventoryCargaGet as $assetInventoryCarga) {

            $Codigo_recinto = $assetInventoryCarga->asset_inventory_barra;
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));
            $assetInventoryCarga->asset_inventory_barra = @$nodeOtherData->node_id;

            $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($assetInventoryCarga->asset_inventory_interno));

            if ($asset) {
                $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                if ($assetOtherDatas) {

                    $value = $assetOtherDatas->asset_other_data_value_value;
                } else {
                    $value = "";
                }
            } else {
                $value = "";
            }


            if (!isset($asset->asset_id)) { // asset not registered
                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {
                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }
                continue;
            }

            if (!isset($nodeOtherData->node_id)) { // node not registered
                if ($asset) {
                    $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                    if ($nodeOtherDataIdDepartamento) {
                        $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                        if ($valueNombreDepartamento) {
                            $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }
                } else {
                    $valueDepartamento = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                    if ($nodeOtherDataNombreRecinto) {

                        $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreRecinto = "";
                    }
                } else {
                    $valueNombreNombreRecinto = "";
                }

                if ($asset) {
                    $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                    if ($nodeOtherDataNombreSubRecinto) {

                        $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }
                } else {
                    $valueNombreNombreSubRecinto = "";
                }

                if ($asset) {
                    $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                } else {
                    $nombreRecinto = "";
                }
                continue;
            }

            if (!array_key_exists($assetInventoryCarga->asset_inventory_barra, $array_node)) {
                $array_node[$assetInventoryCarga->asset_inventory_barra] = array();
                $array_asset_exist[$assetInventoryCarga->asset_inventory_barra] = array();
            }

            if ($asset->node_id != $assetInventoryCarga->asset_inventory_barra) {
                $assetInventory = new AssetInventory();
                $assetInventory->node_id = $assetInventoryCarga->asset_inventory_barra;
                $assetInventory->asset_id = $asset->asset_id;
                $assetInventory->user_id = $this->auth->get_user_data('user_id');
                $assetInventory->save();

                $array_node[$assetInventoryCarga->asset_inventory_barra][] = $assetInventory;
            } else if ($asset->node_id == $assetInventoryCarga->asset_inventory_barra) {
                $array_asset_exist[$assetInventoryCarga->asset_inventory_barra][] = $asset->asset_id;

                // update last inventory date field
                $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                $asset->save();
            }

            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($Codigo_recinto));


            if ($asset) {
                $otherDataCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);

                if ($otherDataCodigoRecinto) {

                    $valueCodigoRecinto = $otherDataCodigoRecinto->infra_other_data_value_value;
                } else {
                    $valueCodigoRecinto = "";
                }
            } else {
                $valueCodigoRecinto = "";
            }



            if ($Codigo_recinto == $valueCodigoRecinto) {

                if (isset($asset->asset_id) and isset($asset->node_id)) { // asset YES registered
                    if ($asset) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            if ($valueNombreDepartamento) {
                                $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }
                }
            }
        }

        foreach ($array_node as $node_id => $node) {
            foreach ($node as $inventory) {
                $assetArrayTraslado = Doctrine_Core::getTable('Asset')->find($inventory->asset_id);
                $assetArrayTrasl[] = $assetArrayTraslado->asset_id;
            }



            foreach (Doctrine_Core::getTable('Asset')->getAssetDiff($node_id, $array_asset_exist[$node_id]) as $asset) {
                $assetArrayTrasladoFaltante = Doctrine_Core::getTable('Asset')->find($asset->asset_id);
                $assetTableTraslado[] = $assetArrayTrasladoFaltante->asset_id;
            }
        }

        if (isset($assetArrayTraslado)) {
            $result = array_diff($assetTableTraslado, $assetArrayTrasl);
        } else {
            if (isset($assetTableTraslado)) {
                $result = $assetTableTraslado;
            } else {
                $result = 0;
            }
        }

        if ($result == 0) {
            
        } else {
            foreach ($result as $res) {
                if ($res) {
                    $asset = Doctrine_Core::getTable('Asset')->find($res);

                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }

                    if ($asset) {
                        ;
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            if ($valueNombreDepartamento) {

                                $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($asset) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($asset) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }


                    $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                    $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;
                    $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                    $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                    $assetInventoryAuxiliarProceso->codigo_auge = $value;
                    $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                    $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                    $assetInventoryAuxiliarProceso->location_transfer = " ";
                    $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                    $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                    $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'missing');
                    $assetInventoryAuxiliarProceso->save();
                }
            }
        }
    }

    function uploadUnRegisteredMasivo() {

        $array_node = array();
        $array_asset_exist = array();

        $assetInventoryCargaGet = Doctrine_Core::getTable('AssetInventoryAuxiliar')->retrieveAll();

        foreach ($assetInventoryCargaGet as $assetInventoryCarga) {
            $Codigo_recinto = $assetInventoryCarga->asset_inventory_barra;
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));

            if ($nodeOtherData) {
                $assetInventoryCarga->asset_inventory_barra = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($assetInventoryCarga->asset_inventory_interno));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }


                if (!isset($asset->asset_id)) { // asset not registered
                    $node = Doctrine_Core::getTable('Node')->find($assetInventoryCarga->asset_inventory_barra);

                    if ($node) {
                        $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 8);

                        if ($nodeOtherDataIdDepartamento) {
                            $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                            if ($valueNombreDepartamento) {
                                $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }
                    } else {
                        $valueDepartamento = "";
                    }

                    if ($node) {
                        $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 2);

                        if ($nodeOtherDataNombreRecinto) {

                            $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreRecinto = "";
                        }
                    } else {
                        $valueNombreNombreRecinto = "";
                    }

                    if ($node) {
                        $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node->node_id, 4);

                        if ($nodeOtherDataNombreSubRecinto) {

                            $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }
                    } else {
                        $valueNombreNombreSubRecinto = "";
                    }

                    if ($node) {
                        $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                    } else {
                        $nombreRecinto = "";
                    }

                    if ($asset) {
                        $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                        $assetInventoryAuxiliarProceso->asset_name = " ";
                        $assetInventoryAuxiliarProceso->brand_name = " ";
                        $assetInventoryAuxiliarProceso->asset_num_serie_intern = $assetInventoryCarga->asset_inventory_intern;
                        $assetInventoryAuxiliarProceso->codigo_auge = " ";

                        if (isset($node->node_id)) {
                            $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                        } else {
                            $assetInventoryAuxiliarProceso->original_location = "El activo no esta Registrado y La ubicacion no esta registrada al mometo de cargar";
                        }

                        if (isset($node->node_id)) {
                            $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                        } else {
                            $assetInventoryAuxiliarProceso->departamento_original = " ";
                        }

                        if (isset($node->node_id)) {
                            $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                        } else {
                            $assetInventoryAuxiliarProceso->nombre_subrecinto_original = " ";
                        }



                        $assetInventoryAuxiliarProceso->location_transfer = " ";
                        $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                        $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                        $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'active_not_registered');
                        $assetInventoryAuxiliarProceso->save();
                    }
                }


                if ($asset) {

                    if (!array_key_exists($assetInventoryCarga->asset_inventory_barra, $array_node)) {
                        $array_node[$assetInventoryCarga->asset_inventory_barra] = array();
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra] = array();
                    }

                    if ($asset->node_id != $assetInventoryCarga->asset_inventory_barra) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $assetInventoryCarga->asset_inventory_barra;
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$assetInventoryCarga->asset_inventory_barra][] = $assetInventory;
                    } else if ($asset->node_id == $assetInventoryCarga->asset_inventory_barra) {
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra][] = $asset->asset_id;

                        // update last inventory date field
                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }
            }
        }
    }

    function uploadUnchangedMasivo() {

        $array_node = array();
        $array_asset_exist = array();

        $assetInventoryCargaGet = Doctrine_Core::getTable('AssetInventoryAuxiliar')->retrieveAll();

        foreach ($assetInventoryCargaGet as $assetInventoryCarga) {
            $Codigo_recinto = $assetInventoryCarga->asset_inventory_barra;
            $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));

            if ($nodeOtherData) {
                $assetInventoryCarga->asset_inventory_barra = @$nodeOtherData->node_id;

                $asset = Doctrine_Core::getTable('Asset')->retrieveOneByNumIntern(trim($assetInventoryCarga->asset_inventory_interno));

                if ($asset) {
                    $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);
                    if ($assetOtherDatas) {

                        $value = $assetOtherDatas->asset_other_data_value_value;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = "";
                }
                if ($asset) {

                    if (!array_key_exists($assetInventoryCarga->asset_inventory_barra, $array_node)) {
                        $array_node[$assetInventoryCarga->asset_inventory_barra] = array();
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra] = array();
                    }

                    if ($asset->node_id != $assetInventoryCarga->asset_inventory_barra) {
                        $assetInventory = new AssetInventory();
                        $assetInventory->node_id = $assetInventoryCarga->asset_inventory_barra;
                        $assetInventory->asset_id = $asset->asset_id;
                        $assetInventory->user_id = $this->auth->get_user_data('user_id');
                        $assetInventory->save();

                        $array_node[$assetInventoryCarga->asset_inventory_barra][] = $assetInventory;
                    } else if ($asset->node_id == $assetInventoryCarga->asset_inventory_barra) {
                        $array_asset_exist[$assetInventoryCarga->asset_inventory_barra][] = $asset->asset_id;

                        $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                        $asset->save();
                    }
                }


                $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($assetInventoryCarga->asset_inventory_barra));


                if ($asset) {
                    $otherDataCodigoRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);

                    if ($otherDataCodigoRecinto) {

                        $valueCodigoRecinto = $otherDataCodigoRecinto->infra_other_data_value_value;
                    } else {
                        $valueCodigoRecinto = "";
                    }
                } else {
                    $valueCodigoRecinto = "";
                }



                if ($Codigo_recinto == $valueCodigoRecinto) {

                    if (isset($asset->asset_id) and isset($asset->node_id)) { // asset YES registered
                        if ($asset) {
                            $nodeOtherDataIdDepartamento = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 8);

                            if ($nodeOtherDataIdDepartamento) {
                                $valueNombreDepartamento = Doctrine_Core::getTable('InfraOtherDataOption')->find($nodeOtherDataIdDepartamento->infra_other_data_option_id);

                                IF ($valueNombreDepartamento) {
                                    $valueDepartamento = $valueNombreDepartamento->infra_other_data_option_name;
                                } else {
                                    $valueDepartamento = "";
                                }
                            } else {
                                $valueDepartamento = "";
                            }
                        } else {
                            $valueDepartamento = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 2);

                            if ($nodeOtherDataNombreRecinto) {

                                $valueNombreNombreRecinto = $nodeOtherDataNombreRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreRecinto = "";
                            }
                        } else {
                            $valueNombreNombreRecinto = "";
                        }

                        if ($asset) {
                            $nodeOtherDataNombreSubRecinto = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 4);

                            if ($nodeOtherDataNombreSubRecinto) {

                                $valueNombreNombreSubRecinto = $nodeOtherDataNombreSubRecinto->infra_other_data_value_value;
                            } else {
                                $valueNombreNombreSubRecinto = "";
                            }
                        } else {
                            $valueNombreNombreSubRecinto = "";
                        }

                        if ($asset) {
                            $nombreRecinto = $valueNombreNombreRecinto . "/" . $valueNombreNombreSubRecinto;
                        } else {
                            $nombreRecinto = "";
                        }

                        $assetInventoryAuxiliarProceso = new AssetInventoryAuxiliarProceso();
                        $assetInventoryAuxiliarProceso->asset_name = $asset->asset_name;

                        if (isset($asset->brand_id)) {
                            $assetInventoryAuxiliarProceso->brand_name = $asset->Brand->brand_name;
                        } else {
                            $assetInventoryAuxiliarProceso->brand_name = " ";
                        }

                        if (isset($asset->asset_id)) {
                            $assetInventoryAuxiliarProceso->asset_num_serie_intern = $asset->asset_num_serie_intern;
                        } else {
                            $assetInventoryAuxiliarProceso->asset_num_serie_intern = " ";
                        }

                        if (isset($asset->asset_id)) {
                            $assetInventoryAuxiliarProceso->codigo_auge = $value;
                        } else {
                            $assetInventoryAuxiliarProceso->codigo_auge = " ";
                        }

                        $assetInventoryAuxiliarProceso->original_location = Doctrine_Core::getTable('Node')->find($asset->node_id)->getPath();
                        $assetInventoryAuxiliarProceso->departamento_original = $valueDepartamento;
                        $assetInventoryAuxiliarProceso->nombre_subrecinto_original = $nombreRecinto;
                        $assetInventoryAuxiliarProceso->location_transfer = " ";
                        $assetInventoryAuxiliarProceso->departamento_transfer = " ";
                        $assetInventoryAuxiliarProceso->nombre_subrecinto_transfer = " ";
                        $assetInventoryAuxiliarProceso->situacion = $this->translateTag('Asset', 'conformitie');
                        $assetInventoryAuxiliarProceso->save();
                    }
                }
            }
        }
    }

}
