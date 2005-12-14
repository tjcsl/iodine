<div style="float: right;">
 <form method="post">
  <input type="hidden" name="toggle_hide" value="1"/>
  <input type="submit" value="Show/hide hidden files"/>
 </form>
</div>
   
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
  <td><a href="[<$dir.name>]/"><img src="[<$I2_ROOT>]www/pics/filecenter/dir2.png" width="16" height="16"/></a></td>
  <td><a href="[<$dir.name>]/">[<$dir.name>]</a></td>
  <td>&nbsp;</td>
  <td>Directory</td>
  <td>[<$dir.last_modified>]</td>
 </tr>
 [</foreach>]
 [<foreach from=$files item=file>]
  <tr>
   <td><a href="[<$file.name>]"><img src="[<$I2_ROOT>]www/pics/filecenter/file2.png" width="15" height="16"/></a></td>
   <td><a href="[<$file.name>]">[<$file.name>]</a></td>
   <td>[<$file.size>]KB</td>
   <td>File</td>
   <td>[<$file.last_modified>]</td>
  </tr>
 [</foreach>]
</table>
<div id="fileupload">
 <form enctype="multipart/form-data" method="post">
  <input type="hidden" name="MAX_FILE_SIZE" value="[<$max_file_size>]"/>
  <input type="file" name="file"/><br/>
  <input type="submit" value="Upload"/>
 </form>
</div>
