<?PHP
require 'client.php';	
//ESTE ES POR DEFECTO




if ($config['client'] == "bech"){
 $config['doc_gui_files'] = array(
		'/application/doc/base.js',
		'/application/doc/store.js', 
                '/application/doc/interface_bech.js'
	);   
}else{
    
  $config['doc_gui_files'] = array(
		'/application/doc/base.js',
		'/application/doc/store.js',
 		'/application/doc/interface.js' 
              
	);  
}

	
	$config['doc_alert_interval'] = 2592000;
	$config['doc_alert_after_expiration'] = FALSE;
        
        $config['doc_image_web'] = array('GIF','PNG','JPEG','JPG');
	
	$config['doc_gui_confgs'] = array(
		'addDocZip' 	=> 'index.php/doc/docdocumentcontroller/addToZip/'
	);
