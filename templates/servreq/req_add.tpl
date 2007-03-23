<form method="post" action="[<$I2_ROOT>]servreq" class="boxform">
<input type="hidden" name="servreq_form" value="" />
<strong>Submit New Service Request</strong>
<table>
	<tr>
		<td>Request Type:</td>
		<td>
			<select name="type">
				<option value="webspace"/>Personal Web Space
				<option value="clubspace"/>Club/Organization Web Site
				<option value="account"/>CSL Machine Account
			</select>
		</td>
	</tr>
	<tr>
		<td>Details:</td>
		<td><textarea name="details" ></textarea></td>
	</tr>
	<tr>
		<td>Sponsor:</td>
		<td>
			<select name="app">
				<option value=0 />N/A
				[<foreach from=$approvers item=person>]
				<option value=[<$person->id>] />[<$person->name>]
				[</foreach>]
			</select>
		</td>
	</tr>
</table>
</form>
