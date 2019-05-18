<?php

# override the default TCPDF config file
if(!defined('K_TCPDF_EXTERNAL_CONFIG')) {	
	define('K_TCPDF_EXTERNAL_CONFIG', TRUE);
}
	
# include TCPDF
//require(APPPATH.'config/tcpdf'.EXT);
require_once(APPPATH.'libraries/jpgraph/jpgraph.php');
require_once(APPPATH.'libraries/jpgraph/jpgraph_pie.php');
require_once(APPPATH.'libraries/jpgraph/jpgraph_pie3d.php');
require_once(APPPATH.'libraries/jpgraph/GoogChart.class.php');
//require_once ('jpgraph/jpgraph.php');
//require_once ('jpgraph/jpgraph_pie.php');

/************************************************************
 * TCPDF - CodeIgniter Integration
 * Library file
 * ----------------------------------------------------------
 * @author Jonathon Hill http://jonathonhill.net
 * @version 1.0
 * @package tcpdf_ci
 ***********************************************************/
class CI_graph  {
	
	
	/**
	 * TCPDF system constants that map to settings in our config file
	 *
	 * @var array
	 * @access private
	 */
//	private $cfg_constant_map = array(
//		'K_PATH_MAIN'	=> 'base_directory',
//		'K_PATH_URL'	=> 'base_url',
//		'K_PATH_FONTS'	=> 'fonts_directory',
//		'K_PATH_CACHE'	=> 'cache_directory',
//		'K_PATH_IMAGES'	=> 'image_directory',
//		'K_BLANK_IMAGE' => 'blank_image',
//		'K_SMALL_RATIO'	=> 'small_font_ratio',
//		'K_CELL_HEIGHT_RATIO' => 'cell_height_ratio'
//	);
	
	
	/**
	 * Settings from our APPPATH/config/tcpdf.php file
	 *
	 * @var array
	 * @access private
	 */
//	private $_config = array();
	
	
	/**
	 * Initialize and configure TCPDF with the settings in our config file
	 *
	 */
	function __construct() {
	


// Create the Pie Graph. 
//$this->graph = new PieGraph(350,250);
//
//$theme_class="DefaultTheme";
//$graph->SetTheme(new $theme_class());


//
//// Create

            
            
            
            
	}
	
	
	
}