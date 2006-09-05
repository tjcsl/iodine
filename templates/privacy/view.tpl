<b><a href="[<$I2_ROOT>]StudentDirectory/info/[<$user->uid>]">[<$user->fullname_comma>]</a></b><br /><br />

<form action="[<$I2_ROOT>]privacy" method="POST">
<table>
<tr><th>&nbsp;</th><th>User</th><th>Parental</th></tr>
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
</table>
<input type="hidden" name="update" value="1"/>
<input type="hidden" name="uid" value="[<$user->uid>]"/>
<input type="Submit" value="Change"/>
</form>
