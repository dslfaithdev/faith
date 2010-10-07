<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/">HOME</a><br />

<?php

require_once '../vars.php';
require_once '../facebook.php';

try
{
	$facebook = new Facebook($appapikey, $appsecret);
	//$user_id = $facebook->require_login();
	
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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/fql_multiquery.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$uid = $facebook->get_loggedin_user();
		$queries = '{
  		"name":"SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=1217497564 LIMIT 10)",
  		"sex":"SELECT sex FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=1217497564 LIMIT 10)",
  		"first_name":"SELECT first_name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=1217497564 LIMIT 10)",
  		"last_name":"SELECT last_name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=1217497564 LIMIT 10)"}';
		
		$result = $facebook->api_client->fql_multiquery($queries);
		echo "<br>facebook->api_client->fql_multiquery($queries) REST API CALLED <br>";
		
		foreach ($result as $key => $value) //*FAITH*
	    {
	    	echo "Key: $key<br>";
	    	
		    foreach ($value as $inner1key => $inner1value) //*FAITH*
		    {
		    	echo "Inner 1 Key: $inner1key; Inner 1 Value: $inner1value<br>";
		    	
			    foreach ($inner1value as $inner2key => $inner2value) //*FAITH*
			    {
			    	echo "Inner 2 Key: $inner2key; Inner 2 Value: $inner2value<br>";
			    	
				    foreach ($inner2value as $inner3key => $inner3value) //*FAITH*
				    {
				    	echo "Inner 3 Key: $inner3key; Inner 3 Value: $inner3value<br>";
				    }
			    }
		    }
		    echo "<br>";
	    }
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
