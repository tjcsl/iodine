<script type="text/javascript" src="[<$I2_ROOT>]www/js/news_groups.js"></script>
[<if isset($added)>]
 [<if $added>]
  Your news item has been posted.
 [<else>]
  There was an error in posting your news item.
 [</if>]
 <a href="[<$I2_ROOT>]news">Back to News</a>
[<elseif count($groups) < 1>]
 <p>You do not have permission to post to any groups.</p>
[<else>]
 <form action="[<$I2_SELF>]" method="post">
  <input type="hidden" name="add_form" value="1" />
  <table cellpadding="0" width="100%">
  <tr><td width="20%">Title:</td><td><input type="text" name="add_title" size="30" /></td></tr>
  <tr><td>Expiration date:</td><td>[<include file='utils/calendar.tpl' post_var='add_expire'>]</td></tr>
  <tr><td colspan="2"><strong>WARNING:</strong> <em>Currently the expiration is set for MIDNIGHT of the selected date; i.e. this news item will not appear at ALL on the date that you select.  If you want to expire the article mid-day, please edit after initially posting and set a specific time.</em></td></tr>
  <tr><td>Visible:</td><td><input type="checkbox" name="add_visible" checked="checked" /></td><td>Public:</td><td><input type="checkbox" name="add_public" checked="checked" /></td></tr>
  </table>
  <table id="news_groups_table" cellpadding="0" width="100%">
   <tr>
    <td width="20%">Groups:</td>
    <td width="1%">
     <select id="groups" class="groups_list" name="add_groups[]">
      [<foreach from=$groups item=group>]
      	<option value="[<$group->gid>]" [<if $group->name == 'all'>]SELECTED[</if>]>[<$group->name>]</option>
      [</foreach>]
     </select>
    </td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td>&nbsp;</td>
    <td><a href="#" onclick="news_addGroup(); return false">Add another group</a></td>
    <td>&nbsp;</td>
   </tr>
  </table>
  Text: <br />
  [<*<textarea id="news_add_text" name="add_text" rows="15"></textarea><br />*>]
  [<include file="richedit/editor.tpl">]
<p><strong>Please note:</strong> All announcements posted to the 'all' group are now auto-posted to <a href="https://twitter.com/tjintranet">Twitter</a> and are visible outside of this application. Any questions about this process should be directed to <a href="mailto:intranet@tjhsst.edu">intranet@tjhsst.edu</a>.</p>
 <input type="hidden" id="text" name="add_text" /> [<* text field that the output from the rich-text formatter will be fed into *>]
 <input type="hidden" name="csrftok" value="[<$csrftok>]" />
  <input type="submit" value="Submit" name="submit" onclick="doonsubmit()" />[<* the onsubmit is for the rich-text formatting to be handled correctly*>]
 </form>
[</if>]
