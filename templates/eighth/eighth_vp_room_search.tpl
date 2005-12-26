[<include file="eighth/eighth_header.tpl">]
Specify the report criteria:<br />
<form action="[<$I2_ROOT>]eighth/vp_room/view/bid/[<$bid>]" method="post">
	<table style="border: 0px; margin: 0px; padding: 0px;">
		<tr>
			<td>&nbsp;</td>
			<th style="padding: 0px 5px;">Room</th>
			<th style="padding: 0px 5px;">Activity ID</th>
			<th style="padding: 0px 5px;">Activity Name</th>
			<th style="padding: 0px 5px;">Teacher</th>
			<th style="padding: 0px 5px;">Students</th>
		</tr>
		<tr>
			<th style="text-align: right;">Include:</th>
			<td style="text-align: center;"><input type="checkbox" name="include[]" value="room" checked="checked" /></td>
			<td style="text-align: center;"><input type="checkbox" name="include[]" value="aid" checked="checked" /></td>
			<td style="text-align: center;"><input type="checkbox" name="include[]" value="name" checked="checked" /></td>
			<td style="text-align: center;"><input type="checkbox" name="include[]" value="teacher" checked="checked" /></td>
			<td style="text-align: center;"><input type="checkbox" name="include[]" value="students" checked="checked" /></td>
		</tr>
		<tr>
			<th style="text-align: right;">Sort:</th>
			<td style="text-align: center;"><input type="radio" name="sort" value="room" checked="checked" /></td>
			<td style="text-align: center;"><input type="radio" name="sort" value="aid" /></td>
			<td style="text-align: center;"><input type="radio" name="sort" value="name" /></td>
			<td style="text-align: center;"><input type="radio" name="sort" value="teacher" /></td>
			<td style="text-align: center;"><input type="radio" name="sort" value="students" /></td>
		</tr>
	</table>
	<input type="checkbox" name="overbooked" /> Show only overbooked activities<br />
	<input type="submit" value="Next" />
</form>
