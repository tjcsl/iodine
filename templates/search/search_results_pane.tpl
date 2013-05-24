[<if isset($numresults)>][<$numresults>] Results:<br />[</if>]
[<foreach from=$info item=user>]
<a href="[<$I2_ROOT>][<$results_destination>][<$user->uid>]">[<$user->fullname_comma>] ([<$user->grade>])</a><br />
[</foreach>]
<br />
<a href="[<$I2_ROOT>]search/clear/[<$return_destination|default:''>]">New Search</a>
