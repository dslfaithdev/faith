<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/">HOME</a><br />

<?php

require_once '../vars.php';
require_once '../facebook.php';

try
{
	$facebook = new Facebook($appapikey, $appsecret);
	$user_id = $facebook->require_login();
	
	$post_params = array();
	foreach ($_POST as $key => &$val) {
      $post_params[] = $key.'='.urlencode($val);
    }
    
    $post_params[] = 'faith_uid='.urlencode($user_id);
	$post_params[] = 'faith_app_id='.urlencode('5');
    
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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/fql_query.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$uid = $facebook->get_loggedin_user();
		$query = "SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=$uid LIMIT 10)";
		$result = $facebook->api_client->fql_query($query);
		echo "<br>facebook->api_client->fql_query(SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=uid LIMIT 10)) REST API CALLED <br>";
		foreach ($result as $key => $value) //*FAITH*
	    {
	    	echo "Key: $key<br>";
	    	
		    foreach ($value as $innerkey => $innervalue) //*FAITH*
		    {
		    	echo "Inner Key: $innerkey; Inner Value: $innervalue<br>";
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