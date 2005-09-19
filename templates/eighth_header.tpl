<script language="javascript" type="text/javascript">
function popup ()
{
var newWin=window.open('','tempwin','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,width=650,height=450,resizable=0');
newWin.focus();
}
</script>

<table style="border: 0px; padding: 0px; margin: 0px">
<tr>
<td>
<a href="[<$I2_ROOT>]eighth"><img src="[<$I2_ROOT>]www/eighth.gif" style="border: 0; width: 300; height: 80"></a>
</td>
<td style="width: 10"></td>
<td style="valign: top">
<table style="border: 0px; padding: 0px; margin: 0px; width: 100%">
<tr>
<td>
<b>[<$smarty.now|date_format:"%B %e, %Y, %l:%M %p">]</b>
</td>
<td style="text-align: right">
<a href="help.phtml?page=/eighth/index.phtml" onclick="popup()" target="tempwin">Help</a>&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
<form action="[<$I2_ROOT>]eighth/vcp_schedule" method="post" name="scheduleform">
<input type="hidden" name="op" value="search">
<table style="border: 0px; padding: 0px; margin: 0px">
<tr>
<td style="width: 80">First name:</TD>
<td style="width: 120"><input type="text" name="firstname" style="width: 115px;"></td>
<td style="width: 80">Student ID:</TD>
<td style="width: 120"><input type="text" name="studentid" style="width: 115px;"></td>
</tr> 
<tr>
<td style="width: 80">Last name:</td>
<td style="width: 120"><input type="text" name="lastname" style="width: 115px;" ></td>
<td style="width: 80">&nbsp;</td>
<td style="width: 120"><input type="submit" value="View Schedule" style="width: 115px;" tabindex="4"></td>
</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
document.scheduleform.studentid.focus();
</script>
</td>
</tr>
</table>

