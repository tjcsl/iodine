<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<script type="text/javascript">
function checklength(area,error,count) {
	error = document.getElementById(error);
	var lengthleft = count - area.value.length - area.value.split("\n").length +1;
	if(lengthleft >=0) {
		error.innerHTML ="(" + (lengthleft) + " characters left)";
	} else {
		error.innerHTML ="<font color='red' style='text-weight: strong'>Over by " + (-lengthleft) + " characters! Extra characters will be truncated.</font>";
	}
}
function checklength_andcopy(area,error,count,to) {
	error = document.getElementById(error);
	var lengthleft = count - area.value.length - area.value.split("\n").length +1;
	if(lengthleft >=0) {
		error.innerHTML ="(" + (lengthleft) + " characters left)";
	} else {
		error.innerHTML ="<font color='red' style='text-weight: strong'>Over by " + (-lengthleft) + " characters! Extra characters will be truncated.</font>";
	}
	to=document.getElementById(to);
	to.value=area.value;
}
</script>
<strong>[<$poll->name>]</strong><br /><br />

[<if !$avail>]
 <font color="red">This poll is not currently available to you.  You may not have permissions to vote in this poll, the current date and time are not within the polling time window, or this poll has been disabled for other reasons.</font><br /><br />
[<elseif $has_voted>]
 <strong><em>Thanks for voting in this poll!  You may change your vote until the poll closes.</em></strong><br /><br />
[</if>]

[<$poll->introduction>]<br /><br />

<form method="post" action="[<if $avail>][<$I2_ROOT>]polls/vote/[<$poll->pid>][</if>]" class="boxform">
<input type="hidden" name="polls_vote_form" value="vote" />
<ol class="poll_questions">
[<foreach from=$poll->questions item=q>]
  <li>
 [<if count($q->answers) != 0 || $q->answertype == 'free_response' || $q->answertype == 'short_response' >]
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
 [<elseif $q->answertype == 'standard_election'>]
  <input type="radio" name="[<$q->qid>]" value="-1" [<if $q->user_voted_for(0)>]checked="checked" [</if>]/><em>Clear Vote</em><br />
  [<foreach from=$q->answers_randomsort key=aid item=answer>]
   <input type="radio" name="[<$q->qid>]" value="[<$aid>]" [<if $q->user_voted_for($aid)>]checked="checked" [</if>]/>[<$answer>]<br />
  [</foreach>]
 [<elseif $q->answertype == 'approval' || $q->answertype == 'split_approval'>]
  [<foreach from=$q->answers key=aid item=answer>]
   <input type="checkbox" name="[<$q->qid>][]" value="[<$aid>]" [<if $q->user_voted_for($aid)>]checked="checked" [</if>]/>[<$answer>]<br />
  [</foreach>]
 [<elseif $q->answertype == 'free_response'>]
  [<assign var="aid" value="0">]
  <div id="error_[<$q->qid>]">(1500 characters left)</div> 
  <textarea id="textarea_[<$q->qid>]" rows="5" cols="80" name="[<$q->qid>]" onkeyup="checklength(this,'error_[<$q->qid>]',10000)">[<if $q->user_voted_for(0)>][<$q->get_response()>][</if>]</textarea><br />
  <script type="text/javascript">
	checklength(document.getElementById('textarea_[<$q->qid>]'),'error_[<$q->qid>]',10000);
  </script>
 [<elseif $q->answertype == 'short_response'>]
  [<assign var="aid" value="0">]
  <div id="error_[<$q->qid>]">(1000 characters left)</div> 
  <input type="text" id="input_[<$q->qid>]" name="[<$q->qid>]" onkeyup="checklength(this,'error_[<$q->qid>]',1000)" value="[<if $q->user_voted_for(0)>][<$q->get_response()>][</if>]" /><br />
  <script type="text/javascript">
	checklength(document.getElementById('input_[<$q->qid>]'),'error_[<$q->qid>]',1000);
  </script>
 [<elseif $q->answertype=='standard_other'>]
  [<assign var="oneselected" value="0">]
  <input type="radio" name="[<$q->qid>]" value="" [<if $q->get_response()=="">][<assign var="oneselected" value="1">]checked="checked" [</if>]/><em>Clear Vote</em><br />
  [<foreach from=$q->answers key=aid item=answer>]
   <input type="radio" name="[<$q->qid>]" value="[<$answer>]" [<if $q->get_response()==$answer>][<assign var="oneselected" value="1">]checked="checked" [</if>]/>[<$answer>]<br />
  [</foreach>]
  <input type="radio" name="[<$q->qid>]" id="radioother_[<$q->qid>]" value="[<$q->get_response()>]" [<if $oneselected=='0'>]checked="checked"[</if>]>
   Other: <input type="text" name="other_[<$q->qid>]" value="[<if $oneselected=='0'>][<$q->get_response()>][</if>]" onkeyup="checklength_andcopy(this,'error_[<$q->qid>]',100,'radioother_[<$q->qid>]')"/>
   <div id="error_[<$q->qid>]">(100 characters left)</div>
  </input>
  <script type="text/javascript">
	checklength(document.getElementById('other_[<$q->qid>]'),'error_[<$q->qid>]',1000);
  </script>
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
