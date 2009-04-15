<form action="[<$I2_ROOT>]StudentDirectory/search/" method="post" class="directory">
<table width="100%">
<tr>
<td align="center">
<input name="studentdirectory_query" type="text" class="directory-field" id="studentdirectory_query" />
</td><td align="center">
<input name="Submit" type="submit" value="Search" class="directory-button" />
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
</script>
<span style="font-style: italic;"><a href="[<$I2_ROOT>]StudentDirectory/search/">Help</a> | <a href="[<$I2_ROOT>]studentdirectory/info/[<$I2_UID>]">Your info</a></span>
