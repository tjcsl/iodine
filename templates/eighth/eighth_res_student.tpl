[<include file="eighth/eighth_header.tpl">]
[<if isset($user) >]
	Choose a student to reschedule:<br />
	<ul>
		<li><a href="[<$I2_ROOT>]eighth/res_student/reschedule/bid/[<$block->bid>]/aid/[<$activity->aid>]/uid/[<$user->uid>]">[<$user->fullname_comma>] ([<$user->uid>], grade [<$user->grade>])</a></li>
	</ul>
[</if>]
<form action="[<$I2_ROOT>]eighth/res_student/user/bid/[<$block->bid>]/aid/[<$activity->aid>]" method="post">
	Student ID: <input type="text" name="uid"><br />
	<input type="submit" value="Next">
</form>
