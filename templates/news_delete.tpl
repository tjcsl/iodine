[<if is_array($news_stories)>]
 Are you sure you want to delete this post?<br /><br />
 Title: [<$news_stories.title>]<br /><br />
 Text:<br />
 [<$news_stories.text>]
 <form action="[<$I2_SELF>]" method="POST">
 <input type="hidden" name="delete_confirm" value="1" />
 <br /><br /><input type="submit" value="Submit" name="submit" />
 </form>
[<else>]
 Your post has been deleted.<br />
 <a href="[<$I2_ROOT>]news">Back to News</a><br />
[</if>]
