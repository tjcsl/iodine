[<include file="eighth/header.tpl">]

<script type="text/javascript">
sponsorList = Array();
sponsorSelected = false;
[<foreach from=$sponsors item='sponsor'>]
	sponsorList[[<$sponsor.sid>]] = Array("[<$sponsor.fname>]", "[<$sponsor.lname>]", "[<$sponsor.pickup>]");
[</foreach>]
function sponsorSelect(sid) {
        document.getElementById("fname").value = sponsorList[sid][0];
        document.getElementById("lname").value = sponsorList[sid][1];
        document.getElementById("pickup").value = sponsorList[sid][2];
        document.getElementById("submit").value = "Edit " + sponsorList[sid][1];
	document.getElementById("sid").value = sid;
	if(!sponsorSelected) {
		document.getElementById("remove").style.visibility = "visible";
		sponsorSelected = true;
	}
	document.getElementById("remove").value = "Remove " + sponsorList[sid][1];
}
</script>

<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="sponsor_list" size="10" onchange="sponsorSelect(this.options[this.selectedIndex].value)">
[<foreach from=$sponsors item='sponsor' key='key'>]
	<option value="[<$sponsor.sid>]">[<$sponsor.name_comma>]</option>
[</foreach>]
</select>

[<if isset($add)>]
<br /><br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/submit" method="post">
	<input type="hidden" id="sid" name="sid" />
	<input type="hidden" id="is_remove" name="is_remove" />
	First Name: <input type="text" name="fname" id="fname" /><br />
	Last Name: <input type="text" name="lname" id="lname" /><br />
	Pickup Location: <input type="text" name="pickup" id="pickup" /><br />	
	<input type="submit" id="submit" value="Add Sponsor" />
	<input type="submit" id="remove" onclick="document.getElementById('is_remove').value = true" style="visibility:hidden;" />
</form>
[</if>]
<br />
