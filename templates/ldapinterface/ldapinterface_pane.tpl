[<if $query_data != NULL>]

 <p>
 [<if !is_string($query_data)>]
  Query results:
  <table id="ldapinterface_data" border="1">
  [<foreach from=$query_data item=row key=dn>]
  <tr>
    <td>[<$dn>]</td>
    [<foreach from=$row item=arr key=key>]
    [<foreach from=$arr item=cell>]
      <td style="text-align:center;">[<if $cell != ''>][<$cell>][<else>]NULL[</if>]</td>
     [</foreach>]
    [</foreach>]
   </tr>
  [</foreach>]
  </table></p>
 [<else>]
  [<$query_data>]
 [</if>]
[</if>]
<p>
<form action="[<$I2_SELF>]" method="POST">
 Query:<br />
 <textarea name="ldapinterface_query" rows="5" cols="50">[<$query>]</textarea>
 <br /><input type="submit" name="ldapinterface_submit" value="Search" />
</form>
</p>
