<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<strong>[<$poll->name|escape:"html">]</strong><br /><br />

[<foreach from=$errors item=error>]
 <strong>[<$error>]</strong><br /><br />
[</foreach>]

[<if !$avail>]
 <font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font><br /><br />
[<elseif $has_voted>]
 <strong><em>Thanks for voting in this poll!  You may change your vote until the poll closes.</em></strong><br /><br />
[</if>]

[<$poll->introduction|escape:"html">]<br /><br />

<form method="post" action="[<if $avail>][<$I2_ROOT>]polls/vote/[<$poll->pid>][</if>]" class="boxform">
<input type="hidden" name="polls_vote_form" value="vote">
[<foreach from=$poll->questions item=question>]
 [<if count($question->answers) != 0 || $question->answertype == 'freeresponse'>]
  <b>[<$question->r_qid>].</b> [<$question->question>]<br />
  [<if $question->answertype == 'checkbox' || $question->answertype == 'split_approval'>]
   [<if $question->maxvotes == 0>]
    You may select as many options as you wish.
   [<else>]
    You may select [<$question->maxvotes>] option[<if $question->maxvotes != 1>]s[</if>].
   [</if>]
   [<if $question->answertype == 'split_approval'>]
    Your vote will be evenly split between the responses you select.
   [</if>]<br />
  [</if>]
 [</if>]
 [<if $question->answertype == 'radio'>]
  <input type="radio" name="[<$question->qid>]" value="[<$question->qid>]000" [<if $question->user_voted_for(0)>]CHECKED [</if>]/>Clear Vote<br />
  [<foreach from=$question->answers key=aid item=answer>]
   <input type="radio" name="[<$question->qid>]" value="[<$aid>]" [<if $question->user_voted_for($aid)>]CHECKED [</if>]/>[<$answer>]<br />
  [</foreach>]
 [<elseif $question->answertype == 'checkbox' || $question->answertype == 'split_approval'>]
  [<foreach from=$question->answers key=aid item=answer>]
   <input type="checkbox" name="[<$aid>]" [<if $question->user_voted_for($aid)>]CHECKED [</if>]/>[<$answer>]<br />
  [</foreach>]
 [<elseif $question->answertype == 'freeresponse'>]
  [<assign var="aid" value="`$question->qid`001">]
  <textarea rows="5" cols="80" name="[<$question->qid>]">[<$question->user_voted_for($aid)>]</textarea><br />
 [</if>]
  <br />
[</foreach>]
[<if $avail>]
 <input type="submit" value="Vote" name="vote" />
[<else>]
 <font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font>
[</if>]
</form>
