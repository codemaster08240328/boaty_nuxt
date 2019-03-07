<?php
if(session_id() == ''){session_start();}
if(!isset($_SESSION['mts_last_search'])) 
{ $_SESSION['mts_last_search'] = ''; }


$language = isset($_REQUEST['mts_language']) ? $_REQUEST['mts_language'] : ( isset($_SESSION['mts_language']) ? $_SESSION['mts_language'] : 0 ) ;

defined('SEDNA_CFG') ? true : define('SEDNA_CFG',json_encode(array(
																	'broker_id'=>'wxft6043',
																	'language'=> $language
																	)));


if(!class_exists('Sedna')){

	

	if(!class_exists('Helper')){

        include get_home_path().'wp-content/plugins/mts-search-engine/Helper_API.php';

	}	

	class Sedna extends Helper {

		

		

		

		public function sync_search($params){

			/* get call data for search results */

			$this->srh_dest 	= isset($params->FROM) ? $params->FROM : null ;

			$this->DEPART_DD 	= isset($params->FROM_DD) ? $params->FROM_DD : null ;

			$this->DEPART_MM 	= isset($params->FROM_MM) ? $params->FROM_MM : null ;

			$this->DEPART_YYYY 	= isset($params->FROM_YY) ? $params->FROM_YY : null ;

			$this->Nombjour 	= isset($params->DAYS_BETWEEN) ? $params->DAYS_BETWEEN : null ;

			$this->SRH_PAS2 	= isset($params->SRH_PAS2) ? $params->SRH_PAS2 : null ;

			$this->SRH_PAS1 	= isset($params->SRH_PAS1) ? $params->SRH_PAS1 : null ;

			$this->SRH_Type 	= isset($params->BOAT) ? $params->BOAT : null ;

			return $this;

		}

		

		public function process_results(){

			/* query api for results based on user input */

			$query 		= http_build_query( (array) $this );

			$sedna_cfg 	= json_decode(SEDNA_CFG,1);

			$broker_id 	= $sedna_cfg['broker_id'];

			$URL  		= "http://client.sednasystem.com/m3/agt/6043/default.asp?action=search&{$query}";

			#echo $URL;

			

			$this->debug_search = $this->byGET_( $URL );

			

			$result = $this->XMLtoarray( $this->debug_search );

			

			if(isset($result['boat'])){

				if(!isset($result['boat'][0])){

					$this->format_search_results( array($result['boat']) );

				}else{

					$this->format_search_results( $result['boat'] );

				}

			}

			

		}

		

		public function getPricesForNext($nr=8, $boat_id){

			

			$this->prices_ssn = array();

			

			parse_str( $_SESSION['mts_last_search'] , $parsed_Str);

			$dest = $parsed_Str['srh_dest'];

			

			$rolling_curl = new RollingCurl(array($this, "build_prices_ssn"));

			

			$orig = date('d-m-Y');

			

			for($i = 0; $i<=$nr;$i++){

				

				if($i > 1){ $orig = $this->date_2; }

				

				$date_1 = date('d-m-Y', strtotime(" + 1 month ", strtotime($orig)));

				

				$this->date_2 = date('d-m-Y', strtotime(" + 7 day ", strtotime($date_1)));

				

				$dmy = explode('-', $date_1);

				$mts_query = "srh_dest={$dest}&DEPART_DD={$dmy[0]}&DEPART_MM={$dmy[1]}&DEPART_YYYY={$dmy[2]}&nombjour=7&id_boat={$boat_id}";

				$src =  'http://client.sednasystem.com/m3/agt/6043/default.asp?action=search&'.$mts_query ;

				#echo $src."::\n";

				$request = new RollingCurlRequest($src);

				

				$request->darray = array( $date_1, $this->date_2 );

				

	    		$rolling_curl->add($request);

			}

			$rolling_curl->execute();

			ksort($this->prices_ssn);

			return $this->prices_ssn;

		}

		public function build_prices_ssn($response, $info, $request){

			#echo "\n::".$response;

			$data = $this->XMLtoarray( $response  );

			

			if( $data != false && isset($data['boat']) && isset($data['boat']['@attributes']) ){

				

				if(empty($this->getBASE_ID) && !empty( $data['boat']['Arrival_bases']) && !empty( $data['boat']['Departure_bases'] ) ){

					$this->getBASE_ID = array();

					$this->getBASE_ID['departure_bases'] = $data['boat']['Departure_bases'];

					$this->getBASE_ID['arrival_bases'] = $data['boat']['Arrival_bases'];

				}

				#print_r($data);

				

				if(empty($this->getBASE_ID) && !empty( $data['boat']['Arrival_bases']) && empty( $data['boat']['Departure_bases'] )  && !empty( $data['boat']['homeport'] )){

					$this->getBASE_ID = array();

					$this->getBASE_ID['departure_bases'] = $data['boat']['homeport'];

					$this->getBASE_ID['arrival_bases'] = $data['boat']['Arrival_bases'];

				}

				if(empty($this->getBASE_ID) && empty( $data['boat']['Arrival_bases']) && !empty( $data['boat']['Departure_bases'] )  && !empty( $data['boat']['homeport'] )){

					$this->getBASE_ID = array();

					$this->getBASE_ID['departure_bases'] = $data['boat']['Departure_bases'];

					$this->getBASE_ID['arrival_bases'] = $data['boat']['homeport'];

				}

				

				if(empty($this->getBASE_ID) && empty( $data['boat']['Arrival_bases']) && empty( $data['boat']['Departure_bases'] )  && !empty( $data['boat']['homeport'] )){

					$this->getBASE_ID = array();

					$this->getBASE_ID['departure_bases'] = $data['boat']['homeport'];

					$this->getBASE_ID['arrival_bases'] = $data['boat']['homeport'];

				}

				if(empty($this->prices_ssn)){$this->prices_ssn=array();}

				

				#$data['boat']['@attributes']['arr_dates'] = $request->darray;

				$this->prices_ssn[strtotime($request->darray[0]) ] = array('price'=> $data['boat']['@attributes']['newprice']  , 'currency'=>$data['boat']['@attributes']['IsoCurr'], 'start'=>$request->darray[0], 'end'=>$request->darray[1] );

			}else{

				#echo "\n::".$response."::\n";

			}

			

		}

		

		public function format_search_results($arr){

			/* format the results array */

			$boat_IDS = array();

			$json_format = array();

			foreach($arr as $b => $oat){

				$boat_IDS[] = $oat['@attributes']['id_boat'];

				Output::addResultRow( array_change_key_case( $this->cu_($oat),CASE_LOWER) , $oat['@attributes']['id_boat'] );

			}	

			return $boat_IDS;

		}

		

		public function cu_($arr){

			

			if(isset($arr['Departure_bases']) && isset($arr['Departure_bases']['base'] )){

				if(!isset($arr['Departure_bases']['base'][0])){

					$arr['Departure_bases']['base'] = array($arr['Departure_bases']['base'] );

				}

				foreach($arr['Departure_bases']['base'] as $k1 => $v1){

					$arr['@attributes']['Departure_bases'][] = $v1['@attributes'];

				}

			}elseif( !empty($arr['homeport']['Departure_bases']) && !empty($arr['homeport']['Departure_bases']['base'] ) ){

				if(empty($arr['homeport']['Departure_bases']['base'][0])){

					$arr['homeport']['Departure_bases']['base'] = array($arr['homeport']['Departure_bases']['base'] );

				}

				foreach($arr['homeport']['Departure_bases']['base'] as $k1 => $v1){

					$arr['@attributes']['Departure_bases'][] = $v1['@attributes'];

				}

			}

			if(isset($arr['Arrival_bases']) && isset($arr['Arrival_bases']['base'] )){

				if(!isset($arr['Arrival_bases']['base'][0])){

					$arr['Arrival_bases']['base'] = array($arr['Arrival_bases']['base'] );

				}

				foreach($arr['Arrival_bases']['base'] as $k1 => $v1){

					$arr['@attributes']['Arrival_bases'][] = $v1['@attributes'];

				}

			}elseif( !empty($arr['homeport']['Arrival_bases']) && !empty($arr['homeport']['Arrival_bases']['base'] ) ){

				

				if(!isset($arr['homeport']['Arrival_bases']['base'][0])){

					$arr['homeport']['Arrival_bases']['base'] = array($arr['homeport']['Arrival_bases']['base'] );

				}

				foreach($arr['homeport']['Arrival_bases']['base'] as $k1 => $v1){

					$arr['@attributes']['Arrival_bases'][] = $v1['@attributes'];

				}

			}

			if(isset($arr['homeport']) && isset($arr['homeport']['@attributes'])){

				$arr['@attributes']['homeport'] =  $arr['homeport']['@attributes'];

			}

			

			return $arr['@attributes'];

		}

		

		public function requestBoat($array, $current){

			/* request boat details multi cURL */	

			#print_r($array);

			#die( $current );

			if($array[0] == $current){

				/* if called for the first time, init multi curl obj */

				$this->rolling_curl = new RollingCurl(array($this, "build_boat_details"));

				#echo "Start:::\n";

			}

			#echo "ADD::::\n";

			

			$sedna_cfg 	= json_decode(SEDNA_CFG,1);

			$broker_id 	= $sedna_cfg['broker_id'];

			

			$diff = explode('-', $current);

			$current = end( $diff );

			

			

			$src = "http://client.sednasystem.com/API/getBoat.asp?id_boat={$current}&refagt={$broker_id}";

			$request = new RollingCurlRequest($src);

			$request->id_boat = $current;

    		$this->rolling_curl->add($request);

			

			

			if($array[ (count($array) - 1) ] == "sedna-".$current ){

				/* if called for the last time, execute */

				$this->rolling_curl->execute();

				#echo "END::::\n";

			}

			

		}



		public function extract_characteristics($id){

			$sedna_cfg 	= json_decode(SEDNA_CFG,1);

			$broker_id 	= $sedna_cfg['broker_id'];

			$url = "http://client.sednasystem.com/API/GetCharacteristics.asp?id_boat={$id}&refagt={$broker_id}";

			$this->debug_search = $this->byGET_( $url );

			

			$result = $this->XMLtoarray( $this->debug_search );

			

			$ret = false;

			if(!empty($result['characteristic'])){

				$ret = array();

				if(!isset($result['characteristic'][0])){$result['characteristic'] = array($result['characteristic']);}

				foreach($result['characteristic'] as $k=> $v){

					$ret[] = $v['@attributes'];

				}

				

			}

			

			return $ret;

		}

		public function extract_extras($id){

			$sedna_cfg 	= json_decode(SEDNA_CFG,1);

			$broker_id 	= $sedna_cfg['broker_id'];

			$url = "http://client.sednasystem.com/API/getExtras2.asp?id_boat={$id}&refagt={$broker_id}";

			$this->debug_search = $this->byGET_( $url );

			

			$result = $this->XMLtoarray( $this->debug_search );

			$ret = false;

			if(!empty($result['extra'])){

				$ret = array();

				if(!isset($result['extra'][0])){$result['extra'] = array($result['extra']);}

				foreach($result['extra'] as $k=> $v){

					$ret[] = $v['@attributes'];

				}

				

			}

			

			return $ret;

		}

		

		public function build_boat_details($response, $info, $request){

			if(!isset($this->boatsDetails)){ $this->boatsDetails = array(); }

			$details = $this->process_boat_details($response);

			if($details != null){

				$this->boatsDetails[ "sedna-".$request->id_boat ] = $details;

				#echo ":WS WASS:::";

			}

		}

		public function process_boat_details($xml){

			

			/* was the request a success? */

			try{

				$arr = $this->XMLtoarray($xml);

			}catch(Exception $e){

				/* was not  */

				$arr   = null;

				$boat = null;

			}

			

			if(is_array($arr)){

				$boat = $this->cu_($arr);

				

				#die( print_r($arr) );

				

				if( isset($arr['picts']) && isset($arr['picts']['pict'])  ):

				$boat['picts'] = array();

				foreach(  (is_array($arr['picts']['pict']) && isset($arr['picts']['pict'][0]) ? $arr['picts']['pict'] : array($arr['picts']['pict']) ) as $p => $ict ){

					$boat['picts'][] = $ict['@attributes']['link'];

				}

				endif;

				if(isset($arr['plans']) && isset($arr['plans']['plan']) ):

				$boat['plans'] = array();

				foreach(  ( is_array($arr['plans']['plan']) && isset($arr['plans']['plan'][0]) ? $arr['plans']['plan'] : array($arr['plans']['plan']) ) as $p => $lan ){

					$boat['plans'][] = $lan['@attributes']['link'];

				}

				endif;

				

				/* don't use this for boat details, use the search results more detailed info's */

				#$boat['homeport'] = $arr['homeport']['@attributes']['name'];

				#unset($arr['homeport']['@attributes']['name']);

				#$boat = array_merge($boat, $this->cu_($arr['homeport']));

			}

			

			return $boat != null ? array_change_key_case($boat,CASE_LOWER) : $boat ;

		}

	/*  end class  */}

}

?>