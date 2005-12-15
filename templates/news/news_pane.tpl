[<if $newsadmin>]
	<a href="[<$I2_ROOT>]news/add">Post a news article</a><br/>
[</if>]
[<foreach from=$stories item=story>]
	[<if !$story->has_been_read()>]
		[<include file="news/news-disp.tpl">]
	[</if>]
[</foreach>]
<br/>
<a href="[<$I2_ROOT>]news/archive">Old news</a><br/>
