
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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/users_hasapppermission.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$result = $facebook->api_client->users_hasAppPermission('email');
		echo "<br>facebook->api_client->users_hasAppPermission('email') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('read_stream');
		echo "<br>facebook->api_client->users_hasAppPermission('read_stream') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('publish_stream');
		echo "<br>facebook->api_client->users_hasAppPermission('publish_stream') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('offline_access');
		echo "<br>facebook->api_client->users_hasAppPermission('offline_access') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('status_update');
		echo "<br>facebook->api_client->users_hasAppPermission('status_update') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('photo_upload');
		echo "<br>facebook->api_client->users_hasAppPermission('photo_upload') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('create_event');
		echo "<br>facebook->api_client->users_hasAppPermission('create_event') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('rsvp_event');
		echo "<br>facebook->api_client->users_hasAppPermission('rsvp_event') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('sms');
		echo "<br>facebook->api_client->users_hasAppPermission('sms') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('video_upload');
		echo "<br>facebook->api_client->users_hasAppPermission('video_upload') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('create_note');
		echo "<br>facebook->api_client->users_hasAppPermission('create_note') REST API CALLED <br>";
		
		echo "result: $result<br>";
		
		$result = $facebook->api_client->users_hasAppPermission('share_item');
		echo "<br>facebook->api_client->users_hasAppPermission('share_item') REST API CALLED <br>";
		
		echo "result: $result<br>";
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