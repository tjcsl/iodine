<a href="[<$I2_ROOT>]docs">Documents Home</a><br /><br />

[<if $error>]<p style="color:red">[<$error>]</p>[</if>]

<p>Note that the document you upload must be less than 10 megabytes and must be one of the following file types: [<$exts>]</p>
<form method="post" action="[<$I2_ROOT>]docs/add" class="boxform" enctype="multipart/form-data">
<table>
<tr><td>Document Name: </td><td><input type="text" name="name" value="" maxlength="128" /></td></tr>
<tr><td>Upload File: </td><td><input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
<input type="file" name="upfile" /></td></tr>
<tr><td><input type="checkbox" name="visible" /> Visible</td><td></td></tr>
</table>
<table id="docs_groups_table" cellpadding="0">
<thead>
<tr>
<td></td>
<th>Group</th>
<th>View</th>
<th>Edit</th>
</tr>
</thead>
<tbody>
<tr>
<td><input type="hidden" name="groups[]" value="0" /></td>
<td><select class="groups_list" name="group_gids[0]">
[<foreach from=$groups item=group>]
	<option value="[<$group->gid>]">[<$group->name>]</option>
[</foreach>]
</select></td>
<td><input type="checkbox" checked="checked" name="view[0]" /></td>
<td><input type="checkbox" name="edit[0]" /></td>
<!-- <td><a onclick="docs_deleteGroup(event)" href="">remove</a></td> -->
</tr><tr>
<td></td>
<!-- <td><a href="" onclick="docs_addGroup(event)">Add another group</a></td> -->
<td></td>
</tr>
</tbody>
</table>

<input type="submit" value="Create document" name="submit" />
</form>
