<table id="filetable">
 <tr>
  <th>&nbsp;</th>
  <th>Name</th>
  <th>Size</th>
  <th>Type</th>
  <th>Last Modified</th>
 </tr>
 [<foreach from=$dirs item=dir>]
 <tr>
  <td><a href="[<$dir.name>]/"><img src="[<$I2_ROOT>]www/pics/filecenter/folder.png" width="24" height="24"/></a></td>
  <td><a href="[<$dir.name>]/">[<$dir.name>]</a></td>
  <td>&nbsp;</td>
  <td>Directory</td>
  <td>[<$dir.last_modified>]</td>
 </tr>
 [</foreach>]
 [<foreach from=$files item=file>]
 <tr>
  <td><a href="[<$file.name>]"><img src="[<$I2_ROOT>]www/pics/filecenter/blank.png" width="24" height="24"/></a></td>
  <td><a href="[<$file.name>]">[<$file.name>]</a></td>
  <td>[<$file.size>]KB</td>
  <td>File</td>
  <td>[<$file.last_modified>]</td>
 </tr>
 [</foreach>]
</table>
