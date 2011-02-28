Roster for <b>[<$activity->name|escape:"html">]</b><br />
Room: [<$activity->block_rooms_comma>]<br />
Date: <b>[<$activity->block->date|date_format:"%A, %B %e %Y">]</b> Block <b>[<$activity->block->block>]</b><br />
Sponsor(s): [<$activity->block_sponsors_comma_short>]<br />
[<assign var='inthisactivity' value=0>]
[<foreach from=$activity->members_obj item="member">]
	[<if $member->uid==$I2_USER->uid>]
		[<assign var='inthisactivity' value=1>]
	[</if>]
[</foreach>]
[<if $activity->comment|escape:"html">]
<br />
<em>[<$activity->comment>]</em>
<br />
[</if>]
[<if !$inthisactivity>]
<br />
<form name="activity_select_form" action="[<$I2_ROOT>]eighth/vcp_schedule/change/uid/[<$I2_USER->uid>]/bids/[<$activity->bid>]" method="post">
<input type="hidden" name="aid" id="aid_box" value="[<$activity->aid>]" />
<input type="submit" name="submit" value="Change to this activity" />
</form>
[</if>]
<br />
[<if $activity->member_count > 0>]
<table cellspacing="0" cellpadding="0">
	<tr>
		<th>Name</th>
		<th>Grade</th>
		<th>E-mail</th>
[<* Insert more personal information *>]
	</tr>
[<foreach from=$activity->members_obj item="member">]
	<tr class="[<cycle values="c1,c2">]">
		<td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/info/[<$member->uid>]">[<$member->name_comma>]</a></td>
		<td class="directory-table">[<$member->grade>]</td>
		<td class="directory-table">
			[<if count($member->mail)>]
				[<if count($member->mail) == 1>]
					[<assign var="mail" value=$member->mail>]
				[<else>]
					[<assign var="mail" value=$member->mail.0>]
				[</if>]
				[<assign var="mail" value=$mail|replace:'?':''>]
				[<mailto address=$mail encode="hex">]
			[<else>]&nbsp;[</if>]
		</td>
	</tr>
[</foreach>]
</table><br />
Total: [<$activity->member_count>] (NOTE: this number includes any students that may have elected not to appear on this list)
[<else>]
<b>No students signed up for this block</b>
[</if>]
