[<include file="eighth/header.tpl">]
<span class="bold">Comments for [<$username>] ([<$uid>]):</span><br />
<form action="[<$I2_ROOT>]eighth/edit/comments/uid/[<$uid>]" method="post">
	<textarea rows="10" cols="80" name="comments">[<$comments>]</textarea><br /><br />
	<input type="submit" value="Modify">
</form>
