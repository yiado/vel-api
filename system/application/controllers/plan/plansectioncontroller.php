<?php

/** @package    Controller
 *  @subpackage PlanSectionController
 */
class PlanSectionController extends APP_Controller
{

    function PlanSectionController()
    {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Lista las ultimas versiones de planos por categoria
     *
     * @post int node_id
     */
    function get()
    {
        $plan_id = $this->input->post('plan_id');
        $planSection = Doctrine_Core::getTable('PlanSection')->finByPlanId($plan_id);

        if($planSection->count())
        {
            echo '({"total":"'.$planSection->count().'", "results":'.$this->json->encode($planSection->toArray()).'})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getFiltered()
    {
        $plan_id = $this->input->post('plan_id');
        $plans = Doctrine_Core::getTable('Plan')->retrieveById($plan_id);
        $infoNodoPadre = Doctrine_Core::getTable('InfraInfo')->findByNodeId($plans->node_id);
        $infra_info_usable_area_total = $infoNodoPadre->infra_info_usable_area_total;

        $plan_section_ids = $this->input->post('plan_section_ids');

        if($plan_section_ids)
        {
            $plan_section_ids = explode(",",$plan_section_ids);
            $planSection = Doctrine_Core::getTable('PlanSection')->finByPlanId($plan_id,1);

            $infra_info_usable_area = 0;

            foreach($planSection->toArray() as $key=> $section)
                {

                $final[] = $section;

                foreach($plan_section_ids as $plan_section_id)
                    {

                    if(trim($section['plan_section_id'])==trim($plan_section_id))
                    {
                        $planNode = Doctrine_Core::getTable('PlanNode')->findByNodeAndPlan($plan_id,$plan_section_id);
                        foreach($planNode as $planNodeSuma)
                            {
                            $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($planNodeSuma->node_id);

                            if($info)
                            {
                                $infra_info_usable_area = $infra_info_usable_area+$info->infra_info_usable_area;
                            }
                            }
                        if($infra_info_usable_area_total!=0)
                        {
                            $infra_info_usable_area_total_p = round((100*$infra_info_usable_area)/$infra_info_usable_area_total,2);
                        } else
                        {
                            $infra_info_usable_area_total_p = 0;
                        }

                        $final[$key]['infra_info_usable_area'] = $infra_info_usable_area;
                        $final[$key]['infra_info_usable_area_total'] = $infra_info_usable_area_total;
                        $final[$key]['infra_info_usable_area_total_p'] = $infra_info_usable_area_total_p;

                        $infra_info_usable_area = 0;
                    }
                    }
                }
            if($planSection->count())
            {
                echo '({"total":"'.$planSection->count().'", "results":'.$this->json->encode($final).'})';
            } else
            {
                echo '({"total":"0", "results":[]})';
            }
        } else
        {
            $planSection = Doctrine_Core::getTable('PlanSection')->finByPlanId($plan_id,1);

            if($planSection->count())
            {
                echo '({"total":"'.$planSection->count().'", "results":'.$this->json->encode($planSection->toArray()).'})';
            } else
            {
                echo '({"total":"0", "results":[]})';
            }
        }
    }

    function getByNode()
    {
        $plan_id = $this->input->post('plan_id');
        $plans = Doctrine_Core::getTable('Plan')->retrieveById($plan_id);

        $this->load->library('RowNodes');
        $node_id = ($plans->node_id&&is_numeric($plans->node_id) ? $plans->node_id : NULL );

        $node = Doctrine_Core::getTable('Node')->find($node_id)->getNode();
        $nodesCantity = $node->getNumberChildren();
        $nodes = $node->getChildren();

        foreach($nodes->toArray() as $key=> $nod)
            {

            $final[] = $nod;
            $PlanNode = Doctrine_Core::getTable('PlanNode')->findOneBy('node_id',$nod['node_id']);
            $final[$key]['handler'] = $PlanNode['handler'];

            if($PlanNode['plan_section_id'])
            {
                $plan_section = Doctrine_Core::getTable('PlanSection')->find($PlanNode['plan_section_id']);
                $final[$key]['plan_section_name'] = $plan_section['plan_section_name'];
            }
            }
        if($nodes->count())
        {
            echo '({"total":"'.$nodes->count().'", "results":'.$this->json->encode($final).'})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getSumTotal()
    {
        $plan_id = $this->input->post('plan_id');
        $plan_section_ids = $this->input->post('plan_section_ids');

        $plans = Doctrine_Core::getTable('Plan')->retrieveById($plan_id);
        $nod = Doctrine_Core::getTable('Node')->find($plans->node_id);
        $ruta = $nod->getPath();
        $infoNodoPadre = Doctrine_Core::getTable('InfraInfo')->findByNodeId($plans->node_id);
        $planSeccion = explode(",",$plan_section_ids);
        $result = array();
        $cont = 0;
        $total_infra_info_usable_area = 0;

        $node_name = array();

        foreach($planSeccion as $plan_section_id)
            {
            $planNode = Doctrine_Core::getTable('PlanNode')->findByNodeAndPlan($plan_id,$plan_section_id);

            foreach($planNode as $plan_node)
                {
                $node = Doctrine_Core::getTable('Node')->find($plan_node->node_id);

                $node_id = $node->node_id;
                $node_name[] = $node->node_id;
                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                $node_type_id = $nodeType->node_type_id;
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);


                foreach($infraConfig as $config)
                    {
                    if($config->infra_attribute=='infra_info_usable_area')
                    {
                        $total_infra_info_usable_area = $total_infra_info_usable_area+(($info) ? $info->{$config->infra_attribute} : NULL);
                    }
                    }
                }
            }

        $plan_filename = $plans->plan_filename;

        if($infoNodoPadre->infra_info_usable_area_total!=0)
        {
            $infra_info_usable_area_total_p = (100*$total_infra_info_usable_area)/$infoNodoPadre->infra_info_usable_area_total;
        } else
        {
            $infra_info_usable_area_total_p = 0;
        }
        $final = array();
        $resultado = array_unique($node_name);
        if($resultado)
        {
            foreach($resultado as $value)
                {
                $arr[] = $value;
                }
        } else
        {
            $arr[] = 0;
        }

        $infra_info_usable_area_total = $infoNodoPadre->infra_info_usable_area_total;

        $final['ruta'] = $ruta;
        $final['node_name'] = $arr;
        $final['plan_filename'] = $plan_filename;
        $final['infra_info_usable_area'] = $total_infra_info_usable_area;
        $final['infra_info_usable_area_total'] = $infra_info_usable_area_total;
        $final['infra_info_usable_area_total_p'] = round($infra_info_usable_area_total_p,2);

        echo '({"total":"'."8".'", "results":'.$this->json->encode($final).'})';
    }

    function nombreNodAndPlan()
    {
        $plan_id = $this->input->post('plan_id');
        $node_id = $this->input->post('node_id');

        $plan = Doctrine_Core::getTable('Plan')->find($plan_id);
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $final = array();
        $final['node_name'] = $node->node_name;
        $final['plan_name'] = $plan->plan_filename;


        echo '({"total":"'."2".'", "results":'.$this->json->encode($final).'})';
    }

    function update()
    {
        $planSection = Doctrine_Core::getTable('PlanSection')->find($this->input->post('plan_section_id'));
        $planSection->fromArray($this->input->postall());
        $planSection->plan_section_status = ($this->input->post('plan_section_status')=='false' ? 0 : 1);
        $planSection->save();
        echo '{"success": true}';
    }

    function updateAll()
    {
        $plan_id = $this->input->post('plan_id');
        $planSections = Doctrine_Core::getTable('PlanSection')->finByPlanId($plan_id);

        foreach($planSections as $planSection)
            {
            $planSection->plan_section_status = $this->input->post('plan_section_status');
            $planSection->save();
            }
        echo '{"success": true}';
    }

    function getgraphCompleto1($plan)
    {

        list($plan_section_ids,$plan_id) = explode('-',$plan);

//        $plan_id = $this->input->post ( 'plan_id' );
//        $plan_section_ids = $this->input->post ( 'plan_section_ids' );

        $plans = Doctrine_Core::getTable('Plan')->retrieveById($plan_id);
        $plan_version = $plans->plan_version;

        $date = date_create($plans->plan_datetime);
        $plan_datetime = date_format($date,'d/m/Y H:i');

        $nod = Doctrine_Core::getTable('Node')->find($plans->node_id);
        $ruta = $nod->getPath();
        $infoNodoPadre = Doctrine_Core::getTable('InfraInfo')->findByNodeId($plans->node_id);
        $planSeccion = explode(".",$plan_section_ids);
        $result = array();
        $cont = 0;
        $total_infra_info_usable_area = 0;

        $node_name = array();

        foreach($planSeccion as $plan_section_id)
            {
            $planNode = Doctrine_Core::getTable('PlanNode')->findByNodeAndPlan($plan_id,$plan_section_id);

            foreach($planNode as $plan_node)
                {
                $node = Doctrine_Core::getTable('Node')->find($plan_node->node_id);

                $node_id = $node->node_id;
                $node_name[] = $node->node_id;
                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                $node_type_id = $nodeType->node_type_id;
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);


                foreach($infraConfig as $config)
                    {
                    if($config->infra_attribute=='infra_info_usable_area')
                    {
                        $total_infra_info_usable_area = $total_infra_info_usable_area+(($info) ? $info->{$config->infra_attribute} : NULL);
                    }
                    }
                }
            }

        $plan_filename = $plans->plan_filename;

        if($infoNodoPadre->infra_info_usable_area_total!=0)
        {
            $infra_info_usable_area_total_p = (100*$total_infra_info_usable_area)/$infoNodoPadre->infra_info_usable_area_total;
        } else
        {
            $infra_info_usable_area_total_p = 0;
        }
        $final = array();
        $resultado = array_unique($node_name);
        if($resultado)
        {
            foreach($resultado as $value)
                {
                $arr[] = $value;
                }
        } else
        {
            $arr[] = 0;
        }

        $infra_info_usable_area_total = $infoNodoPadre->infra_info_usable_area_total;

        $final['ruta'] = $ruta;
        $final['node_name'] = $arr;
        $final['plan_filename'] = $plan_filename;
        $final['infra_info_usable_area'] = $total_infra_info_usable_area;
        $final['infra_info_usable_area_total'] = $infra_info_usable_area_total;
        $final['infra_info_usable_area_total_p'] = round($infra_info_usable_area_total_p,2);

        $porcentaje_total = 100-$final['infra_info_usable_area_total_p'];
        
        //VALIDACIONES
        $dir = str_replace("\\","/",$this->config->item('temp_dir'));
//        $plan = str_replace("\\", "/", $this->config->item('plan_dir'));
        //BORRA Y CREA EL NUEVO GRAFICO EN LA CARPETA TEMP
        if($dir.'grafico.png')
        {
            unlink($dir.'grafico.png');
        }

        //CONFIGURACIONES
        $alto = 350;
        $ancho = 250;
        $this->load->library('graph');
        $data = array(round($porcentaje_total,2),round($infra_info_usable_area_total_p,2));
        $legends = array("Porcentaje Restante","Porcentaje Utilizado");

        // Creating a new graphic   
        $this->graph = new PieGraph($alto,$ancho);
        $this->graph->SetShadow();

        // Naming the graphic  
        $this->graph->title->Set('Grafico Estadistico');
        $this->graph->title->SetFont(FF_VERDANA,FS_BOLD,14);

        // Legend positioning (%/100)   
        $this->graph->legend->Pos(0.1,0.2);

        // Creating a 3D pie graphic   
        $p1 = new PiePlot3D($data);

        // Setting the graphic center (%/100)   
        $p1->SetCenter(0.45,0.5);

        // Setting the ancle   
        $p1->SetAngle(30);

        // Choosing the type   
        $p1->value->SetFont(FF_ARIAL,FS_NORMAL,12);

        // Setting legends for graphic segments  
        $p1->SetLegends($legends);

        // Adding the diagram to the graphic  

        $this->graph->Add($p1);
        // Showing graphic  

        $this->graph->Stroke($dir.'grafico.png');



        $this->load->library('pdf');
        $this->pdf->SetFont('helvetica','',8);

        // add a page
        $this->pdf->AddPage();
        $this->pdf->Image($dir.'grafico.png');
        $this->pdf->lastPage();

        $html = '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
        $html .= '<blockquote>'.'RUTA RECINTO:     '.$final['ruta'].'<br>';
        $html .= 'VERSIÓN DEL PLANO:'.$plan_version.'<br>';
        $html .= 'FECHA DE CARGA:   '.$plan_datetime.'<br><br><br>';
        $html .= 'SUPERFICIE TOTAL (100%):                     '.$final['infra_info_usable_area_total'].' (M2)'.'<br>';
        $html .= 'SUPERFICIE M2 CONSULTADOS (SELECCIONADOS):   '.$final['infra_info_usable_area'].' (M2)'.'<br>';
        $html .= 'FACTOR DE OCUPACIÓN (PORCENTAJE):            '.$final['infra_info_usable_area_total_p'].' %'.'<br>';

        $this->pdf->writeHTML($html,true,false,true,false,'');
        $this->pdf->Output('grafico'.'.pdf','D');
    }

    function getgraphCompleto($plan)
    {
        list($plan_section_ids,$plan_id) = explode('-',$plan);

        $plans = Doctrine_Core::getTable('Plan')->retrieveById($plan_id);
        $plan_version = $plans->plan_version;

        $date = date_create($plans->plan_datetime);
        $plan_datetime = date_format($date,'d/m/Y H:i');

        $nod = Doctrine_Core::getTable('Node')->find($plans->node_id);
        $ruta = $nod->getPath();
        $infoNodoPadre = Doctrine_Core::getTable('InfraInfo')->findByNodeId($plans->node_id);
        $planSeccion = explode(".",$plan_section_ids);
        $result = array();
        $cont = 0;
        $total_infra_info_usable_area = 0;

        $node_name = array();
        $totales = array();
        $plan_section_colores = array();
        $plan_section_names = array();

        foreach($planSeccion as $plan_section_id)
            {

            $planNode = Doctrine_Core::getTable('PlanNode')->findByNodeAndPlan($plan_id,$plan_section_id);
            $info_total = 0;

            foreach($planNode as $plan_node)
            {
                $node = Doctrine_Core::getTable('Node')->find($plan_node->node_id);

                $node_id = $node->node_id;
                $node_name[] = $node->node_id;
                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                $node_type_id = $nodeType->node_type_id;
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeId($node_type_id);

                $info_total = $info->infra_info_usable_area+$info_total;

                foreach($infraConfig as $config)
                {
                    if($config->infra_attribute=='infra_info_usable_area')
                    {
                        $total_infra_info_usable_area = $total_infra_info_usable_area+(($info) ? $info->{$config->infra_attribute} : NULL);
                    }
                }
            }
            $planSection = Doctrine_Core::getTable('PlanSection')->find($plan_section_id);
            $totales[] = $info_total;
            $plan_section_colores[] = $planSection->plan_section_color;
            $plan_section_names[] = $planSection->plan_section_name;
            }

        $plan_filename = $plans->plan_filename;

        if($infoNodoPadre->infra_info_usable_area_total!=0)
        {
            $infra_info_usable_area_total_p = (100*$total_infra_info_usable_area)/$infoNodoPadre->infra_info_usable_area_total;
        } else
        {
            $infra_info_usable_area_total_p = 0;
        }

        $final = array();
        $resultado = array_unique($node_name);
        if($resultado)
        {
            foreach($resultado as $value)
            {
                 $arr[] = $value;
            }
        } else
        {
            $arr[] = 0;
        }

        $infra_info_usable_area_total = $infoNodoPadre->infra_info_usable_area_total;

        $final['ruta'] = $ruta;
        $final['node_name'] = $arr;
        $final['plan_filename'] = $plan_filename;
        $final['infra_info_usable_area'] = $total_infra_info_usable_area;
        $final['infra_info_usable_area_total'] = $infra_info_usable_area_total;
        $final['infra_info_usable_area_total_p'] = round($infra_info_usable_area_total_p,3);

        $porcentaje_total = 100-$final['infra_info_usable_area_total_p'];
        $total_restante = $infra_info_usable_area_total-$total_infra_info_usable_area;

        $alto = 400;
        $ancho = 250;
        $this->load->library('graph');

        $totales[] = $total_restante;

        $porcentajes = array();
        $total_porcentaje = 0;
        
        foreach($totales as $porcen)
            {
            if($porcen>0)
            {
                $porcentajes[] = round(($porcen*100)/$infra_info_usable_area_total,3);
                $total_porcentaje = round((($porcen*100)/$infra_info_usable_area_total)+$total_porcentaje,3);
            } else
            {
                $porcentajes[] = 0;
            }
            }

        $data = $porcentajes;
        $plan_section_names[] = 'NO SELECCIONADOS';
        $plan_section_colores[] = '39F1E3';
        
        $n = 0;
        $final_nombres_porcentaje = array();
        $total_porcentaje_guion = array();
        foreach($plan_section_names as $item)
        {
           
            $final_nombres_porcentaje[$n] = '[' . $porcentajes[$n] . '%' . '+' . $plan_section_names[$n] . ']';
//            if ($total_porcentaje_guion != ''){
//               $total_porcentaje_guion = $total_porcentaje_guion . ',' . int($porcentajes[$n]); 
//            } else {
                $total_porcentaje_guion[] = $porcentajes[$n];
//            }
            $n++;
        } 
        $total_porcentaje_guion= implode(',', $total_porcentaje_guion);
//        print_r($total_porcentaje_guion); exit();
        
        $i = 0;
        $final_nombres = array();
        $total_nombres = '';
        foreach($plan_section_colores as $item)
        {
            if ($total_nombres != ''){
               $total_nombres = $total_nombres . '|' . $final_nombres_porcentaje[$i]; 
            } else {
                $total_nombres = $final_nombres_porcentaje[$i];
            }
            $i++;
        }   

        $h = 0;
        $colors = array();
        $total_color = '';
        foreach($plan_section_colores as $item)
        {
            $colors[$h] = $plan_section_colores[$h];
            if ($total_color != ''){
               $total_color = $total_color . '|' . $plan_section_colores[$h]; 
            } else {
                $total_color = $plan_section_colores[$h];
            }

            $h++;
        }
        $legends = $final_nombres;
        
        //GRAFICO DESDE LIBRERIA

//        $dir = str_replace("\\","/",$this->config->item('temp_dir'));
//        if($dir.'grafico.png')
//        {
//            unlink($dir.'grafico.png');
//        }
//        
//        $this->load->library('graph');
//        $this->graph = new PieGraph(540,400);
//        $this->graph->SetShadow();
//        $this->graph->title->Set('GRÁFICO DE OCUPACIÓN'.'  '.'('.$nod->node_name.')');
//        $this->graph->title->SetFont(FF_VERDANA,FS_NORMAL,11);
//        $this->graph->legend->Pos(0.01,0.1);
//        $this->graph->SetMarginColor('gray');
//
//        $p1 = new PiePlot3D($data);
//        $p1->SetCenter(0.45,0.6);
//        $p1->SetAngle(20);
//        $p1->value->SetFont(FF_ARIAL,FS_NORMAL,10);
//        $p1->SetSliceColors($colors);
//        $p1->SetLegends($legends);

//        $this->graph->Add($p1);
//        $this->graph->Stroke($dir.'grafico.png');

        $this->load->library('pdf');
        $this->pdf->SetFont('helvetica','',8);

        // add a page
        $this->pdf->AddPage();
//        $this->pdf->Image($dir . 'grafico.png');
//        $this->pdf->lastPage();

        $html = '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
        $html .= '<br>';
        $html .= '<table width="100%" border="1" cellpadding="2" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>Secciones</strong></div></td>';
        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>M2</strong></div></td>';
        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>%</strong></div></td>';
        $html .= '</tr>';

        $j = 0;
        $final_nombres = array();
        foreach($plan_section_names as $item)
        {
            $html .= '<tr>';
            $html .= '<td>'.' '.$plan_section_names[$j].'</td>';
            $html .= '<td><div align="center">'.$totales[$j].'</div></td>';
            $html .= '<td><div align="center">'.$porcentajes[$j].'</div></td>';
            $html .= '</tr>';
            $j++;
        }

        $html .= '</table>';
        
        $html .= '<br>';
        $html .= '<table width="100%" border="1" cellpadding="2" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>TOTALES</strong></div></td>';
        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>'.round($infra_info_usable_area_total).' (M2)'.'</strong></div></td>';
        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>'.$total_porcentaje.' (%)'.'</strong></div></td>';
        $html .= '</tr>';
        $html .= '</table>';
        
        $html .= '<p>';
        $html .= '<h1>DETALLES DEL PLANO</h1>';
        $html .= '<table width="100%">';
        $html .= '<tr><td>RUTA RECINTO:</td><td>'.$final['ruta'].'</td></tr>';
        $html .= '<tr><td>VERSIÓN DEL PLANO:</td><td>'.$plan_version.'</td></tr>';
        $html .= '<tr><td>FECHA DE CARGA:</td><td>'.$plan_datetime.'</td></tr>';
        $html .= '</table>';
        $html .= '</p>';
        
        $html .= '<p>';
        $html .= '<h1>DETALLE RESUMIDO DE LO SELECCIONADO</h1>';
        $html .= '<table width="100%">';
        $html .= '<tr><td>SUPERFICIE TOTAL (100%):</td><td>'.$final['infra_info_usable_area_total'].' (M2)'.'</td></tr>';
        $html .= '<tr><td>SUPERFICIE M2 CONSULTADOS (SELECCIONADOS):</td><td>'.$final['infra_info_usable_area'].' (M2)'.'</td></tr>';
        $html .= '<tr><td>FACTOR DE OCUPACIÓN (PORCENTAJE):</td><td>'.$final['infra_info_usable_area_total_p'].' %'.'</td></tr>';
        $html .= '</table>';
        $html .= '</p>';
       
//        print_r($total_porcentaje_guion); exit();
        //GRAFICO DESDE URL DE GOOGLE
        $image = 'http://chart.apis.google.com/chart?cht=p3&chd=s:Uf9a&chs=550x180';
        //TITULO
        $image .='&chtt=GRÁFICO+DE+OCUPACIÓN+' .'('.$nod->node_name.')';
        
        //COLORES
        $image .='&chco=' . $total_color;
//        $image .='&chco=222222|000000|444444|666666|999999|FFFFFF';
        //LABELS
        $image .='&chl=' . $total_nombres;
//        $image .='&chl=[19%+ARCHIVO]|[6%+AGENTE]|[0%+AUTOSERVICIO]|[28%+BANO]|[7%+BOVEDA]|[40%+NO+SELECCIONADO]';
        //DATOS
        $image .='&chd=t:'. $total_porcentaje_guion;
//        print_r($image); EXIT();
//        $image .='&chd=t:19|6|0|28|7|40';
        $this->pdf->Image($image);
//        $this->pdf->Image($image,$x = 15,$y = 40,$w = 30,$h = 20,$type = 'PNG',$link = ' [ url ] http://www.tcpdf.org [ / url ] ',$align = '',$resize = false,$dpi = 300,$palign = '',$ismask = false,$imgmask = false,$border = 0,$fitbox = false,$hidden = false,$fitonpage = false);

        $this->pdf->writeHTML($html,true,false,true,false,'');
        $this->pdf->Output('grafico'.'.pdf','D');
    }

    function getgraph22()
    {
        $this->load->library('graph');
        $this->graph = new GoogChart();
        $data = array('IE7 22%'=>22,'IE6 30.7%'=>30.7,'IE5 1.7%'=>1.7,'Firefox 36.5%'=>36.5,'Mozilla 1.1%'=>1.1,'Safari 2%'=>2,'Opera 1.4%'=>1.4);
        $color = array('#D50B41','#151515','#999999');
        $this->graph->setChartAttrs(array('type'=>'pie','data'=>$data,'size'=>array(450,300),'color'=>$color));
        echo $this->graph;
    }

}
