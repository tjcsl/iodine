<a href="[<$I2_ROOT>]mrcolonial/admin">Homecoming Admin</a><br /><br />

Homecoming court voters for <a href="[<$I2_ROOT>]studentdirectory/info/[<$user->uid>]">[<$user->name>]</a>: <br /><br />
<table>
 <tr>
  <th>Voters ([<$numvoters>])</th>
 </tr>
 <tr>
  <td valign="top">
   [<foreach from=$voters item=voter>]
    <a href="[<$I2_ROOT>]studentdirectory/info/[<$voter.user->uid>]">[<$voter.user->name>]</a><br />
   [</foreach>]
  </td>
 </tr>
</table>
