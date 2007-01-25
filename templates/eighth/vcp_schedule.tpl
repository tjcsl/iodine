[<include file="eighth/header.tpl">]
[<if count($users) == 0 >] 	 
	No students matched search criteria. 	 
[<else>] 	 
<ul> 	 
[<foreach from=$users item="user">] 	 
	<li><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]">[<$user->fullname_comma>] ([<$user->studentid>]), Grade [<$user->grade>]</a></li> 
[</foreach>] 	 
</ul> 	 
[</if>]
