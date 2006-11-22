<script type="text/javascript" src="[<$I2_ROOT>]www/js/news_groups.js"></script>
<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/add" class="boxform">
<input type="hidden" name="poll_add_form" value="poll" />
Name: <input type="text" name="name" value="" /><br />
Start date/time:<input type="text" name="startdt" value="YYYY-MM-DD HH:MM:SS" /><br />
End date/time:<input type="text" name="enddt" value="YYYY-MM-DD HH:MM:SS" /><br />
<input type="checkbox" name="visible" />Is Visible<br />
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
Introduction:<br />
<textarea rows="2" cols="50" name="intro"></textarea><br />
<input type="submit" value="Create and start adding questions" name="submit" />
</form>
