<script type="text/javascript" src="[<$I2_ROOT>]www/js/sort.js"></script>
<script type="text/javascript">
<!--
function sort(table, name) {
	var cell = table.parentNode;
	var direction = document.getElementById("order_" + name).value;
	document.getElementById("arrow_" + name).src = "[<$I2_ROOT>]www/pics/" + (direction == 1 ? "uparrow.gif" : "downarrow.gif");
	//switch the sort order
	document.getElementById("order_" + name).value *= -1;
	table = cell.parentNode.parentNode.parentNode;
	index = cell.cellIndex*3-1;
	if (index == 2)
		index = 1;
	sortTable(table, index, direction);
}
// -->
</script>
<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />

<b>[<$poll->name>]</b><br /><br />
<ol class="poll_questions">
[<foreach from=$questions key=qid item=question>]
 <input type="hidden" name="order_total_[<$qid>]" id="order_total_[<$qid>]" value="-1" />
 <input type="hidden" name="order_9_[<$qid>]" id="order_9_[<$qid>]" value="-1" />
 <input type="hidden" name="order_10_[<$qid>]" id="order_10_[<$qid>]" value="-1" />
 <input type="hidden" name="order_11_[<$qid>]" id="order_11_[<$qid>]" value="-1" />
 <input type="hidden" name="order_12_[<$qid>]" id="order_12_[<$qid>]" value="-1" />
 <li>
 [<if $question.answertype == 'free_response'>]
  <a href="[<$I2_ROOT>]polls/results/[<$poll->pid>]/_[<$question.qid>]">[<$question.text>]</a><br />
 [<else>]
  [<$question.text>]<br />
  <table class="results">
   <col /><col class="l" /><col span="3" />
   <col class="l" /><col span="2" />
   <col class="l" /><col span="2" />
   <col class="l" /><col span="2" />
   <col class="l" /><col span="2" /><col span="3" class="l" />
  <thead>
   <tr>
    <th rowspan="2">Answer</th>
    <th colspan="4">Total Votes<img alt="" id="arrow_total_[<$qid>]" src="[<$I2_ROOT>]www/pics/uparrow.gif" onclick="sort(this, 'total_[<$qid>]')" /></th>
    <th colspan="3">9<img alt=""id="arrow_9_[<$qid>]" src="[<$I2_ROOT>]www/pics/uparrow.gif" onclick="sort(this, '9_[<$qid>]')" /></th>
    <th colspan="3">10<img alt=""id="arrow_10_[<$qid>]" src="[<$I2_ROOT>]www/pics/uparrow.gif" onclick="sort(this, '10_[<$qid>]')" /></th>
    <th colspan="3">11<img alt=""id="arrow_11_[<$qid>]" src="[<$I2_ROOT>]www/pics/uparrow.gif" onclick="sort(this, '11_[<$qid>]')" /></th>
    <th colspan="3">12<img alt=""id="arrow_12_[<$qid>]" src="[<$I2_ROOT>]www/pics/uparrow.gif" onclick="sort(this, '12_[<$qid>]')" /></th>
    <th colspan="3">Staff</th>
   </tr>
   <tr>
    <th colspan="2">T</th><th>M</th><th>F</th>
    <th>T</th><th>M</th><th>F</th>
    <th>T</th><th>M</th><th>F</th>
    <th>T</th><th>M</th><th>F</th>
    <th>T</th><th>M</th><th>F</th>
    <th colspan="3">T</th>
   </tr>
  </thead><tbody>
   [<foreach from=$question.answers item=answer>]
    <tr>
     <th>[<$answer.text>]</th>
     <td>[<$answer.votes.T>]</td><td>([<$answer.percent>]%)</td><td>[<$answer.votes.M>]</td><td>[<$answer.votes.F>]</td>
     <td>[<$answer.votes.9T>]</td><td>[<$answer.votes.9M>]</td><td>[<$answer.votes.9F>]</td>
     <td>[<$answer.votes.10T>]</td><td>[<$answer.votes.10M>]</td><td>[<$answer.votes.10F>]</td>
     <td>[<$answer.votes.11T>]</td><td>[<$answer.votes.11M>]</td><td>[<$answer.votes.11F>]</td>
     <td>[<$answer.votes.12T>]</td><td>[<$answer.votes.12M>]</td><td>[<$answer.votes.12F>]</td>
     <td colspan="3">[<$answer.votes.staffT>]</td>
    </tr>
   [</foreach>]
  </tbody><tfoot>
   <tr>
    <th>Total</th>
    <td>[<$question.total.T>]</td><td></td><td>[<$question.total.M>]</td><td>[<$question.total.F>]</td>
    <td>[<$question.total.9T>]</td><td>[<$question.total.9M>]</td><td>[<$question.total.9F>]</td>
    <td>[<$question.total.10T>]</td><td>[<$question.total.10M>]</td><td>[<$question.total.10F>]</td>
    <td>[<$question.total.11T>]</td><td>[<$question.total.11M>]</td><td>[<$question.total.11F>]</td>
    <td>[<$question.total.12T>]</td><td>[<$question.total.12M>]</td><td>[<$question.total.12F>]</td>
    <td colspan="3">[<$question.total.staffT>]</td>
   </tr>
  </tfoot>
  </table>
  [<if $question.answertype == 'approval'>][<* Ironically, split-vote DOES sum to 100% *>]
    <span class="note">Note: The percentages do not sum to 100% because this is an approval question.</span><br />
  [</if>]
 [</if>]
 [<if $question.voters == 1>]1 person[<else>][<$question.voters>] people[</if>] voted on this question.<br />
 </li>
[</foreach>]
</ol>
