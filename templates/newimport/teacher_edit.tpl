<form action="[<$I2_ROOT>]newimport/[<$method>]" method="POST">
<input type="hidden" name="id" value="[<$iodineUid>]" />
<table>
<tbody>
 <tr><td>Username</td><td><input type="text" name="data[iodineUid]" value="[<$iodineUid>]" /></td></tr>
 <tr><td>UID Number</td><td><input type="text" name="data[iodineUidNumber]" value="[<$iodineUidNumber>]" /></td></tr>
 <tr><td>First Name</td><td><input type="text" name="data[givenName]" value="[<$givenName>]" /></td></tr>
 <tr><td>Last Name</td><td><input type="text" name="data[sn]" value="[<$sn>]" /></td></tr>
</tbody>
</table>
<input type="submit" value="Submit" />
</form>

<br /><br />

[<if $showdelete>]
<form action="[<$I2_ROOT>]newimport/teacher_delete" method="POST">
<input type="hidden" name="uid" value="[<$iodineUid>]" />
<input type="submit" value="Delete this teacher" />
</form>
[</if>]
