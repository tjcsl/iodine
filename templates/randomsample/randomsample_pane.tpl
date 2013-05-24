[<if isset($sample)>]
	<table>
		<tr>
			[<foreach from=$cols item="col">]
				<th>[<$col>]</th>
			[</foreach>]
		</tr>
		[<foreach from=$sample item="res">]
			<tr>
				[<foreach from=$res key="name" item="value">]
					<td>[<$value>]</td>
				[</foreach>]
			</tr><tr><td>&nbsp;</td></tr>
		[</foreach>]
	</table>
[<else>]
<form action="[<$I2_ROOT>]randomsample/results" method="post">
	<table>
		<tr><th>Sample size:</th><td><input type="text" name="size"/></td></tr>
		<tr><th>Filter:</th><td><input type="text" name="filter"/></td></tr>
		<tr><th>Attributes to return:</th><td><input type="text" name="attrs"/></td></tr>
		<tr><td><input type="submit" value="Get Sample"/></td></tr>
	</table>
</form>
[</if>]
