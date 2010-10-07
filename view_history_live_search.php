<?php

require_once 'func.php';

$option = $_POST['option'];
	
if($option == '1')
{
	$live_search_str = $_POST['searchwords'];
	$uid = $_POST['otherval'];
	
	$match_return = false;
	$hint='<form action="view_history_api.php" name="view_history_api_form" id="view_history_api_form" method="post">
			<select style="width: 300px;" name="restapi_select" id="restapi_select" 
				onChange="document.getElementById('."'apifield_loading_img'".').setStyle('."'display'".', '."'inline'".');document.getElementById('."'view_history_api_form'".').submit();">';
	
	if (strlen($live_search_str) > 0)
	{
		mysqlSetup($db);
		
		$api_results = mysql_query("SELECT id, 
							   		   	   name, 
							   		       facebook_description,
							   		       Supported
							   		       from restapi
							   		       where display=1 AND restapi_field_id = $live_search_str", $db);
		
		while($api_row = mysql_fetch_array($api_results))
		{
			$api_id = $api_row['id'];
			$api_name = $api_row['name'];
	
			$hint .= '<option value="'.$api_id.'" selected>'.$api_name.'</option>';
		}
		
		$hint .= '</select>&nbsp;&nbsp;Please select a RESTful API<br />
       	</form>
		';
		
		echo $hint;
	}
}
else if($option == '11')
{
	$live_search_str = $_POST['searchwords'];
	$uid = $_POST['otherval'];
	
	$match_return = false;
	$hint='<form action="view_history_api.php" name="view_history_api_form" id="view_history_api_form" method="post">
			<select style="width: 300px;" name="restapi_select" id="restapi_select" 
				onChange="document.getElementById('."'apifield_loading_img'".').style.display='."'inline'".';document.getElementById('."'view_history_api_form'".').submit();">';
	
	if (strlen($live_search_str) > 0)
	{
		mysqlSetup($db);
		
		$api_results = mysql_query("SELECT id, 
							   		   	   name, 
							   		       facebook_description,
							   		       Supported
							   		       from restapi
							   		       where display=1 AND restapi_field_id = $live_search_str", $db);
		
		while($api_row = mysql_fetch_array($api_results))
		{
			$api_id = $api_row['id'];
			$api_name = $api_row['name'];
	
			$hint .= '<option value="'.$api_id.'" selected>'.$api_name.'</option>';
		}
		
		$hint .= '</select>&nbsp;&nbsp;Please select a RESTful API<br />
       	</form>
		';
		
		echo $hint;
	}
}
else if($option == '2')
{
	require_once 'ip2location.class.php';
	$ip = new ip2location;
	$ipaddr = $_POST['ipaddr'];
	
	$ip->open('./databases/IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE-SAMPLE.BIN');
	$record = $ip->getAll($ipaddr);
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
		<td>
		<b><font color="red">IP Address: ' . $record->ipAddress . '</font></b><br>
		<b>IP Number:</b> ' . $record->ipNumber . '<br>
		<b>Country Short:</b> ' . $record->countryShort . '<br>
		<b>Country Long:</b> ' . $record->countryLong . '<br>
		<b>Region:</b> ' . $record->region . '<br>
		<b>City:</b> ' . $record->city . '<br>
		<b>Latitude:</b> ' . $record->latitude . '<br>
		</td>
		<td>
		<b>Longitude:</b> ' . $record->longitude . '<br>
		<b>ZIP Code:</b> ' . $record->zipCode . '<br>
		<b>Time Zone:</b> ' . $record->timeZone . '<br>
		<b>IDD Code:</b> ' . $record->iddCode . '<br>
		<b>Area Code:</b> ' . $record->areaCode . '<br>
		<b>Weather Station Name:</b> ' . $record->areaCode . '<br>
		click <a href="http://www.ip2location.com/'.$ipaddr.'" target="_blank">here</a> to see details
		</td>
	</tr>
	 </table>';
}
else if($option == '3')
{
	$logID = $_POST['ipaddr'];
	
	mysqlSetup($db);
		
	$detail_results = mysql_query("SELECT logdetails
						   		          from access_log
						   		          where access_log.logID = $logID", $db);
	
	while($detail_row = mysql_fetch_array($detail_results))
	{	
		$logdetails = $detail_row['logdetails'];

		if(!isset($logdetails) || strlen($logdetails) == 0)
		{
			$logdetails = 'Not available for this record!';
		}
		
		echo
		'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
		<tr>
		<td>
		'.htmlspecialchars($logdetails).'
		</td>
		</tr>
	 	</table>';
	}
}
else if($option == '4')
{
	$logID = $_POST['ipaddr'];
	
	mysqlSetup($db);
		
	$detail_results = mysql_query("SELECT html_details
						   		          from url_log
						   		          where url_log.url_logID = $logID", $db);
	
	while($detail_row = mysql_fetch_array($detail_results))
	{	
		$html_details = $detail_row['html_details'];

		if(!isset($html_details) || strlen($html_details) == 0)
		{
			$logdetails = 'Not available for this record!';
		}
		
		$html_details = htmlspecialchars($html_details);
		$order = array("\r\n", "\n", "\r");
		$html_details = str_replace($order, '<br />', $html_details);
	
		echo
		'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
		<tr>
		<td>
		'.$html_details.'
		</td>
		</tr>
	 	</table>';
	}
}
else if($option == '5')
{
	$logID = $_POST['ipaddr'];
	
	mysqlSetup($db);
	
	$results = mysql_query("SELECT restapi.name as name, 
								   access_log.allowed,
								   access_log.access_time,
								   access_log.logID,
								   access_log.url_id,
								   restapi_field.id as restapi_field_id,
								   restapi.id as restapi_id
								   from access_log INNER JOIN restapi
										           ON restapi.id = access_log.api_id
										           INNER JOIN restapi_field
										           ON restapi.restapi_field_id = restapi_field.id
								   where access_log.url_id = $logID
								   order by access_log.access_time DESC", $db);
	
	$div_counter = 0;
	$api_list = 'The application makes the following API calls in this page:';
	
	while($row = mysql_fetch_array($results))
	{	
		$name = $row['name'];
	    $allowed = $row['allowed'];
		$access_time = $row['access_time'];
		$logID = $row['logID'];
		$restapi_field_id = $row['restapi_field_id'];
		$restapi_id = $row['restapi_id'];
		
		$allow_html = '<font color="red">Access Denied</font>';
		if($allowed == '1')
		{
			$allow_html = 'Access Allowed';
		}
			
		$api_list .= ' 
		<br /><font style="padding-left: 10px;padding-right: 10px; font-size: 8pt; line-height: 20px;font-family: Verdana, Arial;">
		'.$name.': '.$allow_html.' . '.$access_time.'
		</font>';
		$div_counter++;
	}
	
	if($div_counter == 0)
	{
		echo
		'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
		<tr>
		<td>
		The application does NOT makes any API calls in this page!
		</td>
		</tr>
	 	</table>';
	}
	else
	{
		echo
		'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
		<tr>
		<td>
		'.$api_list.'
		</td>
		</tr>
	 	</table>';
	}
}
?>







