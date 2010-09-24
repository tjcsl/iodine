<a href="[<$I2_ROOT>]podcasts">Podcasts Home</a><br /><br />

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
</script>
<strong>[<$podcast->name>]</strong><br /><br />

[<if !$avail>]
 <font color="red">This podcast is not currently available to you.  You may not have permissions to view this podcast, the current date and time are not within the podcasting time window, or this podcast has been disabled for other reasons.</font><br /><br />
[<elseif $has_voted>]
 <strong><em>Thanks for responding to this podcast!  You may change your responses until the podcast closes.</em></strong><br /><br />
[</if>]

<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/dQw4w9WgXcQ?fs=1&amp;hl=en_US"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/dQw4w9WgXcQ?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object><br />

[<$podcast->introduction>]<br /><br />

<form method="post" action="[<if $avail>][<$I2_ROOT>]podcasts/vote/[<$podcast->pid>][</if>]" class="boxform">
<input type="hidden" name="podcasts_vote_form" value="vote" />
<ol class="podcast_questions">
[<foreach from=$podcast->questions item=q>]
  <li>
 [<if count($q->answers) != 0 || $q->answertype == 'free_response' || $q->answertype == 'short_response'>]
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
  <div id="error_[<$q->qid>]">(10000 characters left)</div> 
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
 [</if>]
  </li>
[</foreach>]
</ol>
[<if $avail>]
 <input type="submit" value="Respond" name="vote" />
[<else>]
 <font color="red">This podcast is not currently available to you.  You may not have permissions to view this podcast, the current date and time are not within the podcasting time window, or this podcast has been disabled for other reasons.</font>
[</if>]
</form>
