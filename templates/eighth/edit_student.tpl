[<include file="eighth/header.tpl">]
<br /><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]">Return to Student</a><br />
<form action="[<$I2_ROOT>]eighth/edit/student/uid/[<$user->uid>]" method="post">
	<table>
		<tr>
			<th style="width: 100px; text-align: left;">Field</th>
			<th style="width: 100px; text-align: left;">Value</th>
		</tr>
		<tr>
			<td onDblClick="this.style.backgroundColor='#FF0000';">Student ID</td>
			<td><input type="text" name="studentid" value="[<$user->studentid>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>First Name</td>
			<td><input type="text" name="eighth_user_data[fname]" value="[<$user->fname>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Middle Name</td>
			<td><input type="text" name="eighth_user_data[mname]" value="[<$user->mname>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Last Name</td>
			<td><input type="text" name="eighth_user_data[lname]" value="[<$user->lname>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Title</td>
			<td><input type="text" name="eighth_user_data[title]" value="[<$user->title>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Nickname</td>
			<td><input type="text" name="eighth_user_data[nickname]" value="[<$user->nickname>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Grade</td>
			<td><input type="text" name="eighth_user_data[grade]" value="[<$user->grade>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Sex</td>
			<td><input type="text" name="eighth_user_data[sex]" value="[<$user->sex>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Birth Date</td>
			<td><input type="text" name="eighth_user_data[bdate]" value="[<$user->bdate>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Home Phone</td>
			<td><input type="text" name="eighth_user_data[homePhone]" value="[<$user->homePhone>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<th colspan="2">Address #1</th>
		</tr>
		<tr>
			<td>Street</td>
			<td><input type="text" name="eighth_user_data[street]" value="[<$user->street>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>City</td>
			<td><input type="text" name="eighth_user_data[l]" value="[<$user->l>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>State</td>
			<td><input type="text" name="eighth_user_data[st]" value="[<$user->st>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Zip</td>
			<td><input type="text" name="eighth_user_data[postalCode]" value="[<$user->postalCode>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
		<tr>
			<td>Counselor</td>
			<td><input type="text" name="eighth_user_data[counselor_name]" value="[<$user->counselor_name>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		<tr>
			<td>Locker</td>
			<td><input type="text" name="eighth_user_data[locker]" value="[<$user->locker>]" style="border: 1px dotted #000000; padding: 2px;" /></td>
		</tr>
	</table>
	<input type="submit" value="Update">
</form>
