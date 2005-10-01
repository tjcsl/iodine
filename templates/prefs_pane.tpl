<form method="POST" action="[<$I2_ROOT>]prefs">
<input type="hidden" name="prefs_form" value="" />
<table>
<tr>
 <td>Cell Phone:</td>
 <td><input type="text" maxlength="10" name="pref_phone_cell" value="[<$prefs.phone_cell>]" /></td></tr><br />
<tr>
 <td>AIM Screename:</td>
 <td><input type="text" name="pref_sn0" value="[<$prefs.sn0>]" /></td></tr><br />
<tr>
 <td>Yahoo! ID:</td>
 <td><input type="text" name="pref_sn1" value="[<$prefs.sn1>]" /></td></tr><br />
<tr>
 <td>MSN Username:</td>
 <td><input type="text" name="pref_sn2" value="[<$prefs.sn2>]" /></td></tr><br />
<tr>
 <td>Jabber:</td>
 <td><input type="text" name="pref_sn3" value="[<$prefs.sn3>]" /></td></tr><br />
<tr>
 <td>ICQ Number:</td>
 <td><input type="text" name="pref_sn4" value="[<$prefs.sn4>]" /></td></tr><br />
<tr>
 <td>Google Talk Username:</td>
 <td><input type="text" name="pref_sn5" value="[<$prefs.sn5>]" /></td></tr><br />
<tr>
 <td>Email:</td>
 <td><input type="text" name="pref_email1" value="[<$prefs.email1>]" /><br /></td></tr>
 <td>Personal Webpage:</td>
 <td><input type="text" name="pref_webpage" value="[<$prefs.webpage>]" /><br /></td></tr>
<tr>
 <td>Style:</td>
 <td><select name="pref_style">
	[<foreach from=$themes item=theme>]
		<option value="[<$theme>]" [<if $curtheme==$theme>]selected[</if>]>[<$theme>]</option>
	[</foreach>]
</select></td></tr></table>
<br /><input type="submit" value="Set Preferences" name="submit" /><br />
<br /><br />
Add/Remove intraboxes:<br />
<table>
 <tr>
  <td>
   <select name="delete_boxid" size="5">
    [<foreach from=$user_intraboxen item=box>]
     <option value="[<$box.boxid>]">[<$box.display_name>]</option>
    [</foreach>]
   </select>
  </td>
  <td>
   <table>
    <tr>
     <td><input type="submit" name="add_intrabox" value="<--" /></td>
    </tr>
    <tr>
     <td><input type="submit" name="delete_intrabox" value="-->" /></td>
    </tr>
   </table>
  </td>
  <td>
   <select name="add_boxid" size="5">
    [<foreach from=$nonuser_intraboxen item=abox>]
     <option value="[<$abox.boxid>]">[<$abox.display_name>]</option>
    [</foreach>]
   </select>
  </td>
 </tr>
</table>

</form>
