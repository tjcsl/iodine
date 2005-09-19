[<include file="eighth_header.tpl">]
<span style="font-family: courier;">
Activity:&nbsp;&nbsp;&nbsp;[<$activity->name>][<if $activity->restricted >] (R)[</if>]<br />
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$activity->block->date|date_format>], [<$activity->block->block>] block<br />
[<php>]
	$rooms = EighthRoom::id_to_room($this->_tpl_vars['activity']->block_rooms);
	$temp_rooms = array();
	foreach($rooms as $room) {
		$temp_rooms[] = $room->name;
	}
	$this->_tpl_vars['rooms'] = implode(", ", $temp_rooms);
	
	$sponsors = EighthSponsor::id_to_sponsor($this->_tpl_vars['activity']->block_sponsors);
	$temp_sponsors = array();
	foreach($sponsors as $sponsor) {
		$temp_sponsors[] = $sponsor->name;
	}
	$this->_tpl_vars['sponsors'] = implode(", ", $temp_sponsors);
[</php>]
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$rooms>]<br />
Sponsor(s):&nbsp;[<$sponsors>]<br />
<br />
[<php>]
	$this->_tpl_vars['users'] = User::id_to_user($this->_tpl_vars['activity']->members);
	usort($this->_tpl_vars['users'], 'name_cmp');

	function name_cmp($user1, $user2) {
		return strcasecmp($user1->name_comma, $user2->name_comma);
	}
[</php>]
[<foreach from=$users item="user">]
________ [<$user->name_comma>] ([<$user->uid>]) - [<$user->grade>]<br />
[</foreach>]
<br />
[<php>] $this->_tpl_vars['count'] = count($this->_tpl_vars['users']); [</php>]
[<$count>] student[<if $count != 1>]s[</if>] signed up
