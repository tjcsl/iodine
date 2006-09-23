<a href="[<$I2_ROOT>]homecoming/admin">Homecoming Admin</a><br /><br />

Homecoming court voting results for grade [<$grade>]: <br /><br />
<table>
 <tr>
  <th>Males</th><th>Females</th>
 </tr>
 <tr>
  <td>People voted for [<$numvotees_male>][<if $numvotees_male > 1>] different[</if>] boy[<if $numvotees_male != 1>]s[</if>].</td>
  <td>People voted for [<$numvotees_female>][<if $numvotees_female > 1>] different[</if>] girl[<if $numvotees_female != 1>]s[</if>].</td>
 </tr>
 <tr>
  <td valign="top">
   [<foreach from=$males item=votee>]
    <a href="[<$I2_ROOT>]studentdirectory/info/[<$votee.user->uid>]">[<$votee.user->name>]</a> -- [<$votee.numvotes>] vote[<if $votee.numvotes != 1>]s[</if>]<br />
   [</foreach>]
  </td>
  <td valign="top">
   [<foreach from=$females item=votee>]
    <a href="[<$I2_ROOT>]studentdirectory/info/[<$votee.user->uid>]">[<$votee.user->name>]</a> -- [<$votee.numvotes>] vote[<if $votee.numvotes != 1>]s[</if>]<br />
   [</foreach>]
  </td>
 </tr>
</table>
