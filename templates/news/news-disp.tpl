
 <div class="newstitle">[<$story->title>]</div>
 <div class="newsitem">[<if $story->groupsstring>]<div class="newsgroups">Posted to: [<$story->groupsstring>]</div>[</if>]<br />
 [<$story->text>]<br/><br/>
 <div class="newsposted">Posted [<if $story->author>]by [<$story->author->name>] [</if>]at [<$story->posted|date_format:'%l:%M %p on %a %B %e, %Y'>]</div>
 [<if $story->editable()>]
  <br/>
  <a href="[<$I2_ROOT>]news/edit/[<$story->id>]">Edit</a>
  | <a href="[<$I2_ROOT>]news/delete/[<$story->id>]">Delete</a>
 [</if>]
</div>
