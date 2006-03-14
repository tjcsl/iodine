<a href="[<$I2_ROOT>]groups">Groups Home</a><br />
<br />
Group: <strong>[<$group>]</strong><br />
<br />
[<if count($members) > 0>]
This group has the following members:<br />
<ul>
[<foreach from=$members item=person>]
  <li><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a>
    [<if $person.has_perms>]
    <br />Permissions <a href="[<$I2_ROOT>]groups/grant/[<$person.uid>]/[<$gid>]">[grant new permission]</a>:
    <ul>
    [<foreach from=$person.perms item=perm>]
      <li>[<$perm[0]>] <a href="[<$I2_ROOT>]groups/revoke/[<$person.uid>]/[<$gid>]/[<$perm[0]>]">[revoke]</a></li>
    [</foreach>]
    </ul>
    [</if>]
  </li>
[</foreach>]
</ul>
[<else>]
This group has no members.<br />
[</if>]
[<if $admin == "all" || $admin == "master">]
<br />
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
[</if>]
