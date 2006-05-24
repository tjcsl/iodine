[<include file="eighth/header.tpl">]
This is block [<$block>] on [<$date>]<br />
[<if isSet($lastuid)>]
You have marked <span class="bold">[<$lastname>] ([<$studentid>])</span> absent from <span class="bold">[<$activity->name>]</span>.<br />
<a href="[<$I2_ROOT>]eighth/ent_attendance/unmark_absent/bid/[<$bid>]/uid/[<$lastuid>]">Click here to undo</a>
[</if>]
<form action="[<$I2_ROOT>]eighth/ent_attendance/mark_absent/bid/[<$bid>]" method="post">
	Student ID: <input type="text" name="uid" /><br />
	<input type="submit" value="Mark Absent" />
</form>
