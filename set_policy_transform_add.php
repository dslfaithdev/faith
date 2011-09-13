<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Welcome to DSL FAITH </title>
<style type="text/css">
<?php echo htmlentities(file_get_contents('faith_style.css', true)); ?>
</style>
</head>
<table cellspacing="0" cellpadding="0" width="750px">
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
		<a href="<?php echo $facebook_canvas_page_url; ?>set_policy.php">at RESTful API Level</a>
		</td>
		<td class="PageTitleLink">
		<a href="<?php echo $facebook_canvas_page_url; ?>set_policy_transform.php">Network Transformation</a>
		(<font style="padding-left: 5px; padding-right: 5px;">
		 <a href="<?php echo $facebook_canvas_page_url; ?>set_policy_transform_add.php">Add</a></font>
		 <font style="padding-left: 5px; padding-right: 5px;">
		 <a href="<?php echo $facebook_canvas_page_url; ?>set_policy_transform_remove.php">Hide</a></font>)
		 (<font style="padding-left: 5px; padding-right: 5px;">
		 <a href="<?php echo $facebook_canvas_page_url; ?>set_policy_transform_accepted_request.php">Confirmed Requests</a></font>)
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
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-top: 20px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 45px;text-align: left;">
			Social Network Transformation - Add Virtual Friendship Connections
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
			Add virtual friendship connections to your facebook social graph. FAITH transforms the real Facebook social graph to virtual FAITH social graph and outputs the transformed graph to the applications behide FAITH.
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 25px;padding-top: 25px;border-bottom: #AAAAAA 1px solid;">
			<table width="100%">
			
				<?php
				
				require_once 'vars.php';
				require_once 'facebook.php';
				
				try
				{
					$facebook = new Facebook($appapikey, $appsecret);
					$user_id = $facebook->require_login();
					
					if($_POST['find_friend_submit'] == 'Search')
					{
						$uid_list = $_POST['uid_list'] . ',' . $_POST['find_friend_selector'];
						$current_uid = $_POST['find_friend_selector'];
						
						$split_uid_list  = $uid_list;
						$split_uid_array = explode(",", $split_uid_list);
						
						echo '<tr><td>';
						foreach ($split_uid_array as $uid_key => $uid_value) 
						{
    						echo '<font style="padding-left: 5px; padding-right: 5px;">
    							  <fb:profile-pic uid="'.$uid_value.'" linked="false" width="50" height="50" />
    							  </font>';
						}
						echo '</td></tr>';
						
						print_search_bar($current_uid, $uid_list);
					}
					else if($_POST['find_friend_submit'] == 'Back')
					{
						$uid_list = '';
						$current_uid;
						
						$split_uid_list  = $_POST['uid_list'];
						$split_uid_array = explode(",", $split_uid_list);
						
						$uid_count = count($split_uid_array);
						
						if($uid_count > 1)
						{
							$i = 1;
							echo '<tr><td>';
							foreach ($split_uid_array as $uid_key => $uid_value) 
							{
								if($i < $uid_count)
								{
		    						echo '<font style="padding-left: 5px; padding-right: 5px;">
		    							  <fb:profile-pic uid="'.$uid_value.'" linked="false" width="50" height="50" />
		    							  </font>';
	
		    						$uid_list .= $uid_value;
		    						
		    						if($i == $uid_count - 1)
		    						{
		    							$current_uid = $uid_value;
		    						}
		    						else
		    						{
		    							$uid_list .= ',';
		    						}
								}
								
	    						$i++;
							}
							echo '</td></tr>';
							
							print_search_bar($current_uid, $uid_list);
						}
						else
						{
							echo '<tr><td>
							<font style="padding-left: 5px; padding-right: 5px;">
							<fb:profile-pic uid="'.$user_id.'" linked="false" width="50" height="50" />
							</font></td></tr>';
							
							print_search_bar($user_id, $user_id);
						}
					}
					else
					{
						echo '<tr><td>
						<font style="padding-left: 5px; padding-right: 5px;">
						<fb:profile-pic uid="'.$user_id.'" linked="false" width="50" height="50" />
						</font></td></tr>';
						
						print_search_bar($user_id, $user_id);
					}
				}
				catch (Exception $e)
				{
					echo 'Caught database exception: ',  $e->getMessage(), "\n";
				}
				
				function print_search_bar($current_uid, $uid_list)
				{
/* FER edit.
					echo '<tr><td>
							<form action="set_policy_transform_add.php" method="post">
							<fb:friend-selector uid="'.$current_uid.'" name="find_friend_selector" idname="find_friend_selector"></fb:friend-selector>
							<input type="hidden" name="uid_list" value="'.$uid_list.'"/>
							<INPUT type="submit" id="find_friend" name = "find_friend_submit" value="Search" />
							<INPUT type="submit" id="back_friend" name = "find_friend_submit" value="Back" />
							<INPUT type="submit" id="add_friend" name = "find_friend_submit" value="Add" />
							<font style="padding-left: 15px; padding-right: 15px;font-weight: bold;Color: #AA3333;">
							Search from <fb:name uid="'.$current_uid.'" useyou="false" linked="true" />'."'".' friends
							</font>
							</form></td></tr>';
							*/
					echo '<tr><td>
							<form action="set_policy_transform_add.php" method="post">
							<font style="padding-left: 15px; padding-right: 15px;font-weight: bold;Color: #AA3333;">
							Add an uid from <fb:name uid="'.$current_uid.'" useyou="false" linked="true" />'."'".' friends to create a Social Network Transformation.
							</font><br/>
							<input type="text" name="find_friend_selector" id="find_friend_selector"/>
							<input type="hidden" name="uid_list" value="'.$uid_list.'"/>
							<INPUT type="submit" id="add_friend" name = "find_friend_submit" value="Add" />
							</form></td></tr>';
/*end FER edit.*/
				}
				
				?>
				
			</table>
			</td>
			<td width="5%"></td>
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
		
		if($_POST['find_friend_submit'] == 'Add')
		{
			if(is_numeric($_POST['find_friend_selector']))
			{
				$uid_to_add = $_POST['find_friend_selector'];
				$uid_list = $_POST['uid_list'] . ',' . $_POST['find_friend_selector'];
				
				$add_results = mysql_query("SELECT Count(transform_add_id) as count_num FROM transform_add
																		   				WHERE transform_add.add_uid_a = $user_id AND
		            									  						      		  transform_add.add_uid_b = $uid_to_add;", $db);
				
				$add_row = mysql_fetch_array($add_results);
				$add_num = $add_row['count_num'];
					
				if($add_num == '0')
				{
					$name_array = $facebook->api_client->users_getInfo($uid_to_add, 'name');
					$name = $name_array[0]['name'];
					
					date_default_timezone_set('America/Los_Angeles');
					$addtime = date("Y-m-d H:i:s");
					
	 				$query = sprintf("INSERT INTO transform_add (add_uid_a, 
														 		 add_uid_b,
														 		 uid_b_name,
														 	  	 add_time,
														 	  	 social_path) 
														 		 VALUES('%s', '%s', '%s', '%s', '%s')",
																 mysql_real_escape_string($user_id), 
														 		 mysql_real_escape_string($uid_to_add),
														 		 mysql_real_escape_string($name),
														 		 mysql_real_escape_string($addtime),
														 		 mysql_real_escape_string($uid_list));
									
					if(!mysql_query($query))
				    {
					    echo '<div class="fberrorbox">  
			    				Failed to add the rule!  
								</div><br />';
				    }
				    
				    echo '<div class="fbbluebox">  
	    			You have successfully added the rule!  
					</div><br />';
				}
				else
				{
					echo '<div class="fbbluebox">  
	    			You have already added the rule!  
					</div><br />';
				}
			}
			else
			{
				echo '<div class="fberrorbox">  
	    			Please enter the friend you like to add!  
					</div><br />';
			}
		}
		else if($_POST['status'] == 'remove_rule')
		{
			$add_id = $_POST['add_id'];
			
			$commit = "commit";
			mysql_query("begin", $db);
			
			$query = "DELETE FROM transform_add_friend
						  	 WHERE transform_add_friend.transform_add_id = $add_id;";
			if(!mysql_query($query, $db))
			{
				$commit = "rollback";
				$querylog .= "error in query: " . $query . " : " . mysql_error($db) . "<br /><br />";
			}
			
			$query = "DELETE FROM transform_add_app
						  	 WHERE transform_add_app.transform_add_id = $add_id;";
			if(!mysql_query($query, $db))
			{
				$commit = "rollback";
				$querylog .= "error in query: " . $query . " : " . mysql_error($db) . "<br /><br />";
			}
			
			$query = "DELETE FROM transform_add
						  	 WHERE transform_add.transform_add_id = $add_id;";
			if(!mysql_query($query, $db))
			{
				$commit = "rollback";
				$querylog .= "error in query: " . $query . " : " . mysql_error($db) . "<br /><br />";
			}
			
			if($commit == "rollback")
			{
				$querylog .= "ERROR IN TRANSACTION<br /><br />transaction rolled back<br /><br />";
			}
			 
			$remove_rule_results = mysql_query($commit);
			
			if(!$remove_rule_results)
		    {
			    echo '<div class="fberrorbox">  
    			Failed to remove the rule!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox">  
	    			You have successfully removed the rule!  
					</div><br />';
		    }
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
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<table width="100%">
			<tr>
				<td>

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
		
		$results = mysql_query("SELECT transform_add.transform_add_id,
									   transform_add.add_uid_b,
									   transform_add.add_time,
									   transform_add.social_path,
									   transform_add.status
									   from transform_add
									   where transform_add.add_uid_a = $user_id
									   order by transform_add.add_time DESC", $db);
        
		$div_counter = 0;
		while($row = mysql_fetch_array($results))
		{
			$transform_add_id = $row['transform_add_id'];
			$add_uid_b = $row['add_uid_b'];
			$add_time = $row['add_time'];
			$social_path = $row['social_path'];
			$status = $row['status'];
			
			get_add_friendship_contents($transform_add_id,
									    $add_uid_b,
									    $add_time,
									    $social_path,
									    $status,
									    $div_counter);
			$div_counter++;
		}
		
		if($div_counter == 0)
		{
			echo '<tr><td><br /><br /><h5>You have not added any rules.</h5></td></tr>';
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

	function get_add_friendship_contents($transform_add_id,
									     $add_uid_b,
									     $add_time,
									     $social_path,
									     $status,
									     $div_counter)
	{
		GLOBAL $source_server_url;
		
		$status_string = 'Friend Request Pending';
		$status_css_string = 'padding-left: 20px;font-weight:bold;';
		
		if($status == '1')
		{
			$status_css_string = 'padding-left: 20px;font-weight:bold;color: #3b5998;';
			$status_string = 'Friend Request Accepted';
		}
		else if($status == '2')
		{
			$status_css_string = 'padding-left: 20px;font-weight:bold;color: #AA3333;';
			$status_string = 'Friend Request Declined';
		}
		
		$friend_edit_html =
		'<a href="set_policy_transform_add_not_fri.php?id='.$transform_add_id.'">
		Edit
		</a>';
		
		$friend_detail_html =
		'<a href="#" onclick="do_ajax_show_details('."'ip_infor_Div".$div_counter."'".',3,'."'$transform_add_id'".','."'loading_img".$div_counter."'".');">
		Details
		</a>';
		
		$application_edit_html =
		'<a href="set_policy_transform_add_not_app.php?id='.$transform_add_id.'">
		Edit
		</a>';
		
		$application_detail_html =
		'<a href="#" onclick="do_ajax_show_details('."'ip_infor_Div".$div_counter."'".',4,'."'$transform_add_id'".','."'loading_img".$div_counter."'".');">
		Details
		</a>';
		
		$split_uid_array = explode(",", $social_path);
		$social_path = '';
		
		$path_counter = 1;
		foreach ($split_uid_array as $uid_key => $uid_value) 
		{
    		$social_path .= '<font style="padding-left: 5px; padding-right: 5px;line-height: 16px;">
    			  			 <fb:name uid="'.$uid_value.'" useyou="false" linked="true" />
    			  			 </font>';
    		
    		if($path_counter < count($split_uid_array))
    		{
    			$social_path .= '->';
    		}
    		
    		$path_counter++;
		}
		
		
		echo 
		'<tr><td colspan="3">
		<table width="100%" style="padding-top: 5px;">
		<tr>
			<td width="10%">
			<fb:profile-pic uid="'.$add_uid_b.'" linked="false" width="50" height="50" /> 
			</td>
			<td width="2%"></td>
			<td width="88%">
			<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<fb:name uid="'.$add_uid_b.'" useyou="false" linked="true" />
				<font style="padding-left: 20px;">
				Time Added: '.$add_time.'
				</font>
				<font style="'.$status_css_string.'">
				'.$status_string.'
				</font>
				</td>
			</tr>
			<tr>
				<td style="padding-top: 10px;">
				SOCIAL PATH:
				'.$social_path.'
				</td>
			</tr>
			<tr>
				<td width="95%" style="padding-top: 10px;">
				NOT apply to
				<font style="padding-left: 10px; padding-right: 10px;"> Friends ('.$friend_edit_html.' | '.$friend_detail_html.')
				</font> 
				<font style="padding-right: 10px; border-right: #AAAAAA 1px solid;">Applications ('.$application_edit_html.' | '.$application_detail_html.')
				</font>
				<font style="padding-left: 10px; padding-right: 10px;">
				
				<form style="display: inline;" name="add_rule_form'.$transform_add_id.'" id="add_rule_form'.$transform_add_id.'"
					action="set_policy_transform_add.php" method="post">
	    		<input type="hidden" name="add_id" value="'.$transform_add_id.'"/>
	    		<input type="hidden" name="status" VALUE="remove_rule">
	    		<font style="padding-left: 5px;padding-right: 5px;">
	    		<a style="font-weight: bold;text-decoration: underline;"
			 	href="#" onclick="document.getElementById('."'add_rule_form".$transform_add_id."'".').submit();">Remove this Rule</a>
	    		</font>
	       		</form>
	       		
				</font>
				</td>
				<td width="5%" style="text-align: center;vertical-align:top;">
				<img style="display:none;" id="loading_img'.$div_counter.'" src="'.$source_server_url.'image/ajax-loader.gif" />
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td width="10%"></td>
			<td colspan="2">
			<table style="border-bottom: #CCCCCC 1px solid;" width="100%">
			<tr>
				<td width="100%">
			    <div style="background-color: #eceff6;" id="ip_infor_Div'.$div_counter.'"></div>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td></tr>';
	}

?>

				</td>
			</tr>
			</table>
			</td>
			<td width="5%"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20px"></td>
	</tr>
<script type="text/javascript">
<!--
function do_ajax_show_details(div,val,remove_id,img) {

	document.getElementById(img).setStyle('display', 'inline');
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.ondone = function(data) 
				  {
					document.getElementById(img).setStyle('display', 'none');
				  	document.getElementById(div).setInnerFBML(data);
				  }

	var params={"action":'select',"option":val,"searchwords":'none',"add_id":remove_id};
	ajax.post('<?=$set_policy_transform_callbackurl?>?t='+val,params); 
	} 
//-->
</script>
	</table>
	</td>
</tr>
</table>
</html>
<?php

