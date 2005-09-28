[<include file="eighth_header.tpl">]
<span style="font-family: courier;">
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$block->date|date_format>], [<$block->block>] block<br />
[<php>]
	$rooms = EighthRoom::id_to_room($this->_tpl_vars['activity']->rooms);
	$temp_rooms = array();
	foreach($rooms as $room) {
		$temp_rooms[] = $room->name;
	}
	$this->_tpl_vars['rooms'] = implode(",", $temp_rooms);
	
	$sponsors = EighthSponsor::id_to_sponsor($this->_tpl_vars['activity']->sponsors);
	$temp_sponsors = array();
	foreach($sponsors as $sponsor) {
		$temp_sponsors[] = $sponsor->name;
	}
	$this->_tpl_vars['sponsors'] = implode(",", $temp_sponsors);
[</php>]
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$rooms>]<br />
Sponsor(s):&nbsp;[<$sponsors>]<br />
<br />
<form action="[<$I2_ROOT>]eighth/vcp_attendance/update/bid/[<$block->bid>]/aid/[<$activity->aid>]" method="post">
	<input type="button" value="Select All" onClick=";"> <input type="submit" value="Update"><br />
	<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
		<tr>
			<th>Absent</th>
			<th style="padding: 0px 5px;">Student</th>
			<th style="padding: 0px 5px;">Grade</th>
		</tr>
[<php>]
	$this->_tpl_vars['users'] = User::id_to_user($this->_tpl_vars['activity']->get_members($this->_tpl_vars['block']->bid));
	usort($this->_tpl_vars['users'], 'name_cmp');

	function name_cmp($user1, $user2) {
		return strcasecmp($user1->name_comma, $user2->name_comma);
	}
[</php>]
[<foreach from=$users item="user">]
		<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
			<td style="padding: 0px 5px;"><input type="checkbox" name="absentees[]" value="[<$user->uid>]"[<if in_array($user->uid, $absentees) >] checked[</if>]></td>
			<td style="padding: 0px 5px;">[<$user->name_comma>] ([<$user->uid>])</td>
			<td style="padding: 0px 5px;">[<$user->grade>]</td>
		</tr>
[</foreach>]
	</table><br />
	<input type="submit" value="Update">
</form>
