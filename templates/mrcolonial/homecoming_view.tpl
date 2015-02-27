<a href="[<$I2_ROOT>]mrcolonial/admin">Homecoming Admin</a><br /><br />

Homecoming court voting results for grade [<$grade>]: <br /><br />
<table>
 <tr>
  <th>Males</th>
 </tr>
 <tr>
  <td>[<$numvotes_male>] [<if $numvotes_male > 1>] people [<else>] person [</if>] voted for [<$numvotees_male>][<if $numvotees_male > 1>] different[</if>] boy[<if $numvotees_male != 1>]s[</if>].</td>
 </tr>
 <tr>
  <td valign="top">
   [<foreach from=$males item=votee>]
    <a href="[<$I2_ROOT>]studentdirectory/info/[<$votee.user->uid>]">[<$votee.user->name>]</a> -- [<$votee.numvotes>] vote[<if $votee.numvotes != 1>]s[</if>] -- <a href="[<$I2_ROOT>]mrcolonial/voters/[<$votee.user->uid>]">Voters</a><br />
   [</foreach>]
  </td>
 </tr>
</table>
