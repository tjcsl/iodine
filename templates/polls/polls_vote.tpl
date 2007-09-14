<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<strong>[<$poll->name|escape:"html">]</strong><br /><br />

[<if !$avail>]
 <font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font><br /><br />
[<elseif $has_voted>]
 <strong><em>Thanks for voting in this poll!  You may change your vote until the poll closes.</em></strong><br /><br />
[</if>]

[<$poll->introduction|escape:"html">]<br /><br />

<form method="post" action="[<if $avail>][<$I2_ROOT>]polls/vote/[<$poll->pid>][</if>]" class="boxform">
<input type="hidden" name="polls_vote_form" value="vote">
<ol class="poll_questions">
[<foreach from=$poll->questions item=q>]
  <li>
 [<if count($q->answers) != 0 || $q->answertype == 'free_response'>]
  [<$q->question>]<br />
  [<if $q->answertype == 'approval' || $q->answertype == 'split_approval'>]
   [<if $q->maxvotes == 0>]
    You may select as many options as you wish.
   [<else>]
    You may select [<$q->maxvotes>] option[<if $q->maxvotes != 1>]s[</if>].
   [</if>]
   [<if $q->answertype == 'split_approval'>]
    Your vote will be evenly split between the responses you select.
   [</if>]<br />
  [</if>]
 [</if>]
 [<if $q->answertype == 'standard'>]
  <input type="radio" name="[<$q->qid>]" value="-1" [<if $q->user_voted_for(0)>]checked="checked" [</if>]/><em>Clear Vote</em><br />
  [<foreach from=$q->answers key=aid item=answer>]
   <input type="radio" name="[<$q->qid>]" value="[<$aid>]" [<if $q->user_voted_for($aid)>]checked="checked" [</if>]/>[<$answer>]<br />
  [</foreach>]
 [<elseif $q->answertype == 'approval' || $q->answertype == 'split_approval'>]
  [<foreach from=$q->answers key=aid item=answer>]
   <input type="checkbox" name="[<$q->qid>][]" value="[<$aid>]" [<if $q->user_voted_for($aid)>]checked="checked" [</if>]/>[<$answer>]<br />
  [</foreach>]
 [<elseif $q->answertype == 'free_response'>]
  [<assign var="aid" value="0">]
  <textarea rows="5" cols="80" name="[<$q->qid>]">[<if $q->user_voted_for(0)>][<$q->get_response()>][</if>]</textarea><br />
 [</if>]
  </li>
[</foreach>]
</ol>
[<if $avail>]
 <input type="submit" value="Vote" name="vote" />
[<else>]
 <font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font>
[</if>]
</form>
