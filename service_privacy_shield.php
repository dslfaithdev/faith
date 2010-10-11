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
	<td style="height: 650px;vertical-align:top;border-right: #3b5998 3px solid;border-top: #3b5998 3px solid;border-left: #3b5998 3px solid;border-bottom: #3b5998 3px solid;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="3" height="20px"></td>
	</tr>
	<tr>
		<td width="5%"></td>
		<td width="90%">
		<table width="100%" style="background-image:url('<?php echo $source_server_url; ?>image/faith_background.gif');background-repeat:repeat-x;border:1px solid #d4d4d4;">
		<tr>
			<td style="padding-left:20px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 35px;text-align: left;color: #3b5998;">
			Privacy Shield
			</td>
		</tr>
		<tr>
			<td style="padding-left:20px;font-size: 9pt;font-family: Verdana, Arial;line-height: 25px;text-align: left;color: #333333;">
			Recommend you the best privacy settings for your wall posts.
			</td>
		</tr>
		</table>
		</td>
		<td width="5%"></td>
	</tr>
	<tr>
		<td width="5%"></td>
		<td width="90%" style="padding-bottom: 25px;padding-top: 25px;border-bottom: #AAAAAA 1px solid;">
		<table width="100%">
		<tr>
			<td width="65%">
			<form action="set_policy_by_friend.php" method="post">
			<fb:friend-selector <?php GLOBAL $user_id; echo 'uid="'.$user_id.'"';?> name="block_friend_selector" 
			idname="block_friend_selector"></fb:friend-selector>
			<INPUT type="submit" id="add_friend" name = "add_friend_submit" value="Add" />
			<INPUT type="submit" id="remove_friend" name = "remove_friend_submit" value="Remove" />
			<INPUT type="submit" id="back_to_default" name = "back_to_default_submit" value="Back to Default" />
			</form>
			</td>
			<td width="35%" style="text-align:righ;">
			<?php 
			GLOBAL $facebook_canvas_page_url;
			if($_GET['setting'] == '2')
		  	{
		  		echo '<a class="AcceptButtonStyle" style="font-size: 8pt;"
			 	href="'.$facebook_canvas_page_url.'service_privacy_shield.php?setting=1">Switch to Moderate Privacy</a>';
		  	}
		  	else if($_GET['setting'] == '1')
		  	{
		  		echo '<a class="AcceptButtonStyle" style="font-size: 8pt;"
			 	href="'.$facebook_canvas_page_url.'service_privacy_shield.php?setting=2">Switch to high Privacy</a>';
		  	}
			?>
			</td>
		</tr>
		</table>
		</td>
		<td width="5%"></td>
	</tr>
	<tr>
		<td width="5%"></td>
		<td width="90%">
		<table width="100%">
		<tr>
			<td style="padding-top: 10px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 35px;text-align: left;">
		  	<?php 
		  	if($_GET['setting'] == '2')
		  	{
		  		echo 'Privacy Setting for Moderately Private Posts';
		  	}
		  	else if($_GET['setting'] == '1')
		  	{
		  		echo 'Privacy Setting for Very Private Posts';
		  	}
		  	else
		  	{
		  		echo '<div class="fberrorbox">  
	    				Error retrieving your data! 
						</div><br />';
		  	}
		  	?>
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
			$uid_to_blocked = $_POST['block_friend_selector'];
			
			$block_results = mysql_query("SELECT Count(uid) as count_num FROM user_blocked_friend
																	     WHERE user_blocked_friend.uid = $user_id AND
	            									  						   user_blocked_friend.blocked_uid = $uid_to_blocked;", $db);
			
			$block_row = mysql_fetch_array($block_results);
			$block_num = $block_row['count_num'];
				
			if($block_num == '0')
			{
 				$query = sprintf("INSERT INTO user_blocked_friend (uid, 
													 			   blocked_uid) 
													 			   VALUES('%s', '%s')",
																   mysql_real_escape_string($user_id), 
													 			   mysql_real_escape_string($uid_to_blocked));
								
				if(!mysql_query($query))
			    {
				    echo '<div class="fberrorbox">  
		    				Failed to block the friend!  
							</div><br />';
			    }
			    
			    echo '<div class="fbbluebox">  
    			You have successfully blocked the friend!  
				</div><br />';
			}
			else
			{
				echo '<div class="fbbluebox">  
    			You have already blocked the friend!  
				</div><br />';
			}
		}
		else if($_POST['remove_friend_submit'] == 'Remove')
		{
			$uid_to_unblocked = $_POST['block_friend_selector'];
			
			$unblock_results = mysql_query("DELETE FROM user_blocked_friend
						  						   WHERE user_blocked_friend.uid = $user_id AND
            			  								 user_blocked_friend.blocked_uid = $uid_to_unblocked;", $db);
 			if(!$unblock_results)
		    {
			    echo '<div class="fberrorbox">  
    			Failed to unblock the friend!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox">  
	    			You have successfully unblocked the friend!  
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
<?php 
	
	function get_privacy_setting_list_contents()
	{
		GLOBAL $user_id;
		GLOBAL $db;
		
		$block_friend_html = '';
    	$block_friend_results = mysql_query("SELECT uid, blocked_uid FROM user_blocked_friend 
    																 WHERE user_blocked_friend.uid = $user_id;", $db);

    	$blocked_any = false;
    	$five_Counter = 0;
    	$open_tag = true;
    	
		while($block_friend_row = mysql_fetch_array($block_friend_results))
		{
			$blocked_any = true;
			
			if($five_Counter == 0)
	    	{
	    		$block_friend_html .= '<tr>';
	    		$open_tag = true;
	    	}
	    	
			$block_friend_uid = $block_friend_row['blocked_uid'];
			
			$block_friend_html .= 
			'<td width="20%">
			<fb:profile-pic uid="'.$block_friend_uid.'" linked="false" width="50" height="50" />
			<fb:name uid="'.$block_friend_uid.'" useyou="false" linked="true" />
			 </td>';
			$five_Counter++;
			
			if($five_Counter == 5)
	    	{
	    		$block_friend_html .= '</tr>';
	    		$open_tag = false;
	    		$five_Counter = 0;
	    	}
		}
		
		if($five_Counter != 0)
		{
			for($i = $five_Counter; $i < 5; $i++)
			{
				$block_friend_html .= '<td width="20%"></td>';
			}
		}
		
		if($open_tag == true)
		{
			$block_friend_html .= '</tr>';
		}
		
		if(!$blocked_any)
		{
			$block_friend_html = '<tr><td><h5>You have not blocked any friends</h5></td></tr>';
		}
		
		$block_friend_html .= '</table>';
		return $block_friend_html;
	}

?>
	<tr>
		<td colspan="3" height="20px"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</html>

