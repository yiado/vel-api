<?php

	 class RowNode {
	
		function __construct ( $node_id, $node_name, $iconCls, $node_type_name, $node_type_category_name, $node_root=null) {
	
			$this->node_id = $node_id;
			$this->node_name = $node_name;
			$this->icon =  $iconCls;
			$this->node_type_name = $node_type_name;
			$this->node_type_category_name = $node_type_category_name;
			$this->node_root = $node_root;
			
		}
		
	}
	
	class RowNodes {
	
		protected $nodes = array ();
		public $icon_dir = null;
		
		function __construct () {
			
			$CI = & get_instance();
			$this->icon_dir = $CI->config->item('node_icon_url');
			
		}
		
	
		function add ( $node_id, $node_name, $iconCls, $node_type_name, $node_type_category_name, $node_root=null ) {
	
			$n = new RowNode($node_id, $node_name, $this->icon_dir . $iconCls . '.gif', $node_type_name, $node_type_category_name, $node_root);
	
			$this->nodes[] = $n;
	
		}
		
		function count () {
			
			return count($this->nodes);
			
		}
	
		function toJson() {
			
			$CI =& get_instance();
			
			return $CI->json->encode($this->nodes);
			
		}
		
	}
	