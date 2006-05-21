[<if isSet($deleted)>]
	The answer has been deleted.<br />
	<a href="[<$I2_ROOT>]polls/edit/[<$pid>]/[<$qid>]">Return</a>
[<else>]
	Are you sure you want to delete this answer? <br />
	<form action="[<$I2_ROOT>]polls/delete/[<$pid>]/[<$qid>]/[<$aid>]" method="POST">
		<input type="hidden" name="polls_delete_form" value="delete_answer"/>
		<input type="submit" value="I'm sure"/>
	</form>
[</if>]
