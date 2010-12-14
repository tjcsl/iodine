This is the eighth period block exclusion control page.<br />
<br />
Add Exclusion:<br />
<form action='' method='post'>
Block: <input type='text' name='block' /> <br/ >
Excluded Block: <input type='text' name='targetblock' /><br />
<input type='hidden' name='action' value='add'/><input type='submit' value='Add' /><br />
</form>
<br />
<table>
[<foreach from=$excludes item=exclude>]
<tr>
<td>[<$exclude.block>]</td>
<td>[<$exclude.targetblock>]</td>
<td><form action='' method='post'><input type='hidden' name='block' value="[<$exclude.block>]"/><input type='hidden' name='targetblock' value="[<$exclude.targetblock>]"/><input type='hidden' name='action' value='remove'/><input type='submit' value='Remove'/></form></td>
</tr>
[</foreach>]
</table>
