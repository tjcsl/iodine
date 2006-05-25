<script type="text/javascript" src="[<$I2_ROOT>]www/js/news_groups.js"></script>
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
  Title: <input type="text" name="add_title" size="30" /><br/>
  <table id="groups_table" cellpadding="0">
   <tr>
    <td>Groups:</td>
    <td>
     <select id="groups" class="groups_list" name="add_groups[]">
      [<foreach from=$groups item=group>]
      	<option value="[<$group->gid>]">[<$group->name>]</option>
      [</foreach>]
     </select>
    </td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td>&nbsp;</td>
    <td><a href="#" onclick="addGroup(); return false">Add another group</a></td>
    <td>&nbsp;</td>
   </tr>
  </table>
  Text: <br />
  <textarea id="news_add_text" name="add_text" rows="15"></textarea><br />
  <input type="submit" value="Submit" name="submit" />
 </form>
[</if>]
