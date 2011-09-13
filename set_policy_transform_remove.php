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
			Social Network Transformation - Hide Friendship Connections
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
			Hide existing friendship connections from your virtual FAITH social graph. FAITH transforms the real Facebook social graph to virtual FAITH social graph and outputs the transformed graph to the applications behide FAITH.
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 25px;padding-top: 25px;border-bottom: #AAAAAA 1px solid;">
			<table width="100%">
			<tr>
				<td>
				<form action="set_policy_transform_remove.php" method="post">
<?/*FER edit.				
				<fb:friend-selector
				
				<?php
				require_once 'vars.php';
				require_once 'facebook.php';
				
				$facebook = new Facebook($appapikey, $appsecret);
				$user_id = $facebook->require_login();
				echo 'uid="'.$user_id.'"';
				    ?>
				name="block_friend_selector" idname="block_friend_selector"></fb:friend-selector>
*/?>
							<font style="padding-left: 15px; padding-right: 15px;font-weight: bold;Color: #AA3333;">
							Add an uid from <fb:name uid="<?= $user_id ?>" useyou="false" linked="true" /> friends to create a Social Network Transformation.
							</font><br/>
							<input type="text" name="block_friend_selector" id="block_friend_selector"/>

				<INPUT type="submit" id="block_friend" name = "block_friend_submit" value="Add" />
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
			$uid_to_blocked = $_POST['block_friend_selector'];
			
			$block_results = mysql_query("SELECT Count(transform_remove_id) as count_num FROM transform_remove
																	        WHERE transform_remove.remove_uid_a = $user_id AND
	            									  						      transform_remove.remove_uid_b = $uid_to_blocked;", $db);
			
			$block_row = mysql_fetch_array($block_results);
			$block_num = $block_row['count_num'];
				
			if($block_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$removetime = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO transform_remove (remove_uid_a, 
													 			remove_uid_b,
													 			remove_time) 
													 			VALUES('%s', '%s', '%s')",
																mysql_real_escape_string($user_id), 
													 			mysql_real_escape_string($uid_to_blocked),
													 			mysql_real_escape_string($removetime));
								
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
		else if($_POST['status'] == 'remove_rule')
		{
			$remove_id = $_POST['remove_id'];
			
			$commit = "commit";
			mysql_query("begin", $db);
			
			$query = "DELETE FROM transform_remove_friend
						  	 WHERE transform_remove_friend.transform_remove_id = $remove_id;";
			if(!mysql_query($query, $db))
			{
				$commit = "rollback";
				$querylog .= "error in query: " . $query . " : " . mysql_error($db) . "<br /><br />";
			}
			
			$query = "DELETE FROM transform_remove_app
						  	 WHERE transform_remove_app.transform_remove_id = $remove_id;";
			if(!mysql_query($query, $db))
			{
				$commit = "rollback";
				$querylog .= "error in query: " . $query . " : " . mysql_error($db) . "<br /><br />";
			}
			
			$query = "DELETE FROM transform_remove
						  	 WHERE transform_remove.transform_remove_id = $remove_id;";
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
		
		$results = mysql_query("SELECT transform_remove.transform_remove_id,
									   transform_remove.remove_uid_b,
									   transform_remove.remove_time
									   from transform_remove
									   where transform_remove.remove_uid_a = $user_id
									   order by transform_remove.remove_time DESC", $db);
        
		$div_counter = 0;
		while($row = mysql_fetch_array($results))
		{
			$transform_remove_id = $row['transform_remove_id'];
			$remove_uid_b = $row['remove_uid_b'];
			$remove_time = $row['remove_time'];
			
			get_remove_friendship_contents($transform_remove_id,
										   $remove_uid_b,
										   $remove_time,
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

	function get_remove_friendship_contents($transform_remove_id,
										    $remove_uid_b,
										    $remove_time,
										    $div_counter)
	{
		GLOBAL $source_server_url;
		
		$friend_edit_html =
		'<a href="set_policy_transform_remove_not_fri.php?id='.$transform_remove_id.'">
		Edit
		</a>';
		
		$friend_detail_html =
		'<a href="#" onclick="do_ajax_show_details('."'ip_infor_Div".$div_counter."'".',1,'."'$transform_remove_id'".','."'loading_img".$div_counter."'".');">
		Details
		</a>';
		
		$application_edit_html =
		'<a href="set_policy_transform_remove_not_app.php?id='.$transform_remove_id.'">
		Edit
		</a>';
		
		$application_detail_html =
		'<a href="#" onclick="do_ajax_show_details('."'ip_infor_Div".$div_counter."'".',2,'."'$transform_remove_id'".','."'loading_img".$div_counter."'".');">
		Details
		</a>';
		
		echo 
		'<tr><td colspan="3">
		<table width="100%" style="padding-top: 5px;">
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
				<font style="padding-left: 20px;">
				Time Added: '.$remove_time.'
				</font>
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
				
				<form style="display: inline;" name="remove_rule_form'.$transform_remove_id.'" id="remove_rule_form'.$transform_remove_id.'"
					action="set_policy_transform_remove.php" method="post">
	    		<input type="hidden" name="remove_id" value="'.$transform_remove_id.'"/>
	    		<input type="hidden" name="status" VALUE="remove_rule">
	    		<font style="padding-left: 5px;padding-right: 5px;">
	    		<a style="font-weight: bold;text-decoration: underline;"
			 	href="#" onclick="document.getElementById('."'remove_rule_form".$transform_remove_id."'".').submit();">Remove this Rule</a>
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

	var params={"action":'select',"option":val,"searchwords":'none',"remove_id":remove_id};
	ajax.post('<?=$set_policy_transform_callbackurl?>?t='+val,params); 
	} 
//-->
</script>
	</table>
	</td>
</tr>
</table>
</html>

