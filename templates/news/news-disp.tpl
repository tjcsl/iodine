
 <a name="newspost[<$story->id>]"></a>
 <div class="newstitle">[<$story->title>] - 
 [<if !$story->has_been_read()>]
 	<a href="[<$I2_ROOT>]news/read/[<$story->id>]">Mark as read</a>
 [<else>]
 	<a href="[<$I2_ROOT>]news/unread/[<$story->id>]">Mark as unread</a>
 [</if>]
 </div>
 <div class="newsitem">[<if $story->groupsstring>]<div class="newsgroups">Posted to: [<$story->groupsstring>]</div>[</if>]<br />
 [<$story->text>]<br/><br/>
 <div class="newsposted">Posted [<if $story->author>]by [<$story->author->name>] [</if>]at [<$story->posted|date_format:'%l:%M %p on %a %B %e, %Y'>]</div>
 [<if $story->editable()>]
  <br />
  <a href="[<$I2_ROOT>]news/edit/[<$story->id>]">Edit</a>
  | <a href="[<$I2_ROOT>]news/delete/[<$story->id>]">Delete</a>
 [</if>]
</div>
