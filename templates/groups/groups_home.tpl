[<if isset($admin)>]
 You are a groups admin. <a href="[<$I2_ROOT>]groups/admin">Add, modify, or delete groups</a><br /><br />
[</if>]
[<if isset($groups)>]
 You are currently a member of the following groups:<br />
 <ul>
 [<foreach from=$groups item=grp>]
  <li><a href="[<$I2_ROOT>]groups/pane/[<$grp->gid>]">[<$grp->name>]</a> ([<$grp->description>])</li>
 [</foreach>]
 </ul>
[<else>]
 You are not a member of any groups.<br />
[</if>]
[<if isset($group_admin)>]
 You are currently an admin in the following groups:<br />
 [<foreach from=$group_admin item=grp>]
   <a href="[<$I2_ROOT>]groups/pane/[<$grp.gid>]">[<$grp.name>]</a><br />
 [</foreach>]
[</if>]
