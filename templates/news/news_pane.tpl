[<if $newsadmin || $maypost>]
	<a href="[<$I2_ROOT>]news/add">Post a news article</a><br/>
[</if>]
[<foreach from=$stories item=story>]
		[<include file="news/news-disp.tpl">]
[</foreach>]
<br/>
<a href="[<$I2_ROOT>]news/archive">Old news</a> | <a href="[<$I2_ROOT>]news/all">Archived news</a><br/>
