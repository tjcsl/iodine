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
<th>From Block</th>
<th>To Block</th>
<th>Into Activity</th>
[<foreach from=$excludes item=exclude>]
<tr>
<td>[<$exclude.bid>]</td>
<td>[<$exclude.target_bid>]</td>
<td>[<$exclude.aid>]</td>
<td>
<form action='' method='post'>
<input type='hidden' name='block' value="[<$exclude.bid>]"/>
<input type='hidden' name='targetblock' value="[<$exclude.target_bid>]"/>
<input type='hidden' name='action' value='remove'/>
<input type='submit' value='Remove'/>
</form>
</td>
</tr>
[</foreach>]
</table>
