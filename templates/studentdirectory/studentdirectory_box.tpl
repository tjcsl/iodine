<form action="[<$I2_ROOT>]StudentDirectory/search/" method="get" class="directory">
<table width="100%">
<tr>
<td align="center">
<script language="javascript" type="text/javascript" src="[<$I2_ROOT>]www/js/ajax.js"></script>
<script language="javascript" type="text/javascript">
	var xmlhttp;
	function showResult(str) {
		if (str.length<3)
		{
			document.getElementById("livesearch").innerHTML="";
			document.getElementById("livesearch").style.border="0px";
			return;
		}
		xmlhttp=createRequestObject()
		if (xmlhttp==null)
		{
			alert("Your browser does not support XML HTTP Request");
			return;
		}
		var url="[<$I2_ROOT>]suggest/searchsuggest/"+str;
		xmlhttp.onreadystatechange=stateChanged;
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);
	}
	function stateChanged()
	{
		if (xmlhttp.readyState==4)
		{
			var response = xmlhttp.responseText;
			if(response.indexOf("html")==-1 && response.length>4) {
				document.getElementById("livesearch").innerHTML=response;
				document.getElementById("livesearch").style.border="1px solid #A5ACB2";
			}
		}
	}
</script>
<!-- Add onkeyup="showResult(this.value)" for suggestions -->
<input name="q" type="text" class="directory-field" id="studentdirectory_query"/>
<div id="livesearch" style="margin:0px; width:194px; align=left;"></div>
</td><td align="center">
<input type="submit" value="Search" class="directory-button" />
</td>
</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
	if(navigator.userAgent.indexOf('Safari') != -1) {
		var s = document.getElementById('studentdirectory_query')
		s.setAttribute("type", "search");
		s.setAttribute("results", "5");
		s.setAttribute("placeholder", "Search the Directory");
		s.setAttribute("autosave", "iodine-studentdirectory-search");
	}
	document.getElementById("studentdirectory_query").setAttribute("autocomplete","off");
</script>
<span style="font-style: italic;"><a href="[<$I2_ROOT>]StudentDirectory/search/">Help</a> | <a href="[<$I2_ROOT>]studentdirectory/info/[<$I2_UID>]">Your info</a></span>
