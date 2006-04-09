[<include file="eighth/header.tpl">]
<span class="bold">Comments for [<$user->name>] ([<$user->uid>]):</span><br />
<form action="[<$I2_ROOT>]eighth/edit/comments/uid/[<$user->uid>]" method="post">
	<textarea rows="10" cols="80" name="comments">[<$user->comments>]</textarea><br /><br />
	<input type="submit" value="Modify">
</form>
