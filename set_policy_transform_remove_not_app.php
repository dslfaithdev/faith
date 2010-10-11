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
<?php

$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();

try
{
	if(isset($_GET['id']) && strlen($_GET['id']) > 0)
	{
		$remove_id = $_GET['id'];
		mysqlSetup($db);
		
		if($_POST['select_application_submit'] == 'select')
		{
			$remove_id = $_POST['remove_id'];
			$not_apply_app_uid = $_POST['not_apply_app_uid'];
			
			$app_results = mysql_query("SELECT Count(not_apply_app_uid) as count_num FROM transform_remove_app
																	        		WHERE transform_remove_app.transform_remove_id = $remove_id AND
																	        		   	  transform_remove_app.not_apply_app_uid = $not_apply_app_uid;", $db);
			
			$app_row = mysql_fetch_array($app_results);
			$app_num = $app_row['count_num'];
				
			if($app_num == '0')
			{
				date_default_timezone_set('America/Los_Angeles');
				$time_added = date("Y-m-d H:i:s");
				
 				$query = sprintf("INSERT INTO transform_remove_app (transform_remove_id, 
													 				not_apply_app_uid,
													 				time_added) 
														 		    VALUES('%s', '%s', '%s')",
																    mysql_real_escape_string($remove_id), 
														 		    mysql_real_escape_string($not_apply_app_uid),
														 		    mysql_real_escape_string($time_added));
								
				if(!mysql_query($query))
			    {
				    echo '<div class="fberrorbox">  
		    				Failed to add the application to the list!  
							</div><br />';
			    }
			    else
			    {
				    echo '<div class="fbbluebox">  
	    			You have successfully added the application to the list!  
					</div><br />';
			    }
			}
			else
			{
				echo '<div class="fbbluebox">  
    			You have already added the application to the list!  
				</div><br />';
			}
		}
		else if($_POST['remove_from_list_submit'] == 'remove')
		{
			$remove_id = $_POST['remove_id'];
			$not_apply_app_uid = $_POST['not_apply_app_uid'];
			
			$remove_app_results = mysql_query("DELETE FROM transform_remove_app
						  						      WHERE transform_remove_app.transform_remove_id = $remove_id AND
            			  								    transform_remove_app.not_apply_app_uid = $not_apply_app_uid;", $db);
 			if(!$remove_app_results)
		    {
			    echo '<div class="fberrorbox">  
    			Failed to remove the application from list!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox">  
	    			You have successfully removed the application from list!  
					</div><br />';
		    }
		}
		
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
			
			echo 
			'<table width="100%" style="padding-top: 5px;">
			<tr>
				<td colspan="3">
				Hi <fb:name uid="'.$remove_uid_a.'" useyou="false" linked="true" />, please select the applications
				which you do NOT want the following removal rule apply to
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
			
	<?php 

	try
	{
		$facebook = new Facebook($appapikey, $appsecret);
		$user_id = $facebook->require_login();
		mysqlSetup($db);
		
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
				$app_select_sql_query = 'AND facebook_application.is_canvas = 0';
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
												   is_canvas
												   from facebook_application
												   WHERE LOCATE('$search_words', LOWER(app_name)) > 0
												   $app_select_sql_query
												   LIMIT $offset, $rowsPerPage", $db);
			
			$current_row_number = mysql_num_rows($search_results);
			
			$start_item_num =  ($pageNum - 1) * $rowsPerPage + 1;
			$end_item_num = $start_item_num + $current_row_number - 1;
			
			echo '<tr><td>
				  <table width="100%">
				  <tr>
				  	<td style="border-top: #3b5998 1px solid;background-color: #d4dae8;text-align: right;padding:5px">
				  	Results '.$start_item_num.' - '.$end_item_num.' of '.$total_num_of_rows.' for this search
				  	</td>
				  </tr>
				  ';
			
			while($search_row = mysql_fetch_array($search_results))
			{
				$default_page = $search_row['default_page'];
			  	$app_name = $search_row['app_name'];
			  	$app_description = $search_row['app_description'];
			  	$app_id = $search_row['id'];
			  	$is_canvas = $search_row['is_canvas'];
				
			  	get_search_result_contents($default_page,
			  							   $app_name,
			  							   $app_description,
			  							   $app_id,
			  							   $is_canvas);
			}
			echo '
				  <tr><td height="25px"></td></tr>
				  <tr><td style="text-align:center;">
				  '.get_paging_contents($pageNum, $maxPage).'
				  </td></tr>
				  </table>
				  </td></tr>';
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
				$remove_id = $_GET['id'];
				
				$paging_html .= 
				'
				<form style="display: inline" action="set_policy_transform_remove_not_app.php?id='.$remove_id.'&page='.$i.'" name="select_app_paging_form'.$i.'" id="select_app_paging_form'.$i.'" method="post">
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
		  							   	$is_canvas)
	{
		GLOBAL $facebook_canvas_page_url;
		
		$canvas_str = '<font style="padding-left: 5px;padding-right: 5px;font-weight: bold;color: #FF0000;">FBML App</font>';
	  	if($is_canvas == '2')
	  	{
	  		$canvas_str = '<font style="padding-left: 5px;padding-right: 5px;font-weight: bold;color: #FF0000;">IFrame App</font>';
	  	}
		else if($is_canvas == '3')
	  	{
	  		$canvas_str = '<font style="padding-left: 5px;padding-right: 5px;font-weight: bold;color: #FF0000;">Facebook Connect App</font>';
	  	}
	  	
	  	$remove_id = $_GET['id'];
	  	
       	echo 
       	'
       	<tr>
    		<td style="padding-top:10px;" width="100%">
    		<form style="display: inline;" action="set_policy_transform_remove_not_app.php?id='.$remove_id.'" method="post">
    		<input type="hidden" name="remove_id" value="'.$remove_id.'"/>
			<input type="hidden" name="not_apply_app_uid" value="'.$app_id.'"/>
    		<font style="padding-left: 5px;padding-right: 5px;">
    		<INPUT type="submit" id="select_application_submit" name = "select_application_submit" value="select" />
    		</font>
       		</form>
    		<a style="text-decoration: underline; font-size: 9pt; line-height: 20px;font-weight: bold;font-family: Verdana, Arial;"
       			href="'.$facebook_canvas_page_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
       		
       		</td>
       	</tr>
       	<tr>
       		<td style="font-size: 8pt; line-height: 15px;font-family: Verdana, Arial;border-bottom: #CCCCCC 1px solid; padding-bottom: 10px;">
       		'.$canvas_str
       		.$app_description.'
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
			$app_select4 = 'CHECKED';
		}
			
		$search_text = $_POST['search_txt'];
		
		$remove_id = $_GET['id'];
		
		echo
		'
		<tr>
		<td style="vertical-align:top;">
		
		<form style="display: inline;" action="set_policy_transform_remove_not_app.php?id='.$remove_id.'" method="post">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr><td width="10%"></td>
		
		<td width="60%" style="vertical-align: top;">
		<table cellpadding="0" cellspacing="0" width="100%" style="background-color: #3b5998;">
		<tr><td>
		<input type="text" name="search_txt" id="search_txt" maxlength="30" style="width: 450px;"
			onkeyup="do_remove_app_ajax('."'live_search_Div'".',22, '.$user_id.','.$remove_id.');" value="'.$search_text.'"></input>
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
	
	?>
				
			</table>
			</td>
			<td width="5%"></td>
		</tr>

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
			
			$results = mysql_query("SELECT transform_remove_app.not_apply_app_uid,
										   transform_remove_app.time_added,
										   facebook_application.default_page, 
										   facebook_application.app_name, 
										   facebook_application.app_description,
										   facebook_application.id,
										   facebook_application.is_canvas
										   from transform_remove_app, facebook_application
										   where transform_remove_app.not_apply_app_uid = facebook_application.id AND
										         transform_remove_app.transform_remove_id = $remove_id
										   order by transform_remove_app.time_added DESC", $db);
	      
			$application_displayed = 0;
			
			while($row = mysql_fetch_array($results))
			{
				$not_apply_app_uid = $row['not_apply_app_uid'];
				$time_added = $row['time_added'];
				$default_page = $row['default_page'];
			  	$app_name = $row['app_name'];
			  	$app_description = $row['app_description'];
			  	$app_id = $row['id'];
			  	$is_canvas = $row['is_canvas'];
			  	
				get_NOT_apply_application_contents($remove_id,
											  	   $not_apply_app_uid,
											       $time_added,
											       $default_page,
			  							   		   $app_name,
			  							           $app_description,
			  							           $app_id,
			  							           $is_canvas);
			  							           
			 	$application_displayed++;
			}
			
			if($application_displayed == 0)
			{
				echo '<tr><td><br /><br /><h5>You have not added any applications.</h5></td></tr>';
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

	function get_NOT_apply_application_contents($remove_id,
										        $not_apply_app_uid,
										        $time_added,
										        $default_page,
			  							   		$app_name,
			  							        $app_description,
			  							        $app_id,
			  							        $is_canvas)
	{
		GLOBAL $facebook_canvas_page_url;
		
		echo 
		'
		<tr>
			<td width="100%">
			<form style="display: inline;" action="set_policy_transform_remove_not_app.php?id='.$remove_id.'" method="post">
			<INPUT type="hidden" name="remove_id" VALUE="'.$remove_id.'">
			<INPUT type="hidden" name="not_apply_app_uid" VALUE="'.$not_apply_app_uid.'">
			<INPUT type="submit" name="remove_from_list_submit" value="remove" />
			</form>
			<font style="padding-left: 10px;padding-right: 10px;">
			<a style="text-decoration: underline; font-size: 9pt; line-height: 20px;font-weight: bold;font-family: Verdana, Arial;"
       			href="'.$facebook_canvas_page_url.'index.php?ffile=' . $default_page . '&fpro=' . $app_id .'"">'.$app_name.'</a>
			</font>
			Time: '.$time_added.'
			</td>';
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
<script type="text/javascript">
<!--
function do_remove_app_ajax(div,val,uid,remove_id) {
	
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
	
	var params={"action":'select',"option":val,"searchwords":document.getElementById('search_txt').getValue(),"otherval":uid,"app_select":select_app_value,"remove_id":remove_id};
	ajax.post('<?=$set_policy_transform_callbackurl?>?t='+val,params); 
	} 
//-->
</script>
</table>
</html>

