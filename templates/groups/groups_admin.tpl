<a href="[<$I2_ROOT>]groups">Groups Home</a><br />
<br />
The current existant groups are:<br />
[<foreach from=$groups item=grp>]
 [<if $grp->has_member()>]
  <a href="[<$I2_ROOT>]groups/pane/[<$grp->gid>]">
 [</if>]
 [<$grp->name>]</a><br />
 [<if $grp->has_member()>]
  </a>
 [</if>]
[</foreach>]
<br />
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

