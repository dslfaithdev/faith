<?php

require_once 'vars.php';
require_once 'facebook.php';
require_once 'func.php';

try
{
	$myFile = $source_folder_path."testFile.txt";
	$fh = fopen($myFile, 'a');
	$stringData = "restserver.php Called!\n";
	fwrite($fh, $stringData);
	
	$facebook;
	if(isset($_POST['faith_source']) && 
	  ($_POST['faith_source'] == $faith_fbml) || ($_POST['faith_source'] == $faith_fbml_replay)) 		//*FAITH*
	{
		$facebook = new Facebook($appapikey, $appsecret);
	}
	else if(isset($_POST['faith_source']) && $_POST['faith_source'] == $faith_connect)	//*FAITH*
	{
		$facebook = new Facebook($appapikey, $appsecret);
	}
  	else if(isset($_POST['faith_source']) && $_POST['faith_source'] == $faith_iframe)	//*FAITH*
	{
		$facebook = new Facebook($iframe_appapikey, $iframe_appsecret);
	}
	
	$facebook->api_client->Set_Is_FAITH_REST(true);
	
	if(!isset($_POST['faith_app_id']) || !isset($_POST['faith_uid']))
    {
    	exit();
    }
    
    mysqlSetup($db);
    
    $faith_app_id = $_POST['faith_app_id'];
    $faith_uid = $_POST['faith_uid'];
    $faith_url_id = $_POST['faith_url_id'];
	$api_method = $_GET['method'];
    $faith_client_ip = $_POST['faith_client_ip'];
    $app_ip_addr = $_SERVER['REMOTE_ADDR'];
    
	$api_blocked = mysql_query("SELECT Count(*) as CountAPI_Num
						   		from user_disable_api AS DISABLEAPI, restapi
						   		where DISABLEAPI.uid = $faith_uid AND
						   		 	  restapi.facebook_method = '$api_method' AND
						   			  DISABLEAPI.restapi_id = restapi.id AND
						   			  NOT EXISTS
							    	  (
							    	  	SELECT not_apply_app_uid
							    	  	from user_disable_api_app AS TRANREMAPP
							    	  	where TRANREMAPP.uid = $faith_uid AND
							    	  		  TRANREMAPP.restapi_id = DISABLEAPI.restapi_id AND
							    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
							    	  )", $db);
	
	$api_blocked_row = mysql_fetch_array($api_blocked);
	$CountAPI_Num = $api_blocked_row['CountAPI_Num'];
	
	$app_blocked = mysql_query("SELECT Count(*) as CountAPP_Num
						   		from user_disable_app
						   		where user_disable_app.uid = $faith_uid AND
	            					  user_disable_app.app_id = $faith_app_id;", $db);
	
	$app_blocked_row = mysql_fetch_array($app_blocked);
	$CountAPP_Num = $app_blocked_row['CountAPP_Num'];
	
	$Count_Num = $CountAPI_Num + $CountAPP_Num;
	
	$allowed = '1';
	if($Count_Num > 0)
	{
		$allowed = '0';
	}
	
	$result = '';
	
	if($_GET['method'] == 'facebook.admin.getAllocation')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.admin.getAllocation');
  		$api_array['integration_point_name'] = $_POST['integration_point_name'];
  		$api_array['uids'] = $_POST['uids'];
  		
		$result = $facebook->api_client->admin_getAllocation($_POST['integration_point_name'], $_POST['uids']);
	}
	else if($_GET['method'] == 'facebook.admin.getAppProperties')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.admin.getAppProperties');
  		$api_array['properties'] = $_POST['properties'];
  		
		$result = $facebook->api_client->admin_getAppProperties($_POST['properties']);
	}
	else if($_GET['method'] == 'facebook.admin.getMetrics')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.admin.getMetrics');
  		$api_array['start_time'] = $_POST['start_time'];
  		$api_array['end_time'] = $_POST['end_time'];
  		$api_array['period'] = $_POST['period'];
  		$api_array['metrics'] = $_POST['metrics'];
  		
		$result = $facebook->api_client->admin_getMetrics($_POST['start_time'], $_POST['end_time'], $_POST['period'], $_POST['metrics']);
	}
	else if($_GET['method'] == 'facebook.admin.getRestrictionInfo')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.admin.getRestrictionInfo');
  		
		$result = $facebook->api_client->admin_getRestrictionInfo();
	}
	else if($_GET['method'] == 'facebook.admin.getBannedUsers')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.admin.getBannedUsers');
  		$api_array['uids'] = $_POST['uids'];
  		
		$result = $facebook->api_client->admin_getBannedUsers($_POST['uids']);
	}
	else if($_GET['method'] == 'facebook.application.getPublicInfo')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.application.getPublicInfo');
  		$api_array['application_id'] = $_POST['application_id'];
  		$api_array['application_api_key'] = $_POST['application_api_key'];
  		$api_array['application_canvas_name'] = $_POST['application_canvas_name'];
  		
		$result = $facebook->api_client->application_getPublicInfo($_POST['application_id'], 
																   $_POST['application_api_key'], 
																   $_POST['application_canvas_name']);
	}
	else if($_GET['method'] == 'facebook.data.setCookie')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.data.setCookie');
  			$api_array['uid'] = $_POST['uid'];
  			$api_array['name'] = $_POST['name'];
  			$api_array['value'] = $_POST['value'];
  			$api_array['expires'] = $_POST['expires'];
  			$api_array['path'] = $_POST['path'];
  		
			$result = $facebook->api_client->data_setCookie($_POST['uid'], 
															$_POST['name'],
															$_POST['value'],
															$_POST['expires'],
															$_POST['path']);
	  	}
	}
	else if($_GET['method'] == 'facebook.data.getCookies')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.data.getCookies');
  		$api_array['uid'] = $_POST['uid'];
  		$api_array['name'] = $_POST['name'];
  		
		$result = $facebook->api_client->data_getCookies($_POST['uid'], 
														 $_POST['name']);
	}
	else if($_GET['method'] == 'facebook.fbml.setRefHandle')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.fbml.setRefHandle');
  			$api_array['handle'] = $_POST['handle'];
  			$api_array['fbml'] = $_POST['fbml'];
  			
			$result = $facebook->api_client->fbml_setRefHandle($_POST['handle'], 
															   $_POST['fbml']);
	  	}
	}
	else if($_GET['method'] == 'facebook.intl.uploadNativeStrings')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.intl.uploadNativeStrings');
  			$api_array['native_strings'] = $_POST['native_strings'];
  			
			$result = $facebook->api_client->intl_uploadNativeStrings($_POST['native_strings']);
	  	}
	}
	else if($_GET['method'] == 'facebook.notifications.sendEmail')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  		
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.notifications.sendEmail');
  			$api_array['recipients'] = $_POST['recipients'];
  			$api_array['subject'] = $_POST['subject'];
  			$api_array['text'] = $_POST['text'];
  			$api_array['fbml'] = $_POST['fbml'];
  			
	  		$result = $facebook->api_client->notifications_sendEmail($_POST['recipients'],
																	 $_POST['subject'],
																	 $_POST['text'],
																	 $_POST['fbml']);
	  	}
	}
	else if($_GET['method'] == 'facebook.comments.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.comments.get');
  		$api_array['xid'] = $_POST['xid'];
  			
		$result = $facebook->api_client->comments_get($_POST['xid']);
	}
	else if($_GET['method'] == 'facebook.comments.add')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.comments.add');
  			$api_array['xid'] = $_POST['xid'];
  			$api_array['text'] = $_POST['text'];
  			$api_array['uid'] = $_POST['uid'];
  			$api_array['title'] = $_POST['title'];
  			$api_array['url'] = $_POST['url'];
  			$api_array['publish_to_stream'] = $_POST['publish_to_stream'];
  		
			$result = $facebook->api_client->comments_add($_POST['xid'],
														  $_POST['text'],
														  $_POST['uid'],
														  $_POST['title'],
														  $_POST['url'],
														  $_POST['publish_to_stream']);
	  	}
	}
	else if($_GET['method'] == 'facebook.comments.remove')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.comments.remove');
  			$api_array['xid'] = $_POST['xid'];
  			$api_array['comment_id'] = $_POST['comment_id'];
  			
			$result = $facebook->api_client->comments_remove($_POST['xid'],
														  	 $_POST['comment_id']);
	  	}
	}
	else if($_GET['method'] == 'facebook.fbml.refreshImgSrc')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.fbml.refreshImgSrc');
  			$api_array['url'] = $_POST['url'];
  			
			$result = $facebook->api_client->fbml_refreshImgSrc($_POST['url']);
	  	}
	}
	else if($_GET['method'] == 'facebook.fbml.refreshRefUrl')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.fbml.refreshRefUrl');
  			$api_array['url'] = $_POST['url'];
  			
			$result = $facebook->api_client->fbml_refreshRefUrl($_POST['url']);
	  	}
	}
	else if($_GET['method'] == 'facebook.fql.query')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.fql.query');
  		$api_array['query'] = $_POST['query'];
  			
		$result = $facebook->api_client->fql_query($_POST['query']);
		
		$block_list_results = mysql_query("SELECT uid, blocked_uid FROM user_blocked_friend 
    															   WHERE user_blocked_friend.blocked_uid = $faith_uid;", $db);
		
		//--------------------------------------------------------------------------------------------------
		
		$fql_query_str = $_POST['query'];
		
		if(!strripos($result, 'error_code') &&	
	   		substr_count(strtolower($fql_query_str), 'select') == '1') 
		{
			if(strripos($fql_query_str, 'friendlist_member'))	
			{
				$remove_list = '<r>default</r>';
				
				$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
											   					   remove_uid_b
															       from transform_remove AS TRANREM
															       where TRANREM.remove_uid_a = $faith_uid AND
															    	  NOT EXISTS
															    	  (
															    	  	SELECT transform_remove_id,
															    	  		   not_apply_app_uid
															    	  	from transform_remove_app AS TRANREMAPP
															    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
															    	  ) AND
															    	  NOT EXISTS
															    	  (
															    	  	SELECT transform_remove_id,
															    	  		   not_apply_fri_uid
															    	  	from transform_remove_friend AS TRANREMFRI
															    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
															    	   )", $db);
				
				while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
				{
					$remove_list .= '<r>'.$list_remove_by_user_row['remove_uid_b'].'</r>';
				}
				
				$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
											   					remove_uid_a
															    from transform_remove AS TRANREM
															    where TRANREM.remove_uid_b = $faith_uid AND
															    	  NOT EXISTS
															    	  (
															    	  	SELECT transform_remove_id,
															    	  		   not_apply_app_uid
															    	  	from transform_remove_app AS TRANREMAPP
															    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
															    	  ) AND
															    	  NOT EXISTS
															    	  (
															    	  	SELECT transform_remove_id,
															    	  		   not_apply_fri_uid
															    	  	from transform_remove_friend AS TRANREMFRI
															    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
															    	   )", $db);
				
				while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
				{
					$remove_list .= '<r>'.$list_remove_by_user_row['remove_uid_a'].'</r>';
				}
				
				$xml_remove_list = simplexml_load_string($result);
				$NEW_xml_remove_list = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><fql_query_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" list="true"></fql_query_response>');
				
				foreach ($xml_remove_list->children() as $xml_remove_child)
				{
					if(strripos($remove_list, '<r>'.$xml_remove_child->uid.'</r>') == FALSE)
					{
						$new_child = $NEW_xml_remove_list->addChild($xml_remove_child->getName());
						foreach($xml_remove_child->children() as $a => $b) 
						{ 
						    $new_child->addChild($a, $b);
						} 
					}
				}
				
				$result = $NEW_xml_remove_list->asXML();
				$result = str_replace('><', '> <', $result);
			}
			else if(strripos($fql_query_str, 'friend') ||
			   		strripos($fql_query_str, 'standard_friend_info'))
			{
				$query_uid_a; 
				$query_uid_b;
				$query_uid_a_name; 
				$query_uid_b_name;
				
				$fql_query_str = strtolower($fql_query_str);	
				
				list($before_where, $after_where) = explode('where', $fql_query_str);	
				
				if(substr_count($after_where, 'or') == '0' &&		
				   substr_count($after_where, 'and') <= 1 &&		 
				   substr_count($after_where, '!=') == '0')				
				{
					if(substr_count($after_where, 'limit') > 0)
					{
						list($after_where, $amount_limited) = explode('limit', $after_where);
					}
					
					list($query_uid_a, $query_uid_b) = explode('and', $after_where);
					
					if($query_uid_a != NULL)
					{	
						if(substr_count($query_uid_a, 'uid1') == '1')
						{
							$query_uid_a_name = 'uid1';
						}
						else if(substr_count($query_uid_a, 'uid2') == '1')
						{
							$query_uid_a_name = 'uid2';
						}
						
						if(substr_count($query_uid_a, 'me') == '1')
						{
							$query_uid_a = $faith_uid;
						}
						else
						{
							$query_uid_a = str_replace('uid1', '', $query_uid_a);
							$query_uid_a = str_replace('uid2', '', $query_uid_a);
							$query_uid_a = preg_replace("/[^0-9]/", '', $query_uid_a); // eliminate everything that is not a number 
						}
					}
					
					if($query_uid_b != NULL)
					{
						if(substr_count($query_uid_b, 'uid1') == '1')
						{
							$query_uid_b_name = 'uid1';
						}
						else if(substr_count($query_uid_b, 'uid2') == '1')
						{
							$query_uid_b_name = 'uid2';
						}
						
						if(substr_count($query_uid_b, 'me') == '1')
						{
							$query_uid_b = $faith_uid;
						}
						else
						{
							$query_uid_b = str_replace('uid1', '', $query_uid_b);
							$query_uid_b = str_replace('uid2', '', $query_uid_b);
							$query_uid_b = preg_replace("/[^0-9]/", '', $query_uid_b);  
						}
					}
				}
				
				if(isset($query_uid_a) && isset($query_uid_b) && $query_uid_a != $query_uid_b) 	
				{
					$xml_list = simplexml_load_string($result);
					
					if(count($xml_list) == 1) 
					{
						$remove_friendship = false;
						
						$areFriends_remove_by_user_results = mysql_query("SELECT transform_remove_id,
			    																 remove_uid_a,
											   					   				 remove_uid_b
															       				 from transform_remove AS TRANREM
															       				 where TRANREM.remove_uid_a = $query_uid_a AND
															       				 	   TRANREM.remove_uid_b = $query_uid_b AND
																	    	     NOT EXISTS
																	    	     (
																	    	  	  	SELECT transform_remove_id,
																	    	  		   	   not_apply_app_uid
																	    	  			   from transform_remove_app AS TRANREMAPP
																	    	  			   where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
																	    	  		  			 TRANREMAPP.not_apply_app_uid = $faith_app_id
																	    	  	 ) AND
																	    	  	 NOT EXISTS
																	    	  	 (
																	    	  		SELECT transform_remove_id,
																	    	  		   	   not_apply_fri_uid
																	    	  			   from transform_remove_friend AS TRANREMFRI
																	    	  			   where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
																	    	  		  			 TRANREMFRI.not_apply_fri_uid = $faith_uid
																	    	     )", $db);
				
						while($areFriends_remove_by_user_row = mysql_fetch_array($areFriends_remove_by_user_results)) 
						{
							$remove_friendship = true;	
						}
						
			    		$areFriends_remove_by_user_results = mysql_query("SELECT transform_remove_id,
			    																 remove_uid_a,
											   					   				 remove_uid_b
															       				 from transform_remove AS TRANREM
															       				 where TRANREM.remove_uid_a = $query_uid_b AND
															       				 	   TRANREM.remove_uid_b = $query_uid_a AND
																	    	     NOT EXISTS
																	    	     (
																	    	  	  	SELECT transform_remove_id,
																	    	  		   	   not_apply_app_uid
																	    	  			   from transform_remove_app AS TRANREMAPP
																	    	  			   where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
																	    	  		  			 TRANREMAPP.not_apply_app_uid = $faith_app_id
																	    	  	 ) AND
																	    	  	 NOT EXISTS
																	    	  	 (
																	    	  		SELECT transform_remove_id,
																	    	  		   	   not_apply_fri_uid
																	    	  			   from transform_remove_friend AS TRANREMFRI
																	    	  			   where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
																	    	  		  			 TRANREMFRI.not_apply_fri_uid = $faith_uid
																	    	     )", $db);
				
						while($areFriends_remove_by_user_row = mysql_fetch_array($areFriends_remove_by_user_results))	// if b removes a as friend, set not friend
						{
							$remove_friendship = true;	
						}
						
						if($remove_friendship)
						{
							$result = '<?xml version="1.0" encoding="UTF-8"?> <fql_query_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" list="true"/>';
						}
					}
					else if(count($xml_list) == 0)	
					{
						$add_friendship = false;
						
						$areFriends_add_by_user_results = mysql_query("SELECT transform_add_id,
				    														  add_uid_a,
												   							  add_uid_b
																    		  from transform_add AS TRANADD
																    		  where TRANADD.status = 1 AND
																    		  		TRANADD.add_uid_a = $query_uid_a AND
																    		  	    TRANADD.add_uid_b = $query_uid_b AND
																    	  	  NOT EXISTS
																    	  	  (
																    	  	  	SELECT transform_add_id,
																    	  		   	   not_apply_app_uid
																    	  			   from transform_add_app AS TRANADDAPP
																    	  			   where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
																    	  		  	   TRANADDAPP.not_apply_app_uid = $faith_app_id
																    	  	  ) AND
																    	  	  NOT EXISTS
																    	  	  (
																    	  		SELECT transform_add_id,
																    	  		   	   not_apply_fri_uid
																    	  			   from transform_add_friend AS TRANADDFRI
																    	  			   where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
																    	  		  			 TRANADDFRI.not_apply_fri_uid = $faith_uid
																    	      )", $db);
					
						while($areFriends_add_by_user_row = mysql_fetch_array($areFriends_add_by_user_results))
						{
							$add_friendship = true;
						}
						
			    		$areFriends_add_by_user_results = mysql_query("SELECT transform_add_id,
				    														  add_uid_a,
												   							  add_uid_b
																    		  from transform_add AS TRANADD
																    		  where TRANADD.status = 1 AND
																    		  		TRANADD.add_uid_a = $query_uid_b AND
																    		  	    TRANADD.add_uid_b = $query_uid_a AND
																    	  	  NOT EXISTS
																    	  	  (
																    	  	  	SELECT transform_add_id,
																    	  		   	   not_apply_app_uid
																    	  			   from transform_add_app AS TRANADDAPP
																    	  			   where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
																    	  		  	   TRANADDAPP.not_apply_app_uid = $faith_app_id
																    	  	  ) AND
																    	  	  NOT EXISTS
																    	  	  (
																    	  		SELECT transform_add_id,
																    	  		   	   not_apply_fri_uid
																    	  			   from transform_add_friend AS TRANADDFRI
																    	  			   where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
																    	  		  			 TRANADDFRI.not_apply_fri_uid = $faith_uid
																    	      )", $db);
					
						while($areFriends_add_by_user_row = mysql_fetch_array($areFriends_add_by_user_results))
						{
							$add_friendship = true;
						}
						
						if($add_friendship)
						{
							$uid_a_pos = stripos($before_where, $query_uid_a_name);
							$uid_b_pos = stripos($before_where, $query_uid_b_name);
							
							$NEW_xml_list = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><fql_query_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" list="true"></fql_query_response>');
							$new_child = $NEW_xml_list->addChild('friend_info');
							
							if($uid_a_pos && $uid_b_pos)
							{
								if($uid_a_pos > $uid_b_pos)
								{
									$new_child->addChild($query_uid_b_name, $query_uid_b);
									$new_child->addChild($query_uid_a_name, $query_uid_a);
								}
								else
								{
									$new_child->addChild($query_uid_a_name, $query_uid_a);
									$new_child->addChild($query_uid_b_name, $query_uid_b);
								}
							}
							else if($uid_a_pos)
							{
								$new_child->addChild($query_uid_a_name, $query_uid_a);
							}
							else if($uid_b_pos)
							{
								$new_child->addChild($query_uid_b_name, $query_uid_b);
							}
							
							$result = $NEW_xml_list->asXML();
							$result = str_replace('><', '> <', $result);
						}
					}
				}
				else if(isset($query_uid_a) && !isset($query_uid_b))	
				{
					$add_list = '';
					$remove_list = '<r>default</r>';
					
					$list_add_by_user_results = mysql_query("SELECT transform_add_id,
												   					add_uid_b
																    from transform_add AS TRANADD
																    where TRANADD.status = 1 AND
																    	  TRANADD.add_uid_a = $query_uid_a AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_app_uid
																    	  	from transform_add_app AS TRANADDAPP
																    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_add_friend AS TRANADDFRI
																    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
					{
						$add_uid_b = $list_add_by_user_row['add_uid_b'];
						$add_list .= $add_uid_b . ',';
					}
					
					$list_add_by_user_results = mysql_query("SELECT transform_add_id,
												   					add_uid_a
																    from transform_add AS TRANADD
																    where TRANADD.status = 1 AND
																    	  TRANADD.add_uid_b = $query_uid_a AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_app_uid
																    	  	from transform_add_app AS TRANADDAPP
																    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_add_friend AS TRANADDFRI
																    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
					{
						$add_uid_a = $list_add_by_user_row['add_uid_a'];
						$add_list .= $add_uid_a . ',';
					}
					
					$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
												   					   remove_uid_b
																       from transform_remove AS TRANREM
																       where TRANREM.remove_uid_a = $query_uid_a AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_app_uid
																    	  	from transform_remove_app AS TRANREMAPP
																    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_remove_friend AS TRANREMFRI
																    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
					{
						if(substr_count($remove_list, '<r>'.$list_remove_by_user_row['remove_uid_b'].'</r>') == '0')
						{
							$remove_list .= '<r>'.$list_remove_by_user_row['remove_uid_b'].'</r>';
						}
					}
					
					$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
												   					remove_uid_a
																    from transform_remove AS TRANREM
																    where TRANREM.remove_uid_b = $query_uid_a AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_app_uid
																    	  	from transform_remove_app AS TRANREMAPP
																    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_remove_friend AS TRANREMFRI
																    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
					{
						if(substr_count($remove_list, '<r>'.$list_remove_by_user_row['remove_uid_a'].'</r>') == '0')
						{
							$remove_list .= '<r>'.$list_remove_by_user_row['remove_uid_a'].'</r>';
						}
					}
					
					
					$xml_list = simplexml_load_string($result);
					$NEW_xml_list = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><fql_query_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" list="true"></fql_query_response>');
					
					$uid_arr_to_add = explode(',', $add_list);
					$xml_child_nodes = $xml_list->children();
					
					foreach($uid_arr_to_add as $uid_to_add)	
					{
						if($uid_to_add != '' &&
						   !strripos($result, '<uid1>'.$uid_to_add.'</uid1>') &&
						   !strripos($result, '<uid2>'.$uid_to_add.'</uid2>'))
						{
							$new_child = $NEW_xml_list->addChild($xml_child_nodes[0]->getName());
							foreach($xml_child_nodes[0]->children() as $a => $b) 
							{
								if($b != $query_uid_a)
								{ 
							    	$new_child->addChild($a, $uid_to_add);
								}
								else
								{
									$new_child->addChild($a, $b);
								}
							} 
						}
					}
					
					foreach ($xml_child_nodes as $xml_child) 
					{
						if(strripos($remove_list, '<r>'.$xml_child->uid1.'</r>') == FALSE &&
						   strripos($remove_list, '<r>'.$xml_child->uid2.'</r>') == FALSE)
						{
							$new_child = $NEW_xml_list->addChild($xml_child->getName());
							foreach($xml_child->children() as $a => $b) 
							{ 
							    $new_child->addChild($a, $b);
							} 
						}
					}
					
					$result = $NEW_xml_list->asXML();
					$result = str_replace('><', '> <', $result);
					
				}
			}
			else if(strripos($fql_query_str, 'connection'))
			{
				$query_source_id; 
				
				$fql_query_str = strtolower($fql_query_str);	
				
				list($before_where, $query_source_id) = explode('where', $fql_query_str);	
				
				if(substr_count($query_source_id, 'or') == '0' &&		
				   substr_count($query_source_id, 'and') == '0')			
				{
					if(substr_count($query_source_id, 'limit') > 0)
					{
						list($query_source_id, $amount_limited) = explode('limit', $query_source_id);
					}
					
					if(substr_count($query_uid_a, 'me') == '1')
					{
						$query_uid_a = $faith_uid;
					}
					else
					{
						$query_source_id = str_replace('source_id', '', $query_source_id);
						$query_source_id = preg_replace("/[^0-9]/", '', $query_source_id); 
					}
				}
				
				if(isset($query_source_id) && strlen($query_source_id) > 0)	
				{
					$add_list = '';
					$remove_list = '<r>default</r>';
					
					$list_add_by_user_results = mysql_query("SELECT transform_add_id,
												   					add_uid_b
																    from transform_add AS TRANADD
																    where TRANADD.status = 1 AND
																    	  TRANADD.add_uid_a = $query_source_id AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_app_uid
																    	  	from transform_add_app AS TRANADDAPP
																    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_add_friend AS TRANADDFRI
																    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
					{
						$add_uid_b = $list_add_by_user_row['add_uid_b'];
						$add_list .= $add_uid_b . ',';
					}
					
					$list_add_by_user_results = mysql_query("SELECT transform_add_id,
												   					add_uid_a
																    from transform_add AS TRANADD
																    where TRANADD.status = 1 AND
																    	  TRANADD.add_uid_b = $query_source_id AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_app_uid
																    	  	from transform_add_app AS TRANADDAPP
																    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_add_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_add_friend AS TRANADDFRI
																    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
																    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
					{
						$add_uid_a = $list_add_by_user_row['add_uid_a'];
						$add_list .= $add_uid_a . ',';
					}
					
					$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
												   					   remove_uid_b
																       from transform_remove AS TRANREM
																       where TRANREM.remove_uid_a = $query_source_id AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_app_uid
																    	  	from transform_remove_app AS TRANREMAPP
																    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_remove_friend AS TRANREMFRI
																    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
					{
						if(substr_count($remove_list, '<r>'.$list_remove_by_user_row['remove_uid_b'].'</r>') == '0')
						{
							$remove_list .= '<r>'.$list_remove_by_user_row['remove_uid_b'].'</r>';
						}
					}
					
					$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
												   					remove_uid_a
																    from transform_remove AS TRANREM
																    where TRANREM.remove_uid_b = $query_source_id AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_app_uid
																    	  	from transform_remove_app AS TRANREMAPP
																    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
																    	  ) AND
																    	  NOT EXISTS
																    	  (
																    	  	SELECT transform_remove_id,
																    	  		   not_apply_fri_uid
																    	  	from transform_remove_friend AS TRANREMFRI
																    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
																    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
																    	   )", $db);
					
					while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
					{
						if(substr_count($remove_list, '<r>'.$list_remove_by_user_row['remove_uid_a'].'</r>') == '0')
						{
							$remove_list .= '<r>'.$list_remove_by_user_row['remove_uid_a'].'</r>';
						}
					}
					
					$xml_list = simplexml_load_string($result);
					$NEW_xml_list = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><fql_query_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" list="true"></fql_query_response>');
					
					$uid_arr_to_add = explode(',', $add_list);
					$xml_child_nodes = $xml_list->children();
					
					foreach($uid_arr_to_add as $uid_to_add)	
					{
						if($uid_to_add != '' &&
						   !strripos($result, '<target_id>'.$uid_to_add.'</target_id>'))
						{
							$new_child = $NEW_xml_list->addChild($xml_child_nodes[0]->getName());
							foreach($xml_child_nodes[0]->children() as $a => $b) 
							{
								if($a == 'target_id')
								{
									$new_child->addChild($a, $uid_to_add);
								}
								else if($a == 'is_deleted')
								{
									$new_child->addChild($a, ' ');
								}
								else if($a == 'updated_time')
								{
									$new_child->addChild($a, ' ');
								}
								else if($a == 'is_following')
								{
									$new_child->addChild($a, '1');
								}
								else if($a == 'target_type')
								{
									$new_child->addChild($a, 'user');
								}
							} 
						}
					}
					
					foreach ($xml_child_nodes as $xml_child) 
					{
						if(strripos($remove_list, '<r>'.$xml_child->target_id.'</r>') == FALSE)
						{
							$new_child = $NEW_xml_list->addChild($xml_child->getName());
							foreach($xml_child->children() as $a => $b) 
							{ 
							    $new_child->addChild($a, $b);
							} 
						}
					}
					
					$result = $NEW_xml_list->asXML();
					$result = str_replace('><', '> <', $result);
				}
			}
		}
		
		//--------------------------------------------------------------------------------------------------
	}
	else if($_GET['method'] == 'facebook.fql.multiquery')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.fql.multiquery');
  		$api_array['queries'] = $_POST['queries'];
  		
		$result = $facebook->api_client->fql_multiquery($_POST['queries']);
		
		$block_list_results = mysql_query("SELECT uid, blocked_uid FROM user_blocked_friend 
    															   WHERE user_blocked_friend.blocked_uid = $faith_uid;", $db);
		
		$result = preg_replace("/[\n\r]/","",$result); 
		
		while($block_list_row = mysql_fetch_array($block_list_results))
		{
			$blocker = $block_list_row['uid'];
			$pattern = '{<user>(.*'.$blocker.'.*?)</user>}';
			$replacement = '';
			$result = preg_replace($pattern, $replacement, $result);
		}
	}
	else if($_GET['method'] == 'facebook.friends.areFriends')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.friends.areFriends');
  		$api_array['uids1'] = $_POST['uids1'];
  		$api_array['uids2'] = $_POST['uids2'];
  		
		$result = $facebook->api_client->friends_areFriends($_POST['uids1'],
															$_POST['uids2']);

		$xml_areFriends = simplexml_load_string($result);
		
	    foreach ($xml_areFriends->children() as $areFriends_child_node) 
	    {
	    	if($areFriends_child_node->are_friends == '0')
	    	{
	    		$areFriends_add_by_user_results = mysql_query("SELECT transform_add_id,
		    														  add_uid_a,
										   							  add_uid_b
														    		  from transform_add AS TRANADD
														    		  where TRANADD.status = 1 AND
														    		  		TRANADD.add_uid_a = $areFriends_child_node->uid1 AND
														    		  	    TRANADD.add_uid_b = $areFriends_child_node->uid2 AND
														    	  	  NOT EXISTS
														    	  	  (
														    	  	  	SELECT transform_add_id,
														    	  		   	   not_apply_app_uid
														    	  			   from transform_add_app AS TRANADDAPP
														    	  			   where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  	   TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  	  ) AND
														    	  	  NOT EXISTS
														    	  	  (
														    	  		SELECT transform_add_id,
														    	  		   	   not_apply_fri_uid
														    	  			   from transform_add_friend AS TRANADDFRI
														    	  			   where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  			 TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	      )", $db);
			
				while($areFriends_add_by_user_row = mysql_fetch_array($areFriends_add_by_user_results))
				{
					$areFriends_child_node->are_friends = '1';
				}
				
	    		$areFriends_add_by_user_results = mysql_query("SELECT transform_add_id,
		    														  add_uid_a,
										   							  add_uid_b
														    		  from transform_add AS TRANADD
														    		  where TRANADD.status = 1 AND
														    		  		TRANADD.add_uid_a = $areFriends_child_node->uid2 AND
														    		  	    TRANADD.add_uid_b = $areFriends_child_node->uid1 AND
														    	  	  NOT EXISTS
														    	  	  (
														    	  	  	SELECT transform_add_id,
														    	  		   	   not_apply_app_uid
														    	  			   from transform_add_app AS TRANADDAPP
														    	  			   where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  	   TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  	  ) AND
														    	  	  NOT EXISTS
														    	  	  (
														    	  		SELECT transform_add_id,
														    	  		   	   not_apply_fri_uid
														    	  			   from transform_add_friend AS TRANADDFRI
														    	  			   where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  			 TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	      )", $db);
			
				while($areFriends_add_by_user_row = mysql_fetch_array($areFriends_add_by_user_results))
				{
					$areFriends_child_node->are_friends = '1';
				}
	    	}
	    	else if($areFriends_child_node->are_friends == '1')
	    	{
	    		$areFriends_remove_by_user_results = mysql_query("SELECT transform_remove_id,
	    																 remove_uid_a,
									   					   				 remove_uid_b
													       				 from transform_remove AS TRANREM
													       				 where TRANREM.remove_uid_a = $areFriends_child_node->uid1 AND
													       				 	   TRANREM.remove_uid_b = $areFriends_child_node->uid2 AND
															    	     NOT EXISTS
															    	     (
															    	  	  	SELECT transform_remove_id,
															    	  		   	   not_apply_app_uid
															    	  			   from transform_remove_app AS TRANREMAPP
															    	  			   where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  			 TRANREMAPP.not_apply_app_uid = $faith_app_id
															    	  	 ) AND
															    	  	 NOT EXISTS
															    	  	 (
															    	  		SELECT transform_remove_id,
															    	  		   	   not_apply_fri_uid
															    	  			   from transform_remove_friend AS TRANREMFRI
															    	  			   where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  			 TRANREMFRI.not_apply_fri_uid = $faith_uid
															    	     )", $db);
		
				while($areFriends_remove_by_user_row = mysql_fetch_array($areFriends_remove_by_user_results))
				{
					$areFriends_child_node->are_friends = '0';
				}
				
	    		$areFriends_remove_by_user_results = mysql_query("SELECT transform_remove_id,
	    																 remove_uid_a,
									   					   				 remove_uid_b
													       				 from transform_remove AS TRANREM
													       				 where TRANREM.remove_uid_a = $areFriends_child_node->uid2 AND
													       				 	   TRANREM.remove_uid_b = $areFriends_child_node->uid1 AND
															    	     NOT EXISTS
															    	     (
															    	  	  	SELECT transform_remove_id,
															    	  		   	   not_apply_app_uid
															    	  			   from transform_remove_app AS TRANREMAPP
															    	  			   where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  			 TRANREMAPP.not_apply_app_uid = $faith_app_id
															    	  	 ) AND
															    	  	 NOT EXISTS
															    	  	 (
															    	  		SELECT transform_remove_id,
															    	  		   	   not_apply_fri_uid
															    	  			   from transform_remove_friend AS TRANREMFRI
															    	  			   where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
															    	  		  			 TRANREMFRI.not_apply_fri_uid = $faith_uid
															    	     )", $db);
		
				while($areFriends_remove_by_user_row = mysql_fetch_array($areFriends_remove_by_user_results))
				{
					$areFriends_child_node->are_friends = '0';
				}
	    	}
		}

		$result = $xml_areFriends->asXML();
	}
	else if($_GET['method'] == 'facebook.friends.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.friends.get');
  		$api_array['flid'] = $_POST['flid'];
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->friends_get($_POST['flid'],
													 $_POST['uid']);

		if(!strripos($result, 'error_code')) 
		{
			$friendsget_uid = $faith_uid;	
			
			if(strlen($_POST['uid']) > 0)
			{
				$friendsget_uid = $_POST['uid'];
			}
			
			if(strlen($_POST['flid']) == 0) 
			{
			$list_add_by_user_results = mysql_query("SELECT transform_add_id,
										   					add_uid_b
														    from transform_add AS TRANADD
														    where TRANADD.status = 1 AND
														    	  TRANADD.add_uid_a = $friendsget_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_app_uid
														    	  	from transform_add_app AS TRANADDAPP
														    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_add_friend AS TRANADDFRI
														    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
			{
				$add_uid_b = $list_add_by_user_row['add_uid_b'];
				if(!strripos($result, '<uid>'.$add_uid_b.'</uid>'))
				{
					$pattern = '</friends_get_response>';
					$replacement = '<uid>'.$add_uid_b.'</uid> </friends_get_response>';
					$result = str_replace($pattern, $replacement, $result);
				}
			}
			
			$list_add_by_user_results = mysql_query("SELECT transform_add_id,
										   					add_uid_a
														    from transform_add AS TRANADD
														    where TRANADD.status = 1 AND
														    	  TRANADD.add_uid_b = $friendsget_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_app_uid
														    	  	from transform_add_app AS TRANADDAPP
														    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_add_friend AS TRANADDFRI
														    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
			{
				$add_uid_a = $list_add_by_user_row['add_uid_a'];
				if(!strripos($result, '<uid>'.$add_uid_a.'</uid>'))
				{
					$pattern = '</friends_get_response>';
					$replacement = '<uid>'.$add_uid_a.'</uid> </friends_get_response>';
					$result = str_replace($pattern, $replacement, $result);
				}
			}
			}
			
			$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
										   					   remove_uid_b
														       from transform_remove AS TRANREM
														       where TRANREM.remove_uid_a = $friendsget_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_app_uid
														    	  	from transform_remove_app AS TRANREMAPP
														    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_remove_friend AS TRANREMFRI
														    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
			{
				$remove_uid_b = $list_remove_by_user_row['remove_uid_b'];
				$pattern = '{<uid>'.$remove_uid_b.'</uid>}';
				$replacement = ' ';
				$result = preg_replace($pattern, $replacement, $result);
			}
			
			$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
										   					remove_uid_a
														    from transform_remove AS TRANREM
														    where TRANREM.remove_uid_b = $friendsget_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_app_uid
														    	  	from transform_remove_app AS TRANREMAPP
														    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_remove_friend AS TRANREMFRI
														    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
			{
				$remove_uid_a = $list_remove_by_user_row['remove_uid_a'];
				$pattern = '{<uid>'.$remove_uid_a.'</uid>}';
				$replacement = ' ';
				$result = preg_replace($pattern, $replacement, $result);
			}
		}										 
													 
		//---------------------------------------------------------------------------------------------											 
	}
	else if($_GET['method'] == 'facebook.friends.getAppUsers')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.friends.getAppUsers');
	  	
		$result = $facebook->api_client->friends_getAppUsers();
		
		//-----------------------------------------------------------------------------------------------
		
		$facebook->api_client->Set_Is_FAITH_REST(false);
		
		$list_add_by_user_results = mysql_query("SELECT transform_add_id,
									   					add_uid_b
													    from transform_add AS TRANADD
													    where TRANADD.status = 1 AND
													    	  TRANADD.add_uid_a = $faith_uid AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_add_id,
													    	  		   not_apply_app_uid
													    	  	from transform_add_app AS TRANADDAPP
													    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
													    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
													    	  ) AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_add_id,
													    	  		   not_apply_fri_uid
													    	  	from transform_add_friend AS TRANADDFRI
													    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
													    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
													    	   )", $db);
		
		while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
		{
			$add_uid_b = $list_add_by_user_row['add_uid_b'];
			if(!strripos($result, '<uid>'.$add_uid_b.'</uid>') &&
				$facebook->api_client->users_isAppUser($add_uid_b))
			{
				$pattern = '</friends_getAppUsers_response>';
				$replacement = '<uid>'.$add_uid_b.'</uid> </friends_getAppUsers_response>';
				$result = str_replace($pattern, $replacement, $result);
			}
		}
		//-----------------------------------------------------------------------------------------------
		
		$list_add_by_user_results = mysql_query("SELECT transform_add_id,
									   					add_uid_a
													    from transform_add AS TRANADD
													    where TRANADD.status = 1 AND
													    	  TRANADD.add_uid_b = $faith_uid AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_add_id,
													    	  		   not_apply_app_uid
													    	  	from transform_add_app AS TRANADDAPP
													    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
													    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
													    	  ) AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_add_id,
													    	  		   not_apply_fri_uid
													    	  	from transform_add_friend AS TRANADDFRI
													    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
													    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
													    	   )", $db);
		
		while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
		{
			$add_uid_a = $list_add_by_user_row['add_uid_a'];
			if(!strripos($result, '<uid>'.$add_uid_a.'</uid>') &&
			   $facebook->api_client->users_isAppUser($add_uid_a))
			{
				$pattern = '</friends_getAppUsers_response>';
				$replacement = '<uid>'.$add_uid_a.'</uid> </friends_getAppUsers_response>';
				$result = str_replace($pattern, $replacement, $result);
			}
		}
		
		$facebook->api_client->Set_Is_FAITH_REST(true);
		//-----------------------------------------------------------------------------------------------
		
		$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
									   					   remove_uid_b
													       from transform_remove AS TRANREM
													       where TRANREM.remove_uid_a = $faith_uid AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_remove_id,
													    	  		   not_apply_app_uid
													    	  	from transform_remove_app AS TRANREMAPP
													    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
													    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
													    	  ) AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_remove_id,
													    	  		   not_apply_fri_uid
													    	  	from transform_remove_friend AS TRANREMFRI
													    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
													    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
													    	   )", $db);
		
		while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
		{
			$remove_uid_b = $list_remove_by_user_row['remove_uid_b'];
			$pattern = '{<uid>'.$remove_uid_b.'</uid>}';
			$replacement = '';
			$result = preg_replace($pattern, $replacement, $result);
		}
		//-----------------------------------------------------------------------------------------------
		
		$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
									   					remove_uid_a
													    from transform_remove AS TRANREM
													    where TRANREM.remove_uid_b = $faith_uid AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_remove_id,
													    	  		   not_apply_app_uid
													    	  	from transform_remove_app AS TRANREMAPP
													    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
													    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
													    	  ) AND
													    	  NOT EXISTS
													    	  (
													    	  	SELECT transform_remove_id,
													    	  		   not_apply_fri_uid
													    	  	from transform_remove_friend AS TRANREMFRI
													    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
													    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
													    	   )", $db);
		
		while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
		{
			$remove_uid_a = $list_remove_by_user_row['remove_uid_a'];
			$pattern = '{<uid>'.$remove_uid_a.'</uid>}';
			$replacement = '';
			$result = preg_replace($pattern, $replacement, $result);
		}
		
		//-----------------------------------------------------------------------------------------------
	}
	else if($_GET['method'] == 'facebook.friends.getLists')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.friends.getLists');
  		
		$result = $facebook->api_client->friends_getLists();
	}
	else if($_GET['method'] == 'facebook.friends.getMutualFriends')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.friends.getMutualFriends');
  		$api_array['target_uid'] = $_POST['target_uid'];
  		$api_array['source_uid'] = $_POST['source_uid'];
  		
		$result = $facebook->api_client->friends_getMutualFriends($_POST['target_uid'],
													 			  $_POST['source_uid']);
													 			  
		if(!strripos($result, 'error_code')) 
		{		
			$GMF_source_uid = $faith_uid;	
			$GMF_target_uid = $_POST['target_uid'];
			
			if(strlen($_POST['source_uid']) > 0)
			{
				$GMF_source_uid = $_POST['source_uid'];
			}

			$facebook->api_client->Set_Is_FAITH_REST(false);
			
			$list_add_by_user_results = mysql_query("SELECT transform_add_id,
										   					add_uid_b
														    from transform_add AS TRANADD
														    where TRANADD.status = 1 AND
														    	  TRANADD.add_uid_a = $GMF_source_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_app_uid
														    	  	from transform_add_app AS TRANADDAPP
														    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_add_friend AS TRANADDFRI
														    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
			{
				$add_uid_b = $list_add_by_user_row['add_uid_b'];
				$GMF_areFriend_result = $facebook->api_client->friends_areFriends($add_uid_b, $GMF_target_uid);
				
				if(!strripos($result, '<uid>'.$add_uid_b.'</uid>') &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$pattern = '</friends_getMutualFriends_response>';
					$replacement = '<uid>'.$add_uid_b.'</uid> </friends_getMutualFriends_response>';
					$result = str_replace($pattern, $replacement, $result);
				}
			}											
			//-----------------------------------------------------------------------------------------------
			
			$list_add_by_user_results = mysql_query("SELECT transform_add_id,
										   					add_uid_a
														    from transform_add AS TRANADD
														    where TRANADD.status = 1 AND
														    	  TRANADD.add_uid_b = $GMF_source_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_app_uid
														    	  	from transform_add_app AS TRANADDAPP
														    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_add_friend AS TRANADDFRI
														    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
			{
				$add_uid_a = $list_add_by_user_row['add_uid_a'];
				$GMF_areFriend_result = $facebook->api_client->friends_areFriends($add_uid_a, $GMF_target_uid);
				
				if(!strripos($result, '<uid>'.$add_uid_a.'</uid>') &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$pattern = '</friends_getMutualFriends_response>';
					$replacement = '<uid>'.$add_uid_a.'</uid> </friends_getMutualFriends_response>';
					$result = str_replace($pattern, $replacement, $result);
				}
			}
			//-----------------------------------------------------------------------------------------------
			
			$list_add_by_user_results = mysql_query("SELECT transform_add_id,
										   					add_uid_b
														    from transform_add AS TRANADD
														    where TRANADD.status = 1 AND
														    	  TRANADD.add_uid_a = $GMF_target_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_app_uid
														    	  	from transform_add_app AS TRANADDAPP
														    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_add_friend AS TRANADDFRI
														    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
			{
				$add_uid_b = $list_add_by_user_row['add_uid_b'];
				$GMF_areFriend_result = $facebook->api_client->friends_areFriends($add_uid_b, $GMF_source_uid);
				
				if(!strripos($result, '<uid>'.$add_uid_b.'</uid>') &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$pattern = '</friends_getMutualFriends_response>';
					$replacement = '<uid>'.$add_uid_b.'</uid> </friends_getMutualFriends_response>';
					$result = str_replace($pattern, $replacement, $result);
				}
			}											
			//-----------------------------------------------------------------------------------------------
			
			$list_add_by_user_results = mysql_query("SELECT transform_add_id,
										   					add_uid_a
														    from transform_add AS TRANADD
														    where TRANADD.status = 1 AND
														    	  TRANADD.add_uid_b = $GMF_target_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_app_uid
														    	  	from transform_add_app AS TRANADDAPP
														    	  	where TRANADDAPP.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_add_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_add_friend AS TRANADDFRI
														    	  	where TRANADDFRI.transform_add_id = TRANADD.transform_add_id AND
														    	  		  TRANADDFRI.not_apply_fri_uid = $faith_uid
														    	   )", $db);
			
			while($list_add_by_user_row = mysql_fetch_array($list_add_by_user_results))
			{
				$add_uid_a = $list_add_by_user_row['add_uid_a'];
				$GMF_areFriend_result = $facebook->api_client->friends_areFriends($add_uid_a, $GMF_source_uid);
				
				if(!strripos($result, '<uid>'.$add_uid_a.'</uid>') &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$pattern = '</friends_getMutualFriends_response>';
					$replacement = '<uid>'.$add_uid_a.'</uid> </friends_getMutualFriends_response>';
					$result = str_replace($pattern, $replacement, $result);
				}
			}
			//-----------------------------------------------------------------------------------------------
			
			$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
										   					   remove_uid_b
														       from transform_remove AS TRANREM
														       where 
														          (TRANREM.remove_uid_a = $GMF_source_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_app_uid
														    	  	from transform_remove_app AS TRANREMAPP
														    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_remove_friend AS TRANREMFRI
														    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
														    	   )) OR
														    	   (TRANREM.remove_uid_a = $GMF_target_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_app_uid
														    	  	from transform_remove_app AS TRANREMAPP
														    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_remove_friend AS TRANREMFRI
														    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
														    	   ))
														    	   ", $db);
			
			while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
			{
				$remove_uid_b = $list_remove_by_user_row['remove_uid_b'];
				$pattern = '{<uid>'.$remove_uid_b.'</uid>}';
				$replacement = '';
				$result = preg_replace($pattern, $replacement, $result);
			}
			//-----------------------------------------------------------------------------------------------
			
			$list_remove_by_user_results = mysql_query("SELECT transform_remove_id,
										   					   remove_uid_a
														       from transform_remove AS TRANREM
														       where 
														          (TRANREM.remove_uid_b = $GMF_source_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_app_uid
														    	  	from transform_remove_app AS TRANREMAPP
														    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_remove_friend AS TRANREMFRI
														    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
														    	   )) OR
														    	   (TRANREM.remove_uid_b = $GMF_target_uid AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_app_uid
														    	  	from transform_remove_app AS TRANREMAPP
														    	  	where TRANREMAPP.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMAPP.not_apply_app_uid = $faith_app_id
														    	  ) AND
														    	  NOT EXISTS
														    	  (
														    	  	SELECT transform_remove_id,
														    	  		   not_apply_fri_uid
														    	  	from transform_remove_friend AS TRANREMFRI
														    	  	where TRANREMFRI.transform_remove_id = TRANREM.transform_remove_id AND
														    	  		  TRANREMFRI.not_apply_fri_uid = $faith_uid
														    	   ))
														    	   ", $db);
			
			while($list_remove_by_user_row = mysql_fetch_array($list_remove_by_user_results))
			{
				$remove_uid_a = $list_remove_by_user_row['remove_uid_a'];
				$pattern = '{<uid>'.$remove_uid_a.'</uid>}';
				$replacement = '';
				$result = preg_replace($pattern, $replacement, $result);
			}
			//-----------------------------------------------------------------------------------------------
			
			$facebook->api_client->Set_Is_FAITH_REST(true);
		}
	}
	else if($_GET['method'] == 'facebook.groups.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.groups.get');
  		$api_array['uid'] = $_POST['uid'];
  		$api_array['gids'] = $_POST['gids'];
  		
		$result = $facebook->api_client->groups_get($_POST['uid'],
													$_POST['gids']);
	}
	else if($_GET['method'] == 'facebook.groups.getMembers')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.groups.getMembers');
  		$api_array['gid'] = $_POST['gid'];
  		
		$result = $facebook->api_client->groups_getMembers($_POST['gid']);
	}
	else if($_GET['method'] == 'facebook.links.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.links.get');
  		$api_array['uid'] = $_POST['uid'];
  		$api_array['limit'] = $_POST['limit'];
  		$api_array['link_ids'] = $_POST['link_ids'];
  		
		$result = $facebook->api_client->links_get($_POST['uid'],
												   $_POST['limit'],
												   $_POST['link_ids']);
	}
	else if($_GET['method'] == 'facebook.notes.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.notes.get');
  		$api_array['uid'] = $_POST['uid'];
  		$api_array['note_ids'] = $_POST['note_ids'];
  		
		$result = $facebook->api_client->notes_get($_POST['uid'],
												   $_POST['note_ids']);
	}
	else if($_GET['method'] == 'facebook.notifications.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.notifications.get');
  		
		$result = $facebook->api_client->notifications_get();
	}
	else if($_GET['method'] == 'facebook.pages.getInfo')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.pages.getInfo');
  		$api_array['page_ids'] = $_POST['page_ids'];
  		$api_array['fields'] = $_POST['fields'];
  		$api_array['uid'] = $_POST['uid'];
  		$api_array['type'] = $_POST['type'];
  		
		$result = $facebook->api_client->pages_getInfo($_POST['page_ids'],
												   	   $_POST['fields'],
												   	   $_POST['uid'],
												   	   $_POST['type']);
	}
	else if($_GET['method'] == 'facebook.pages.isAdmin')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.pages.isAdmin');
  		$api_array['page_id'] = $_POST['page_id'];
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->pages_isAdmin($_POST['page_id'],
												   	   $_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.pages.isAppAdded')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.pages.isAppAdded');
  		$api_array['page_id'] = $_POST['page_id'];
  		
		$result = $facebook->api_client->pages_isAppAdded($_POST['page_id']);
	}
	else if($_GET['method'] == 'facebook.pages.isFan')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.pages.isFan');
  		$api_array['page_id'] = $_POST['page_id'];
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->pages_isFan($_POST['page_id'],
													 $_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.stream.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.stream.get');
  		$api_array['viewer_id'] = $_POST['viewer_id'];
  		$api_array['source_ids'] = $_POST['source_ids'];
  		$api_array['start_time'] = $_POST['start_time'];
  		$api_array['end_time'] = $_POST['end_time'];
  		$api_array['limit'] = $_POST['limit'];
  		$api_array['filter_key'] = $_POST['filter_key'];
  		$api_array['exportable_only'] = $_POST['exportable_only'];
  		$api_array['metadata'] = $_POST['metadata'];
  		$api_array['post_ids'] = $_POST['post_ids'];
  		$api_array['query'] = $_POST['query'];
  		$api_array['everyone_stream'] = $_POST['everyone_stream'];
  		
		$result = $facebook->api_client->stream_get($_POST['viewer_id'],
													$_POST['source_ids'],
													$_POST['start_time'],
													$_POST['end_time'],
													$_POST['limit'],
													$_POST['filter_key'],
													$_POST['exportable_only'],
													$_POST['metadata'],
													$_POST['post_ids'],
													$_POST['query'],
													$_POST['everyone_stream']);
	}
	else if($_GET['method'] == 'facebook.stream.getComments')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.stream.getComments');
  		$api_array['post_id'] = $_POST['post_id'];
  		
		$result = $facebook->api_client->stream_getComments($_POST['post_id']);
	}
	else if($_GET['method'] == 'facebook.stream.getFilters')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.stream.getFilters');
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->stream_getFilters($_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.users.getInfo')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.users.getInfo');
  		$api_array['uids'] = $_POST['uids'];
  		$api_array['fields'] = $_POST['fields'];
  		
		$result = $facebook->api_client->users_getInfo($_POST['uids'], 
													   $_POST['fields']);
	}
	else if($_GET['method'] == 'facebook.users.getStandardInfo')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.users.getStandardInfo');
  		$api_array['uids'] = $_POST['uids'];
  		$api_array['fields'] = $_POST['fields'];
  		
		$result = $facebook->api_client->users_getStandardInfo($_POST['uids'], 
															   $_POST['fields']);
	}
	else if($_GET['method'] == 'facebook.users.hasAppPermission')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.users.hasAppPermission');
  		$api_array['ext_perm'] = $_POST['ext_perm'];
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->users_hasAppPermission($_POST['ext_perm'], 
															    $_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.users.isAppUser')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.users.isAppUser');
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->users_isAppUser($_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.users.isVerified')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.users.isVerified');
  		
		$result = $facebook->api_client->users_isVerified();
	}
	else if($_GET['method'] == 'facebook.video.getUploadLimits')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.video.getUploadLimits');
  		
		$result = $facebook->api_client->video_getUploadLimits();
	}
	else if($_GET['method'] == 'facebook.links.post')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.links.post');
  			$api_array['url'] = $_POST['url'];
  			$api_array['comment'] = $_POST['comment'];
  			$api_array['uid'] = $_POST['uid'];
  		
			$result = $facebook->api_client->links_post($_POST['url'],
														$_POST['comment'],
														$_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.notes.create')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.notes.create');
  			$api_array['title'] = $_POST['title'];
  			$api_array['content'] = $_POST['content'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->notes_create($_POST['title'],
														  $_POST['content'],
														  $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.notes.delete')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.notes.delete');
  			$api_array['note_id'] = $_POST['note_id'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->notes_delete($_POST['note_id'],
														  $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.notes.edit')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.notes.edit');
  			$api_array['note_id'] = $_POST['note_id'];
  			$api_array['title'] = $_POST['title'];
  			$api_array['content'] = $_POST['content'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->notes_edit($_POST['note_id'],
														$_POST['title'],
														$_POST['content'],
														$_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.stream.addComment')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.stream.addComment');
  			$api_array['post_id'] = $_POST['post_id'];
  			$api_array['comment'] = $_POST['comment'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->stream_addComment($_POST['post_id'],
															   $_POST['comment'],
															   $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.stream.publish')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{ 	
	  		$api_array = array('method' => 'facebook.stream.publish');
  			$api_array['message'] = $_POST['message'];
  			$api_array['attachment'] = $_POST['attachment'];
  			$api_array['action_links'] = $_POST['action_links'];
  			$api_array['target_id'] = $_POST['target_id'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->stream_publish($_POST['message'],
															$_POST['attachment'],
															$_POST['action_links'],
															$_POST['target_id'],
															$_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.stream.addLike')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.stream.addLike');
  			$api_array['post_id'] = $_POST['post_id'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->stream_addLike($_POST['post_id'],
															$_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.stream.remove')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.stream.remove');
  			$api_array['post_id'] = $_POST['post_id'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->stream_remove($_POST['post_id'],
														   $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.stream.removeComment')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.stream.removeComment');
  			$api_array['comment_id'] = $_POST['comment_id'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->stream_removeComment($_POST['comment_id'],
															   	  $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.stream.removeLike')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.stream.removeLike');
  			$api_array['post_id'] = $_POST['post_id'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->stream_removeLike($_POST['post_id'],
															   $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.users.setStatus')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.users.setStatus');
  			$api_array['status'] = $_POST['status'];
  			$api_array['uid'] = $_POST['uid'];
  			$api_array['clear'] = $_POST['clear'];
  			$api_array['status_includes_verb'] = $_POST['status_includes_verb'];
  			
			$result = $facebook->api_client->users_setStatus($_POST['status'],
															 $_POST['uid'],
															 $_POST['clear'],
															 $_POST['status_includes_verb']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.addGlobalNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.addGlobalNews');
  			$api_array['news'] = $_POST['news'];
  			$api_array['image'] = $_POST['image'];
  			
	  		$result = $facebook->api_client->dashboard_addGlobalNews($_POST['news'],
														 		     $_POST['image']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.addNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.addNews');
  			$api_array['news'] = $_POST['news'];
  			$api_array['image'] = $_POST['image'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->dashboard_addNews($_POST['news'],
															   $_POST['image'],
															   $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.clearGlobalNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.clearGlobalNews');
  			$api_array['news_ids'] = $_POST['news_ids'];
  			
			$result = $facebook->api_client->dashboard_clearGlobalNews($_POST['news_ids']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.clearNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.clearNews');
  			$api_array['news_ids'] = $_POST['news_ids'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->dashboard_clearNews($_POST['news_ids'],
																 $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.decrementCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.decrementCount');
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->dashboard_decrementCount($_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.getActivity')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.dashboard.getActivity');
  		$api_array['activity_ids'] = $_POST['activity_ids'];
  		$api_array['uid'] = $_POST['uid'];
  			
		$result = $facebook->api_client->dashboard_getActivity($_POST['activity_ids'],
															   $_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.dashboard.getCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.dashboard.getCount');
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->dashboard_getCount($_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.dashboard.getGlobalNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.dashboard.getGlobalNews');
  		$api_array['news_ids'] = $_POST['news_ids'];
  		
		$result = $facebook->api_client->dashboard_getGlobalNews($_POST['news_ids']);
	}
	else if($_GET['method'] == 'facebook.dashboard.getNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.dashboard.getNews');
  		$api_array['news_ids'] = $_POST['news_ids'];
  		$api_array['uid'] = $_POST['uid'];
  		
		$result = $facebook->api_client->dashboard_getNews($_POST['news_ids'],
														   $_POST['uid']);
	}
	else if($_GET['method'] == 'facebook.dashboard.incrementCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.incrementCount');
  			$api_array['uid'] = $_POST['uid'];
  		
			$result = $facebook->api_client->dashboard_incrementCount($_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.multiAddNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.multiAddNews');
  			$api_array['uids'] = $_POST['uids'];
  			$api_array['news'] = $_POST['news'];
  			$api_array['image'] = $_POST['image'];
  			
			$result = $facebook->api_client->dashboard_multiAddNews($_POST['uids'],
																	$_POST['news'],
																	$_POST['image']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.multiClearNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.multiClearNews');
  			$api_array['ids'] = $_POST['ids'];
  			
			$result = $facebook->api_client->dashboard_multiClearNews($_POST['ids']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.multiDecrementCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.multiDecrementCount');
  			$api_array['uids'] = $_POST['uids'];
  			
			$result = $facebook->api_client->dashboard_multiDecrementCount($_POST['uids']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.multiGetCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.dashboard.multiGetCount');
  		$api_array['uids'] = $_POST['uids'];
  			
		$result = $facebook->api_client->dashboard_multiGetCount($_POST['uids']);
	}
	else if($_GET['method'] == 'facebook.dashboard.multiGetNews')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.dashboard.multiGetNews');
  		$api_array['ids'] = $_POST['ids'];
  		
		$result = $facebook->api_client->dashboard_multiGetNews($_POST['ids']);
	}
	else if($_GET['method'] == 'facebook.dashboard.multiIncrementCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.multiIncrementCount');
  			$api_array['uids'] = $_POST['uids'];
  		
			$result = $facebook->api_client->dashboard_multiIncrementCount($_POST['uids']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.multiSetCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.multiSetCount');
  			$api_array['ids'] = $_POST['ids'];
  			
			$result = $facebook->api_client->dashboard_multiSetCount($_POST['ids']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.publishActivity')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.publishActivity');
  			$api_array['activity'] = $_POST['activity'];
  			
			$result = $facebook->api_client->dashboard_publishActivity($_POST['activity']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.removeActivity')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.removeActivity');
  			$api_array['activity_ids'] = $_POST['activity_ids'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->dashboard_removeActivity($_POST['activity_ids'],
																	  $_POST['uid']);
	  	}
	}
	else if($_GET['method'] == 'facebook.dashboard.setCount')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.dashboard.setCount');
  			$api_array['count'] = $_POST['count'];
  			$api_array['uid'] = $_POST['uid'];
  			
			$result = $facebook->api_client->dashboard_setCount($_POST['count'],
															 	$_POST['uid']);
	  	}
	}
	
	else if($_GET['method'] == 'facebook.photos.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.photos.get');
  		$api_array['subj_id'] = $_POST['subj_id'];
  		$api_array['aid'] = $_POST['aid'];
  		$api_array['pids'] = $_POST['pids'];	
  			
		$result = $facebook->api_client->photos_get($_POST['subj_id'],
													$_POST['aid'],
												 	$_POST['pids']);
	}
	else if($_GET['method'] == 'facebook.photos.getAlbums')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.photos.getAlbums');
  		$api_array['uid'] = $_POST['uid'];
  		$api_array['aids'] = $_POST['aids'];
  		
		$result = $facebook->api_client->photos_getAlbums($_POST['uid'],
														  $_POST['aids']);
	}
	
	else if($_GET['method'] == 'facebook.events.cancel')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.events.cancel');
	  		$api_array['eid'] = $_POST['eid'];
	  		$api_array['cancel_message'] = $_POST['cancel_message'];
  		
			$result = $facebook->api_client->events_cancel($_POST['eid'],
														   $_POST['cancel_message']);
	  	}
	}
	else if($_GET['method'] == 'facebook.events.create')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.events.create');
	  		$api_array['event_info'] = $_POST['event_info'];
	  		
			$result = $facebook->api_client->events_create($_POST['event_info']);
	  	}
	}
	else if($_GET['method'] == 'facebook.events.edit')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.events.edit');
	  		$api_array['eid'] = $_POST['eid'];
	  		$api_array['event_info'] = $_POST['event_info'];
	  		
			$result = $facebook->api_client->events_edit($_POST['eid'],
														 $_POST['event_info']);
	  	}
	}
	else if($_GET['method'] == 'facebook.events.get')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.events.get');
	  	$api_array['uid'] = $_POST['uid'];
	  	$api_array['eids'] = $_POST['eids'];
	  	$api_array['start_time'] = $_POST['start_time'];
	  	$api_array['end_time'] = $_POST['end_time'];
	  	$api_array['rsvp_status'] = $_POST['rsvp_status'];	
	  		
		$result = $facebook->api_client->events_get($_POST['uid'],
													$_POST['eids'],
													$_POST['start_time'],
													$_POST['end_time'],
													$_POST['rsvp_status']);
	}
	else if($_GET['method'] == 'facebook.events.getMembers')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	$api_array = array('method' => 'facebook.events.getMembers');
	  	$api_array['eid'] = $_POST['eid'];
	  	
		$result = $facebook->api_client->events_getMembers($_POST['eid']);
	}
	else if($_GET['method'] == 'facebook.events.invite')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
	  	}
  	
	  	if($allowed == '1')
	  	{
			$result = $facebook->api_client->events_invite($_POST['eid'],
														   $_POST['uids'],
														   $_POST['personal_message']);
														   
			if(strripos($result, '<error_code>'))
			{
				$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
				
				$api_array = array('method' => 'facebook.events.invite');
	  			$api_array['eid'] = $_POST['eid'];
	  			$api_array['uids'] = $_POST['uids'];
	  			$api_array['personal_message'] = $_POST['personal_message'];
	  	
				$result = $facebook->api_client->events_invite($_POST['eid'],
														   $_POST['uids'],
														   $_POST['personal_message']);
			}
	  	}
	}
	else if($_GET['method'] == 'facebook.events.rsvp')
	{
		if(isset($_GET['session_key']))
	  	{
	  		$session_key = $_GET['session_key'];
			$facebook->api_client->Set_Session_Key_For_FAITH($session_key);
	  	}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'facebook.events.rsvp');
  			$api_array['eid'] = $_POST['eid'];
  			$api_array['rsvp_status'] = $_POST['rsvp_status'];
	  			
			$result = $facebook->api_client->events_rsvp($_POST['eid'],
														 $_POST['rsvp_status']);
	  	}
	}
	
	fwrite($fh, "(restserver.php)end query for $api_method \n");
	
	$logging_setting = '0';
	
	$results = mysql_query("SELECT logging_setting
								   from setting_logging
								   where uid = $faith_uid", $db);
			
	while($row = mysql_fetch_array($results))
	{
		$logging_setting = $row['logging_setting'];
	}
	
	if($logging_setting == '2' || $logging_setting == '3')
	{
	date_default_timezone_set('America/Los_Angeles');
	$time_added = date("Y-m-d H:i:s");
	
	if($_POST['faith_source'] == $faith_fbml || $_POST['faith_source'] == $faith_connect)
	{
		$query = sprintf("INSERT INTO access_log (uid, 
												  app_id,
												  allowed,
												  access_time,
												  logdetails,
												  url_id,
												  parameter,
												  sessionkey,
												  replay_type,
												  api_id,
												  app_ip_addr,
												  user_ip_addr) 
												  VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s',(SELECT id FROM restapi where facebook_method = '$api_method'),INET_ATON('$app_ip_addr'),INET_ATON('$faith_client_ip'))",
												  $faith_uid,
												  mysql_real_escape_string($faith_app_id),
												  mysql_real_escape_string($allowed),
												  mysql_real_escape_string($time_added),
												  mysql_real_escape_string($result),
												  mysql_real_escape_string($faith_url_id),
												  mysql_real_escape_string(json_encode($api_array)),
											      mysql_real_escape_string($session_key),
											      mysql_real_escape_string($faith_fbml_replay));
		
		//'$result'
		if(!mysql_query($query))
		{
			fwrite($fh, "(restserver.php)Query failed" . mysql_error() ."\n");
		}
	}
	else if($_POST['faith_source'] == $faith_fbml_replay)
	{
		$query = sprintf("INSERT INTO access_log_replay (allowed,
												  		 access_time,
												  		 logdetails,
												  		 logID) 
												  VALUES('%s','%s','%s','%s')",
												  mysql_real_escape_string($allowed),
												  mysql_real_escape_string($time_added),
												  mysql_real_escape_string($result),
												  mysql_real_escape_string($_POST['replay_lod_id']));
	
		//'$result'
		if(!mysql_query($query))
		{
			fwrite($fh, "(iframerestserver.php)Query failed" . mysql_error() ."\n");
		}
	}
	}
	
	if($Count_Num > 0)
	{
		print '';
	}
	else
	{
		print $result;
	}
	
	fclose($fh);
}
catch (Exception $e)
{
	
}

?>






