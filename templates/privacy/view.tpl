<b><a href="[<$I2_ROOT>]StudentDirectory/info/[<$user->uid>]">[<$user->fullname_comma>]</a></b><br /><br />

<script type="text/javascript">
var parent_boxes = new Array("perm_showaddress", "perm_showphone", "perm_showbdate", "perm_showpictures", "perm_showschedule", "perm_showeighth", "perm_showlocker");
var self_boxes = new Array("perm_showaddressself", "perm_showphoneself", "perm_showbdayself", "perm_showpictureself", "perm_showscheduleself", "perm_showeighthself", "perm_showlockerself");

function toggle_parent(currstate) {
	for (var i in parent_boxes) {
		eval("document.privacy_form."+parent_boxes[i]+".checked = "+currstate);
	}
}

function toggle_self(currstate) {
	for (var i in self_boxes) {
		eval("document.privacy_form."+self_boxes[i]+".checked = "+currstate);
	}
}

</script>

<form action="[<$I2_ROOT>]privacy" method="POST" name="privacy_form">
<table>
<tr><th>&nbsp;</th><th>User</th><th>Parental</th></tr>
<tr>
	<th>ALL</th>
	<td><input type="checkbox" onclick="toggle_self(this.checked);" /></td>
	<td><input type="checkbox" onclick="toggle_parent(this.checked);" /></td>
</tr>
<tr>
	<th>Address</th>
	<td><input name="perm_showaddressself" type="checkbox" [<if $user->showaddressself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showaddress" type="checkbox" [<if $user->showaddress>]checked="checked"[</if>]/></td>
</tr>
<tr>
	<th>Phone</th>
	<td><input name="perm_showphoneself" type="checkbox" [<if $user->showphoneself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showphone" type="checkbox" [<if $user->showphone>]checked="checked"[</if>]/></td>
</tr>
<tr>
	<th>Birthday</th>
	<td><input name="perm_showbdayself" type="checkbox" [<if $user->showbdayself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showbdate" type="checkbox" [<if $user->showbdate>]checked="checked"[</if>]/></td>
</tr>
<tr>
	<th>Pictures</th>
	<td><input name="perm_showpictureself" type="checkbox" [<if $user->showpictureself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showpictures" type="checkbox" [<if $user->showpictures>]checked="checked"[</if>]/></td>
</tr>
<tr>
	<th>Schedule</th>
	<td><input name="perm_showscheduleself" type="checkbox" [<if $user->showscheduleself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showschedule" type="checkbox" [<if $user->showschedule>]checked="checked"[</if>]/></td>
</tr>
<tr>
	<th>Eighth Period Schedule</th>
	<td><input name="perm_showeighthself" type="checkbox" [<if $user->showeighthself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showeighth" type="checkbox" [<if $user->showeighth>]checked="checked"[</if>]/></td>
</tr>
<tr>
	<th>Locker Number</th>
	<td><input name="perm_showlockerself" type="checkbox" [<if $user->showlockerself>]checked="checked"[</if>]/></td>
	<td><input name="perm_showlocker" type="checkbox" [<if $user->showlocker>]checked="checked"[</if>]/></td>
</tr>
</table>
<input type="hidden" name="update" value="1"/>
<input type="hidden" name="uid" value="[<$user->uid>]"/>
<input type="Submit" value="Change"/>
</form>
