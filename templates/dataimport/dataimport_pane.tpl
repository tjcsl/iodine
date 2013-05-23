<span style="color: red;"><strong>THIS IS DEPRECATED. USE THE <a href="[<$I2_ROOT>]newimport">NEWIMPORT</a> MODULE INSTEAD.</strong></span><br /><br />
Database update procedure:
<ol>
	<li>Back up the database, just to be safe.</li>
	<li>If it's the beginning of the year, you will need to press the "It's the beginning of the year!" button. This will ensure that students' entries in LDAP are correctly set for the beginning of the year -- privacy permissions are set correctly and eighth office comments are cleared.</li>
	<li>If it's the beginning of the year, you will also need to clear eighth period absences using the appropriate button.</li>
	<li>Set the path to the "Student Data" file. Note that this is the location of the file on the server, and the file must be readable by www-data. This is the "INTRANET.***" file in the SASI dump.</li>
	<li>Set the LDAP Admin password. It's the one in the config file.</li>
	<li>Click "Do Student Import". This will get rid of students that no longer exist, create database entries for new students, and bring existing students up to date. Note that this can take a significant amount of time (on the order of half an hour) if the script needs to delete a lot of old users.</li>
	<li>Set the "Student Schedule" and "Course Information" files. Again, you need to enter the locations of the files on the server.</li>
	<li>Click "Import Schedule Data". This takes ~5 minutes.</li>
