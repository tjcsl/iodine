<a href="[<$I2_ROOT>]groups">Groups Home</a><br />
<br />
Group: <strong>[<$group|escape>]</strong><br />
<br />
[<if $can_add>]
 <h3>Adding Users</h3>
 To add a person, enter their user id here:<br />
 <form method="post" action="[<$I2_ROOT>]groups/pane/[<$group|escape>]" class="boxform">
 <input type="hidden" name="group_form" value="add" />
 <input type="text" name="uid" value="" /><input type="submit" value="Add" name="submit" /><br /><br />
 </form>
[</if>]
[<if $can_remove>]
 <h3>Removing Users</h3>
 To remove a person, enter their user id here:<br />
 <form method="post" action="[<$I2_ROOT>]groups/pane/[<$group|escape>]" class="boxform">
 <input type="hidden" name="group_form" value="remove" />
 <input type="text" name="uid" value="" /><input type="submit" value="Remove" name="submit" /><br /><br />
 </form>
[</if>]
[<if $can_set_perms>]
 <h3>Set Permissions</h3>
 <p>
 <form method="post" action="[<$I2_ROOT>]groups/pane/[<$group|escape>]" class="boxform">
 <input type="hidden" name="group_form" value="grant" />
 To grant a permission to a user, enter their user id here:<br />
 <input type="text" name="uid" value="" /><br />
 and select a permission to grant here:<br />
 <select name="permission">
  [<foreach from=$perms item=perm>]
   <option value="[<$perm->pid>]">[<$perm->name>] ([<$perm->description>])</option>
  [</foreach>]
 </select><br />
 <input type="submit" value="Grant" name="submit" /><br /><br />
 </form>
 </p>
 <p>
 <form method="post" action="[<$I2_ROOT>]groups/pane/[<$group|escape>]" class="boxform">
 <input type="hidden" name="group_form" value="grantgroup" />
 To grant a permission to all members in a group, enter the group name or GID here:<br />
 <input type="text" name="gid" value="" /><br />
 and select a permission to grant here:<br />
 <select name="permission">
  [<foreach from=$perms item=perm>]
   <option value="[<$perm->pid>]">[<$perm->name>] ([<$perm->description>])</option>
  [</foreach>]
 </select><br />
 <input type="submit" value="Grant" name="submit" /><br /><br />
 </form>
 </p>
[</if>]
[<if count($perm_users) gt 0 or count($perm_groups) gt 0>]
<h3>Permissions</h3>
[</if>]
[<if count($perm_users) > 0>]
<p>The following users have permissions in this group:</p>
<table width="100%">
 <thead>
  <th>User</th>
  <th>Permissions</th>
  <th>Grant</th>
 </thead>
 <tbody>
[<foreach from=$perm_users item=subject>]
  <tr class="[<cycle values="c1,c2">]">
   <td><a href="[<$I2_ROOT>]studentdirectory/info/[<$subject.uid>]">[<$subject.name>]</a></td>
   <td>
    <ul>
    [<foreach from=$subject.perms item=perm>]
      <li><acronym title="[<$perm->description>]">[<$perm->name>]</acronym> <a href="[<$I2_ROOT>]groups/revoke/[<$subject.uid>]/[<$gid>]/[<$perm->pid>]">[revoke]</a></li>
    [</foreach>]
    </ul>
   </td>
   <td><a href="[<$I2_ROOT>]groups/grant/[<$subject.uid>]/[<$gid>]">[grant new permission]</a></td>
  </tr>
[</foreach>]
 </tbody>
</table>
[</if>]
[<if count($perm_groups) > 0>]
<p>The following groups have permissions in this group:</p>
<table width="100%">
 <thead>
  <th>Group</th>
  <th>Permissions</th>
  <th>Grant</th>
 </thead>
 <tbody>
[<foreach from=$perm_groups item=subject>]
  <tr class="[<cycle values="c1,c2">]">
   <td><a href="[<$I2_ROOT>]groups/pane/[<$subject.gid>]">[<$subject.name>]</a></td>
   <td>
    <ul>
    [<foreach from=$subject.perms item=perm>]
      <li><acronym title="[<$perm->description>]">[<$perm->name>]</acronym> <a href="[<$I2_ROOT>]groups/revokegroup/[<$subject.gid>]/[<$gid>]/[<$perm->pid>]">[revoke]</a></li>
    [</foreach>]
    </ul>
   </td>
   <td><a href="[<$I2_ROOT>]groups/grantgroup/[<$subject.gid>]/[<$gid>]">[grant new permission]</a></td>
  </tr>
