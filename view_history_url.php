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
<tr>
	<td>
	<table>
	<tr>
		<td class="PageTitleLink">
		<a href="<?php echo $facebook_canvas_page_url; ?>view_history_url.php">URL Log by Time</a>
		</td>
		<td class="PageTitleLink">
		<a href="<?php echo $facebook_canvas_page_url; ?>view_history.php">API Log by Time</a>
		</td>
		<td class="PageTitleLink">
		<a href="<?php echo $facebook_canvas_page_url; ?>view_history_api.php">API Log by RESTful API</a>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td style="height: 650px;vertical-align:top;border-right: #3b5998 3px solid;border-top: #3b5998 3px solid;border-left: #3b5998 3px solid;border-bottom: #3b5998 3px solid;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="10" width="100%">
		<tr><td width="50px"></td>
		<td class="FAITHHomeDescription">
		<br />
		<h4>View URL Access Log by Time :</h4>
		
		</td>
		<td width="50px"></td></tr>
		
		<?php
		
		GLOBAL $dsiplay_number;
		$dsiplay_number = '300';
		
		if($_POST['submit_date'] == 'Submit')
		{
			$dsiplay_number = $_POST['display_quantity'];
		}
		
		echo
		'<tr>
			<td width="50px"></td>
			<td>
			<form action="view_history_url.php" method="post">
			<font style="color: #AA3333;">From:</font>
			<select name="start_month" id="start_month">'.get_month_contents(1).'</select>
			<select name="start_day" id="start_day">'.get_day_contents(1).'</select>
			<select name="start_year" id="start_year">'.get_year_contents(1).'</select>
			
			<font style="color: #AA3333;">To:</font>
			<select name="end_month" id="end_month">'.get_month_contents(0).'</select>
			<select name="end_day" id="end_day">'.get_day_contents(0).'</select>
			<select name="end_year" id="end_year">'.get_year_contents(0).'</select>
			
			<font style="color: #AA3333;">Show first </font>
			<input type="text" id="display_quantity" name="display_quantity" size="3" value="'.$dsiplay_number.'" maxlength="6" class="TextCSS" />
			<input type="submit" id="submit_date" name = "submit_date" value="Submit" />
			</form>
			</td>
			<td width="50px"></td>
		</tr>';
		
		function get_month_contents($is_from)
	    {
	    	$arr_Month = array("1" => "Jan", "2" => "Feb", "3" => "Mar", "4" => "Apr", "5" => "May", "6" => "Jun", 
	    				       "7" => "Jul", "8" => "Aug", "9" => "Sept", "10" => "Oct", "11" => "Nov", "12" => "Dec",);
	    	$html_month ='';
	    	$today = getdate();
	    	$current_month = $today['mon'];
	    	for ($month = 1 ; $month <= 12; $month++)
	    	{
	    		if($is_from == 1 && $month == $_POST['start_month'])
	    		{
	    			$html_month .= '<option value="'.$month.'" selected>'.$arr_Month[$month].'</option>';
	    		}
	    		else if($is_from == 0 && $month == $_POST['end_month'])
	    		{
	    			$html_month .= '<option value="'.$month.'" selected>'.$arr_Month[$month].'</option>';
	    		}
	    		else if($current_month == $month && $_POST['submit_date'] != 'Submit')
	    		{
	    			$html_month .= '<option value="'.$month.'" selected>'.$arr_Month[$month].'</option>';
	    		}
	    		else
	    		{
					$html_month .= '<option value="'.$month.'">'.$arr_Month[$month].'</option>';
	    		}
	    	}
	    	return $html_month;
	    }
	    
	    function get_day_contents($is_from)
	    {
	    	$html_day ='';
	    	$today = getdate();
	    	$current_day = $today['mday'];
	    	
	    	for ($day = 1 ; $day <= 31; $day++)
	    	{
	    		if($is_from == 1 && $day == $_POST['start_day'])
	    		{
	    			$html_day .= '<option value="'.$day.'" selected>'.$day.'</option>';
	    		}
	    		else if($is_from == 0 && $day == $_POST['end_day'])
	    		{
	    			$html_day .= '<option value="'.$day.'" selected>'.$day.'</option>';
	    		}
	    		else if($current_day == $day && $_POST['submit_date'] != 'Submit')
	    		{
	    			$html_day .= '<option value="'.$day.'" selected>'.$day.'</option>';
	    		}
	    		else
	    		{
					$html_day .= '<option value="'.$day.'">'.$day.'</option>';
	    		}
	    	}
	    	return $html_day;
	    }
	    
	    function get_year_contents($is_from)
	    {
	    	$html_year ='';
	    	$today = getdate();
	    	$current_year = $today['year'];
	    	for ($year = $current_year - 5 ; $year <= $current_year + 5; $year++)
	    	{
	    		if($is_from == 1 && $year == $_POST['start_year'])
	    		{
	    			$html_year .= '<option value="'.$year.'" selected>'.$year.'</option>';
	    		}
	    		else if($is_from == 0 && $year == $_POST['end_year'])
	    		{
	    			$html_year .= '<option value="'.$year.'" selected>'.$year.'</option>';
	    		}
	    		else if($current_year == $year && $_POST['submit_date'] != 'Submit')
	    		{
	    			$html_year .= '<option value="'.$year.'" selected>'.$year.'</option>';
	    		}
	    		else
	    		{
					$html_year .= '<option value="'.$year.'">'.$year.'</option>';
	    		}
	    	}
	    	return $html_year;
	    }
    
		?>
		
		</table>
		</td>
	</tr>
