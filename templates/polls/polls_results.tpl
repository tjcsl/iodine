<script type="text/javascript">
<!--
function value(row) {
	var cell = row.cells.item(value.index);
	return parseInt(cell.firstChild.nodeValue);
}
function compare(row1, row2) {
	return (value(row1)-value(row2))*value.direction;
}

function sort(table, direction) {
	var cell = table.parentNode;
	value.direction = direction;
	table = cell.parentNode.parentNode.parentNode;
	var body = table.tBodies.item(0);
	var rows = new Array(body.rows.length);
	for (var i=0;i < body.rows.length; i++) {
		rows[i] = body.rows.item(i);
	}
	value.index = cell.cellIndex*3-1;
	if (value.index == 2)
		value.index = 1;
	rows.sort(compare);
	for (var i=0;i<rows.length;i++) {
		body.appendChild(rows[i]);
	}
}
// -->
</script>
<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<b>[<$pollname>]</b><br /><br />

[<foreach from=$questions item=question>]
 [<$question.r_qid>].
 [<if $question['answertype'] == 'freeresponse'>]
  <a href="[<$I2_ROOT>]polls/results/[<$pid>]/[<$question.qid>]">[<$question.text>]</a><br /><br /><br />
 [<else>]
  [<$question.text>]<br />
  <table class="results">
   <col><col class="l"><col span="3">
   <col class="l"><col span="2">
   <col class="l"><col span="2">
   <col class="l"><col span="2">
   <col class="l"><col span="2"><col span="3" class="l">
  <thead>
   <tr>
    <th rowspan="2">Answer</th>
    <th colspan="4"><img src="[<$I2_ROOT>]www/pics/down.gif" onclick="sort(this, -1)">Total Votes<img src="[<$I2_ROOT>]www/pics/up.gif" onclick="sort(this, 1)"></th>
    <th colspan="3"><img src="[<$I2_ROOT>]www/pics/down.gif" onclick="sort(this, -1)">9<img src="[<$I2_ROOT>]www/pics/up.gif" onclick="sort(this, 1)"></th>
    <th colspan="3"><img src="[<$I2_ROOT>]www/pics/down.gif" onclick="sort(this, -1)">10<img src="[<$I2_ROOT>]www/pics/up.gif" onclick="sort(this, 1)"></th>
    <th colspan="3"><img src="[<$I2_ROOT>]www/pics/down.gif" onclick="sort(this, -1)">11<img src="[<$I2_ROOT>]www/pics/up.gif" onclick="sort(this, 1)"></th>
    <th colspan="3"><img src="[<$I2_ROOT>]www/pics/down.gif" onclick="sort(this, -1)">12<img src="[<$I2_ROOT>]www/pics/up.gif" onclick="sort(this, 1)"></th>
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
     <td>[<$answer.votes.T>]</td><td>([<$answer.percent.T>]%)</td><td>[<$answer.votes.M>]</td><td>[<$answer.votes.F>]</td>
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
  [<if $question.voters == 1>]1 person[<else>][<$question.voters>] people[</if>] voted on this question.<br />
  [<if isset($question.approval)>]Note: The percentages do not sum to 100% because this is an approval question.[</if>]<br /><br />
 [</if>]
[</foreach>]
