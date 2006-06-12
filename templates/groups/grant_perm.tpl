[<if not (isset($perm_granted) and $perm_granted ) >]
<form method="POST" action="[<$I2_SELF>]">
<p>For group [<$group->name>], grant permission to the [<if isset($group_perm)>]group[<else>]user[</if>][<$subject->name>]:</p>
<p><select name="groups_grant_permission">
 [<foreach from=$perms item=perm>]
 <option value="[<$perm.pid>]">[<$perm.name>] ([<$perm.description>])</option>
 [</foreach>]
</select></p>
<p><input type="submit" name="groups_grant_submit" value="Grant" /></p>
</form>
[</if>]
