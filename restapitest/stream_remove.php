
<a href="http://apps.facebook.com/dsl_faith/">HOME</a><br />
<fb:prompt-permission perms="publish_stream">
Grant permission for publish_stream
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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/stream_remove.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$result = $facebook->api_client->stream_remove('1217497564_106915176015292');
		echo "<br>facebook->api_client->stream_remove('1217497564_106915176015292') REST API CALLED <br>";
		
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