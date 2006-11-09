<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<strong>[<$poll->name|escape:"html">]</strong><br /><br />

[<foreach from=$errors item=error>]
 <strong>[<$error>]</strong><br /><br />
[</foreach>]

[<$poll->introduction|escape:"html">]<br /><br />
[<if !$avail>]
<font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font>
[</if>]

<form method="post" action="[<if $avail>][<$I2_ROOT>]polls/vote/[<$poll->pid>][</if>]" class="boxform">
<input type="hidden" name="polls_vote_form" value="vote">
[<foreach from=$poll->questions item=question>]
 <b>[<$question->r_qid>].</b> [<$question->question>]<br />
 [<if $question->maxvotes == 0>]
  You may select as many options as you wish.<br />
 [<else>]
  You may select [<$question->maxvotes>] option[<if $question->maxvotes != 1>]s[</if>].<br />
 [</if>]
 [<*[<if $question->type == 'standard'>]<input type="radio" name="[<$question->qid>]" value="[<$question->qid>]000" [<if $question->user_voted_for(0)>]CHECKED [</if>]/>Clear Vote<br />[</if>]*>]
 [<if $question->maxvotes == 1>]<input type="radio" name="[<$question->qid>]" value="[<$question->qid>]000" [<if $question->user_voted_for(0)>]CHECKED [</if>]/>Clear Vote<br />[</if>]
 [<foreach from=$question->answers key=aid item=answer>]
 [<*<input [<if $question->type == 'standard'>]type="radio" name="[<$question->qid>]" value="[<$aid>]"[<elseif $question->type=='approval'>]type="checkbox" name="[<$aid>]"[<else>]UNIMPLEMENTED[</if>] [<if $question->user_voted_for($aid)>]CHECKED [</if>]/>[<$answer>]<br />*>]
 <input [<if $question->maxvotes == 1>]type="radio" name="[<$question->qid>]" value="[<$aid>]"[<else>]type="checkbox" name="[<$aid>][</if>]" [<if $question->user_voted_for($aid)>]CHECKED [</if>]/>[<$answer>]<br />
 [</foreach>]
 <br />
[</foreach>]
[<if $avail>]<input type="submit" value="Vote" name="vote" />
[<else>]<font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font>
[</if>]
</form>
