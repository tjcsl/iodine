<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<b>[<$pollname>]</b><br /><br />

[<foreach from=$questions item=question>]
 [<$question.text>]<br />
 <table border="1">
  <tr><th rowspan="2">Answer</th>
  <th colspan="3">Total Votes</th>
  <th colspan="3">9</th>
  <th colspan="3">10</th>
  <th colspan="3">11</th>
  <th colspan="3">12</th>
  <th colspan="3">Staff</th>
  </tr>
  <tr>
  <th>T</th><th>M</th><th>F</th>
  <th>T</th><th>M</th><th>F</th>
  <th>T</th><th>M</th><th>F</th>
  <th>T</th><th>M</th><th>F</th>
  <th>T</th><th>M</th><th>F</th>
  <th colspan="3">T</th>
  </tr>
  [<foreach from=$question.answers item=answer>]
  <tr><td>[<$answer.text>]</td>
  <td>[<$answer.votes.T>] ([<$answer.percent.T>]%)</td><td>[<$answer.votes.M>]</td><td>[<$answer.votes.F>]</td>
  <td>[<$answer.votes.9T>]</td><td>[<$answer.votes.9M>]</td><td>[<$answer.votes.9F>]</td>
  <td>[<$answer.votes.10T>]</td><td>[<$answer.votes.10M>]</td><td>[<$answer.votes.10F>]</td>
  <td>[<$answer.votes.11T>]</td><td>[<$answer.votes.11M>]</td><td>[<$answer.votes.11F>]</td>
  <td>[<$answer.votes.12T>]</td><td>[<$answer.votes.12M>]</td><td>[<$answer.votes.12F>]</td>
  <td colspan="3">[<$answer.votes.staffT>]</td>
  </tr>
  [</foreach>]
  <tr><td>Total</td>
  <td>[<$question.total.T>]</td><td>[<$question.total.M>]</td><td>[<$question.total.F>]</td>
  <td>[<$question.total.9T>]</td><td>[<$question.total.9M>]</td><td>[<$question.total.9F>]</td>
  <td>[<$question.total.10T>]</td><td>[<$question.total.10M>]</td><td>[<$question.total.10F>]</td>
  <td>[<$question.total.11T>]</td><td>[<$question.total.11M>]</td><td>[<$question.total.11F>]</td>
  <td>[<$question.total.12T>]</td><td>[<$question.total.12M>]</td><td>[<$question.total.12F>]</td>
  <td colspan="3">[<$question.total.staffT>]</td>
  </tr>
 </table>
 [<if $question.voters == 1>]1 person[<else>][<$question.voters>] people[</if>] voted on this question.<br />
 [<if isset($question.approval)>]Note: The percentages do not sum to 100% because this is an approval question.[</if>]<br /><br />
[</foreach>]
