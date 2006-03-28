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
[<if count($members) > 0>]
This group has the following members:<br />
<table width="100%">
  <th>User</th>
  <th>Permissions</th>
  <th>Grant</th>
[<foreach from=$members item=person>]
  <tr><td><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a></td>
  <td>
  [<if $person.has_perms>]
    <ul>
    [<foreach from=$person.perms item=perm>]
      <li>[<$perm[0]>] <a href="[<$I2_ROOT>]groups/revoke/[<$person.uid>]/[<$gid>]/[<$perm[0]>]">[revoke]</a></li>
    [</foreach>]
    </ul>
  [<else>]
  User has no special permissions.
  [</if>]
  </td>
  <td><a href="[<$I2_ROOT>]groups/grant/[<$person.uid>]/[<$gid>]">[grant new permission]</a></td>
  </tr>
[</foreach>]
</table>
[<else>]
This group has no members.<br />
[</if>]
