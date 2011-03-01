[<include file="eighth/header.tpl">]
<form action="[<$I2_ROOT>]eighth/postsigns" method='get'>
<input type='hidden' name='arequest' value='daterange'/>
Results from <input type='text' value="[<$starttime>]" name='starttime'/> to <input type='text' value='[<$endtime>]' name='endtime'/> <input type='submit' value='Go'/><br />
</form>
<table>
<tr>
<th>Time</th>
<th>Sponsor</th>
<th>Student</th>
<th colspan=2>From</th>
<th colspan=2>To</th>
</tr>
[<foreach from=$data item=oc>]
<tr>
<td>[<$oc.time>]</td>
<td>[<$oc.sponsors>]</td>
<td>[<$oc.username>]</td>
<td>[<$oc.fromaid>]</td>
<td>[<$oc.fromaidname>]</td>
<td>[<$oc.toaid>]</td>
<td>[<$oc.toaidname>]</td>
[<*<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$oc.uid>]">[<$oc.username>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.fromaid>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.fromaidname>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.toaid>]">[<$oc.toaid>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.toaidname>]</a></td>*>]
</tr>
[</foreach>]
</table>
