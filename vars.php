<?php
/******************************************************************************
 *
 * Filename: vars.php
 * Purpose: This fild holds all of the global static variables used by cyrusDSL
 *          such as the API key, the path vs. weight variable, etc.
 *
 *****************************************************************************/

//Facebook API variables
$appapikey = '552820d26044c5326c72dc8c7fbfedfc';
$appsecret = '40d8dcb1c886996f51954096154c9f11';
$iframe_appid = '120596987958636';
$iframe_appapikey = '7c618ec52efbbe484d7f9dd24c746161';
$iframe_appsecret = '18b0533d96d25ad4e23ca8386039b6c4';

$faith_fbml = '1';
$faith_connect = '2';
$faith_iframe = '3';

//MySQL Database Info
$dbHost = "localhost";
$dbUsername = "leeru";
$dbPassword = "92660341";
$dbName = "leeru";

//FAITH variables
$rowsPerPage = 10;
$callbackurl='http://cyrus.cs.ucdavis.edu/~dslfaith/faith/select_app_live_search.php';
$view_history_callbackurl='http://cyrus.cs.ucdavis.edu/~dslfaith/faith/view_history_live_search.php';
$set_policy_transform_callbackurl='http://cyrus.cs.ucdavis.edu/~dslfaith/faith/set_policy_transform_live_search.php';

//Social Graph variables
$listFile = "adjacencylist.txt";
$pathVsTrustWeight = 0.5;
$trustThreshold = 0.4;

//Test Mercurial by Ray
