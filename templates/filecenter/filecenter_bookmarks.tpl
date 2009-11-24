Filecenter Bookmarks:<br />
<table>
[<foreach from=$otherdirs item=dir>]
<tr><td>[<$dir.name>]</td><td><a href="[<$I2_ROOT>]filecenter/[<$dir.path>]">[<$dir.path>]</a></td><td><form method="get" action="[<$I2_ROOT>]filecenter/bookmarks/"><input type="hidden" name="action" value="remove" /><input type="hidden" name="name" value="[<$dir.name>]" /><input type="hidden" name="path" value="[<$dir.path>]" /><input type="submit" value="Remove" /></form></td></tr>
[</foreach>]
</table>
<form method="get" action="[<$I2_ROOT>]filecenter/bookmarks/"><input type="hidden" name="action" value="add" />Name: <input type="text" name="name"/> Path: <input type="text" name="path" /><input type="submit" value="Add" /></form><br />
