<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Welcome to DSL FAITH </title>
<style type="text/css">
<?php echo htmlentities(file_get_contents('faith_style.css', true)); ?>
</style>
</head>
<table cellspacing="0" cellpadding="0">
<tr>
	<td>
	<?php 
	require_once 'func.php';
	require_once 'vars.php';
	require_once 'facebook.php';
	
	try
	{
	mysqlSetup($db);
	$facebook = new Facebook($appapikey, $appsecret);
	$user_id = $facebook->require_login();
	
	$results = mysql_query("SELECT transform_add.transform_add_id,
								   transform_add.add_uid_a,
								   transform_add.add_time,
								   transform_add.social_path,
								   transform_add.status
								   from transform_add
								   where transform_add.add_uid_b = $user_id AND
								   	     transform_add.status = 0
								   order by transform_add.add_time DESC", $db);
        
	$div_counter = 0;
	while($row = mysql_fetch_array($results))
	{
		$div_counter++;
	}
	
	display_header_links($div_counter, $user_id);
	
	//-----------------------------------------------------------
	$logging_enabld = false;
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $user_id", $db);
	
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
		$disable_checked = '';
		
		if($logging_setting == '1' || $logging_setting == '2' || $logging_setting == '3')
		{
			$logging_enabld = true;
		}
	}
	
	if(!$logging_enabld)
	{
		$query = sprintf("INSERT INTO setting_logging (uid, 
										 			   logging_setting) 
										 			   VALUES('%s', '%s')",
													   mysql_real_escape_string($user_id), 
										 			   mysql_real_escape_string("3"));
		if(!mysql_query($query))
	    {	
	    	
	    }
	}
	//-----------------------------------------------------------
	
	}
	catch (Exception $e)
	{
		echo 'Caught database exception: ',  $e->getMessage(), "\n";
	}
	?>
	</td>
</tr>
<tr>
	<td height="10px"></td>
</tr>
<?php

require_once 'vars.php';
require_once 'facebook.php';
	
