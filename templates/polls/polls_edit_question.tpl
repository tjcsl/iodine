<a href="[<$I2_ROOT>]polls/edit/[<$question->pid>]">Edit Poll</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/edit/[<$question->pid>]" class="boxform">
<input type="hidden" name="poll_edit_form" value="question" />
<input type="hidden" name="qid" value="[<$question->qid>]" />
Question: <input type="text" name="question" value="[<$question->question>]" /><br />
Max Votes (0 for unlimited): <input type="text" name="maxvotes" value="[<$question->maxvotes>]" /><br />
<textarea rows="5" cols="50" name="answers">[<foreach from=$question->answers item=answer>][<$answer>]

[</foreach>]</textarea><br />
<input type="submit" value="Update" name="submit">
</form>

