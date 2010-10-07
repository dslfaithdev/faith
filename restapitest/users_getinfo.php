<a href="http://apps.facebook.com/dsl_faith/">HOME</a><br />

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
	
    $opts = array(
	  'http'=>array(
	    'method'=>"POST",
	    'header'=>"Accept-language: en\r\n" .
	              "Cookie: foo=bar\r\n",
		'content'=>$postStr /* Session_Ket_For_FAITHuid=user_idpass session key to application server */
	  )
	);
	
	$context = stream_context_create($opts);
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/users_getinfo.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$uid = $facebook->get_loggedin_user();
		$result = $facebook->api_client->users_getInfo($uid, 'last_name, first_name, about_me, activities, affiliations, birthday, birthday_date, books');
		echo "<br>facebook->get_loggedin_user() <br>";
		echo "<br>facebook->api_client->users_getInfo(uid, 'last_name, first_name, about_me, activities, affiliations, birthday, birthday_date, books') REST API CALLED <br>";
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