<?php
/******************************************************************************
 *
 * Filename: vars.php
 * Purpose: This fild holds all of the global static variables used by cyrusDSL
 *          such as the API key, the path vs. weight variable, etc.
 *
 *****************************************************************************/
//PHP debug.
// Turn off all error reporting
error_reporting(0);

// Report simple running errors
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// Report all errors except E_NOTICE
// This is the default value set in php.ini
//error_reporting(E_ALL ^ E_NOTICE);

// Report all PHP errors (see changelog)
//error_reporting(E_ALL);


//Facebook API variables
$appapikey = '552820d26044c5326c72dc8c7fbfedfc';	// '552820d26044c5326c72dc8c7fbfedfc'
$appsecret = '40d8dcb1c886996f51954096154c9f11';	// '40d8dcb1c886996f51954096154c9f11'
$iframe_appid = '120596987958636';							// '120596987958636'
$iframe_appapikey = '7c618ec52efbbe484d7f9dd24c746161'; 	// '7c618ec52efbbe484d7f9dd24c746161'
$iframe_appsecret = '18b0533d96d25ad4e23ca8386039b6c4';		// '18b0533d96d25ad4e23ca8386039b6c4'

$faith_fbml = '1';
$faith_fbml_replay = '11';
$faith_connect = '2';
$faith_iframe = '3';
$faith_iframe_replay = '33';
$faith_dsl_replay = '44';

//URLs & Paths
$source_server_url = "http://faith.garm.comlab.bth.se/faith/"; 			// "http://cyrus.cs.ucdavis.edu/~dslfaith/faith/"
$facebook_canvas_page_url = "http://apps.facebook.com/dsl_faith/"; 				// "http://apps.facebook.com/dsl_faith/"
$facebook_iframe_canvas_page_url = "http://apps.facebook.com/dsl_faith_iframe/"; 	// "http://apps.facebook.com/dsl_faith_iframe/"
//$facebook_canvas_page_url = $facebook_iframe_canvas_page_url;
$source_folder_path = "/usr/local/www/vhosts/faith.garm.comlab.bth.se/htdocs/faith/";							// "/home/dslfaith/public_html/faith/"

//MySQL Database Info
$dbHost = "127.0.0.1:3307";
$dbUsername = "leeru";
$dbPassword = "92660341";
$dbName = "leeru";

//FAITH variables
$rowsPerPage = 10;
$callbackurl='http://cyrus.cs.ucdavis.edu/~dslfaith/faith/select_app_live_search.php';
$callbackurl='https://faith.garm.comlab.bth.se/faith/select_app_live_search.php';
// 'http://cyrus.cs.ucdavis.edu/~dslfaith/faith/select_app_live_search.php'
$view_history_callbackurl='http://cyrus.cs.ucdavis.edu/~dslfaith/faith/view_history_live_search.php';
$view_history_callbackurl='https://faith.garm.comlab.bth.se/faith/view_history_live_search.php';
// 'http://cyrus.cs.ucdavis.edu/~dslfaith/faith/view_history_live_search.php'
$set_policy_transform_callbackurl='http://cyrus.cs.ucdavis.edu/~dslfaith/faith/set_policy_transform_live_search.php';
$set_policy_transform_callbackurl='https://faith.garm.comlab.bth.se/faith/set_policy_transform_live_search.php';
// 'http://cyrus.cs.ucdavis.edu/~dslfaith/faith/set_policy_transform_live_search.php'

//Social Graph variables
$listFile = "adjacencylist.txt";
$pathVsTrustWeight = 0.5;
$trustThreshold = 0.4;
