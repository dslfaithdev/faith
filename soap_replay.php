<?php

require_once 'func.php';

//Setting up the client, specifying the server.
$dsl_soap_client = new SoapClient(null, 
                                array('location' => "http://cyrus.cs.ucdavis.edu/~dslfaith/php/soap.php", //"http://cyrus.cs.ucdavis.edu/~trantho/php/soap.php",  testing
                                      'uri'      => "urn://cyrus.cs.ucdavis.edu/req",
                                      'trace'    => 1,
                                      'exception'=> 1));

$failt_srv = new SoapServer(null, array('uri' => "urn://cyrus.cs.ucdavis.edu/dslfaith"));      

function faith_accessAllowed($logID, $faith_uid, $faith_app_id, $api_method, $api_array, $result)
{
	if(!isset($logID))
		return false;
		
	if(!isset($faith_uid))
		return false;

	if(!isset($faith_app_id))
		return false;
		
	if(!isset($api_method))
		return false;
		
	mysqlSetup($db);
	
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
	
	$allowed = '1';
	if($Count_Num > 0)
	{
		$allowed = '0';
	}
	
	$logging_setting = '0';
	
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $faith_uid", $db);
			
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
	}
	
	if($logging_setting == '2' || $logging_setting == '3')
	{
		if(gettype($result) == 'array')
		{
			$array_str = '';
			foreach($result as $index => $value)
			{
				$array_str = $array_str . ' { Array ' . $index . ' -> ';
				
				if(gettype($value) == 'array')
				{
					foreach($value as $inner_index => $inner_value)
					{
						$array_str = $array_str . ' [ Array ' . $inner_index . ' -> ';
						
						if(gettype($inner_value) == 'array')
						{
							foreach($inner_value as $most_inner_index => $most_inner_value)
							{
								$array_str = $array_str . ' ( Array ' . $most_inner_index . ' -> ' . $most_inner_value . ' ) ';
							}
							
							$array_str = $array_str . ' ] ';
						}
						else
						{
							$array_str = $array_str . $inner_value . ' ] ';
						}
					}
				}
				
				$array_str = $array_str . ' } ';
			}
			$result = $array_str;
		}
		$faith_dsl_replay = 44;
		date_default_timezone_set('America/Los_Angeles');
		$time_added = date("Y-m-d H:i:s");
		$query = sprintf("INSERT INTO access_log_replay (allowed,
												  		 access_time,
												  		 logdetails,
												  		 logID) 
												  		 VALUES('%s','%s','%s','%s')",
														 mysql_real_escape_string($allowed),
														 mysql_real_escape_string($time_added),
														 mysql_real_escape_string($result),
														 mysql_real_escape_string($logID));
								  
		if(!mysql_query($query))
		{
			return false;
		} 
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
function uidToName($uid, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'uidToName');
  $api_array['uid'] = $uid;
  			
  $result = $dsl_soap_client->__soapCall("uidToName",array($uid));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "uidToName", $api_array, $result))
  {
  	return "";
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("uidToName");

//Name to UID
function nameToUid($name, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'nameToUid');
  $api_array['name'] = $name;
  
  $result = $dsl_soap_client->__soapCall("nameToUid",array($name));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "nameToUid", $api_array, $result))
  {	
  	return "";
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("nameToUid");

//Note that findSocialPath returns an array of (nodes,trust) pairs connecting the source user to the destination.
function findSocialPath($src,$dest, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'findSocialPath');
  $api_array['src'] = $src;
  $api_array['dest'] = $dest;
  
  $result = $dsl_soap_client->__soapCall("findSocialPath",array($src,$dest));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "findSocialPath", $api_array, $result))
  {
  	return -1;
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("findSocialPath");

//Like findSocialPath but returns an array of paths.
function findMultipleSocialPaths($src,$dest, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'findMultipleSocialPaths');
  $api_array['src'] = $src;
  $api_array['dest'] = $dest;
  
  $result = $dsl_soap_client->__soapCall("findMultipleSocialPaths",array($src,$dest));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "findMultipleSocialPaths", $api_array, $result))
  {
  	return -1;
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("findMultipleSocialPaths");

//Note that findTargets (a) requires an array of keywords for input, even if it is only one keyword, and (b) returns an array of (nodes,distance) pairs.
function findTargets($src,$keywords, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'findTargets');
  $api_array['src'] = $src;
  $api_array['keywords'] = $keywords;
  
  $result = $dsl_soap_client->__soapCall("findTargets",array($src,$keywords)); //array("3200156",$keywords));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "findTargets", $api_array, $result))
  {	
  	return -1;
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("findTargets");

//Note that setOutcome (a) requires an array of nodes along the path for input and (b) returns "1" always.
function setOutcome($path, $outcome, $logID, $faith_uid, $faith_app_id){

  $api_array = array('method' => 'setOutcome');
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "setOutcome", $api_array))
  {
  	return -1;
  }
  
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
function getReceivedKeywords($uid, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'getReceivedKeywords');
  $api_array['uid'] = $uid;
  
  $result = $dsl_soap_client->__soapCall("getReceivedKeywords",array($uid));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "getReceivedKeywords", $api_array, $result))
  {	
  	return array();
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("getReceivedKeywords");

//Note that getFriends returns an array. Returns all friends who are also using DSL.
function getFriends($uid, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'getFriends');
  $api_array['uid'] = $uid;
  
  $result = $dsl_soap_client->__soapCall("getFriends",array($uid));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "getFriends", $api_array, $result))
  {	
  	return array();
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("getFriends");

//How much does $truster trust $trustee?
function getTrust($truster, $trustee, $logID, $faith_uid, $faith_app_id){
	
  global $dsl_soap_client;

  $api_array = array('method' => 'getTrust');
  $api_array['truster'] = $truster;
  $api_array['trustee'] = $trustee;
  
  $result = $dsl_soap_client->__soapCall("getTrust",array($truster,$trustee));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "getTrust", $api_array, $result))
  {	
  	return -1;
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("getTrust");

//Note that this function will update the trust along the path depending on if the message was successfully sent or not. Be careful when calling this method as it modifies data. Be sure you don't accidentally spam this function.
function sendMessage($path, $logID, $faith_uid, $faith_app_id){
	
  $api_array = array('method' => 'sendMessage');
  $api_array['path'] = $path;
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "sendMessage", $api_array))
  	return -1;
	
  global $dsl_soap_client;

  if(!is_array($path))
    return -1;

  return $dsl_soap_client->__soapCall("sendMessage",array($path));
}
$failt_srv->addFunction("sendMessage");

