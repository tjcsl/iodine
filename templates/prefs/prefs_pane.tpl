<form method="post" action="[<$I2_ROOT>]prefs" class="boxform">
<input type="hidden" name="prefs_form" value="" />
<table>
<tr>
 <td>Style:</td>
 <td><select name="pref_style">
	[<foreach from=$themes item=theme>]
		<option value="[<$theme>]" [<if $curtheme==$theme>]selected="selected"[</if>]>[<$theme>]</option>
	[</foreach>]
</select></td></tr></table><br />
<b>Privacy Options</b><br />
<input type="checkbox" name="pref_showaddressself" [<if $showaddressself>]checked="checked"[</if>]/> Show Address<br />
<input type="checkbox" name="pref_showbdayself" [<if $showbdayself>]checked="checked"[</if>]/> Show Birthday<br />
<input type="checkbox" name="pref_showmapself" [<if $showmapself>]checked="checked"[</if>]/> Show Map Links<br />
<input type="checkbox" name="pref_showpictureself" [<if $showpictureself>]checked="checked"[</if>]/> Show Pictures<br />
<input type="checkbox" name="pref_showphoneself" [<if $showphoneself>]checked="checked"[</if>]/> Show Home Telephone Number<br />
<input type="checkbox" name="pref_showscheduleself" [<if $showscheduleself>]checked="checked"[</if>]/> Show Schedule<br />
<br /><input type="submit" value="Set Preferences" name="submit" /><br />
<br /><br />
Add/Remove intraboxes:<br />
<table>
 <tr>
  <td>
   <select name="delete_boxid[]" size="5" multiple="multiple">
    [<foreach from=$user_intraboxen item=box>]
     <option value="[<$box.boxid>]">[<$box.display_name>]</option>
    [</foreach>]
   </select>
  </td>
  <td>
   <table>
    <tr>
     <td><input type="submit" name="add_intrabox" value="&lt;--" /></td>
    </tr>
    <tr>
     <td><input type="submit" name="delete_intrabox" value="--&gt;" /></td>
    </tr>
   </table>
  </td>
  <td>
   <select name="add_boxid[]" size="5" multiple="multiple">
    [<foreach from=$nonuser_intraboxen item=abox>]
     <option value="[<$abox.boxid>]">[<$abox.display_name>]</option>
    [</foreach>]
   </select>
  </td>
 </tr>
</table>

</form>
