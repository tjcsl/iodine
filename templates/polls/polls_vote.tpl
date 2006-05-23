<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<b>[<$poll->name>]</b><br /><br />

[<$poll->introduction>]<br /><br />

<form method="post" action="[<$I2_ROOT>]polls/vote/[<$poll->pid>]" class="boxform">
<input type="hidden" name="polls_vote_form" value="vote">
[<foreach from=$poll->questions item=question>]
 [<$question->question>]<br />
 [<if $question->type == 'standard'>]<input type="radio" name="[<$question->qid>]" value="[<$question->qid>]000" [<if $question->user_voted_for(0)>]CHECKED [</if>]/>Clear Vote<br />[</if>]
 [<foreach from=$question->answers key=aid item=answer>]
 <input [<if $question->type == 'standard' || ($question->type == 'approval' && $question->maxvotes == 1)>]type="radio" name="[<$question->qid>]" value="[<$aid>]"[<elseif $question->type=='approval'>]type="checkbox" name="[<$aid>]"[<else>]/>UNIMPLEMENTED[</if>] [<if $question->user_voted_for($aid)>]CHECKED [</if>]/>[<$answer>]<br />
 [</foreach>]
 <br />
[</foreach>]
<input type="submit" value="Vote" name="vote" />
</form>
