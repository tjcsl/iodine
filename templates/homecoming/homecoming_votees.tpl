<a href="[<$I2_ROOT>]homecoming/admin">Homecoming Admin</a><br /><br />

Homecoming votes by <a href="[<$I2_ROOT>]studentdirectory/info/[<$user->uid>]">[<$user->name>]</a>: <br /><br />
<table>
 <tr>
[<if $mvotee>]  <th>Male:</th>[</if>]
[<if isset($fvotee)>]  <th>Female:</th>[</if>]
 </tr>
 <tr>
[<if $mvotee>]
  <td>
   <a href="[<$I2_ROOT>]studentdirectory/info/[<$mvotee->uid>]">[<$mvotee->name>]</a><br />
  </td>
[</if>]
[<if isset($fvotee)>]
  <td>
   <a href="[<$I2_ROOT>]studentdirectory/info/[<$fvotee->uid>]">[<$fvotee->name>]</a><br />
  </td>
[</if>]
 </tr>
</table>
