[<if isset($admin)>]You are a polls admin. <a href="[<$I2_ROOT>]polls/admin">Add, modify, or delete polls</a><br /><br />[</if>]

You have access to the following polls:
<ul>
[<foreach from=$polls item=poll>]
 <li><a href="[<$I2_ROOT>]polls/vote/[<$poll->pid>]">[<$poll->name>]</a></li>
[</foreach>]
</ul>
