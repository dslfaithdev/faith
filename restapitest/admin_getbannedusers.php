
<a href="http://apps.facebook.com/dsl_faith/">HOME</a><br />

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
	
    $opts = array(
	  'http'=>array(
	    'method'=>"POST",
	    'header'=>"Accept-language: en\r\n" .
	              "Cookie: foo=bar\r\n",
		'content'=>$postStr /* Session_Ket_For_FAITHuid=user_idpass session key to application server */
	  )
	);
	
	$context = stream_context_create($opts);
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/admin_getbannedusers.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		//$banArrayID = array('uids'=>'3325339');
		//$banArray = $facebook->api_client->Admin_banUsers($banArrayID);
		//echo "banArray = $banArray;";
		
		$result = $facebook->api_client->admin_getBannedUsers(array('uids'=>'3325339'));
		echo "<br>facebook->api_client->admin_getBannedUsers() REST API CALLED <br>";
		
		
		foreach ($result as $key => $value) //*FAITH*
	    {
	    	echo "Outer Key: $key; Outer Value: $value<br>";
	    }
	    
		$result = $facebook->api_client->admin_getBannedUsers();
		echo "<br>facebook->api_client->admin_getBannedUsers() REST API CALLED <br>";
		
		foreach ($result as $key => $value) //*FAITH*
	    {
	    	echo "Outer Key: $key; Outer Value: $value<br>";
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