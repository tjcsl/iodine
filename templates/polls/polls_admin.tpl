<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

All polls:
<ul>
[<foreach from=$polls item=poll>]
 <li><a href="[<$I2_ROOT>]polls/results/[<$poll->pid>]">[<$poll->name>]</a> (<a href="[<$I2_ROOT>]polls/edit/[<$poll->pid>]">edit</a>) (<a href="[<$I2_ROOT>]polls/delete/[<$poll->pid>]">delete</a>)</li>
[</foreach>]
</ul>

<a href="[<$I2_ROOT>]polls/add">Add a new poll</a>
