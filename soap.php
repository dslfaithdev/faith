<?php

require_once 'func.php';

//Setting up the client, specifying the server.
$dsl_soap_client = new SoapClient(null, 
                                array('location' => "http://cyrus.cs.ucdavis.edu/~dslfaith/php/soap.php", //"http://cyrus.cs.ucdavis.edu/~trantho/php/soap.php",  testing
                                      'uri'      => "urn://cyrus.cs.ucdavis.edu/req",
                                      'trace'    => 1,
                                      'exception'=> 1));

$failt_srv = new SoapServer(null, array('uri' => "urn://cyrus.cs.ucdavis.edu/dslfaith"));      

function faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, $api_method)
{
	if(!isset($faith_uid))
		return false;

	if(!isset($faith_client_ip))
		return false;
	
	if(!isset($faith_app_id))
		return false;
		
	if(!isset($api_method))
		return false;
		
	mysqlSetup($db);
	
    $app_ip_addr = $_SERVER['REMOTE_ADDR'];
	
	$api_blocked = mysql_query("SELECT Count(*) as CountAPI_Num
						   		from user_disable_api, restapi
						   		where user_disable_api.uid = $faith_uid AND
						   		 	  restapi.facebook_method = '$api_method' AND
						   			  user_disable_api.restapi_id = restapi.id;", $db);
	
	$api_blocked_row = mysql_fetch_array($api_blocked);
	$CountAPI_Num = $api_blocked_row['CountAPI_Num'];
	
	$app_blocked = mysql_query("SELECT Count(*) as CountAPP_Num
						   		from user_disable_app
						   		where user_disable_app.uid = $faith_uid AND
	            					  user_disable_app.app_id = $faith_app_id;", $db);
	
	$app_blocked_row = mysql_fetch_array($app_blocked);
	$CountAPP_Num = $app_blocked_row['CountAPP_Num'];
	
	$Count_Num = $CountAPI_Num + $CountAPP_Num;
	
	fwrite($fh, "(restserver.php)Count_Num = $Count_Num\n");
	
	$allowed = '1';
	if($Count_Num > 0)
	{
		$allowed = '0';
	}
	
	$query = sprintf("INSERT INTO access_log (uid, 
											  api_id,
											  app_id,
											  allowed,
											  access_time,
											  app_ip_addr,
											  user_ip_addr) 
											  VALUES( 
											  $faith_uid,
											  (SELECT id FROM restapi where facebook_method = '$api_method'),
											  $faith_app_id,
											  $allowed,
											  NOW(),
											  INET_ATON('$app_ip_addr'),
											  INET_ATON('$faith_client_ip'))");
	
											  
											  
	if(!mysql_query($query))
	{
		return false;
	} 
	
	if($Count_Num > 0)
	{
		return false;
	}
	
	return true;
}

function dsl_isPositiveInt($num){
  return is_int($num) && ($num>0);
}
$failt_srv->addFunction("dsl_isPositiveInt");

//UID to Name
function uidToName($uid, $faith_uid, $faith_client_ip, $faith_app_id){

  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "uidToName"))
  	return "";
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return "";
	
  return $dsl_soap_client->__soapCall("uidToName",array($uid));
}
$failt_srv->addFunction("uidToName");

//Name to UID
function nameToUid($name, $faith_uid, $faith_client_ip, $faith_app_id){

  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "nameToUid"))
  	return "";
	
  global $dsl_soap_client;

  return $dsl_soap_client->__soapCall("nameToUid",array($name));
}
$failt_srv->addFunction("nameToUid");

//Note that findSocialPath returns an array of (nodes,trust) pairs connecting the source user to the destination.
function findSocialPath($src,$dest, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "findSocialPath"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($src))
    return -1;
  if(!dsl_isPositiveInt($dest))
    return -2;

  return $dsl_soap_client->__soapCall("findSocialPath",array($src,$dest));
}
$failt_srv->addFunction("findSocialPath");

//Like findSocialPath but returns an array of paths.
function findMultipleSocialPaths($src,$dest, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "findMultipleSocialPaths"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($src))
    return -1;
  if(!dsl_isPositiveInt($dest))
    return -2;

  return $dsl_soap_client->__soapCall("findMultipleSocialPaths",array($src,$dest));
}
$failt_srv->addFunction("findMultipleSocialPaths");

//Note that findTargets (a) requires an array of keywords for input, even if it is only one keyword, and (b) returns an array of (nodes,distance) pairs.
function findTargets($src,$keywords, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "findTargets"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($src))
    return -1;
  if(!is_array($keywords))
    return -2;

  return $dsl_soap_client->__soapCall("findTargets",array("3200156",$keywords));
}
$failt_srv->addFunction("findTargets");

//Note that setOutcome (a) requires an array of nodes along the path for input and (b) returns "1" always.
function setOutcome($path, $outcome, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "setOutcome"))
  	return -1;
	
  global $dsl_soap_client;

  if(!is_array($path))
    return -1;
  if($outcome!=0 && $outcome!=1)
    return -2;

  return $dsl_soap_client->__soapCall("setOutcome",array($path,$outcome));
}
$failt_srv->addFunction("setOutcome");

//Note that getReceivedKeywords returns a complicated array of 
//(keywords, neighbors) pairs where neighbors is an array of neighbors to the 
//UID that passed the keyword on to the user.
function getReceivedKeywords($uid, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "getReceivedKeywords"))
  	return array();
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return array();

  return $dsl_soap_client->__soapCall("getReceivedKeywords",array($uid));
}
$failt_srv->addFunction("getReceivedKeywords");

//Note that getFriends returns an array. Returns all friends who are also using DSL.
function getFriends($uid, $faith_uid, $faith_client_ip, $faith_app_id){
	
   if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "getFriends"))
  	return array();
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return array();

  return $dsl_soap_client->__soapCall("getFriends",array($uid));
}
$failt_srv->addFunction("getFriends");

//How much does $truster trust $trustee?
function getTrust($truster, $trustee, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "getTrust"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($truster))
    return -1;
  if(!dsl_isPositiveInt($trustee))
    return -2;

  return $dsl_soap_client->__soapCall("getTrust",array($truster,$trustee));
}
$failt_srv->addFunction("getTrust");

//Note that this function will update the trust along the path depending on if the message was successfully sent or not. Be careful when calling this method as it modifies data. Be sure you don't accidentally spam this function.
function sendMessage($path, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "sendMessage"))
  	return -1;
	
  global $dsl_soap_client;

  if(!is_array($path))
    return -1;

  return $dsl_soap_client->__soapCall("sendMessage",array($path));
}
$failt_srv->addFunction("sendMessage");

//Adds a user to DSL.
function addUser($uid,$name, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "addUser"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return -1;
  
  return $dsl_soap_client->__soapCall("addUser",array($uid,$name));
}
$failt_srv->addFunction("addUser");

//Removes a user from DSL.
function removeUser($uid, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "removeUser"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return -1;

  return $dsl_soap_client->__soapCall("removeUser",array($uid));
}
$failt_srv->addFunction("removeUser");

//Get the URL for the user's thumbnail picture.
function getPic($uid, $faith_uid, $faith_client_ip, $faith_app_id){
	
  if(!faith_accessAllowed($faith_uid, $faith_client_ip, $faith_app_id, "getPic"))
  	return -1;
	
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return -1;
  
  return $dsl_soap_client->__soapCall("getPic",array($uid));
}
$failt_srv->addFunction("getPic");

$failt_srv->handle(); 