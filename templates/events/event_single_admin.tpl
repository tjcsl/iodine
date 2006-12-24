<a href="[<$I2_ROOT>]events">Events Home</a><br /><br />

<table>
 <tr>
  <th>Name</th>
  <th>Teacher</th>[<*FIXME FIXME FIXME*>]
  <th>Paid?</th>
 </tr>
 [<foreach from=$event->users_signed_up() item=user>]
  <tr>
   <td>[<$user->name>]</td>
   <td>[<assign var=verifier value=$event->get_user_verifier($user)>][<$verifier->name>]</td>
   <td>[<if $event->user_has_paid($user)>]yes[<else>]<span style="color: #FF0000;"><strong>NO</strong></span>[</if>]</td>
  </tr>
 [</foreach>]
</table>
