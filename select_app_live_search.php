
<?php

require_once 'func.php';
require_once 'vars.php';

//get the q parameter from URL
$live_search_str = $_POST['searchwords'];
$uid = $_POST['otherval'];
$option = $_POST['option'];
$app_select = $_POST['app_select'];
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
if($app_select == '1') //FBML Only
{
	$app_select_sql_query = 'AND facebook_application.is_canvas = 1';
}
else if($app_select == '2') //IFrame Only
{
	$app_select_sql_query = 'AND facebook_application.is_canvas = 2';
}
else if($app_select == '3') //Facebook Connect Only
{
	$app_select_sql_query = 'AND facebook_application.is_canvas = 3';
}

$match_return = false;
$hint='<table cellpadding="0" cellspacing="10" width="450px">';

//lookup all links from the xml file if length of q>0
if (strlen($live_search_str) > 0)
{
	mysqlSetup($db);
	
	$results = mysql_query("SELECT default_page, 
								   app_name, 
								   app_description,
								   id,
								   is_canvas,
								   (SELECT COUNT(uid) FROM user_disable_app WHERE user_disable_app.uid = $uid 
								   											  AND user_disable_app.app_id = id) as block_app,
								   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.uid = $uid 
												   							  AND user_bookmark_app.app_id = id) as bookmark_app,
								   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.app_id = id) as total_bookmark_app
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
	  	$block_app = $row['block_app'];
		$bookmark_app = $row['bookmark_app'];
	  	$is_canvas = $row['is_canvas'];
	  	
	  	$canvas_str = '<font style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;color: #aa3333;">FBML App</font>';
	  	if($is_canvas == '2')
	  	{
	  		$canvas_str = '<font style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;color: #aa3333;">IFrame App</font>';
	  	}
		else if($is_canvas == '3')
	  	{
	  		$canvas_str = '<font style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;color: #aa3333;">Facebook Connect App</font>';
	  	}
	  	
		$block_me_str = 
	  	'<a '.$iframe_target.' style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;border-right: #AAAAAA 1px solid;text-decoration: underline;" 
	  		href="'.$root_url.'select_app.php?app_id='.$app_id.'">block</a>';
		
		$bookmark_me_str = 
	  	'<a '.$iframe_target.' style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;border-right: #AAAAAA 1px solid;text-decoration: underline;" 
	  		href="'.$root_url.'select_app.php?bkapp_id='.$app_id.'">bookmark</a>';
	  	
	  	if($block_app > 0)
	  	{
	  		$block_me_str = 
	  		'<a '.$iframe_target.' style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;border-right: #AAAAAA 1px solid;color: red;text-decoration: underline;" 
	  			href="'.$root_url.'select_app.php?unapp_id='.$app_id.'">blocked</a>';
	  	}
	  	
		if($bookmark_app > 0)
	  	{
	  		$bookmark_me_str = 
	  		'<a '.$iframe_target.' style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;border-right: #AAAAAA 1px solid;color: #333333;text-decoration: underline;font-weight: bold;" 
	  			href="'.$root_url.'select_app.php?unbkapp_id='.$app_id.'">bookmarked</a>';
	  	}
	  	
	  	$view_history = 
	  	'<a '.$iframe_target.' style="font-size: 8pt;font-family: Verdana, Arial;padding-left: 5px;padding-right: 5px;border-right: #AAAAAA 1px solid;text-decoration: underline;" 
	  		href="'.$facebook_canvas_page_url.'view_history_app.php?app_id='.$app_id.'">view log</a>';
	  	
       	$hint .= 
       	'
       	<tr>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 15px;" width="450px">
    		<br />
       		<label>
       		<a '.$iframe_target.' href="'.$root_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
       		</label><br />
       		'.$app_description.'
       		</td>
       	</tr>
       	<tr aligh="left">
       		<td width="450px" aligh="left" style="border-bottom: #CCCCCC 1px solid; padding-bottom: 10px;">
       		'.$view_history.$block_me_str.$bookmark_me_str.$canvas_str.'
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

?>