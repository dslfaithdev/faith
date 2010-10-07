<?php

try
{
	if(isset($_GET['faithproxyurl']))
	{
		$url = urldecode($_GET['faithproxyurl']);
		
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Accept-language: en\r\n" .
		              "Cookie: \r\n",
			'content'=>'' 
		  )
		);
		
		$context = stream_context_create($opts);
		$proxy_result = file_get_contents($url, false, $context);
		
		echo $proxy_result;
	}
	else
	{
		echo 'error retrieving data!';
	}
}
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>