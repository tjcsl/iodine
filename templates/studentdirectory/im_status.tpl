[<foreach from=$im_networks key=network item=description>]
  [<if isSet($im_sns[$network])>]
    [<$description>][<if count($im_sns[$network]) > 1>]s[</if>]:
    <ul class="none">
       [<foreach from=$im_sns[$network] key=sn item=status>]
         <li>[<if $status !== FALSE>]<img src="[<$im_icons>][<$status>]" />[</if>]
	 [<if $network == 'aim'>]
	   <a href=aim:goim?screenname=[<$sn|escape:'url'>]> [<$sn|escape:'html'>]</a>
	 [<elseif $network == 'googletalk'>]
	   <a href="gtalk:chat?jid=[<$sn|escape:'url'>]"> [<$sn|escape:'html'>]</a>
	 [<elseif $network == 'skype' >]
	   <a href="skype:[<$sn|escape:'url'>]"> [<$sn|escape:'html'>]</a>
	 [<elseif $network == 'xfire' >]
	   <a href="xfire:add_friend?user=[<$sn|escape:'url'>]"> [<$sn|escape:'html'>]</a>
	 [<elseif $network == 'msn' >]
	   <a href="mnsim:chat?contact=[<$sn|escape:'url'>]"> [<$sn|escape:'html'>]</a>
	 [<elseif $network == 'yahoo' >]
	   <a href="ymsgr:sendIM?[<$sn|escape:'url'>]"> [<$sn|escape:'html'>]</a>
	 [<else>]
	   [<$sn|escape:'html'>]
	 [</if>]
	 </li>
       [</foreach>]
    </ul>	
  [</if>]
[</foreach>]
