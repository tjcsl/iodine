[<foreach from=$im_networks key=network item=description>]
  [<if isSet($im_sns[$network])>]
    [<$description>][<if count($im_sns[$network]) > 1>]s[</if>]:
    <ul class="none">
       [<foreach from=$im_sns[$network] key=sn item=status>]
         <li>[<if $status !== FALSE>]<img src="[<$im_icons>][<$status>]" />[</if>] [<$sn|escape:'html'>]</li>
       [</foreach>]
    </ul>	
  [</if>]
[</foreach>]
