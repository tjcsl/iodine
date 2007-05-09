<script type="text/javascript" src="[<$I2_ROOT>]www/js/news_groups.js"></script>
<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/edit/[<$poll->pid>]" class="boxform">
<input type="hidden" name="poll_edit_form" value="poll" />
Name: <input type="text" name="name" value="[<$poll->name>]" /><br />
Start date/time:<input type="text" name="startdt" value="[<$poll->startdt>]" /><br />
End date/time:<input type="text" name="enddt" value="[<$poll->enddt>]" /><br />
<input type="checkbox" name="visible" [<if $poll->visible>]CHECKED [</if>]/>Is Visible<br />
 <table id="groups_table" cellpadding="0">
  <tr>
   <td>Groups:</td>
   <td>
    [<if count($poll->groups) == 0>]
    <select id="groups" class="groups_list" name="add_groups[]">
     [<foreach from=$groups item=group>]
      <option value="[<$group->gid>]">[<$group->name>]</option>
     [</foreach>]
    </select>
    [<else>]
    <select id="groups" class="groups_list" name="add_groups[]">
     [<foreach from=$groups item=group>]
      <option value="[<$group->gid>]"[<if $group->gid == $poll->groups[0]->gid>] selected[</if>]>[<$group->name>]</option>
     [</foreach>]
    </select>
    [</if>]
   </td>
   <td>&nbsp;</td>
  </tr>
  <tr>
   <td>&nbsp;</td>
   <td><a href="#" onclick="addGroup(); return false">Add another group</a></td>
   <td>&nbsp;</td>
  </tr>
 </table>
 <script type="text/javascript">
  [<section name=i loop=$poll->groups start=1>]
   addGroup([<$poll->groups[i]->gid>]);
  [</section>]
 </script>
Introduction:<br />
<textarea rows="2" cols="50" name="intro">[<$poll->introduction>]</textarea><br />
<input type="submit" value="Update" name="submit" />
</form><br />

Questions:<br />
[<foreach from=$poll->questions item=question>]
 [<$question->r_qid>]. [<$question->question>] (qid:[<$question->qid>])(type:[<$question->answertype>])[<if $question->answertype == 'checkbox'>]([<if $question->maxvotes == 0>]No vote limit[<else>]Pick [<$question->maxvotes>][</if>])[</if>] (<a href="[<$I2_ROOT>]polls/edit/[<$poll->pid>]/[<$question->qid>]">edit</a>) (<a href="[<$I2_ROOT>]polls/delete/[<$poll->pid>]/[<$question->qid>]">delete</a>)<br />
[<if $question->answertype != 'freeresponse'>]
 <ul>
 [<foreach from=$question->answers item=answer>]
  <li>[<$answer>]</li>
 [</foreach>]
 </ul>
 <br />
[</if>]
[</foreach>]
<a href="[<$I2_ROOT>]polls/add/[<$poll->pid>]">Add a question</a>
