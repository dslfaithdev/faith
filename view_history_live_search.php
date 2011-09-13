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
else if($option == '3') // api results
{
	$logID = $_POST['ipaddr'];
	$result = '';
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
		
		$result =
		'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
		<tr>
		<td>
		'.htmlspecialchars($logdetails).'
		</td>
		</tr>';
	}
	
	$replay_detail_results = mysql_query("SELECT allowed,
												 access_time,
												 logdetails
						   		          		 from access_log_replay
						   		          		 where access_log_replay.logID = $logID
						   		          		 order by access_log_replay.access_time DESC", $db);
	
	while($replay_detail_row = mysql_fetch_array($replay_detail_results))
	{	
		$replaylogdetails = $replay_detail_row['logdetails'];
		$replayallowed = $replay_detail_row['allowed'];
		$replayaccess_time = $replay_detail_row['access_time'];
		
		$access_string = "Access Allowed";
		
		if($replayallowed != '1')
		{
			$access_string = "Access Denined";
		}
		
		$result .=
		'<tr>
		<td><font style="color:#AA3333;"><b>Replayed at '.$replayaccess_time.',&nbsp;&nbsp;&nbsp;'.$access_string.'</b><br />
		'.htmlspecialchars($replaylogdetails).'
		</font><br /></td>
		</tr>';
	}
	
	$result .= '</table>';
	echo $result;
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
else if($option == '33') // replay
{
	$logID = $_POST['ipaddr'];
	$post_params = array();
	
	mysqlSetup($db);
	$results = mysql_query("SELECT access_log.uid,
								   access_log.app_id,
								   access_log.parameter,
								   access_log.sessionkey,
								   access_log.replay_type,
								   restapi.name as name
								   from access_log INNER JOIN restapi
										           ON restapi.id = access_log.api_id
								   where access_log.logID = $logID", $db);
	
	$row = mysql_fetch_array($results);
	$post_params[] = 'faith_uid='.urlencode($row['uid']);
	$post_params[] = 'faith_app_id='.urlencode($row['app_id']);
	$post_params[] = 'access_token='.$row['sessionkey'];
	$post_params[] = 'faith_source='.$row['replay_type'];
	$post_params[] = 'replay_lod_id='.$logID;
	
	$parameters = $row['parameter'];
	$parameters = json_decode($parameters, true);
	
	$dsl_arr = array();
	
	foreach($parameters as $para_index => $para_array)
	{
		$dsl_arr[$para_index] = $para_array;
		$post_params[] = $para_index.'='.$para_array;
	}
	
	$postStr = implode('&', $post_params);	
    $opts = array(
	  'http'=>array(
	    'method'=>"POST",
	    'header'=>"Accept-language: en\r\n" .
	              "Cookie: foo=bar\r\n",
		'content'=>$postStr
	  )
	);
		
	$context = stream_context_create($opts);
	
	if($row['replay_type'] == $faith_iframe_replay)
	{
		$replay_result = file_get_contents($source_server_url.'iframerestserver.php', false, $context);
	}
	else if($row['replay_type'] == $faith_fbml_replay)
	{
		$replay_result = file_get_contents($source_server_url.'restserver.php?method='.$parameters['method'].'&session_key='.$row['sessionkey'], false, $context);
	}
	else if($row['replay_type'] == $faith_dsl_replay)
	{
		require_once("soap_replay_lib.php");
		
		if($dsl_arr['method']=='uidToName')
		{
			$replay_result = dsl_uidToName(((int)$dsl_arr['uid']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='nameToUid')
		{
			$replay_result = dsl_nameToUid($dsl_arr['name'], $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='findSocialPath')
		{
			$replay_result = dsl_findSocialPath(((int)$dsl_arr['src']), ((int)$dsl_arr['dest']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='findMultipleSocialPaths')
		{
			$replay_result = dsl_findMultipleSocialPaths(((int)$dsl_arr['src']), ((int)$dsl_arr['dest']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='findTargets')
		{
			$replay_result = dsl_findTargets(((int)$dsl_arr['src']), $dsl_arr['keywords'], $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='setOutcome')
		{
			$replay_result = dsl_setOutcome($dsl_arr['path'], $dsl_arr['outcome'], $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='getReceivedKeywords')
		{
			$replay_result = dsl_getReceivedKeywords(((int)$dsl_arr['uid']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='getFriends')
		{
			$replay_result = dsl_getFriends(((int)$dsl_arr['uid']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='getTrust')
		{
			$replay_result = dsl_getTrust(((int)$dsl_arr['truster']), ((int)$dsl_arr['trustee']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='sendMessage')
		{
			$replay_result = dsl_sendMessage($dsl_arr['path'], $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='addUser')
		{
			$replay_result = dsl_addUser(((int)$dsl_arr['uid']), $dsl_arr['name'], $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='removeUser')
		{
			$replay_result = dsl_removeUser(((int)$dsl_arr['uid']), $logID, $row['uid'], $row['app_id']);
		}
		else if($dsl_arr['method']=='getPic')
		{
			$replay_result = dsl_getPic(((int)$dsl_arr['uid']), $logID, $row['uid'], $row['app_id']);
		}
		
		if(gettype($replay_result) == 'array')
		{
			$array_str = '';
			foreach($replay_result as $index => $value)
			{
				$array_str = $array_str . ' { Array ' . $index . ' -> ';
				
				if(gettype($value) == 'array')
				{
					foreach($value as $inner_index => $inner_value)
					{
						$array_str = $array_str . ' [ Array ' . $inner_index . ' -> ';
						
						if(gettype($inner_value) == 'array')
						{
							foreach($inner_value as $most_inner_index => $most_inner_value)
							{
								$array_str = $array_str . ' ( Array ' . $most_inner_index . ' -> ' . $most_inner_value . ' ) ';
							}
							
							$array_str = $array_str . ' ] ';
						}
						else
						{
							$array_str = $array_str . $inner_value . ' ] ';
						}
					}
				}
				
				$array_str = $array_str . ' } ';
			}
			$replay_result = $array_str;
		}
	}
	
	echo
		'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
		<tr>
		<td>
		'.htmlspecialchars($replay_result).'
		</td>
		</tr>
	 	</table>'; 
}
?>







