[<if $info === FALSE and not $user>]


The specified student does not exist. Either you mistyped a URL, or something in Intranet is broken.
[</if>]
[<if $user && $user->fname>]
[<if $user->uid == $I2_USER->uid>]
This is YOUR info page.  All information stored in the Intranet database is visible to you. <br />
In order to choose what info can be seen by OTHER users, please setup your <a href="[<$I2_ROOT>]prefs">preferences</a>.<br /><br />
[</if>]
<table>
<tr><td valign="top">
<img src="[<$I2_ROOT>]pictures/[<$user->uid>]" vspace="2" width="172" height="228" /></td>
<td valign="top">
[<$user->fullname>] (<a href="mailto:[<$user->username>]@tjhsst.edu">[<$user->username>]@tjhsst.edu</a>)[<if $user->grade != -1>], Grade [<$user->grade>][</if>]<br />
[<if $user->bdate>]Born [<$user->bdate>]<br />[</if>]
Counselor: [<$user->counselor_name>]<br />
[<if $user->homePhone>][<foreach from=$user->phone_home item=phone>]Phone (home): [<$phone>]<br />[</foreach>]
[</if>]<br />
[<if $user->phone_cell>]Cell phone: [<$user->phone_cell>]<br />[</if>]
[<if count($user->phone_other)>]Alternate phone number(s):
 <ul>
 [<foreach from=$user->phone_other item=phone_other>]
  <li>[<$phone_other>]</li>
 [</foreach>]
 </ul>
[</if>]
<br />
[<if $user->street>]
 [<$user->street>]<br />
 [<$user->l>], [<$user->st>] [<$user->postalCode>]<br />
 [<if $user->address2_street>]
  2nd address:<br />
  [<$user->address2_street>]<br />
  [<$user->address2_city>], [<$user->address2_state>] [<$user->address2_zip>]<br />
 [</if>]
 [<if $user->address3_street>]
  3rd address:<br />
  [<$user->address3_street>]<br />
  [<$user->address3_city>], [<$user->address3_state>] [<$user->address3_zip>]<br />
 [</if>]
[</if>]
[<if $user->street && $user->show_map>]
Get directions:
[<if $I2_UID != $user->uid>]<a href="http://maps.google.com/maps?f=d&hl=en&saddr=[<$I2_USER->street>], [<$I2_USER->l>], [<$I2_USER->st>] [<$I2_USER->postalCode>]&daddr=[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]">from your home</a>
or <a href="http://maps.google.com/maps?f=d&hl=en&saddr=6560 Braddock Rd, Alexandria, VA 22312&daddr=[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]">from school</a>
[<else>]<a href="http://maps.google.com/maps?f=d&hl=en&saddr=[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]&daddr=6560 Braddock Rd, Alexandria, VA 22312">to school</a>
[</if>]
<br />
[</if>]
<br />
[<if count($user->mail)>]Personal e-mail address(es): 
[<foreach from=$user->mail item="email" name="emails">]
	[<if $smarty.foreach.emails.last and not $smarty.foreach.emails.first>]
		and
	[<elseif not $smarty.foreach.emails.first>]
		,
	[</if>]
	<a href="mailto:[<$email>]">[<$email|escape:'html'>]</a>
	[</foreach>]
[</if>]
<br />
<br />
[<if count($user->aim)>]
AIM/AOL Screenname(s):
 <ul>
 [<foreach from=$user->aim item=aim>]
  <li><img src="[<$I2_ROOT>]www/pics/osi/[<$im_status.aim.$aim>].png" /> <a href="aim:goim?screenname=[<$aim>]">[<$aim|escape:'html'>]</a></li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->yahoo)>]Yahoo! ID(s):
 <ul>
 [<foreach from=$user->yahoo item=yahoo>]
  <li><img src="[<$I2_ROOT>]www/pics/osi/[<$im_status.yahoo.$yahoo>].png" /> [<$yahoo|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->msn)>]MSN Username(s):
 <ul>
 [<foreach from=$user->msn item=msn>]
  <li>[<$msn|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->jabber)>]Jabber Username(s):
 <ul>
 [<foreach from=$user->jabber item=jabber>]
  <li><img src="[<$I2_ROOT>]www/pics/osi/[<$im_status.jabber.$jabber>].png" /> [<$jabber|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->icq)>]ICQ Number(s):
 <ul>
 [<foreach from=$user->icq item=icq>]
  <li><img src="[<$I2_ROOT>]www/pics/osi/[<$im_status.icq.$icq>].png" /> [<$icq|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->googleTalk)>]Google Talk Username(s):
 <ul>
 [<foreach from=$user->googleTalk item=googleTalk>]
  <li>[<$googleTalk|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->xfire)>]XFire handle(s):
 <ul>
 [<foreach from=$user->xfire item=xfire>]
  <li>[<$xfire|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->webpage)>]Webpage(s):
 <ul>
 [<foreach from=$user->webpage item=webpage>]
  <li><a href="[<$webpage|escape:'html'>]">[<$webpage>]</a></li>
 [</foreach>]
 </ul>
[</if>]
[<if $user->locker>]Locker Number: [<$user->locker|escape:'html'>]<br />[</if>]
</td></tr></table>

[<if $schedule>]
 <br />Classes:<br />
 [<foreach from=$schedule item=class>]
  <a href="[<$I2_ROOT>]studentdirectory/class/[<$class->sectionid>]">Period: [<$class->period>], Name: [<$class->name>], Room: [<$class->room>]</a><br />
 [</foreach>]
[</if>]

[<if $eighth>]
 <br />Eighth Periods:<br />
 <table cellspacing="0">
  <tr>
   <th>Date</th>
   <th>Activity</th>
   <th>Room(s)</th>
  </tr>
 [<foreach from=$eighth item=activity>]
  <tr class="[<cycle values="c1,c2">]">
   <td class="directory-table">[<$activity->block->date|date_format>], [<$activity->block->block>] Block</td>
   <td class="directory-table">[<if $activity->aid != 999>]<a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_full_r>]</a>[<else>]HAS NOT SELECTED AN ACTIVITY[</if>]</td>
   <td class="directory-table">[<$activity->block_rooms_comma>]</td>
  </tr>
 [</foreach>]
 </table>
[</if>]
[<elseif $info>]
[<include file="search/search_results_pane.tpl" results_destination="StudentDirectory/info/">]
[<else>]
There were no results matching your query.
[</if>]
