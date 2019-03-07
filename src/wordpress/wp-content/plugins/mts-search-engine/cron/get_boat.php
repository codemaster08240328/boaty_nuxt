<?php
#exit;
$language = isset($_REQUEST['mts_language']) ? $_REQUEST['mts_language'] : ( isset($_SESSION['mts_language']) ? $_SESSION['mts_language'] : 0 ) ;
defined('SEDNA_CFG') ? true : define('SEDNA_CFG',json_encode(array(
																	'broker_id'=>'wxft6043',
																	'language'=> $language
																	)));

if(isset($_GET['path'])){ die( __FILE__ ); }


include( dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php' );


/* ======================== this file will update all boats and insert new ones ===============  */

/* settings */
$limit = 50;

$older_than_nr = "1";
$older_than_period = "MONTH";
#$older_than_period = "WEEK";
#$older_than_period = "DAY";
#$older_than_period = "SECOND"; # used for dev

$sedna_cfg 	= json_decode(SEDNA_CFG,1);
$broker_id 	= $sedna_cfg['broker_id'];
$language      = $sedna_cfg['language'];

/* process functions and classes */
$h= new Helper;

function update_boat( $response, $info, $request ){
	global $h;
	global $conn;
	
	switch ($request->api) {
		case 'Sedna':
			
			$vals = $request->app->process_boat_details(  $response );
			
			unset( $vals['id_boat'] );
			
			$sql = " UPDATE mts_sedna_boats SET ";
			foreach( $vals as $key => $value ){
				$val = is_array($value) ? json_encode($value) : $value;
				$sql.= "{$key} = '{$val}', ";	
			}
			$id_boat = strtolower( $request->api."-".$request->id_boat );
			$sql.= " lu = NOW() WHERE id_boat = '{$id_boat}' ";
			#print_r($vals);
			#echo $sql."\n\n";
			$conn->Execute( $sql );
			break;
	}
	
}

																																   /* new inserted boats */		         /* boats added $older_than_nr $older_than_period ago (eg 2 WEEKS) */
$select_boats = " SELECT id_boat FROM mts_sedna_boats WHERE lu = '0000-00-00 00:00:00' OR lu < DATE_SUB( NOW(), INTERVAL {$older_than_nr} {$older_than_period} ) LIMIT {$limit}  ";

#echo $select_boats;

$boats_res = $conn->Execute( $select_boats );

if( $boats_res && $boats_res->_numOfRows > 0  ){
	
	$boats_results = $boats_res->GetArray();
	print_r($boats_results);
	
	$rolling_curl = new RollingCurl('update_boat');
	
	foreach( $boats_results as $index => $boat ){
		
		$boat_arr = explode('-', $boat['id_boat']);
		
		$api = ucfirst($boat_arr[0]);
		$id_boat = $boat_arr[1];
		
		if( !class_exists($api) ){
			$file = WEB_ROOT.DS.'apis'.DS.strtolower($api).'.php';
			if( file_exists($file) ){ require_once( $file ); }	
		}
		
		if( class_exists($api) ){
			/* start updating/insert api */
			$app = new $api;
			#print_r($app);
			
			switch ($api) {
				case 'Sedna':
					/* for sedna boats only */
					$src = "http://client.sednasystem.com/API/getBoat.asp?id_boat={$id_boat}&refagt={$broker_id}";
					$request = new RollingCurlRequest($src);
					$request->id_boat = $id_boat;
					$request->api = $api;
					$request->app = $app;
		    		$rolling_curl->add($request);
					break;
			}
			
		}
		
	}
	
	$rolling_curl->execute();
	
	
}else{ echo "No boats to update"; }

?>