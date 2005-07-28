[<if $info === FALSE>]
The specified student does not exist. Either you mistyped a URL, or something in Intranet is broken.
[<elseif isset($info.fname)>]
Student: [<$info.fname>] [<if $info.nickname>] ([<$info.nickname>]) [</if>] [<$info.mname>] [<$info.lname>]
[<elseif is_array($info)>]

	[<foreach from=$info item=user>]
		<a href="[<$I2_ROOT>]studentdirectory/info/[<$user->uid>]">[<$user->fullname_comma>]
			[<if $user->grade>]([<$user->grade>])[</if>]
		</a><br />
	[</foreach>]

[<else>]
Internal error, please contact the intranetmaster.
[</if>]
