[<include file="eighth/sponsor_selection.tpl">]
<script type="text/javascript">
function sponsorSelect(sid) {
        document.getElementById("fname").value = sponsorData[sid][0];
        document.getElementById("lname").value = sponsorData[sid][1];
        document.getElementById("pickup").value = sponsorData[sid][2];
	document.getElementById("userid").value = sponsorData[sid][3];
        document.getElementById("submit").value = "Change " + sponsorData[sid][1];
	document.getElementById("sid").value = sid;
	document.getElementById("remove").style.visibility = "visible";
	document.getElementById("remove").value = "Remove " + sponsorData[sid][1];
}
var sponsorData = Array();
[<foreach from=$sponsors item='sponsor'>]
	sponsorData[[<$sponsor['sid']>]] = Array("[<$sponsor.fname>]", "[<$sponsor.lname>]", "[<$sponsor.pickup>]", "[<$sponsor.userid>]");
[</foreach>]
</script>
<br /><br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/submit" method="post">
	<input type="hidden" id="sid" name="sid" />
	<input type="hidden" id="is_remove" name="is_remove" />
<table>
	<tr><td>First Name:</td>
	  <td><input type="text" name="fname" id="fname" /></td></tr>
	<tr><td>Last Name:</td>
	  <td><input type="text" name="lname" id="lname" /></td></tr>
	<tr><td>Pickup Location:</td>
	  <td><input type="text" name="pickup" id="pickup" /></td></tr>
	<tr><td>Iodine User ID:</td>
	  <td><input type="text" name="userid" id="userid" /></td></tr>
</table>
	<input type="submit" id="submit" value="Add Sponsor" />
	<input type="submit" id="remove" onclick="document.getElementById('is_remove').value = true" style="visibility:hidden;" />
</form>
<br />
