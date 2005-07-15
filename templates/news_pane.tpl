[<foreach from=$news_stories item=story>]
<div class="newsitem">
 <div class="bold">[<$story.title>]</div><br />
 [<$story.text>]<br /><br />
 <em>Posted [<if isset($story.author)>]by [<$story.author>] [</if>]at [<$story.posted|date_format:"%l:%M %p on %a %B %e, %Y">]</em>
</div>
[</foreach>]
