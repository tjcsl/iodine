[<foreach from=$news_stories item=story>]
<div class="newsitem">
 <div class="bold">[<$story.title>]</div><br />
 [<$story.text>]<br /><br />
 <em>Posted by [<$story.author>] at [<$story.posted|date_format:"%l:%M %p on %a %B %e, %Y">]</em>
</div>
[</foreach>]
