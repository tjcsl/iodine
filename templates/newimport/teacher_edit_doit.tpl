<form action="[<$I2_ROOT>]newimport/teacher_edit_doit" method="POST">
[<foreach from=$warnings item=warning>]
  <strong>Warning: [<$warning>]</strong><br />
[</foreach>]
<br />
<input type="hidden" name="id" value="[<$iodineUid_old>]" />

You are about to make the following changes:<br /><br />
<table>
<tbody>
 <tr><td>Username:</td><td>[<$iodineUid_old>]</td><td>=&gt;</td><td>[<$iodineUid_new>]</td></tr>
 <tr><td>UID Number:</td><td>[<$iodineUidNumber_old>]</td><td>=&gt;</td><td>[<$iodineUidNumber_new>]</td></tr>
 <tr><td>First Name:</td><td>[<$givenName_old>]</td><td>=&gt;</td><td>[<$givenName_new>]</td></tr>
 <tr><td>Last Name:</td><td>[<$sn_old>]</td><td>=&gt;</td><td>[<$sn_new>]</td></tr>
</tbody>
</table>
<br />

<input type="hidden" name="data[iodineUid]" value="[<$iodineUid_new>]" />
<input type="hidden" name="data[iodineUidNumber]" value="[<$iodineUidNumber_new>]" />
<input type="hidden" name="data[givenName]" value="[<$givenName_new>]" />
<input type="hidden" name="data[sn]" value="[<$sn_new>]" />
<input type="submit" name="DOIT" value="Yes, make these changes!" />
</form>
