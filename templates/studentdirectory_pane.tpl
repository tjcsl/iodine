[<if $info === FALSE>]

The specified student does not exist. Either you mistyped a URL, or something in Intranet is broken.

[<elseif isset($info.fname)>]

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
