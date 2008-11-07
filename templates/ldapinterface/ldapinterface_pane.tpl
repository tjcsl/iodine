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
 <textarea name="ldapinterface_query" rows="5" cols="70">[<$query>]</textarea><br />
 Base DN:<br />
 <textarea name="ldapinterface_dn" rows="1" cols="70">[<$last_dn>]</textarea><br />
 Attributes:<br />
 <textarea name="ldapinterface_attrs" rows="3" cols="70">[<$last_attrs>]</textarea><br />
 <input type="radio" name="ldap_searchtype" value="search" [<if $searchtype=='search'>]checked="checked"[</if>]/>Search<br />
 <input type="radio" name="ldap_searchtype" value="list" [<if $searchtype=='list'>]checked="checked"[</if>]/>List<br />
 <input type="radio" name="ldap_searchtype" value="read" [<if $searchtype=='read'>]checked="checked"[</if>]/>Read<br />
 <input type="radio" name="ldap_searchtype" value="delete" [<if $searchtype=='delete'>]checked="checked"[</if>]/>Delete<br />
 <input type="radio" name="ldap_searchtype" value="delete_recursive" [<if $searchtype=='delete_recursive'>]checked="checked"[</if>]/>Delete (Recursive)<br />
 <br /><input type="submit" name="ldapinterface_submit" value="Search"/>
</form>
</p>
