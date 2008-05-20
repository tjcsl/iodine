[<include file="eighth/header.tpl">]
<table><tr><td>
<br />
<form method="post" action="[<$I2_ROOT>]eighth/amr_group/modify/gid/[<$group->gid>]">
<input type="text" name="name" size="25" value="[<$group->name|replace:'eighth_':''>]" />
<input type="submit" value="Change group name" />
</form>
<br />
[<if count($group->members) > 0>]
<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
	<tr>
		<th style="padding: 0px 5px; text-align: left;">Name</th>
		<th style="padding: 0px 5px; text-align: left;">Student ID</th>
		<th style="padding: 0px 5px; text-align: left;">Email</th>
		<th style="padding: 0px 5px; text-align: left;">Grade</th>
		<td><a href="[<$I2_ROOT>]eighth/amr_group/remove_all/gid/[<$group->gid>]">Remove all</a></td>
	</tr>
	<tr><td colspan="4">&nbsp;</td></tr>
[<foreach from=$membersorted item='member'>]
	<tr>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$member->uid>]">[<$member->name_comma>]</a></td>
		<td style="padding: 0px 5px; text-align: center;">[<$member->studentid>]</td>
		<td style="padding: 0px 5px; text-align: center;">
			[<if count($member->mail)>]
				[<if count($member->mail) == 1>]
					[<assign var="mail" value=$member->mail>]
				[<else>]
					[<assign var="mail" value=$member->mail.0>]
				[</if>]
				[<mailto address=$mail encode="hex">]
			[<else>]&nbsp;[</if>]
		</td>
		<td style="padding: 0px 5px; text-align: center;">[<$member->grade>]</td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/amr_group/remove_member/gid/[<$group->gid>]/uid/[<$member->uid>]">Remove</a></td>
	</tr>
[</foreach>]
	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/amr_group/remove_all/gid/[<$group->gid>]">Remove all</a></td></tr>
</table><br />
[</if>]
</td><td style="vertical-align: top;">
<a href="[<$I2_ROOT>]eighth/amr_group/remove/gid/[<$group->gid>]">Remove Group</a><br />
[<if isSet($lastaction)>]<b>[<$lastaction>]</b><br />[</if>]
	<fieldset style="width: 220px;">
	[<if isSet($info)>]
		[<include file="search/search_results_pane.tpl">]
	[<else>]
		[<include file="search/search_pane.tpl">]
	[</if>]
	<br />
	<form method="post" action="[<$I2_ROOT>]eighth/amr_group/add_members/gid/[<$group->gid>]" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
		Text file: <input type="file" name="textfile" size="20px" /><br />
		<input type="submit" value="Add multiple" />
	</form>
	</fieldset>
</td></tr>
</table>
<script language="javascript" type="text/javascript">
	<!--
		document.theform.uid.focus();
	// -->
</script>
