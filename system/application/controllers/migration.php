<?php

	class migration extends APP_Controller {
		
		
		var $attr_config = array(1=>-1,2=>2,3=>3,4=>4,5=>-1,6=>6,7=>-1,8=>7,9=>-1,10=>8,11=>-1,12=>-1,13=>9,14=>-1,15=>10,16=>-1,17=>11,18=>-1,19=>12,20=>-1,21=>13,22=>-1,23=>14,24=>5,25=>15,26=>16,27=>17,28=>18,29=>19,30=>20,31=>21,32=>22,33=>23,34=>24,35=>-1,36=>-1,37=>-1,38=>25,39=>-1,40=>-1,41=>-1,42=>-1,43=>-1,44=>-1,45=>-1,46=>1,200=>1,300=>9,301=>4,302=>2,303=>3,304=>-1,305=>1,306=>29,307=>3,308=>4,309=>5,310=>9,311=>10,312=>11,313=>12,314=>13,315=>14,316=>15,317=>-1,318=>16,319=>17,320=>18,321=>19,322=>20,323=>21,324=>22,325=>23,326=>24,327=>-1,328=>25,329=>26,330=>27,331=>28,332=>-1,333=>-1,334=>-1,335=>-1,336=>-1,337=>-1,338=>-1,339=>-1,340=>-1,341=>-1,342=>-1,343=>-1,344=>2,345=>-1,346=>1,347=>-1,348=>2,349=>-1,350=>3,351=>4,352=>5,353=>-1,354=>1,355=>0,356=>1,357=>10,358=>6,359=>9,360=>6,361=>6,362=>7,363=>9,364=>10,365=>8,366=>0,367=>0,369=>2);
		
		function alex () {
			
			$migconn = $this->load->database('migration', TRUE);
			$q = $migconn->query("SELECT * FROM node WHERE node_id = 79851");
			
			print_r($q->result());
			
		}
		
		function nodes ( $node_parent = 0 ) {
			
			$migconn = $this->load->database('migration', TRUE);
			
			ini_set("memory_limit","2000M");
			
			$q = $migconn->query("SELECT * FROM node WHERE node_father_id = " . $node_parent);
			
			foreach ($q->result() as $__node) {
				
					$node = new Node();
					$node['node_id'] = $__node->node_id;
					$node['node_name'] = $__node->name;
					$node['node_type_id'] = $__node->node_type_id;
					$node->save();
					
					if ($__node->node_father_id > 0) {
						
						$node->getNode()->insertAsLastChildOf(Doctrine_Core::getTable('Node')->find($__node->node_father_id));
						
					} else {
						
						$treeObject = Doctrine_Core::getTable('Node')->getTree();
						$treeObject->createRoot($node);
						
					}
					
					$this->nodes($__node->node_id);
					
			}
			
		}
		
		function datos ( $offset, $limit=100000 ) {
			
			$migconn = $this->load->database('migration', TRUE);
			
			$nodes = $migconn->query("SELECT * FROM node ORDER BY node_id ASC LIMIT " . $offset . ", " . $limit);
			
			foreach ($nodes->result() as $node) {
				
				$infos = $migconn->query("SELECT * FROM info_value WHERE node_id = " . $node->node_id);
				
				foreach ($infos->result() as $info) {
					
					$valueOther = new InfraOtherDataValue();
					$valueOther->infra_other_data_attribute_id = $info->attribute_id;
					$valueOther->node_id = $info->node_id;
					
					if ($info->info_option_id != null) {
						$valueOther->infra_other_data_option_id = $info->info_option_id;
					} else {
						$valueOther->infra_other_data_value_value = $info->value;
					}
					
					$valueOther->save();
					
				}
					
			}
			
		}
		
		function superficieMigrate () {
			
			echo '<pre>';
			
			ini_set("memory_limit","2000M");
			
			$nodes = $this->db->query("SELECT * FROM node JOIN infra_other_data_value ON node.node_id = infra_other_data_value.node_id AND infra_other_data_attribute_id = 24");
			
			foreach ($nodes->result() as $node) {
								
				$info = new InfraInfo();
				$info->node_id = $node->node_id;
				$info->infra_info_usable_area = $node->infra_other_data_value_value;
				$info->save();
					
			}
			
		}
		
		function superficieTotalMigrate () {
			
			echo '<pre>';
			
			ini_set("memory_limit","2000M");
			
			$nodes = $this->db->query("SELECT * FROM node JOIN infra_other_data_value ON node.node_id = infra_other_data_value.node_id AND infra_other_data_attribute_id = 365");
			
			foreach ($nodes->result() as $node) {
				
				$info = new InfraInfo();
				$info->node_id = $node->node_id;
				$info->infra_info_usable_area_total = $node->infra_other_data_value_value;
				$info->save();
					
			}
			
		}
		
		function superficieFlexMigrate ( $att_id, $infra_att ) {
			
			echo '<pre>';
			
			ini_set("memory_limit","2000M");
			
			$nodes = $this->db->query("SELECT * FROM node JOIN infra_other_data_value ON node.node_id = infra_other_data_value.node_id AND infra_other_data_attribute_id = " . $att_id);
			
			foreach ($nodes->result() as $node) {
				
				$info = new InfraInfo();
				
				$info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node->node_id);
				if ($info === false) {
					$info = new InfraInfo();
					$info->node_id = $node->node_id;
				}
				$info->{$infra_att} = $node->infra_other_data_value_value;
				$info->save();
					
			}
			
		}
		
		function buildFicha () {
			
			echo '<pre>';
			
			foreach(array_fill(4, 19, true) as $n_id => $nod ) {
	
				foreach (array_fill(1, 46, true) as $a_i => $att) {
					
					echo $n_id . '=>' . $a_i .'(' . $this->attr_config[$a_i] .')' . PHP_EOL;
					
					$infraOtherDataAttributeNodeType = new InfraOtherDataAttributeNodeType();
					$infraOtherDataAttributeNodeType->infra_other_data_attribute_id = $a_i;
					$infraOtherDataAttributeNodeType->node_type_id = $n_id;
					$infraOtherDataAttributeNodeType->infra_other_data_attribute_node_type_order = $this->attr_config[$a_i];
		
					$infraOtherDataAttributeNodeType->save();
					
				}
				
				echo PHP_EOL;
				
			}
			
		}
		
		function buildFicha2 () {
			
			echo '<pre>';
			
			foreach(array_fill(4, 19, true) as $n_id => $nod ) {
	
				$infraConfiguration = new InfraConfiguration();

				$infraConfiguration->node_type_id = $n_id;
				$infraConfiguration->infra_attribute = 'infra_info_usable_area';
				$infraConfiguration->infra_configuration_order = 0;

				$infraConfiguration->save();
				
				echo PHP_EOL;
				
			}
			
		}
		
		function fixPlano () {
			
			echo '<pre>';
			
			$nodes = $this->db->query("SELECT DISTINCT node_id, plan_category_id FROM plan");
			
			foreach ($nodes->result() as $node) {
				
				$plan = $this->db->query("select * from plan where node_id = " . $node->node_id . " and plan_category_id = " . $node->plan_category_id . " order by plan_datetime desc limit 1")->row();
				
				$this->db->query("update plan set plan_current_version = 1 where plan_id = " . $plan->plan_id);
				
				//select * from plan where node_id = 1 order by plan_datetime desc limit 1
				
				echo $plan->plan_id . ' -> ' . $plan->plan_filename . PHP_EOL;
				
			}
			
		}
		
		function bplan () {
			
			// open this directory 
			$myDirectory = opendir("back_plan/");
			
			// get each entry
			while($entryName = readdir($myDirectory)) {
				$dirArray[] = $entryName;
			}
			
			// close directory
			closedir($myDirectory);
			
			//	count elements in array
			$indexCount	= count($dirArray);
			
			// sort 'em
			sort($dirArray);
			
			for($index=0; $index < $indexCount; $index++) {
			        if (substr("$dirArray[$index]", 0, 1) != "." && strpos($dirArray[$index], '.') === false){ // don't list hidden files
			
					$this->planmig($dirArray[$index]);
			
			//		exit();
				}
			}
			
		}
		
		function planmig ( $plan ) {
			
			$this->load->helper('file');
			
			echo '<pre>';
			
			$xml = simplexml_load_file('back_plan/' . $plan);
			$buf = array();
			
			foreach ($xml->g as $g) {
				
				$id = "" . $g->attributes()->id . "";

				if ($g->attributes()->id != 'copy') {
					
					$buf[$id] = array();
					
					foreach ($g->path as $path) {

						$buf[$id][] = "<path id='" . $path->attributes()->id . "' d='" . $path->attributes()->d . "' stroke='" . $path->attributes()->stroke . "' " .
									  "stroke-width='" . $path['stroke-width'] . "' fill='" . $path->attributes()->fill . "'></path>\n";
						
					}
					
				}
				
			}
			
	 		$buffer = array();
	 		foreach ($buf as $layer => $section) {
				$buffer[] = "<g id='" . $layer . "'>\n" . implode("  ", $section) . "</g>\n";
			}
			 
	 		$svg = $this->load->view('svg/svgbase', array('svg_tags' => implode("", $buffer)), true);
	 		
	 		write_file($this->config->config['plan_dir'] . $plan . '.svg', $svg);
			
		}
		
		function docs () {
			
			$nodes = $this->db->query("SELECT * FROM doc_migracion2");
			
			foreach ($nodes->result() as $node) {
				
				rename($this->config->item('doc_dir') . $node->doc_id, $this->config->item('doc_dir') . $node->doc_id . '.' . $node->extension_name);
					
			}
			
			
		}
		
		function bech () {
			
			echo '<pre>';
			
			$statement = Doctrine_Manager::getInstance()->connection();
			
			$node = Doctrine_Core :: getTable ( 'Node' )->find (1073);
			
	        $q = Doctrine_Query :: create ()
	                ->from ( 'Node n' )
	                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
	                ->where ( 'n.lft >= ?' , $node->lft )
	                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
	                ->andWhere ( 'n.node_type_id = ?' , 4 );
	
	        $results = $q->execute ();
	        
	        foreach ($results as $result) {
	        	
	        	$q2 = $this->db->query("SELECT * FROM node JOIN doc_migracion ON doc_migracion.node_id = node.node_id WHERE node_parent_id = " . $result->node_parent_id . " AND lft >= " . $result->lft . " AND rgt <= " . $result->rgt);
	        	
				foreach ($q2->result() as $doc) {
					
			        $statement->execute('UPDATE doc_migracion SET node_id = ' . $result->node_id . ', flag = 1 WHERE doc_id = ' . $doc->doc_id);
					
					echo $result->node_name . ' - ' . $doc->upload_filename . PHP_EOL;
						
				}
	        	
	        }
			
		}
		
		
		function bech2 () {
			
			$statement = Doctrine_Manager::getInstance()->connection();
			
			$q1 = $this->db->query("SELECT * FROM doc_migracion2");
	
	        foreach ($q1->result() as $doc) {
	        	
	            $document = new DocDocument();
	            $document->node_id = $doc->node_id;
	            $document->doc_category_id = $doc->subject_id;
	            $document->doc_extension_id = $doc->extension_id;
	            $document->doc_document_description = 'MIGRACION DOCUMENTOS GENERALES';
	            $document->doc_document_filename = $doc->upload_filename . '.' . $doc->extension_name;
	            $document->doc_current_version_id = NULL;
	            $document->save();
	            
                $version = new DocVersion();
                $version->doc_version_code = 1;
                $version->doc_version_code_client = 'M';
                $version->doc_version_filename = $doc->doc_id . '.' . $doc->extension_name;
                $version->doc_version_comments = $doc->description;
                $version->doc_version_expiration = NULL;
                $version->doc_version_keyword = $doc->tags;
                $version->doc_version_alert = NULL;
                $version->doc_version_alert_email = NULL;
                $version->doc_version_notification_email = NULL;
                $version->user_id = 99;
                $version->doc_document_id = $document->doc_document_id;
                $version->save();
                
                $document->doc_current_version_id = $version->doc_version_id;
                $document->save();
                
	        }
			
		}
		
		
	}
