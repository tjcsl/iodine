[<include file="eighth/header.tpl">]
<table><tr><td style="vertical-align: top;">
[<include file="eighth/include_list_open.tpl">]
[<include file="eighth/activity_selection.tpl" op='view' bid=$block->bid field='aid'>]
[<include file="eighth/block_selection.tpl" header="FALSE" title='' method='vcp_attendance' op='view' field='bid' bid=$block->bid>]
[<include file="eighth/include_list_close.tpl">]
</td><td style="vertical-align: top;">
<span style="font-family: monospace;">
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$act->block->date|date_format>], [<$act->block->block>] block<br />
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$act->block_rooms_comma>]<br />
Sponsor(s):&nbsp;[<$act->block_sponsors_comma>]<br />
</span>
<br />
<form name="vcp_attendance_form" action="[<$I2_ROOT>]eighth/vcp_attendance/update/bid/[<$act->bid>]/aid/[<$act->aid>]" method="post">
	<input type="button" value="Select All" onclick="CA();" name="selectall" /> [<if $is_admin>]<input type="submit" value="Update" />[</if>]<br />
	<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
		<tr>
			<th>Absent</th>
			<th style="padding: 0px 5px;">Student</th>
			<th style="padding: 0px 5px;">Grade</th>
		</tr>
[<foreach from=$act->members_obj item=user>]
		<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">]">
			<td style="padding: 0px 5px;"><input type="checkbox" name="absentees[]" value="[<$user->uid>]"[<if in_array($user->uid, $absentees) >] checked="checked"[</if>] onchange="CCA(this);" /></td>
			<td style="padding: 0px 5px;">[<$user->name_comma>] ([<$user->iodineUidNumber>])</td>
			<td style="padding: 0px 5px;">[<$user->grade>]</td>
		</tr>
[</foreach>]
	</table><br />
	[<if $is_admin || $is_sponsor>]<input type="submit" value="Update" />[</if>]
</form>
</td></tr></table>
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
		frm.selectall.value = (TO == TB ? "Deselect All" : "Select All");
	}
</script>
