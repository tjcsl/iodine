[<if isset($newsitem)>]
 Are you sure you want to delete this post?<br /><br />
 Title: [<$newsitem->title>]<br /><br />
 Groups: [<$newsitem->groupsstring>]<br /><br />
 Text:<br />
 [<$newsitem->text>]
 <form action="[<$I2_SELF>]" method="POST">
 <input type="hidden" name="delete_confirm" value="1" />
 <br /><br /><input type="submit" value="Delete" name="submit" />
 </form>
[<else>]
 Your post has been deleted.<br />
 <a href="[<$I2_ROOT>]news">Back to News</a><br />
[</if>]
