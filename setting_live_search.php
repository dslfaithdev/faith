<?php

require_once 'func.php';

// Disable Logging = no record
// Enable URL Logging Only = 1
// Enable API Logging Only = 2
// Enable Logging = 3

$user_id = $_POST['uid'];
$option = $_POST['option'];

mysqlSetup($db);

$disable_checked = 'checked';
$url_checked = '';
$api_checked = '';
$enable_checked = '';

$privacy_default_checked = '';
$privacy_quality_checked = '';
$privacy_category_checked = '';
$privacy_keywords_checked = '';

if($option == '1')
{
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $user_id", $db);
	
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
		$disable_checked = '';
		
		if($logging_setting == '1')
		{
			$url_checked = 'checked';
		}
		else if($logging_setting == '2')
		{
			$api_checked = 'checked';
		}
		else if($logging_setting == '3')
		{
			$enable_checked = 'checked';
		}
	}
	
	$results = mysql_query("SELECT enable
								   from privacy_settings
								   where uid = $user_id", $db);
	
	$privacy_disable = 'disabled="disabled"';
	while($row = mysql_fetch_array($results))
	{
		$enable = $row['enable'];
		$privacy_disable = '';
		
		if($enable == '0')
		{
			$privacy_default_checked = 'checked';
		}
		else if($enable == '1')
		{
			$privacy_quality_checked = 'checked';
		}
		else if($enable == '2')
		{
			$privacy_category_checked = 'checked';
		}
		else if($enable == '3')
		{
			$privacy_keywords_checked = 'checked';
		}
	}
	
	echo '<ul>
		  <li style="color: #AA3333;padding-top: 10px;padding-left: 10px;">
		  	<a href="#" onclick="do_clsoe_get_faith_setting();">close</a>
		  </li>
		  <br />
		  <li style="color: #AA3333;padding-left: 10px;">
		  	Access Log Setting
		  </li>
		  <hr>'.
		  //<input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,0".');" name="setting_select_input" id="Disable_Logging" '.$disable_checked.' value="0">Disable Logging</input><br />
		  '<input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,1".');" name="setting_select_input" id="Enable_URL" '.$url_checked.' value="1">Enable URL Logging Only</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,2".');" name="setting_select_input" id="Enable_API" '.$api_checked.' value="2">Enable API Logging Only</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,3".');" name="setting_select_input" id="Enable_Logging" '.$enable_checked.' value="3">Enable Logging</input>
		  <br /><br /><br />
		  <li style="color: #AA3333;padding-left: 10px;">
		  	<a style="text-decoration:underline;" href="'.$facebook_iframe_canvas_page_url.'service_privacy_shield.php?setting=1">Wall Post Privacy Setting</a>
		  </li>
		  <hr>
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,0".');" name="privacy_setting_select_input" id="Enable_Default" '.$privacy_default_checked.' '.$privacy_disable.' value="0">Default</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,1".');" name="privacy_setting_select_input" id="Enable_Quality" '.$privacy_quality_checked.' '.$privacy_disable.' value="1">Quality/Tie Strength</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,2".');" name="privacy_setting_select_input" id="Enable_Category" '.$privacy_category_checked.' '.$privacy_disable.' value="2">Category/Friend List</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,3".');" name="privacy_setting_select_input" id="Enable_Keywords" '.$privacy_keywords_checked.' '.$privacy_disable.' value="3">Keywords/Interests</input><br />
		  </ul>';
}
else if($option == '2')
{
	$logging_setting = '0';
	$value = $_POST['value'];
	
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $user_id", $db);
	
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
	}
	
	if($value == '0' && $logging_setting != '0')
	{
		$remove_results = mysql_query("DELETE FROM setting_logging
					  						       where uid = $user_id", $db);
		if(!$remove_results)
	    {
		    echo 'Failed to change the setting!';
	    }
	    else
	    {
		    echo 'Setting has been changed successfully!';
	    }
	}
	else if($logging_setting == '0' && $value != '0')
	{
		$query = sprintf("INSERT INTO setting_logging (uid, 
										 			   logging_setting) 
										 			   VALUES('%s', '%s')",
													   mysql_real_escape_string($user_id), 
										 			   mysql_real_escape_string($value));
				
		if(!mysql_query($query))
	    {echo mysql_error();	
	    	echo 'Failed to change the setting!';
	    }
		else
	    {
		    echo 'Setting has been changed successfully!';
	    }
	}
	else if($logging_setting != '0' && $value != '0')
	{
		$query = sprintf("UPDATE setting_logging SET logging_setting = '%s'
												   WHERE uid = '%s'",
												   mysql_real_escape_string($value),
												   $user_id);
								
		if(!mysql_query($query))
	    {
		    echo 'Failed to change the setting!';
	    }
		else
	    {
		    echo 'Setting has been changed successfully!';
	    }
	}
	
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $user_id", $db);
	
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
		$disable_checked = '';
		
		if($logging_setting == '1')
		{
			$url_checked = 'checked';
		}
		else if($logging_setting == '2')
		{
			$api_checked = 'checked';
		}
		else if($logging_setting == '3')
		{
			$enable_checked = 'checked';
		}
	}
	
	$results = mysql_query("SELECT enable
								   from privacy_settings
								   where uid = $user_id", $db);
	
	$privacy_disable = 'disabled="disabled"';
	while($row = mysql_fetch_array($results))
	{
		$enable = $row['enable'];
		$privacy_disable = '';
		
		if($enable == '0')
		{
			$privacy_default_checked = 'checked';
		}
		else if($enable == '1')
		{
			$privacy_quality_checked = 'checked';
		}
		else if($enable == '2')
		{
			$privacy_category_checked = 'checked';
		}
		else if($enable == '3')
		{
			$privacy_keywords_checked = 'checked';
		}
	}
	
	echo '<ul>
		  <li style="color: #AA3333;padding-top: 10px;padding-left: 10px;">
		  	<a href="#" onclick="do_clsoe_get_faith_setting();">close</a>
		  </li>
		  <hr>'.
		  //<input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,0".');" name="setting_select_input" id="Disable_Logging" '.$disable_checked.' value="0">Disable Logging</input><br />
		  '<input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,1".');" name="setting_select_input" id="Enable_URL" '.$url_checked.' value="1">Enable URL Logging Only</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,2".');" name="setting_select_input" id="Enable_API" '.$api_checked.' value="2">Enable API Logging Only</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,3".');" name="setting_select_input" id="Enable_Logging" '.$enable_checked.' value="3">Enable Logging</input>
		  <br /><br /><br />
		  <li style="color: #AA3333;padding-left: 10px;">
		  	<a style="text-decoration:underline;" href="'.$facebook_iframe_canvas_page_url.'service_privacy_shield.php?setting=1">Wall Post Privacy Setting</a>
		  </li>
		  <hr>
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,0".');" name="privacy_setting_select_input" id="Enable_Default" '.$privacy_default_checked.' '.$privacy_disable.' value="0">Default</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,1".');" name="privacy_setting_select_input" id="Enable_Quality" '.$privacy_quality_checked.' '.$privacy_disable.' value="1">Quality/Tie Strength</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,2".');" name="privacy_setting_select_input" id="Enable_Category" '.$privacy_category_checked.' '.$privacy_disable.' value="2">Category/Friend List</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,3".');" name="privacy_setting_select_input" id="Enable_Keywords" '.$privacy_keywords_checked.' '.$privacy_disable.' value="3">Keywords/Interests</input><br />
		  </ul>';
}
else if($option == '3')
{
	$value = $_POST['value'];
	
	if($value >= '0' && $value <= '3')
	{
		$query = sprintf("UPDATE privacy_settings SET enable = '%s'
												   WHERE uid = '%s'",
												   mysql_real_escape_string($value),
												   $user_id);
								
		if(!mysql_query($query))
	    {
		    echo 'Failed to change the privacy setting!';
	    }
		else
	    {
		    echo 'Privacy setting has been changed successfully!';
	    }
	}
	
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $user_id", $db);
	
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
		$disable_checked = '';
		
		if($logging_setting == '1')
		{
			$url_checked = 'checked';
		}
		else if($logging_setting == '2')
		{
			$api_checked = 'checked';
		}
		else if($logging_setting == '3')
		{
			$enable_checked = 'checked';
		}
	}
	
	$results = mysql_query("SELECT enable
								   from privacy_settings
								   where uid = $user_id", $db);
	
	$privacy_disable = 'disabled="disabled"';
	while($row = mysql_fetch_array($results))
	{
		$enable = $row['enable'];
		$privacy_disable = '';
		
		if($enable == '0')
		{
			$privacy_default_checked = 'checked';
		}
		else if($enable == '1')
		{
			$privacy_quality_checked = 'checked';
		}
		else if($enable == '2')
		{
			$privacy_category_checked = 'checked';
		}
		else if($enable == '3')
		{
			$privacy_keywords_checked = 'checked';
		}
	}
	
	echo '<ul>
		  <li style="color: #AA3333;padding-top: 10px;padding-left: 10px;">
		  	<a href="#" onclick="do_clsoe_get_faith_setting();">close</a>
		  </li>
		  <br />
		  <li style="color: #AA3333;padding-left: 10px;">
		  	Access Log Setting
		  </li>
		  <hr>'.
		  //<input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,0".');" name="setting_select_input" id="Disable_Logging" '.$disable_checked.' value="0">Disable Logging</input><br />
		  '<input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,1".');" name="setting_select_input" id="Enable_URL" '.$url_checked.' value="1">Enable URL Logging Only</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,2".');" name="setting_select_input" id="Enable_API" '.$api_checked.' value="2">Enable API Logging Only</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',2,3".');" name="setting_select_input" id="Enable_Logging" '.$enable_checked.' value="3">Enable Logging</input>
		  <br /><br /><br />
		  <li style="color: #AA3333;padding-left: 10px;">
		  	<a style="text-decoration:underline;" href="'.$facebook_iframe_canvas_page_url.'service_privacy_shield.php?setting=1">Wall Post Privacy Setting</a>
		  </li>
		  <hr>
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,0".');" name="privacy_setting_select_input" id="Enable_Default" '.$privacy_default_checked.' '.$privacy_disable.' value="0">Default</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,1".');" name="privacy_setting_select_input" id="Enable_Quality" '.$privacy_quality_checked.' '.$privacy_disable.' value="1">Quality/Tie Strength</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,2".');" name="privacy_setting_select_input" id="Enable_Category" '.$privacy_category_checked.' '.$privacy_disable.' value="2">Category/Friend List</input><br />
		  <input type="radio" onclick="do_change_faith_setting_ajax('."'".$user_id."',3,3".');" name="privacy_setting_select_input" id="Enable_Keywords" '.$privacy_keywords_checked.' '.$privacy_disable.' value="3">Keywords/Interests</input><br />
		  </ul>';
}

?>







