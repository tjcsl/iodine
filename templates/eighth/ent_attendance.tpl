[<include file="eighth/header.tpl">]
This is block [<$block>] on [<$date>]<br />
[<if isset($lastuid)>]
You have marked <span class="bold">[<$lastname>] ([<$studentid>])</span> absent from <span class="bold">[<$activity->name>]</span>.<br />
<a href="[<$I2_ROOT>]eighth/ent_attendance/unmark_absent/bid/[<$bid>]/uid/[<$lastuid>]">Click here to undo</a>
[</if>]
<form action="[<$I2_ROOT>]eighth/ent_attendance/mark_absent/bid/[<$bid>]" method="post" name="mark_absent_form">
	Student ID: <input type="text" name="uid" /><br />
	<script language="javascript" type="text/javascript">
		document.mark_absent_form.uid.focus();
	</script>
	<input type="submit" value="Mark Absent" />
</form>
