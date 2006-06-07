<div style="text-align: right;">
 <form method="post">
  <input type="hidden" name="toggle_hide" value="1"/>
  <input type="submit" value="Show/hide hidden files"/>
 </form>
</div>

<form method="post">
<input type="text" name="mkdir" />
<input type="submit" value="Create Directory"/>
</form>
<br />

<table id="filetable">
 <thead>
  <tr>
   <th class="image">&nbsp;</th>
   <th class="name">Name</th>
   <th class="size">Size</th>
   <th class="type">Type</th>
   <th class="modified">Last Modified</th>
  </tr>
 </thead>
 <tbody>
  [<foreach from=$dirs item=dir>]
  <tr>
   <td><a href="[<$dir.name|escape:"url">]/"><img src="[<$I2_ROOT>]www/pics/filecenter/dir2.png" width="16" height="16"/></a></td>
   <td>
    [<if $dir.name == '..'>]<a href="..">[<else>]<a href="" onclick="return options(this, 'dir')">[</if>][<$dir.name|escape>]</a>
   </td>
   <td>&nbsp;</td>
   <td>Directory</td>
   <td>[<$dir.last_modified>]</td>
  </tr>
  [</foreach>]
  [<foreach from=$files item=file>]
  <tr>
   <td><a href="[<$file.name|escape:"url">]"><img src="[<$I2_ROOT>]www/pics/filecenter/file2.png" width="15" height="16"/></a></td>
   <td><a href="" onclick="return options(this, 'file')">[<$file.name|escape>]</a></td>
   <td class="size">[<$file.size>]</td>
   <td>File</td>
   <td>[<$file.last_modified>]</td>
  </tr>
  [</foreach>]
 </tbody>
</table>
<div id="fileupload">
 <div>Upload a file:</div>
 <form enctype="multipart/form-data" method="post">
  <input type="hidden" name="MAX_FILE_SIZE" value="[<$max_file_size>]"/>
  <input type="file" name="file"/><br/>
  <input type="submit" value="Upload"/>
 </form>
</div>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/filecenter.js"></script>
