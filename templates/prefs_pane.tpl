<form method="POST" action="[<$I2_ROOT>]prefs">
<input type="hidden" name="prefs_form" value="" />
Cell Phone: <input type="text" maxlength="10" name="pref_phone_cell" value="[<$prefs.phone_cell>]" /><br />
AIM Screename: <input type="text" name="pref_sn0" value="[<$prefs.sn0>]" /><br />
<em>Other fields...</em>
<br /><input type="submit" value="Submit" name="submit" /><br />
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
    [<foreach from=$nonuser_intraboxen item=box>]
     <option value="[<$box.boxid>]">[<$box.display_name>]</option>
    [</foreach>]
   </select>
  </td>
 </tr>
</table>

</form>