</ol>
[<if isset($userdata)>]
	[<*'
	[<foreach from=$userdata item=row>]
		[<foreach from=$row key=key item=value>]
			[<$key>] = [<$value>]
		[</foreach>]
		<br />
	[</foreach>]
	'*>]
[<else>]
	[<if $startyear>]
		It's the beginning of year! <a href="[<$I2_ROOT>]dataimport/unset_startyear">UNSET</a>
	[<else>]
		<form action="[<$I2_ROOT>]dataimport/start_year" method="post">
			<input type="hidden" name="start_year" value="1" />
			<input type="submit" value="This is the initial data import of the year!"/><br/>
		</form>
	[</if>]
	<br /><br />
	[<if $userfile>]
		Student data file <a href="[<$I2_ROOT>]dataimport/unset_user">SET</a>
	[<else>]
		<form action="[<$I2_ROOT>]dataimport/userdata" method="post">
			<table>
			<tr><td>Student Data file:</td><td> <input type="text" name="userfile"/></td></tr>
			</table>
			<input type="submit" value="Set Student Data Info"/><br/>
		</form>
	[</if>]
	<br /><br />
	[<if $teacherfile>]
		Teacher data <a href="[<$I2_ROOT>]dataimport/unset_teacher">SET</a>
	[<else>]
		<form action="[<$I2_ROOT>]dataimport/teacherdata" method="post">
			<table>
			<tr><td>Teacher Data file:</td><td> <input type="text" name="teacherfile"/></td><td>(TEACHER.***)</td></tr>
			<tr><td>Teacher LDAP server: </td><td><input type="text" name="teacherserver"/></td></tr>
			<tr><td>Teacher LDAP username: </td><td><input type="text" name="teacherdn"/></td></tr>
			<tr><td>Teacher LDAP password: </td><td><input type="password" name="teacherpass"/></td></tr>
			<tr><td>Obsolete Staff file:</td><td><input type="text" name="stafffile"/></td><td>(STAFF.TXT)</td></tr>
			</table>
			<input type="submit" value="Set Teacher Data Info"/><br/>
		</form>
	[</if>]
	<br /><br />
	[<* disabled'
	<form action="[<$I2_ROOT>]dataimport/teachersponsors" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Make all teachers into Eighth-Period sponsors"/><br/>
	</form>
	<br /><br/>
	'*>]
	[<if $admin_pass>]
		[<* disabled'
		<form action="[<$I2_ROOT>]dataimport/clean" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Clean the entire database"/><br/>
		</form>
		<br /><br/>
		<form action="[<$I2_ROOT>]dataimport/clean_students" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Delete all students"/><br/>
		</form>
		<br /><br/>
		<form action="[<$I2_ROOT>]dataimport/clean_teachers" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Delete all teachers"/><br/>
		</form>
		<br /><br/>
		<form action="[<$I2_ROOT>]dataimport/clean_eighth" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Delete all Eighth-period data"/><br/>
		</form>
		<br /><br/>
		<form action="[<$I2_ROOT>]dataimport/clean_other" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Delete assorted data"/><br/>
		</form>
		<br /><br/>
		'*>]
		[<* This is dangerous, so it is disabled'
		<form action="[<$I2_ROOT>]dataimport/fixit" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Ensure the database is usable"/><br/>
		</form>
		<br /><br/>'*>]
	[</if>]
	[<if $startyear>]
		<form action="[<$I2_ROOT>]dataimport/absences" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Clear eighth period absences"/><br />
		</form>
		<br /><br />
	[</if>]
	[<if $userfile && $admin_pass>]
		<form action="[<$I2_ROOT>]dataimport/students" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Do student import"/><br/>
		</form>
		<br /><br/>
	[</if>]
	[<if $teacherfile && $admin_pass>]
		<form action="[<$I2_ROOT>]dataimport/teachers" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Do teacher import"/><br/>
		</form>
		<br /><br/>
	[</if>]
	[<if $schedulefile && $admin_pass>]
		<form action="[<$I2_ROOT>]dataimport/schedules" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Import Schedule Data"/><br/>
		</form>
		<br /><br/>
	[</if>]
	[<if $teacherfile && $userfile && $admin_pass && $intranet_pass>]
		<form action="[<$I2_ROOT>]dataimport/doeverything" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="FIX INTRANET" style="font-style: bold; font-size: 40pt; color: red;"/><br/>
		</form>
	<br /><br />
	[</if>]
[<if $intranet_pass>]
		<form action="[<$I2_ROOT>]dataimport/eighth_permissions" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Import 8th-period permissions data"/><br/>
		</form>
	[<if $admin_pass>]
		<form action="[<$I2_ROOT>]dataimport/eighthdata" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Load All 8th-period data"/><br/>
		</form><br />
		<form action="[<$I2_ROOT>]dataimport/fixuser" method="post">
			<input type="text" name="studentid" value="StudentID"/><input type="submit" value="Repair a broken user"/><br/>
		</form>
		<form action="[<$I2_ROOT>]dataimport/eighth_absences" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Load 8th-period absence data"/><br/>
		</form>
		<form action="[<$I2_ROOT>]dataimport/eighth_groups" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Load 8th-period group data"/><br/>
		</form>
		<br /><br />
		<form action="[<$I2_ROOT>]dataimport/studentinfo" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Expand student information using old Intranet data"/><br/>
		</form>
		<br /><br />
		<form action="[<$I2_ROOT>]dataimport/polls" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Import Polls"/><br/>
		</form>
	[</if>]
		<form action="[<$I2_ROOT>]dataimport/aphorisms" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Import Aphorisms"/><br/>
		</form>
		<form action="[<$I2_ROOT>]dataimport/teacherparking" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Import teacher parking spots"/><br/>
		</form>
[</if>]
	[<if !$admin_pass>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
			LDAP Admin Password: <input type="password" name="admin_pass"/><br />
			<input type="submit" value="Set LDAP Password"/><br/>
		</form>
		<br/>
	[<else>]
		LDAP Admin Password <a href="[<$I2_ROOT>]dataimport/unset_pass">SET</a><br />
	[</if>]
	[<if !$schedulefile>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
			Student Schedule File: <input type="text" name="schedulefile" /> (SCS.***)<br />
			Course Information File: <input type="text" name="classfile" /> (CLS.***)<br />
			<input type="submit" value="Set Schedule Information"/><br />
		</form>
	[<else>]
		Student Schedule Information <a href="[<$I2_ROOT>]dataimport/unset_schedule">SET</a><br />
	[</if>]
	[<*'
	[<if !$intranet_pass>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
		<table>
			<tr><td>Intranet MySQL server:</td><td> <input type="text" name="intranet_server"/></td></tr>
			<tr><td>Intranet MySQL database: </td><td><input type="text" name="intranet_db"/></td></tr>
			<tr><td>Intranet MySQL user: </td><td><input type="text" name="intranet_user"/></td></tr>
			<tr><td>Intranet MySQL password: </td><td><input type="password" name="intranet_pass"/></td></tr>
		</table>
			<input type="submit" value="Set Intranet 1 Server"/><br/>
		</form>
	[<else>]
		Intranet MySQL Information <a href="[<$I2_ROOT>]dataimport/unset_intranet">SET</a>
	[</if>]
	'*>]
[</if>]