<?php

require_once 'vars.php';
require_once 'facebook.php';

try
{
	$facebook = new Facebook($appapikey, $appsecret);
	$user_id = $facebook->require_login();
	
	try
	{
		mysqlSetup($db);
		
		GLOBAL $dsiplay_number;
		$results;
		
		if($_POST['submit_date'] == 'Submit')
		{
			$from_day_str = $_POST['start_year'].'-'.$_POST['start_month'].'-'.$_POST['start_day'];
			$to_day_str = $_POST['end_year'].'-'.$_POST['end_month'].'-'.$_POST['end_day'];
			
			$results = mysql_query("SELECT url_log.uid, 
									   facebook_application.app_name as app_name,
									   url_log.access_time_start,
									   url_log.access_time_end,
									   url_log.url_details,
									   url_log.faith_type,
									   url_log.url_logID,
									   INET_NTOA(url_log.app_ip_addr) AS app_ip_addr,
									   INET_NTOA(url_log.user_ip_addr) AS user_ip_addr
									   from url_log INNER JOIN facebook_application
										            ON facebook_application.id = url_log.app_id
									   where url_log.uid = $user_id AND
									   TO_DAYS(url_log.access_time_start) BETWEEN TO_DAYS('$from_day_str') AND TO_DAYS('$to_day_str')
									   order by url_log.access_time_start DESC
									   LIMIT $dsiplay_number", $db);
		}
		else
		{
			$results = mysql_query("SELECT url_log.uid, 
										   facebook_application.app_name as app_name,
										   url_log.access_time_start,
										   url_log.access_time_end,
										   url_log.url_details,
										   url_log.faith_type,
										   url_log.url_logID,
										   INET_NTOA(url_log.app_ip_addr) AS app_ip_addr,
										   INET_NTOA(url_log.user_ip_addr) AS user_ip_addr
										   from url_log INNER JOIN facebook_application
											            ON facebook_application.id = url_log.app_id
										   where url_log.uid = $user_id
										   order by url_log.access_time_start DESC
										   LIMIT $dsiplay_number", $db);
		}
		echo mysql_error();
	
	
		$div_counter = 0;
		while($row = mysql_fetch_array($results))
		{
			$uid = $row['uid'];
			$app_name = $row['app_name'];
			$access_time_start = $row['access_time_start'];
			$access_time_end = $row['access_time_end'];
			$url_details = $row['url_details'];
			$app_ip_addr = $row['app_ip_addr'];
			$user_ip_addr = $row['user_ip_addr'];
			$faith_type = $row['faith_type'];
			$url_logID = $row['url_logID'];
			
			get_url_log_contents($uid,
								 $app_name,
								 $access_time_start,
								 $access_time_end,
								 $url_details,
								 $app_ip_addr,
								 $user_ip_addr,
								 $faith_type,
								 $url_logID,
								 $div_counter);
			$div_counter++;
		}
	}
	catch (Exception $e)
	{
		echo 'Caught database exception: ',  $e->getMessage(), "\n";
	}
} 
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>

<?php 

	function get_url_log_contents($uid,
								  $app_name,
								  $access_time_start,
								  $access_time_end,
								  $url_details,
								  $app_ip_addr,
								  $user_ip_addr,
								  $faith_type,
								  $url_logID,
								  $div_counter)
	{
		GLOBAL $source_server_url;
		GLOBAL $facebook_canvas_page_url;
		GLOBAL $facebook_iframe_canvas_page_url;
		
		$app_server_html ='';
		$client_server_html = '';
		
		if(isset($app_ip_addr) && strlen($app_ip_addr) > 0)
		{
			$app_server_html .= 
			'App Server :
			<a href="#" onclick="do_ajax_ip('."'ip_infor_Div".$div_counter."'".',2,'."'$app_ip_addr'".','."'loading_img".$div_counter."'".');">
			'.$app_ip_addr.'
			</a>';
		}
		
		if(isset($user_ip_addr) && strlen($user_ip_addr) > 0)
		{
			$client_server_html .= 
			'User : 
			<a href="#" onclick="do_ajax_ip('."'ip_infor_Div".$div_counter."'".',2,'."'$user_ip_addr'".','."'loading_img".$div_counter."'".');">' . $user_ip_addr . '</a>';
		}
		
		$root_url = '<a href="'.$facebook_canvas_page_url.'">FAITH FBML</a>';

		if($faith_type == '2')
		{
			$root_url = '<a href="'.$source_server_url.'fbc/">FAITH Facebook Connect</a>'; 
		}
		else if($faith_type == '3')
		{
			$root_url = $root_url = '<a target="_top" href="'.$facebook_iframe_canvas_page_url.'">FAITH IFrame</a>';
		}
		
		$api_detail_message = '<a href="#" onclick="do_ajax_ip('."'ip_infor_Div".$div_counter."'".',5,'."'$url_logID'".','."'loading_img".$div_counter."'".');">API details</a>';
		
		$html_detail_message = '<a href="#" onclick="do_ajax_ip('."'ip_infor_Div".$div_counter."'".',4,'."'$url_logID'".','."'loading_img".$div_counter."'".');">html details</a>';
		
		echo 
		'<tr><td>
		<table width="100%" style="padding-top: 5px;">
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<a href="'.$url_details.'">' . $url_details .'</a>
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">
			<a href="'.$facebook_canvas_page_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'">' . $app_name . '</a>
			via
			'.$root_url.'
			</font>
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">'. $access_time_start .
			'</font> 
			</td>
			<td width="5%">
			</td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">' . $app_server_html . 
			' </font>
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">' . $client_server_html . 
			' </font>
			<font style="padding-left: 10px;">' . $api_detail_message .
			' </font>
			<font style="padding-left: 10px;">' . $html_detail_message . 
			' </font>
			</td>
			<td width="5%" style="text-align: center;vertical-align:top;">
			<img style="display:none;" id="loading_img'.$div_counter.'" src="'.$source_server_url.'image/ajax-loader.gif" />
			</td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<table style="border-bottom: #CCCCCC 1px solid; padding-bottom: 5px;" width="100%">
			<tr>
				<td width="100%">
			    <div style="background-color: #eceff6;word-wrap: break-word;" id="ip_infor_Div'.$div_counter.'"></div>
				</td>
			</tr>
			</table>
			</td>
			<td width="5%"></td>
		</tr>
		</table>
		</td></tr>';
	}

?>
<tr>
	<td height="80px"></td>
</tr>
<script type="text/javascript">
<!--
function do_ajax_ip(div,val,ip,img) {

	document.getElementById(img).setStyle('display', 'inline');
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.ondone = function(data) 
				  {
					document.getElementById(img).setStyle('display', 'none');
				  	document.getElementById(div).setInnerFBML(data);
				  }

	var params={"action":'select',"option":val,"searchwords":'none',"ipaddr":ip};
	ajax.post('<?=$view_history_callbackurl?>?t='+val,params); 
	} 
//-->
</script>
</table>
	</td>
</tr>
</table>
</html>
