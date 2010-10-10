<?php

require_once 'func.php';

$option = $_POST['option'];
	
if($option == '1')
{
	$remove_id = $_POST['remove_id'];
	mysqlSetup($db);
	
	$friend_results = mysql_query("SELECT transform_remove_friend.not_apply_fri_uid,
										  transform_remove_friend.time_added
										  from transform_remove_friend
										  where transform_remove_friend.transform_remove_id = $remove_id
										  order by transform_remove_friend.time_added DESC", $db);
	
	$number_added = 0;
	$removal_friend_list = 'This rule does NOT apply to the following friends:';
	
	while($friend_row = mysql_fetch_array($friend_results))
	{
		$not_apply_fri_uid = $friend_row['not_apply_fri_uid'];
		$removal_friend_list .= ' 
		<font style="padding-left: 10px; padding-right: 10px;line-height: 16px;">
		<fb:name uid="'.$not_apply_fri_uid.'" useyou="false" linked="true" /> 
		</font>';
		$number_added++;
	}
	
	if($number_added == 0)
	{
		$removal_friend_list = 'This rule applies to ALL friends.';
	}
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
	<td>
	'.$removal_friend_list.'
	</td>
	</tr>
 	</table>';
}
if($option == '2')
{
	$remove_id = $_POST['remove_id'];
	mysqlSetup($db);
	
	$application_results = mysql_query("SELECT transform_remove_app.not_apply_app_uid,
										   	   transform_remove_app.time_added,
										       facebook_application.default_page, 
										       facebook_application.app_name, 
										       facebook_application.app_description,
										       facebook_application.id,
										       facebook_application.is_canvas
										       from transform_remove_app, facebook_application
										       where transform_remove_app.not_apply_app_uid = facebook_application.id AND
										             transform_remove_app.transform_remove_id = $remove_id
										       order by transform_remove_app.time_added DESC", $db);
	
	$app_number_added = 0;
	$removal_application_list = 'This rule does NOT apply to the following applications:';
	
	while($application_row = mysql_fetch_array($application_results))
	{
		$not_apply_app_uid = $application_row['not_apply_app_uid'];
		$default_page = $application_row['default_page'];
		$app_name = $application_row['app_name'];
		$app_description = $application_row['app_description'];
		$app_id = $application_row['id'];
		$is_canvas = $application_row['is_canvas'];
		
		$removal_application_list .= ' 
		<font style="padding-left: 10px;padding-right: 10px;">
		<a style="text-decoration: underline; font-size: 8pt; line-height: 20px;font-family: Verdana, Arial;"
       		href="'.$facebook_canvas_page_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
		</font>';
		$app_number_added++;
	}
	
	if($app_number_added == 0)
	{
		$removal_application_list = 'This rule applies to ALL applications.';
	}
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
	<td>
	'.$removal_application_list.'
	</td>
	</tr>
 	</table>';
}
else if($option == '22')
{
	require_once 'vars.php';
	
	$live_search_str = $_POST['searchwords'];
	$uid = $_POST['otherval'];
	$app_select = $_POST['app_select'];
	$remove_id = $_POST['remove_id'];
	$iframe_target = '';
	
	$root_url = $facebook_canvas_page_url;
	
	if($option == $faith_connect)
	{
		$root_url = $source_server_url.'fbc/';
	}
	else if($option == $faith_iframe)
	{
		$root_url = $facebook_iframe_canvas_page_url;
		$iframe_target = 'target="_parent"';
	}
	
	$app_select_sql_query = '';
	if($app_select == '1')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 1';
	}
	else if($app_select == '2')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 2';
	}
	else if($app_select == '3')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 3';
	}
	
	$match_return = false;
	$hint='<table cellpadding="0" cellspacing="10" width="100%">';
	
	//lookup all links from the xml file if length of q>0
	if (strlen($live_search_str) > 0)
	{
		mysqlSetup($db);
		
		$results = mysql_query("SELECT default_page, 
									   app_name, 
									   app_description,
									   id,
									   is_canvas
									   from facebook_application
									   WHERE LOCATE('$live_search_str', LOWER(app_name)) > 0
									   $app_select_sql_query
									   LIMIT 4", $db);
		
		
		
		while($row = mysql_fetch_array($results))
		{
			$match_return = true;
			$default_page = $row['default_page'];
		  	$app_name = $row['app_name'];
		  	$app_description = $row['app_description'];
		  	$app_id = $row['id'];
		  	
		  	$is_canvas = $row['is_canvas'];
		  	
		  	$canvas_str = 'FBML App';
			if($is_canvas == '3')
		  	{
		  		$canvas_str = 'FB Connect App';
		  	}
		  	else if($is_canvas == '2')
		  	{
		  		$canvas_str = 'IFrame App';
		  	}
		  	
	       	$hint .= 
	       	'
	       	<tr style="text-align: left;">
	    		<td style="text-align: left;display: inline;font-family: Verdana, Arial;font-size: 8pt;line-height: 15px;" width="100%">
	    		<form style="display: inline;" action="set_policy_transform_remove_not_app.php?id='.$remove_id.'" method="post">
	    		<input type="hidden" name="remove_id" value="'.$remove_id.'"/>
				<input type="hidden" name="not_apply_app_uid" value="'.$app_id.'"/>
	    		<font style="padding-left: 5px;padding-right: 5px;">
	    		<INPUT type="submit" id="select_application_submit" name = "select_application_submit" value="select" />
	    		</font>
	       		</form>
	    		<label>
	       		<a '.$iframe_target.' href="'.$root_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
	       		</label>
	       		<br />
	       		<font style="padding-left: 15px;font-weight: bold;color: #FF0000;">'.$canvas_str.'</font>
	       		'.$app_description.'
	       		</td>
	       	</tr>';
		}
	}
	
	if (!$match_return)
	{
	  	$hint='<tr><td><br /><h5>&nbsp;&nbsp;&nbsp;no appropriate applications found for this search</h5><br /></td></tr></table>';
	}
	else
	{
		$hint=$hint . '</table>';
	}
	
	//output the response
	echo $hint;
}
if($option == '3')
{
	$add_id = $_POST['add_id'];
	mysqlSetup($db);
	
	$friend_results = mysql_query("SELECT transform_add_friend.not_apply_fri_uid,
										  transform_add_friend.time_added
										  from transform_add_friend
										  where transform_add_friend.transform_add_id = $add_id
										  order by transform_add_friend.time_added DESC", $db);
	
	$number_added = 0;
	$removal_friend_list = 'This rule does NOT apply to the following friends:';
	
	while($friend_row = mysql_fetch_array($friend_results))
	{
		$not_apply_fri_uid = $friend_row['not_apply_fri_uid'];
		$removal_friend_list .= ' 
		<font style="padding-left: 10px; padding-right: 10px;line-height: 16px;">
		<fb:name uid="'.$not_apply_fri_uid.'" useyou="false" linked="true" /> 
		</font>';
		$number_added++;
	}
	
	if($number_added == 0)
	{
		$removal_friend_list = 'This rule applies to ALL friends.';
	}
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
	<td>
	'.$removal_friend_list.'
	</td>
	</tr>
 	</table>';
}
if($option == '4')
{
	$add_id = $_POST['add_id'];
	mysqlSetup($db);
	
	$application_results = mysql_query("SELECT transform_add_app.not_apply_app_uid,
										   	   transform_add_app.time_added,
										       facebook_application.default_page, 
										       facebook_application.app_name, 
										       facebook_application.app_description,
										       facebook_application.id,
										       facebook_application.is_canvas
										       from transform_add_app, facebook_application
										       where transform_add_app.not_apply_app_uid = facebook_application.id AND
										             transform_add_app.transform_add_id = $add_id
										       order by transform_add_app.time_added DESC", $db);
	
	$app_number_added = 0;
	$removal_application_list = 'This rule does NOT apply to the following applications:';
	
	while($application_row = mysql_fetch_array($application_results))
	{
		$not_apply_app_uid = $application_row['not_apply_app_uid'];
		$default_page = $application_row['default_page'];
		$app_name = $application_row['app_name'];
		$app_description = $application_row['app_description'];
		$app_id = $application_row['id'];
		$is_canvas = $application_row['is_canvas'];
		
		$removal_application_list .= ' 
		<font style="padding-left: 10px;padding-right: 10px;">
		<a style="text-decoration: underline; font-size: 8pt; line-height: 20px;font-family: Verdana, Arial;"
       		href="'.$facebook_canvas_page_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
		</font>';
		$app_number_added++;
	}
	
	if($app_number_added == 0)
	{
		$removal_application_list = 'This rule applies to ALL applications.';
	}
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
	<td>
	'.$removal_application_list.'
	</td>
	</tr>
 	</table>';
}
else if($option == '44')
{
	require_once 'vars.php';
	
	$live_search_str = $_POST['searchwords'];
	$uid = $_POST['otherval'];
	$app_select = $_POST['app_select'];
	$add_id = $_POST['add_id'];
	$iframe_target = '';
	
	$root_url = $facebook_canvas_page_url;
	
	if($option == $faith_connect)
	{
		$root_url = $source_server_url.'fbc/';
	}
	else if($option == $faith_iframe)
	{
		$root_url = $facebook_iframe_canvas_page_url;
		$iframe_target = 'target="_parent"';
	}
	
	$app_select_sql_query = '';
	if($app_select == '1')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 1';
	}
	else if($app_select == '2')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 2';
	}
	else if($app_select == '2')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 3';
	}
	
	$match_return = false;
	$hint='<table cellpadding="0" cellspacing="10" width="100%">';
	
	//lookup all links from the xml file if length of q>0
	if (strlen($live_search_str) > 0)
	{
		mysqlSetup($db);
		
		$results = mysql_query("SELECT default_page, 
									   app_name, 
									   app_description,
									   id,
									   is_canvas
									   from facebook_application
									   WHERE LOCATE('$live_search_str', LOWER(app_name)) > 0
									   $app_select_sql_query
									   LIMIT 4", $db);
		
		
		
		while($row = mysql_fetch_array($results))
		{
			$match_return = true;
			$default_page = $row['default_page'];
		  	$app_name = $row['app_name'];
		  	$app_description = $row['app_description'];
		  	$app_id = $row['id'];
		  	
		  	$is_canvas = $row['is_canvas'];
		  	
		  	$canvas_str = 'FBML App';
			if($is_canvas == '3')
		  	{
		  		$canvas_str = 'FB Connect App';
		  	}
		  	else if($is_canvas == '2')
		  	{
		  		$canvas_str = 'IFrame App';
		  	}
		  	
	       	$hint .= 
	       	'
	       	<tr style="text-align: left;">
	    		<td style="text-align: left;display: inline;font-family: Verdana, Arial;font-size: 8pt;line-height: 15px;" width="100%">
	    		<form style="display: inline;" action="set_policy_transform_add_not_app.php?id='.$add_id.'" method="post">
	    		<input type="hidden" name="add_id" value="'.$add_id.'"/>
				<input type="hidden" name="not_apply_app_uid" value="'.$app_id.'"/>
	    		<font style="padding-left: 5px;padding-right: 5px;">
	    		<INPUT type="submit" id="select_application_submit" name = "select_application_submit" value="select" />
	    		</font>
	       		</form>
	    		<label>
	       		<a '.$iframe_target.' href="'.$root_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
	       		</label>
	       		<br />
	       		<font style="padding-left: 15px;font-weight: bold;color: #FF0000;">'.$canvas_str.'</font>
	       		'.$app_description.'
	       		</td>
	       	</tr>';
		}
	}
	
	if (!$match_return)
	{
	  	$hint='<tr><td><br /><h5>&nbsp;&nbsp;&nbsp;no appropriate applications found for this search</h5><br /></td></tr></table>';
	}
	else
	{
		$hint=$hint . '</table>';
	}
	
	//output the response
	echo $hint;
}
if($option == '5')
{
	$user_id = $_POST['user_id'];
	$api_id = $_POST['api_id'];
	mysqlSetup($db);
	
	$friend_results = mysql_query("SELECT user_disable_api_friend.not_apply_fri_uid,
										  user_disable_api_friend.time_added
										  from user_disable_api_friend
										  where user_disable_api_friend.uid = $user_id AND
										  		user_disable_api_friend.restapi_id = $api_id
										  order by user_disable_api_friend.time_added DESC", $db);
	
	$number_added = 0;
	$removal_friend_list = 'This rule does NOT apply to the following friends:';
	
	while($friend_row = mysql_fetch_array($friend_results))
	{
		$not_apply_fri_uid = $friend_row['not_apply_fri_uid'];
		$removal_friend_list .= ' 
		<font style="padding-left: 10px; padding-right: 10px;line-height: 16px;">
		<fb:name uid="'.$not_apply_fri_uid.'" useyou="false" linked="true" /> 
		</font>';
		$number_added++;
	}
	
	if($number_added == 0)
	{
		$removal_friend_list = 'This rule applies to ALL friends.';
	}
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
	<td>
	'.$removal_friend_list.'
	</td>
	</tr>
 	</table>';
}
if($option == '6')
{
	$user_id = $_POST['user_id'];
	$api_id = $_POST['api_id'];
	mysqlSetup($db);
	
	$application_results = mysql_query("SELECT user_disable_api_app.not_apply_app_uid,
										   	   user_disable_api_app.time_added,
										       facebook_application.default_page, 
										       facebook_application.app_name, 
										       facebook_application.app_description,
										       facebook_application.id,
										       facebook_application.is_canvas
										       from user_disable_api_app, facebook_application
										       where user_disable_api_app.not_apply_app_uid = facebook_application.id AND
										             user_disable_api_app.uid = $user_id AND
										             user_disable_api_app.restapi_id = $api_id
										       order by user_disable_api_app.time_added DESC", $db);
	
	$app_number_added = 0;
	$removal_application_list = 'This rule does NOT apply to the following applications:';
	
	while($application_row = mysql_fetch_array($application_results))
	{
		$not_apply_app_uid = $application_row['not_apply_app_uid'];
		$default_page = $application_row['default_page'];
		$app_name = $application_row['app_name'];
		$app_description = $application_row['app_description'];
		$app_id = $application_row['id'];
		$is_canvas = $application_row['is_canvas'];
		
		$removal_application_list .= ' 
		<font style="padding-left: 10px;padding-right: 10px;">
		<a style="text-decoration: underline; font-size: 8pt; line-height: 20px;font-family: Verdana, Arial;"
       		href="'.$facebook_canvas_page_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
		</font>';
		$app_number_added++;
	}
	
	if($app_number_added == 0)
	{
		$removal_application_list = 'This rule applies to ALL applications.';
	}
	
	echo
	'<table cellpadding="0" cellspacing="10" width="100%" style="font-size: 10pt;">
	<tr>
	<td>
	'.$removal_application_list.'
	</td>
	</tr>
 	</table>';
}
else if($option == '66')
{
	require_once 'vars.php';
	
	$live_search_str = $_POST['searchwords'];
	$uid = $_POST['otherval'];
	$app_select = $_POST['app_select'];
	$api_id = $_POST['api_id'];
	$iframe_target = '';
	
	$root_url = $facebook_canvas_page_url;
	
	if($option == $faith_connect)
	{
		$root_url = $source_server_url.'fbc/';
	}
	else if($option == $faith_iframe)
	{
		$root_url = $facebook_iframe_canvas_page_url;
		$iframe_target = 'target="_parent"';
	}
	
	$app_select_sql_query = '';
	if($app_select == '1')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 1';
	}
	else if($app_select == '2')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 2';
	}
	else if($app_select == '2')
	{
		$app_select_sql_query = 'AND facebook_application.is_canvas = 3';
	}
	
	$match_return = false;
	$hint='<table cellpadding="0" cellspacing="10" width="100%">';
	
	//lookup all links from the xml file if length of q>0
	if (strlen($live_search_str) > 0)
	{
		mysqlSetup($db);
		
		$results = mysql_query("SELECT default_page, 
									   app_name, 
									   app_description,
									   id,
									   is_canvas
									   from facebook_application
									   WHERE LOCATE('$live_search_str', LOWER(app_name)) > 0
									   $app_select_sql_query
									   LIMIT 4", $db);
		
		
		
		while($row = mysql_fetch_array($results))
		{
			$match_return = true;
			$default_page = $row['default_page'];
		  	$app_name = $row['app_name'];
		  	$app_description = $row['app_description'];
		  	$app_id = $row['id'];
		  	
		  	$is_canvas = $row['is_canvas'];
		  	
		  	$canvas_str = 'FBML App';
			if($is_canvas == '3')
		  	{
		  		$canvas_str = 'FB Connect App';
		  	}
		  	else if($is_canvas == '2')
		  	{
		  		$canvas_str = 'IFrame App';
		  	}
		  	
	       	$hint .= 
	       	'
	       	<tr style="text-align: left;">
	    		<td style="text-align: left;display: inline;font-family: Verdana, Arial;font-size: 8pt;line-height: 15px;" width="100%">
	    		<form style="display: inline;" action="set_policy_not_app.php?api_id='.$api_id.'" method="post">
	    		<input type="hidden" name="api_id" value="'.$api_id.'"/>
				<input type="hidden" name="not_apply_app_uid" value="'.$app_id.'"/>
	    		<font style="padding-left: 5px;padding-right: 5px;">
	    		<INPUT type="submit" id="select_application_submit" name = "select_application_submit" value="select" />
	    		</font>
	       		</form>
	    		<label>
	       		<a '.$iframe_target.' href="'.$root_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
	       		</label>
	       		<br />
	       		<font style="padding-left: 15px;font-weight: bold;color: #FF0000;">'.$canvas_str.'</font>
	       		'.$app_description.'
	       		</td>
	       	</tr>';
		}
	}
	
	if (!$match_return)
	{
	  	$hint='<tr><td><br /><h5>&nbsp;&nbsp;&nbsp;no appropriate applications found for this search</h5><br /></td></tr></table>';
	}
	else
	{
		$hint=$hint . '</table>';
	}
	
	//output the response
	echo $hint;
}

?>







