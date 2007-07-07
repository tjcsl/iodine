[<assign var=pid value=$poll->pid>]
<script type="text/javascript" src="[<$I2_ROOT>]www/js/polls.js"></script>

<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/edit/[<$poll->pid>]" class="boxform">
<input type="hidden" name="poll_edit_form" value="poll" />
Name: <input type="text" name="name" value="[<$poll->name>]" /><br />
Start date/time:<input type="text" name="startdt" value="[<$poll->startdt>]" /><br />
End date/time:<input type="text" name="enddt" value="[<$poll->enddt>]" /><br />
<input type="checkbox" name="visible" [<if $poll->visible>]checked="checked" [</if>]/> Visible<br />
<table id="groups_table" cellpadding="0">
 <tr>
  <td>Groups:</td>
  <td>
  [<if count($poll->groups) == 0>]
    <select id="groups" class="groups_list" name="groups[]">
    [<foreach from=$groups item=group>]
     <option value="[<$group->gid>]">[<$group->name>]</option>
    [</foreach>]
    </select>
    </td><td>&nbsp;</td>
    </tr><tr><td>&nbsp;</td><td>
  [<else>]
    [<foreach from=$poll->groups key=gid item=perms>]
      <select id="groups" class="groups_list" name="groups[]">
      [<foreach from=$groups item=g>]
        <option value="[<$g->gid>]"[<if $g->gid == $gid>] selected="selected"[</if>]>[<$g->name>]</option>
      [</foreach>]
      </select>
    </td><td><a onclick="deleteGroup(event)" href="">remove</a></td>
    </tr><tr>
    <td>&nbsp;</td><td>
    [</foreach>]
  [</if>]
  <a href="#" onclick="addGroup(); return false">Add another group</a></td>
  <td>&nbsp;</td>
 </tr>
</table>
Introduction:<br />
<textarea rows="2" cols="50" name="intro">[<$poll->introduction>]</textarea><br /><br />

Questions:<br />
<table id="poll_question_list">
<thead>
  <tr><th>Id</th><th>Question</th><th>Type</th><th>Vote limit</th></tr>
</thead><tbody>
[<foreach from=$poll->questions item=q>]
  <tr><th rowspan="2">[<$q->qid>]<input name="question[]" value="[<$q->qid>]" type="hidden" /></th>
  <td><input name="q_[<$q->qid>]_name" value="[<$q->question>]" /></td>
  <td><select name="q_[<$q->qid>]_type">
[<foreach from=$types item=val key=k>]
    <option value="[<$k>]" [<if $q->answertype == $k>]selected="selected"[</if>]>[<$val>]</option>
[</foreach>]
  </select></td>
  <td><input name="q_[<$q->qid>]_lim" maxlength="3" size="3" value="[<$q->maxvotes>]" /></td>
  <td><a href="[<$I2_ROOT>]polls/edit/[<$pid>]/delq/[<$q->qid>]"><img src="[<$I2_ROOT>]www/pics/close.gif" onclick="deleteRow(event)"/></a></td>
  </tr><tr><td colspan=4">
    <ul>[<foreach from=$q->answers item=ans key=aid>]<li>
      <input type="hidden" name="a_[<$q->qid>][]" value="[<$aid>]" />
      <a href="[<$I2_ROOT>]polls/edit/[<$pid>]/dela/[<$q->qid>]/[<$aid>]" onclick="deleteAnswer(event)">Delete</a>&nbsp;&nbsp;&nbsp;<input name="a_[<$q->qid>]_[<$aid>]" value="[<$ans>]" />
    </li>[</foreach>]<li>
      <a href="[<$I2_ROOT>]polls/edit/[<$pid>]/adda/[<$q->qid>]" onclick="addAnswer(event)">Add an answer choice</a>
    </li></ul>
  </td></tr>
[</foreach>]
</tbody>
</table>
<a onclick="addQuestion(event)" href="[<$I2_ROOT>]polls/edit/[<$pid>]/addq">Add a question</a><br />
<input type="submit" value="Update" name="submit" />
</form>
