[<if isset($admin)>]
 You are a groups admin. <a href="[<$I2_ROOT>]groups/admin">Add, modify, or delete groups</a><br /><br />
[</if>]
[<if isset($groups)>]
 You are currently a member of the following groups:<br />
 [<foreach from=$groups item=val>]
  <a href="[<$I2_ROOT>]groups/group/[<$val>]">[<$val>]</a><br />
 [</foreach>]
[<else>]
 You are not a member of any groups.<br />
[</if>]
[<if isset($group_admin)>]
 You are currently an admin in the following groups:<br />
 [<foreach from=$group_admin item=val>]
   <a href="[<$I2_ROOT>]groups/group/[<$val>]">[<$val>]</a><br />
 [</foreach>]
[</if>]
