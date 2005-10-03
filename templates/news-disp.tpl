<div class="newsitem">
 <div class="newstitle">[<$story.title>]</div><br />
 [<$story.text>]<br /><br />
 <div class="newsposted">Posted [<if isset($story.author)>]by [<$story.author>] [</if>]at [<$story.posted|date_format:'%l:%M %p on %a %B %e, %Y'>]</div>
 [<if $story.editable>]
  <br />
  <a href="[<$I2_ROOT>]news/edit/[<$story.id>]">Edit</a> |
  <a href="[<$I2_ROOT>]news/delete/[<$story.id>]">Delete</a>
 [</if>]
</div>
