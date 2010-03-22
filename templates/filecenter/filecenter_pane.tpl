
[<if isset($error)>]
<div style="text-align: left; float: left;">
 <font color=red><b>[<$error>]</b></font>
</div>
[</if>]

<div style="text-align: right;">
 <form method="post" action="">
  <input type="hidden" name="toggle_hide" value="1"/>
  <input type="submit" value="Show/hide hidden files"/>
 </form>
</div>

[<if $insertperm=="true">][<* You actually do need insert permissions in afs to create directories *>]
<form method="post" action="">
<input type="text" name="mkdir" />
<input type="submit" value="Create Directory"/>
</form>
[</if>]
<br />

<table id="filetable">
 <thead>
  <tr>
   <th class="image">&nbsp;</th>
   <th class="name"><form name="nameform" method="get" action=""><input type="hidden" name="sort" value="name" />[<if $sort=='name' && $reverse=='false'>]<input type="hidden" name="reverse" value="true" />[</if>]<a href="#" onclick="parentNode.submit()">Name</a></form></th>
   <th class="size"><form name="sizeform" method="get" action=""><input type="hidden" name="sort" value="size" />[<if $sort=='size' && $reverse=='false'>]<input type="hidden" name="reverse" value="true" />[</if>]<a href="#" onclick="parentNode.submit()">Size</a></form></th>
   <th class="type">Type</th>
   <th class="modified"><form name="mtimeform" method="get" action=""><input type="hidden" name="sort" value="mtime" />[<if $sort=='mtime' && $reverse=='false'>]<input type="hidden" name="reverse" value="true" />[</if>]<a href="#" onclick="parentNode.submit()">Last Modified</a></form></th>
  </tr>
 </thead>
 <tbody>
  <tr>
   <td><a href="."><img src="[<$I2_ROOT>]www/pics/filecenter/dir2new.png" width="16" height="16" alt="" /></a></td>
   <td>
    <a href="" onclick="return options(this, 'cur', [<$readperm>], [<$writeperm>], [<$deleteperm>])">* Current Directory (List actions)</a>
   </td>
   <td>&nbsp;</td>
   <td>Directory</td>
   <td>&nbsp;</td>
  </tr>
  [<foreach from=$dirs item=dir>]
  <tr>
   <td><a href="[<$dir.name|escape:"url">]/">[<if $dir.link>]<img src="[<$I2_ROOT>]www/pics/filecenter/dir2link.png" width="16" height="16" alt="" />[<else>]<img src="[<$I2_ROOT>]www/pics/filecenter/dir2new.png" width="16" height="16" alt="" />[</if>]</a></td>
   <td>
    [<if $dir.name == '..'>]<a href="..">* Parent Directory (Go up one level)[<elseif $dir.link>]<a href="" onclick="return options(this, '[<$dir.name>]', 'linkdir', true, true, true)">[<$dir.name|escape>][<elseif $dir.empty>]<a href="" onclick="return options(this, '[<$dir.name>]', 'emptydir', true, true, true)">[<$dir.name|escape>][<else>]<a href="" onclick="return options(this, '[<$dir.name>]', 'dir', true, true, true)">[<$dir.name|escape>][</if>]</a>
   </td>
   <td>&nbsp;</td>
   <td>Directory</td>
   <td>[<$dir.last_modified>]</td>
  </tr>
  [</foreach>]
  [<foreach from=$files item=file>]
  <tr>
   <td><a href="[<$file.name|escape:"url">]">[<if $file.link>]<img src="[<$I2_ROOT>]www/pics/filecenter/file2link.png" width="15" height="16" alt="" />[<else>]<img src="[<$I2_ROOT>]www/pics/filecenter/file2new.png" width="16" height="16" alt="" />[</if>]</a></td>
   <td>
    [<if $file.link>]<a href="" onclick="return options(this, '[<$file.name>]', 'link', true, true, true)">[<$file.name|escape>][<else>]<a href="" onclick="return options(this, '[<$file.name>]', 'file', [<$readperm>], [<$writeperm>], [<$deleteperm>])">[<$file.name|escape>][</if>]</a>
   </td>
   <td class="size">[<$file.size>]</td>
   <td>File</td>
   <td>[<$file.last_modified>]</td>
  </tr>
  [</foreach>]
 </tbody>
</table>
[<if $insertperm=='true'>]
<div id="fileupload">
 <div>Upload a file:</div>
 <form enctype="multipart/form-data" method="post" action="">
  <input type="hidden" name="MAX_FILE_SIZE" value="[<$max_file_size>]"/>
  <input type="file" name="file"/><br/>
  <input type="submit" value="Upload"/>
 </form>
</div>
[</if>]
<script type="text/javascript" src="[<$I2_ROOT>]www/js/filecenter.js"></script>
