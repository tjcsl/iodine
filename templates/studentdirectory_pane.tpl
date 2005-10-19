[<if $info === FALSE>]

The specified student does not exist. Either you mistyped a URL, or something in Intranet is broken.

[<elseif isset($info.fname)>]
<table>
<tr><td valign="top">
<img src="[<$I2_ROOT>]www/bomb.gif" vspace="2" width="172" height="228" /></td>
<td valign="top">
[<if $info.title>][<$info.title>][</if>] [<$info.fname>][<if $info.nickname>] ([<$info.nickname>])[</if>] [<$info.mname>] [<$info.lname>][<if $info.suffix>] [<$info.suffix>][</if>] (<a href="mailto:[<$info.username>]@tjhsst.edu">[<$info.username>]@tjhsst.edu</a>), Grade [<$info.grade>]<br />
Born [<$info.bdate>]<br />
Phone (home): [<$info.phone_home>]<br />
[<$info.address1_street>]<br />
[<$info.address1_city>], [<$info.address1_state>] [<$info.address1_zip>]<br />
[<if $info.address2_street>]
2nd address:<br />
[<$info.address2_street>]<br />
[<$info.address2_city>], [<$info.address2_state>] [<$info.address2_zip>]<br />
[</if>]
[<if $info.address3_street>]
3rd address:<br />
[<$info.address3_street>]<br />
[<$info.address3_city>], [<$info.address3_state>] [<$info.address3_zip>]<br />
[</if>]
Map from home | Map from school<br />
Counselor: [<$info.counselor>]<br />
<br />
[<if $info.email0>]Personal e-mail: <a href="mailto:[<$info.email0>]">[<$info.email0>]</a><br />[</if>]
[<if $info.email1>]2nd personal e-mail: <a href="mailto:[<$info.email1>]">[<$info.email1>]</a><br />[</if>]
[<if $info.email2>]3rd personal e-mail: <a href="mailto:[<$info.email2>]">[<$info.email2>]</a><br />[</if>]
[<if $info.email3>]4th personal e-mail: <a href="mailto:[<$info.email3>]">[<$info.email3>]</a><br />[</if>]
[<if $info.phone_other>]Alternate phone: [<$info.phone_other>]<br />[</if>]
[<if $info.phone_cell>]Cell phone: [<$info.phone_cell>]<br />[</if>]
[<if $info.sn0>]AIM/AOL Screenname: <a href="aim:goim?screenname=[<$info.sn0>]">[<$info.sn0>]</a><br />[</if>]
[<if $info.sn1>]Yahoo! ID: [<$info.sn1>]<br />[</if>]
[<if $info.sn2>]MSN Username: [<$info.sn2>]<br />[</if>]
[<if $info.sn3>]Jabber: [<$info.sn3>]<br />[</if>]
[<if $info.sn4>]ICQ Number: [<$info.sn4>]<br />[</if>]
[<if $info.sn5>]Google Talk: [<$info.sn5>]<br />[</if>]
[<if $info.sn6>]sn6: [<$info.sn6>]<br />[</if>]
[<if $info.sn7>]sn7: [<$info.sn7>]<br />[</if>]
[<if $info.webpage>]Webpage: <a href="[<$info.webpage>]">[<$info.webpage>]</a><br />[</if>]
[<if $info.locker>]Locker Number: [<$info.locker>]<br />[</if>]
</td></tr></table>
<br /><br />
The DUMP:<br />
Student: [<$info.fname>] [<if $info.nickname>] ([<$info.nickname>]) [</if>] [<$info.mname>] [<$info.lname>] [<$info.suffix>]
<br />Info:<br /><br />
[<foreach from=$info item=val key=key>]
[<if ! ($key == "fname" || $key == "nickname" || $key == "mname" || $key == "lname" || $key == "suffix")>]
[<$key>]: [<$val>]<br />
[</if>]
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
