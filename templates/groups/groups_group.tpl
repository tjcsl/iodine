<a href="[<$I2_ROOT>]groups">Groups Home</a><br />
<br />
Group: <strong>[<$group>]</strong><br />
<br />
[<if $admin>]
You may add and remove people from this group.<br />
To add a person, enter their user id here:<br />
<form method="post" action="[<$I2_ROOT>]groups/pane/[<$group>]" class="boxform">
<input type="hidden" name="group_form" value="add" />
<input type="text" name="uid" value="" /><input type="submit" value="Add" name="submit" /><br />
</form>
To remove a person, enter their user id here:<br />
<form method="post" action="[<$I2_ROOT>]groups/pane/[<$group>]" class="boxform">
<input type="hidden" name="group_form" value="remove" />
<input type="text" name="uid" value="" /><input type="submit" value="Remove" name="submit" /><br />
</form>
<br />
[</if>]
[<if count($perm_users) > 0>]
<p>The following users have permissions in this group:</p>
<table width="100%">
 <thead>
  <th>User</th>
  <th>Permissions</th>
  <th>Grant</th>
 </thead>
 <tbody>
[<foreach from=$perm_users item=subject>]
  <tr class="[<cycle values="c1,c2">]">
   <td><a href="[<$I2_ROOT>]studentdirectory/info/[<$subject.uid>]">[<$subject.name>]</a></td>
   <td>
    <ul>
    [<foreach from=$subject.perms item=perm>]
      <li><acronym title="[<$perm.description>]">[<$perm.name>]</acronym> <a href="[<$I2_ROOT>]groups/revoke/[<$subject.uid>]/[<$gid>]/[<$perm.pid>]">[revoke]</a></li>
    [</foreach>]
    </ul>
   </td>
   <td><a href="[<$I2_ROOT>]groups/grant/[<$subject.uid>]/[<$gid>]">[grant new permission]</a></td>
  </tr>
[</foreach>]
 </tbody>
</table>
[</if>]
[<if count($perm_groups) > 0>]
<p>The following groups have permissions in this group:</p>
<table width="100%">
 <thead>
  <th>Group</th>
  <th>Permissions</th>
  <th>Grant</th>
 </thead>
 <tbody>
[<foreach from=$perm_groups item=subject>]
  <tr class="[<cycle values="c1,c2">]">
   <td><a href="[<$I2_ROOT>]groups/pane/[<$subject.gid>]">[<$subject.name>]</a></td>
   <td>
    <ul>
    [<foreach from=$subject.perms item=perm>]
      <li><acronym title="[<$perm.description>]">[<$perm.name>]</acronym> <a href="[<$I2_ROOT>]groups/revokegroup/[<$subject.gid>]/[<$gid>]/[<$perm.pid>]">[revoke]</a></li>
    [</foreach>]
    </ul>
   </td>
   <td><a href="[<$I2_ROOT>]groups/grantgroup/[<$subject.gid>]/[<$gid>]">[grant new permission]</a></td>
  </tr>
[</foreach>]
 </tbody>
</table>
[</if>]
[<if count($members) > 0>]
<p>This group has the following static members:</p>
<table width="100%">
 <thead>
  <th>User</th>
  <th>Remove</th>
 </thead>
 <tbody>
[<foreach from=$members item=person>]
  <tr class="[<cycle values="c1,c2">]">
   <td><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a></td>
   <td><a href="[<$I2_ROOT>]groups/remove/[<$person.uid>]/[<$gid>]">[remove this person from this group]</a></td>
  </tr>
[</foreach>]
 </tbody>
</table>
[</if>]
