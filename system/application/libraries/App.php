<?php

	class App {

		var $CI;

		function App () {

			$this->CI =& get_instance();

		}

		function getTempFileDir ( $file ) {

			return $this->CI->config->item('temp_dir') . $file;

		}

		function getTempFilePath ( $file ) {

			return 'temp/' . $file;

		}

		function getFileExtension ( $filename ) {

			return strtolower(end(explode(".", $filename)));

		}

		function getFileName ( $filename ) {

			$array_name = explode(".", $filename);
			$extension = array_pop($array_name);
			$real_name = implode(".", $array_name);
			return $real_name;

		}

		function searchNodeBranch ( $node_id=null, $ancestors=null ) {

			$xml = simplexml_load_file($this->CI->auth->get_user_data('xml_permissions_file'));
			$aux = array();

			if (is_null($node_id)) {
				$children = $xml->children();
			} else {
				$children = $this->searchNodeBranchIterate($node_id, $ancestors, $xml);
			}

			foreach($children as $node) {

				array_push($aux, Doctrine_Core::getTable('Node')->find($node['id']));

			}

			return $aux;

		}

		function searchNodeBranchIterate ( $node_id, $ancestors, $xml ) {

			foreach ($xml->children() as $node) {

				if ($node['id'] == $node_id) {

					log_message('debug', 'Node search: ' . $node['id'] . '==' . $node_id);
					return $node->children();
				}

				if (in_array($node['id'], $ancestors)) {
					return $this->searchNodeBranchIterate($node_id, $ancestors, $node);
				}

			}

		}


	    /**
	     *
	     * Genera un numero de folio con la cantidad de ceros a la izquierda definidos por el usuario
	     * @param integer $number
	     * @param integer $number_zeros
	     * @return string $string_folio
	     */
	    function generateFolio($number, $number_zeros = 11) {

	        $string_folio = str_pad($number, $number_zeros, '0', STR_PAD_LEFT);
	        return $string_folio;
	    }

	}

	function loadListeners ( &$record, $listener ) {

		$CI =& get_instance();
		$onfig = $CI->config->item('listener_config');

		foreach ($onfig[$listener] as $class) {

			$record->addListener(new $class());

		}

	}

	function DoctrineObjectToArray( $iteration, $attribute='node_id' ) {

		$aux = array();

		foreach ($iteration as $ob) {

			array_push($aux, $ob[$attribute]);

		}

		return $aux;

	}