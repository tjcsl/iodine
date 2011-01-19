[<include file="eighth/header.tpl">]
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
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$oc.uid>]">[<$oc.username>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.fromaid>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.fromaidname>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.toaid>]">[<$oc.toaid>]</a></td>
<td><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.toaidname>]</a></td>
</tr>
[</foreach>]
</table>
