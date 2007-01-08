[<if $info === FALSE and not $user>]


The specified user does not exist. Either you mistyped a URL, or something in Intranet is broken.
[</if>]
[<if $user && $user->fname>]
[<if $user->uid == $I2_USER->uid>]
<strong>This is YOUR info page.  All of your information will ALWAYS be visible to you.</strong><br />
In order to choose what info can be seen by OTHER users, please setup your <a href="[<$I2_ROOT>]prefs">preferences</a>.<br /><br />
[</if>]
[<if $is_admin>]
<strong>This student is an Intranet Administrator, please contact him/her with any problems you encounter.</strong><br /><br />
[</if>]
<table>
<tr>
[<if $user->grade != 'staff'>]
<td valign="top">
<img src="[<$I2_ROOT>]pictures/[<$user->uid>]" vspace="2" width="172" height="228" /><br />
<a href="[<$I2_ROOT>]studentdirectory/pictures/[<$user->uid>]">View pictures from all years</a>
[<if $homecoming_may_vote>]<br /><br /><strong><a href="[<$I2_ROOT>]homecoming/vote/[<$user->uid>]">Vote for this person<br />for homecoming court</a></strong>[</if>]
</td>
[</if>]
<td valign="top">
[<$user->fullname>][<if $user->grade != 'staff'>], Grade [<$user->grade>][<else>], on staff[</if>]<br />
[<if $user->bdate>]Born [<$user->bdate>]<br />[</if>]
[<if $user->counselor>]Counselor: [<$user->counselor_name>]<br />[</if>]
<br />
[<if $user->homePhone || $user->phone_cell || count($user->phone_other)>]
Phone number(s):
 <ul class="none">
 [<if $user->homePhone>][<foreach from=$user->phone_home item=phone>]<li>[<$phone>] (Home)</li>[</foreach>][</if>]
 [<if $user->phone_cell>]<li>[<$user->phone_cell>] (Cell)</li>[</if>]
 [<if count($user->phone_other)>][<foreach from=$user->phone_other item=phone_other>]<li>[<$phone_other>] (Other)</li>[</foreach>][</if>]
 </ul>
[</if>]
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
[<* Disabled to appease the administration. -- AES 6/14/06 *>]
[<if $user->street && $user->show_map>]
Get directions:
[<if $I2_UID != $user->uid>]<a href="http://maps.google.com/maps?f=d&hl=en&saddr=[<$I2_USER->street>], [<$I2_USER->l>], [<$I2_USER->st>] [<$I2_USER->postalCode>]&daddr=[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]">from your home</a>
or <a href="http://maps.google.com/maps?f=d&hl=en&saddr=6560 Braddock Rd, Alexandria, VA 22312&daddr=[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]">from school</a>
[<else>]<a href="http://maps.google.com/maps?f=d&hl=en&saddr=[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]&daddr=6560 Braddock Rd, Alexandria, VA 22312">to school</a>
[</if>]
<br />
[</if>]
<br />
[<mailto address=$user->tjmail encode="hex" text="E-mail">] address(es): 
[<foreach from=$user->mail item="email" name="emails">]
	[<if $smarty.foreach.emails.last and not $smarty.foreach.emails.first>]
		and
	[<elseif not $smarty.foreach.emails.first>]
		,
	[</if>]
	[<mailto address=$email encode="hex">]
	[</foreach>]
<br />
<br />
[<if count($user->aim)>]
	[<include file="studentdirectory/aim.tpl">]
[</if>]
[<if count($user->yahoo)>]Yahoo! ID(s):
 <ul class="none">
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
 <ul class="none">
 [<foreach from=$user->jabber item=jabber>]
  <li><img src="[<$I2_ROOT>]www/pics/osi/[<$im_status.jabber.$jabber>].png" /> [<$jabber|escape:'html'>]</li>
 [</foreach>]
 </ul>
[</if>]
[<if count($user->icq)>]ICQ Number(s):
 <ul class="none">
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
[<if count($user->skype)>]Skype handle(s):
 <ul class="none">
 [<foreach from=$user->skype item=skype>]
  <li><img src="http://mystatus.skype.com/smallicon/[<$skype|escape:'html'>]" /> <a href="skype:[<$skype>]?call">[<$skype|escape:'html'>]</a></li>
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

[<if $schedule && count($schedule) > 0 >]
<div style="float: left; margin-right: 50px;">
 <br /><span class="bold">Classes:</span><br />
 <table cellspacing="0">
  <thead>
   <tr>
    <th>Pd</th>
    <th>Name</th>
    <th>Rm</th>
    <th>Teacher</th>
    <th>Quarter(s)</td>
   </tr>
  </thead>
  <tbody>
 [<foreach from=$schedule item=class>]
   <tr class="[<cycle values="c1,c2">]">
    <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$class->sectionid>]">[<$class->period>]</a></td>
    <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$class->sectionid>]">[<$class->name>]</a></td>
    <td class="directory-table" style="text-align:center;">[<$class->room>]</td>
    <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/info/[<$class->teacher->uid>]">[<$class->teacher->sn>]</a></td>
    <td class="directory-table">[<$class->term>]</td>
   </tr>
 [</foreach>]
  </tbody>
 </table>
</div>
[</if>]

[<if $eighth>]
 <br /><span class="bold">Eighth Periods:</span><br />
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
[<elseif $info and isset($info.class) and isset($info.students)>]
<p><a href="[<$I2_ROOT>]studentdirectory/section/[<$info.class->classid>]">Click here to view a list of all sections of [<$info.class->name>].</a></p>
<p>Students in <a href=[<$I2_ROOT>]studentdirectory/info/[<$info.class->teacher->uid>]>[<$info.class->teacher->name>]</a>'s [<$info.class->name>], period [<$info.class->period>]:</p>
<table cellspacing="0">
 <thead>
  <tr>
   <th>Name</th>
   <th>Email</th>
  </tr>
 </thead>
[<foreach from=$info.students item=student>]
 <tbody>
  <tr class="[<cycle values="c1,c2">]">
   <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/info/[<$student->uid>]">[<$student->fullname_comma>] ([<$student->grade>])</a></td>
   <td class="directory-table">
    [<if count($student->mail)>]
	  	[<if count($student->mail) == 1>]
			[<assign var="mail" value=$student->mail>]
		[<else>]
			[<assign var="mail" value=$student->mail.0>]
		[</if>]
		[<mailto address=$mail encode="hex">]
	  </td>
    [<else>]
     &nbsp;
    [</if>]
  </tr>
 </tbody>
[</foreach>]
</table>
<br />
Other classes taught by [<$info.class->teacher->name>]:
<table cellspacing="0">
 <thead>
  <th>Period</th>
  <th>Name</th>
  <th>Room(s)</th>
  <th>Quarter(s)</th>
 </thead>
 <tbody>
 [<foreach from=$info.class->other_classes() item=otherclass>]
  <tr class="[<cycle values="c1,c2">]">
   <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$otherclass->sectionid>]">[<$otherclass->period>]</a></td>
   <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$otherclass->sectionid>]">[<$otherclass->name>]</a></td>
   <td class="directory-table">[<$otherclass->room>]</td>
   <td class="directory-table">[<$otherclass->term>]</td>
  </tr>
 [</foreach>]
 </tbody>
</table>
[<elseif $info>]
[<include file="search/search_results_pane.tpl" results_destination="StudentDirectory/info/">]
[<else>]
There were no results matching your query.
[</if>]
