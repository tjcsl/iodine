<script>
$(function() {
$(".mainbox .boxheader").prepend("<img src='[<$I2_ROOT>]www/gc/new.gif' class=blink /><img src='[<$I2_ROOT>]www/gc/new.gif' class=blink />").append("<img src='[<$I2_ROOT>]www/gc/new.gif' class=blink /><img src='[<$I2_ROOT>]www/gc/new.gif' class=blink />");
});
</script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/news.js"></script>
<script type="text/javascript">
	var news_root = '[<$I2_ROOT>]news/';
</script>
<div style="text-align:left; margin-bottom:3px;">
[<if $newsadmin || $maypost>]
	<a href="[<$I2_ROOT>]news/add">Post a news article</a>
[</if>]
[<$weatherstatus>]
[<if !$newsadmin >]
	<a href="[<$I2_ROOT>]news/request">Submit a news article for posting</a>
[</if>]
<span style="float:right; margin-right:3px;"><a href="http://www.twitter.com/TJIntranet" target="_blank"><img src="[<$I2_ROOT>]www/pics/twitter_logo.png" width="15" alt="Follow on Twitter" title="Follow on Twitter" style="position: relative; top: 3px" /></a><img src="[<$I2_ROOT>]www/pics/rss_logo.png" width="15" alt="Feeds:" title="Feeds" style="position: relative; top: 3px" /> <a href="[<$I2_ROOT>]feeds/rss">RSS</a>&middot;<a href="[<$I2_ROOT>]feeds/atom">ATOM</a></span>
<br/>
</div>
<div>
[<if $I2_USER->is_group_member('students_all')>]
<div class="newspost">
<div class="newstitle">Happy first Tuesday of April!</div>
<div class="newsitem"><div class="newsgroups">Posted by the Intranet Developers to students_all.</div>
<div id="newsitem_9001">
<p>We hope you enjoy the blast from the past. If you experience difficulties with the enhanced feature set, <a href="[<$I2_ROOT>]gc/optout">click here to opt out</a>.</p>
</div>
</div>
</div>
[</if>]
[<foreach from=$stories item=story>]
		[<include file="news/news-disp.tpl">]
[<foreachelse>]
		<br />
		<p>There are no newsposts that can be displayed this time. You can individually re-add posts that you want to see by clicking "Old news" below and selecting "Mark as unread."</p>
[</foreach>]
</div>
<br/>
<a href="[<$I2_ROOT>]news/archive">Old news</a> | <a href="[<$I2_ROOT>]news/all">Archived news</a><br />
