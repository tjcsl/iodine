<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/edit/[<$poll->pid>]" class="boxform">
<input type="hidden" name="poll_edit_form" value="poll" />
Name: <input type="text" name="name" value="[<$poll->name>]" /><br />
Start date/time:<input type="text" name="startdt" value="[<$poll->startdt>]" /><br />
End date/time:<input type="text" name="enddt" value="[<$poll->enddt>]" /><br />
<input type="checkbox" name="visible" [<if $poll->visible>]CHECKED [</if>]/>Is Visible<br />
Groups: <input type="text" name="groups" value="[<foreach from=$poll->groupids item=gid>][<$gid>],[</foreach>]" /><br />
Introduction:<br />
<textarea rows="2" cols="50" name="intro">[<$poll->introduction>]</textarea><br />
<input type="submit" value="Update" name="submit" />
</form><br />

Questions:<br />
[<foreach from=$poll->questions item=question>]
 [<$question->qid>]. [<$question->question>] ([<if $question->maxvotes == 0>]No vote limit[<else>]Pick [<$question->maxvotes>][</if>]) (<a href="[<$I2_ROOT>]polls/edit/[<$poll->pid>]/[<$question->qid>]">edit</a>) (<a href="[<$I2_ROOT>]polls/delete/[<$poll->pid>]/[<$question->qid>]">delete</a>)<br />
 <ul>
 [<foreach from=$question->answers item=answer>]
  <li>[<$answer>]</li>
 [</foreach>]
 </ul>
 <br />
[</foreach>]

<a href="[<$I2_ROOT>]polls/add/[<$poll->pid>]">Add a question</a>
