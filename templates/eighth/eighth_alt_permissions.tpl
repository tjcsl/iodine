[<include file="eighth/eighth_header.tpl">]
Restricted Members of [<$activity->name>]<br />
<table cellpadding="0" cellspacing="0" style="border: 0px;">
	<tr>
		<th style="padding: 0px 5px;">Name</th>
		<th style="padding: 0px 5px;">Student ID</th>
		<th style="padding: 0px 5px;">Grade</th>
		<td></td>
	</tr>
[<foreach from=$activity->restricted_members_obj item="member">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td style="padding: 0px 5px;">[<$member->name>]</td>
		<td style="text-align: center; padding: 0px 5px;">[<$member->uid>]</td>
		<td style="text-align: center; padding: 0px 5px;">[<$member->grade>]</td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/alt_permissions/remove_member/aid/[<$activity->aid>]/uid/[<$member->uid>]">Remove</a></td>
	</tr>
[</foreach>]
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/alt_permissions/remove_all/aid/[<$activity->aid>]">Remove All</a></td>
</table>
<form action="[<$I2_ROOT>]eighth/alt_permissions/add_member/aid/[<$activity->aid>]" method="post">
	<fieldset style="width: 150px;">
	<legend>Member to add</legend>
		First Name: <input type="text" name="fname" /><br />
		Last Name: <input type="text" name="lname" /><br />
		Student ID: <input type="text" name="uid" /><br />
		<input type="submit" value="Add Member" style="margin-top: 10px;" />
	</fieldset>
</form>
<form action="[<$I2_ROOT>]eighth/alt_permissions/add_group/aid/[<$activity->aid>]" method="post">
	Group to add: <select name="gid">
[<foreach from=$groups item="group">]
		<option value="[<$group->gid>]">[<$group->name|replace:"eighth_":"">]</option>
[</foreach>]
	</select><input type="submit" value="Add Group" />
</form>
