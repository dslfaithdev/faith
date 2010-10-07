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
		<a href="http://apps.facebook.com/dsl_faith/register_app.php">Step 1 - Register</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/client_library.php">Step 2 - Download Library</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/supported_api.php">FAITH Supported APIs</a>
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
			Step 2 : Download FAITH library</td><td colspan="2">
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
			replace the <font color="#333333"> facebook.php and facebookapi_php5_restlib.php </font> of the official PHP Facebook library in your existing application with FAITH library.
			<br /><br />
			Find your application in <a href="http://apps.facebook.com/dsl_faith/select_app.php">Browse Applications</a>
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Verdana, Arial;font-size: 7pt;">
			<tr>
				<td width="25%" style="text-align: right;line-height: 30px;">
				<font style="font-weight: bold;color: red;">Released in 2010-10-05</font>
				</td>
				<td width="5%"></td>
				<td width="70%">
				<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Verdana, Arial;font-size: 7pt;">
				<tr>
					<td>
					<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/clientlibrary/iframe-10-10-05/iframe-10-10-05.rar">RAR file</a>
					</td>
					<td>
					<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/clientlibrary/iframe-10-10-05/iframe-10-10-05.zip">ZIP file</a>
					</td>
					<td width="300px">
					<font style="font-weight: bold;color: red;">For IFRAME apps</font>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="20px"></td>
			</tr>
			<tr>
				<td width="25%" style="text-align: right;line-height: 30px;">
				<font style="font-weight: bold;color: red;">Released in 2010-10-05</font>
				</td>
				<td width="5%"></td>
				<td width="70%">
				<table cellpadding="0" cellspacing="0" width="100%" style="font-family: Verdana, Arial;font-size: 7pt;">
				<tr>
					<td>
					<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/clientlibrary/fbml-10-10-05/fbml-10-10-05.rar">RAR file</a>
					</td>
					<td>
					<a href="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/clientlibrary/fbml-10-10-05/fbml-10-10-05.zip">ZIP file</a>
					</td>
					<td width="300px">
					<font style="font-weight: bold;color: red;">For FBML and FACEBOOK CONNECT apps</font>
					</td>
				</tr>
				</table>
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
		<td height="20px" colspan="2"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</html>