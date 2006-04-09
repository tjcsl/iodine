<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<b>[<$pollname>]</b><br /><br />

[<foreach from=$questions item=question>]
 [<$question.text>]<br />
 <table border="1">
  [<foreach from=$question.answers item=answer>]
  <tr><td>[<$answer.text>]</td><td>[<$answer.votes>] votes ([<$answer.percent>]%)</td></tr>
  [</foreach>]
  <tr><td>Total</td><td>[<$question.total>]</td></tr>
 </table>
 [<if $question.voters == 1>]1 person[<else>][<$question.voters>] people[</if>] voted on this question.<br />
 [<if isset($question.approval)>]Note: The percentages do not sum to 100% because this is an approval question.[</if>]<br /><br />
[</foreach>]
