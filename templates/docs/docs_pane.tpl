[<if $is_admin>]You are an intranet admin. <a href="[<$I2_ROOT>]docs/add">Upload a document</a><br /><br />
[<else>]You have permission to access the following documents:[</if>]

<table class="docs">
[<foreach from=$docs item=doc>]
	<tr><th><a href="[<$I2_ROOT>]docs/view/[<$doc->docid>]">[<$doc->name>]</a></th>
	[<if $is_admin>]
	<td><a href="[<$I2_ROOT>]docs/edit/[<$doc->docid>]">Edit</a></td>
	<td><a href="[<$I2_ROOT>]docs/delete/[<$doc->docid>]">Delete</a></td>
	[</if>]
	</tr>
[</foreach>]
</table>
[<if empty($docs)>]
<p>There are currently no documents that you can view.</p>
[</if>]

