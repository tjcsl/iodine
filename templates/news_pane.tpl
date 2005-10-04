<a href="[<$I2_ROOT>]news/add">Post a news article</a><br/>
[<foreach from=$news_stories item=story>]
	[<if !$story.read>]
		[<include file="news-disp.tpl">]
	[</if>]
[</foreach>]
<br/>
<a href="[<$I2_ROOT>]news/archive">Old news</a><br/>
