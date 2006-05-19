<script src="[<$I2_ROOT>]www/js/prefs.js" type="text/javascript" language="javascript"></script>
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
<table>
	<tr>
		<td>Cell Phone:</td>
		<td><input type="text" name="pref_mobile" /></td>
	</tr>
	<tr>
		<td>Other Phone #(s):</td>
		<td>
[<foreach from=$I2_USER->phone_other item=phone_other name=phone_other_loop>]
			<input class="pref_preference_input" type="text" name="pref_telephoneNumber[]" value="[<$phone_other>]" />[<if $smarty.foreach.phone_other_loop.first>]<a href="#" onClick="add_field('telephoneNumber', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('phone_other', this);">Remove</a>[</if>][<if !$smarty.foreach.phone_other_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_telephoneNumber[]" /><a href="#" onClick="add_field('telephoneNumber', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Alternate Email(s):</td>
		<td>
[<foreach from=$I2_USER->mail item=email name=mail_loop>]
			<input class="pref_preference_input" type="text" name="pref_mail[]" value="[<$email>]" />[<if $smarty.foreach.mail_loop.first>]<a href="#" onClick="add_field('mail', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('mail', this);">Remove</a>[</if>][<if !$smarty.foreach.mail_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_mail[]" /><a href="#" onClick="add_field('mail', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>AIM:</td>
		<td>
[<foreach from=$I2_USER->aim item=aim name=aim_loop>]
			<input class="pref_preference_input" type="text" name="pref_aim[]" value="[<$aim>]" />[<if $smarty.foreach.aim_loop.first>]<a href="#" onClick="add_field('aim', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('aim', this);">Remove</a>[</if>][<if !$smarty.foreach.aim_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_aim[]" /><a href="#" onClick="add_field('aim', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Yahoo:</td>
		<td>
[<foreach from=$I2_USER->yahoo item=yahoo name=yahoo_loop>]
			<input class="pref_preference_input" type="text" name="pref_yahoo[]" value="[<$yahoo>]" />[<if $smarty.foreach.yahoo_loop.first>]<a href="#" onClick="add_field('yahoo', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('yahoo', this);">Remove</a>[</if>][<if !$smarty.foreach.yahoo_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_yahoo[]" /><a href="#" onClick="add_field('yahoo', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>MSN:</td>
		<td>
[<foreach from=$I2_USER->msn item=msn name=msn_loop>]
			<input class="pref_preference_input" type="text" name="pref_msn[]" value="[<$msn>]" />[<if $smarty.foreach.msn_loop.first>]<a href="#" onClick="add_field('msn', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('msn', this);">Remove</a>[</if>][<if !$smarty.foreach.msn_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_msn[]" /><a href="#" onClick="add_field('msn', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>ICQ:</td>
		<td>
[<foreach from=$I2_USER->icq item=icq name=icq_loop>]
			<input class="pref_preference_input" type="text" name="pref_icq[]" value="[<$icq>]" />[<if $smarty.foreach.icq_loop.first>]<a href="#" onClick="add_field('icq', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('icq', this);">Remove</a>[</if>][<if !$smarty.foreach.icq_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_icq[]" /><a href="#" onClick="add_field('icq', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Google Talk:</td>
		<td>
[<foreach from=$I2_USER->googleTalk item=googleTalk name=googleTalk_loop>]
			<input class="pref_preference_input" type="text" name="pref_googleTalk[]" value="[<$googleTalk>]" />[<if $smarty.foreach.googleTalk_loop.first>]<a href="#" onClick="add_field('googleTalk', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('googleTalk', this);">Remove</a>[</if>][<if !$smarty.foreach.googleTalk_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_googleTalk[]" /><a href="#" onClick="add_field('googleTalk', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Jabber:</td>
		<td>
[<foreach from=$I2_USER->jabber item=jabber name=jabber_loop>]
			<input class="pref_preference_input" type="text" name="pref_jabber[]" value="[<$jabber>]" />[<if $smarty.foreach.jabber_loop.first>]<a href="#" onClick="add_field('jabber', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('jabber', this);">Remove</a>[</if>][<if !$smarty.foreach.jabber_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_jabber[]" /><a href="#" onClick="add_field('jabber', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Webpage(s):</td>
		<td>
[<foreach from=$I2_USER->webpage item=webpage name=webpage_loop>]
			<input class="pref_preference_input" type="text" name="pref_webpage[]" value="[<$webpage>]" />[<if $smarty.foreach.webpage_loop.first>]<a href="#" onClick="add_field('webpage', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('webpage', this);">Remove</a>[</if>][<if !$smarty.foreach.webpage_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_webpage[]" /><a href="#" onClick="add_field('webpage', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Locker:</td>
		<td><input type="text" name="pref_locker" /></td>
	</tr>
</table>
<b>Privacy Options</b><br />
<input type="checkbox" name="showaddressself" [<if $prefs.showaddressself>]checked="checked"[</if>]/> Show Address<br />
<input type="checkbox" name="showbdayself" [<if $prefs.showbdayself>]checked="checked"[</if>]/> Show Birthday<br />
<input type="checkbox" name="showmapself" [<if $prefs.showmapself>]checked="checked"[</if>]/> Show Map Links<br />
<input type="checkbox" name="showpictureself" [<if $prefs.showpictureself>]checked="checked"[</if>]/> Show Pictures<br />
<input type="checkbox" name="showphoneself" [<if $prefs.showphoneself>]checked="checked"[</if>]/> Show Home Telephone Number<br />
<input type="checkbox" name="showscheduleself" [<if $prefs.showscheduleself>]checked="checked"[</if>]/> Show Schedule<br />
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
