<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/add" class="boxform">
<input type="hidden" name="poll_add_form" value="poll" />
Name: <input type="text" name="name" value="" /><br />
Start date/time:<input type="text" name="startdt" value="YYYY-MM-DD HH:MM:SS" /><br />
End date/time:<input type="text" name="enddt" value="YYYY-MM-DD HH:MM:SS" /><br />
<input type="checkbox" name="visible" />Is Visible<br />
Groups: <input type="text" name="groups" value="" /><br />
Introduction:<br />
<textarea rows="2" cols="50" name="intro"></textarea><br />
<input type="submit" value="Create and start adding questions" name="submit" />
</form>
