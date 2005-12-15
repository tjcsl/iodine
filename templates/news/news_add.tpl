[<if isset($added)>]
 Your news item has been posted.
 <a href="[<$I2_ROOT>]news">Back to News</a>
[<else>]
 <form action="[<$I2_SELF>]" method="POST">
  <input type="hidden" name="add_form" value="1" />
  Title: <input type="text" name="add_title" size="30" /><br />
  Groups: <input type="text" name="add_groups" size="30" /><br />
  Text: <br />
  <textarea name="add_text" cols="80" rows="15"></textarea><br />
  <input type="submit" value="Submit" name="submit" />
 </form>
[</if>]
