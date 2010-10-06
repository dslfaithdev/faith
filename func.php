<?php
/******************************************************************************
 *
 * Filename: func.php
 * Purpose: Holds all of the functions that are commonly used by more than one
 *          page. ex: setting up the database connection with mysqlSetup(&db)
 *
 *****************************************************************************/

require_once 'vars.php';
//require_once 'facebook.php';

/******************************************************************************
 *
 * Purpose: Sets up the database connection.
 * Takes: &db: the variable will modified to be the database connection.
 * Returns: none
 *
 *****************************************************************************/
function mysqlSetup(&$db) {
  global $dbHost, $dbUsername, $dbPassword, $dbName;
  if($dbPassword==""){
    $db = mysql_connect($dbHost, $dbUsername);
  } else {
    $db = mysql_connect($dbHost, $dbUsername, $dbPassword);
  }
  mysql_select_db($dbName,$db); //Specify our database (trantho)
}

function display_header_links($div_counter, $user_id)
{
	$pending_request_str = '<img style="border-style: none" title="You have no pending friend request."
								 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add.jpg" />';
	
	if($div_counter > 0 && $div_counter < 10)
	{
		$pending_request_str = '<img style="border-style: none" title="You have '.$div_counter.' pending friend requests!"
									 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add_alert_'.$div_counter.'.jpg" />';
	}
	else if($div_counter >= 10)
	{
		$pending_request_str = '<img style="border-style: none" title="You have '.$div_counter.' pending friend requests!"
									 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add_alert_10.jpg" />';
	}
	
	echo 
	'<table class="FAITHDescriptionContent" cellspacing="5" cellpadding="0" width="750px">
    <tr bgcolor="#3b5998">
        <td>
        <table cellspacing="0" cellpadding="0" class="FAITHHeaderTitle">
        <tr>
        	<td class="FAITHHeaderTitle" width="150px">DSL FAITH</td>
        	<td width="90px" class="AddImage">
        	<a href="http://apps.facebook.com/dsl_faith/set_policy_transform_pending_request.php">
        	'.$pending_request_str.'</a></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="60px">through</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="120px"><a style="color:#dd3c10;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith/">Facebook FBML</a>,</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="120px"><a target="_parent" style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith_iframe/">Facebook iframe</a></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="20px">or</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="125px"><a style="color:white;text-decoration: underline;" href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/">Facebook Connect</a></td>
        	<td width="15px"></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="70px"><a style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith/index.php?ffile=index.php&fpro=32">Calendar</a></td>
        </tr>
        </table>
        </td>
    </tr>
    <tr>
    	<td>
    	<table cellspacing="0" cellpadding="0">
    	<tr>
  			<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a href="http://apps.facebook.com/dsl_faith/select_app.php">Search Apps</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a href="http://apps.facebook.com/dsl_faith/set_policy.php">Access Rules</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a href="http://apps.facebook.com/dsl_faith/view_history_url.php">Access Logs</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a href="http://apps.facebook.com/dsl_faith/register_app.php">Register Apps</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;" width="130px">
    		<a href="#" onclick="do_get_faith_setting_ajax('."'".$user_id."',1".');">
			Settings
			</a>
    		</td>
    		<td width="20px"><img style="display:none;" id="setting_loading_img" src="http://node0.DSL-FAITH.DSL.emulab.net/faith/image/setting-ajax-loader.gif" /></td>
    	</tr>
    	</table>
    	</td>
    </tr>
    </table>
    <div id="faith_main_setting_div">
    </div>
    <script type="text/javascript">
	<!--
	function do_get_faith_setting_ajax(uid, val) {
	
		'."document.getElementById('setting_loading_img').setStyle('display', 'inline');".'
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.ondone = function(data) '."
					  {
					    document.getElementById('setting_loading_img').setStyle('display', 'none');
						document.getElementById('faith_main_setting_div').setInnerFBML(data);
						document.getElementById('faith_main_setting_div').setClassName('SettingTable');
					  }".'
	
		var params={"uid":uid,"option":val};
		'."ajax.post('http://cyrus.cs.ucdavis.edu/~dslfaith/faith/setting_live_search.php?t='+val,params); 
		}".'
	function do_change_faith_setting_ajax(uid, option, val) {
	
		'."document.getElementById('setting_loading_img').setStyle('display', 'inline');".'
		var ajax = new Ajax();
		ajax.responseType = Ajax.FBML;
		ajax.ondone = function(data) '."
					  {
					    document.getElementById('setting_loading_img').setStyle('display', 'none');
						document.getElementById('faith_main_setting_div').setInnerFBML(data);
						document.getElementById('faith_main_setting_div').setClassName('SettingTable');
					  }".'
	
		var params={"uid":uid,"option":option,"value":val};
		'."ajax.post('http://cyrus.cs.ucdavis.edu/~dslfaith/faith/setting_live_search.php?t='+val,params); 
		} 
	function do_clsoe_get_faith_setting() {
	    document.getElementById('faith_main_setting_div').setValue('');
		document.getElementById('faith_main_setting_div').setClassName('SettingTableClear');
		} 
	//-->
	</script>";
}

