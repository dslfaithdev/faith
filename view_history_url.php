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
									   LIMIT 100", $db);
        
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
			'Application Server IP :
			<a href="#" onclick="do_ajax_ip('."'ip_infor_Div".$div_counter."'".',2,'."'$app_ip_addr'".','."'loading_img".$div_counter."'".');">
			'.$app_ip_addr.'
			</a>';
		}
		
		if(isset($user_ip_addr) && strlen($user_ip_addr) > 0)
		{
			$client_server_html .= 
			'Client IP : 
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
