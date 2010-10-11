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
		 <a href="<?php echo $facebook_canvas_page_url; ?>set_policy_transform_remove.php">Remove</a></font>)
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
			at RESTful API Level - Remove Friends from This Setting
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
<?php

$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();

try
{
	if(isset($_GET['api_id']) && strlen($_GET['api_id']) > 0)
	{
		$api_id = $_GET['api_id'];
		mysqlSetup($db);
		
		$api_results = mysql_query("SELECT restapi.name,
										   restapi.facebook_description
									       from restapi
										   where restapi.id = $api_id", $db);
	        
		while($api_row = mysql_fetch_array($api_results))
		{
			$api_name = $api_row['name'];
			$api_facebook_description = $api_row['facebook_description'];
			
			echo 
			'<table width="100%" style="padding-top: 5px;">
			<tr>
				<td>
				Hi <fb:name uid="'.$user_id.'" useyou="false" linked="true" />, please select the friends
				whom you do NOT want the following rule apply to
				</td>
			</tr>
			<tr>
				<td>
				<font style="font-weight: bolder;font-size: 8pt;font-family: Verdana, Arial;line-height: 20px;">
				'.$api_name.'
				</font>
				</td>
			</tr>
			<tr>
				<td>
				'.$api_facebook_description.'
				</td>
			</tr>
			</table>';
		}
	}
}
catch (Exception $e)
{
	echo 'Caught database exception: ',  $e->getMessage(), "\n";
}

?>
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 25px;padding-top: 25px;border-bottom: #AAAAAA 1px solid;">
			<table width="100%">
			<tr>
				<td>
				<form action="set_policy_not_fri.php?api_id=<?php echo $_GET['api_id'] ?>" method="post">
				<fb:friend-selector
				
				<?php
				require_once 'vars.php';
				require_once 'facebook.php';
				
				$facebook = new Facebook($appapikey, $appsecret);
				$user_id = $facebook->require_login();
				echo 'uid="'.$user_id.'"';
				    ?>
				name="no_friend_selector" idname="no_friend_selector"></fb:friend-selector>
				<INPUT type="submit" id="add_friend" name = "add_friend_submit" value="Add" />
				<INPUT type="submit" id="add_friend_exclude_me" name = "add_friend_submit" value="Add Myself" />
				</form>
				</td>
			</tr>
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
		
		if($_POST['add_friend_submit'] == 'Add')
		{
			$api_id = $_GET['api_id'];
			$uid_NOT_apply = $_POST['no_friend_selector'];
			
			$api_results = mysql_query("SELECT Count(not_apply_fri_uid) as count_num FROM user_disable_api_friend
																	        		 WHERE user_disable_api_friend.uid = $user_id AND
																	        	     	   user_disable_api_friend.restapi_id = $api_id AND
																	        	     	   user_disable_api_friend.not_apply_fri_uid = $uid_NOT_apply", $db);
			
			$api_row = mysql_fetch_array($api_results);
			$api_num = $api_row['count_num'];
				
			if($api_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$time_added = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO user_disable_api_friend (uid, 
													 				   restapi_id,
													 				   not_apply_fri_uid,
													 				   time_added) 
															 		   VALUES('%s', '%s', '%s', '%s')",
																	   mysql_real_escape_string($user_id), 
															 		   mysql_real_escape_string($api_id),
															 		   mysql_real_escape_string($uid_NOT_apply),
															 		   mysql_real_escape_string($time_added));
								
				if(!mysql_query($query))
			    {
				    echo '<div class="fberrorbox">  
		    				Failed to add the friend to the list!  
							</div><br />';
			    }
			    else
			    {
				    echo '<div class="fbbluebox">  
	    			You have successfully added the friend to the list!  
					</div><br />';
			    }
			}
			else
			{
				echo '<div class="fbbluebox">  
    			You have already added the friend to the list!  
				</div><br />';
			}
		}
		else if($_POST['remove_from_list_submit'] == 'remove')
		{
			$api_id = $_GET['api_id'];
			$uid_to_remove = $_POST['not_apply_fri_uid'];
			
			$unblock_results = mysql_query("DELETE FROM user_disable_api_friend
						  						   WHERE user_disable_api_friend.uid = $user_id AND
						  						   		 user_disable_api_friend.restapi_id = $api_id AND
            			  								 user_disable_api_friend.not_apply_fri_uid = $uid_to_remove;", $db);
 			if(!$unblock_results)
		    {
			    echo '<div class="fberrorbox">  
    			Failed to remove the friend from list!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox">  
	    			You have successfully removed the friend from list!  
					</div><br />';
		    }
		}
		if($_POST['add_friend_submit'] == 'Add Myself')
		{
			$api_id = $_GET['api_id'];
			$uid_NOT_apply = $user_id;
			
			$api_results = mysql_query("SELECT Count(not_apply_fri_uid) as count_num FROM user_disable_api_friend
																	        		 WHERE user_disable_api_friend.uid = $user_id AND
																	        	     	   user_disable_api_friend.restapi_id = $api_id AND
																	        	     	   user_disable_api_friend.not_apply_fri_uid = $uid_NOT_apply", $db);
			
			$api_row = mysql_fetch_array($api_results);
			$api_num = $api_row['count_num'];
				
			if($api_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$time_added = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO user_disable_api_friend (uid, 
													 				   restapi_id,
													 				   not_apply_fri_uid,
													 				   time_added) 
															 		   VALUES('%s', '%s', '%s', '%s')",
																	   mysql_real_escape_string($user_id), 
															 		   mysql_real_escape_string($api_id),
															 		   mysql_real_escape_string($uid_NOT_apply),
															 		   mysql_real_escape_string($time_added));
								
				if(!mysql_query($query))
			    {
				    echo '<div class="fberrorbox">  
		    				Failed to add yourself to the list!  
							</div><br />';
			    }
			    else
			    {
				    echo '<div class="fbbluebox">  
	    			You have successfully added yourself to the list!  
					</div><br />';
			    }
			}
			else
			{
				echo '<div class="fbbluebox">  
    			You have already added yourself to the list!  
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

<?php

require_once 'vars.php';
require_once 'facebook.php';

try
{
	if(isset($_GET['api_id']) && strlen($_GET['api_id']) > 0)
	{
		$facebook = new Facebook($appapikey, $appsecret);
		$user_id = $facebook->require_login();
		
		try
		{
			mysqlSetup($db);
			$api_id = $_GET['api_id'];
			
			$results = mysql_query("SELECT user_disable_api_friend.not_apply_fri_uid,
										   user_disable_api_friend.time_added
										   from user_disable_api_friend
										   where user_disable_api_friend.uid = $user_id AND
										   		 user_disable_api_friend.restapi_id = $api_id
										   order by user_disable_api_friend.time_added DESC", $db);
	        
			$friend_displayed = 0;
			$counter = 0; 
			
			while($row = mysql_fetch_array($results))
			{
				if($counter == 0)
				{
					echo '<tr>';
				}
				
				$not_apply_fri_uid = $row['not_apply_fri_uid'];
				$time_added = $row['time_added'];
				
				get_NOT_apply_friend_contents($api_id,
											  $not_apply_fri_uid,
											  $time_added);
				
				$friend_displayed++;
				$counter++;
				if($counter == 3)
				{
					echo '</tr>';
					$counter = 0;
				}
			}
			
			if($counter != 0)
			{
				for (;$counter < 3;$counter++)
				{
					echo '<td width="33%"></td>';
				}
				
				echo '</tr>';
			}
			
			if($friend_displayed == 0)
			{
				echo '<tr><td><br /><br /><h5>You have not added any friends.</h5></td></tr>';
			}
		}
		catch (Exception $e)
		{
			echo 'Caught database exception: ',  $e->getMessage(), "\n";
		}
	}
} 
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>

<?php 

	function get_NOT_apply_friend_contents($api_id,
										   $not_apply_fri_uid,
										   $time_added)
	{
		echo 
		'<td width="33%"><table width="100%">
		<tr>
			<td width="25%">
			<fb:profile-pic uid="'.$not_apply_fri_uid.'" linked="false" width="50" height="50" /> 
			</td>
			<td width="3%"></td>
			<td width="72%">
			<fb:name uid="'.$not_apply_fri_uid.'" useyou="false" linked="true" />
			<br />
			Time: '.$time_added.'
			<br />
			<form style="display: inline;" action="set_policy_not_fri.php?api_id='.$api_id.'" method="post">
			<INPUT type="hidden" name="api_id" VALUE="'.$api_id.'">
			<INPUT type="hidden" name="not_apply_fri_uid" VALUE="'.$not_apply_fri_uid.'">
			<INPUT type="submit" name="remove_from_list_submit" value="remove" />
			</form>
			</td>
		</tr></table></td>';
	}

?>

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
	</table>
	</td>
</tr>
</table>
</html>

