[<foreach from=$users item=user>]
	[<if count($user->mail) > 1>]
	[<$user->mail.0>]<br />
	[<else>]
	[<$user->mail>]<br />
	[</if>]
[</foreach>]