[</foreach>]
 </tbody>
</table>
[</if>]
<h3>Membership</h3>
[<if count($dynamic_member_rules) > 0>]
<p>This group has the following dynamic membership rules:</p>
<table>
 <thead>
  <th>Rule type</th>
  <th>Query</th>
[<if $global_admin>]  <th>Delete</th>[</if>]
 </thead>
 <tbody>
 [<foreach from=$dynamic_member_rules item=member>]
  <tr class="[<cycle values="c1,c2">]">
   <td>[<$member.type>]</td>
   [<if $member.type == 'JOIN'>]
   <td><a href="[<$I2_ROOT>]groups/pane/[<$member.group1->gid>]">[<$member.group1->name>]</a> [<$member.optype>] <a href="[<$I2_ROOT>]groups/pane/[<$member.group2->gid>]">[<$member.group2->name>]</a></td>
   [<else>]
   <td>[<$member.query>]</td>
   [</if>]
[<if $global_admin>]   <td><a href="[<$I2_ROOT>]groups/deldynrule/[<$gid>]">delete</a></td>[</if>][<* This only supports one rule because you only need one, and also because there is no way to distinguish the rules *>]
  </tr>
 [</foreach>]
 </tbody>
</table>
[<else>]
[<if $global_admin>]
<p>Add a dynamic membership rule to this group: <form method="post" action="[<$I2_ROOT>]groups/adddynrule/[<$gid>]"><input type="text" name="rule" /><input type="submit" value="Add" /></form></p>
[</if>]
[</if>]
[<if count($members) > 0>]
<p>This group has the following static members:</p>
<table style="width: 30em;">
 <thead>
  <th>User</th>
  [<if $can_remove>]<th>Remove</th>[</if>]
 </thead>
 <tbody>
[<foreach from=$members item=person>]
  <tr class="[<cycle values="c1,c2">]">
   <td style="text-align: center;"><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a></td>
   [<if $can_remove>]
    <td style="text-align: center;"><a href="[<$I2_ROOT>]groups/remove/[<$person.uid>]/[<$gid>]">[remove this person from this group]</a></td>
   [</if>]
  </tr>
[</foreach>]
 </tbody>
</table>
[<else>]
<p>This group has no static members.</p>
[</if>]
[<if count($dynamic_members) > 0>]
<p>This group has the following dynamic members:</p>
<table style="width: 15em;">
 <thead>
  <th>User</th>
 </thead>
 <tbody>
[<foreach from=$dynamic_members item=person>]
  <tr class="[<cycle values="c1,c2">]">
   <td style="text-align: center;"><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a></td>
  </tr>
[</foreach>]
 </tbody>
</table>
[<else>]
<p>This group has no dynamic members.</p>
[</if>]
<h3>Special Properties</h3>
<p>This group has the following filecenter bookmarks:</p>
<table style="width: 30em;">
 <thead>
  <th>Name</th>
  <th>Directory</th>
  [<if $can_set_perms>]
   <th>Remove</th>
  [</if>]
 </thead>
 <tbody>
[<foreach from=$bookmarks item=bookmark>]
  <tr class="[<cycle values="c1,c2">]">
   <td style="text-align: center;">[<$bookmark.name>]</td>
   <td style="text-align: center;"><a href="[<$I2_ROOT>]filecenter/[<$bookmark.path>]">[<$bookmark.path>]</a></td>
   [<if $can_set_perms>]
    <td style="text-align: center;"><form method="get" action="[<$I2_ROOT>]groups/bookmarks/[<$gid>]"><input type="hidden" name="action" value="remove" /><input type="hidden" name="name" value="[<$bookmark.name>]" /><input type="hidden" name="path" value="[<$bookmark.path>]" /><input type="submit" value="Remove" /></form></td>
   [</if>]
  </tr>
[</foreach>]
 </tbody>
</table>
[<if $can_set_perms>]
<form method="get" action="[<$I2_ROOT>]groups/bookmarks/[<$gid>]"><input type="hidden" name="action" value="add" />Name: <input type="text" name="name"/> Path: <input type="text" name="path" /><input type="submit" value="Add" /></form>
[</if>]
