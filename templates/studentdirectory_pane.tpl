[<if $info === FALSE>]
The specified student does not exist. Either you mistyped a URL, or something in Intranet is broken.
[<else>]
Student: [<$info.fname>] [<if $info.nickname>] ([<$info.nickname>]) [</if>] [<$info.mname>] [<$info.lname>]
[</if>]
