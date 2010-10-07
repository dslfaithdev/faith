var xmlhttp;

function showLiveSearchResult(str)
{
	alert('This is what an alert message looks like.');
if (str.length==0)
  {
  document.getElementById("live_search_div").innerHTML="";
  document.getElementById("live_search_div").style.border="0px";
  return;
  }
xmlhttp=GetXmlHttpObject()
if (xmlhttp==null)
  {
  alert ("Your browser does not support XML HTTP Request");
  return;
  }
var url="select_app_live_search.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
xmlhttp.onreadystatechange=stateChanged ;
xmlhttp.open("GET",url,true);
xmlhttp.send(null);
}

function stateChanged()
{
if (xmlhttp.readyState==4)
  {
  document.getElementById("live_search_div").innerHTML=xmlhttp.responseText;
  document.getElementById("live_search_div").style.border="1px solid #A5ACB2";
  }
}

function GetXmlHttpObject()
{
if (window.XMLHttpRequest)
  {
  // code for IE7+, Firefox, Chrome, Opera, Safari
  return new XMLHttpRequest();
  }
if (window.ActiveXObject)
  {
  // code for IE6, IE5
  return new ActiveXObject("Microsoft.XMLHTTP");
  }
return null;
}