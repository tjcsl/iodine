<script type="text/javascript" src="[<$I2_ROOT>]www/js/news.js"></script>
<script type="text/javascript">
	var news_root = '[<$I2_ROOT>]news/';
</script>
<div align="left">
[<if $newsadmin || $maypost>]
	<a href="[<$I2_ROOT>]news/add">Post a news article</a><br />
[</if>]
[<if !$newsadmin >]
	<a href="[<$I2_ROOT>]news/request">Request posting a news article</a><br />
[</if>]</div><div style="position:absolute; right:3px; top:18px;"><img src="[<$I2_ROOT>]www/pics/rss_logo.png" width="15" alt="RSS" title="RSS" style="position: relative; top: 3px" /> <a href="[<$I2_ROOT>]feeds/rss">RSS</a>&middot;<a href="[<$I2_ROOT>]feeds/atom">ATOM</a></div>
[<foreach from=$stories item=story>]
		[<include file="news/news-disp.tpl">]
[</foreach>]
<br/>
<a href="[<$I2_ROOT>]news/archive">Old news</a> | <a href="[<$I2_ROOT>]news/all">Archived news</a><br />
