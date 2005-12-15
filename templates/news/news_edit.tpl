[<if isset($edited)>]
Yours news post has been changed.<br />
<a href="[<$I2_ROOT>]news">Back to news</a><br />
[</if>]
<form action="[<$I2_SELF>]" method="POST">
 <input type="hidden" name="edit_form" value="1" />
 Title: <input type="text" name="edit_title" value="[<$newsitem->title>]" size="30" /><br />
 Groups: <input type="text" name="edit_groups" value="[<$newsitem->groupsstring>]" size="30" /><br />
 Text: <br />
 <textarea name="edit_text" cols="80" rows="15">[<$newsitem->text|escape:"html">]</textarea><br />
 <input type="submit" value="Submit" name="submit" />
</form>
