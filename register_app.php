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
		<a href="<?php echo $facebook_canvas_page_url; ?>register_app.php">Step 1 - Register</a>
		</td>
		<td class="PageTitleLink">
		<a href="<?php echo $facebook_canvas_page_url; ?>client_library.php">Step 2 - Download Library</a>
		</td>
		<td class="PageTitleLink">
		<a href="<?php echo $facebook_canvas_page_url; ?>supported_api.php">FAITH Supported APIs</a>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td style="vertical-align:top;border-right: #3b5998 3px solid;border-top: #3b5998 3px solid;border-left: #3b5998 3px solid;border-bottom: #3b5998 3px solid;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-top: 20px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 45px;text-align: left;">
			Step 1 : Register your Facebook application with FAITH</td><td colspan="2">
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
	<?php
	
	try
	{
		$facebook = new Facebook($appapikey, $appsecret);
		$user_id = $facebook->require_login();
		
		if (isset($_POST['canvas_page']) && strlen($_POST['canvas_page']) > 0 &&
			isset($_POST['canvas_callback']) && strlen($_POST['canvas_callback']) > 0 &&
			isset($_POST['default_page']) && strlen($_POST['default_page']) > 0 &&
			isset($_POST['app_name']) && strlen($_POST['app_name']) > 0 &&
			isset($_POST['app_description']) && strlen($_POST['app_description']) > 0)
		{
			$canvas_page = htmlentities($_POST['canvas_page']);
			$canvas_callback = htmlentities($_POST['canvas_callback']);
			$default_page = htmlentities($_POST['default_page']);
			$app_name = htmlentities($_POST['app_name']);
			$app_description = htmlentities($_POST['app_description']);
			$register_app_input = htmlentities($_POST['register_app_input']);
			
			mysqlSetup($db);
			
			$query = sprintf("INSERT INTO facebook_application (canvas_page, 
																canvas_callback, 
																default_page, 
																app_name, 
																app_description,
																uid,
																is_canvas)
            													VALUES( '%s', '%s','%s','%s','%s','%s','%s')",
													            mysql_real_escape_string($canvas_page),
													            mysql_real_escape_string($canvas_callback),
													            mysql_real_escape_string($default_page),
													            mysql_real_escape_string($app_name),
													            mysql_real_escape_string($app_description),
													            mysql_real_escape_string($user_id),
													            mysql_real_escape_string($register_app_input));
			
			if(!mysql_query($query))
            {
	            echo 'Query failed '.mysql_error();
	            get_input_table_contents($canvas_page,
									 $canvas_callback,
									 $default_page,
									 $app_name,
									 $app_description);
	            exit();
            }
      		else
            {
            	$sql_id = mysql_insert_id();
            	
            	$results = mysql_query("SELECT canvas_page, 
											   canvas_callback, 
											   default_page, 
											   app_name, 
											   app_description,
											   uid,
											   is_canvas
											   from facebook_application
											   where id = $sql_id", $db);
            	
            	$row = mysql_fetch_array($results);
            	$new_canvas_page = html_entity_decode($row['canvas_page']);
				$new_canvas_callback = html_entity_decode($row['canvas_callback']);
				$new_default_page = html_entity_decode($row['default_page']);
				$new_app_name = html_entity_decode($row['app_name']);
				$new_app_description = html_entity_decode($row['app_description']);
				$is_canvas = html_entity_decode($row['is_canvas']);
				
				get_success_table_contents($new_canvas_page,
										   $new_canvas_callback,
										   $new_default_page,
										   $new_app_name,
										   $new_app_description,
										   $is_canvas);
        	}
		}
		else
		{
			get_input_table_contents();
		}
	}
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	?>
	
	<?php 
	
	function get_input_table_contents($canvas_page,
									  $canvas_callback,
									  $default_page = 'index.php',
									  $app_name,
									  $app_description)
	{
		echo 
		'<form action="register_app.php" method="post">
		<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Verdana, Arial;font-size: 8pt;">
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Default Page</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<input type="text" id="default_page" name="default_page" size="50" maxlength="80" value="'. $default_page .'" />
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Application Name</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<input type="text" id="app_name" name="app_name" size="50" maxlength="200" value="'. $app_name .'"  />
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Description</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<input type="text" id="app_description" name="app_description" size="50" maxlength="1000" value="'. $app_description .'"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" height="15px"></td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Canvas Page URL</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<input type="text" id="canvas_page" name="canvas_page" size="50" maxlength="200" value="'. $canvas_page .'"  />
			<font color="red">must end with a slash</font>
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			</td>
			<td width="10%"></td>
			<td width="70%">
			The base URL for your canvas pages on FAITH (e.g., http://apps.facebook.com/faith/)
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Canvas Callback URL</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<input type="text" id="canvas_callback" name="canvas_callback" size="50" maxlength="200" value="'. $canvas_callback .'" />
			<font color="red">must end with a slash</font>
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			</td>
			<td width="10%"></td>
			<td width="70%">
			FAITH pulls the content for your application canvas pages from this URL or directory.
			(e.g., http://cyrus.cs.ucdavis.edu/~test/testprojectone/)
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 50px;">
			<label for="canvas_page">Application Type</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<input type="radio" name="register_app_input" id="register_app_input_fbml" value="1" Checked>FBML Application</input>
			<input type="radio" name="register_app_input" id="register_app_input_iframe" value="2">IFrame Application</input>
			<input type="radio" name="register_app_input" id="register_app_input_fbconnect" value="3">Facebook Connect Application</input>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="10px"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td width="70%">
			<input type="submit" id="submit" name = "submit" value="Submit Your Application!" />
			</td>
		</tr>
		<tr>
			<td colspan="3" height="10px"></td>
		</tr>
		</table>
		</form>';
	}
	
	function get_success_table_contents($new_canvas_page,
										$new_canvas_callback,
										$new_default_page,
										$new_app_name,
										$new_app_description,
										$is_canvas)
	{
		$application_type = 'FBML Application';
		if($is_canvas == '2')
		{
			$application_type = 'IFrame Application';
		}
		else if($is_canvas == '3')
		{
			$application_type = 'Facebook Connect Application';
		}
		
		echo 
		'
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td colspan="3" height="30px" style="text-align: center;">
			<label><font color="#333333">Thank you for summitting your application! You have completed step 1. Please proceed to step 2.</font></label></td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Default Page</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<label>'. $new_default_page .'</label>
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Application Name</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<label>'. $new_app_name .'</label>
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Description</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<label>'. $new_app_description .'</label>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="30px"></td>
		</tr>
		<tr>
			<td width="20%" width="200px" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Canvas Page URL</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<label>'. $new_canvas_page .'</label>
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Canvas Callback URL</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<label>' . $new_canvas_callback . '</label>
			</td>
		</tr>
		<tr>
			<td width="20%" style="text-align: right;line-height: 30px;">
			<label for="canvas_page">Application Type</label>
			</td>
			<td width="10%"></td>
			<td width="70%">
			<label>' . $application_type . '</label>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="10px"></td>
		</tr>
		</table>';
	}
	?>
		</td>
		<td width="5%"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</html>