//Adds a user to DSL.
function addUser($uid,$name, $logID, $faith_uid, $faith_app_id){
  
  $api_array = array('method' => 'addUser');
  $api_array['uid'] = $uid;
  $api_array['name'] = $name;
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "addUser", $api_array))
  	return -1;
	
  global $dsl_soap_client;

  return $dsl_soap_client->__soapCall("addUser",array($uid,$name));
}
$failt_srv->addFunction("addUser");

//Removes a user from DSL.
function removeUser($uid, $logID, $faith_uid, $faith_app_id){
 
  $api_array = array('method' => 'removeUser');
  $api_array['uid'] = $uid;
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "removeUser", $api_array))
  	return -1;
	
  global $dsl_soap_client;

  return $dsl_soap_client->__soapCall("removeUser",array($uid));
}
$failt_srv->addFunction("removeUser");

//Get the URL for the user's thumbnail picture.
function getPic($uid, $logID, $faith_uid, $faith_app_id){

  global $dsl_soap_client;
  
  $api_array = array('method' => 'getPic');
  $api_array['uid'] = $uid;
  
  $result = $dsl_soap_client->__soapCall("getPic",array($uid));
  
  if(!faith_accessAllowed($logID, $faith_uid, $faith_app_id, "getPic", $api_array, $result))
  {
  	return -1;
  }
  else
  {
  	return $result;
  }
}
$failt_srv->addFunction("getPic");

$failt_srv->handle(); 