[<if isset($admin)>]
<a href="[<$I2_ROOT>]podcasts/add">Add a new podcast</a>
[</if>]
<br /><br />
<table class="podcasts">
  <tr><td colspan="5"><b>Active Podcasts</b></td></tr>
[<foreach from=$open item=podcast>]
  <tr><th>[<$podcast->name>]</th>
    <td><a href="[<$I2_ROOT>]podcasts/vote/[<$podcast->pid>]">View!</a></td>
    [<if isset($admin)>]
    <td><a href="[<$I2_ROOT>]podcasts/results/[<$podcast->pid>]">View responses</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/export_csv/[<$podcast->pid>]">(as CSV)</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/edit/[<$podcast->pid>]">Edit</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/delete/[<$podcast->pid>]">Delete</a></td>
    <td>[<$podcast->startdt>] to [<$podcast->enddt>]</td>
    [</if>]
  </tr>
[</foreach>]
[<if isset($admin)>]
  <tr><td colspan="5"><b>Finished podcasts</b></td></tr>
[<foreach from=$finished item=podcast>]
  <tr><th>[<$podcast->name>]</th>
    <td></td>
    <td><a href="[<$I2_ROOT>]podcasts/results/[<$podcast->pid>]">View results</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/export_csv/[<$podcast->pid>]">(as CSV)</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/edit/[<$podcast->pid>]">Edit</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/delete/[<$podcast->pid>]">Delete</a></td>
    <td>[<$podcast->startdt>] to [<$podcast->enddt>]</td>
  </tr>
[</foreach>]
  <tr><td colspan="5"><b>Unstarted podcasts</b></td></tr>
[<foreach from=$unstarted item=podcast>]
  <tr><th>[<$podcast->name>]</th>
    <td></td>
    <td><a href="[<$I2_ROOT>]podcasts/results/[<$podcast->pid>]">View results</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/export_csv/[<$podcast->pid>]">(as CSV)</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/edit/[<$podcast->pid>]">Edit</a></td>
    <td><a href="[<$I2_ROOT>]podcasts/delete/[<$podcast->pid>]">Delete</a></td>
    <td>[<$podcast->startdt>] to [<$podcast->enddt>]</td>
  </tr>
[</foreach>]
[</if>]
</table>
