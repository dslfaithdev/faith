<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Welcome to DSL FAITH </title>
</head>
<style type="text/css">
<?php echo htmlentities(file_get_contents('faith_style.css', true)); ?>
</style>
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
		<a href="http://apps.facebook.com/dsl_faith/select_app.php">Live Search</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/select_app.php?search=1">Bookmarked</a>
		</td>
		<td class="PageTitleLink">
		<a href="http://apps.facebook.com/dsl_faith/select_app.php?search=2">Blocked</a>
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
			<td width="10%"></td>
			<td width="60%" style="padding-top: 30px;text-align: right;">
			<img src="http://cyrus.cs.ucdavis.edu/~dslfaith/faith/image/dsl_logo.jpg" /></td>
			<td width="20%" style="padding-top: 30px;font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;line-height: 35px;text-align: center;vertical-align:bottom;">
			Live Search</td>
			<td width="10%"></td>
		</tr>
		</table>
		</td>
	</tr>
	<?php 

	try
	{
		$facebook = new Facebook($appapikey, $appsecret);
		$user_id = $facebook->require_login();
		mysqlSetup($db);
		
		if(isset($_GET['app_id']) && strlen($_GET['app_id']) > 0)
		{
			$block_app_id = $_GET['app_id'];
			
			$block_results = mysql_query("SELECT Count(uid) as count_num FROM user_disable_app
																	     WHERE user_disable_app.uid = $user_id AND
	            									  						   user_disable_app.app_id = $block_app_id;", $db);
			
			$block_row = mysql_fetch_array($block_results);
			$block_num = $block_row['count_num'];
				
			if($block_num == '0')
			{
 				$query = sprintf("INSERT INTO user_disable_app (uid, 
													 			app_id) 
													 			VALUES('%s', '%s')",
																mysql_real_escape_string($user_id), 
													 			mysql_real_escape_string($block_app_id));
								
				if(!mysql_query($query))
			    {
				    echo '<div class="fberrorbox" style="width: 500px;">  
		    				Failed to block the selected application!  
							</div><br />';
			    }
			    
			    echo '<div class="fbbluebox" style="width: 500px;">  
    			You have successfully blocked the selected application!  
				</div><br />';
			}
			else
			{
				echo '<div class="fbbluebox" style="width: 500px;">  
    			You have already blocked the selected application!  
				</div><br />';
			}
		}
		else if(isset($_GET['unapp_id']) && strlen($_GET['unapp_id']) > 0)
		{
			
			$unblock_app_id = $_GET['unapp_id'];
			
			$unblock_results = mysql_query("DELETE FROM user_disable_app
						  						   WHERE user_disable_app.uid = $user_id AND
            			  								 user_disable_app.app_id = $unblock_app_id;", $db);
 			if(!$unblock_results)
		    {
			    echo '<div class="fberrorbox" style="width: 500px;">  
    			Failed to unblock the selected application!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox" style="width: 500px;">  
	    			You have successfully unblocked the selected application!  
					</div><br />';
		    }
		}
		else if(isset($_GET['bkapp_id']) && strlen($_GET['bkapp_id']) > 0)
		{
			$bk_app_id = $_GET['bkapp_id'];
			
			$bk_results = mysql_query("SELECT Count(uid) as bkcount_num FROM user_bookmark_app
																	    WHERE user_bookmark_app.uid = $user_id AND
	            									  						  user_bookmark_app.app_id = $bk_app_id;", $db);
			
			$bk_row = mysql_fetch_array($bk_results);
			$bk_num = $bk_row['bkcount_num'];
				
			if($bk_num == '0')
			{
 				$query = sprintf("INSERT INTO user_bookmark_app (uid, 
													 			 app_id) 
													 			 VALUES('%s', '%s')",
																 mysql_real_escape_string($user_id), 
													 			 mysql_real_escape_string($bk_app_id));
								
				if(!mysql_query($query))
			    {
				    echo '<div class="fberrorbox" style="width: 500px;">  
		    				Failed to bookmark the selected application!  
							</div><br />';
			    }
			    
			    echo '<div class="fbbluebox" style="width: 500px;">  
    			You have successfully bookmarked the selected application!  
				</div><br />';
			}
			else
			{
				echo '<div class="fbbluebox" style="width: 500px;">  
    			You have already bookmarked the selected application!  
				</div><br />';
			}
		}
		else if(isset($_GET['unbkapp_id']) && strlen($_GET['unbkapp_id']) > 0)
		{
			
			$unbkapp_id = $_GET['unbkapp_id'];
			
			$unbkapp_results = mysql_query("DELETE FROM user_bookmark_app
						  						   WHERE user_bookmark_app.uid = $user_id AND
            			  								 user_bookmark_app.app_id = $unbkapp_id;", $db);
 			if(!$unbkapp_results)
		    {
			    echo '<div class="fberrorbox" style="width: 500px;">  
    			Failed to unbookmark the selected application!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox" style="width: 500px;">  
	    			You have successfully unbookmarked the selected application!  
					</div><br />';
		    }
		}
		
		get_live_search_contents($user_id);
		
		
		if(isset($_POST['search_txt']) && strlen($_POST['search_txt']) > 0)
		{
			$app_select = $_POST['app_select_input'];
			$app_select_sql_query = '';
			if($app_select == '1')
			{
				$app_select_sql_query = 'AND facebook_application.is_canvas = 1';
			}
			else if($app_select == '2')
			{
				$app_select_sql_query = 'AND facebook_application.is_canvas = 2';
			}
			else if($app_select == '3')
			{
				$app_select_sql_query = 'AND facebook_application.is_canvas = 3';
			}
			
			$search_txt = $_POST['search_txt'];
			
			$pageNum = 1;
			if(isset($_GET['page']) && strlen($_GET['page']) > 0)
			{
			    $pageNum = $_GET['page'];
			}
			
			$offset = ($pageNum - 1) * $rowsPerPage;
			$search_words =	$_POST['search_txt'];
			
			$total_row_results = mysql_query("SELECT count(id) as total_row_num
												   	 from facebook_application
												   	 WHERE LOCATE('$search_words', LOWER(app_name)) > 0
												   	 $app_select_sql_query", $db);
			$total_row_= mysql_fetch_array($total_row_results);
			$total_num_of_rows = $total_row_['total_row_num'];
			$maxPage = ceil($total_num_of_rows/$rowsPerPage);
			
			$search_results = mysql_query("SELECT default_page, 
												   app_name, 
												   app_description,
												   id,
												   is_canvas,
												   (SELECT COUNT(uid) FROM user_disable_app WHERE user_disable_app.uid = $user_id 
												   											  AND user_disable_app.app_id = id) as block_app,
												   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.uid = $user_id 
												   											   AND user_bookmark_app.app_id = id) as bookmark_app,
												   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.app_id = id) as total_bookmark_app
												   from facebook_application
												   WHERE LOCATE('$search_words', LOWER(app_name)) > 0
												   $app_select_sql_query
												   LIMIT $offset, $rowsPerPage", $db);
			
			$current_row_number = mysql_num_rows($search_results);
			
			$start_item_num =  ($pageNum - 1) * $rowsPerPage + 1;
			$end_item_num = $start_item_num + $current_row_number - 1;
			
			echo '<tr><td>
				  <table width="100%">
				  <tr><td width="10%"></td>
				  <td width="80%">
				  <br /><br />
				  <table width="100%">
				  <tr>
				  	<td style="border-top: #3b5998 1px solid;background-color: #d4dae8;text-align: right;padding:5px">
				  	Results '.$start_item_num.' - '.$end_item_num.' of '.$total_num_of_rows.' for this search
				  	</td>
				  </tr>
				  <tr><td height="15px"></td></tr>';
			
			while($search_row = mysql_fetch_array($search_results))
			{
				$default_page = $search_row['default_page'];
			  	$app_name = $search_row['app_name'];
			  	$app_description = $search_row['app_description'];
			  	$app_id = $search_row['id'];
			  	$block_app = $search_row['block_app'];
			  	$bookmark_app = $search_row['bookmark_app'];
				$total_bookmark_app = $search_row['total_bookmark_app'];
				$is_canvas = $search_row['is_canvas'];
				
			  	get_search_result_contents($default_page,
			  							   $app_name,
			  							   $app_description,
			  							   $app_id,
			  							   $block_app,
			  							   $bookmark_app,
			  							   $total_bookmark_app,
			  							   $is_canvas);
			}
			echo '
				  <tr><td height="25px"></td></tr>
				  <tr><td style="text-align:center;">
				  '.get_paging_contents($pageNum, $maxPage).'
				  </td></tr>
				  </table>
				  </td><td width="10%"></td></tr>
				  </table></td></tr>';
		}
		else if(isset($_GET['search']) && $_GET['search'] == 1)		//Bookmarked
		{
			$pageNum = 1;
			if(isset($_GET['page']) && strlen($_GET['page']) > 0)
			{
			    $pageNum = $_GET['page'];
			}
			
			$search = $_GET['search'];
			$offset = ($pageNum - 1) * $rowsPerPage;
			$total_row_results = mysql_query("SELECT count(id) as total_row_num
												   	 from facebook_application, user_bookmark_app
												   	 WHERE facebook_application.id = user_bookmark_app.app_id AND
												   	 	   user_bookmark_app.uid = $user_id", $db);
			
			$total_row_= mysql_fetch_array($total_row_results);
			$total_num_of_rows = $total_row_['total_row_num'];
			$maxPage = ceil($total_num_of_rows/$rowsPerPage);
			
			$search_results = mysql_query("SELECT default_page, 
												   app_name, 
												   app_description,
												   id,
												   is_canvas,
												   (SELECT COUNT(uid) FROM user_disable_app WHERE user_disable_app.uid = $user_id 
												   											  AND user_disable_app.app_id = id) as block_app,
												   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.uid = $user_id 
												   											   AND user_bookmark_app.app_id = id) as bookmark_app,
												   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.app_id = id) as total_bookmark_app
												   from facebook_application, user_bookmark_app
												   WHERE facebook_application.id = user_bookmark_app.app_id AND
												   	 	 user_bookmark_app.uid = $user_id
												   LIMIT $offset, $rowsPerPage", $db);
			
			$current_row_number = mysql_num_rows($search_results);
			
			$start_item_num =  ($pageNum - 1) * $rowsPerPage + 1;
			$end_item_num = $start_item_num + $current_row_number - 1;
			
			echo '<tr><td>
				  <table width="100%">
				  <tr><td width="10%"></td>
				  <td width="80%">
				  <br /><br /><br />
				  <table width="100%">
				  <tr>
				  	<td style="border-top: #3b5998 1px solid;background-color: #d4dae8;text-align: right;padding:5px">
				  	Results '.$start_item_num.' - '.$end_item_num.' of '.$total_num_of_rows.' bookmarks
				  	</td>
				  </tr>
				  <tr><td height="15px"></td></tr>';
			
			while($search_row = mysql_fetch_array($search_results))
			{
				$default_page = $search_row['default_page'];
			  	$app_name = $search_row['app_name'];
			  	$app_description = $search_row['app_description'];
			  	$app_id = $search_row['id'];
			  	$block_app = $search_row['block_app'];
			  	$bookmark_app = $search_row['bookmark_app'];
				$total_bookmark_app = $search_row['total_bookmark_app'];
				$is_canvas = $search_row['is_canvas'];
				
			  	get_search_result_contents($default_page,
			  							   $app_name,
			  							   $app_description,
			  							   $app_id,
			  							   $block_app,
			  							   $bookmark_app,
			  							   $total_bookmark_app,
			  							   $is_canvas);
			}
			echo '
				  <tr><td height="25px"></td></tr>
				  <tr><td style="text-align:center;">
				  '.get_search_paging_contents($pageNum, $maxPage).'
				  </td></tr>
				  </table>
				  </td><td width="10%"></td></tr>
				  </table></td></tr>';
		}
		else if(isset($_GET['search']) && $_GET['search'] == 2)		//Blocked
		{
			$pageNum = 1;
			if(isset($_GET['page']) && strlen($_GET['page']) > 0)
			{
			    $pageNum = $_GET['page'];
			}
			
			$search = $_GET['search'];
			$offset = ($pageNum - 1) * $rowsPerPage;
			$total_row_results = mysql_query("SELECT count(id) as total_row_num
												   	 from facebook_application, user_disable_app
												   	 WHERE facebook_application.id = user_disable_app.app_id AND
												   	 	   user_disable_app.uid = $user_id", $db);
			
			$total_row_= mysql_fetch_array($total_row_results);
			$total_num_of_rows = $total_row_['total_row_num'];
			$maxPage = ceil($total_num_of_rows/$rowsPerPage);
			
			$search_results = mysql_query("SELECT default_page, 
												   app_name, 
												   app_description,
												   id,
												   is_canvas,
												   (SELECT COUNT(uid) FROM user_disable_app WHERE user_disable_app.uid = $user_id 
												   											  AND user_disable_app.app_id = id) as block_app,
												   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.uid = $user_id 
												   											   AND user_bookmark_app.app_id = id) as bookmark_app,
												   (SELECT COUNT(uid) FROM user_bookmark_app WHERE user_bookmark_app.app_id = id) as total_bookmark_app
												   from facebook_application, user_disable_app
												   WHERE facebook_application.id = user_disable_app.app_id AND
												   	 	 user_disable_app.uid = $user_id
												   LIMIT $offset, $rowsPerPage", $db);
			
			$current_row_number = mysql_num_rows($search_results);
			
			$start_item_num =  ($pageNum - 1) * $rowsPerPage + 1;
			$end_item_num = $start_item_num + $current_row_number - 1;
			
			echo '<tr><td>
				  <table width="100%">
				  <tr><td width="10%"></td>
				  <td width="80%">
				  <br /><br /><br />
				  <table width="100%">
				  <tr>
				  	<td style="border-top: #3b5998 1px solid;background-color: #d4dae8;text-align: right;padding:5px">
				  	Results '.$start_item_num.' - '.$end_item_num.' of '.$total_num_of_rows.' blocked applications
				  	</td>
				  </tr>
				  <tr><td height="15px"></td></tr>';
			
			while($search_row = mysql_fetch_array($search_results))
			{
				$default_page = $search_row['default_page'];
			  	$app_name = $search_row['app_name'];
			  	$app_description = $search_row['app_description'];
			  	$app_id = $search_row['id'];
			  	$block_app = $search_row['block_app'];
			  	$bookmark_app = $search_row['bookmark_app'];
			  	$total_bookmark_app = $search_row['total_bookmark_app'];
				$is_canvas = $search_row['is_canvas'];
				
			  	get_search_result_contents($default_page,
			  							   $app_name,
			  							   $app_description,
			  							   $app_id,
			  							   $block_app,
			  							   $bookmark_app,
			  							   $total_bookmark_app,
			  							   $is_canvas);
			}
			echo '
				  <tr><td height="25px"></td></tr>
				  <tr><td style="text-align:center;">
				  '.get_search_paging_contents($pageNum, $maxPage).'
				  </td></tr>
				  </table>
				  </td><td width="10%"></td></tr>
				  </table></td></tr>';
		}
	}
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	?>
	
	<?php 
	
	function get_search_paging_contents($pageNum, $maxPage)
	{
		$start_paging_num = $pageNum - 5;
		if($start_paging_num <= 1)
		{
			$start_paging_num = 1;
		}
		
		$end_paging_num = $pageNum + 5;
		if($end_paging_num >= $maxPage)
		{
			$end_paging_num = $maxPage;
		}
		
		$paging_html = '';
		
		for($i = $start_paging_num; $i <= $end_paging_num; $i++)
		{
			if($i == $pageNum)
			{
				$paging_html .= 
				'<font style="padding-left: 5px;padding-right: 5px;font-weight: bold;">'.$i.'</font>';
			}
			else
			{
				$paging_html .= 
				'
				<a style="padding-left: 5px;padding-right: 5px;" 
					href="select_app.php?page='.$i.'&search='.$_GET['search'].'">'.$i.'</a>';
			}
		}
		
		return $paging_html;
	}
	
	function get_paging_contents($pageNum, $maxPage)
	{
		$start_paging_num = $pageNum - 5;
		if($start_paging_num <= 1)
		{
			$start_paging_num = 1;
		}
		
		$end_paging_num = $pageNum + 5;
		if($end_paging_num >= $maxPage)
		{
			$end_paging_num = $maxPage;
		}
		
		$paging_html = '';
		
		for($i = $start_paging_num; $i <= $end_paging_num; $i++)
		{
			if($i == $pageNum)
			{
				$paging_html .= 
				'<font style="padding-left: 5px;padding-right: 5px;font-weight: bold;">'.$i.'</font>';
			}
			else
			{
				$paging_html .= 
				'
				<form style="display: inline" action="select_app.php?page='.$i.'" name="select_app_paging_form'.$i.'" id="select_app_paging_form'.$i.'" method="post">
				<input type="hidden" name="search_txt" value="'.$_POST['search_txt'].'"/>
				<input type="hidden" name="app_select_input" value="'.$_POST['app_select_input'].'"/>
				<a style="padding-left: 5px;padding-right: 5px;" 
					href="#" onclick="document.getElementById('."'select_app_paging_form".$i."'".').submit();">'.$i.'</a>
				</form>';
			}
		}
		
		return $paging_html;
	}
	
	function get_search_result_contents($default_page,
		  							   	$app_name,
		  							    $app_description,
		  							   	$app_id,
		  							   	$block_app,
		  							   	$bookmark_app,
		  							   	$total_bookmark_app,
		  							   	$is_canvas)
	{
		$canvas_str = '<font style="padding-left: 10px;padding-right: 10px;color: #aa3333;border-right: #AAAAAA 1px solid;">FBML App</font>';
	  	if($is_canvas == '2')
	  	{
	  		$canvas_str = '<font style="padding-left: 10px;padding-right: 10px;color: #aa3333;border-right: #AAAAAA 1px solid;">IFrame App</font>';
	  	}
		else if($is_canvas == '3')
	  	{
	  		$canvas_str = '<font style="padding-left: 10px;padding-right: 10px;color: #aa3333;border-right: #AAAAAA 1px solid;">Facebook Connect App</font>';
	  	}
	  	
		$total_bookmark_str = 
	  	'<font style="padding-left: 10px;padding-right: 10px;color: #3b5998;">total '.$total_bookmark_app.' bookmark(s)</font>';
		
		$block_me_str = 
	  	'<a style="padding-left: 10px;padding-right: 10px;border-right: #AAAAAA 1px solid;text-decoration: underline;" 
	  		href="http://apps.facebook.com/dsl_faith/select_app.php?app_id='.$app_id.'">block</a>';
		
		$bookmark_me_str = 
	  	'<a style="padding-left: 10px;padding-right: 10px;border-right: #AAAAAA 1px solid;text-decoration: underline;" 
	  		href="http://apps.facebook.com/dsl_faith/select_app.php?bkapp_id='.$app_id.'">bookmark</a>';
	  	
	  	if($block_app > 0)
	  	{
	  		$block_me_str = 
	  		'<a style="padding-left: 10px;padding-right: 10px;border-right: #AAAAAA 1px solid;color: red;text-decoration: underline;" 
	  			href="http://apps.facebook.com/dsl_faith/select_app.php?unapp_id='.$app_id.'">blocked</a>';
	  	}
	  	
		if($bookmark_app > 0)
	  	{
	  		$bookmark_me_str = 
	  		'<a style="padding-left: 10px;padding-right: 10px;border-right: #AAAAAA 1px solid;color: #333333;text-decoration: underline;font-weight: bold;" 
	  			href="http://apps.facebook.com/dsl_faith/select_app.php?unbkapp_id='.$app_id.'">bookmarked</a>';
	  	}
	  	
	  	$view_history = 
	  	'<form style="display: inline" action="view_history_app.php" name="view_history_app_form'.$app_id.'" id="view_history_app_form'.$app_id.'" method="post">
	  	 <INPUT type="hidden" name="AppID" VALUE="'.$app_id.'">
	  	 <a style="padding-left: 10px;padding-right: 10px;border-right: #AAAAAA 1px solid;text-decoration: underline;" 
	  	 	href="#" onclick="document.getElementById('."'view_history_app_form".$app_id."'".').submit();">view log</a>
	  	 </form>';
	  	
       	echo 
       	'
       	<tr>
    		<td width="100%" style="padding-top:10px;">
       		<a style="text-decoration: underline; font-size: 9pt; line-height: 20px;font-weight: bold;font-family: Verdana, Arial;"
       			href="http://apps.facebook.com/dsl_faith/index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
       		</td>
       	</tr>
       	<tr>
       		<td style="font-size: 8pt; line-height: 15px;font-family: Verdana, Arial;">
       		'.$app_description.'
       		</td>
       	</tr>
       	<tr>
       		<td style="border-bottom: #CCCCCC 1px solid; padding-bottom: 10px;">
       		'.$view_history.$block_me_str.$bookmark_me_str.$canvas_str.$total_bookmark_str.'
       		</td>
    	</tr>';
	}
	
	function get_live_search_contents($user_id)
	{
		$app_select = $_POST['app_select_input'];
		$app_select1;
		$app_select2;
		$app_select3;
		$app_select4;
		
		if($app_select == '1')
		{
			$app_select1 = 'CHECKED';
		}
		else if($app_select == '2')
		{
			$app_select2 = 'CHECKED';
		}
		else if($app_select == '3')
		{
			$app_select3 = 'CHECKED';
		}
		else if($app_select == '4')
		{
			$app_select4 = 'CHECKED';
		}
		else
		{
			$app_select1 = 'CHECKED';
		}
			
		$search_text = $_POST['search_txt'];
		
		echo
		'
		<tr>
		<td style="vertical-align:top;">
		
		<form style="display: inline;" action="select_app.php" method="post">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr><td width="10%"></td>
		
		<td width="60%" style="vertical-align: top;">
		<table cellpadding="0" cellspacing="0" width="100%" style="background-color: #3b5998;">
		<tr><td>
		<input type="text" name="search_txt" id="search_txt" maxlength="30" style="width: 450px;"
			onkeyup="do_ajax('."'live_search_Div'".',1, '.$user_id.');" value="'.$search_text.'"></input>
		</td></tr>
		<tr><td>
		<div id="live_search_Div" style="background-color: #ffffff;text-align: center;">
		<font style="line-height: 20px;font-size: 9pt;">search applications in FAITH.</font>
		</div>
		</td></tr>
		</table>
		</td>
		
		<td width="20%" style="vertical-align: top;text-align: right;padding-left: 5px;">
		<INPUT type="submit" id="live_search" name = "live_search" value="Search Applications" style="width: 130px;" />
		<br /><br />
		<table cellpadding="0" cellspacing="0" width="100%" style="border-left: #3b5998 1px solid;padding-left: 5px;">
		<tr><td height="50px" style="vertical-align: top;background-color: #eceff6;">
			<table cellpadding="0" cellspacing="10" width="100%">
			<tr>
				<td>
				<font style="font-weight: bolder;font-size: 10pt;font-family: Verdana, Arial;text-decoration: underline;text-align: right;">Search for</font><br />
				</td>
			</tr>
			<tr>
				<td>
				<input type="radio" name="app_select_input" id="app_select_input1" value="1" '.$app_select1.'>FBML Only</input><br />
				<input type="radio" name="app_select_input" id="app_select_input2" value="2" '.$app_select2.'>IFrame Only</input><br />
				<input type="radio" name="app_select_input" id="app_select_input3" value="3" '.$app_select3.'>Facebook Connect Only</input><br />
				<input type="radio" name="app_select_input" id="app_select_input4" value="4" '.$app_select4.'>ALL</input>
				</td>
			</tr>
			</table>
		</td></tr>
		</table>
		</td>
		
		<td width="10%"></td></tr>
		</table>
		</form>
		</td>
		</tr>
		';
	}
	
	/*function get_select_application_table_Title()
	{
		echo
		'
		<tr>
		<td>
		<br /><br />
		<table cellpadding="0" cellspacing="0" width="750px">
		<tr><td width="50px"></td>
		<td>
		<h4>Applications Blocked :</h4>
		</td>
		<td width="50px"></td></tr>
		</table>
		</td>
		</tr>
		';
	}*/
	
	/*function get_select_application_table_contents($default_page,
									  			   $app_name,
									  			   $app_description,
									  			   $app_id)
	{
		echo 
		'<tr><td>
		<table style="font-family: Verdana, Arial;font-size: 8pt;line-height: 15px;">
		<tr>
			<td colspan="2" height="5px"></td>
		</tr>
		<tr>
			<td width="50px"></td>
			<td>
			<label>
			<a href="http://apps.facebook.com/dsl_faith/index.php?ffile=' . $default_page . '&fpro=' . $app_id .'">' . $app_name . '</a>
			</label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>' .
			$app_description
			. '<br />
			<a style="padding-left: 5px;padding-right: 5px;text-decoration: underline;font-style: italic;" 
	  						href="http://apps.facebook.com/dsl_faith/select_app.php?unapp_id='.$app_id.'">unblock it</a></td>
		</tr>
		</table>
		</td></tr>';
	}*/
	
	?>
	<tr>
		<td height="20px"></td>
	</tr>
	</table>
	</td>
</tr>
<script type="text/javascript">
<!--
function do_ajax(div,val,uid) {
	
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.ondone = function(data) 
				  {
				  	document.getElementById(div).setInnerFBML(data);
				  	document.getElementById(div).setStyle('border', '1px solid #A5ACB2');
				  	document.getElementById(div).setStyle('background', 'white');
				  	document.getElementById(div).setStyle('width', '99%');
				  }

	var select_app_value = '4';
	if(document.getElementById('app_select_input1').getChecked())
	{
		select_app_value = '1';
	}
	else if(document.getElementById('app_select_input2').getChecked())
	{
		select_app_value = '2';
	}
	else if(document.getElementById('app_select_input3').getChecked())
	{
		select_app_value = '3';
	}
	
	var params={"action":'select',"option":val,"searchwords":document.getElementById('search_txt').getValue(),"otherval":uid,"app_select":select_app_value};
	ajax.post('<?=$callbackurl?>?t='+val,params); 
	} 
//-->
</script>
</table>
</html>
