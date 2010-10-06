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
		<a href="http://apps.facebook.com/dsl_faith/view_history_url.php">URL Log by Time</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/view_history.php">API Log by Time</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/view_history_api.php">API Log by RESTful API</a>
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
		<h4>View API Access Log by Time :</h4>
		
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
		
		$results = mysql_query("SELECT restapi.name as name, 
									   facebook_application.app_name as app_name,
									   facebook_application.default_page as default_page,
									   facebook_application.id as app_id,
									   access_log.allowed,
									   access_log.access_time,
									   access_log.logID,
									   access_log.url_id,
									   INET_NTOA(access_log.app_ip_addr) AS app_ip_addr,
									   INET_NTOA(access_log.user_ip_addr) AS user_ip_addr,
									   restapi_field.id as restapi_field_id,
									   restapi.id as restapi_id
									   from access_log INNER JOIN facebook_application
										               ON facebook_application.id = access_log.app_id
										               INNER JOIN restapi
										               ON restapi.id = access_log.api_id
										               INNER JOIN restapi_field
										               ON restapi.restapi_field_id = restapi_field.id
									   where access_log.uid = $user_id
									   order by access_log.access_time DESC
									   LIMIT 100", $db);
        
		$div_counter = 0;
		while($row = mysql_fetch_array($results))
		{
			$name = $row['name'];
			$app_name = $row['app_name'];
			$allowed = $row['allowed'];
			$access_time = $row['access_time'];
			$logID = $row['logID'];
			$restapi_field_id = $row['restapi_field_id'];
			$restapi_id = $row['restapi_id'];
			$default_page = $row['default_page'];
			$app_id = $row['app_id'];
			$app_ip_addr = $row['app_ip_addr'];
			$user_ip_addr = $row['user_ip_addr'];
			$url_id = $row['url_id'];
			
			if($allowed == '1')
			{
				$allowed = 'Access Allowed';
			}
			else
			{
				$allowed = '<font color="red">Access Denied</font>';
			}
			
			get_access_log_contents($name,
								    $app_name,
								    $allowed,
								    $access_time,
								    $logID,
								    $restapi_field_id,
								    $restapi_id,
								    $default_page,
								    $app_id,
								    $app_ip_addr,
								    $user_ip_addr,
								    $url_id,
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

	function get_access_log_contents($name,
								     $app_name,
								     $allowed,
								     $access_time,
								     $logID,
								     $restapi_field_id,
								     $restapi_id,
								     $default_page,
								     $app_id,
								     $app_ip_addr,
								     $user_ip_addr,
								     $url_id,
								     $div_counter)
	{
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
		
		$detail_message = '<a href="#" onclick="do_ajax_ip('."'ip_infor_Div".$div_counter."'".',3,'."'$logID'".','."'loading_img".$div_counter."'".');">details</a>';
		
		echo 
		'<tr><td>
		<table width="100%" style="padding-top: 5px;">
		<tr>
			<td width="5%"></td>
			<td width="40%">
			<a href="http://apps.facebook.com/dsl_faith/set_policy.php?field=' . $restapi_field_id . '">' . $name . '</a>
			</td>
			<td width="10%">called by:</td>
			<td width="40%">
			<a href="http://apps.facebook.com/dsl_faith/index.php?ffile=' . $default_page . '&fpro=' . $app_id .'">' . $app_name . '</a>
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td colspan="3" width="90%">
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">'. $access_time . 
			'</font> 
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">' . $allowed . 
			' </font>
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">' . $app_server_html . 
			' </font>
			<font style="padding-left: 10px; padding-right: 10px; border-right: #AAAAAA 1px solid;">' . $client_server_html . 
			' </font>
			<font style="padding-left: 10px;">' . $detail_message . 
			' </font>
			</td>
			</td>
			<td width="5%" style="text-align: center;vertical-align:top;">
			<img style="display:none;" id="loading_img'.$div_counter.'" src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/ajax-loader.gif" />
			</td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td colspan="3" width="90%">
			<table style="border-bottom: #CCCCCC 1px solid; padding-bottom: 5px;" width="100%">
			<tr>
				<td width="100%">
			    <div style="background-color: #eceff6;" id="ip_infor_Div'.$div_counter.'"></div>
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
