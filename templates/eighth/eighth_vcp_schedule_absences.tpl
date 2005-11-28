[<include file="eighth/eighth_header.tpl">]
[<php>] $this->_tpl_vars['count'] = count($this->_tpl_vars['absences']); [</php>]
Absence information for ([<$uid>]): [<$count>] absence[<if $count != 1 >]s[</if>]<br /><br />
<table cellspacing="0" cellpadding="0" style="padding: 0; spacing: 0; border: none">
	<tr>
		<th style="padding: 0px 10px;">Date</th>
		<th style="padding: 0px 10px;">Activity</th>
		<th style="padding: 0px 10px;">Activity ID</th>
	</tr>
[<foreach from=$absences item="activity">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td style="padding: 0px 10px; text-align: center;">[<$activity->block->date|date_format>]</td>
		<td style="padding: 0px 10px; text-align: center;">[<$activity->name_r>]</td>
		<td style="padding: 0px 10px; text-align: center;">[<$activity->aid>]</td>
		[<if $admin>]<td style="padding: 0px 10px; text-align: center;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/remove_absence/uid/[<$uid>]/bid/[<$activity->bid>]">Remove</a></td>[</if>]
	</tr>
[</foreach>]
</table>
