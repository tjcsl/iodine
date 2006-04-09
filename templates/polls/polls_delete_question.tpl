<a href="[<$I2_ROOT>]polls/edit/[<$pid>]">Edit Poll</a><br /><br />
[<if isset($deleted)>]
 The question has been deleted.
[<else>]
 You are about to delete a poll question. Are you really, entirely sure about this?
 <form method="post" action="[<$I2_ROOT>]polls/delete/[<$pid>]/[<$qid>]" class="boxform">
 <input type="hidden" name="polls_delete_form" value="delete_question" />
 <input type="submit" value="Delete" name="submit" />
 </form>
[</if>]
