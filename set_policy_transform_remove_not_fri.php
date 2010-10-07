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
		<a href="http://apps.facebook.com/dsl_faith/set_policy.php">at RESTful API Level</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/set_policy_transform.php">Network Transformation</a>
		(<font style="padding-left: 5px; padding-right: 5px;">
		 <a href="http://apps.facebook.com/dsl_faith/set_policy_transform_add.php">Add</a></font>
		 <font style="padding-left: 5px; padding-right: 5px;">
		 <a href="http://apps.facebook.com/dsl_faith/set_policy_transform_remove.php">Hide</a></font>)
		 (<font style="padding-left: 5px; padding-right: 5px;">
		 <a href="http://apps.facebook.com/dsl_faith/set_policy_transform_accepted_request.php">Confirmed Requests</a></font>)
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
			Social Network Transformation - Hide Friendship Connections</td><td colspan="2">
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
<?php

$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();
$target_friend_uid;

try
{
	if(isset($_GET['id']) && strlen($_GET['id']) > 0)
	{
		$remove_id = $_GET['id'];
		mysqlSetup($db);
		
		$remove_results = mysql_query("SELECT transform_remove.remove_uid_a,
											  transform_remove.remove_uid_b,
											  transform_remove.remove_time
											  from transform_remove
											  where transform_remove.transform_remove_id = $remove_id", $db);
	        
		while($remove_row = mysql_fetch_array($remove_results))
		{
			$remove_uid_a = $remove_row['remove_uid_a'];
			$remove_uid_b = $remove_row['remove_uid_b'];
			$remove_time = $remove_row['remove_time'];
			
			global $target_friend_uid;
			$target_friend_uid = $remove_uid_b;
			
			echo 
			'<table width="100%" style="padding-top: 5px;">
			<tr>
				<td colspan="3">
				Hi <fb:name uid="'.$remove_uid_a.'" useyou="false" linked="true" />, please select the friends
				whom you do NOT want the following removal rule apply to
				</td>
			</tr>
			<tr>
				<td width="10%">
				<fb:profile-pic uid="'.$remove_uid_b.'" linked="false" width="50" height="50" /> 
				</td>
				<td width="2%"></td>
				<td width="88%">
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>
					<fb:name uid="'.$remove_uid_b.'" useyou="false" linked="true" />
					<font style="padding-left: 20px;font-weight:bold;">
					Time Added: '.$remove_time.'
					</font>
					</td>
				</tr>
				</table>
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
				<form action="set_policy_transform_remove_not_fri.php?id=<?php echo $_GET['id'] ?>" method="post">
				<fb:friend-selector
				
				<?php
				require_once 'vars.php';
				require_once 'facebook.php';
				
				$facebook = new Facebook($appapikey, $appsecret);
				$user_id = $facebook->require_login();
				echo 'uid="'.$user_id.'"';
				    ?>
				name="no_friend_selector" idname="no_friend_selector"></fb:friend-selector>
				<INPUT type="submit" id="block_friend" name = "block_friend_submit" value="Add" />
				<INPUT type="submit" id="block_friend_exclude_me" name = "block_friend_submit" value="Add Myself" />
				<INPUT type="submit" id="block_friend_exclude_him" name = "block_friend_submit" value="Add Friend in Rule" />
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
		
		if($_POST['block_friend_submit'] == 'Add')
		{
			$remove_id = $_GET['id'];
			$uid_NOT_apply = $_POST['no_friend_selector'];
			
			$block_results = mysql_query("SELECT Count(not_apply_fri_uid) as count_num FROM transform_remove_friend
																	        		   WHERE transform_remove_friend.transform_remove_id = $remove_id AND
																	        		   		 transform_remove_friend.not_apply_fri_uid = $uid_NOT_apply;", $db);
			
			$block_row = mysql_fetch_array($block_results);
			$block_num = $block_row['count_num'];
				
			if($block_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$time_added = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO transform_remove_friend (transform_remove_id, 
													 				   not_apply_fri_uid,
													 				   time_added) 
															 		   VALUES('%s', '%s', '%s')",
																	   mysql_real_escape_string($remove_id), 
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
			$remove_id = $_POST['remove_id'];
			$uid_to_remove = $_POST['not_apply_fri_uid'];
			
			$unblock_results = mysql_query("DELETE FROM transform_remove_friend
						  						   WHERE transform_remove_friend.transform_remove_id = $remove_id AND
            			  								 transform_remove_friend.not_apply_fri_uid = $uid_to_remove;", $db);
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
		else if($_POST['block_friend_submit'] == 'Add Myself')
		{
			$remove_id = $_GET['id'];
			$uid_NOT_apply = $user_id;
			
			$block_results = mysql_query("SELECT Count(not_apply_fri_uid) as count_num FROM transform_remove_friend
																	        		   WHERE transform_remove_friend.transform_remove_id = $remove_id AND
																	        		   		 transform_remove_friend.not_apply_fri_uid = $uid_NOT_apply;", $db);
			
			$block_row = mysql_fetch_array($block_results);
			$block_num = $block_row['count_num'];
				
			if($block_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$time_added = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO transform_remove_friend (transform_remove_id, 
													 				   not_apply_fri_uid,
													 				   time_added) 
															 		   VALUES('%s', '%s', '%s')",
																	   mysql_real_escape_string($remove_id), 
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
		else if($_POST['block_friend_submit'] == 'Add Friend in Rule')
		{
			$remove_id = $_GET['id'];
			
			global $target_friend_uid;
			$uid_NOT_apply = $target_friend_uid;
			
			$block_results = mysql_query("SELECT Count(not_apply_fri_uid) as count_num FROM transform_remove_friend
																	        		   WHERE transform_remove_friend.transform_remove_id = $remove_id AND
																	        		   		 transform_remove_friend.not_apply_fri_uid = $uid_NOT_apply;", $db);
			
			$block_row = mysql_fetch_array($block_results);
			$block_num = $block_row['count_num'];
				
			if($block_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$time_added = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO transform_remove_friend (transform_remove_id, 
													 				   not_apply_fri_uid,
													 				   time_added) 
															 		   VALUES('%s', '%s', '%s')",
																	   mysql_real_escape_string($remove_id), 
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
	if(isset($_GET['id']) && strlen($_GET['id']) > 0)
	{
		$facebook = new Facebook($appapikey, $appsecret);
		$user_id = $facebook->require_login();
		
		try
		{
			mysqlSetup($db);
			$remove_id = $_GET['id'];
			
			$results = mysql_query("SELECT transform_remove_friend.not_apply_fri_uid,
										   transform_remove_friend.time_added
										   from transform_remove_friend
										   where transform_remove_friend.transform_remove_id = $remove_id
										   order by transform_remove_friend.time_added DESC", $db);
	        
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
				
				get_NOT_apply_friend_contents($remove_id,
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

	function get_NOT_apply_friend_contents($remove_id,
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
			<form style="display: inline;" action="set_policy_transform_remove_not_fri.php?id='.$remove_id.'" method="post">
			<INPUT type="hidden" name="remove_id" VALUE="'.$remove_id.'">
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

