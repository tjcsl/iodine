<script src="[<$I2_ROOT>]www/js/prefs.js" type="text/javascript" language="javascript"></script>
<script src="[<$I2_ROOT>]www/js/color_chooser.js" type="text/javascript" language="javascript"></script>
<form method="post" action="[<$I2_ROOT>]prefs" class="boxform">
<input type="hidden" name="prefs_form" value="" />
<strong>Personal Information</strong>
<table>
	<tr>
		<td>Cell Phone:</td>
		<td><input type="text" name="pref_mobile" value="[<$I2_USER->mobile>]" /></td>
	</tr>
	<tr>
		<td>Other Phone #(s):</td>
		<td>
[<foreach from=$I2_USER->telephoneNumber item=phone_other name=phone_other_loop>]
			<input class="pref_preference_input" type="text" name="pref_telephoneNumber[]" value="[<$phone_other|escape:'html'>]" />[<if $smarty.foreach.phone_other_loop.first>]<a href="#" onClick="add_field('telephoneNumber', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('phone_other', this);">Remove</a>[</if>][<if !$smarty.foreach.phone_other_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_telephoneNumber[]" /><a href="#" onClick="add_field('telephoneNumber', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>E-mail address(es):</td>
		<td>
[<foreach from=$I2_USER->mail item=email name=mail_loop>]
			<input class="pref_preference_input" type="text" name="pref_mail[]" value="[<$email|escape:'html'>]" />[<if $smarty.foreach.mail_loop.first>]<a href="#" onClick="add_field('mail', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('mail', this);">Remove</a>[</if>][<if !$smarty.foreach.mail_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_mail[]" /><a href="#" onClick="add_field('mail', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>AIM:</td>
		<td>
[<foreach from=$I2_USER->aim item=aim name=aim_loop>]
			<input class="pref_preference_input" type="text" name="pref_aim[]" value="[<$aim|escape:'html'>]" />[<if $smarty.foreach.aim_loop.first>]<a href="#" onClick="add_field('aim', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('aim', this);">Remove</a>[</if>][<if !$smarty.foreach.aim_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_aim[]" /><a href="#" onClick="add_field('aim', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Yahoo:</td>
		<td>
[<foreach from=$I2_USER->yahoo item=yahoo name=yahoo_loop>]
			<input class="pref_preference_input" type="text" name="pref_yahoo[]" value="[<$yahoo|escape:'html'>]" />[<if $smarty.foreach.yahoo_loop.first>]<a href="#" onClick="add_field('yahoo', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('yahoo', this);">Remove</a>[</if>][<if !$smarty.foreach.yahoo_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_yahoo[]" /><a href="#" onClick="add_field('yahoo', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>MSN:</td>
		<td>
[<foreach from=$I2_USER->msn item=msn name=msn_loop>]
			<input class="pref_preference_input" type="text" name="pref_msn[]" value="[<$msn|escape:'html'>]" />[<if $smarty.foreach.msn_loop.first>]<a href="#" onClick="add_field('msn', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('msn', this);">Remove</a>[</if>][<if !$smarty.foreach.msn_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_msn[]" /><a href="#" onClick="add_field('msn', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>ICQ:</td>
		<td>
[<foreach from=$I2_USER->icq item=icq name=icq_loop>]
			<input class="pref_preference_input" type="text" name="pref_icq[]" value="[<$icq|escape:'html'>]" />[<if $smarty.foreach.icq_loop.first>]<a href="#" onClick="add_field('icq', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('icq', this);">Remove</a>[</if>][<if !$smarty.foreach.icq_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_icq[]" /><a href="#" onClick="add_field('icq', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Google Talk:</td>
		<td>
[<foreach from=$I2_USER->googleTalk item=googleTalk name=googleTalk_loop>]
			<input class="pref_preference_input" type="text" name="pref_googleTalk[]" value="[<$googleTalk|escape:'html'>]" />[<if $smarty.foreach.googleTalk_loop.first>]<a href="#" onClick="add_field('googleTalk', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('googleTalk', this);">Remove</a>[</if>][<if !$smarty.foreach.googleTalk_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_googleTalk[]" /><a href="#" onClick="add_field('googleTalk', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Jabber:</td>
		<td>
[<foreach from=$I2_USER->jabber item=jabber name=jabber_loop>]
			<input class="pref_preference_input" type="text" name="pref_jabber[]" value="[<$jabber|escape:'html'>]" />[<if $smarty.foreach.jabber_loop.first>]<a href="#" onClick="add_field('jabber', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('jabber', this);">Remove</a>[</if>][<if !$smarty.foreach.jabber_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_jabber[]" /><a href="#" onClick="add_field('jabber', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>XFire:</td>
		<td>
[<foreach from=$I2_USER->xfire item=xfire name=xfire_loop>]
			<input class="pref_preference_input" type="text" name="pref_xfire[]" value="[<$xfire|escape:'html'>]" />[<if $smarty.foreach.xfire_loop.first>]<a href="#" onClick="add_field('xfire', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('xfire', this);">Remove</a>[</if>][<if !$smarty.foreach.xfire_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_xfire[]" /><a href="#" onClick="add_field('xfire', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Skype:</td>
		<td>
[<foreach from=$I2_USER->skype item=skype name=skype_loop>]
			<input class="pref_preference_input" type="text" name="pref_skype[]" value="[<$skype|escape:'html'>]" />[<if $smarty.foreach.skype_loop.first>]<a href="#" onClick="add_field('skype', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('skype', this);">Remove</a>[</if>][<if !$smarty.foreach.skype_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_skype[]" /><a href="#" onClick="add_field('skype', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
	<tr>
		<td>Webpage(s):</td>
		<td>
[<foreach from=$I2_USER->webpage item=webpage name=webpage_loop>]
			<input class="pref_preference_input" type="text" name="pref_webpage[]" value="[<$webpage|escape:'html'>]" />[<if $smarty.foreach.webpage_loop.first>]<a href="#" onClick="add_field('webpage', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('webpage', this);">Remove</a>[</if>][<if !$smarty.foreach.webpage_loop.last>]<br />[</if>]
[<foreachelse>]
			<input class="pref_preference_input" type="text" name="pref_webpage[]" /><a href="#" onClick="add_field('webpage', this);">Add Another</a>
[</foreach>]
		</td>
	</tr>
</table>
<br />
<strong><a href="[<$I2_ROOT>]calc">Calculator Registration</a></strong>
<br /><br />
[<if $I2_USER->grade != "staff">]
<strong>Preferred Picture</strong><br />
<em>Since the eighth period office and TJ faculty can always see your pictures, it is recommended that you choose your preferred picture even if you disable "Show Pictures" below.</em><br />
<input type="radio" name="pref_preferredPhoto" value="AUTO" [<if $prefs.preferredPhoto == "AUTO">]checked="checked"[</if>]/>Auto-select the most recent photo<br />
[<foreach from=$photonames key=photo item=text>]
<input type="radio" name="pref_preferredPhoto" value="[<$photo>]" [<if $prefs.preferredPhoto == $photo>]checked="checked"[</if>]/>[<$text>] photo<br />
[</foreach>]
<br />
<strong>Privacy Options</strong><br />
<em>If your parent did not give permission on the "Intranet Posting Agreement" form to show specific personal information, other students will not be able to see that information regardless of your personal settings.</em><br />
<em>Note that all TJ staff can always view all of this information.</em><br />
<table style="text-align: center;" cellpadding="1" cellspacing="0">
<tr><td style="padding: 0ex .5ex; text-decoration: underline;">Parent</td><td style="padding: 0ex 2ex; text-decoration: underline;">You</td><td></td></tr>
<tr><td><input type="checkbox" name="showaddress" [<if $prefs.showaddress>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showaddressself" [<if $prefs.showaddressself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Address</td></tr>
<tr><td><input type="checkbox" name="showbday" [<if $prefs.showbday>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showbdayself" [<if $prefs.showbdayself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Birthday</td></tr>
[<*<tr><td><input type="checkbox" name="showmap" [<if $prefs.showmap>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showmapself" [<if $prefs.showmapself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Map Links</td></tr>*>]
<tr><td><input type="checkbox" name="showpicture" [<if $prefs.showpicture>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showpictureself" [<if $prefs.showpictureself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Pictures</td></tr>
<tr><td><input type="checkbox" name="showphone" [<if $prefs.showphone>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showphoneself" [<if $prefs.showphoneself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Home Telephone Number</td></tr>
<tr><td><input type="checkbox" name="showschedule" [<if $prefs.showschedule>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showscheduleself" [<if $prefs.showscheduleself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Class Schedule</td></tr>
<tr><td><input type="checkbox" name="showeighth" [<if $prefs.showeighth>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showeighthself" [<if $prefs.showeighthself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Eighth Period Schedule</td></tr>
<tr><td><input type="checkbox" name="showlocker" [<if $prefs.showlocker>]checked="checked"[</if>] disabled /></td><td><input type="checkbox" name="showlockerself" [<if $prefs.showlockerself>]checked="checked"[</if>]/></td><td style="text-align: left">Show Locker Number</td></tr>
</table>
<br />
[</if>]
<strong>Display Options</strong><br />
<table>
<tr>
 <td>Style:</td>
 <td><select name="pref_style">
	[<foreach from=$themes item=theme>]
		<option value="[<$theme>]" [<if $curtheme==$theme>]selected="selected"[</if>]>[<$theme>]</option>
	[</foreach>]
</select></td></tr>
	<tr>
		<td>Intrabox titlebar color (hex RRBBGG):</td>
		<td><input type="text" name="pref_boxcolor" value="[<$I2_USER->boxcolor|escape:'html'>]"/> <a target="_blank" href="[<$I2_ROOT>]info/prefs/color">Color chooser</a></td>
	</tr>
	<tr>
		<td>Intrabox title text color (hex RRBBGG):</td>
		<td><input type="text" name="pref_boxtitlecolor" value="[<$I2_USER->boxtitlecolor|escape:'html'>]"/></td>
	</tr>
	<tr>
		<td>Mail messages to display in mail box:</td>
		<td>
			[<assign var="messages" value=$I2_USER->mailmessages>]
			<select name="pref_mailmessages">
				<option value="-1" [<if $messages == -1>]selected="selected"[</if>]/>0
				<option value="1" [<if $messages == 1>]selected="selected"[</if>]/>1
				<option value="2" [<if $messages == 2>]selected="selected"[</if>]/>2
				<option value="3" [<if $messages == 3>]selected="selected"[</if>]/>3
				<option value="4" [<if $messages == 4>]selected="selected"[</if>]/>4
				<option value="5" [<if $messages == 5>]selected="selected"[</if>]/>5
				<option value="6" [<if $messages == 6>]selected="selected"[</if>]/>6
				<option value="7" [<if $messages == 7>]selected="selected"[</if>]/>7
				<option value="8" [<if $messages == 8>]selected="selected"[</if>]/>8
				<option value="9" [<if $messages == 9>]selected="selected"[</if>]/>9
				<option value="10" [<if $messages == 10>]selected="selected"[</if>]/>10
			</select>
		</td>
	</tr>
</table>
Add/Remove intraboxes:<br />
<table>
 <tr>
  <td>
   <select name="delete_boxid[]" size="5" multiple="multiple" style="width: 200px;">
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
   <select name="add_boxid[]" size="5" multiple="multiple" style="width: 200px;">
    [<foreach from=$nonuser_intraboxen item=abox>]
     <option value="[<$abox.boxid>]">[<$abox.display_name>]</option>
    [</foreach>]
   </select>
  </td>
 </tr>
</table>
<br /><input type="submit" value="Set Preferences" name="submit" /><br /><br />

</form>
