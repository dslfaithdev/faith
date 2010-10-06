<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/">HOME</a><br />

<?php

require_once '../vars.php';
require_once '../facebook.php';

try
{
	$facebook = new Facebook($appapikey, $appsecret);
	
	$post_params = array();
	foreach ($_POST as $key => &$val) {
      $post_params[] = $key.'='.urlencode($val);
    }
    $postStr = implode('&', $post_params);
	
    $cookie_params = array();
	foreach ($_COOKIE as $key => &$val) {
      $cookie_params[] = $key.'='.urlencode($val);
    }
    $cookieStr = implode(';', $cookie_params);
    
    $opts = array(
	  'http'=>array(
	    'method'=>"POST",
	    'header'=>"Accept-language: en\r\n" .
	              "Cookie: $cookieStr\r\n",
		'content'=>$postStr /* Session_Ket_For_FAITHuid=user_idpass session key to application server */
	  )
	);
	
	$context = stream_context_create($opts);
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/data_setcookie.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$result = $facebook->api_client->data_setCookie('1217497564', 'test', 'cookie1');
		echo "<br>facebook->api_client->data_setCookie('1217497564', 'test', 'cookie1') REST API CALLED <br>";
		
		echo "result: $result (Returns 1 indicating success or 0 indicating failure)<br>";
	}
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
} 
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}
