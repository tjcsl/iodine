<a href="[<$I2_ROOT>]mrcolonial/admin">Homecoming Admin</a><br /><br />

Homecoming votes: <br /><br />
<table>
 <tr>
  <th>User:</th>
  <th>Male:</th>
 </tr>
 [<foreach from=$voters item=voter>]
  <tr>
   <td>[<if isset($voter.user)>]<a href="[<$I2_ROOT>]studentdirectory/info/[<$voter.user->uid>]">[<$voter.user->name>]</a>[</if>]</td>
   <td>[<if isset($voter.male)>][<if $voter.male->uid == $voter.user->uid>]<b><u>[</if>]<a href="[<$I2_ROOT>]studentdirectory/info/[<$voter.male->uid>]">[<$voter.male->name>]</a>[<if $voter.male->uid == $voter.user->uid>]</b></u>[</if>][</if>]</td>
  </tr>
 [</foreach>]
</table>
