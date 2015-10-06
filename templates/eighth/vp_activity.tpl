[<include file="eighth/header.tpl">]
<h2>Schedule for [<$activity->name>] - [<$activity->aid>]:</h2>
[<if count($activities) > 0 >]
	<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px">
		<tr>
			<th style="padding: 5px;">Date</th>
			<th style="padding: 5px;">Activity</th>
			<th style="padding: 5px;">Room(s)</th>
			<th style="padding: 5px;">Students Enrolled</th>
		</tr>
	[<foreach from=$activities item="act">]
		<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">];">
			<td style="padding: 0px 5px;">[<$act->block->date|date_format:"%a %B %e, %Y">], [<$act->block->block>] block</td>
			<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/vp_roster/view/bid/[<$act->bid>]/aid/[<$act->aid>]">[<$act->name_r>] - [<$act->aid>]</a></td>
			<td style="padding: 0px 5px; text-align: center;">[<$act->block_rooms_comma>]</td>
			<td style="padding: 0px 5px; text-align: center;">[<$act->member_count>]</td>
		</tr>
	[</foreach>]
	</table><br />
	<div style="float: right; margin: 10px;">
		<a href="[<$I2_ROOT>]eighth/vp_activity/format/aid/[<$activity->aid>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" /></a>
	</div>
[<else>]
	<span style="color: red; font-weight: bold;">This activity is not scheduled for any blocks</span>
[</if>]
