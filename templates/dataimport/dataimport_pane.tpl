[<if isSet($userdata)>]
	[<*
	[<foreach from=$userdata item=row>]
		[<foreach from=$row key=key item=value>]
			[<$key>] = [<$value>]
		[</foreach>]
		<br />
	[</foreach>]
	*>]
[<else>]
	[<if $userfile>]
		Student data file <a href="[<$I2_ROOT>]dataimport/unset_student">SET</a>
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
			<tr><td>Teacher Data file:</td><td> <input type="text" name="teacherfile"/></td></tr>
			<tr><td>Teacher LDAP server: </td><td><input type="text" name="teacherserver"/></td></tr>
			<tr><td>Teacher LDAP username: </td><td><input type="text" name="teacherdn"/></td></tr>
			<tr><td>Teacher LDAP password: </td><td><input type="password" name="teacherpass"/></td></tr>
			<tr><td>Obsolete</b> Staff file:</td><td><input type="text" name="stafffile"/></td></tr>
			</table>
			<input type="submit" value="Set Teacher Data Info"/><br/>
		</form>
	[</if>]
	<br /><br />
	<form action="[<$I2_ROOT>]dataimport/teachersponsors" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Make all teachers into Eighth-Period sponsors"/><br/>
	</form>
	<br /><br/>
	[<if $admin_pass>]
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
		<form action="[<$I2_ROOT>]dataimport/clean_other" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="Delete assorted data"/><br/>
		</form>
		<br /><br/>
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
	
	[<if $teacherfile && $userfile && $admin_pass && $intranet_pass>]
		<form action="[<$I2_ROOT>]dataimport/doeverything" method="post">
			<input type="hidden" name="doit" value="1"/>
			<input type="submit" value="FIX INTRANET" style="font-style: bold; font-size: 40pt; color: red;"/><br/>
		</form>
	<br /><br />
	[</if>]
[<if $intranet_pass>]
	<form action="[<$I2_ROOT>]dataimport/eighthdata" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Load 8th-period data"/><br/>
	</form>
	<form action="[<$I2_ROOT>]dataimport/studentinfo" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Expand student information using old Intranet data"/><br/>
	</form>
	<br /><br />
	[</if>]
	[<if !$admin_pass>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
			LDAP Admin Password: <input type="password" name="admin_pass"/><br/>
			<input type="submit" value="Set LDAP Password"/><br/>
		</form>
		<br/>
	[<else>]
		LDAP Admin Password <a href="[<$I2_ROOT>]dataimport/unset_pass">SET</a><br/>
	[</if>]
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
[</if>]
