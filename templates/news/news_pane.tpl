<script type="text/javascript" src="https://iodine.tjhsst.edu/~wyang/i2/www/js/news.js"></script>
<script type="text/javascript">
	news_root = '[<$I2_ROOT>]news/';
</script>

<strong><a href="[<$I2_ROOT>]news/translate">Translate to English</a></strong><br />

[<if $newsadmin || $maypost>]
	<a href="[<$I2_ROOT>]news/add">Post a news article</a><br/>
[</if>]
[<foreach from=$stories item=story>]
		[<include file="news/news-disp.tpl">]
[</foreach>]
<br/>
<a href="[<$I2_ROOT>]news/archive">Old news</a> | <a href="[<$I2_ROOT>]news/all">Archived news</a><br/>
