[<if isset($admin)>]
 You are a groups admin. <a href="[<$I2_ROOT>]groups/admin">Add, modify, or delete groups and permissions</a><br /><br />
[</if>]
[<if isset($prefixes)>]
 [<foreach from=$prefixes item=prefix>]
  He/she is an admin for all groups beginning with "[<$prefix>]_". <a href="[<$I2_ROOT>]groups/padmin/[<$prefix>]">Add, modify, or delete [<$prefix>]_ groups</a><br /><br />
 [</foreach>]
[</if>]
[<if isset($groups) && count($groups)>0>]
 He/she is currently a member of the following groups:<br />
 <ul>
 [<foreach from=$groups item=grp>]
  <li><a href="[<$I2_ROOT>]groups/pane/[<$grp->gid>]">[<$grp->name>]</a> [<if $grp->has_permission($I2_USER,Permission::getPermission('GROUP_JOIN'))>]<em>- <a href="[<$I2_ROOT>]groups/sleave/[<$grp->gid>]">[Leave this group]</a> -</em> [</if>]([<$grp->description>])</li>
 [</foreach>]
 </ul>
[<else>]
 He/she is not a member of any groups.<br />
[</if>]
[<if isset($group_admin) && count($group_admin)>0>]
 He/she is currently an admin in the following groups:<br />
 <ul>
 [<foreach from=$group_admin item=grp>]
   <li><a href="[<$I2_ROOT>]groups/pane/[<$grp->gid>]">[<$grp->name>]</a></li>
 [</foreach>]
 </ul>
[</if>]
[<if isset($group_join) && count($group_join)>0>]
 He/she is not a member of the following groups, but may join them:<br />
 <ul>
 [<foreach from=$group_join item=grp>]
  <li>[<$grp->name>] ([<$grp->description>])</li>
 [</foreach>]
 </ul>
[</if>]
