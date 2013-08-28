<a href="[<$I2_ROOT>]docs">Documents Home</a><br /><br />

[<if $error>]<p style="color:red">[<$error>]</p>[</if>]

<form method="post" action="[<$I2_ROOT>]docs/edit/[<$doc->docid>]" class="boxform" enctype="multipart/form-data">
<table>
<tr><td>Document Name: </td><td><input type="text" name="name" value="[<$doc->name>]" maxlength="128" /></td></tr>
<tr><td>Document Type: </td><td><input type="text" name="type" value="[<$type>]" /></td></tr>
<tr><td>Visible: </td><td><input type="checkbox" name="visible" [<if $doc->visible>]checked="yes"[</if>]" /></td></tr>
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
        <option value="[<$group->gid>]" [<if $gid == $group->gid>]selected="selected"[</if>]>[<$group->name>]</option>
[</foreach>]
</select></td>
<td><input type="checkbox" [<if $view == 1>]checked="checked"[</if>] name="view[0]" /></td>
<td><input type="checkbox" [<if $edit == 1>]checked="checked"[</if>] name="edit[0]" /></td>
<!--<td><a onclick="docs_deleteGroup(event)" href="">remove</a></td> --> 
</tr><tr>
<td></td>
<!-- <td><a href="" onclick="docs_addGroup(event)">Add another group</a></td> -->
<td></td>
</tr>
</tbody>
</table>
<input type="submit" value="Update document" name="submit" />
</form>
