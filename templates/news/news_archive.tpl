Old News <br/><br/>
[<foreach from=$news_stories item=story>]
	[<if $story.read>]
		[<include file="news/news-disp.tpl">]
	[</if>]
[</foreach>]
