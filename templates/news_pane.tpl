<a href="[<$I2_ROOT>]news/add">Post a news article</a><br />
[<foreach from=$news_stories item='story'>]
<div class="newsitem">
 <div class="bold">[<$story.title>]</div><br />
 [<$story.text>]<br /><br />
 <em>Posted [<if isset($story.author)>]by [<$story.author>] [</if>]at [<$story.posted|date_format:'%l:%M %p on %a %B %e, %Y'>]</em>
 [<if $I2_UID == $story.authorID>]
  <br />
  <a href="[<$I2_ROOT>]news/edit/[<$story.id>]">Edit</a> |
  <a href="[<$I2_ROOT>]news/delete/[<$story.id>]">Delete</a>
 [</if>]
</div>
[</foreach>]
