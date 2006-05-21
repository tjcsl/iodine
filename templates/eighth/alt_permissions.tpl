[<include file="eighth/header.tpl">]
Restricted Members of [<$activity->name>]<br />
<table><tr><td>
<table cellpadding="0" cellspacing="0" style="border: 0px;">
	<tr>
		<th style="padding: 0px 5px;">Name</th>
		<th style="padding: 0px 5px;">Student ID</th>
		<th style="padding: 0px 5px;">Grade</th>
		<td></td>
	</tr>
[<foreach from=$activity->restricted_members_obj_sorted item="member">]
	<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">]">
		<td style="padding: 0px 5px;">[<$member->name_comma>]</td>
		<td style="text-align: center; padding: 0px 5px;">[<$member->tjhsstStudentId>]</td>
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
</td><td style="vertical-align: top;">
	<fieldset style="width: 250px;">
	[<if isSet($search_destination)>]
		[<include file="search/search_pane.tpl">]
	[<else>]
		[<include file="search/search_results_pane.tpl">]
	[</if>]
	</fieldset>
<form action="[<$I2_ROOT>]eighth/alt_permissions/add_group/aid/[<$activity->aid>]" method="post">
	Group to add: <select name="gid">
[<foreach from=$groups item="group">]
		<option value="[<$group->gid>]">[<$group->name|replace:"eighth_":"">]</option>
[</foreach>]
	</select><br /><input type="submit" value="Add Group" />
</form>
</td></tr></table>
