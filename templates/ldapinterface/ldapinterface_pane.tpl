[<if $query_data != NULL>]

 [<if !is_string($query_data)>]
  Query results:<br/>
  [<foreach from=$query_data item=row key=dn>]
    <em>[<$dn>]</em><br/>
    [<foreach from=$row item=arr key=key>]
    [<foreach from=$arr item=cell>]
      <b>[<$key>]</b>:
      [<if $cell != ''>][<$cell>][<else>]NULL[</if>]
      <br/>
     [</foreach>]
    [</foreach>]
    <br/>
  [</foreach>]
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
