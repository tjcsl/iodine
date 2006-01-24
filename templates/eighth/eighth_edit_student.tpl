[<include file="eighth/eighth_header.tpl">]
<form action="[<$I2_ROOT>]eighth/edit/student/uid/[<$user->uid>]" method="post">
	<table>
		<tr>
			<th style="width: 100px; text-align: left;">Field</th>
			<th style="width: 100px; text-align: left;">Value</th>
		</tr>
		<tr>
			<td onDblClick="this.style.backgroundColor='#FF0000';">Student ID</td>
			<td><input type="text" name="uid" value="[<$user->uid>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>First Name</td>
			<td><input type="text" name="fname" value="[<$user->fname>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Middle Name</td>
			<td><input type="text" name="mname" value="[<$user->mname>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input type="text" name="lname" value="[<$user->lname>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Suffix</td>
			<td><input type="text" name="suffix" value="[<$user->suffix>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Nickname</td>
			<td><input type="text" name="nickname" value="[<$user->nickname>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Grade</td>
			<td><input type="text" name="grade" value="[<$user->grade>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Sex</td>
			<td><input type="text" name="sex" value="[<$user->sex>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Birth Date</td>
			<td><input type="text" name="bdate" value="[<$user->bdate>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Home Phone</td>
			<td><input type="text" name="phone_home" value="[<$user->phone_home>]" style="border: none;" /></td>
		</tr>
		<tr>
			<th colspan="2">Address #1</th>
		</tr>
		<tr>
			<td>Street</td>
			<td><input type="text" name="address1_street" value="[<$user->address1_street>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>City</td>
			<td><input type="text" name="address1_city" value="[<$user->address1_city>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>State</td>
			<td><input type="text" name="address1_state" value="[<$user->address1_state>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Zip</td>
			<td><input type="text" name="address1_zip" value="[<$user->address1_zip>]" style="border: none;" /></td>
		</tr>
		<tr>
			<td>Counselor</td>
			<td><input type="text" name="counselor" value="[<$user->counselor>]" style="border: none;" /></td>
		<tr>
			<td>Locker</td>
			<td><input type="text" name="locker" value="[<$user->locker>]" style="border: none;" /></td>
		</tr>
	</table>
	<input type="submit" value="Update">
</form>
