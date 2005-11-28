[<include file="eighth/eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$block->date|date_format>] - [<$block->block>] block</span><br /><br />
<table style="border: 0px; margin: 0px; padding: 0px; width: 100%;">
	<tr>
		<th style="padding: 0px 5px; text-align: left;">Location</th>
		<th style="padding: 0px 5px; text-align: left;">Activity ID</th>
		<th style="padding: 0px 5px; text-align: left;">Activity Name</th>
		<th style="padding: 0px 5px; text-align: left;">Teacher</th>
		<th style="padding: 0px 5px; text-align: left;">Students</th>
		<th style="padding: 0px 5px; text-align: left;">Capacity</th>
	</tr>
[<foreach from=$utilizations item="utilization">]
	<tr>
		<td style="padding: 0px 5px;">[<$utilization.room->name>]</td>
		<td style="padding: 0px 5px;">[<$utilization.activity->aid>]</td>
		<td style="padding: 0px 5px;">[<$utilization.activity->name>]</td>
		<td style="padding: 0px 5px;">[<$utilization.activity->block_sponsors_comma>]</td>
		<td style="padding: 0px 5px;">[<$utilization.students>]</td>
		<td style="padding: 0px 5px;">[<$utilization.room->capacity>]</td>
	</tr>
[</foreach>]
</table>
