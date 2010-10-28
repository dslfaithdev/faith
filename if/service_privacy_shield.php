<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title> Welcome to DSL FAITH (IFrame) </title>
<style type="text/css">
<?php echo htmlentities(file_get_contents('../faith_style.css', true)); ?>
</style>
</head>
<body>
<center>
<table cellspacing="0" cellpadding="0" width="750px">
<tr>
	<td>
	<?php 
	require_once '../func.php';
	require_once '../vars.php';
	require_once '../if/src/facebook.php';
	try
	{	
	mysqlSetup($db);
	$facebook = new Facebook(array('appId'  => $iframe_appid,
								   'secret' => $iframe_appsecret,
								   'cookie' => true,));
	
	$user_id = $facebook->getUser();
	$session = $facebook->getSession();
	
	$has_permission = file_get_contents(
	'https://api.facebook.com/method/users.hasAppPermission?ext_perm=read_friendlists&access_token='.$session['access_token'].'&format=json'); 
	
	if(!$has_permission ||
		strripos($has_permission, 'error_code'))
	{
		$facebook->request($facebook_iframe_canvas_page_url,
					   	   'publish_stream,email,create_event,read_stream,sms,rsvp_event,offline_access,read_friendlists,email');
	}
	
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
	
	display_header_links_if($div_counter, $user_id);
	}
	catch (Exception $e)
	{
		echo 'Caught database exception: ',  $e->getMessage(), "\n";
	}
	?>
	</td>
</tr>
	<div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId(); ?>',
          session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });
        
        FB.Canvas.setAutoResize();
      };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
<tr>
	<td height="10px"></td>
