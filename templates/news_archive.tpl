Old News <br/><br/>
[<foreach from=$news_stories item=story>]
	[<if $story.read>]
		[<include news-disp.tpl>]
	[</if>]
[</foreach>]
