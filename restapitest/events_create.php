
<a href="http://apps.facebook.com/dsl_faith/">HOME</a><br />
<fb:prompt-permission perms="create_event">
Grant permission for create_event
</fb:prompt-permission>

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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/events_create.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$event_fb = array("name" => "DSL FAITH test", "host" => "FATIH host", "start_time" => "1215929160", "end_time" => "1215929160");
		$result = $facebook->api_client->events_create(json_encode($event_fb));
		echo 'event_fb = array("name" => "DSL FAITH test", "host" => "FATIH host", "start_time" => "1215929160", "end_time" => "1215929160")';
		echo "<br>facebook->api_client->events_cancel('','Test events_cancel from DSL FAITH') REST API CALLED <br>";
		
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