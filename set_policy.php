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
			at RESTful API Level
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%" style="padding-bottom: 15px;font-size: 8pt;font-family: Verdana, Arial;line-height: 15px;text-align: left;border-bottom: #AAAAAA 1px solid;">
			The Facebook RESTful API uses a REST-like interface. The Facebook method calls are made over the internet by sending HTTP GET or POST requests to the Facebook API REST server. 
			Nearly any computer language can be used to communicate over HTTP with the REST server, and it is one of the main methods Facebook applications use to retrieve user information from Facebook. 
			You can disable any Facebook API to prevent the applications from retrieving your information.
			</td>
			<td width="5%"></td>
		</tr>
		<tr>
			<td width="5%"></td>
			<td width="90%">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="35%" style="border-right: #AAAAAA 1px solid;height: 350px;vertical-align:top;">
				<table cellpadding="0" cellspacing="10" width="100%">
<?php 
require_once 'vars.php';
require_once 'facebook.php';

try
{
	$facebook = new Facebook($appapikey, $appsecret);
	$user_id = $facebook->require_login();
	
	mysqlSetup($db);
	
	if(!$_GET['api_id'])
	{
		$results = mysql_query("SELECT id, 
									   name 
									   from restapi_field
									   where display = 1", $db);
	        
		while($row = mysql_fetch_array($results))
		{
			$id = $row['id'];
			$name = $row['name'];
			
			if($_GET['field'] == $id)
			{
				get_restapi_field_table_contents($id,
											  	 $name,
											  	 true);
			}
			else
			{
				get_restapi_field_table_contents($id,
											  	 $name,
											  	 false);
			}
		}
	}
}
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
				</table>
				</td>
				<td width="65%" style="vertical-align:top;">
				<table cellpadding="0" cellspacing="10" width="100%">
<?php 

try
{
	if(isset($_GET['field']) && strlen($_GET['field']) > 0)
	{
		if(isset($_POST['status']) && $_POST['status'] == 'block') 
		{
			$api_id = $_POST['ApiID'];
			$query = sprintf("INSERT INTO user_disable_api (uid, restapi_id) VALUES( '%s', '%s')", $user_id, $api_id);
			
			if(!mysql_query($query))
            {
	            echo '<div class="fberrorbox">  
	    				Failed to block the selected API!  
						</div><br />';
	            exit();
            } 
            else
            {
            	$Count_Num = '1';
            	echo '<div class="fbbluebox">  
		    			You have successfully blocked the selected API!  
						</div><br />';
            }
		}
		else if(isset($_POST['status']) && $_POST['status'] == 'unblock')
		{
			$api_id = $_POST['ApiID'];
			
			$commit = "commit";
			mysql_query("begin", $db);
			
			$query = "DELETE FROM user_disable_api_app
						  	 WHERE user_disable_api_app.uid = $user_id AND
						  	 	   user_disable_api_app.restapi_id = $api_id;";
			if(!mysql_query($query, $db))
			{
				$commit = "rollback";
				$querylog .= "error in query: " . $query . " : " . mysql_error($db) . "<br /><br />";
			}
			
			$query = "DELETE FROM user_disable_api where uid = $user_id AND 
												   restapi_id = $api_id;";
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
    			Failed to unblock the selected API!  
				</div><br />';
		    }
		    else
		    {
			    echo '<div class="fbbluebox">  
	    			You have successfully unblocked the selected API!  
					</div><br />';
		    }
		    
		    
			/*$api_id = $_POST['ApiID'];
			$query = sprintf("DELETE FROM user_disable_api where uid = " . $user_id . " AND restapi_id = $api_id");
			
			if(!mysql_query($query))
            {
	             echo '<div class="fberrorbox">  
	    				Failed to unblock the selected API!  
						</div><br />';
	            exit();
            } 
			else
            {
            	$Count_Num = '0';
            	echo '<div class="fbbluebox">  
		    			You have successfully unblocked the selected API!  
						</div><br />';
            }*/
		}
			
		
		$field = $_GET['field'];
		$api_results = mysql_query("SELECT id, 
							   		   	   name, 
							   		       facebook_description,
							   		       Supported,
							   		       (SELECT Count(*) as Count_Num from user_disable_api
						   		        	where user_disable_api.uid = " . $user_id . " AND user_disable_api.restapi_id = restapi.id) AS blocked
							   		       from restapi
							   		       where display=1 AND restapi_field_id = $field", $db);
		
		$div_counter = 0;
		while($api_row = mysql_fetch_array($api_results))
		{
			$api_id = $api_row['id'];
			$api_name = $api_row['name'];
			$api_facebook_description = $api_row['facebook_description'];
			$api_Supported = $api_row['Supported'];
			$blocked = $api_row['blocked'];
	
			get_restapi_table_contents($user_id,
									   $api_id,
									   $api_name,
									   $api_facebook_description,
									   $api_Supported,
									   $field,
									   $blocked,
									   $div_counter);
			$div_counter++;
		}
	}
}
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
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
<?php 

	function get_restapi_field_table_contents($id, 
									   		  $name,
									   		  $select)
	{
		GLOBAL $facebook_canvas_page_url;
		
		$color = '#3b5998';
		
		if($select)
		{
			$color = '#dd3c10';
		}
		
		echo 
		'<tr>
		<td style="background-color: '.$color.';text-align: center;line-height: 30px;">
		<a style="color: #ffffff;font-weight: bold;font-family: Verdana, Arial;font-size: 8pt;" 
			href="'.$facebook_canvas_page_url.'set_policy.php?field=' . $id . '">' . $name . '</a>
		</td>
		</tr>';
	}
	
	function get_restapi_table_contents($user_id,
										$api_id,
									    $api_name,
									    $api_facebook_description,
									    $api_Supported,
									    $field,
									    $blocked,
									    $div_counter)
	{
		$exclude_list_html = '';
		
		$block_html = '';
		
		if($blocked > 0)
		{
			$block_html = 
			'<form style="display: inline;" name="set_policy_form'.$api_id.'" id="set_policy_form'.$api_id.'" 
				action="set_policy.php?field=' . $field . '" method="post">
			 <INPUT type="hidden" name="ApiID" VALUE="'.$api_id.'">
			 <INPUT type="hidden" name="status" VALUE="unblock">
			 <a style="color: #dd3c10;font-weight: bold;text-decoration: underline;"
			 	href="#" onclick="document.getElementById('."'set_policy_form".$api_id."'".').submit();">unblock this API</a>
			 </form>';
			
			$exclude_list_html =
			'<tr>
				<td colspan="2" style="padding-top: 5px;">
				
					NOT apply to
					
					<font style="padding-right: 10px;">
					Applications (<a href="set_policy_not_app.php?api_id='.$api_id.'">
								  Edit
								  </a> 
					| <a href="#" onclick="do_ajax_show_details('."'ip_infor_Div".$div_counter."'".',6,'."'$user_id'".','."'$api_id'".','."'loading_img".$div_counter."'".');">
					  Details
					  </a>)
					</font>
					<img style="display:none;" id="loading_img'.$div_counter.'" src="'.$source_server_url.'image/ajax-loader.gif" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<div style="background-color: #eceff6;" id="ip_infor_Div'.$div_counter.'"></div>
				</td>
			</tr>';
		}
		else
		{
			$block_html = 
			'<form style="display: inline;" name="set_policy_form'.$api_id.'" id="set_policy_form'.$api_id.'" 
				action="set_policy.php?field=' . $field . '" method="post">
			 <INPUT type="hidden" name="ApiID" VALUE="'.$api_id.'">
			 <INPUT type="hidden" name="status" VALUE="block">
			 <a style="font-weight: bold;text-decoration: underline;"
			 	href="#" onclick="document.getElementById('."'set_policy_form".$api_id."'".').submit();">block this API</a>
			 </form>';
		}
		
		echo 
		'
		<tr>
			<td width="70%" style="font-weight: bold;font-family: Verdana, Arial;font-size: 9pt;">
			' . $api_name . '
			</td>
			<td width="30%" style="text-align: right;">
			'.$block_html.'
			</td>
		</tr>
		<tr>
			<td colspan="2">' . $api_facebook_description . '</td>
		</tr>
		'.$exclude_list_html.'
		<tr>
			<td colspan="2" style="border-bottom: #CCCCCC 1px solid;"></td>
		</tr>';
	}

?>
	<tr>
		<td height="20px"></td>
	</tr>
<script type="text/javascript">
<!--
function do_ajax_show_details(div,val,user_id,api_id,img) {

	document.getElementById(img).setStyle('display', 'inline');
	var ajax = new Ajax();
	ajax.responseType = Ajax.FBML;
	ajax.ondone = function(data) 
				  {
					document.getElementById(img).setStyle('display', 'none');
				  	document.getElementById(div).setInnerFBML(data);
				  }

	var params={"action":'select',"option":val,"searchwords":'none',"api_id":api_id,"user_id":user_id};
	ajax.post('<?=$set_policy_transform_callbackurl?>?t='+val,params); 
	} 
//-->
</script>
	</table>
	</td>
</tr>
</table>
</html>

<?php 
/*
<font style="padding-left: 10px; padding-right: 10px;"> 
					Friends (<a href="set_policy_not_fri.php?api_id='.$api_id.'">
							 Edit
							 </a> 
					| <a href="#" onclick="do_ajax_show_details('."'ip_infor_Div".$div_counter."'".',5,'."'$user_id'".','."'$api_id'".','."'loading_img".$div_counter."'".');">
					  Details
					  </a>)
					</font> 
 */
?>