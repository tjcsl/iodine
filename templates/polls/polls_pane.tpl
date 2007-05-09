[<if isset($admin)>]<a href="[<$I2_ROOT>]polls/add">Add a new poll</a>[</if>]
<br /><br />
<table class="polls">
  <tr><td colspan="5"><b>Open Polls</b></td></tr>
[<foreach from=$open item=poll>]
  <tr><th>[<$poll->name>]</th>
    <td><a href="[<$I2_ROOT>]polls/vote/[<$poll->pid>]">Vote!</a></td>
    [<if isset($admin)>]
    <td><a href="[<$I2_ROOT>]polls/results/[<$poll->pid>]">View results</a></td>
    <td><a href="[<$I2_ROOT>]polls/export_csv/[<$poll->pid>]">(as CSV)</a></td>
    <td><a href="[<$I2_ROOT>]polls/edit/[<$poll->pid>]">Edit</a></td>
    <td><a href="[<$I2_ROOT>]polls/delete/[<$poll->pid>]">Delete</a></td>
    [</if>]
  </tr>
[</foreach>]
[<if isset($admin)>]
  <tr><td colspan="5"><b>Finished polls</b></td></tr>
[<foreach from=$finished item=poll>]
  <tr><th>[<$poll->name>]</th>
    <td></td>
    <td><a href="[<$I2_ROOT>]polls/results/[<$poll->pid>]">View results</a></td>
    <td><a href="[<$I2_ROOT>]polls/export_csv/[<$poll->pid>]">(as CSV)</a></td>
    <td><a href="[<$I2_ROOT>]polls/edit/[<$poll->pid>]">Edit</a></td>
    <td><a href="[<$I2_ROOT>]polls/delete/[<$poll->pid>]">Delete</a></td>
  </tr>
[</foreach>]
  <tr><td colspan="5"><b>Unstarted polls</b></td></tr>
[<foreach from=$unstarted item=poll>]
  <tr><th>[<$poll->name>]</th>
    <td></td>
    <td><a href="[<$I2_ROOT>]polls/results/[<$poll->pid>]">View results</a></td>
    <td><a href="[<$I2_ROOT>]polls/export_csv/[<$poll->pid>]">(as CSV)</a></td>
    <td><a href="[<$I2_ROOT>]polls/edit/[<$poll->pid>]">Edit</a></td>
    <td><a href="[<$I2_ROOT>]polls/delete/[<$poll->pid>]">Delete</a></td>
  </tr>
[</foreach>]
[</if>]
</table>
