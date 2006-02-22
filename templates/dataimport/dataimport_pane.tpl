[<if isSet($data)>]
	[<foreach from=$data item=row>]
		[<foreach from=$row key=key item=value>]
			[<$key>] = [<$value>]
		[</foreach>]
	[</foreach>]
[<else>]
	<form action="[<$I2_ROOT>]dataimport/data" method="post">
		Data file: <input type="text" name="datafile"/><br/>
		<input type="submit" value="load data"/>
	</form>
[</if>]
