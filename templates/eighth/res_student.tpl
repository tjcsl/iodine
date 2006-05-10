[<include file="eighth/header.tpl">]
[<include file="eighth/include_list_open.tpl">]
[<include file="eighth/activity_selection.tpl">]
[<include file="eighth/include_list_close.tpl">]
[<if isset($user) >]
	Choose a student to reschedule:<br />
	<ul>
		<li><a href="[<$I2_ROOT>]eighth/res_student/reschedule/bid/[<$block->bid>]/aid/[<$act->aid>]/uid/[<$user->uid>]">[<$user->fullname_comma>] ([<$user->tjhsstStudentId>], grade [<$user->grade>])</a></li>
	</ul>
[</if>]
<form action="[<$I2_ROOT>]eighth/res_student/user/bid/[<$block->bid>]/aid/[<$act->aid>]" method="post">
	Student ID: <input type="text" name="studentId" /><br />
	<input type="submit" value="Next" />
</form>
