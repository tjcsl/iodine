[<* A 'TODO' next to an item means it is not quite done yet (Hopefully including what needs to be done as well) *>]
[<* A 'P+' next to an item means it prints fully *>]
[<* A 'P-' next to an item means it prints somewhat *>]

[<include file="eighth/header.tpl">]

<table id="eighth_pane_table">
	<tr>
		<td>
			<b>Student Options</b>
			<ol>
				<li><a href="[<$I2_ROOT>]eighth/reg_group"><b>Register a group of students for an activity</b></a></li>
				<li><a href="[<$I2_ROOT>]eighth/amr_group">Add, modify, or remove a special group of students</a></li>
				<li><a href="[<$I2_ROOT>]eighth/alt_permissions">Add students to a restricted activity</a></li>
				<li><a href="[<$I2_ROOT>]eighth/people_switch">Switch all the students in one activity into another</a></li>
			</ol>
		</td>
		<td>
			<b>Attendance Options</b>
			<ol>
				<li><a href="[<$I2_ROOT>]eighth/res_student"><b>Reschedule students by student ID for a single activity</b></a></li>
				<li>[<* P+ *>]<a href="[<$I2_ROOT>]eighth/vcp_attendance"><b>View, change, or print attendance data</b></a></li>
				<li><a href="[<$I2_ROOT>]eighth/ent_attendance"><b>Enter TA absences by student ID</b></a></li>
				<li>[<* P- TODO: Needs to not be completely broken! *>]<a href="[<$I2_ROOT>]eighth/vp_delinquent">View or print a list of delinquent students</a></li>
			</ol>
		</td>
	</tr>
	<tr>
		<td>
			<b>Activity Scheduling Options</b>
			<ol>
				<li><a href="[<$I2_ROOT>]eighth/amr_activity">Add, modify, or remove an activity</a></li>
				<li><a href="[<$I2_ROOT>]eighth/amr_room">Add, modify, or remove a room</a></li>
				<li><a href="[<$I2_ROOT>]eighth/amr_sponsor">Add, modify, or remove an activity sponsor</a></li>
				<li><a href="[<$I2_ROOT>]eighth/sch_activity"><b>Schedule an activity for eighth period</b></a></li>
				<li>[<* P+ *>]<a href="[<$I2_ROOT>]eighth/vp_roster"><b>View or print a class roster</b></a></li>
				<li>[<* P- *>]<a href="[<$I2_ROOT>]eighth/vp_room"><b>View or print the utilization of a room</b></a></li>
				<li><a href="[<$I2_ROOT>]eighth/cancel_activity">Cancel/set comments/advertise for an activity</a></li>
				<li><a href="[<$I2_ROOT>]eighth/room_sanity">Room assignment sanity check</a></li>
				<li>[<* P+ *>]<a href="[<$I2_ROOT>]eighth/vp_sponsor">View or print sponsor schedule</a></li>
			</ol>
		</td>
		<td>
			<b>Special Options</b>
			<ol>
				<li><a href="[<$I2_ROOT>]eighth/fin_schedules"><b>Finalize student schedules</b></a></li>
				<li>[<* P+ *>]<a href="[<$I2_ROOT>]eighth/prn_attendance"><b>Print activity rosters</b></a></li>
				<li><a href="[<$I2_ROOT>]eighth/chg_start"><b>Change starting date</b></a></li>
				<li><a href="[<$I2_ROOT>]eighth/ar_block#add">Add or remove 8th period block from system</a></li>
				<li>[<* TODO: Needs to pull users from LDAP, not MySQL *>]<a href="[<$I2_ROOT>]eighth/rep_schedules">Repair broken schedules</a></li>
				[<* This is pointless for now, but is good to keep around *>]
				[<* <li><a href="[<$I2_ROOT>]eighth/set_printer">Set printer</a></li> *>]
				<li><a href="[<$I2_ROOT>]eighth/export_csv">Export out of school schedules</a></li>
			</ol>
		</td>
	</tr>
</table>
<div id="eighth_pane_logout"><a href="[<$I2_ROOT>]logout">Logout</a></div>
