<?php

require_once 'vars.php';
require_once 'if/src/facebook.php';
require_once 'func.php';

try
{
	$myFile = "/home/dslfaith/public_html/faith/testFile.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");
	$stringData = "iframerestserver.php Called!\n";
	fwrite($fh, $stringData);
	
	$facebook;
	if(isset($_POST['faith_source']) && $_POST['faith_source'] == $faith_iframe)	//*FAITH*
	{
		$facebook = new Facebook(array('appId'  => $iframe_appid,
									   'secret' => $iframe_appsecret,
									   'cookie' => true,));
	}
	
	$facebook->Set_Is_FAITH_REST(true);
	
	if(!isset($_POST['faith_app_id']) || !isset($_POST['faith_uid']))
    {
    	exit();
    }
    
    mysqlSetup($db);
    
    $faith_app_id = $_POST['faith_app_id'];
    $faith_uid = $_POST['faith_uid'];
    $faith_url_id = $_POST['faith_url_id'];
	$api_method = $_POST['method'];
    $faith_client_ip = $_POST['faith_client_ip'];
    $app_ip_addr = $_SERVER['REMOTE_ADDR'];
    
	$api_method = strtolower($api_method);
    if(strripos($api_method, 'acebook.') != 1)
    {
    	$api_method = 'facebook.' . $api_method;
    }
    
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
	
	if($api_method == 'facebook.admin.getallocation')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'admin.getAllocation');
  		if(isset($_POST['integration_point_name']))
	  		$api_array['integration_point_name'] = $_POST['integration_point_name'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.admin.getappproperties')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'admin.getAppProperties');
  		if(isset($_POST['properties']))
	  		$api_array['properties'] = $_POST['properties'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.admin.getmetrics')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'admin.getMetrics');
  		if(isset($_POST['start_time']))
	  		$api_array['start_time'] = $_POST['start_time'];
	  	if(isset($_POST['end_time']))
	  		$api_array['end_time'] = $_POST['end_time'];
	  	if(isset($_POST['period']))
	  		$api_array['period'] = $_POST['period'];
	  	if(isset($_POST['metrics']))
	  		$api_array['metrics'] = $_POST['metrics'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.admin.getrestrictioninfo')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'admin.getRestrictionInfo');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.admin.getbannedusers')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
		$api_array = array('method' => 'admin.getBannedUsers');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uids']))
	  		$api_array['uids'] = $_POST['uids'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.application.getpublicinfo')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
		$api_array = array('method' => 'application.getPublicInfo');
  		if(isset($_POST['application_id']))
	  		$api_array['application_id'] = $_POST['application_id'];
	  	if(isset($_POST['application_api_key']))
	  		$api_array['application_api_key'] = $_POST['application_api_key'];
	  	if(isset($_POST['application_canvas_name']))
	  		$api_array['application_canvas_name'] = $_POST['application_canvas_name'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.data.setcookie')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'data.setCookie');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['name']))
		  		$api_array['name'] = $_POST['name'];
		  	if(isset($_POST['value']))
		  		$api_array['value'] = $_POST['value'];
		  	if(isset($_POST['expires']))
		  		$api_array['expires'] = $_POST['expires'];
		  	if(isset($_POST['path']))
		  		$api_array['path'] = $_POST['path'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.data.getcookies')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'data.getCookies');
  		if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	if(isset($_POST['name']))
	  		$api_array['name'] = $_POST['name'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.fbml.setrefhandle')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'fbml.setRefHandle');
	  		if(isset($_POST['handle']))
		  		$api_array['handle'] = $_POST['handle'];
		  	if(isset($_POST['fbml']))
		  		$api_array['fbml'] = $_POST['fbml'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.intl.uploadnativestrings')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'intl.uploadNativeStrings');
	  		if(isset($_POST['native_strings']))
		  		$api_array['native_strings'] = $_POST['native_strings'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.notifications.sendemail')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  		
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'notifications.sendEmail');
	  		if(isset($_POST['recipients']))
		  		$api_array['recipients'] = $_POST['recipients'];
		  	if(isset($_POST['subject']))
		  		$api_array['subject'] = $_POST['subject'];
		  	if(isset($_POST['text']))
		  		$api_array['text'] = $_POST['text'];
		  	if(isset($_POST['fbml']))
		  		$api_array['fbml'] = $_POST['fbml'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.comments.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'comments.get');
  		if(isset($_POST['xid']))
	  		$api_array['xid'] = $_POST['xid'];
	  	if(isset($_POST['object_id']))
	  		$api_array['object_id'] = $_POST['object_id'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.comments.add')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'comments.add');
	  		if(isset($_POST['text']))
		  		$api_array['text'] = $_POST['text'];
		  	if(isset($_POST['xid']))
		  		$api_array['xid'] = $_POST['xid'];
		  	if(isset($_POST['object_id']))
		  		$api_array['object_id'] = $_POST['object_id'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['title']))
		  		$api_array['title'] = $_POST['title'];
		  	if(isset($_POST['url']))
		  		$api_array['url'] = $_POST['url'];
		  	if(isset($_POST['publish_to_stream']))
		  		$api_array['publish_to_stream'] = $_POST['publish_to_stream'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.comments.remove')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'comments.remove');
	  		if(isset($_POST['comment_id']))
		  		$api_array['comment_id'] = $_POST['comment_id'];
		  	if(isset($_POST['xid']))
		  		$api_array['xid'] = $_POST['xid'];
		  	if(isset($_POST['object_id']))
		  		$api_array['object_id'] = $_POST['object_id'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.fbml.refreshimgsrc')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'facebook.fbml.refreshImgSrc');
	  		if(isset($_POST['url']))
		  		$api_array['url'] = $_POST['url'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.fbml.refreshrefurl')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'fbml.refreshRefUrl');
	  		if(isset($_POST['url']))
		  		$api_array['url'] = $_POST['url'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.fql.query')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'fql.query');
  		if(isset($_POST['query']))
	  		$api_array['query'] = $_POST['query'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
		
		$block_list_results = mysql_query("SELECT uid, blocked_uid FROM user_blocked_friend 
    															   WHERE user_blocked_friend.blocked_uid = $faith_uid;", $db);
		
		//--------------------------------------------------------------------------------------------------
		
		$fql_query_str = $_POST['query'];
		
		if(!strripos($result, 'error_code') &&	// if not a facebook error message
	   		substr_count(strtolower($fql_query_str), 'select') == '1') // only one select is allowed
		{
			$result = json_decode($result, true);
			
			if(strripos($fql_query_str, 'friendlist_member'))	// query friendlist_member table
			{
				//remove friendship according to the rule created by user-------------------------------------------
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
					
					foreach($result as $result_index => $result_array)
					{
						if(in_array($remove_uid_b, $result_array))
						{
							array_splice($result, $result_index, 1);
						}
					}
				}
				
				//remove friendship according to the rule created by friends----------------------------------------
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
					
					foreach($result as $result_index => $result_array)
					{
						if(in_array($remove_uid_a, $result_array))
						{
							array_splice($result, $result_index, 1);
						}
					}
				}
			}
			else if(strripos($fql_query_str, 'friend') ||
			   		strripos($fql_query_str, 'standard_friend_info'))
			{
				$query_uid_a; 
				$query_uid_b;
				$query_uid_a_name; 
				$query_uid_b_name;
				
				$fql_query_str = strtolower($fql_query_str);	// lowercase query string
				
				list($before_where, $after_where) = explode('where', $fql_query_str);	// eliminate the portion before where
				
				if(substr_count($after_where, 'or') == '0' &&		// OR is not allowed
				   substr_count($after_where, 'and') <= 1 &&		// 
				   substr_count($after_where, '!=') == '0')			// NOT EQUAL to is not allowed			
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
							$query_uid_b = preg_replace("/[^0-9]/", '', $query_uid_b); // eliminate everything that is not a number 
						}
					}
				}
				
				if(isset($query_uid_a) && isset($query_uid_b) && $query_uid_a != $query_uid_b) 	// check if two are friends
				{
					if(count($result) == 1) // the two uids are friend
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
				
						while($areFriends_remove_by_user_row = mysql_fetch_array($areFriends_remove_by_user_results)) // if a removes b as friend, set not friend
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
							$result = array();
						}
					}
					else if(count($result) == 0)	// NOT friend
					{
						$add_friendship = false;
						
						//set to true if either of them has added the virtual friendship connection
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
							
							if($uid_a_pos && $uid_b_pos)
							{
								$result = array( 0 => array($query_uid_a_name => $query_uid_a), 
												 1 => array($query_uid_b_name => $query_uid_b));
							}
							else if($uid_a_pos)
							{
								$result = array( 0 => array($query_uid_a_name => $query_uid_a));
							}
							else if($uid_b_pos)
							{
								$result = array( 0 => array($query_uid_b_name => $query_uid_b));
							}
						}
					}
				}
				else if(isset($query_uid_a) && !isset($query_uid_b))	// look for the friends of uid_a
				{
					$add_list = '';
					$remove_list = '';
					
					//add friendship according to the rule created by target uid
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
					
					//add friendship according to the rule created by the friends of target uid-----------------
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
					
					//remove friendship according to the rule created by user-------------------------------------------
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
						$remove_uid_b = $list_remove_by_user_row['remove_uid_b'];
						$remove_list .= $remove_uid_b . ',';
					}
					
					//remove friendship according to the rule created by friends----------------------------------------
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
						$remove_uid_a = $list_remove_by_user_row['remove_uid_a'];
						$remove_list .= $remove_uid_a . ',';
					}
					
					$uid_arr_to_add = explode(',', $add_list);
					$uid_arr_to_remove = explode(',', $remove_list);
					
					foreach($uid_arr_to_add as $uid_to_add)	// add friendship according to add list
					{
						$uid_in_array = false;
						
						foreach($result as $result_index => $result_value)
						{
							if(in_array($uid_to_add, $result_value))
							{
								$uid_in_array = true;
							}
						}
						
						if($uid_to_add != '' && !$uid_in_array)
						{
							$temp_arr = $result[0];
							
							foreach($temp_arr as $temp_arr_index => $temp_arr_value)
							{
								if($temp_arr_value != $query_uid_a)
								{
									$temp_arr[$temp_arr_index] = $uid_to_add;
								}
							}
							
							$result[] = $temp_arr;
						}
					}
					
					foreach($uid_arr_to_remove as $uid_to_remove) // remove friendship according to remove list
					{
						$uid_in_array = false;
						$uid_index = 0;
						
						foreach($result as $result_index => $result_value)
						{
							if(in_array($uid_to_remove, $result_value))
							{
								$uid_index = $result_index;
								$uid_in_array = true;
							}
						}
						
						if($uid_to_remove != '' && $uid_in_array)
						{
							array_splice($result, $uid_index, 1);
						}
					}
				}
			}
			/*else if(strripos($fql_query_str, 'connection'))
			{
				$query_source_id; 
				
				$fql_query_str = strtolower($fql_query_str);	// lowercase query string
				
				list($before_where, $query_source_id) = explode('where', $fql_query_str);	// eliminate the portion before where
				
				if(substr_count($query_source_id, 'or') == '0' &&		// OR is not allowed
				   substr_count($query_source_id, 'and') == '0')		// NOT EQUAL to is not allowed			
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
						$query_source_id = preg_replace("/[^0-9]/", '', $query_source_id); // eliminate everything that is not a number 
					}
				}
				
				if(isset($query_source_id) && strlen($query_source_id) > 0)	// look for the friends of source_id
				{
					$add_list = '';
					$remove_list = '<r>default</r>';
					
					//add friendship according to the rule created by source id
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
					
					//add friendship according to the rule created by the friends of target uid-----------------
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
					
					//remove friendship according to the rule created by user-------------------------------------------
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
					
					//remove friendship according to the rule created by friends----------------------------------------
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
					
					foreach($uid_arr_to_add as $uid_to_add)	// add friendship according to add list
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
					
					foreach ($xml_child_nodes as $xml_child) // remove friendship according to remove list
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
			}*/
			
			$result = json_encode($result);
		}
		
		//--------------------------------------------------------------------------------------------------
	}
	else if($api_method == 'facebook.fql.multiquery')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'fql.multiquery');
  		if(isset($_POST['queries']))
	  		$api_array['queries'] = $_POST['queries'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.friends.arefriends')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'friends.areFriends');
  		if(isset($_POST['uids1']))
	  		$api_array['uids1'] = $_POST['uids1'];
	  	if(isset($_POST['uids2']))
	  		$api_array['uids2'] = $_POST['uids2'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);

	  	//-----------------------------------------------------------------------------------------------
	  	
		$arr_areFriends = json_decode($result, true);
    	
    	foreach ($arr_areFriends as $areFriends_index => $areFriends_value) 
	    {
	    	if($arr_areFriends[$areFriends_index]['are_friends'] == '0')
	    	{
	    		//set to true if either of them has added the virtual friendship connection
		    	$areFriends_add_by_user_results = mysql_query("SELECT transform_add_id,
		    														  add_uid_a,
										   							  add_uid_b
														    		  from transform_add AS TRANADD
														    		  where TRANADD.status = 1 AND
														    		  		TRANADD.add_uid_a = ".$arr_areFriends[$areFriends_index]['uid1']." AND
														    		  	    TRANADD.add_uid_b = ".$arr_areFriends[$areFriends_index]['uid2']." AND
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
					$arr_areFriends[$areFriends_index]['are_friends'] = '1';
				}
				
	    		$areFriends_add_by_user_results = mysql_query("SELECT transform_add_id,
		    														  add_uid_a,
										   							  add_uid_b
														    		  from transform_add AS TRANADD
														    		  where TRANADD.status = 1 AND
														    		  		TRANADD.add_uid_a = ".$arr_areFriends[$areFriends_index]['uid2']." AND
														    		  	    TRANADD.add_uid_b = ".$arr_areFriends[$areFriends_index]['uid1']." AND
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
					$arr_areFriends[$areFriends_index]['are_friends'] = '1';
				}
	    	}
	    	else if($arr_areFriends[$areFriends_index]['are_friends'] == '1')
	    	{
	    		//set to false if either of them has removed the virtual friendship connection
	    		$areFriends_remove_by_user_results = mysql_query("SELECT transform_remove_id,
	    																 remove_uid_a,
									   					   				 remove_uid_b
													       				 from transform_remove AS TRANREM
													       				 where TRANREM.remove_uid_a = ".$arr_areFriends[$areFriends_index]['uid1']." AND
													       				 	   TRANREM.remove_uid_b = ".$arr_areFriends[$areFriends_index]['uid2']." AND
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
					$arr_areFriends[$areFriends_index]['are_friends'] = '0';
				}
				
	    		$areFriends_remove_by_user_results = mysql_query("SELECT transform_remove_id,
	    																 remove_uid_a,
									   					   				 remove_uid_b
													       				 from transform_remove AS TRANREM
													       				 where TRANREM.remove_uid_a = ".$arr_areFriends[$areFriends_index]['uid2']." AND
													       				 	   TRANREM.remove_uid_b = ".$arr_areFriends[$areFriends_index]['uid1']." AND
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
					$arr_areFriends[$areFriends_index]['are_friends'] = '0';
				}
	    	}
		}
    
		$result = json_encode($arr_areFriends);
		
		//------------------------------------------------------------------------------------------
	}
	else if($api_method == 'facebook.friends.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
	  		$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'friends.get');
  		if(isset($_POST['flid']))
	  		$api_array['flid'] = $_POST['flid'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
		
		if(!strripos($result, 'error_code')) // if not a facebook error message
		{
			$result = json_decode($result, true);
			
			$friendsget_uid = $faith_uid;	// determine the uid whose friend list is returned
			
			if(strlen($_POST['uid']) > 0)
			{
				$friendsget_uid = $_POST['uid'];
			}
			
			if(strlen($_POST['flid']) == 0) //when not list
			{
			//add friendship according to the rule created by target uid
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
				if(!in_array($add_uid_b, $result))
				{
					$result[] = $add_uid_b;
				}
			}
			
			//add friendship according to the rule created by the friends of target uid-----------------
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
				if(!in_array($add_uid_a, $result))
				{
					$result[] = $add_uid_a;
				}
			}
			}
			
			//remove friendship according to the rule created by user-------------------------------------------
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
				if(in_array($remove_uid_b, $result))
				{
					$remove_index = array_search($remove_uid_b, $result);
					array_splice($result, $remove_index, 1);
				}
			}
			
			//remove friendship according to the rule created by friends----------------------------------------
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
				if(in_array($remove_uid_a, $result))
				{
					$remove_index = array_search($remove_uid_a, $result);
					array_splice($result, $remove_index, 1);
				}
			}
			
			$result = json_encode($result);
		}										 
													 
		//---------------------------------------------------------------------------------------------											 
		
	}
	else if($api_method == 'facebook.friends.getappusers')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'friends.getAppUsers');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
		
		
		$result = json_decode($result, true);
    
    	$facebook->Set_Is_FAITH_REST(false);
		
		//add friendship according to the rule created by user-------------------------------------------
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
			try
			{
				/*$isAppUser = $facebook->api(array('method' => 'users.isAppUser',
								   				  'uid' => $add_uid_b));*/
		  		
				if(!in_array($add_uid_b, $result))// && $isAppUser)
				{
						$result[] = $add_uid_b;
				}
			}
			catch (FacebookApiException $e)
			{
				
			}
		}
		//-----------------------------------------------------------------------------------------------
		
		//add friendship according to the rule created by friends----------------------------------------
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
			
			try
			{
				/*$isAppUser = $facebook->api(array('method' => 'users.isAppUser',
								   				  'uid' => $add_uid_a));*/
		  		
				if(!in_array($add_uid_a, $result))// && $isAppUser)
				{
						$result[] = $add_uid_a;
				}
			}
			catch (FacebookApiException $e)
			{
				
			}
		}
		
		$facebook->Set_Is_FAITH_REST(true);
		//-----------------------------------------------------------------------------------------------
		
		//remove friendship according to the rule created by user-------------------------------------------
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
			if(in_array($remove_uid_b, $result))
			{
				$remove_index = array_search($remove_uid_b, $result);
				array_splice($result, $remove_index, 1);
			}
		}
		//-----------------------------------------------------------------------------------------------
		
		//remove friendship according to the rule created by friends----------------------------------------
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
			if(in_array($remove_uid_a, $result))
			{
				$remove_index = array_search($remove_uid_a, $result);
				array_splice($result, $remove_index, 1);
			}
		}
		
		$result = json_encode($result);
		
		//-----------------------------------------------------------------------------------------------
	}
	else if($api_method == 'facebook.friends.getlists')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'friends.getLists');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.friends.getmutualfriends')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'friends.getMutualFriends');
  		if(isset($_POST['target_uid']))
	  		$api_array['target_uid'] = $_POST['target_uid'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['source_uid']))
	  		$api_array['source_uid'] = $_POST['source_uid'];
	  	$result = $facebook->api($api_array);
													 			  
		if(!strripos($result, 'error_code')) // if not a facebook error message
		{
			$result = json_decode($result, true);
			
			$GMF_source_uid = $faith_uid;	// determine the uid whose friend list is returned
			$GMF_target_uid = $_POST['target_uid'];
			
			if(strlen($_POST['source_uid']) > 0)
			{
				$GMF_source_uid = $_POST['source_uid'];
			}

			//$facebook->Set_Is_FAITH_REST(false);
			
			//add friendship according to the rule created by source id-------------------------------------------
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
				
				$GMF_areFriend_result = json_decode($facebook->api(array('method' => 'friends.areFriends',
															 'uids1' => array($add_uid_b),
															 'uids2' => array($GMF_target_uid),)), true);
				
				if(!in_array($add_uid_b, $result) &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$result[] = $add_uid_b;
				}
			}											
			//-----------------------------------------------------------------------------------------------
			
			//add friendship according to the rule created by friends of source id----------------------------------------
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
				
				$GMF_areFriend_result = json_decode($facebook->api(array('method' => 'friends.areFriends',
															 'uids1' => array($add_uid_a),
															 'uids2' => array($GMF_target_uid),)), true);
				
				if(!in_array($add_uid_a, $result) &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$result[] = $add_uid_a;
				}
			}
			//-----------------------------------------------------------------------------------------------
			
			//add friendship according to the rule created by target id-------------------------------------------
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
				
				$GMF_areFriend_result = json_decode($facebook->api(array('method' => 'friends.areFriends',
															 'uids1' => array($add_uid_b),
															 'uids2' => array($GMF_source_uid),)), true);
				
				if(!in_array($add_uid_b, $result) &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$result[] = $add_uid_b;
				}
			}											
			//-----------------------------------------------------------------------------------------------
			
			//add friendship according to the rule created by friends of target id----------------------------------------
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
				
				$GMF_areFriend_result = json_decode($facebook->api(array('method' => 'friends.areFriends',
															 'uids1' => array($add_uid_a),
															 'uids2' => array($GMF_source_uid),)), true);
				
				if(!in_array($add_uid_a, $result) &&
					$GMF_areFriend_result[0]['are_friends'] == '1')
				{
					$result[] = $add_uid_a;
				}
			}
			//-----------------------------------------------------------------------------------------------
			
			//remove friendship according to the rule created by source id & target id-----------------------
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
				
				if(in_array($remove_uid_b, $result))
				{
					$remove_index = array_search($remove_uid_b, $result);
					array_splice($result, $remove_index, 1);
				}
			}
			//-----------------------------------------------------------------------------------------------
			
			//remove friendship according to the rule created by the friends of source id & target id--------
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
				
				if(in_array($remove_uid_a, $result))
				{
					$remove_index = array_search($remove_uid_a, $result);
					array_splice($result, $remove_index, 1);
				}
			}
			
			//$facebook->Set_Is_FAITH_REST(true);
			
			$result = json_encode($result);
		}
	}
	else if($api_method == 'facebook.groups.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'groups.get');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['gids']))
	  		$api_array['gids'] = $_POST['gids'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.groups.getmembers')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'groups.getMembers');
  		if(isset($_POST['gid']))
	  		$api_array['gid'] = $_POST['gid'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.links.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'links.get');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	if(isset($_POST['link_ids']))
	  		$api_array['link_ids'] = $_POST['link_ids'];
	  	if(isset($_POST['limit']))
	  		$api_array['limit'] = $_POST['limit'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.notes.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'notes.get');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.notifications.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'notifications.get');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.pages.getinfo')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'pages.getinfo');
  		if(isset($_POST['fields']))
	  		$api_array['fields'] = $_POST['fields'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['page_ids']))
	  		$api_array['page_ids'] = $_POST['page_ids'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.pages.isadmin')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'pages.isAdmin');
  		if(isset($_POST['page_id']))
	  		$api_array['page_id'] = $_POST['page_id'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.pages.isappadded')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'pages.isAppAdded');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['page_id']))
	  		$api_array['page_id'] = $_POST['page_id'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.pages.isfan')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'pages.isFan');
  		if(isset($_POST['page_id']))
	  		$api_array['page_id'] = $_POST['page_id'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	    if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.status.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'status.get');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	    if(isset($_POST['limit']))
	  		$api_array['limit'] = $_POST['limit'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.stream.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'stream.get');
  		if(isset($_POST['viewer_id']))
	  		$api_array['viewer_id'] = $_POST['viewer_id'];
	  	if(isset($_POST['source_ids']))
	  		$api_array['source_ids'] = $_POST['source_ids'];
	    if(isset($_POST['start_time']))
	  		$api_array['start_time'] = $_POST['start_time'];
	  	if(isset($_POST['end_time']))
	  		$api_array['end_time'] = $_POST['end_time'];
	  	if(isset($_POST['limit']))
	  		$api_array['limit'] = $_POST['limit'];
	    if(isset($_POST['filter_key']))
	  		$api_array['filter_key'] = $_POST['filter_key'];
	  	if(isset($_POST['metadata']))
	  		$api_array['metadata'] = $_POST['metadata'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.stream.getcomments')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'stream.getComments');
  		if(isset($_POST['post_id']))
	  		$api_array['post_id'] = $_POST['post_id'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.stream.getfilters')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'stream.getFilters');
  		if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.users.getinfo')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'users.getInfo');
  		if(isset($_POST['uids']))
	  		$api_array['uids'] = $_POST['uids'];
	  	if(isset($_POST['fields']))
	  		$api_array['fields'] = $_POST['fields'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.users.getloggedinuser')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'users.getLoggedInUser');
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.users.getstandardinfo')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'users.getStandardinfo');
  		if(isset($_POST['uids']))
	  		$api_array['uids'] = $_POST['uids'];
	  	if(isset($_POST['fields']))
	  		$api_array['fields'] = $_POST['fields'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.users.hasapppermission')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'users.hasAppPermission');
  		if(isset($_POST['ext_perm']))
	  		$api_array['ext_perm'] = $_POST['ext_perm'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.users.isappuser')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			//$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'users.isAppUser');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.users.isverified')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'users.isVerified');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.video.getuploadlimits')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'video.getUploadLimits');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.links.post')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'links.post');
	  		if(isset($_POST['url']))
		  		$api_array['url'] = $_POST['url'];
		  	if(isset($_POST['comment']))
		  		$api_array['comment'] = $_POST['comment'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['image']))
		  		$api_array['image'] = $_POST['image'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.links.preview')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'links.preview');
	  		if(isset($_POST['url']))
		  		$api_array['url'] = $_POST['url'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['format']))
		  		$api_array['format'] = $_POST['format'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.livemessage.send')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'liveMessage.send');
	  		if(isset($_POST['recipient']))
		  		$api_array['recipient'] = $_POST['recipient'];
		  	if(isset($_POST['event_name']))
		  		$api_array['event_name'] = $_POST['event_name'];
		  	if(isset($_POST['message']))
		  		$api_array['message'] = $_POST['message'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.notes.create')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'notes.create');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['title']))
		  		$api_array['title'] = $_POST['title'];
		  	if(isset($_POST['content']))
		  		$api_array['content'] = $_POST['content'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.notes.delete')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'notes.delete');
	  		if(isset($_POST['note_id']))
		  		$api_array['note_id'] = $_POST['note_id'];
		  	if(isset($_POST['title']))
		  		$api_array['title'] = $_POST['title'];
		  	if(isset($_POST['content']))
		  		$api_array['content'] = $_POST['content'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.notes.edit')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'notes.edit');
	  		if(isset($_POST['note_id']))
		  		$api_array['note_id'] = $_POST['note_id'];
		  	if(isset($_POST['title']))
		  		$api_array['title'] = $_POST['title'];
		  	if(isset($_POST['content']))
		  		$api_array['content'] = $_POST['content'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.stream.addcomment')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'stream.addComment');
	  		if(isset($_POST['post_id']))
		  		$api_array['post_id'] = $_POST['post_id'];
		  	if(isset($_POST['comment']))
		  		$api_array['comment'] = $_POST['comment'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.stream.publish')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'stream.publish');
	  		if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['message']))
		  		$api_array['message'] = $_POST['message'];
		  	if(isset($_POST['attachment']))
		  		$api_array['attachment'] = $_POST['attachment'];
		  	if(isset($_POST['action_links']))
		  		$api_array['action_links'] = $_POST['action_links'];
		  	if(isset($_POST['target_id']))
		  		$api_array['target_id'] = $_POST['target_id'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['privacy']))
		  		$api_array['privacy'] = $_POST['privacy'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.stream.addlike')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'stream.addLike');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['post_id']))
		  		$api_array['post_id'] = $_POST['post_id'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.stream.remove')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'stream.remove');
	  		if(isset($_POST['post_id']))
		  		$api_array['post_id'] = $_POST['post_id'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.stream.removecomment')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'stream.removeComment');
	  		if(isset($_POST['comment_id']))
		  		$api_array['comment_id'] = $_POST['comment_id'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.stream.removelike')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'stream.removeLike');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['post_id']))
		  		$api_array['post_id'] = $_POST['post_id'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.status.set')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'status.set');
	  		if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['status']))
		  		$api_array['status'] = $_POST['status'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.users.setstatus')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'users.setStatus');
	  		if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['status']))
		  		$api_array['status'] = $_POST['status'];
		  	if(isset($_POST['clear']))
		  		$api_array['clear'] = $_POST['clear'];
		  	if(isset($_POST['status_includes_verb']))
		  		$api_array['status_includes_verb'] = $_POST['status_includes_verb'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.sms.cansend')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'sms.canSend');
  		if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.sms.send')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'sms.send');
	  		if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['message']))
		  		$api_array['message'] = $_POST['message'];
		  	if(isset($_POST['session_id']))
		  		$api_array['session_id'] = $_POST['session_id'];
		  	if(isset($_POST['req_session']))
		  		$api_array['req_session'] = $_POST['req_session'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.addglobalnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
	  		$api_array = array('method' => 'dashboard.addGlobalNews');
	  		if(isset($_POST['news']))
		  		$api_array['news'] = $_POST['news'];
		  	if(isset($_POST['image']))
		  		$api_array['image'] = $_POST['image'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.addnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.addNews');
	  		if(isset($_POST['news']))
		  		$api_array['news'] = $_POST['news'];
		  	if(isset($_POST['image']))
		  		$api_array['image'] = $_POST['image'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.clearglobalnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.clearGlobalNews');
	  		if(isset($_POST['news_ids']))
		  		$api_array['news_ids'] = $_POST['news_ids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.clearnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.clearNews');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	if(isset($_POST['news_ids']))
		  		$api_array['news_ids'] = $_POST['news_ids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.decrementcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.decrementCount');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.getactivity')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'dashboard.getActivity');
  		if(isset($_POST['activity_ids']))
	  		$api_array['activity_ids'] = $_POST['activity_ids'];
	  	if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.dashboard.getcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'dashboard.getCount');
  		if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.dashboard.getglobalnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'dashboard.getGlobalNews');
  		if(isset($_POST['news_ids']))
	  		$api_array['news_ids'] = $_POST['news_ids'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.dashboard.getnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'dashboard.getNews');
  		if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	if(isset($_POST['news_ids']))
	  		$api_array['news_ids'] = $_POST['news_ids'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.dashboard.incrementcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.incrementCount');
	  		if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.multiaddnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.multiAddNews');
	  		if(isset($_POST['uids']))
		  		$api_array['uids'] = $_POST['uids'];
		  	if(isset($_POST['news']))
		  		$api_array['news'] = $_POST['news'];
		  	if(isset($_POST['image']))
		  		$api_array['image'] = $_POST['image'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.multiclearnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.multiClearNews');
	  		if(isset($_POST['ids']))
		  		$api_array['ids'] = $_POST['ids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.multidecrementcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.multiDecrementCount');
	  		if(isset($_POST['uids']))
		  		$api_array['uids'] = $_POST['uids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.multigetcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'dashboard.multiGetCount');
  		if(isset($_POST['uids']))
	  		$api_array['uids'] = $_POST['uids'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.dashboard.multigetnews')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'dashboard.multiGetNews');
  		if(isset($_POST['ids']))
	  		$api_array['ids'] = $_POST['ids'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.dashboard.multiincrementcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.multiIncrementCount');
	  		if(isset($_POST['uids']))
		  		$api_array['uids'] = $_POST['uids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.multisetcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.multiSetCount');
	  		if(isset($_POST['ids']))
		  		$api_array['ids'] = $_POST['ids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.publishactivity')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.publishActivity');
	  		if(isset($_POST['activity']))
		  		$api_array['activity'] = $_POST['activity'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.removeactivity')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.removeActivity');
	  		if(isset($_POST['activity_ids']))
		  		$api_array['activity_ids'] = $_POST['activity_ids'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.dashboard.setcount')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'dashboard.setCount');
	  		if(isset($_POST['count']))
		  		$api_array['count'] = $_POST['count'];
		  	if(isset($_POST['uid']))
		  		$api_array['uid'] = $_POST['uid'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	
	else if($api_method == 'facebook.photos.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'photos.get');
  		if(isset($_POST['subj_id']))
	  		$api_array['subj_id'] = $_POST['subj_id'];
	  	if(isset($_POST['aid']))
	  		$api_array['aid'] = $_POST['aid'];
	  	if(isset($_POST['pids']))
	  		$api_array['pids'] = $_POST['pids'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.photos.getalbums')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
		
  		$api_array = array('method' => 'photos.getAlbums');
  		if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	if(isset($_POST['aids']))
	  		$api_array['aids'] = $_POST['aids'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	
	else if($api_method == 'facebook.events.cancel')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'events.cancel');
	  		if(isset($_POST['eid']))
		  		$api_array['eid'] = $_POST['eid'];
		  	if(isset($_POST['cancel_message']))
		  		$api_array['cancel_message'] = $_POST['cancel_message'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.events.create')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'events.create');
	  		if(isset($_POST['event_info']))
		  		$api_array['event_info'] = $_POST['event_info'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['[no name]']))
		  		$api_array['[no name]'] = $_POST['[no name]'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.events.edit')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'events.edit');
	  		if(isset($_POST['eid']))
		  		$api_array['eid'] = $_POST['eid'];
		  	if(isset($_POST['event_info']))
		  		$api_array['event_info'] = $_POST['event_info'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	else if($api_method == 'facebook.events.get')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'events.get');
  		if(isset($_POST['uid']))
	  		$api_array['uid'] = $_POST['uid'];
	  	if(isset($_POST['eids']))
	  		$api_array['eids'] = $_POST['eids'];
	  	if(isset($_POST['start_time']))
	  		$api_array['start_time'] = $_POST['start_time'];
	  	if(isset($_POST['end_time']))
	  		$api_array['end_time'] = $_POST['end_time'];
	  	if(isset($_POST['rsvp_status']))
	  		$api_array['rsvp_status'] = $_POST['rsvp_status'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.events.getmembers')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
		$api_array = array('method' => 'events.getMembers');
  		if(isset($_POST['eid']))
	  		$api_array['eid'] = $_POST['eid'];
	  	if(isset($_POST['callback']))
	  		$api_array['callback'] = $_POST['callback'];
	  	$result = $facebook->api($api_array);
	}
	else if($api_method == 'facebook.events.invite')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			//$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'events.invite');
	  		if(isset($_POST['eid']))
		  		$api_array['eid'] = $_POST['eid'];
		  	if(isset($_POST['uids']))
		  		$api_array['uids'] = $_POST['uids'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	if(isset($_POST['personal_message']))
		  		$api_array['personal_message'] = $_POST['personal_message'];
		  	$result = $facebook->api($api_array);
		  	
		  	
	  	}
	}
	else if($api_method == 'facebook.events.rsvp')
	{
		if(isset($_POST['access_token']))
	  	{
	  		$access_token = $_POST['access_token'];
			$facebook->Set_Access_Token_For_FAITH($access_token);
		}
  	
	  	if($allowed == '1')
	  	{
			$api_array = array('method' => 'events.rsvp');
	  		if(isset($_POST['eid']))
		  		$api_array['eid'] = $_POST['eid'];
		  	if(isset($_POST['rsvp_status']))
		  		$api_array['rsvp_status'] = $_POST['rsvp_status'];
		  	if(isset($_POST['callback']))
		  		$api_array['callback'] = $_POST['callback'];
		  	$result = $facebook->api($api_array);
	  	}
	}
	
	fwrite($fh, "(iframerestserver.php)end query for $api_method \n");
	
	$result = str_replace('","', '" , "', $result);
	$result = str_replace('},{', '} , {', $result);
	
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
	$query = sprintf("INSERT INTO access_log (uid, 
											  app_id,
											  allowed,
											  access_time,
											  logdetails,
											  url_id,
											  api_id,
											  app_ip_addr,
											  user_ip_addr) 
											  VALUES('%s','%s','%s','%s','%s','%s',(SELECT id FROM restapi where facebook_method = '$api_method'),INET_ATON('$app_ip_addr'),INET_ATON('$faith_client_ip'))",
											  $faith_uid,
											  mysql_real_escape_string($faith_app_id),
											  mysql_real_escape_string($allowed),
											  mysql_real_escape_string($time_added),
											  mysql_real_escape_string($result),
											  mysql_real_escape_string($faith_url_id));
	
	//'$result'
	if(!mysql_query($query))
	{
		fwrite($fh, "(iframerestserver.php)Query failed" . mysql_error() ."\n");
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






