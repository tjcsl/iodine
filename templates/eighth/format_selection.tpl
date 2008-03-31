[<include file="eighth/header.tpl">]
<h2>Choose an output format for [<$title>]:</h2>
[<if !$user>]
<form action="[<$I2_ROOT>]eighth/[<$module>]/print[<$args>]" method=post>
	<input type="hidden" name="format" value="print" />
	<input type="submit" value="Send to Printer" />
</form><br /><br />
[</if>]

 
<form action="[<$I2_ROOT>]eighth/[<$module>]/print[<$args>]" method=post>
	<select name="format" id="format">
	[<foreach from=$formats item='format_name' key='format'>]
		<option value="[<$format>]">[<$format_name>]</option>
	[</foreach>]</select><br />
	<input type="submit" value="Select Output Format" />
</form>
