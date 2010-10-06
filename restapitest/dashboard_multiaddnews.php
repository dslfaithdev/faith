
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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/dashboard_multiaddnews.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$uids = array('1217497564');
		$news = array(array('message' => '(Testing) dashboard_multiAddNews DSL FAITH!',
                    		'action_link' => array('text' => 'more details.',
                                           		   'href' => 'http://apps.facebook.com/dsl_faith/')));
		$result = $facebook->api_client->dashboard_multiAddNews($uids, $news);
		echo "<br>news = array(array('message' => '(Testing) dashboard_multiAddNews DSL FAITH!',
                    		'action_link' => array('text' => 'more details.',
                                           		   'href' => 'http://apps.facebook.com/dsl_faith/'))); <br>";
		echo "<br>facebook->api_client->dashboard_multiAddNews(uids, news) REST API CALLED <br>";
		
		foreach ($result as $key => $value) //*FAITH*
	    {
	    	echo "Outer Key: $key; Outer Value: $value<br>";
	    	
	    	foreach ($value as $key => $innervalue) //*FAITH*
		    {
		    	echo "Inner Key: $key; Inner Value: $innervalue<br>";
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