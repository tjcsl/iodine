[<if not ( isset($perm_granted) and $perm_granted ) >]
<form method="POST" action="[<$I2_SELF>]">
For group [<$group->name>], grant permission to [<$user->name>]:
<input type="text" name="groups_grant_permission" /> <input type="submit" name="groups_grant_submit" value="Grant" />
</form>
[</if>]
