<?php

//Setting up the client, specifying the server.
$dsl_soap_client = new SoapClient(null, 
                                array('location' => "http://cyrus.cs.ucdavis.edu/~dslfaith/faith/soap_replay.php",
                                      'uri'      => "urn://cyrus.cs.ucdavis.edu/dslfaith",
                                      'trace'    => 1,
                                      'exception'=> 1));

function dsl_isPositiveInt($num){
  return is_int($num) && ($num>0);
}

//UID to Name
function dsl_uidToName($uid, $logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return "";

  return $dsl_soap_client->__soapCall("uidToName",array($uid, $logID, $faith_uid, $faith_app_id));
}

//Name to UID
function dsl_nameToUid($name, $logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  return $dsl_soap_client->__soapCall("nameToUid",array($name, $logID, $faith_uid, $faith_app_id));
}

//Note that findSocialPath returns an array of (nodes,trust) pairs connecting the source user to the destination.
function dsl_findSocialPath($src,$dest,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($src))
    return -1;
  if(!dsl_isPositiveInt($dest))
    return -2;

  return $dsl_soap_client->__soapCall("findSocialPath",array($src,$dest, $logID, $faith_uid, $faith_app_id));
}

//Like findSocialPath but returns an array of paths.
function dsl_findMultipleSocialPaths($src,$dest,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($src))
    return -1;
  if(!dsl_isPositiveInt($dest))
    return -2;

  return $dsl_soap_client->__soapCall("findMultipleSocialPaths",array($src,$dest, $logID, $faith_uid, $faith_app_id));
}

//Note that findTargets (a) requires an array of keywords for input, even if it is only one keyword, and (b) returns an array of (nodes,distance) pairs.
function dsl_findTargets($src,$keywords,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($src))
    return -1;
  if(!is_array($keywords))
    return -2;

  return $dsl_soap_client->__soapCall("findTargets",array("3200156",$keywords, $logID, $faith_uid, $faith_app_id));
}

//Note that setOutcome (a) requires an array of nodes along the path for input and (b) returns "1" always.
function dsl_setOutcome($path, $outcome, $logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!is_array($path))
    return -1;
  if($outcome!=0 && $outcome!=1)
    return -2;

  return $dsl_soap_client->__soapCall("setOutcome",array($path,$outcome, $logID, $faith_uid, $faith_app_id));
}

//Note that getReceivedKeywords returns a complicated array of 
//(keywords, neighbors) pairs where neighbors is an array of neighbors to the 
//UID that passed the keyword on to the user.
function dsl_getReceivedKeywords($uid,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return array();

  return $dsl_soap_client->__soapCall("getReceivedKeywords",array($uid, $logID, $faith_uid, $faith_app_id));
}

//Note that getFriends returns an array. Returns all friends who are also using DSL.
function dsl_getFriends($uid,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return array();

  return $dsl_soap_client->__soapCall("getFriends",array($uid, $logID, $faith_uid, $faith_app_id));
}

//How much does $truster trust $trustee?
function dsl_getTrust($truster, $trustee, $logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($truster))
    return -1;
  if(!dsl_isPositiveInt($trustee))
    return -2;

  return $dsl_soap_client->__soapCall("getTrust",array($truster,$trustee, $logID, $faith_uid, $faith_app_id));
}

//Note that this function will update the trust along the path depending on if the message was successfully sent or not. Be careful when calling this method as it modifies data. Be sure you don't accidentally spam this function.
function dsl_sendMessage($path,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!is_array($path))
    return -1;

  return $dsl_soap_client->__soapCall("sendMessage",array($path, $logID, $faith_uid, $faith_app_id));
}

//Adds a user to DSL.
function dsl_addUser($uid,$name,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return -1;
  
  return $dsl_soap_client->__soapCall("addUser",array($uid,$name, $logID, $faith_uid, $faith_app_id));
}

//Removes a user from DSL.
function dsl_removeUser($uid,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return -1;

  return $dsl_soap_client->__soapCall("removeUser",array($uid, $logID, $faith_uid, $faith_app_id));
}

//Get the URL for the user's thumbnail picture.
function dsl_getPic($uid,$logID, $faith_uid, $faith_app_id){
  global $dsl_soap_client;

  if(!dsl_isPositiveInt($uid))
    return -1;
  
  return $dsl_soap_client->__soapCall("getPic",array($uid, $logID, $faith_uid, $faith_app_id));
}

