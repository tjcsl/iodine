[<if isset($added)>]
 [<if $added>]
  Your news item has been posted.
 [<else>]
  There was an error in posting your news item.
 [</if>]
 <a href="[<$I2_ROOT>]news">Back to News</a>
[<else>]
 <form action="[<$I2_SELF>]" method="POST">
  <input type="hidden" name="add_form" value="1" />
  Title: <input type="text" name="add_title" size="30" /><br />
  Group: <select name="add_groups">
  [<foreach from=$groups item=group>]
  	<option value="[<$group->gid>]">[<$group->name>]</option>
  [</foreach>]
  </select><br />
  Text: <br />
  <textarea name="add_text" cols="80" rows="15"></textarea><br />
  <input type="submit" value="Submit" name="submit" />
 </form>
[</if>]
