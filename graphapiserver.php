<?php

require_once 'vars.php';
require_once 'graph/src/facebook.php';
require_once 'func.php';


        $myFile = $source_folder_path."testFile.txt";

        $fh = fopen($myFile, 'a');
        $stringData = "GRAPHserver.php Called!\n";
        fwrite($fh, $stringData);


        $facebook = 'testing facebook var';
        if(isset($_POST['faith_source']) && $_POST['faith_source'] == $faith_iframe)    //*FAITH*
        {
                $facebook = new Facebook(array('appId'  => $iframe_appid,
                                                                           'secret' => $iframe_appsecret,
                                                                           'cookie' => true,));
        }
$facebook->Set_Is_FAITH_GRAPH(true);


    if(!isset($_POST['faith_app_id']) || !isset($_POST['faith_uid']))
    {
        exit("SOMETHING IS NOT SET");
    }

                if(isset($_POST['access_token']))
                {
                        $access_token = $_POST['access_token'];
                        $facebook->Set_Access_Token_For_FAITH($access_token);
                }
mysqlSetup($db);

$path = $_POST['path'];
$method = $_POST['method'];
for($i = 0; $i < sizeof($_POST['params']); $i++)
{
	$params[] = $_POST['params'][$i];
}

$faith_uid = $_POST['faith_uid'];
$faith_app_id = $_POST['faith_app_id'];
$faith_url_id = $_POST['faith_url_id'];
$faith_client_id = $_POST['faith_client_ip'];
$app_ip_addr = $_SERVER['REMOTE_ADDR'];

$result = $facebook->api($path, $method, $params);

print $result;

$allowed = 1;
$time_added = date("Y-m-d H:i:s");

$query = sprintf("INSERT INTO access_log (uid,
					app_id,
					allowed,
					access_time,
					logdetails, 
					url_id,
					api_id,
					app_ip_addr,
					user_ip_addr) 
					VALUES('%s', '%s', '%s', '%s', '%s', '%s', 133, INET_ATON('$app_id_addr'), INET_ATON('$faith_client_ip'))", 
					$faith_uid, 
					mysql_real_escape_string($faith_app_id),
					mysql_real_escape_string($allowed),
					mysql_real_escape_string($time_added),
					mysql_real_escape_string($result),
					mysql_real_escape_string($faith_url_id));

	if(!mysql_query($query))
	{
		fwrite($fh, "graphServer.php Query Failed" . mysql_error() . "\n");
	}

	$fclose($fh);

// */


?>






