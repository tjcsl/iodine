<a href="[<$I2_ROOT>]groups">Groups Home</a><br />
<br />
<table width="100%">
<tr>
<td valign=top>
To add a group, enter a new name here:<br />
<form method="post" action="[<$I2_ROOT>]groups/admin/" class="boxform">
<input type="hidden" name="group_admin_form" value="add" />
<input type="text" name="name" value="" /><input type="submit" value="Add" name="submit" /><br />
</form>
To remove a group, enter its name here:<br />
<form method="post" action="[<$I2_ROOT>]groups/admin/" class="boxform">
<input type="hidden" name="group_admin_form" value="remove" />
<input type="text" name="name" value="" /><input type="submit" value="Remove" name="submit" /><br />
</form>
<br />
The current existant groups are:<br />
<ul>
[<foreach from=$groups item=grp>]
  <li><a href="[<$I2_ROOT>]groups/pane/[<$grp->gid>]">[<$grp->name>]</a></li>
[</foreach>]
</ul>
</td>
<td valign=top>
To add a permission, enter a new name here:<br />
<form method="post" action="[<$I2_ROOT>]groups/admin/" class="boxform">
<input type="hidden" name="group_admin_form" value="add_perm" />
<input type="text" name="name" value="" /><input type="submit" value="Add" name="submit" /><br />
</form>
To remove a permission, enter its name here:<br />
<form method="post" action="[<$I2_ROOT>]groups/admin/" class="boxform">
<input type="hidden" name="group_admin_form" value="remove_perm" />
<input type="text" name="name" value="" /><input type="submit" value="Remove" name="submit" /><br />
</form>
<br />
The current existant permissions are:<br />
<ul>
[<foreach from=$perms item=perm>]
  <li><a href="[<$I2_ROOT>]groups/perm/[<$perm->pid>]">[<$perm->name>]</a> ([<$perm->description>])</li>
[</foreach>]
</ul>
</td>
</tr>
</table>
