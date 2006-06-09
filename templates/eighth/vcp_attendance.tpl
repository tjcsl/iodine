[<include file="eighth/header.tpl">]
[<include file="eighth/include_list_open.tpl">]
[<include file="eighth/activity_selection.tpl">]
[<include file="eighth/include_list_close.tpl">]
<span style="font-family: courier;">
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$act->block->date|date_format>], [<$act->block->block>] block<br />
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$act->block_rooms_comma>]<br />
Sponsor(s):&nbsp;[<$act->block_sponsors_comma>]<br />
<br />
<form name="vcp_attendance_form" action="[<$I2_ROOT>]eighth/vcp_attendance/update/bid/[<$act->bid>]/aid/[<$act->aid>]" method="post">
	<input type="button" value="Select All" onclick="CA();" name="selectall" /> <input type="submit" value="Update" /><br />
	<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
		<tr>
			<th>Absent</th>
			<th style="padding: 0px 5px;">Student</th>
			<th style="padding: 0px 5px;">Grade</th>
		</tr>
[<foreach from=$act->members_obj item=user>]
		<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">]">
			<td style="padding: 0px 5px;"><input type="checkbox" name="absentees[]" value="[<$user->uid>]"[<if in_array($user->uid, $absentees) >] checked="checked"[</if>] onChange="CCA(this);" /></td>
			<td style="padding: 0px 5px;">[<$user->name_comma>] ([<$user->uid>])</td>
			<td style="padding: 0px 5px;">[<$user->grade>]</td>
		</tr>
[</foreach>]
	</table><br />
	<input type="submit" value="Update" />
</form>
<script language="javascript" type="text/javascript">
	var frm = document.vcp_attendance_form;
	function CA() {
		var trk = 0;
		for (var i = 0; i < frm.elements.length; i++) {
			var e = frm.elements[i];
			if ((e.name != 'selectall') && (e.type == 'checkbox')) {
				trk++;
				e.checked = (frm.selectall.value == "Select All" ? true : false);
			}
		}
		if(frm.selectall.value == "Select All") {
			frm.selectall.value = "Deselect All";
		}
		else {
			frm.selectall.value = "Select All";
		}
	}
	function CCA(CB){
		var TB = TO = 0;
		for (var i = 0; i < frm.elements.length; i++) {
			var e = frm.elements[i];
			if ((e.name != 'selectall') && (e.type == 'checkbox')) {
				TB++;
				if(e.checked) TO++;
			}
		}
		frm.selectall.value = (TO == TB ? "Deelect All" : "Select All");
	}
</script>