function display_header_links_fbc($div_counter, $user_id)
{
	$pending_request_str = '<img style="border-style: none" title="You have no pending friend request."
								 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add.jpg" />';
	
	if($div_counter > 0 && $div_counter < 10)
	{
		$pending_request_str = '<img style="border-style: none" title="You have '.$div_counter.' pending friend requests!"
									 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add_alert_'.$div_counter.'.jpg" />';
	}
	else if($div_counter >= 10)
	{
		$pending_request_str = '<img style="border-style: none" title="You have '.$div_counter.' pending friend requests!"
									 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add_alert_10.jpg" />';
	}
	
	echo
	'
	<table class="FAITHDescriptionContent" cellspacing="5" cellpadding="0" width="750px">
    <tr bgcolor="#3b5998">
        <td>
        <table cellspacing="0" cellpadding="0" class="FAITHHeaderTitle">
        <tr>
        	<td class="FAITHHeaderTitle" width="150px">DSL FAITH</td>
        	<td width="90px" style="line-height: 0px;font-size: 0pt;padding-top: 5px;padding-bottom: 5px;">
        	<a style="background-color: #3b5998;color: #3b5998;" href="http://apps.facebook.com/dsl_faith/set_policy_transform_pending_request.php">
        	'.$pending_request_str.'</a></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="60px">through</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="120px"><a style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith/">Facebook FBML</a>,</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="120px"><a target="_parent" style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith_iframe/">Facebook iframe</a></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="20px">or</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="125px"><a style="color:#dd3c10;text-decoration: underline;" href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/">Facebook Connect</a></td>
        	<td width="15px"></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="70px"><a style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith/index.php?ffile=index.php&fpro=32">Calendar</a></td>
        </tr>
        </table>
        </td>
    </tr>
    <tr>
    	<td>
    	<table cellspacing="0" cellpadding="0">
    	<tr>
  			<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/select_app.php">Search Apps</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/set_policy.php">Access Rules</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/view_history_url.php">Access Logs</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/register_app.php">Register Apps</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;" width="150px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/">
			Settings (FBML Only)
			</a>
    		</td>
    	</tr>
    	</table>
    	</td>
    </tr>
    </table>';
}

function display_header_links_if($div_counter, $user_id)
{
	$pending_request_str = '<img style="border-style: none" title="You have no pending friend request."
								 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add.jpg" />';
	
	if($div_counter > 0 && $div_counter < 10)
	{
		$pending_request_str = '<img style="border-style: none" title="You have '.$div_counter.' pending friend requests!"
									 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add_alert_'.$div_counter.'.jpg" />';
	}
	else if($div_counter >= 10)
	{
		$pending_request_str = '<img style="border-style: none" title="You have '.$div_counter.' pending friend requests!"
									 src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/facebook_add_alert_10.jpg" />';
	}
	
	echo
	'
	<table class="FAITHDescriptionContent" cellspacing="5" cellpadding="0" width="750px">
    <tr bgcolor="#3b5998">
        <td>
        <table cellspacing="0" cellpadding="0" class="FAITHHeaderTitle">
        <tr>
        	<td class="FAITHHeaderTitle" width="150px">DSL FAITH</td>
        	<td width="90px" style="line-height: 0px;font-size: 0pt;padding-top: 5px;padding-bottom: 5px;">
        	<a target="_parent" style="background-color: #3b5998;color: #3b5998;" href="http://apps.facebook.com/dsl_faith/set_policy_transform_pending_request.php">
        	'.$pending_request_str.'</a></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="60px">through</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="120px"><a target="_parent" style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith/">Facebook FBML</a>,</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="120px"><a target="_parent" style="color:#dd3c10;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith_iframe/">Facebook iframe</a></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="20px">or</td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="125px"><a target="_parent" style="color:white;text-decoration: underline;" href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/fbc/">Facebook Connect</a></td>
        	<td width="15px"></td>
        	<td style="font-family: Verdana, Arial;font-size: 8pt;color: #FFFFFF;text-align: left;" width="70px"><a target="_parent" style="color:white;text-decoration: underline;" href="http://apps.facebook.com/dsl_faith/index.php?ffile=index.php&fpro=32">Calendar</a></td>
        </tr>
        </table>
        </td>
    </tr>
    <tr>
    	<td>
    	<table cellspacing="0" cellpadding="0">
    	<tr>
  			<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith_iframe/select_app.php">Search Apps</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/set_policy.php">Access Rules</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/view_history_url.php">Access Logs</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;border-right: #AAAAAA 1px solid;" width="149px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/register_app.php">Register Apps</a>
    		</td>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 13px;text-align: center;" width="130px">
    		<a target="_parent" href="http://apps.facebook.com/dsl_faith/">
			Settings (FBML Only)
			</a>
    		</td>
    	</tr>
    	</table>
    	</td>
    </tr>
    </table>';
}
