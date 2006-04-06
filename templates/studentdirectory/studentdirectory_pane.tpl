[<if $info === FALSE and not $user>]

The specified student does not exist. Either you mistyped a URL, or something in Intranet is broken.
[</if>]
[<if $user->fname>]
<table>
<tr><td valign="top">
<img src="[<$I2_ROOT>]www/pics/bomb.png" vspace="2" width="172" height="228" /></td>
<td valign="top">
[<$user->fname>][<if $user->nickname>] ([<$user->nickname>])[</if>] [<$user->mname>] [<$user->lname>][<if $user->suffix>] [<$user->suffix>][</if>] (<a href="mailto:[<$user->username>]@tjhsst.edu">[<$user->username>]@tjhsst.edu</a>), Grade [<$user->grade>]<br />
[<if $user->bdate>]Born [<$user->bdate>]<br />[</if>]
[<if $user->phone_home>]Phone (home): [<$user->phone_home>]
 [<else>]Home phone information not available.
[</if>]</br />
[<if $user->address1_street>]
 [<$user->address1_street>]<br />
 [<$user->address1_city>], [<$user->address1_state>] [<$user->address1_zip>]<br />
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
[<else>]
 Address information not available.<br />
[</if>]
Map from home | Map from school<br />
Counselor: [<$user->counselor>]<br />
<br />
[<if $user->email0>]Personal e-mail: <a href="mailto:[<$user->email0>]">[<$user->email0>]</a><br />[</if>]
[<if $user->email1>]2nd personal e-mail: <a href="mailto:[<$user->email1>]">[<$user->email1>]</a><br />[</if>]
[<if $user->email2>]3rd personal e-mail: <a href="mailto:[<$user->email2>]">[<$user->email2>]</a><br />[</if>]
[<if $user->email3>]4th personal e-mail: <a href="mailto:[<$user->email3>]">[<$user->email3>]</a><br />[</if>]
[<if $user->phone_other>]Alternate phone: [<$user->phone_other>]<br />[</if>]
[<if $user->phone_cell>]Cell phone: [<$user->phone_cell>]<br />[</if>]
[<if $user->sn0>]AIM/AOL Screenname: <a href="aim:goim?screenname=[<$user->sn0>]">[<$user->sn0>]</a><br />[</if>]
[<if $user->sn1>]Yahoo! ID: [<$user->sn1>]<br />[</if>]
[<if $user->sn2>]MSN Username: [<$user->sn2>]<br />[</if>]
[<if $user->sn3>]Jabber: [<$user->sn3>]<br />[</if>]
[<if $user->sn4>]ICQ Number: [<$user->sn4>]<br />[</if>]
[<if $user->sn5>]Google Talk: [<$user->sn5>]<br />[</if>]
[<if $user->sn6>]sn6: [<$user->sn6>]<br />[</if>]
[<if $user->sn7>]sn7: [<$user->sn7>]<br />[</if>]
[<if $user->webpage>]Webpage: <a href="[<$user->webpage>]">[<$user->webpage>]</a><br />[</if>]
[<if $user->locker>]Locker Number: [<$user->locker>]<br />[</if>]
</td></tr></table>

[<if $schedule>]
 <br />Classes:<br />
 [<foreach from=$schedule item=class>]
  <a href="[<$I2_ROOT>]studentdirectory/class/[<$class->sectionid>]">Period: [<$class->period>], Name: [<$class->name>], Room: [<$class->room>]</a><br />
 [</foreach>]
[</if>]

<br /><br />
The DUMP:<br />
Student: [<$user->fname>] [<if $user->nickname>] ([<$user->nickname>]) [</if>] [<$user->mname>] [<$user->lname>] [<$user->suffix>]
<br />Info:<br /><br />
[<foreach from=$info item=val key=key>]
[<if ! ($key == "fname" || $key == "nickname" || $key == "mname" || $key == "lname" || $key == "suffix")>]
[<$key>]: [<$val>]<br />
[</if>]
[</foreach>]

[<elseif is_array($info) and isset($info.class)>]
 [<foreach from=$info.students item=student>]
  <a href="[<$I2_ROOT>]studentdirectory/info/[<$student->uid>]">[<$student->name>]</a><br />
 [</foreach>]

[<elseif is_array($info)>]

	[<foreach from=$info item=user>]
		<a href="[<$I2_ROOT>]studentdirectory/info/[<$user->uid>]">[<$user->fullname_comma>]
			[<if $user->grade>]([<$user->grade>])[</if>]
		</a><br />
	[</foreach>]

[<else>]
Internal error, please contact the intranetmaster.
[</if>]
