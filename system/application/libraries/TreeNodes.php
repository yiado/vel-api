<?php

	 class TreeNode {
	
		public $text = "";
		public $id = "";
		public $leaf = true;
		public $allowDrop = false;
		public $href = "#";
		public $expanded = false;
		public $qtip = "";
		public $node_type_name = "";
		public $node_type_category_name = "";
		public $icon = null;
		/**
		 * Si es true, setea a checked el checkbox del nodo
		 * @var boolean $checked_node
		 */
		public $checked_node = false;
		public $children = null;
		
		function __construct ( $id, $text, $icon, $leaf, $expanded, $href, $node_type_name, $node_type_category_name, $checked_node = false, $children=null ) {
	
			$CI = & get_instance();
			$this->icon_dir = $CI->config->item('node_icon_url');
	
			$this->id = $id;
			$this->text = $text;
			$this->icon =  $this->icon_dir . $icon . '.gif';
			$this->leaf = false;
			$this->expanded = $expanded;
			$this->href = $href;
			$this->qtip = $node_type_name;
			$this->node_type_name = $node_type_name;
			$this->node_type_category_name = $node_type_category_name;
			$this->checked_node = $checked_node;

			$this->children = $children;
			
		}
		
	}
	
	class TreeNodes {
	
		protected $nodes = array ();
	
		function add ( $id, $text, $icon, $leaf, $expanded, $href, $node_type_name, $node_type_category_name=null, $user_group_id=null, $children=null ) {
	
			$n = new TreeNode($id, $text, $icon, $leaf, $expanded, $href, $node_type_name, $node_type_category_name, $user_group_id, $children);
	
			$this->nodes[] = $n;
	
		}
	
		function toJson() {
			
			$CI =& get_instance();
			
			return $CI->json->encode($this->nodes);
			
		}
		
	}
	