</tr>
<tr>
	<td style="height: 650px;vertical-align:top;border-right: #3b5998 3px solid;border-top: #3b5998 3px solid;border-left: #3b5998 3px solid;border-bottom: #3b5998 3px solid;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="3" height="20px"></td>
	</tr>
	<?php 
		if($_POST['enable_setting_hidden_input'] == 'from_setting')
		{
			$enable_value_update = '0';
			
			if(isset($_POST['enable_setting_input']) && strtolower($_POST['enable_setting_input']) == 'on')
			{
				$enable_value_update = $_POST['enable_setting_hidden_input_value'];
			}
			
			$query = sprintf("UPDATE privacy_settings SET enable = '%s'
													   WHERE uid = '%s'",
													   mysql_real_escape_string($enable_value_update),
													   $user_id);
									
			if(!mysql_query($query))
		    {
		    	echo '<div class="fberrorbox" style="width: 500px;">  
		    			  Failed to update the privacy setting!
						  </div><br />';
		    }
			else
		    {
		    	echo '<div class="fbbluebox">  
	    			Privacy setting has been changed successfully!  
					</div><br />';
		    }
		}
				
	?>
	<tr>
		<td width="5%"></td>
		<td width="90%">
		<form style="white-space:nowrap;" action="<?php echo $source_server_url ?>if/service_privacy_shield.php?setting=<?php echo $_GET['setting'] ?>&signed_request=<?php echo $_GET['signed_request'] ?>" method="post">
		<table width="100%" style="background-image:url('<?php echo $source_server_url; ?>image/faith_background.gif');background-repeat:repeat-x;border:1px solid #d4d4d4;">
		<tr>
			<td width="35%">
			<font style="padding-left:20px;font-weight: bolder;font-size: 11pt;font-family: Verdana, Arial;line-height: 40px;text-align: left;color: #3b5998;">
			Privacy Shield
			</font>
			</td>
			<td width="65%">
			<font style="font-weight: bolder;padding-left:20px;font-size: 8pt;font-family: Verdana, Arial;line-height: 30px;text-align: left;color: #AA3333;">
			<input type="hidden" name="enable_setting_hidden_input" value="from_setting"/>
			<input type="hidden" name="enable_setting_hidden_input_value" value="<?php echo $_GET['setting'] ?>"/>
			<?php 
				GLOBAL $user_id;
				GLOBAL $db;
		
				$results = mysql_query("SELECT enable
											   from privacy_settings
											   where uid = $user_id", $db);
				
				$privacy_disable = 'disabled="disabled"';
				$enable_setting_checkbox = '';
				while($row = mysql_fetch_array($results))
				{
					$privacy_disable = '';
					$enable = $row['enable'];
					
					if($enable == $_GET['setting'])
					{
						$enable_setting_checkbox = 'CHECKED';
					}
				}
			?>
			<input onclick="this.form.submit()" type="CHECKBOX" name="enable_setting_input" id="enable_setting_input" <?php GLOBAL $privacy_disable; echo $privacy_disable; ?> 
				<?php GLOBAL $enable_setting_checkbox; echo $enable_setting_checkbox; ?>>USE THIS SETTING FOR ALL YOUR WALL POSTS IN FAITH</input>
			</font>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<font style="padding-left:20px;font-size: 8pt;font-family: Verdana, Arial;line-height: 30px;text-align: left;color: #333333;">
			Recommend you a better privacy setting for your wall posts.
			</font>
			</td>
		</tr>
		</table>
		</form>
		</td>
		<td width="5%"></td>
	</tr>
<?php

// 	0 is default setting
//	setting==1 	quality_tie
//	setting==2	category_friendlist		category_friendlist_attri
//	setting==3	keyword_interests		keyword_interests_attri

require_once '../vars.php';
require_once '../if/src/facebook.php';

try
{	
	GLOBAL $user_id;
	
	if(!init_settings('', ''))
	{
		return;
	}
	
	try
	{
		GLOBAL $db;
		
		$target_field = 'quality_tie';
		if($_GET['setting'] == '2')
		{
			$target_field = 'category_friendlist';
		}
		else if($_GET['setting'] == '3')
		{
			$target_field = 'keyword_interests';
		}
		
		$privacy_setting_results = mysql_query("SELECT $target_field FROM privacy_settings 
    																 WHERE uid = $user_id;", $db);
    	
		$list = '';
		while($privacy_setting_row = mysql_fetch_array($privacy_setting_results))
		{
			$list = $privacy_setting_row[$target_field];
		}
		
		if($_POST['add_friend_submit'] == 'Add')
		{
			$uid = $_POST['modify_friend_selector'];
			$lid = $_POST['friend_list_select'];
			
			if($lid != '0' && $lid != NULL)
			{
				$friend_results = $facebook->api(array('method'=>'friends.get',
													   'flid'=>$lid));
				
				if(count($friend_results) > 0)
				{
					foreach($friend_results as $friend_id)
					{
						if(substr_count($list, $friend_id.',') == '0')
						{
							$query = sprintf("UPDATE privacy_settings SET $target_field = '%s'
															   		  WHERE uid = '%s'",
															   		  mysql_real_escape_string($list.$friend_id.','),
															   		  $user_id);
											
						    $list = $list.$friend_id.',';
							if(!mysql_query($query))
						    {
							    echo '<div class="fberrorbox" style="width: 500px;">  
					    			  Failed to add the user!  
									  </div><br />';
						    } 
						}
					}
					echo '<div class="fbbluebox">  
				    			You have successfully added the friend!  
								</div><br />';
				}
			}
			else if($uid != NULL)
			{
				if(substr_count($list, $uid.',') == '0')
				{
					$query = sprintf("UPDATE privacy_settings SET $target_field = '%s'
													   		  WHERE uid = '%s'",
													   		  mysql_real_escape_string($list.$uid.','),
													   		  $user_id);
									
					if(!mysql_query($query))
				    {
					    echo '<div class="fberrorbox" style="width: 500px;">  
			    			  Failed to add the user!  
							  </div><br />';
				    } 
				    else
				    {
				    	echo '<div class="fbbluebox">  
		    			You have successfully added the friend!  
						</div><br />';
				    }
				}
				else
				{
					echo '<div class="fberrorbox">  
	    			You have already added the friend!  
					</div><br />';
				}
			}
			else
			{
				echo '<div class="fberrorbox" style="width: 500px;">  
		    			  Please select a friendlist or enter a valid friend name!  
						  </div><br />';
			}
		}
		else if($_POST['remove_friend_submit'] == 'Remove')
		{
			$uid = $_POST['modify_friend_selector'];
			$lid = $_POST['friend_list_select'];
			
			if($lid != '0' && $lid != NULL)
			{
				$friend_results = $facebook->api(array('method'=>'friends.get',
													   'flid'=>$lid));
				
				if(count($friend_results) > 0)
				{
					foreach($friend_results as $friend_id)
					{
						if(substr_count($list, $friend_id.',') > '0')
						{
							$list = str_replace($friend_id.',', '', $list);
							
							$query = sprintf("UPDATE privacy_settings SET $target_field = '%s'
															   		  WHERE uid = '%s'",
															   		  mysql_real_escape_string($list),
															   		  $user_id);
											
							if(!mysql_query($query))
						    {
							    echo '<div class="fberrorbox" style="width: 500px;">  
					    			  Failed to remove the user!  
									  </div><br />';
						    } 
						}
					}
					echo '<div class="fbbluebox">  
				    			You have successfully removed the friend!  
								</div><br />';
				}
			}
			else if($uid != NULL)
			{
				if(substr_count($list, $uid.',') > '0')
				{
					$list = str_replace($uid.',', '', $list);
					
					$query = sprintf("UPDATE privacy_settings SET $target_field = '%s'
													   		  WHERE uid = '%s'",
													   		  mysql_real_escape_string($list),
													   		  $user_id);
									
					if(!mysql_query($query))
				    {
					    echo '<div class="fberrorbox" style="width: 500px;">  
			    			  Failed to remove the user!  
							  </div><br />';
				    } 
				    else
				    {
				    	echo '<div class="fbbluebox">  
		    			You have successfully removed the friend!  
						</div><br />';
				    }
				}
				else
				{
					echo '<div class="fberrorbox">  
	    			The friend is not in the setting!  
					</div><br />';
				}
			}
			else
			{
				echo '<div class="fberrorbox" style="width: 500px;">  
		    			  Please select a friendlist or enter a valid friend name!  
						  </div><br />';
			}
		}
		else if($_POST['textarea_submit'] == 'Share')
		{
			if(strlen($_POST['privacy_textarea']) > '0')
			{
				$message = $_POST['privacy_textarea'];
				
				$sql_privacy_field = 'quality_tie';
				if($_GET['setting'] == '2')
				{
					$sql_privacy_field = 'category_friendlist';
				}
				else if($_GET['setting'] == '3')
				{
					$sql_privacy_field = 'keyword_interests';
				}
				
				$privacy_results = mysql_query("SELECT $sql_privacy_field FROM privacy_settings 
		    															  WHERE uid = $user_id;", $db);
		    	
				while($privacy_row = mysql_fetch_array($privacy_results))
				{
					$privacy_list = $privacy_row[$sql_privacy_field];
				}
				
				if(strlen($privacy_list) >= 2)
				{
					$privacy_string = '{"value":"CUSTOM","friends":"SOME_FRIENDS","allow":"'.$privacy_list.'",}';
					
					$results = $facebook->api(array('method'=>'stream.publish',
	    											'message'=>$message,
													'privacy'=>$privacy_string,));
	    
				    echo '<div class="fbbluebox">  
	    			Your message has been posted sucessfully!  
					</div><br />';
				}
			}
			else
			{
				echo '<div class="fberrorbox">
					  Message cannot be empty!
					  </div><br />';
			}
		}
		else if($_POST['get_suggestions_submit'] == 'Get Privacy Suggestions')
		{
			$post_params = array();
			$attributes = '';
			
			if($_GET['setting'] == '1')
			{
				$post_params[] = 'priv_level='.urlencode('r_quality');
				
				$privacy_recommendation = '["765554109", "3200156", "3219599", "695694021", "759410694", "581205756", "621651366"]';
			}
			else if($_GET['setting'] == '2')
			{
				$post_params[] = 'priv_level='.urlencode('group');
				$attributes = $_POST['privacy_txt'];
				
				if($_POST['Family_input'] == 'on')
				{
					$post_params[] = 'group_name='.urlencode('family');
					$attributes .= '<s>family</s>';
				}
				
				if($_POST['Work_input'] == 'on')
				{
					$post_params[] = 'group_name='.urlencode('work');
					$attributes .= '<s>work</s>';
				}
				
				if($_POST['Schoolt_input'] == 'on')
				{
					$post_params[] = 'group_name='.urlencode('school');
					$attributes .= '<s>school</s>';
				}
				
				$post_params[] = 'ui_list_names='.urlencode($_POST['privacy_txt']);
				
				$privacy_recommendation = '["765554109","710706363","3200156","3219599","695694021","1004264792","3224061","1214439232","639273140","759410694","1080532999","581205756","621651366","513817635"]';
			}
			else if($_GET['setting'] == '3')
			{
				$post_params[] = 'priv_level='.urlencode('interests');
				$post_params[] = 'ui_interests='.urlencode($_POST['privacy_txt']);
				$attributes = $_POST['privacy_txt'];
				
				$privacy_recommendation = '["695694021","1004264792","3224061","1214439232","639273140","759410694","1080532999","581205756","621651366","513817635"]';
			}
		    
			$postStr = implode('&', $post_params);
			
			$opts = array(
			  'http'=>array(
			    'method'=>"POST",
			    'header'=>"Accept-language: en\r\n" .
			              "Cookie: \r\n",
				'content'=>$postStr 
			  )
			);
			
			$context = stream_context_create($opts);
			//$privacy_recommendation = file_get_contents('http://cyrus.cs.ucdavis.edu/~banksl/hellominifb/priv_shield.py/callbackMain?access_token='.$session['access_token'], false, $context);
			
			//echo 'Result from Privacy Setting is = ' . htmlspecialchars($privacy_recommendation);	
			
			init_settings($privacy_recommendation, $attributes);
		}
	}
	catch (Exception $e)
	{
		echo '<div class="fberrorbox" style="width: 500px;">'. 
		    			  $e->getMessage() 
						  .'</div><br />';
	}
} 
catch (Exception $e)
{
	echo '<div class="fberrorbox" style="width: 500px;">'. 
		    			  $e->getMessage() 
						  .'</div><br />';
}

?>
	<tr>
		<td width="5%"></td>
		<td width="90%" style="padding-bottom: 10px;border-bottom: #AAAAAA 1px solid;">
		<table width="100%">
		<tr>
			<td style="border-bottom: #AAAAAA 1px solid;">
			<font style="line-height:30px;font-weight: bolder;font-size: 9pt;font-family: Verdana, Arial;color: #111111;">Share Your Status</font>
			<form style="white-space:nowrap;" action="<?php echo $source_server_url ?>if/service_privacy_shield.php?setting=<?php echo $_GET['setting'] ?>&signed_request=<?php echo $_GET['signed_request'] ?>" method="post">
			<textarea id="privacy_textarea" name="privacy_textarea" style="width:580px;height:40px;"><?php echo $_POST['privacy_textarea'] ?></textarea>
			<INPUT class="PrivacyShieldButtonStyle" type="submit" id="textarea_submit" name = "textarea_submit" value="Share" />
			</form>
			</td>
		</tr>
		<tr>
			<td>
			<form style="white-space:nowrap;" action="<?php echo $source_server_url ?>if/service_privacy_shield.php?setting=<?php echo $_GET['setting'] ?>&signed_request=<?php echo $_GET['signed_request'] ?>" method="post">
			<table width="100%">
			<tr>
				<td width="60%" style="padding-top:10px;">
				<?php
				
				GLOBAL $user_id;
				GLOBAL $db;
				
				if($_GET['setting'] == '3')
			  	{
				  	$privacy_setting_results = mysql_query("SELECT keyword_interests_attri FROM privacy_settings 
			    																 	 	   WHERE uid = $user_id;", $db);
					
				  	$privacy_txt = '';
					while($privacy_setting_row = mysql_fetch_array($privacy_setting_results))
					{
						$privacy_txt = $privacy_setting_row['keyword_interests_attri'];
					}
				
			  		echo '<font style="font-weight: bolder;font-size: 9pt;line-height: 20px;color: #AA3333;">Please enter keywords</font>'
			  			  .'<font style="padding-left: 10px;font-size: 9pt;line-height: 20px;color: #555555;">'."(must be separated by ';')</font><br />".
			  			  '<input type="text" name="privacy_txt" id="privacy_txt" maxlength="5000" style="width: 300px;"
							 value="'.$privacy_txt.'"></input>';
			  	}
			  	else if($_GET['setting'] == '2')
			  	{
			  		$privacy_setting_results = mysql_query("SELECT category_friendlist_attri FROM privacy_settings 
			    																 	 		 WHERE uid = $user_id;", $db);
					
				  	$privacy_txt = '';
					while($privacy_setting_row = mysql_fetch_array($privacy_setting_results))
					{
						$privacy_txt = $privacy_setting_row['category_friendlist_attri'];
					}
					
					$family_checked = '';
					$work_checked = '';
					$school_checked = '';
					
					if(strripos($privacy_txt, 's>family</s>'))
					{
						$family_checked = 'CHECKED';
						$privacy_txt = str_replace('<s>family</s>', '', $privacy_txt);
					}
					
			  		if(strripos($privacy_txt, 's>work</s>'))
					{
						$work_checked = 'CHECKED';
						$privacy_txt = str_replace('<s>work</s>', '', $privacy_txt);
					}
					
			  		if(strripos($privacy_txt, 's>school</s>'))
					{
						$school_checked = 'CHECKED';
						$privacy_txt = str_replace('<s>school</s>', '', $privacy_txt);
					}
					
			  		echo '<font style="font-weight: bolder;font-size: 9pt;line-height: 20px;color: #AA3333;">Please enter friendlist names</font>'
			  			  .'<font style="padding-left: 10px;font-size: 9pt;line-height: 20px;color: #555555;">'."(must be separated by ';')</font><br />".
			  			  '<input type="text" name="privacy_txt" id="privacy_txt" maxlength="5000" style="width: 300px;"
							 value="'.$privacy_txt.'"></input><br />
						   <input type="CHECKBOX" name="Family_input" id="Family_input" '.$family_checked.'>Family</input>
						   <input type="CHECKBOX" name="Work_input" id="Work_input" '.$work_checked.'>Work</input>
						   <input type="CHECKBOX" name="Schoolt_input" id="Schoolt_input" '.$school_checked.'>School</input>
						   <font style="padding-left: 10px;font-size: 9pt;line-height: 20px;color: #555555;">'."(or select from left)</font>";
			  	}
				?>
				<br /><br />
				<INPUT class="PrivacyShieldButtonStyle" type="submit" id="get_suggestions_submit_id" name = "get_suggestions_submit" value="Get Privacy Suggestions" />
				</td>
				<td width="40%" style="padding-top:10px;font-weight: bolder;font-size: 8pt;font-family: Verdana, Arial;line-height: 20px;text-align: left;color: #333333;">
				<input type="radio" name="app_select_input" id="app_select_input1" onclick="parent.location='<?php echo $facebook_iframe_canvas_page_url; ?>service_privacy_shield.php?setting=1';"
				<?php if($_GET['setting'] == '1') echo "CHECKED" ?>>Relationship Quality/Tie Strength</input><br />
				<input type="radio" name="app_select_input" id="app_select_input2" onclick="parent.location='<?php echo $facebook_iframe_canvas_page_url; ?>service_privacy_shield.php?setting=2';"
				<?php if($_GET['setting'] == '2') echo "CHECKED" ?>>Relationship Category/Friend List</input><br />
				<input type="radio" name="app_select_input" id="app_select_input3" onclick="parent.location='<?php echo $facebook_iframe_canvas_page_url; ?>service_privacy_shield.php?setting=3';"
				<?php if($_GET['setting'] == '3') echo "CHECKED" ?>>Keywords/Interests</input>
				</td>
			</tr>
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
		<td width="5%"></td>
	</tr>
	<tr>
		<td width="5%"></td>
		<td width="90%" style="padding-top: 10px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 35px;text-align: left;">
		<?php 
		if($_GET['setting'] == '3')
	  	{
	  		echo 'Privacy Setting for Keywords/Interests<br />';
	  	}
	  	else if($_GET['setting'] == '2')
	  	{
	  		echo 'Privacy Setting for Relationship Category/Friend List<br />';
	  	}
	  	else if($_GET['setting'] == '1')
	  	{
	  		echo 'Privacy Setting for Relationship Quality/Tie Strength<br />';
	  	}
	  	else
	  	{
	  		echo '<div class="fberrorbox">  
    				Error retrieving your data! 
					</div><br />';
	  	}
	  	?>
		<table width="100%">
		<tr>
			<td colspan="3" style="padding-bottom: 5px;padding-top: 5px;">
			<div>
			<fb:serverFbml style="width: 10px; height: 150px;">
			<script type="text/fbml">
			<fb:fbml>
			<form style="white-space:nowrap;" action="<?php echo $source_server_url ?>if/service_privacy_shield.php?setting=<?php echo $_GET['setting'] ?>&signed_request=<?php echo $_GET['signed_request'] ?>" method="post">
			<?php
				$friendlist_results = $facebook->api(array('method'=>'friends.getLists',));
				
				if(count($friendlist_results) > 0)
				{
					echo '<select name="friend_list_select" id="friend_list_select">
					  	  <option value="0" selected>Please select a list</option>';
			
					foreach($friendlist_results as $friendlist_index => $friendlist_id)
					{
						echo '<option value="'.$friendlist_id['flid'].'">'.$friendlist_id['name'].'</option>';
					}
					
					echo '</select>';
				}
			?>
			<fb:friend-selector <?php GLOBAL $user_id; echo 'uid="'.$user_id.'"';?> name="block_friend_selector" 
			idname="modify_friend_selector"></fb:friend-selector>
			<INPUT type="submit" id="add_friend" name = "add_friend_submit" value="Add" />
			<INPUT type="submit" id="remove_friend" name = "remove_friend_submit" value="Remove" />
			</form>
			</fb:fbml>
			</script>
			</fb:serverFbml>
			</div>
			</td>
		</tr>
		<?php 
		get_privacy_setting_list_contents();
		?>
		</table>
		</td>
		<td width="5%"></td>
	</tr>
<?php 
	function init_settings($uid_list, $attributes)
	{
		GLOBAL $user_id;
		GLOBAL $db;

		if($user_id == NULL)
		{
			return;
		}
		
		$privacy_setting_results = mysql_query("SELECT enable FROM privacy_settings 
    																 WHERE uid = $user_id;", $db);
		
		$list_initialized = false;
		while($privacy_setting_row = mysql_fetch_array($privacy_setting_results))
		{
			$list_initialized = true;
		}
		
		if(!$list_initialized)
		{
			$insert_query = sprintf("INSERT INTO privacy_settings (uid) 
													 		 	   VALUES('%s')",
																   $user_id);
								
			if(!mysql_query($insert_query))
		    {
			    echo '<div class="fberrorbox">  
	    				Failed to initialize privacy setting. Please try again later!  
						</div><br />';
			    
			    return false;
		    }
		}
		
		if(strlen($uid_list) > 0)
		{
			$field = 'quality_tie';
			$attri = 'category_friendlist_attri';
			if(isset($_GET['setting']) && $_GET['setting'] == '2')
			{
				$field = 'category_friendlist';
				$attri = 'category_friendlist_attri';
			}
			else if(isset($_GET['setting']) && $_GET['setting'] == '3')
			{
				$field = 'keyword_interests';
				$attri = 'keyword_interests_attri';
			}
				
			$quality_tie_arr = json_decode($uid_list, true);
			
			$quality_tie_list = '';
			foreach($quality_tie_arr as $privacy_uid)
			{
				$quality_tie_list .= $privacy_uid . ',';
			}
		
			$query = sprintf("UPDATE privacy_settings SET $field = '%s',
														  $attri = '%s'
												   		  WHERE uid = '%s'",
												   		  mysql_real_escape_string($quality_tie_list),
												   		  mysql_real_escape_string($attributes),
												   		  $user_id);
								
			if(!mysql_query($query))
		    {
			    echo '<div class="fberrorbox" style="width: 500px;">  
	    			  Failed to initialize privacy setting. Please try again later!  
					  </div><br />';
			    
				return false;
		    } 
		    else
		    {
		    	return true;
		    }
		}
		
		return true;
	}
	
	function get_privacy_setting_list_contents()
	{
		if(isset($_GET['setting']) && ($_GET['setting'] == '1' || $_GET['setting'] == '2' || $_GET['setting'] == '3'))
		{
			GLOBAL $user_id;
			GLOBAL $db;
			
			$target_field = 'quality_tie';
			if($_GET['setting'] == '2')
			{
				$target_field = 'category_friendlist';
			}
			else if($_GET['setting'] == '3')
			{
				$target_field = 'keyword_interests';
			}
			
			$privacy_setting_results = mysql_query("SELECT $target_field FROM privacy_settings 
	    																 WHERE uid = $user_id;", $db);
	    	
			while($privacy_setting_row = mysql_fetch_array($privacy_setting_results))
			{
				$list = $privacy_setting_row[$target_field];
			}
			
			if(strlen($list) <= 1)
			{
				echo '<tr><td><h5>Please click Get Privacy Suggestions to get privacy recommendations!</h5></td></tr>';
			}
			else
			{
				display_list($list);
			}
		}
	}

	function display_list($list)
	{
		GLOBAL $source_server_urlimage;
	
		$split_list_array = explode(",", $list);
		$content_html = '';
		
		$open_tag = false;
		$three_Counter == 0;
		
		foreach ($split_list_array as $uid_key => $uid_value)
		{
			if($uid_value != '')
			{
				if($three_Counter == 0)
		    	{
		    		$content_html .= '<tr>';
		    		$open_tag = true;
		    	}
		    	
				$content_html .= 
				'<td width="33%">
				<table cellspacing="5" cellpadding="0" width="100%" class="PrivacyShieldBackGround" style="border:1px solid #d4d4d4;">
				<tr>
					<td>
					<fb:profile-pic uid="'.$uid_value.'" linked="true" width="40px" height="40px" />
					</td>
					<td style="font-weight: bolder;font-size: 9pt;font-family: Verdana, Arial;color: #3b5998;">
					<fb:name uid="'.$uid_value.'" useyou="false" linked="false" />
					</td>
				</tr>
				</table>
				 </td>';
				$three_Counter++;
				
				if($three_Counter == 3)
		    	{
		    		$content_html .= '</tr>';
		    		$open_tag = false;
		    		$three_Counter = 0;
		    	}
			}
		}
		
		if($three_Counter != 0)
		{
			for($i = $three_Counter; $i < 3; $i++)
			{
				$content_html .= '<td width="33%"></td>';
			}
		}
		
		if($open_tag == true)
		{
			$content_html .= '</tr>';
		}
		
		$content_html .= '</table><br /><br />';
		echo $content_html;
	}
?>
	<tr>
		<td colspan="3" height="20px">
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

</center>
</body>
</html>

