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
			DSL FAITH Supported REST APIs</td><td colspan="2">
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
				
				<a href="http://apps.facebook.com/dsl_faith/restapitest/admin_getallocation.php">admin_getallocation</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/admin_getappproperties.php">admin_getappproperties</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/admin_getmetrics.php">admin_getmetrics</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/admin_getrestrictioninfo.php">admin_getrestrictioninfo</a><br />
<a href="">Admin.setAppProperties (NOT SUPPORTED BY FAITH)</a><br />
<a href="">Admin.setRestrictionInfo (NOT SUPPORTED BY FAITH)</a><br />
<a href="">Admin.banUsers (NOT SUPPORTED BY FAITH)</a><br />
<a href="">Admin.unbanUsers (NOT SUPPORTED BY FAITH)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/admin_getbannedusers.php">admin_getbannedusers</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/application_getpublicinfo.php">application_getpublicinfo</a><br />
<a href="">Batch.run (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/data_setcookie.php">data_setcookie</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/data_getcookies.php">data_getcookies</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/fbml_setrefhandle.php">fbml_setrefhandle</a><br />
<a href="">Intl.getTranslations (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/intl_uploadnativestrings.php">intl_uploadnativestrings (NOT YET COMPLETED)</a><br />
<a href="">Links.getStats (NOT YET COMPLETED)</a><br />
<a href="">LiveMessage.send (NOT YET COMPLETED)</a><br />
<a href="">Notifications.markRead (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/notifications_sendemail.php">notifications_sendemail</a><br />
<a href="">auth.createToken (NOT SUPPORTED BY FAITH)</a><br />
<a href="">auth.expireSession (NOT SUPPORTED BY FAITH)</a><br />
<a href="">auth.getSession (NOT SUPPORTED BY FAITH)</a><br />
<a href="">auth.promoteSession (NOT SUPPORTED BY FAITH)</a><br />
<a href="">auth.revokeAuthorization (NOT SUPPORTED BY FAITH)</a><br />
<a href="">auth.revokeExtendedPermission (NOT SUPPORTED BY FAITH)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/comments_get.php">comments_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/comments_add.php">comments_add</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/comments_remove.php">comments_remove</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/fbml_refreshimgsrc.php">fbml_refreshimgsrc and fbml_refreshrefurl</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/fql_query.php">fql_query</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/fql_multiquery.php">fql_multiquery</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/friends_arefriends.php">friends_areFriends</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/friends_get.php">friends_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/friends_getappusers.php">friends_getappusers</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/friends_getlists.php">friends_getlists</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/friends_getmutualfriends.php">friends_getmutualfriends</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/groups_get.php">groups_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/groups_getmembers.php">groups_getmembers</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/links_get.php">links_get</a><br />
<a href="">message_getthreadsinfolder (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/notes_get.php">notes_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/notifications_get.php">notifications_get</a><br />
<a href="">Notifications.getList (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/pages_getinfo.php">pages_getinfo</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/pages_isadmin.php">pages_isadmin</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/pages_isappadded.php">pages_isappadded</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/pages_isfan.php">pages_isfan</a><br />
<a href="">Status.get (NOT YET COMPLETED)</a><br />
<a href="">Status.set (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_get.php">stream_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_getcomments.php">stream_getcomments</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_getfilters.php">stream_getfilters</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_getinfo.php">users_getinfo</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_getinfo.php">facebook-get_loggedin_user and facebook-require_login</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_getstandardinfo.php">users_getstandardinfo</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_hasapppermission.php">users_hasapppermission</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_isappuser.php">users_isappuser</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_isverified.php">users_isverified</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/video_getuploadlimits.php">video_getuploadlimits</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/links_post.php">links_post</a><br />
<a href="">Links.preview (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/notes_create.php">notes_create</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/notes_delete.php">notes_delete</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/notes_edit.php">notes_edit</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_addcomment.php">stream_addcomment</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_addlike.php">stream_addlike</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_publish.php">stream_publish</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_remove.php">stream_remove</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_removecomment.php">stream_removecomment</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/stream_removelike.php">stream_removelike</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/users_setstatus.php">users_setstatus</a><br />
<a href="">Video.upload (NOT YET COMPLETED)</a><br />
<a href="">Sms.canSend (NOT SUPPORTED BY FAITH)</a><br />
<a href="">Sms.send (NOT SUPPORTED BY FAITH)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_addglobalnews.php">dashboard_addglobalnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_addnews.php">dashboard_addnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_clearglobalnews.php">dashboard_clearglobalnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_clearnews.php">dashboard_clearnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_decrementcount.php">dashboard_decrementcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_getactivity.php">dashboard_getactivity</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_getcount.php">dashboard_getcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_getglobalnews.php">dashboard_getglobalnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_getnews.php">dashboard_getnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_incrementcount.php">dashboard_incrementcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multiaddnews.php">dashboard_multiaddnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multiclearnews.php">dashboard_multiclearnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multidecrementcount.php">dashboard_multidecrementcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multigetcount.php">dashboard_multigetcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multigetnews.php">dashboard_multigetnews</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multiincrementcount.php">dashboard_multiincrementcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_multisetcount.php">dashboard_multisetcount</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_publishactivity.php">dashboard_publishactivity</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_removeactivity.php">dashboard_removeactivity</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/dashboard_setcount.php">dashboard_setcount</a><br />
<a href="">photos.addTag (NOT YET COMPLETED)</a><br />
<a href="">photos.createAlbum (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/photos_get.php">photos_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/photos_getalbums.php">photos_getalbums</a><br />
<a href="">photos.getTags (NOT YET COMPLETED)</a><br />
<a href="">photos.upload (NOT YET COMPLETED)</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_cancel.php">events_cancel</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_create.php">events_create</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_edit.php">events_edit</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_get.php">events_get</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_getmembers.php">events_getmembers</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_invite.php">events_invite</a><br />
<a href="http://apps.facebook.com/dsl_faith/restapitest/events_rsvp.php">events_rsvp</a><br />
<a href="">fbml.deleteCustomTags (NOT YET COMPLETED)</a><br />
<a href="">fbml.getCustomTags (NOT YET COMPLETED)</a><br />
<a href="">fbml.registerCustomTags (NOT YET COMPLETED)</a><br />
				
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