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
	$homepage = file_get_contents('http://169.237.6.102/~dslfaith/testapplicationone/restapitest/dashboard_addnews.php/', false, $context);
	echo $homepage;
	
	echo "<br><br>************     DSL FAITH Certified     ************<br><br>";
	
	try
	{
		$news = array(array('message' => '(Testing message) dashboard_addNews from DSL FAITH Facebook Connect!',
                    		'action_link' => array('text' => 'Check it out now.',
                                           		   'href' => 'http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/')));
		$result = $facebook->api_client->dashboard_addNews($news);
		echo "<br>news = array(array('message' => '(Testing message) dashboard_addNews from DSL FAITH Facebook Connect!',
                    		'action_link' => array('text' => 'Check it out now.',
                                           		   'href' => 'http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/'))); <br>";
		echo "<br>facebook->api_client->dashboard_addNews(news) REST API CALLED <br>";
		
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