[<include file="eighth_header.tpl">]
[<if count($activities) > 0 >]
	<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px">
		<tr>
			<th style="padding: 5px;">Date</th>
			<th style="padding: 5px;">Activity</th>
			<th style="padding: 5px;">Room(s)</th>
			<th style="padding: 5px;">Students Enrolled</th>
		</tr>
	[<foreach from=$activities item="activity">]
		<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">];">
			<td style="padding: 0px 5px;">[<$activity->block->date|date_format:"%B %e, %Y">], [<$activity->block->block>] block</td>
			<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/vp_roster/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_r>] - [<$activity->aid>]</a></td>
	[<php>]
		$rooms = EighthRoom::id_to_room($this->_tpl_vars['activity']->block_rooms);
		$temp_rooms = array();
		foreach($rooms as $room) {
			$temp_rooms[] = $room->name;
		}
		$this->_tpl_vars['rooms'] = implode(", ", $temp_rooms);
	[</php>]
			<td style="padding: 0px 5px; text-align: center;">[<$rooms>]</td>
			<td style="padiing: 0px 5px; text-align: center;">[<php>] echo count($this->_tpl_vars['activity']->members); [</php>]</td>
		</tr>
	[</foreach>]
	</table>
[<else>]
	<span style="color: red; font-weight: bold;">This sponsor is not scheduled for any activities</span>
[</if>]
