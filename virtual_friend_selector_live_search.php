
<?php

require_once 'func.php';
require_once 'vars.php';

//get the q parameter from URL
$live_search_str = $_POST['searchwords'];
$uid = $_POST['uid'];
$EventID = $_POST['eid'];
$option = $_POST['option'];

$live_search_str = strtolower($live_search_str); 

if($option == 'virtual_friend_selector')
{
$match_return = false;
$hint='<table cellpadding="0" cellspacing="5" width="160px"">';

if (strlen($live_search_str) > 0)
{
	mysqlSetup($db);
	
	$results = mysql_query("SELECT uid_b_name, 
								   add_uid_b
								   from transform_add
								   WHERE add_uid_a = $uid AND
								         LOCATE('$live_search_str', LOWER(uid_b_name)) > 0 AND
								         status = 1
								   LIMIT 10", $db);
	
	
	
	while($row = mysql_fetch_array($results))
	{
		$match_return = true;
		
		$uid_b_name = $row['uid_b_name'];
	  	$add_uid_b = $row['add_uid_b'];
	  	
       	$hint .= 
       	'
       	<tr>
    		<td style="font-family: Verdana, Arial;font-size: 8pt;line-height: 10px;border-bottom: #CCCCCC 1px solid; padding-bottom: 10px;">
    		<a href="#" onClick="set_friend_selector_value('."'friend_selector_txt$EventID',"."'$uid_b_name'".');return false;">'.$uid_b_name.'</a>
       		</td>
       	</tr>';
	}
}

if (!$match_return)
{
  	$hint='<tr><td><h5>&nbsp;&nbsp;&nbsp;no match found</h5></td></tr></table>';
}
else
{
	$hint=$hint . '</table>';
}

//output the response
echo $hint;
}
?>