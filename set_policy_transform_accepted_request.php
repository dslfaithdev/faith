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
			Social Network Transformation - Accepted Fried Requested
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
			The friend requests that I have accepted. FAITH transforms the real Facebook social graph to virtual FAITH social graph and outputs the transformed graph to the applications behide FAITH.
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
		
		if($_POST['status'] == 'Ignore')
		{
			$add_id = $_POST['add_id'];
			
			$query = sprintf("UPDATE transform_add SET transform_add.status = '%s'
												   WHERE transform_add.transform_add_id = '%s'",
												   mysql_real_escape_string('2'),
												   $add_id);
								
			if(!mysql_query($query))
		    {
			    echo '<div class="fberrorbox" style="width: 500px;">  
	    			  Failed to ignore the request!  
					  </div><br />';
		    }
		    else
		    {
		    	echo '<div class="fbbluebox">  
	    				Request ignored!  
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
									   transform_add.add_uid_a,
									   transform_add.add_time,
									   transform_add.social_path,
									   transform_add.status
									   from transform_add
									   where transform_add.add_uid_b = $user_id AND
									   	     transform_add.status = 1
									   order by transform_add.add_time DESC", $db);
        
		$div_counter = 0;
		while($row = mysql_fetch_array($results))
		{
			$transform_add_id = $row['transform_add_id'];
			$add_uid_a = $row['add_uid_a'];
			$add_time = $row['add_time'];
			$social_path = $row['social_path'];
			
			get_accepted_friendship_contents($transform_add_id,
									    	 $add_uid_a,
									    	 $add_time,
									    	 $social_path);
			$div_counter++;
		}
		
		if($div_counter == 0)
		{
			echo '<tr><td><br /><br /><h5>You have not confirmed any requests.</h5></td></tr>';
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

	function get_accepted_friendship_contents($transform_add_id,
									     	  $add_uid_b,
									     	  $add_time,
									     	  $social_path)
	{
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
				<font style="padding-left: 20px;font-weight:bold;">
				Confirmed Request
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
				<td width="95%" style="padding-top: 15px;padding-bottom: 15px;border-bottom: #CCCCCC 1px solid;">
				<form style="padding-right: 10px;display: inline;" name="ignore_request_form'.$transform_add_id.'" id="ignore_request_form'.$transform_add_id.'"
					action="set_policy_transform_accepted_request.php" method="post">
	    		<input type="hidden" name="add_id" value="'.$transform_add_id.'"/>
	    		<input type="hidden" name="status" VALUE="Ignore">
	    		<font style="padding-left: 5px;padding-right: 5px;">
	    		<a class="DeclineButtonStyle"
			 	href="#" onclick="document.getElementById('."'ignore_request_form".$transform_add_id."'".').submit();">Ignore</a>
	    		</font>
	       		</form>
				</td>
				<td width="5%">
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
	</table>
	</td>
</tr>
</table>
</html>
<?php

