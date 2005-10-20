[<if $query_data != NULL>]
 <p>
 <br />Query results:
 <table id="mysqlinterface_data" border="1">
 [<foreach from=$header_data item=header>]
  <th>[<$header>]</th>
 [</foreach>]
 [<foreach from=$query_data item=row>]
  <tr>
   [<foreach from=$row item=cell key=key>]
[<*data is given in both associative and numeric indices, only want it once*>]
    [<if is_int($key)>]
     <td style="text-align:center;">[<if $cell != ''>][<$cell>][<else>]NULL[</if>]</td>
    [</if>]
   [</foreach>]
  </tr>
 [</foreach>]
 </table></p>
[</if>]
<p>
<form action="[<$I2_SELF>]" method="POST">
 Query:<br />
 <textarea name="mysqlinterface_query" rows="5" cols="50"></textarea>
 <br /><input type="submit" name="mysqlinterface_submit" value="Submit" />
</form>
</p>
