[<include file="eighth/header.tpl">]
<strong>Activities that have not had attendance taken:</strong><br />
<table>
<th>Actvitiy</th><th>Sponsors</th>
[<foreach from=$acts item=act>]
<tr>
<td>[<$act->name>]</td>
<td>
[<foreach from=$act->sponsors_obj item="sponsor" name="sponsors">]
[<if $smarty.foreach.sponsors.last and not $smarty.foreach.sponsors.first>]
	and
[<elseif not $smarty.foreach.sponsors.first>]
	,
[</if>]
[<if $sponsor->userid != 0>]<a href="[<$I2_ROOT>]studentdirectory/info/[<$sponsor->userid>]">[</if>]
[<$sponsor->lname>]
[<if $sponsor->fname>]
, [<$sponsor->fname|substr:0:1>]
[</if>]
[<if $sponsor->userid != 0>]</a>[</if>]
[</foreach>]
</td>
</tr>
[</foreach>]
</table>
