<p>
[<foreach from=$dirs item=dir>]
 Dir: [<$dir.name>], Last modified by [<$dir.lastmod_user>] on [<$dir.lastmod_date>], log entry: [<$dir.lastmod_log>] <br /><hr />
[</foreach>]
</p>
<p>
[<foreach from=$files item=file>]
 File: [<$file.name>], Last modified by [<$file.lastmod_user>] on [<$file.lastmod_date>]<br />
log entry:<br />
[<$file.lastmod_log>] <br /><hr />
[</foreach>]
</p>
