[<include file="eighth/header.tpl">]
<div style="font-family: courier;">
Activity:&nbsp;&nbsp;&nbsp;[<$activity->name>][<if $activity->restricted >] (R)[</if>]<br />
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$activity->block->date|date_format>], [<$activity->block->block>] block<br />
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$activity->block_rooms_comma>]<br />
Sponsor(s):&nbsp;[<$activity->block_sponsors_comma>]<br />
</div>
<br />
[<if $activity->advertisement>]Special Info: [<$activity->advertisement>]<br />[</if>]

[<foreach from=$activity->members_obj item=member>]
________ [<$member->name_comma>] ([<$member->tjhsstStudentId>]) - [<$member->grade>]<br />
[</foreach>]
<br />
[<$activity->member_count>] student[<if $activity->member_count != 1>]s[</if>] signed up <br />
<div style="float: right; margin: 10px;">
	<a href="[<$I2_ROOT>]eighth/vp_roster/format/aid/[<$activity->aid>]/bid/[<$activity->bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" /></a>
</div>
