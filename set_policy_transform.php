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
			<td colspan="2" width="90%" style="padding-top: 20px;padding-bottom: 5px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 45px;text-align: left;border-bottom: #AAAAAA 1px solid;">
			Social Network Transformation</td><td colspan="2">
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="45%" valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="8%" height="45px">
				<img src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/addconn.gif" />
				</td>
				<td width="92%" class="TransformTitleLink">
				<a href="http://apps.facebook.com/dsl_faith/set_policy_transform_add.php">Add Virtual Friendship Connections</a>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="TransformTitleDescription">
				Add virtual friendship connections to your facebook social graph. FAITH transforms the real Facebook social graph to virtual FAITH social graph and outputs the transformed graph to the applications behide FAITH.
				</td>
			</tr>
			<tr>
				<td width="8%" height="45px">
				<img src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/addconnbyfile.gif" />
				</td>
				<td width="92%" class="TransformTitleLink">
				<a href="http://apps.facebook.com/dsl_faith/set_policy_transform_addbyfile.php">Add Virtual Friendship Connections by Files</a>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="TransformTitleDescription">
				Add virtual friendship connections to your facebook social graph by uploading files to FAITH for faster inputs.
				</td>
			</tr>
			</table>
			</td>
			<td width="45%" valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="8%" height="45px">
				<img src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/addconn.gif" />
				</td>
				<td width="92%" class="TransformTitleLink">
				<a href="http://apps.facebook.com/dsl_faith/set_policy_transform_remove.php">Remove Virtual Friendship Connections</a>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="TransformTitleDescription">
				Remove existing friendship connections from your virtual FAITH social graph. FAITH transforms the real Facebook social graph to virtual FAITH social graph and outputs the transformed graph to the applications behide FAITH.
				</td>
			</tr>
			<tr>
				<td width="8%" height="45px">
				<img src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/remconnbyfile.gif" />
				</td>
				<td width="92%" class="TransformTitleLink">
				<a href="http://apps.facebook.com/dsl_faith/set_policy_transform_rembyfile.php">Remove Virtual Friendship Connections by Files</a>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="TransformTitleDescription">
				Remmove existing friendship connections from your virtual FAITH social graph by uploading files to FAITH for faster inputs.
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

