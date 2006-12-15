AIM/AOL Screenname(s):
<ul class="none">
[<foreach from=$user->aim item=username key=k>]
	<li><img src="[<$aim_icon.$k>]" /> <a href="aim:goim?screenname=[<$username>]">[<$username|escape:'html'>]</a></li>
[</foreach>]
</ul>
