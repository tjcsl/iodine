[<include file="eighth/header.tpl">]
<form action="[<$I2_ROOT>]eighth/export_csv/export/bid/[<$bid>]">
<input type="submit" value="Export list as CSV" />
</form>
<table>
  <tr>
    <th>Name</th><th>Activity</th><th>Block</th>
  </tr>
  [<foreach from=$activities item=activity>]
  [<foreach from=$activity->members_obj item=member>]
  <tr>
    <td>[<$member->name_comma>]</td><td>[<$activity->name>]</td><td>[<$activity->block->block>]</td>
  </tr>
  [</foreach>]
  [</foreach>]
</table>