try
{	
	$facebook = new Facebook($appapikey, $appsecret);
	$user_id = $facebook->require_login($required_permissions = 'publish_stream,email,create_event,read_stream,sms,rsvp_event,offline_access');
		
	if(isset($_GET['ffile']) && isset($_GET['fpro']))
	{
		echo '<tr><td>';
		
		$fpro = $_GET['fpro'];
		
		$post_params = array();
		foreach ($_POST as $key => &$val) {
		  if($key != 'fb_sig_app_id')
		  {
	      	$post_params[] = $key.'='.urlencode($val);
		  }
	    }
	    
	    $faith_url_id = '0';
	    $logging_setting = '0';
		try
		{
			$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $user_id", $db);
			
			while($row = mysql_fetch_array($results))
			{
				$logging_setting = $row['logging_setting'];
			}
			
			$app_ip_addr = $_SERVER['REMOTE_ADDR'];
			$faith_client_ip = $_SERVER['HTTP_X_FB_USER_REMOTE_ADDR'];
			
			if($logging_setting == '1' || $logging_setting == '3')
			{
				$query = sprintf("INSERT INTO url_log (uid, 
													   faith_type,
													   app_ip_addr,
													   user_ip_addr) 
													   VALUES('%s','%s',INET_ATON('$app_ip_addr'),INET_ATON('$faith_client_ip'))",
													   mysql_real_escape_string($user_id),
													   mysql_real_escape_string($faith_fbml));
				
				if(!mysql_query($query))
				{
					echo "Query failed" . mysql_error() . "<br />";
				}
			
				$faith_url_id = mysql_insert_id();
			}
		}
		catch (Exception $e)
		{
			echo 'Caught exception: ',  $e->getMessage(), "<br />";
		}
	    
	    $post_params[] = 'faith_uid='.urlencode($user_id);
		$post_params[] = 'faith_app_id='.urlencode($fpro);
		$post_params[] = 'faith_client_ip='.urlencode($_SERVER['HTTP_X_FB_USER_REMOTE_ADDR']);
		$post_params[] = 'faith_source='.urlencode($faith_fbml);
		$post_params[] = 'faith_url_id='.urlencode($faith_url_id);
	    $post_params[] = 'faith_u='.urlencode($source_server_url);
	    $post_params[] = 'faith_k='.urlencode($appapikey);
	    
	    $postStr = implode('&', $post_params);
		
	    $opts = array(
		  'http'=>array(
		    'method'=>"POST",
		    'header'=>"Accept-language: en\r\n" .
		              "Cookie: foo=bar\r\n",
			'content'=>$postStr
		  )
		);
	
		mysqlSetup($db);
		
		$results = mysql_query("SELECT canvas_page, 
									   canvas_callback
									   from facebook_application
									   where id = $fpro", $db);
	
		$row = mysql_fetch_array($results);
		
	$canvas_callback = html_entity_decode($row['canvas_callback']);
	$canvas_page = html_entity_decode($row['canvas_page']);
	$canvas_callback = str_replace('http://cyrus.cs.ucdavis.edu/' ,'http://169.237.6.102/', $canvas_callback);
	
	$FaithFBURL = $facebook_canvas_page_url;
	
	$targetURL = $canvas_callback . urldecode ( $_GET['ffile']);
	
	date_default_timezone_set('America/Los_Angeles');
	$access_time_start = date("Y-m-d H:i:s");
	
	$context = stream_context_create($opts);
	$homepage = file_get_contents($targetURL, false, $context);
	
	$access_time_end = date("Y-m-d H:i:s");
	
	$href_regex ="href"; // 6 the href bit of the tag
	$href_regex .="\s*"; // 7 zero or more whitespace
	$href_regex .="="; // 8 the = of the tag
	$href_regex .="\s*"; // 9 zero or more whitespace
	$href_regex .="[\"']?"; // 10 none or one of " or '
	$href_regex .="("; // 11 opening parenthesis, start of the bit we want to capture
	$href_regex .="[^\"' >]+"; // 12 one or more of any character _except_ our closing characters
	$href_regex .=")"; // 13 closing parenthesis, end of the bit we want to capture
	$href_regex .="[\"' >]"; // 14 closing chartacters of the bit we want to capture

	$regex = "/"; // regex start delimiter
	$regex .= $href_regex; //
	$regex .= "/"; // regex end delimiter
	$regex .= "i"; // Pattern Modifier - makes regex case insensative
	$regex .= "s"; // Pattern Modifier - makes a dot metacharater in the pattern
	// match all characters, including newlines
	$regex .= "U"; // Pattern Modifier - makes the regex ungready

	preg_match_all($regex, $homepage, $matches);
	$arr_completeURL = $matches[0];
	$arr_page = $matches[1];
	
	$count = count($arr_completeURL);
	for ($i = 0; $i < $count; $i++)
	{
		if(substr_count(strtolower($arr_page[$i]), 'href=') == '0' &&
		   $arr_page[$i] != '#')
		{
			if(substr_count(strtolower($arr_page[$i]), 'http:') == '0')
			{
				$page = $arr_page[$i];
				$completeURL = $arr_completeURL[$i];
				
				$replaceStr = str_replace($page , $FaithFBURL . 'index.php?' . 'ffile=' . urlencode($page) . '&fpro=' . $fpro, $completeURL);
				
				$homepage = str_replace($arr_completeURL[$i], 
										$replaceStr,
										$homepage);
			}
			else if(substr_count(strtolower($arr_page[$i]), $canvas_page) == '1')
			{
				$page = str_replace($canvas_page, '', $arr_page[$i]);
				$completeURL = $arr_completeURL[$i];
				$replaceStr = str_replace($arr_page[$i] , $FaithFBURL . 'index.php?' . 'ffile=' . urlencode($page) . '&fpro=' . $fpro, $completeURL);
				$homepage = str_replace($arr_completeURL[$i], 
										$replaceStr,
										$homepage);
			}
		}
	}
	
	$href_regex ="action"; // 6 the href bit of the tag
	$href_regex .="\s*"; // 7 zero or more whitespace
	$href_regex .="="; // 8 the = of the tag
	$href_regex .="\s*"; // 9 zero or more whitespace
	$href_regex .="[\"']?"; // 10 none or one of " or '
	$href_regex .="("; // 11 opening parenthesis, start of the bit we want to capture
	$href_regex .="[^\"' >]+"; // 12 one or more of any character _except_ our closing characters
	$href_regex .=")"; // 13 closing parenthesis, end of the bit we want to capture
	$href_regex .="[\"' >]"; // 14 closing chartacters of the bit we want to capture

	$regex = "/"; // regex start delimiter
	$regex .= $href_regex; //
	$regex .= "/"; // regex end delimiter
	$regex .= "i"; // Pattern Modifier - makes regex case insensative
	$regex .= "s"; // Pattern Modifier - makes a dot metacharater in the pattern
	// match all characters, including newlines
	$regex .= "U"; // Pattern Modifier - makes the regex ungready

	preg_match_all($regex, $homepage, $matches);
	$arr_completeURL = $matches[0];
	$arr_page = $matches[1];
	
	$count = count($arr_completeURL);
	for ($i = 0; $i < $count; $i++)
	{
		if(substr_count(strtolower($arr_page[$i]), 'http:') == '0')
		{
		$page = $arr_page[$i];
		$completeURL = $arr_completeURL[$i];
		
		$replaceStr = str_replace($page , $FaithFBURL . 'index.php?' . 'ffile=' . urlencode($page) . '&fpro=' . $fpro, $completeURL);
		
		$homepage = str_replace($arr_completeURL[$i], 
								$replaceStr,
								$homepage);
		}
	}
	
	try
	{
		if(($logging_setting == '1' || $logging_setting == '3') && $faith_url_id != '0')
		{
			$url_details = $FaithFBURL . 'index.php?ffile=' . $_GET['ffile'] . '&fpro=' . $fpro;
			
			
			
			$query = sprintf("UPDATE url_log SET app_id = '%s',
												 access_time_start = '%s',
												 access_time_end = '%s',
												 url_details = '%s',
												 html_details = '%s'
												 WHERE url_logID = '%s'",
												 mysql_real_escape_string($fpro),
												 mysql_real_escape_string($access_time_start),
												 mysql_real_escape_string($access_time_end),
												 mysql_real_escape_string($url_details),
												 mysql_real_escape_string($homepage),
												 mysql_real_escape_string($faith_url_id));
			
			if(!mysql_query($query))
			{
				echo "Query failed" . mysql_error() . "<br />";
			}
		}
	}
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "<br />";
	}
	
	echo $homepage;
	}
	else
	{
		get_home_page_contents($user_id);
	}
} 
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

