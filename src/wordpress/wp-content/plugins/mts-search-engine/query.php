<?php

# use this file for asynchronous requests.
include('config.php');

$apis = array('Sedna');

if(isset($_REQUEST['api']) && in_array($_REQUEST['api'], $apis)){
	# if session is valid, close it, else end script;
	$h = new Helper;
	
	if($h->isValidSession()){session_write_close();}else{ die('Forbidden![alert 1]'); } # disable session closing for now.
	
	$api_src = WEB_ROOT.DS.'apis'.DS.strtolower($_REQUEST['api']).'.php';
	if(file_exists($api_src)){
		require_once($api_src);
		if(class_exists($_REQUEST['api'])){
			
			$params = new Input();
			
			$api = new $_REQUEST['api'];
			
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'search'){
				/* call to search methods */
				$api->sync_search($params);
			
				$api->process_results();
				
				Output::print_results();
			}
			

			
		}
	}else{ echo "Files does not exist\n".$api_src; }
}

?>