<a href="[<$I2_ROOT>]groups">Groups Home</a> - <a href="[<$I2_ROOT>]groups/admin">Groups Admin</a><br />
<br />
Editing Permission: [<$perm->name>] ([<$perm->desc>])<br />
<br />
To delete this permission, click here:
<form method="post" action="[<$I2_ROOT>]groups/perm/[<$perm->pid>]" class="boxform">
<input type="hidden" name="group_perm_form" value="delete">
<input type="submit" value="Delete" name="submit" />
</form><br />
To rename this permission, enter a new name here:<br />
<form method="post" action="[<$I2_ROOT>]groups/perm/[<$perm->pid>]" class="boxform">
<input type="hidden" name="group_perm_form" value="set_name" />
<input type="text" name="name" value="[<$perm->name>]" /><input type="submit" value="Rename" name="submit" />
</form><br />
To give this permission a new description, enter the description here:<br />
<form method="post" action="[<$I2_ROOT>]groups/perm/[<$perm->pid>]" class="boxform">
<input type="hidden" name="group_perm_form" value="set_desc" />
<input type="text" name="desc" value="[<$perm->desc>]" style="width: 75%;" /><input type="submit" value="Rename" name="submit" />
</form><br />