function get_home_page_contents($user_id)
{
	GLOBAL $facebook_canvas_page_url;
	
	echo 
	'
	<tr>
		<td>
		<table>
		<tr>
			<td class="PageTitleLink">
			<a href="'.$facebook_canvas_page_url.'select_app.php">Live Search</a>
			</td>
			<td class="PageTitleLink">
			<a href="'.$facebook_canvas_page_url.'select_app.php?search=1">Bookmarked</a>
			</td>
			<td class="PageTitleLink">
			<a href="'.$facebook_canvas_page_url.'select_app.php?search=2">Blocked</a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
	<table style="height: 650px;border-right: #3b5998 3px solid;border-top: #3b5998 3px solid;border-left: #3b5998 3px solid;border-bottom: #3b5998 3px solid;" width="100%">
	 	'.get_live_search_contents($user_id).'
	 </table>';
}

function get_live_search_contents($user_id)
{
	GLOBAL $source_server_url;
	$search_text = $_POST['search_txt'];
	
	return
	'
	<tr>
	<td>
	
	<form style="display: inline;" action="select_app.php" method="post">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td></td>
		<td colspan="2" style="text-align: center;">
		<img src="'.$source_server_url.'image/dsl_logo.jpg" /></td>
		<td >
		<td></td>
	</tr>
	<tr>
	<td width="10%"></td>
	
	<td width="60%" height="420px" style="vertical-align:top;">
		<table cellpadding="0" cellspacing="0" width="100%" style="background-color: #3b5998;">
		
		<tr><td>
		<input type="text" name="search_txt" id="search_txt" maxlength="30" style="width: 450px;"
			onkeyup="do_ajax('."'live_search_Div'".',1, '.$user_id.');" value="'.$search_text.'"></input>
		</td></tr>
		<tr><td>
		<div id="live_search_Div" style="background-color: #ffffff;text-align: center;">
		<br /><br /><br />
		<a style="padding-right:15px;padding-left:15px;" href="http://www.geni.net/" target="_blank">
		<img src="'.$source_server_url.'image/geni.png" style="border-style: none" />
		</a>
		<a style="padding-right:15px;padding-left:15px;" href="http://www.ucdavis.edu/" target="_blank">
		<img src="'.$source_server_url.'image/uc-davis.jpg" style="border-style: none" />
		</a>
		<a style="padding-right:15px;padding-left:15px;" href="http://dsl.cs.ucdavis.edu/lab_website/index.php" target="_blank">
		<img src="'.$source_server_url.'image/dsl.jpg" style="border-style: none" />
		</a>
		</div>
		</td></tr>
		</table>
	</td>
		
	<td width="20%" style="vertical-align:text-top;text-align: right;padding-left: 5px;">
	<INPUT type="submit" id="live_search" name = "live_search" value="Search Applications" style="width: 130px;" />
	<br /><br />
	<table cellpadding="0" cellspacing="0" width="100%" style="border-left: #3b5998 1px solid;padding-left: 5px;">
	<tr><td height="350px" style="vertical-align: top;background-color: #eceff6;">
		<table cellpadding="0" cellspacing="10" width="100%">
		<tr>
			<td>
			<font style="font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;text-decoration: underline;text-align: right;">Search for</font><br />
			</td>
		</tr>
		<tr>
			<td>
			<input type="radio" name="app_select_input" id="app_select_input1" CHECKED value="1">FBML Only</input><br />
			<input type="radio" name="app_select_input" id="app_select_input2" value="2">IFrame Only</input><br />
			<input type="radio" name="app_select_input" id="app_select_input3" value="3">Facebook Connect Only</input><br />
			<input type="radio" name="app_select_input" id="app_select_input4" value="4">ALL</input>
			</td>
		</tr>
		</table>
	</td></tr>
	</table>
	</td>
		
	<td width="10%"></td>
	</tr>
	</table>
	</form>
	</td>
	</tr>
	';
}

?>
	</td>
</tr>
<script type="text/javascript">
<!--
function do_ajax(div,val,uid) {

	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.ondone = function(data) 
				  {
				  	document.getElementById(div).setInnerFBML(data);
				  	document.getElementById(div).setStyle('border', '1px solid #A5ACB2');
				  	document.getElementById(div).setStyle('background', 'white');
				  	document.getElementById(div).setStyle('width', '99%');
				  }

	var select_app_value = '4';
	if(document.getElementById('app_select_input1').getChecked())
	{
		select_app_value = '1';
	}
	else if(document.getElementById('app_select_input2').getChecked())
	{
		select_app_value = '2';
	}
	else if(document.getElementById('app_select_input3').getChecked())
	{
		select_app_value = '3';
	}
	
	var params={"action":'select',"option":val,"searchwords":document.getElementById('search_txt').getValue(),"otherval":uid,"app_select":select_app_value};
	ajax.post('<?=$callbackurl?>?t='+val,params); 
	} 
//-->
</script>
</table>
</html>
<?php
