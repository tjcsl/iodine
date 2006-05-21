<a href="[<$I2_ROOT>]polls/edit/[<$question->pid>]">Edit Poll</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/edit/[<$question->pid>]" class="boxform">
<input type="hidden" name="poll_edit_form" value="question" />
<input type="hidden" name="qid" value="[<$question->qid>]" />
<input type="submit" value="Update" name="submit">
Question: <textarea rows="5" cols="80" name="question">[<$question->question|escape:"html">]</textarea><br />
</form>
Max Votes (0 for unlimited): <input type="text" name="maxvotes" value="[<$question->maxvotes>]" /><br />
[<foreach from=$question->answers key=aid item=answer>]
Answer: [<$answer>] <a href="[<$I2_ROOT>]polls/edit/[<$pid>]/[<$qid>]/[<$aid>]">Edit</a> <a href="[<$I2_ROOT>]polls/delete/[<$pid>]/[<$qid>]/[<$aid>]">Remove</a><br />
[</foreach>]<br />
<a href="[<$I2_ROOT>]polls/add/[<$pid>]/[<$qid>]">Add an answer</a>
</form>

